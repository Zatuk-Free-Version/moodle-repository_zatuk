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
 * This file contains the moodle hooks for the zatuk repository.
 *
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
define('MOODLE_ZATUK_WEB_SERVICE', 'zatuk_web_service');
global $CFG;
require_once($CFG->dirroot . '/repository/lib.php');
use repository_zatuk\video_service;
use repository_zatuk\app_service;
use repository_zatuk\phpzatuk;
use repository_zatuk\zatuk_constants as zc;
/**
 * zatuk repository plugin
 */
class repository_zatuk extends repository {

    /**
     * @var array $service
     */
    protected $service;
    /**
     * @var string $zatukapiurl
     */
    protected $zatukapiurl;
    /**
     * @var string $zatukkey
     */
    protected $zatukkey;
    /**
     * @var string $zatuksecret
     */
    protected $zatuksecret;
    /**
     *  repository_zatuk constructor
     *
     * @param int $repositoryid repository instance id.
     * @param int|stdClass $context a context id or context object.
     * @param array $options repository options.
     * @return void
     */
    public function __construct($repositoryid, $context, $options = []) {
        parent::__construct($repositoryid, $context, $options);
        $this->zatukkey = get_config('repository_zatuk', 'zatuk_key');
        $this->zatukapiurl = get_config('repository_zatuk', 'zatukapiurl');
        $this->zatuksecret = get_config('repository_zatuk', 'zatuk_secret');
        $this->service = new app_service($this->zatukkey, $this->zatuksecret);
    }

    /**
     * Edit/Create Admin Settings Moodle form.
     *
     * @param object $mform
     * @param string $classname
     * @return void
     */
    public static function type_config_form($mform, $classname = 'repository') {
        global $OUTPUT, $CFG, $PAGE;

        $mformid = 'zatukconfigureform';
        $mform->setAttributes(['id' => $mformid] + $mform->getAttributes());
        $isrepositoryenabled = (new video_service())->isrepositoryenabled();
        if (!$isrepositoryenabled) {
            (new video_service())->enablezatuk();
            purge_caches();
        }
        $systemcontext = context_system::instance();
        $zatukkey = get_config('repository_zatuk', 'zatuk_key');
        $zatuksecret = get_config('repository_zatuk', 'zatuk_secret');
        $renderer = $PAGE->get_renderer('repository_zatuk');
        if ($zatuksecret == "" && $zatukkey == "") {
            $render = new repository_zatuk\output\zatuksettings($systemcontext);
        } else {
            $render = new repository_zatuk\output\zatukconfiguration($systemcontext);
        }
        $htmlelement = $OUTPUT->render_from_template('repository_zatuk/htmlelement', ['data' => $renderer->render($render)]);
        $mform->addElement('html', $htmlelement);
    }

    /**
     * Is this repository accessing after login?
     *
     * @return bool
     */
    public function check_login() {
        return true;
    }
    /**
     * file types supported by url downloader plugin
     *
     * @return array
     */
    public function supported_filetypes() {
        return ['video'];
    }
    /**
     * Is this repository accessing private data?
     *
     * @return bool
     */
    public function contains_private_data() {
        return true;
    }
    /**
     * Tells how the file can be picked from this repository
     *
     * Maximum value is FILE_INTERNAL | FILE_EXTERNAL | FILE_REFERENCE
     *
     * @return int
     */
    public function supported_returntypes() {
        return (FILE_INTERNAL | FILE_EXTERNAL);
    }
    /**
     * Does this repository used to browse moodle files?
     *
     * @return bool
     */
    public function has_moodle_files() {
        return false;
    }
    /**
     * Search files in repository
     * When doing global search, $search_text will be used as
     * keyword.
     *
     * @param array $q search key word
     * @param array $page page
     * @return array
     */
    public function search($q, $page = []) {
        $result  = [];
        $result['nologin'] = true;
        $result['page'] = (int)$page;
        if ($result['page'] < zc::STATUSA) {
            $result['page'] = zc::STATUSA;
        }
        $start = ($result['page'] - zc::STATUSA) * zc::ZATUK_THUMBS_PER_PAGE + zc::STATUSA;
        $start = $start - zc::STATUSA;
        $phpzatuk = new phpzatuk($this->zatukapiurl, $this->zatukkey, $this->zatuksecret);
        $searchurl = $phpzatuk->createsearchapiurl();
        $params = $phpzatuk->get_listing_params();
        $params['page'] = $result['page'];
        $params['q'] = $q;
        $params['perpage'] = zc::ZATUK_THUMBS_PER_PAGE;
        $request = new curl();
        try {
            $content = $request->post($searchurl, $params);
            $content = json_decode($content, true);
            $result['list'] = $this->get_collection($content);
            $result['norefresh'] = true;
            $result['nosearch'] = false;
            $result['total'] = $content['meta']['total'];
            $result['pages'] = ceil($content['meta']['total'] / zc::ZATUK_THUMBS_PER_PAGE);
            $result['perpage'] = zc::ZATUK_THUMBS_PER_PAGE;
            return $result;
        } catch (Exception $e) {
            throw new moodle_exception($e->getMessage());
        }

    }

