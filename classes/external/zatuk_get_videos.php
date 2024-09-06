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
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use context_system;
use StdClass;
use repository_zatuk\video_service;
/**
 * zatuk repository external API
 *
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class zatuk_get_videos extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'sorting'   => new external_single_structure(
                [
                    'key'   => new external_value(PARAM_RAW, 'key', VALUE_OPTIONAL),
                    'order' => new external_value(PARAM_RAW, 'order', VALUE_OPTIONAL),
                ]
            ),
            'search'    => new external_value(PARAM_RAW, 'search'),
            'status'    => new external_value(PARAM_RAW, 'status'),
        ]);
    }

    /**
     * Returns a list of videos in a provided list of filters.
     * @param array $sorting
     * @param array $search
     * @param array $status
     * @return  array
     */
    public static function execute(
        $sorting,
        $search,
        $status,
    ): array {

        [
            'sorting' => $sorting,
            'search' => $search,
            'status' => $status,
        ] = self::validate_parameters(self::execute_parameters(), [
            'sorting' => $sorting,
            'search' => $search,
            'status' => $status,
        ]);
        self::validate_context(context_system::instance());
        require_capability('repository/zatuk:view', context_system::instance());
        $filters = new StdClass;
        $filters->search = $search;
        $filters->sort = $sorting;
        $filters->status = $status;
        $videoservice = new video_service();
        $videos = $videoservice->get_uploaded_videos($filters);
        return $videos;
    }

    /**
     * Describe the return structure of the external service.
     *
     * @return external_multiple_structure
     */
    public static function execute_returns() {
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
}
