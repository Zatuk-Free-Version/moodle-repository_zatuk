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
 *  repository_zatuk upload_zatuk_videos scheduled task.
 *
 * @since Moodle 2.0
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 namespace repository_zatuk\task;

 use repository_zatuk\video_service;

/**
 * upload_zatuk_videos
 */
class upload_zatuk_videos extends \core\task\scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins)
     * @return string
     */
    public function get_name() {
        return get_string('upload_videos', 'repository_zatuk');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        $service = new video_service();
        $service->upload_videos_to_zatuk();
    }
}
