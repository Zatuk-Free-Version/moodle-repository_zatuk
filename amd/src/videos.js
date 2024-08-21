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
 * Defines zatuk video script.
 *
 * @since      Moodle 2.0
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import Ajax from 'core/ajax';
import messagemodal from 'repository_zatuk/messagemodal';
let MessageModal = new messagemodal();

const videos = (filters) => {
    let promise = Ajax.call([
        {
            methodname: 'repository_zatuk_get_videos',
            args: filters
        }
    ]);
    promise[0].done((response) => {
       MessageModal.confirmbox(response);
    }).fail( (error) => {
      MessageModal.confirmbox(error);
    });
};
export default { videos };
