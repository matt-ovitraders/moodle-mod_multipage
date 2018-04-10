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

    public static function fetch_page_titles() {
        $page_titles = array();
        $page_titles[] = get_string('nolink', 'mod_multipage');
    }

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

        $data = file_postupdate_standard_editor(
                $data,
                'pagecontents',
                $pagecontentsoptions, 
                $context, 
                'mod_multipage',
                'pagecontents', 
                $data->id);

        $DB->update_record('multipage_pages', $data);

        return $data->id;                        
    }

}