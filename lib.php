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
 * zatuk repository plugin
 */
class repository_zatuk extends repository {

    /** Number of videos to load per page */
    const ZATUK_THUMBS_PER_PAGE = 10;
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
     * @var array $zatuk
     */
    protected $zatuk;
    /**
     *  repository_zatuk constructor
     *
     * @param int $repositoryid repository instance id.
     * @param int|stdClass $context a context id or context object.
     * @param array $options repository options.
     * @return void
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = []) {
        parent::__construct($repositoryid, $context, $options);
        $this->zatukkey = get_config('repository_zatuk', 'zatuk_key');
        $this->zatukapiurl = get_config('repository_zatuk', 'zatuk_api_url');
        $this->zatuksecret = get_config('repository_zatuk', 'zatuk_secret');
        $this->service = new app_service($this->zatukkey, $this->zatuksecret);
        $this->zatuk = new phpzatuk($this->zatukapiurl, $this->zatukkey, $this->zatuksecret);

    }

    /**
     * Edit/Create Admin Settings Moodle form.
     *
     * @param object $mform
     * @param string $classname
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
    public function search($q, $page = 0) {
        $result  = [];
        $result['nologin'] = true;
        $result['page'] = (int)$page;
        if ($result['page'] < 1) {
            $result['page'] = 1;
        }
        $start = ($result['page'] - 1) * self::ZATUK_THUMBS_PER_PAGE + 1;
        $start = $start - 1;
        $searchurl = $this->zatuk->createsearchapiurl();
        $params = $this->zatuk->get_listing_params();
        $params['q'] = $q;
        $params['perpage'] = self::ZATUK_THUMBS_PER_PAGE;
        $request = new curl();
        $content = $request->post($searchurl, $params);
        $content = json_decode($content, true);
        $result['list'] = $this->get_collection($content);
        $result['norefresh'] = true;
        $result['nosearch'] = false;
        $result['total'] = $content['meta']['total'];
        $result['pages'] = ceil($content['meta']['total'] / self::ZATUK_THUMBS_PER_PAGE);
        $result['perpage'] = self::ZATUK_THUMBS_PER_PAGE;
        return $result;
    }

    /**
     * Private method to get zatuk search results
     *
     * @param array $content [description]
     * @return array
     */
    private function get_collection($content) {
        $list = [];
        if (count($content['data']) > 0) {
            foreach ($content['data'] as $entry) {
                $list[] = [
                    'shorttitle' => $entry['title'],
                    'thumbnail_title' => $entry['title'],
                    'title' => $entry['title'].'.avi', // This is a hack so we accept this file by extension.
                    'thumbnail' => $this->zatukapiurl.stripslashes($entry['thumbnail']),
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
     * Given a path, and perhaps a search, get a list of files.
     *
     * See details on {@link http://docs.moodle.org/dev/Repository_plugins}
     *
     * @param string $path this parameter can a folder name, or a identification of folder
     * @param string $page the page number of file list
     * @return array the list of files
     */
    public function get_listing($path='', $page = '') {
        global $OUTPUT;
        $folderurl = $OUTPUT->image_url('f/folder-128')->out();
        $listingurl = $this->zatuk->createlistingapiurl();

        $params = $this->zatuk->get_listing_params();
        $params['currentPath'] = $path ? $path : '/';
        $params['search'] = null;
        $request = new curl();
        $content = $request->post($listingurl, $params);

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
                'thumbnail' => $this->zatukapiurl.'/storage/'.$files['thumbnail'],
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
