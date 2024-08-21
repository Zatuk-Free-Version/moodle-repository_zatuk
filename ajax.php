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
define('AJAX_SCRIPT', true);
require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir.'/externallib.php');
use repository_zatuk\video_service;
global $CFG, $DB, $USER, $PAGE, $OUTPUT, $SESSION;
require_login();
require_capability('repository/zatuk:view', context_system::instance());
$organization = required_param('organization', PARAM_RAW);
$zatukapiurl = required_param('zatuk_api_url', PARAM_RAW);
$organisationcode = required_param('organization', PARAM_RAW);
$email = required_param('email', PARAM_RAW);
$name = required_param('name', PARAM_RAW);
$sdata = new stdClass();
$sdata->email = $email;
$sdata->name = $name;
$sdata->organisationcode = $organisationcode;
$sdata->zatuk_api_url = $zatukapiurl;
$sdata->organization = $organization;
set_config('zatuk_api_url', $zatukapiurl, 'repository_zatuk');
$zatukplans = new video_service();
$response = $zatukplans->zatukingplan($sdata);
echo $response->success;

