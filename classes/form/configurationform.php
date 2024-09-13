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
 * repository_zatuk configurationform form
 *
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace repository_zatuk\form;

use moodleform;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->libdir . '/formslib.php');
/**
 * configurationform
 */
class configurationform extends moodleform {

    /**
     * Define this form - called by the parent constructor
     */
    public function definition() {
        global $USER;
        $mform = $this->_form;
        $siteconfiguration  = get_site();
        $name = get_config('repository_zatuk', 'name');
        $email = get_config('repository_zatuk', 'email');
        $organization = get_config('repository_zatuk', 'organization');
        $organizationcode = get_config('repository_zatuk', 'organizationcode');
        $zatukkey = get_config('repository_zatuk', 'zatuk_key');
        $zatuksecret = get_config('repository_zatuk', 'zatuk_secret');

        if (empty($name)) {
            $name = $USER->username;
        }

        if (empty($email)) {
            $email = $USER->email;
        }

        if (empty($organization)) {
            $organization = $siteconfiguration->fullname;
        }

        if (empty($organizationcode)) {
            $organizationcode = $siteconfiguration->shortname;
        }

        if (empty($zatukkey)) {
            $zatukkey = get_config('repository_zatuk', 'zatuk_key');
        }
        if (empty($zatuksecret)) {
            $zatuksecret = get_config('repository_zatuk', 'zatuk_secret');
        }

        $mform->addElement('text', 'name', get_string('name'), ['value' => $name]);
        $mform->setType('name', PARAM_RAW_TRIMMED);
        $mform->addHelpButton('name', 'name_content', 'repository_zatuk');
        $mform->addRule('name', get_string('required', 'repository_zatuk'), 'required', null, 'client');

        $mform->addElement('text', 'email', get_string('email'), ['value' => $email]);
        $mform->setType('email', PARAM_RAW_TRIMMED);
        $mform->addHelpButton('email', 'email_content', 'repository_zatuk');
        $mform->addRule('email', get_string('required', 'repository_zatuk'), 'required', null, 'client');

        $mform->addElement('text', 'organization', get_string('organization', 'repository_zatuk'), ['value' => $organization]);
        $mform->setType('organization', PARAM_RAW_TRIMMED);
        $mform->addHelpButton('organization', 'organization_content', 'repository_zatuk');
        $mform->addRule('organization', get_string('required', 'repository_zatuk'), 'required', null, 'client');

        $orgcodestring = get_string('organization_code', 'repository_zatuk');

        if ($zatukkey == "" && $zatuksecret == "") {
            $mform->addElement('text', 'organization_code', $orgcodestring, ['value' => $organizationcode]);
            $mform->setType('organization_code', PARAM_RAW_TRIMMED);
            $mform->addHelpButton('organization_code', 'organization_code_content', 'repository_zatuk');
            $mform->addRule('organization_code', get_string('required', 'repository_zatuk'), 'required', null, 'client');

            $mform->addElement('submit', 'submit', get_string('zatuksettings', 'repository_zatuk'),
            ['data-action' => 'zatuksettings']);
        } else {
            $orgcodevalues = ['value' => $organizationcode, "disabled" => "disabled"];
            $mform->addElement('text', 'organization_code', $orgcodestring , $orgcodevalues);
            $mform->setType('organization_code', PARAM_RAW_TRIMMED);
            $mform->addHelpButton('organization_code', 'organization_code_content', 'repository_zatuk');
            $mform->addRule('organization_code', get_string('required', 'repository_zatuk'), 'required', null, 'client');

            $mform->addElement('submit', 'submit', get_string('zatuksettingsupdate', 'repository_zatuk'),
            ['data-action' => 'updatesettings']);
        }
    }
}
