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

namespace repository_zatuk\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use context_system;
use repository_zatuk\video_service;
/**
 * zatuk repository external API
 *
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class zatuk_get_video_url extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'videoid'   => new external_value(PARAM_RAW, 'Video Id', VALUE_REQUIRED),
        ]);
    }

    /**
     * Returns a response of video object by given videoid.
     * @param string $videoid
     * @return  array
     */
    public static function execute($videoid): array {

        [
            'videoid' => $videoid,
        ] = self::validate_parameters(self::execute_parameters(), [
            'videoid' => $videoid,
        ]);
        self::validate_context(context_system::instance());
        require_capability('repository/zatuk:view', context_system::instance());
        $videoservice = new video_service();
        $video = $videoservice->get_video($videoid);
        return [
            'error'     => $video['error'],
            'message'   => $video['message'],
            'response'  => $video['response']->data,
        ];
    }

    /**
     * Describe the return structure of the external service.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
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
        ]);
    }
}
