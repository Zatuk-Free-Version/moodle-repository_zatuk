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
use stdClass;
use repository_zatuk\video_service;
/**
 * zatuk repository external API
 *
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_zatuk_settings extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'organization' => new external_value(PARAM_TEXT, 'organization', VALUE_REQUIRED),
            'email' => new external_value(PARAM_EMAIL, 'email', VALUE_REQUIRED),
            'name' => new external_value(PARAM_TEXT, 'name', VALUE_REQUIRED),
        ]);
    }

    /**
     * Returns the updated response.
     * @param string $organization
     * @param string $email
     * @param string $name
     * @return bool
     */
    public static function execute(
        $organization,
        $email,
        $name,
    ): array {

        [
            'organization' => $organization,
            'email' => $email,
            'name' => $name,
        ] = self::validate_parameters(self::execute_parameters(), [
            'organization' => $organization,
            'email' => $email,
            'name' => $name,
        ]);
        self::validate_context(context_system::instance());
        $sdata = new stdClass();
        $sdata->email = $email;
        $sdata->name = $name;
        $sdata->organization = $organization;
        $videoservice = new video_service();
        $response = $videoservice->update_zatuk_configuration_setting($sdata);
        return ['success'   => $response];
    }

    /**
     * Describe the return structure of the external service.
     *
     * @return external_single_structure
     */
    public static function execute_returns() {
        return new external_single_structure([
            'success'  => new external_value(PARAM_RAW, 'success', VALUE_OPTIONAL),
        ]);
    }
}
