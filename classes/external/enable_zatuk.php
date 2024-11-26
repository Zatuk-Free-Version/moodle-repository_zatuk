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
use repository_zatuk\zatuk_constants as zc;

/**
 * zatuk repository external API
 *
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enable_zatuk extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
           'haskeygenerated' => new external_value(PARAM_INT, 'haskeygenerated'),
        ]);
    }

    /**
     * Returns enable response.
     * @param int $haskeygenerated
     * @return array
     */
    public static function execute($haskeygenerated) {
        global $DB;

        [
            'haskeygenerated' => $haskeygenerated,
        ] = self::validate_parameters(self::execute_parameters(), [
            'haskeygenerated' => $haskeygenerated,
        ]);
        self::validate_context(context_system::instance());
        require_capability('repository/zatuk:view', context_system::instance());
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
     * Describe the return structure of the external service.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'success'  => new external_value(PARAM_INT, 'success'),
        ]);
    }
}