    /**
     * Private method to get zatuk search results
     *
     * @param array $content [description]
     * @return array
     */
    private function get_collection($content) {
        global $DB, $USER;

        $list = [];
        if (isset($content['data']) && count($content['data']) > zc::DEFAULTSTATUS) {
            foreach ($content['data'] as $entry) {
                $videorecord = $DB->get_record('zatuk_uploaded_videos', ['videoid' => $entry['videoid']]);
                if (!is_siteadmin() && $videorecord->videoid == $entry['videoid'] &&
                    $videorecord->public == 0 &&
                    $videorecord->usercreated != $USER->id) {
                    continue;
                }
                $list[] = [
                    'shorttitle' => $entry['title'],
                    'thumbnail_title' => $entry['title'],
                    'title' => $entry['title'].'.avi', // This is a hack so we accept this file by extension.
                    'thumbnail' => $this->zatukapiurl.stripslashes($entry['thumbnail']),
                    'videoid' => stripslashes($entry['videoid']),
                    'thumbnail_width' => zc::COLLECTION_THUMBNAIL_WIDTH,
                    'thumbnail_height' => zc::COLLECTION_THUMBNAIL_HEIGHT,
                    'size' => zc::COLLECTION_SIZE,
                    'date' => strtotime($entry['timecreated']),
                    'license' => 'unknown',
                    'author' => $entry['usercreated'],
                    'source' => $entry['path'],
                ];
            }
        }
        return $list;
    }
    /**
     * Given a path, and perhaps a search, get a list of files.
     *
     * See details on {@link http://docs.moodle.org/dev/Repository_plugins}
     *
     * @param string $path this parameter can a folder name, or a identification of folder
     * @param string $page the page number of file list
     * @return array the list of files
     */
    public function get_listing($path='', $page = '') {
        global $OUTPUT, $USER, $DB;

        $folderurl = $OUTPUT->image_url(zc::FOLDERPATH128)->out();
        $phpzatuk = new phpzatuk($this->zatukapiurl, $this->zatukkey, $this->zatuksecret);
        $listingurl = $phpzatuk->createlistingapiurl();
        $params = $phpzatuk->get_listing_params();
        $params['currentPath'] = $path ? $path : '/';
        $params['search'] = null;
        $request = new curl();
        $content = $request->post($listingurl, $params);
        $content = json_decode($content, true);
        $return = ['dynload' => true, 'nosearch' => false, 'nologin' => true];
        if (!empty($content['navPath'])) {
            foreach ($content['navPath'] as $paths) {
                $pathelement = [
                    'icon' => $OUTPUT->image_url(file_folder_icon())->out(false),
                    'path' => $paths['navpathdata'],
                    'name' => $paths['name'],
                ];
                $return['path'][] = $pathelement;
            }
        } else {

            $return['path'] = [];
        }
        $folderlists = (!empty($content) && is_array($content['forganizations']) && is_array($content['fdirectories'])) ?
            array_merge($content['forganizations'], $content['fdirectories']) : [];
        if (!empty($folderlists)) {
            foreach ($folderlists as $folders) {
                $listelement = [];
                $listelement['thumbnail'] = $folderurl;
                $listelement['thumbnail_width'] = zc::LISTING_THUMBNAIL_WIDTH;
                $listelement['thumbnail_height'] = zc::LISTING_THUMBNAIL_HEIGHT;
                $listelement['title'] = $folders['fullname'];
                $listelement['path'] = $folders['path'];
                $listelement['children'] = [];
                $return['list'][] = $listelement;
            }
        } else {
            $return['list'] = [];
        }
        $fileslist = (!empty($content)) ? $content['fvideos'] : [];
        if (!empty($fileslist)) {
            foreach ($fileslist as $files) {
                $videorecord = $DB->get_record('zatuk_uploaded_videos', ['videoid' => $files['videoid']]);
                if (!is_siteadmin() && $videorecord->videoid == $files['videoid'] &&
                    $videorecord->public == 0 && $videorecord->usercreated != $USER->id) {
                    continue;
                }
                $filecontent = [
                    'thumbnail' => $this->zatukapiurl.'/storage/'.$files['thumbnail'],
                    'title' => $files['title'],
                    'source' => $files['encodedurl'],
                    'date' => strtotime($files['timecreated']),
                    'license' => 'unknown',
                    'thumbnail_title' => $files['title'],
                    'encoded_url' => $files['encodedurl'],
                ];
                $return['list'][] = $filecontent;
            }
        } else {
            $return['list'] = [];
        }
        return $return;

    }

}
