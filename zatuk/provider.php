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
 * @since Moodle 2.0
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_zatuk\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;

/**
 * provider
 */
class provider implements \core_privacy\local\metadata\provider, \core_privacy\local\request\plugin\provider {
    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no data.
     *
     * @return  string
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }
    /**
     * Get information about the user data stored by this plugin.
     *
     * @param  collection $collection An object for storing metadata.
     * @return collection The metadata.
     */
    public static function get_metadata(collection $collection): collection {
        $repositoryzatukaccess = [
            'id' => 'privacy:metadata:id',
            'costcenterid' => 'privacy:metadata:costcenterid',
            'channelid' => 'privacy:metadata:channelid',
            'timemodified' => 'privacy:metadata:timemodified',
            'timecreated' => 'privacy:metadata:timecreated',
            'usermodified' => 'privacy:metadata:usermodified',
        ];
        $collection->add_database_table('repository_zatuk_access',
            $repositoryzatukaccess,
            'privacy:metadata:repository_zatuk_access'
        );

        return $collection;
    }
    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $params = ['userid' => $userid, 'contextuser' => CONTEXT_USER];
        $sql = "SELECT id
                  FROM {context}
                 WHERE instanceid = :userid and contextlevel = :contextuser";
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);
        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_user) {
            return;
        }

        $userlist->add_user($context->instanceid);
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param   context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        // Only delete data for a user context.
        if ($context->contextlevel == CONTEXT_USER) {
            static::delete_user_data($context->instanceid, $context);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        $context = $userlist->get_context();

        if ($context instanceof \context_user) {
            static::delete_user_data($context->instanceid, $context);
        }
    }

    /**
     * Deletes non vital information about a user.
     *
     * @param  int      $userid  The user ID to delete
     * @param  \context $context The user context
     */
    protected static function delete_user_data(int $userid, \context $context) {
        global $DB;
    }
}
