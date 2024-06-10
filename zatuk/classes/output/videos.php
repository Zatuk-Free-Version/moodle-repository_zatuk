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
 * repository_zatuk videos class.
 *
 * @since Moodle 2.0
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace repository_zatuk\output;

use renderable;
use renderer_base;
use templatable;
use repository_zatuk\video_service;
/**
 * videos
 */
class videos implements renderable, templatable {
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
     * @param array $context
     */
    public function __construct($context) {
        $this->context = $context;
        $this->service = new video_service();
    }
    /**
     * [export_for_template description]
     * @param renderer_base $output
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;
        $videos = ['ids' => ['6458938524810', '645893af41d20']];
        $payload = [
            'videoids'  => json_encode($videos),
        ];
        $data = new \stdClass();
        $data->all = true;
        $data->uploadvideosurl = $CFG->wwwroot.'/repository/zatuk/upload_video.php';
        $data->videos = $this->service->index($payload);
        return $data;
    }

}
