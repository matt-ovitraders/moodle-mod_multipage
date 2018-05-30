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
 * Page utilities for multipage
 *
 * @package    mod_multipage
 * @copyright  Richard Jones https://richardnz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_multipage\local;

defined('MOODLE_INTERNAL') || die();
/**
 * Utility class for counting pages and so on
 *
 * @package    mod_multipage
 * @copyright  2018 Richard Jones https://richardnz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pages  {

    /**
     * Count the number of pages in a multipage mod
     *
     * @param int $multipageid the id of a multipage
     * @return int the number of pages in the database that lesson has
     */
    public static function count_pages($multipageid) {
       global $DB;

       return $DB->count_records('multipage_pages',
                array('multipageid'=>$multipageid));
    }
    /**
     * Get the page titles for the prev/next drop downs
     * keys are the page values, text is the page title
     *
     * @param int $multipageid the id of a multipage
     * @return array of pageid=>titles of pages in the multipage
     */
    public static function fetch_page_titles($multipageid) {
        $page_titles = array();
        $pagecount = self::count_pages($multipageid);
        if ($pagecount != 0) {
            for ($p = 1; $p <= $pagecount; $p++ ) {
                $pid = self::get_page_id_from_sequence($multipageid, $p);
                $data = self::get_page_record($pid);
                $page_titles[$pid] = $data->pagetitle;
           }
        }
        // Add a "none" link
        $page_titles[0] =
                get_string('nolink', 'mod_multipage');

        return $page_titles;
    }
    /**
     * Get the page links for the multipage index
     *
     * @param int $multipageid the id of a multipage
     * @param int $course id
     * @param boolean $homepage true if this is the home page
     * @return array of links to pages in the multipage
     */
    public static function fetch_page_links($multipageid, $courseid) {
        global $CFG;
        //require_once($CFG->libdir . '/weblib.php');
        //require_once($CFG->libdir . '/outputcomponents.php');
        $page_links = array();

        // Count the content pages and make the links
        $pagecount = self::count_pages($multipageid);
        if ($pagecount != 0) {
            for ($p = 1; $p <= $pagecount; $p++ ) {
                $pageid = self::get_page_id_from_sequence(
                        $multipageid, $p);
                $data = self::get_page_record($pageid);
                $page_url = new
                        \moodle_url('/mod/multipage/showpage.php',
                        array('courseid' => $courseid,
                        'multipageid' => $data->multipageid,
                        'pageid' => $pageid));
                $link = \html_writer::link($page_url,
                        $data->pagetitle);
                $page_links[] = $link;
           }
        }
       return $page_links;
    }
    /**
     * Add a page record to the pages table
     *
     * @param $data object - the data to add
     * @param $context object - our module context
     * @return $id - the id of the inserted record
     */
    public static function add_page_record($data, $context){
        global $DB;

        $pagecontentsoptions = multipage_get_editor_options($context);

        // insert a dummy record and get the id
        $data->timecreated = time();
        $data->timemodified = time();
        $data->pagecontents = ' ';
        $data->pagecontentsformat = FORMAT_HTML;
        $dataid = $DB->insert_record('multipage_pages', $data);

        $data->id = $dataid;

        // Massage the data into a form for saving
        $data = file_postupdate_standard_editor(
                $data,
                'pagecontents',
                $pagecontentsoptions,
                $context,
                'mod_multipage',
                'pagecontents',
                $data->id);
        // Update the record with full editor data
        $DB->update_record('multipage_pages', $data);

        return $data->id;
    }

    /**
     * Given a page id return the data for that page record
     *
     * @param int $pageid the page id
     * @return object representing the record
     */
    public static function get_page_record($pageid) {
        global $DB;
        return $DB->get_record('multipage_pages',
                array('id' => $pageid), '*', MUST_EXIST);
    }

   /**
     * Given a multipage id and sequence number, find that page record
     *
     * @param int $multipageid the instance id
     * @param int $sequence, where the page is in the lesson sequence
     * @return int pageid, the id of the page in the pages table
     */

    public static function get_page_id_from_sequence($multipageid,
            $sequence) {
        global $DB;
        $data = $DB->get_record('multipage_pages',
                array('multipageid' => $multipageid,
                'sequence' => $sequence));
        return $data->id;
    }
    /**
     * Given a multipage id return its sequence number
     *
     * @param int $multipageid the instance id
     * @param int $sequence, where the page is in the lesson sequence
     * @return int pageid, the id of the page in the pages table
     */

    public static function get_page_sequence_from_id($pageid) {
        global $DB;

       return $DB->get_field('multipage_pages',
            'sequence',  array('id' => $pageid));
    }

    /**
     * Check if this is the last page of the instance
     *
     * @param object $data the multipage object
     * @return boolean true if this is the last page
     */
    public static function is_last_page($data) {
        return ($data->sequence == self::count_pages($data->multipageid));
    }

