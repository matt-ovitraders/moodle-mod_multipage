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

}