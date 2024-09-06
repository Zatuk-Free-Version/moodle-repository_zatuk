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
 * Defines zatuk player action script.
 *
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import $ from 'jquery';
import Ajax from 'core/ajax';
import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import Templates from 'core/templates';
import videojs from 'media_videojs/video-lazy';
import messagemodal from 'repository_zatuk/messagemodal';

let MessageModal = new messagemodal();
export const play = ( root ) => {
    $(root).on('click', (e) => {
        e.preventDefault();
        let args = $(root).data();
        get_video_url(args.video);
    });
};
const get_video_url = (videoid) => {
    let promise = Ajax.call([
        {
            methodname: 'repository_zatuk_get_video_url',
            args: {
                videoid: videoid
            }
        }
    ]);
    promise[0].done((response) => {
        if(!response.error){
            let video = response.response;
            ModalFactory.create({
                title: video.title,
                type: ModalFactory.types.DEFAULT,
                body: Templates.render('repository_zatuk/videos/player', video)
            }).done((modal) => {
                modal.show();
                modal.getRoot().on(ModalEvents.shown, () => {
                    const player = videojs('zatuk_player_'+videoid);
                    player.src({
                        autoplay:true,
                        controls:true,
                        src: video.player_url,
                        type: 'application/x-mpegURL'
                    });
                    player.play();
                });
                modal.getRoot().on(ModalEvents.hidden, function(){
                    let player = videojs('zatuk_player_'+videoid);
                    player.dispose();
                });
            });
        }else{

        }
    }).fail((error) => {
       MessageModal.confirmbox(error);
    });
};
export default { play };