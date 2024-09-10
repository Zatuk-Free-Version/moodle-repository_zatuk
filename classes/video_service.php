<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * repository_zatuk video service class.
 *
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace repository_zatuk;

use stdClass;
use context_system;
use repository_zatuk\app_service;
use repository_zatuk\zatuk_constants as zc;
/**
 * Class video_service
 */
class video_service {

    /**
     * @var $db;
     */
    public $db;

    /**
     * @var array $table;
     */
    public $table;

    /**
     * @var string $reference;
     */
    public $reference;

    /**
     * @var array $userid;
     */
    public $userid;
    /**
     * @var  array $statusoptions;
     */
    public $statusoptions;
    /**
     * @var object $service;
     */
    public $service;

    /**
     * Main constructor for video service.
     * @param string $reference
     * @param int $userid
     */
    public function __construct($reference = '', $userid = zc::DEFAULTSTATUS) {
        global $DB;
        $this->db = $DB;

        $param = get_config('repository_zatuk', 'zatuk_key').','.get_config('repository_zatuk', 'zatuk_secret');
        $this->service = new app_service($param);
        $this->table = 'repository_zatuk_videos';
        $this->reference = $reference;
        $this->userid = $userid;
        $this->statusoptions = ['Uploaded', 'Published'];
    }

    /**
     * Get list of uploaded videos.
     * @param object $params
     * @return array $videos
     */
    public function get_uploaded_videos($params) {
        $sql = "SELECT rzv.id, rzv.*, u.username FROM {repository_zatuk_videos} rzv
                  JOIN {user} u ON u.id = rzv.usercreated WHERE 1 = 1";
        $queryparams = [];
        if (!empty($params->search)) {
            $sql .= " AND ".$this->db->sql_like('rzv.title', ':titlesearch', false)." ";
            $queryparams['titlesearch']  = '%'.$params->search.'%';
        }

        if (!empty($params->status)) {
            $sql .= " AND rzv.status =:vstatus";
            $queryparams['vstatus']  = $params->status;
        }

        if (!empty($params->user)) {
            $sql .= " AND rzv.usercreated = :vuser";
            $queryparams['vuser']  = $params->user;
        }

        if (!empty($params->sort)) {
            if ($params->sort == 'title') {
                $sql .= " ORDER BY rzv.title ASC";
            } else if ($params->sort == 'datecreated') {
                $sql .= " ORDER BY rzv.timecreated DESC";
            }
        } else {
            $sql .= " ORDER BY rzv.id DESC";
        }
        $results = $this->db->get_records_sql($sql, $queryparams);
        return $this->format_videos_response($results);
    }

    /**
     * Formats Video response.
     * @param array $results
     * @return array $videos
     */
    public function format_videos_response($results) {
        $response = [];
        foreach ($results as $result) {
            $response[] = [
                'id'            => $result->id,
                'videoid'       => $result->videoid,
                'title'         => $result->title,
                'description'   => unserialize($result->description),
                'tags'          => $result->tags,
                'status'        => $this->statusoptions[$result->status],
                'username'      => $result->username,
                'usercreated'   => $result->usercreated,
                'thumbnail'     => $result->thumbnail,
            ];
        }
        return $response;
    }

    /**
     * Saves uploaded video information in database.
     * @param object $video
     * @return integer $id
     */
    public function store($video) {
        $data = new stdClass;
        $data->videoid = uniqid();
        $data->videofile = $video->videofile;
        $data->title = $video->title;
        $data->videothumbnail = $video->thumbnail;
        $data->description = serialize($video->description);
        $data->tags = $video->tags;
        $data->status = zc::DEFAULTSTATUS;
        $data->reference = $this->reference;
        $data->reference_id = $this->get_reference_id($video);
        $data->organization = $video->organization;
        $data->usercreated = $this->userid;
        $data->timecreated = time();
        $data->timemodified = time();
        return $this->db->insert_record($this->table, $data);
    }

    /**
     * Uploads all the uploaded videos to zatuk.
     *
     * @return array $response
     */
    public function upload_videos_to_zatuk() {
        $response = [];
        $sql = "SELECT rzv.id, rzv.videoid, f.id as fileid,
        f.filename, rzv.title, rzv.description,
        rzv.tags FROM {repository_zatuk_videos} rzv
                  JOIN {files} f ON f.itemid = rzv.id
                 WHERE f.component = :component
                   AND f.filearea = :filearea
                   AND f.filename != :filename
                   AND rzv.status = :status";

        $videos = $this->db->get_records_sql($sql,
                    ['component' => 'repository_zatuk',
                        'filearea' => 'repository_video',
                        'filename' => '.',
                        'status' => zc::DEFAULTSTATUS]
                    );

        foreach ($videos as $video) {
            $videofile = $this->get_file_object($video->fileid, 'video');
            $data = [
                'title'         => $video->title,
                'filename'      => $video->filename,
                'videoid'       => $video->videoid,
                'description'   => get_string('description'),
                'tags'          => $video->tags,
                'video'         => $videofile,
            ];
            $upload = $this->service->upload_video($data);
            if (!$upload['response']->error) {
                $this->update_status($video->id, 1);
            }
            $response[] = $upload;
        }
        return $response;
    }

