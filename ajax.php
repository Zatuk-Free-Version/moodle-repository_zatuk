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
 * This file uset to fetch information from repository_zatuk config and display the data.
 *
 * @since      Moodle 2.0
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use repository_zatuk\app_service;

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir.'/externallib.php');
global $CFG, $DB, $USER, $PAGE, $OUTPUT, $SESSION;
require_login();
require_capability('repository/zatuk:view', context_system::instance());
$organization = required_param('organization', PARAM_RAW);
$zatukapiurl = required_param('zatuk_api_url', PARAM_RAW);
$organisationcode = required_param('organization', PARAM_RAW);
$email = required_param('email', PARAM_RAW);
$name = required_param('name', PARAM_RAW);
$service = $DB->get_record('external_services', ['shortname' => 'zatuk_web_service', 'enabled' => 1]);
if ($service) {
    $conditions = [
        'userid' => $USER->id,
        'externalserviceid' => $service->id,
        'tokentype' => EXTERNAL_TOKEN_PERMANENT,
    ];
        $existingtokens = $DB->get_record('external_tokens', $conditions, 'token', IGNORE_MISSING);
    if ($existingtokens) {
        $token = $existingtokens->token;
    } else {
        $token = external_generate_token(EXTERNAL_TOKEN_PERMANENT, $service->id, $USER->id, context_system::instance(), 0);
    }

} else {
    $token = '';
}
$service = new app_service();
set_config('zatuk_api_url', $zatukapiurl, 'repository_zatuk');
$response = $service->upgrade_package($name, $email, $token, $organization, $organisationcode);
$response = $response['response'];
if (!$response->success) {
    if ($response->errors && is_object($response->errors)) {
        foreach ($response->errors as $key => $apierror) {
            if ($key == 'token') {
                $errors['moodle_token'] = $apierror[0];
            } else {
                $errors[$key] = $apierror[0];
            }
        }
    }
    $errors['generic_errors'] = $response->message;
} else {

    if ($organization) {
        set_config('organization', $organization, 'repository_zatuk');
    }
    if ($organisationcode) {
        set_config('organisationcode', $organisationcode, 'repository_zatuk');
    }
    if ($email) {
        set_config('email', $email, 'repository_zatuk');
    }
    if ($name) {
        set_config('name', $name, 'repository_zatuk');
    }

    if ($response->api_info && !empty($response->api_info->key)) {
        set_config('zatuk_key', $response->api_info->key, 'repository_zatuk');
    }

    if ($response->api_info && !empty($response->api_info->secret)) {
        set_config('zatuk_secret', $response->api_info->secret, 'repository_zatuk');
    }
}
echo $response->success;
