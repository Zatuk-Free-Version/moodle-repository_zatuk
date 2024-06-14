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
 * zatuk repository external API
 *
 * @since Moodle 2.0
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
require_once("$CFG->libdir/externallib.php");
use repository_zatuk\video_service;
/**
 * repository_zatuk_external
 */
class repository_zatuk_external extends external_api {

    /**
     * [validate_instance_parameters  description]
     */
    public static function validate_instance_parameters() {
        return new external_function_parameters(
            [
                'value' => new external_value(PARAM_RAW, 'Test Parameter', VALUE_OPTIONAL),
            ]
        );
    }

    /**
     * [validate_instance  description]
     * @param array $value
     */
    public static function validate_instance($value = '') {
        return [
            'success'   => true,
        ];
    }
    /**
     * [validate_instance_returns  description]
     * @return  array external_single_structure
     */
    public static function validate_instance_returns() {
        return new external_single_structure(
            [
                'success'  => new external_value(PARAM_RAW, 'success', VALUE_OPTIONAL),
            ]
        );
    }
    /**
     * [get_videos_parameters  description]
     * @return  array external_function_parameters
     */
    public static function get_videos_parameters() {
        return new external_function_parameters(
            [
                'sorting'   => new external_single_structure(
                    [
                        'key'   => new external_value(PARAM_RAW, 'key', VALUE_OPTIONAL),
                        'order' => new external_value(PARAM_RAW, 'order', VALUE_OPTIONAL),
                    ]
                ),
                'search'    => new external_value(PARAM_RAW, 'search', VALUE_OPTIONAL),
                'status'    => new external_value(PARAM_RAW, 'status', VALUE_OPTIONAL),
            ]
        );
    }
    /**
     * [get_videos  description]
     * @param array $sorting
     * @param array $search
     * @param array $status
     * @return  array
     */
    public static function get_videos($sorting, $search, $status) {
        $filters = new StdClass;
        $filters->search = $search;
        $filters->sort = $sorting;
        $filters->status = $status;
        $videoservice = new video_service();
        $videos = $videoservice->index($filters);
        return $videos;
    }
    /**
     * [get_videos_returns  description]
     * @return  array external_multiple_structure
     */
    public static function get_videos_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'id'            => new external_value(PARAM_INT, 'id', VALUE_REQUIRED),
                    'videoid'       => new external_value(PARAM_RAW, 'videoid', VALUE_REQUIRED),
                    'title'         => new external_value(PARAM_RAW, 'title', VALUE_REQUIRED),
                    'description'   => new external_single_structure(
                        [
                            'format'    => new external_value(PARAM_RAW, 'format', VALUE_OPTIONAL),
                            'text'      => new external_value(PARAM_RAW, 'text', VALUE_OPTIONAL),
                        ]
                    ),
                    'tags'          => new external_value(PARAM_RAW, 'tags', VALUE_REQUIRED),
                    'status'        => new external_value(PARAM_RAW, 'status', VALUE_REQUIRED),
                    'username'      => new external_value(PARAM_RAW, 'username', VALUE_OPTIONAL),
                    'usercreated'   => new external_Value(PARAM_INT, 'usercreated', VALUE_OPTIONAL),
                    'thumbnail'     => new external_value(PARAM_RAW, 'thumbnail', VALUE_OPTIONAL),
                ]
            )
        );
    }
    /**
     * [get_video_url_parameters  description]
     * @return  array external_function_parameters
     */
    public static function get_video_url_parameters() {
        return new external_function_parameters(
            [
                'videoid'   => new external_value(PARAM_RAW, 'Video Id', VALUE_REQUIRED),
            ]
        );
    }
    /**
     * [get_video_url  description]
     * @param array $videoid
     * @return  array
     */
    public static function get_video_url($videoid) {
        $videoservice = new video_service();
        $video = $videoservice->get_video($videoid);
        return [
            'error'     => $video['error'],
            'message'   => $video['message'],
            'response'  => $video['response']->data,
        ];
    }
    /**
     * [get_video_url_parameters  description]
     * @return  array external_single_structure
     */
    public static function get_video_url_returns() {
        return new external_single_structure(
            [
                'error'   => new external_value(PARAM_BOOL, 'error', VALUE_REQUIRED),
                'message'   => new external_value(PARAM_RAW, 'message', VALUE_OPTIONAL),
                'response'  => new external_single_structure(
                    [
                        'id'            => new external_value(PARAM_RAW, 'id', VALUE_REQUIRED),
                        'title'         => new external_value(PARAM_RAW, 'title', VALUE_OPTIONAL),
                        'duration'      => new external_value(PARAM_RAW, 'duration', VALUE_OPTIONAL),
                        'usercreated'   => new external_value(PARAM_RAW, 'usercreated', VALUE_OPTIONAL),
                        'usermodified'  => new external_value(PARAM_RAW, 'usermodified', VALUE_OPTIONAL),
                        'videoid'       => new external_value(PARAM_RAW, 'videoid', VALUE_REQUIRED),
                        'player_url'    => new external_value(PARAM_RAW, 'player_url', VALUE_REQUIRED),
                    ]
                ),
            ]
        );
    }

    /**
     * [enable_zatuk_parameters  description]
     * @return  array external_function_parameters
     */
    public static function enable_zatuk_parameters() {
        return new external_function_parameters(
            [
                'value' => new external_value(PARAM_RAW, 'Test Parameter', VALUE_OPTIONAL),
            ]
        );
    }

    /**
     * [enable_zatuk  description]
     * @param array $value
     */
    public static function enable_zatuk($value = '') {
        $enablezatuk = new video_service();
        $response = $enablezatuk->enablezatuk();
        if ($response) {
            $result = true;
        } else {
            $result = false;
        }
        return [
            'success'   => $result,
        ];
    }
    /**
     * [enable_zatuk_returns  description]
     * @return  array external_single_structure
     */
    public static function enable_zatuk_returns() {
        return new external_single_structure(
            [
                'success'  => new external_value(PARAM_RAW, 'success', VALUE_OPTIONAL),
            ]
        );
    }
    /**
     * [zatukplans_parameters  description]
     * @return  array external_function_parameters
     */
    public static function zatukplans_parameters() {
        return new external_function_parameters(
            [
                'organization' => new external_value(PARAM_RAW, 'organization'),
                'zatuk_api_url' => new external_value(PARAM_RAW, 'zatuk_api_url'),
                'organisationcode' => new external_value(PARAM_RAW, 'organisationcode'),
                'email' => new external_value(PARAM_RAW, 'email'),
                'name' => new external_value(PARAM_RAW, 'name'),
            ]
        );
    }

    /**
     * [zatukplans  description]
     * @param array $organization
     * @param array $zatukapiurl
     * @param array $organisationcode
     * @param array $email
     * @param array $name
     */
    public static function zatukplans($organization='', $zatukapiurl='', $organisationcode='', $email='', $name='') {

         $params = self::validate_parameters(
            self::zatukplans_parameters(),
            [
                'organization' => $organization,
                'zatuk_api_url' => $zatukapiurl,
                'organisationcode' => $organisationcode,
                'email' => $email,
                'name' => $name,
            ]
        );
        $data = [];
        $stable = new stdClass();
        $stable->email = $email;
        $stable->name = $name;
        $stable->organisationcode = $organisationcode;
        $stable->zatuk_api_url = $zatukapiurl;
        $stable->organization = $organization;
        set_config('zatuk_api_url', $zatukapiurl, 'repository_zatuk');
        $zatukplans = new video_service();
        $response = $zatukplans->zatukingplan($stable);
        $arr = json_decode(json_encode ($response->errors ) , true);
        foreach ($arr as $key => $value) {
            $errors[$key] = json_decode(json_encode ($value[0]) , true);
            $errormessage = $errors['token'] . $errors['url'] . $errors['email'];
            $errormessage .= $errors['shortname'] .$errors['organization_name'] . $errors['name'];
        }
        $data['success'] = $response->success;
        $data['error'] = $response->error;
        $data['message'] = $response->message;
        $data['errormessage'] = $errormessage;
        return $data;
    }
    /**
     * [enable_zatuk_returns  description]
     * @return  array external_single_structure
     */
    public static function zatukplans_returns() {
         return new external_single_structure(
            [
            'success' => new external_value(PARAM_RAW, 'success'),
            'error' => new external_value(PARAM_RAW, 'error'),
            'message' => new external_value(PARAM_RAW, 'message'),
            'errormessage' => new external_value(PARAM_RAW, 'errors'),
            ]
        );
    }

    /**
     * [updatezatuksettings_parameters  description]
     * @return  array external_function_parameters
     */
    public static function updatezatuksettings_parameters() {
        return new external_function_parameters(
            [
                'organization' => new external_value(PARAM_RAW, 'organization'),
                'email' => new external_value(PARAM_RAW, 'email'),
                'name' => new external_value(PARAM_RAW, 'name'),
            ]
        );
    }

    /**
     * [zatukplans  description]
     * @param array $organization
     * @param array $email
     * @param array $name
     */
    public static function updatezatuksettings($organization='', $email='', $name='') {

         $params = self::validate_parameters(
            self::updatezatuksettings_parameters(),
            [
                'organization' => $organization,
                'email' => $email,
                'name' => $name,
            ]
        );
        $stable = new stdClass();
        $stable->email = $email;
        $stable->name = $name;
        $stable->organization = $organization;
        $updatezatuksettings = new video_service();
        $response = $updatezatuksettings->updatezatuksetting($stable);
        return $response;
    }
    /**
     * [updatezatuksettings  description]
     * @return  array external_single_structure
     */
    public static function updatezatuksettings_returns() {
        return new external_value(PARAM_BOOL, 'return');

    }
}

