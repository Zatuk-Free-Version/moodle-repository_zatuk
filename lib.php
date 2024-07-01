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
 * @since      Moodle 2.0
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
define('MOODLE_ZATUK_WEB_SERVICE', 'zatuk_web_service');
require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->dirroot.'/repository/zatuk/zatuklib.php');
use repository_zatuk\app_service;
/**
 * repository_zatuk
 */
class repository_zatuk extends repository {

    /**
     * @var array $service
     */
    protected $service;
    /**
     * @var array $zatukkey
     */
    protected $zatukkey;
    /**
     * @var array $zatuksecret
     */
    protected $zatuksecret;
    /**
     * @var array $settings
     */

    /**
     * [__construct description]
     * @param array $repositoryid
     * @param array $context
     * @param array $options
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = []) {
        parent::__construct($repositoryid, $context, $options);
        $this->api_key = get_config('repository_zatuk', 'zatuk_key');
        $this->api_url = get_config('repository_zatuk', 'zatuk_api_url');
        $this->secret = get_config('repository_zatuk', 'zatuk_secret');
        $this->service = new app_service($this->zatukkey, $this->zatuksecret);
        $this->email_address  = $this->get_option('email');
        $this->user_name  = $this->get_option('name');
        $this->zatuk = new phpzatuk($this->api_url, $this->api_key, $this->secret, $this->email_address, $this->user_name);

    }

    /**
     * [type_config_form description]
     * @param array $mform
     * @param array $classname
     */
    public static function type_config_form($mform, $classname = 'repository') {
        global $OUTPUT, $CFG, $PAGE;
        purge_caches();
        require_login();
        $systemcontext = context_system::instance();
        $zatukkey = get_config('repository_zatuk', 'zatuk_key');
        $zatuksecret = get_config('repository_zatuk', 'zatuk_secret');
        $renderer = $PAGE->get_renderer('repository_zatuk');
        if ($zatuksecret == "" && $zatukkey == "") {
            $render = new repository_zatuk\output\zatuksettings($systemcontext);
            $mform->addElement('html', html_writer::tag('div', $renderer->render($render)));

        } else {
            $render = new repository_zatuk\output\zatukconfiguration($systemcontext);
            $mform->addElement('html', html_writer::tag('div', $renderer->render($render)));
        }

    }

    /**
     * [type_form_validation description]
     * @param array $mform
     * @param array $data
     * @param array $errors
     */
    public static function type_form_validation($mform, $data, $errors) {

    }
    /**
     * [check_login description]
     */
    public function check_login() {
        return true;
    }
    /**
     * [supported_filetypes description]
     */
    public function supported_filetypes() {
        return ['video'];
    }
    /**
     * [contains_private_data description]
     */
    public function contains_private_data() {
        return true;
    }
    /**
     * [supported_returntypes description]
     */
    public function supported_returntypes() {
        return FILE_EXTERNAL;
    }
    /**
     * [has_moodle_files description]
     */
    public function has_moodle_files() {
        return false;
    }
    /**
     * [search description]
     * @param array $q
     * @param array $page
     */

     /**
      * [get_collection description]
      * @param array $content [description]
      * Private method to get video list
      */
    private function get_collection($content) {
        $list = [];
        if (count($content['data']) > 0) {
            foreach ($content['data'] as $entry) {
                $list[] = [
                    'shorttitle' => $entry['title'],
                    'thumbnail_title' => $entry['title'],
                    'title' => $entry['title'].'.avi', // This is a hack so we accept this file by extension.
                    'thumbnail' => $this->api_url.stripslashes($entry['thumbnail']),
                    'videoid' => stripslashes($entry['videoid']),
                    'thumbnail_width' => 150,
                    'thumbnail_height' => 150,
                    'size' => 1 * 1024 * 1024,
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
     * [search description]
     * @param array $q
     * @param array $page
     */
    public function search($q, $page = 0) {
        return $this->service->get_videos();
    }
    /**
     * return list
     * @param array $path
     * @param array $page
     */
    public function get_listing($path='', $page = '') {
        global $OUTPUT;
        $folderurl = $OUTPUT->pix_url('f/folder-128')->out();
        $this->listingurl = $this->zatuk->createlistingapiurl();

        $params = $this->zatuk->get_listing_params();
        $params['currentPath'] = $path ? $path : '/';
        $params['search'] = null;
        $request = new curl();
        $content = $request->post($this->listingurl, $params);

        $content = json_decode($content, true);
        $folderlists = array_merge($content['forganizations'], $content['fdirectories']);
        $fileslist = $content['fvideos'];
        $return = ['dynload' => true, 'nosearch' => false, 'nologin' => true];
        foreach ($content['navPath'] as $paths) {
            $pathelement = [
                'icon' => $OUTPUT->image_url(file_folder_icon(90))->out(false),
                'path' => $paths['navpathdata'],
                'name' => $paths['name'],
            ];
            $return['path'][] = $pathelement;

        }
        $return['list'] = [];
        foreach ($folderlists as $folders) {
            $listelement = [];
            $listelement['thumbnail'] = $folderurl;
            $listelement['thumbnail_width'] = 90;
            $listelement['thumbnail_height'] = 90;
            $listelement['title'] = $folders['fullname'];
            $listelement['path'] = $folders['path'];
            $listelement['children'] = [];
            $return['list'][] = $listelement;
        }
        foreach ($fileslist as $files) {
            $filecontent = [
                'thumbnail' => $this->api_url.'/storage/'.$files['thumbnail'],
                'title' => $files['title'].'.avi',
                'source' => $files['encodedurl'],
                'date' => strtotime($files['timecreated']),
                'license' => 'unknown',
                'thumbnail_title' => $files['title'],
                'encoded_url' => $files['encodedurl'],
            ];
            $return['list'][] = $filecontent;
        }
        return $return;

    }

}
