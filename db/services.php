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
 * repository_zatuk external functions and service definitions.
 *
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot.'/repository/zatuk/lib.php');
$functions = [
    'repository_zatuk_get_video_url'   => [

        'classname'     => 'repository_zatuk_external',
        'methodname'    => 'zatuk_get_video_url',
        'classpath'     => 'repository/zatuk/classes/external.php',
        'description'   => 'Gets dynamic video player url from zatuk',
        'type'          => 'read',
        'capabilities'  => 'repository/zatuk:processzatukrepository',
        'services'      => [MOODLE_ZATUK_WEB_SERVICE],
        'ajax'          => true,
        'loginrequired' => true,
    ],
    'repository_zatuk_get_videos'   => [
        'classname'     => 'repository_zatuk_external',
        'methodname'    => 'zatuk_get_videos',
        'classpath'     => 'repository/zatuk/classes/external.php',
        'description'   => 'gets list of available videos',
        'type'          => 'read',
        'capabilities'  => 'repository/zatuk:processzatukrepository',
        'services'      => [MOODLE_ZATUK_WEB_SERVICE],
        'ajax'          => true,
        'loginrequired' => true,
    ],
    'repository_configure_zatuk'   => [
        'classname'     => 'repository_zatuk_external',
        'methodname'    => 'configure_zatuk',
        'classpath'     => 'repository/zatuk/classes/external.php',
        'description'   => 'Configure zatuk plan',
        'type'          => 'read',
        'capabilities'  => 'repository/zatuk:processzatukrepository',
        'services'      => [MOODLE_ZATUK_WEB_SERVICE],
        'ajax'          => true,
        'loginrequired' => true,
    ],
    'repository_enable_zatuk'   => [
        'classname'     => 'repository_zatuk_external',
        'methodname'    => 'enable_zatuk',
        'classpath'     => 'repository/zatuk/classes/external.php',
        'description'   => 'enables the zatuk plugin',
        'type'          => 'read',
        'capabilities'  => 'repository/zatuk:processzatukrepository',
        'services'      => [MOODLE_ZATUK_WEB_SERVICE],
        'ajax'          => true,
        'loginrequired' => true,
    ],
    'repository_update_zatuk_settings'   => [
        'classname'     => 'repository_zatuk_external',
        'methodname'    => 'update_zatuk_settings',
        'classpath'     => 'repository/zatuk/classes/external.php',
        'description'   => 'updates the zatuk settings',
        'type'          => 'read',
        'capabilities'  => 'repository/zatuk:processzatukrepository',
        'services'      => [MOODLE_ZATUK_WEB_SERVICE],
        'ajax'          => true,
        'loginrequired' => true,
    ],
];
$services = [
   'Zatuk Webservices'  => [
        'functions' => [], // Unused as we add the service in each function definition, third party services would use this.
        'enabled' => 1,
        'restrictedusers' => 0,
        'shortname' => 'zatuk_web_service',
        'downloadfiles' => 1,
        'uploadfiles' => 1,
    ],
];


