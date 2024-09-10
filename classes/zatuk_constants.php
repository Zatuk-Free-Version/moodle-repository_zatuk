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
 * repository_zatuk zatuk constants class
 *
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace repository_zatuk;

/**
 * class zatuk constants
 */
class zatuk_constants {
    /**
     * @var int indicates the width of the collection thumbnail.
     */
    public const COLLECTION_THUMBNAIL_WIDTH = 150;
    /**
     * @var int indicates the height of the collection thumbnail.
     */
    public const COLLECTION_THUMBNAIL_HEIGHT = 150;

    /**
     * @var string indicates the size of the collection.
     */
    public const COLLECTION_SIZE =  '1 * 1024 * 1024';
    /**
     * @var int indicates the width of the listing thumbnail.
     */
    public const LISTING_THUMBNAIL_WIDTH =  150;
    /**
     * @var int indicates the height of the listing thumbnail.
     */
    public const LISTING_THUMBNAIL_HEIGHT =  150;
    /**
     * @var int indicates number of thumbnails per page.
     */
    public const ZATUK_THUMBS_PER_PAGE =  10;
    /**
     * @var int indicates the default status as zero.
     */
    public const DEFAULTSTATUS = 0;
    /**
     * @var int indicates the status-a value as 1.
     */
    public const STATUSA = 1;

    /**
     * @var string indicates foler path value.
     */
    public const FOLDERPATH128 = 'f/folder-128';

}
