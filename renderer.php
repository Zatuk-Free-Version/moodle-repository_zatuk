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
 * zatuk repository renderer.
 *
 * @since       Moodle 2.0
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use repository_zatuk\output\videos;
use repository_zatuk\output\zatuksettings;
use repository_zatuk\output\zatukconfiguration;

/**
 * repository_zatuk_renderer
 */
class repository_zatuk_renderer extends plugin_renderer_base {
    /**
     * render_videos
     * @param videos $output
     */
    public function render_videos(videos $output) {
        return $this->render_from_template('repository_zatuk/videos/index', $output->export_for_template($this));
    }

    /**
     * render_zatuksettings
     * @param zatuksettings $output
     */
    public function render_zatuksettings(zatuksettings $output) {
        return $this->render_from_template('repository_zatuk/zatuk_view', $output->export_for_template($this));
    }
}
