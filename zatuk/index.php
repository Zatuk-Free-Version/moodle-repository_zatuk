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
 * This file used to fetch all uploaded videos and displaying the data.
 *
 * @since Moodle 2.0
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
global $OUTPUT, $CFG, $PAGE;
require_login();
use repository_zatuk\video_service;
$systemcontext = context_system::instance();
$pageurl = new moodle_url('/repository/zatuk/index.php');
$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_context($systemcontext);
$heading = get_string('uploadedvideos', 'repository_zatuk');
$PAGE->set_title($heading);
$PAGE->set_heading($heading);
$PAGE->navbar->add($heading);
echo $OUTPUT->header();
$renderer = $PAGE->get_renderer('repository_zatuk');
echo $renderer->render(new repository_zatuk\output\videos($systemcontext));
echo $OUTPUT->footer();

