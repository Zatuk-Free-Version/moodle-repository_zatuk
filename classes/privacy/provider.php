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
 * repository_zatuk provider class.
 *
 * @since      Moodle 2.0
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace repository_zatuk\privacy;

use core_privacy\local\metadata\collection;

/**
 * Privacy Subsystem for repository_zatuk implementing metadata, plugin providers.
 *
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_user_preference(
            'organization',
            'privacy:metadata:repository_zatuk:organization'
        );

        $collection->add_user_preference(
            'organisationcode',
            'privacy:metadata:repository_zatuk:organisationcode'
        );

        $collection->add_user_preference(
            'name',
            'privacy:metadata:repository_zatuk:name'
        );

        $collection->add_user_preference(
            'email',
            'privacy:metadata:repository_zatuk:email'
        );

        $collection->add_user_preference(
            'zatuk_key',
            'privacy:metadata:repository_zatuk:zatuk_key'
        );

         $collection->add_user_preference(
            'zatuk_secret',
            'privacy:metadata:repository_zatuk:zatuk_secret'
        );

        return $collection;
    }

}


