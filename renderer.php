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

use repository_zatuk\output\zatuksettings;
use repository_zatuk\output\zatukconfiguration;

/**
 *  Zatuk repository renderer class.
 */
class repository_zatuk_renderer extends plugin_renderer_base {

    /**
     * Renders the repository zatuk settinfs.
     * @param zatuksettings $output
     * @return bool|string the rendered output
     */
    public function render_zatuksettings(zatuksettings $output) {
        return $this->render_from_template('repository_zatuk/zatuk_view', $output->export_for_template($this));
    }
}
