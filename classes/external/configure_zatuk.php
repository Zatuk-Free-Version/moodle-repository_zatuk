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
class configure_zatuk extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'organization' => new external_value(PARAM_RAW, 'organization'),
            'zatukapiurl' => new external_value(PARAM_RAW, 'zatukapiurl'),
            'organizationcode' => new external_value(PARAM_RAW, 'organizationcode'),
            'email' => new external_value(PARAM_RAW, 'email'),
            'name' => new external_value(PARAM_RAW, 'name'),
        ]);
    }

    /**
     * Generates the token with the give passed parameters .
     * @param string||null $organization
     * @param string||null $zatukapiurl
     * @param string||null $organizationcode
     * @param string||null $email
     * @param string||null $name
     * @return array
     */
    public static function execute(
        $organization = '',
        $zatukapiurl = '',
        $organizationcode = '',
        $email = '',
        $name = '',

    ): array {

        [
            'organization' => $organization,
            'zatukapiurl' => $zatukapiurl,
            'organizationcode' => $organizationcode,
            'email' => $email,
            'name' => $name,
        ] = self::validate_parameters(self::execute_parameters(), [
            'organization' => $organization,
            'zatukapiurl' => $zatukapiurl,
            'organizationcode' => $organizationcode,
            'email' => $email,
            'name' => $name,
        ]);
        self::validate_context(context_system::instance());
        require_capability('repository/zatuk:view', context_system::instance());
        $data = [];
        $sdata = new stdClass();
        $sdata->email = $email;
        $sdata->name = $name;
        $sdata->organizationcode = $organizationcode;
        $sdata->zatukapiurl = $zatukapiurl;
        $sdata->organization = $organization;
        set_config('zatukapiurl', $zatukapiurl, 'repository_zatuk');
        $videoservice = new video_service();
        $response = $videoservice->configure_zatuk_repository($sdata);
        $arr = json_decode(json_encode ($response->errors) , true);
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
     * Describe the return structure of the external service.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_RAW, 'success'),
            'error' => new external_value(PARAM_RAW, 'error'),
            'message' => new external_value(PARAM_RAW, 'message'),
            'errormessage' => new external_value(PARAM_RAW, 'errors'),
        ]);
    }
}