    /**
     * Get single video information.
     * @param string $videoid
     * @return array $video
     */
    public function get_video($videoid) {
        $video = $this->service->get_video($videoid);
        return $video;
    }

    /**
     * Updates the status of uploaded videos.
     * @param integer $id
     * @param integer $status
     * @return integer
     */
    public function update_status($id, $status) {
        $data = new StdClass;
        $data->id = $id;
        $data->status = $status;
        return $this->db->update_record($this->table, $data);
    }

    /**
     * Gets encryption key.
     * @param string $url
     * @return string $key
     */
    public function encryption_key($url) {
        $response = $this->service->get_encryption_key($url);
        return $response['response'];
    }

    /**
     * Gets file Object from Moodle Data.
     * @param integer $fileid
     * @param integer $filetype
     * @return object $file
     */
    public function get_file_object($fileid, $filetype) {
        $filestorage = get_file_storage();
        $file = $filestorage->get_file_by_id($fileid);
        return $file;
    }

    /**
     * Gets reference id.
     * @param object $data
     * @return integer $referenceid
     */
    public function get_reference_id($data) {
        $referenceid = zc::DEFAULTSTATUS;
        switch($this->reference){
            case 'uploads':
                $referenceid = $data->videofile;
            break;
            case 'zoom':
            break;
            case 'webx':
            break;
            default:
        }
        return $referenceid;
    }
    /**
     * Enable zatuk respotiroy.
     * @return bool
     */
    public function enablezatuk() {
        $repositoryenabled = $this->isrepositoryenabled();
        if (!$repositoryenabled) {
            $row = [];
            $data = [];
            $row['type'] = 'zatuk';
            $row['visible'] = zc::STATUSA;
            $sortorder = $this->db->get_record_sql("SELECT sortorder FROM {repository} ORDER BY ID DESC LIMIT 1");
            $row['sortorder'] = $sortorder->sortorder + 1;
            $record = $this->db->insert_record('repository', $row);
            if ($record) {
                $data['typeid'] = $record;
                $data['contextid'] = 1;
                $data['timecreated'] = time();
                $data[' timemodified'] = time();
                $this->db->insert_record('repository_instances', $data);
            }
            return $record;
        } else {
            return $repositoryenabled;
        }
    }
    /**
     * Check zatuk respotiroy enabled or not.
     * @return bool
     */
    public function isrepositoryenabled() {

        $result = $this->db->record_exists('repository', ['type' => 'zatuk', 'visible' => zc::STATUSA]);
        return $result;
    }
    /**
     * Configure zatuk repository.
     * @param stdClass $sdata
     * @return stdclass
     */
    public function configure_zatuk_repository($sdata) {
        global $USER, $CFG;
        require_once($CFG->libdir.'/externallib.php');
        $systemcontext = context_system::instance();
        $organization = $sdata->organization;
        $organizationcode = $sdata->organizationcode;
        $email = $sdata->email;
        $name = $sdata->name;
        $service = $this->db->get_record('external_services', ['shortname' => 'zatuk_web_service', 'enabled' => zc::STATUSA]);
        if ($service) {
            $conditions = [
            'userid' => $USER->id,
            'externalserviceid' => $service->id,
            'tokentype' => EXTERNAL_TOKEN_PERMANENT,
            ];
            $existingtokens = $this->db->get_record('external_tokens', $conditions, 'token', IGNORE_MISSING);
            if ($existingtokens) {
                $token = $existingtokens->token;
            } else {
                $token = external_generate_token(EXTERNAL_TOKEN_PERMANENT, $service->id, $USER->id, $systemcontext->id, zc::DEFAULTSTATUS);
            }
        } else {
            $token = '';
        }
        $service = new app_service();

        $apiresponse = $service->upgrade_package($name, $email, $token, $organization, $organizationcode);
        $response = $apiresponse['response'];
        if (!$response->success) {
            if ($response->errors && is_object($response->errors)) {
                foreach ($response->errors as $key => $apierror) {
                    if ($key == 'token') {
                        $errors['moodle_token'] = $apierror[0];
                    } else {
                        $errors[$key] = $apierror[0];
                    }
                }
            }
            $errors['generic_errors'] = $response->message;
        } else {

            if ($organization) {
                set_config('organization', $organization, 'repository_zatuk');
            }
            if ($organizationcode) {
                set_config('organizationcode', $organizationcode, 'repository_zatuk');
            }
            if ($email) {
                set_config('email', $email, 'repository_zatuk');
            }
            if ($name) {
                set_config('name', $name, 'repository_zatuk');
            }

            if ($response->api_info && !empty($response->api_info->key)) {
                set_config('zatuk_key', $response->api_info->key, 'repository_zatuk');
            }

            if ($response->api_info && !empty($response->api_info->secret)) {
                set_config('zatuk_secret', $response->api_info->secret, 'repository_zatuk');
            }
            purge_caches();
        }
        return $response;
    }
    /**
     * Describes the zatuk configuration settings updation.
     * @param object $sdata
     * @return bool
     */
    public function update_zatuk_configuration_setting($sdata) {
        set_config('name', $sdata->name, 'repository_zatuk');
        set_config('organization', $sdata->organization, 'repository_zatuk');
        set_config('email', $sdata->email, 'repository_zatuk');
        return true;
    }
}
