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
 * repository_zatuk upload video form
 *
 * @since Moodle 2.0
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace repository_zatuk\form;

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/formslib.php');
use context_system;

/**
 * upload_video_form
 */
class upload_video_form extends \moodleform {

    /**
     * [definition description]
     */
    public function definition() {
        global $CFG, $USER, $DB, $PAGE;
        $mform = $this->_form;
        $editoroptions = $this->_customdata['editoroptions'];

        // Video Title.
        $mform->addElement('text', 'title', get_string('videotitle', 'repository_zatuk'));
        $mform->addRule('title', get_string('videotitlerequired', 'repository_zatuk'), 'required', null, 'client');
        $mform->setType('title', PARAM_RAW);

        // Video File(Accepts .mp4, .avi, .m4v, .mov).

        $vacceptedformat = ['filearea' => 'zatuk_videos',
                            'component' => 'repository_zatuk',
                            'accepted_types' => ['.mp4', '.avi', '.m4v', '.mov'],
                           ];
        $mform->addElement('filepicker', 'videofile', get_string('videofile', 'repository_zatuk'), null, $vacceptedformat);
        $mform->addHelpButton('videofile', 'videofile', 'repository_zatuk');
        $mform->addRule('videofile', get_string('videofilerequired', 'repository_zatuk'), 'required', null, 'client');

        // Advanced Fields Sections.
        $mform->addElement('header', 'advancedhdr', get_string('advancedfields', 'repository_zatuk'));
        $mform->setExpanded('advancedhdr', false);
        $tagsoptions = [
            'class' => 'tagnameselect',
            'data-class' => 'tagselect',
            'multiple' => true,
            'placeholder' => 'Select Tags',
        ];

        $mform->addElement('autocomplete', 'organization', get_string('category'), $organisations, $organizationoptions);
        $mform->addHelpButton('organization', 'organisation', 'repository_zatuk');
        $mform->setType('organization', PARAM_INT);

        $mform->addElement('autocomplete', 'tags', get_string('videotags', 'repository_zatuk'), $tags, $tagsoptions);
        $mform->addHelpButton('tags', 'tags', 'repository_zatuk');
        $mform->setType('tags', PARAM_INT);
        $mform->addElement('editor', 'description', get_string('videodescription', 'repository_zatuk'), null, $editoroptions);
        $mform->addHelpButton('description', 'description', 'repository_zatuk');
        $mform->setType('description', PARAM_RAW);

        $tacceptedformat = ['accepted_types' => ['.jpg', '.jpeg', '.png']];

        $mform->addElement('filepicker', 'thumbnail', get_string('videothumbnail', 'repository_zatuk'), null, $tacceptedformat );
        $mform->addHelpButton('thumbnail', 'thumbnail', 'repository_zatuk');

        $this->add_action_buttons();

    }
    /**
     * [validation description]
     * @param array $data
     * @param array $files
     */
    public function validation($data, $files) {

    }
}
