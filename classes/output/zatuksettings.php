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
 * repository_zatuk zatuksettings class.
 *
 * @since      Moodle 2.0
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace repository_zatuk\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;
use repository_zatuk\form\configurationform;
/**
 * videos
 */
class zatuksettings implements renderable, templatable {
    /**
     * @var array $context
     */
    protected $context;
    /**
     * @var array $service
     */
    protected $service;
    /**
     * [__construct description]
     * @param \context $context
     */
    public function __construct($context) {

    }
    /**
     * [export_for_template description]
     * @param renderer_base $output
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $USER;
        $data = new stdClass;
        $configurationform = new configurationform();
        $data->register_form = $configurationform->render();
        $data->zatukkey = get_config('repository_zatuk', 'zatuk_key');
        $data->zatuksecret = get_config('repository_zatuk', 'zatuk_secret');
        return $data;
    }




}
