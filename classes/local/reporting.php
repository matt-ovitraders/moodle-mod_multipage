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
 * Defines report classes
 *
 * @package    mod_multipage
 * @copyright  2018 Richard Jones <richardnz@outlook.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_newmodule
 * @see https://github.com/justinhunt/moodle-mod_pairwork
 */

namespace mod_multipage\local;

defined('MOODLE_INTERNAL') || die;

class reporting {

    /*
     * Basic Report - get the module records for this course
     *
     * @param $courseid - course to get records for
     * @return array of objects
     */
    public static function fetch_module_data($courseid){
        global $DB;
        $records = $DB->get_records('multipage', 
                array('course'=>$courseid), null, 'id, name, title, timecreated');
        
        foreach ($records as $record) {
            $record->timecreated = date("Y-m-d H:i:s",$record->timecreated);
        } 
        return $records;
    }
    /*
     * Page report - get the page records for this course
     *
     * @param $multipageid - instance to get records for
     * @return array of objects sorted by sequence number
     */
    public static function fetch_page_data($multipageid){
        global $DB;
        $records = $DB->get_records('multipage_pages', 
                array('multipageid'=>$multipageid), 'sequence', '*');
        
        foreach ($records as $record) {
            $record->timecreated = date("Y-m-d H:i:s",$record->timecreated);
            $record->timemodified = date("Y-m-d H:i:s",$record->timemodified);
        } 
        return $records;
    }

    public static function fetch_headers() {
        $fields = array('id' => 'id',
        'multipageid' => 'multipageid',
        'sequence' => 'sequence',
        'prevpageid' => 'prevpageid',
        'nextpageid' => 'nextpageid',
        'pagetitle' => 'pagetitle',
        'pagecontents' => 'pagecontents',
        'pagecontentsformat' => 'pagecontentsformat',
        'show_toggle' => 'show_toggle',
        'togglename' => 'togglename',
        'toggletext' => 'toggletext',
        'timecreated' => 'timecreated',
        'timemodified' => 'timemodified');

        return $fields;
    }
}