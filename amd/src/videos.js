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
import {get_string as getString} from 'core/str';
import ModalFactory from 'core/modal_factory';
export const confirmbox = (message) => {
     ModalFactory.create({
        body: message,
        type: ModalFactory.types.ALERT,
        buttons: {
            ok: getString('Thank_you'),
        },
        removeOnClose: true,
      })
      .done(function(modal) {
        modal.show();
      });
};
const videos = (filters) => {
    let promise = Ajax.call([
        {
            methodname: 'repository_zatuk_get_videos',
            args: filters
        }
    ]);
    promise[0].done((response) => {
       confirmbox(response);
    }).fail( (error) => {
      confirmbox(error);
    });
};
export default { videos };