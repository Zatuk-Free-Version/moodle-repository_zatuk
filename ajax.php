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
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('AJAX_SCRIPT', true);
require_once('../../config.php');
use repository_zatuk\zatuk_constants as zc;
require_login();
require_capability('repository/zatuk:view', context_system::instance());
$organization = required_param('organization', PARAM_RAW);
$organizationcode = required_param('organizationcode', PARAM_RAW);
$email = required_param('email', PARAM_RAW);
$name = required_param('name', PARAM_RAW);
$zatukapiurl = zc::ZATUK_API_URL;
$response = false;
if ($zatukapiurl) {
    $response = set_config('zatukapiurl', $zatukapiurl, 'repository_zatuk');
}
if ($organization) {
    $response = set_config('organization', $organization, 'repository_zatuk');
}
if ($organizationcode) {
    $response = set_config('organizationcode', $organizationcode, 'repository_zatuk');
}
if ($email) {
    $response = set_config('email', $email, 'repository_zatuk');
}
if ($name) {
    $response = set_config('name', $name, 'repository_zatuk');
}

echo json_encode($response, true);