/**
     * Given a multipage and sequence number
     * Move the page by exchanging sequence numbers
     *
     * @param int $multipageid the multipage instance
     * @param int $sequence the page sequence number
     * @return none
     */
    public static function move_page_up($multipageid, $sequence) {
        global $DB;

        $pageid_up = self::get_page_id_from_sequence(
                $multipageid, $sequence);
        $pageid_down = self::get_page_id_from_sequence(
                $multipageid, ($sequence - 1));

        self::decrement_page_sequence($pageid_up);
        self::increment_page_sequence($pageid_down);
    }

    /**
     * Given a multipage and sequence number
     * Move the page by exchanging sequence numbers
     *
     * @param int $multipageid the multipage instance
     * @param int $sequence the page sequence number
     * @return none
     */
    public static function move_page_down($multipageid, $sequence) {
        global $DB;

        $pageid_down = self::get_page_id_from_sequence(
                $multipageid, $sequence);
        $pageid_up = self::get_page_id_from_sequence(
                $multipageid, ($sequence + 1));

        self::increment_page_sequence($pageid_down);
        self::decrement_page_sequence($pageid_up);
    }

   /**
     * Given a page record id
     * decrease the sequence number by 1
     *
     * @param int $pageid
     * @return none
     */
    public static function decrement_page_sequence($pageid) {
        global $DB;
        $sequence = $DB->get_field('multipage_pages',
            'sequence',
            array('id' => $pageid));
        $DB->set_field('multipage_pages',
            'sequence', ($sequence - 1),
            array('id' => $pageid));
    }

   /**
     * Given a page record id
     * increase the sequence number by 1
     *
     * @param int $pageid
     * @return none
     */
    public static function increment_page_sequence($pageid) {
        global $DB;
        $sequence = $DB->get_field('multipage_pages',
                'sequence',
                array('id' => $pageid));
        $DB->set_field('multipage_pages',
                'sequence', ($sequence + 1),
                array('id' => $pageid));
    }

    /**
     * Update a page record
     *
     * @param int $data from edit_page form
     * @param object $context, the module context
     */
    public static function update_page_record($data, $context) {
        global $DB;

        $pagecontentsoptions = multipage_get_editor_options($context);
        $data->timemodified = time();

        $data = file_postupdate_standard_editor(
                $data,
                'pagecontents',
                $pagecontentsoptions,
                $context,
                'mod_multipage',
                'pagecontents',
                $data->id);

        $DB->update_record('multipage_pages', $data);
    }
    /**
     * Fix the links to a deleted page
     *
     * @param int $multipageid instance the page is in
     * @param int $pageid of deleted page
     * @param object $context, the module context
     */
    public static function fix_page_links($multipageid, $pageid) {
        global $DB;

        $pagedata = self::get_page_record($pageid);

        // Pages to process
        $pagecount = self::count_pages($multipageid);
        if ($pagecount != 0) {
            for ($p = 1; $p <= $pagecount; $p++ ) {
                $pid = self::get_page_id_from_sequence($multipageid, $p);
                // Don't worry about this page
                if ($pid != $pageid) {
                    $data = self::get_page_record($pid);
                    if ($data->nextpageid == $pageid) {
                        // link to the page following the deleted page
                        $DB->set_field('multipage_pages',
                                'nextpageid', $pagedata->nextpageid,
                                 array('id' => $pid));
                    }
                    if ($data->prevpageid == $pageid) {
                        // link to the page preceding the deleted page
                        $DB->set_field('multipage_pages',
                                'prevpageid', $pagedata->prevpageid,
                                 array('id' => $pid));
                    }
               }
            }
        }
    }
    public static function export_pages($multipageid, $filename) {
        require_once($CFG->libdir.'/dataformatlib.php');
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
        $dataformat = 'csv';
        $numpages = self::count_pages($multipageid);
        $sequence = 1;
        $iterator = array();
        while ($sequence <= $numpages) {
            $pageid = self::get_page_id_from_sequence(
                    $multipageid, $sequence);
            $iterator[] = self::get_page_record($pageid);
            $sequence++;
        }
        \download_as_dataformat($filename, $dataformat,
                $fields, $iterator);
    }
/**
     * Given a multipage id
     * Fix the pages so that next and previous match page order
     * as it is on the page management screen
     *
     * @param int $multipageid
     * @return none
     */
    public static function fix_page_sequence($multipageid) {
        global $DB;

        $pagecount = self::count_pages($multipageid);

        if ($pagecount != 0) {
            for ($p = 1; $p <= $pagecount; $p++) {
                $pid = self::get_page_id_from_sequence($multipageid,
                        $p);
                // ID of previous page in sequence (unless first).
                if ($p != 1) {
                    $previd = self::get_page_id_from_sequence(
                            $multipageid, ($p - 1));
                } else {
                    $previd = 0;
                }
                // Next id (unless last).
                if ($p == $pagecount) {
                    $nextid = 0;
                } else {
                    $nextid = self::get_page_id_from_sequence(
                            $multipageid, ($p + 1));
                }

                $DB->set_field('multipage_pages',
                        'prevpageid', ($previd),
                        array('id' => $pid));
                $DB->set_field('multipage_pages',
                        'nextpageid', ($nextid),
                        array('id' => $pid));
           }
       }
    }

}