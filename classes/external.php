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
 * @since      Moodle 2.0
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir.'/externallib.php');
use repository_zatuk\video_service;
use repository_zatuk\zatuk_constants as zc;
/**
 * repository_zatuk_external
 */
class repository_zatuk_external extends external_api {

    /**
     * Describes the parameters for zatuk_get_videos .
     *
     * @return  external_function_parameters
     */
    public static function zatuk_get_videos_parameters() {
        return new external_function_parameters(
            [
                'sorting'   => new external_single_structure(
                    [
                        'key'   => new external_value(PARAM_RAW, 'key', VALUE_OPTIONAL),
                        'order' => new external_value(PARAM_RAW, 'order', VALUE_OPTIONAL),
                    ]
                ),
                'search'    => new external_value(PARAM_RAW, 'search'),
                'status'    => new external_value(PARAM_RAW, 'status'),
            ]
        );
    }
    /**
     * Returns a list of videos in a provided list of filters.
     * @param array $sorting
     * @param string $search
     * @param string $status
     * @return  array
     */
    public static function zatuk_get_videos($sorting, $search, $status) {
        $params = self::validate_parameters(
            self::zatuk_get_videos_parameters(),
            [
                'sorting' => $sorting,
                'search' => $search,
                'status' => $status,
            ]
        );
        self::validate_context(context_system::instance());
        $filters = new StdClass;
        $filters->search = $search;
        $filters->sort = $sorting;
        $filters->status = $status;
        $videoservice = new video_service();
        $videos = $videoservice->get_uploaded_videos($filters);
        return $videos;
    }
    /**
     * Describes the zatuk_get_videos return value.
     *
     * @return  external_multiple_structure
     */
    public static function zatuk_get_videos_returns() {
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
     * Describes the parameters for zatuk_get_video_url .
     *
     * @return  external_function_parameters
     */
    public static function zatuk_get_video_url_parameters() {
        return new external_function_parameters(
            [
                'videoid'   => new external_value(PARAM_RAW, 'Video Id', VALUE_REQUIRED),
            ]
        );
    }
    /**
     * Returns a response of video object by given videoid.
     * @param string $videoid
     * @return  array
     */
    public static function zatuk_get_video_url($videoid) {
        $params = self::validate_parameters(
            self::zatuk_get_video_url_parameters(),
            [
                'videoid' => $videoid,
            ]
        );
        self::validate_context(context_system::instance());
        $videoservice = new video_service();
        $video = $videoservice->get_video($videoid);
        return [
            'error'     => $video['error'],
            'message'   => $video['message'],
            'response'  => $video['response']->data,
        ];
    }
    /**
     * Describes the zatuk_get_video_url return value.
     *
     * @return external_single_structure
     */
    public static function zatuk_get_video_url_returns() {
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
     * Describes the parameters for enable_zatuk .
     *
     * @return external_function_parameters
     */
    public static function enable_zatuk_parameters() {
        return new external_function_parameters(
            [
                'haskeygenerated' => new external_value(PARAM_INT, 'haskeygenerated'),
            ]
        );
    }

    /**
     * Returns enable response.
     * @param int $haskeygenerated
     * @return array
     */
    public static function enable_zatuk($haskeygenerated) {
        $params = self::validate_parameters(
            self::enable_zatuk_parameters(),
            [
                'haskeygenerated' => $haskeygenerated,
            ]
        );
        self::validate_context(context_system::instance());
        $videoservice = new video_service();
        $result = zc::DEFAULTSTATUS;
        if ($haskeygenerated) {
            $response = $videoservice->enablezatuk();
            $result = ($response) ? zc::STATUSA : zc::DEFAULTSTATUS;
        } else {
            $videoservice->remove_zatuk_settings();
            $result = zc::DEFAULTSTATUS;
        }
        return ['success' => $result];
    }
    /**
     * Describes the enable_zatuk return value.
     *
     * @return external_single_structure
     */
    public static function enable_zatuk_returns() {
        return new external_single_structure(
            [
                'success'  => new external_value(PARAM_RAW, 'success', VALUE_OPTIONAL),
            ]
        );
    }
    /**
     * Describes the parameters for configure_zatuk .
     *
     * @return external_function_parameters
     */
    public static function configure_zatuk_parameters() {
        return new external_function_parameters(
            [
               'organization' => new external_value(PARAM_TEXT, 'organization', VALUE_REQUIRED),
                'organizationcode' => new external_value(PARAM_TEXT, 'organizationcode', VALUE_REQUIRED),
                'email' => new external_value(PARAM_EMAIL, 'email', VALUE_REQUIRED),
                'name' => new external_value(PARAM_TEXT, 'name', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * Generates the token with the give passed parameters .
     * @param string $organization
     * @param string $organizationcode
     * @param string $email
     * @param string $name
     * @return array
     */
    public static function configure_zatuk($organization,
        $organizationcode,
        $email,
        $name) {

        $params = self::validate_parameters(
            self::configure_zatuk_parameters(),
            [
                'organization' => $organization,
                'organizationcode' => $organizationcode,
                'email' => $email,
                'name' => $name,
            ]
        );
        self::validate_context(context_system::instance());
        $sdata = new stdClass();
        $sdata->email = $email;
        $sdata->name = $name;
        $sdata->organizationcode = $organizationcode;
        $sdata->organization = $organization;
        $zatukapiurl = zc::ZATUK_API_URL;
        set_config('zatukapiurl', $zatukapiurl, 'repository_zatuk');
        $videoservice = new video_service();
        $response = $videoservice->configure_zatuk_repository($sdata);
        $success = zc::DEFAULTSTATUS;
        $message = '';
        if ($response->success) {
            $success = zc::STATUSA;
            $message = $response->message;
        } else {
            $success = zc::DEFAULTSTATUS;
            $message = $response->message;
        }
        return ['success' => $success, 'message' => $message];
    }
    /**
     * Describes the configure_zatuk return value.
     * @return  external_single_structure
     */
    public static function configure_zatuk_returns() {
         return new external_single_structure(
            [
            'success' => new external_value(PARAM_RAW, 'success'),
            'message' => new external_value(PARAM_RAW, 'message'),
            ]
        );
    }

    /**
     * Describes the parameters for update_zatuk_settings .
     *
     * @return external_function_parameters
     */
    public static function update_zatuk_settings_parameters() {
        return new external_function_parameters(
            [
                'organization' => new external_value(PARAM_TEXT, 'organization', VALUE_REQUIRED),
                'email' => new external_value(PARAM_EMAIL, 'email', VALUE_REQUIRED),
                'name' => new external_value(PARAM_TEXT, 'name', VALUE_REQUIRED),
            ]
        );
    }
    /**
     * Returns the updated response.
     * @param string $organization
     * @param string $email
     * @param string $name
     * @return array
     */
    public static function update_zatuk_settings($organization, $email, $name) {

         $params = self::validate_parameters(
            self::update_zatuk_settings_parameters(),
            [
                'organization' => $organization,
                'email' => $email,
                'name' => $name,
            ]
        );
        self::validate_context(context_system::instance());
        $sdata = new stdClass();
        $sdata->email = $email;
        $sdata->name = $name;
        $sdata->organization = $organization;
        $videoservice = new video_service();
        $response = $videoservice->update_zatuk_configuration_setting($sdata);
        return ['success'   => $response];;
    }
    /**
     * Describes the update_zatuk_settings return value.
     *
     * @return external_single_structure
     */
    public static function update_zatuk_settings_returns() {
        return new external_single_structure([
            'success'  => new external_value(PARAM_RAW, 'success', VALUE_OPTIONAL),
        ]);

    }
}
