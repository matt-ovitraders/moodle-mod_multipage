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
 * Custom renderer for output of pages
 *
 * @package    mod_multipage
 * @copyright  2016 Richard Jones <richardnz@outlook.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_newmodule
 *
 */

class mod_multipage_renderer extends plugin_renderer_base {

    /**
     * Returns the header for the module
     *
     * @param string $lessontitle the module name.
     * @param string $coursename the course name.
     * @return string header output
     */
    public function header($lessontitle, $coursename) {

        // Header setup
        $this->page->set_title($this->page->course->shortname.": ".$coursename);
        $this->page->set_heading($this->page->course->fullname);
        $output = $this->output->header();

        $output .= $this->output->heading($lessontitle);

        return $output;
    }
    /**
     * Returns the header for the module
     *
     * @param string $multipage the module name.
     * @param int $multipageid the module instance id.
     * @return string header output
     */
    public function fetch_intro($multipage, $multipageid) {
    
        $output = $this->output->box(format_module_intro(
                'multipage', $multipage, $multipageid), 
                'generalbox mod_introbox', 'multipageintro');
        
        return $output;
    }
    /**
     * Returns the editing links
     *
     * @return string editing links
     */
    public function fetch_editing_links() {
    
        $html = html_writer::start_div(
                'mod_multipage' . '_page_edit');
        
        $html .= '<p>' . get_string('edit_page', 
                'mod_multipage') . '</p>';      
        
        $html .=  html_writer::end_div();

        return $html;
    }
    /**
     * Returns a link to add a page
     *
     * @param int $courseid 
     * @param int $multipageid
     * @param int $sequence the page sequence number
     * @return string add link html
     */
    public function add_first_page_link($courseid, $multipageid, $sequence) {
    
        $html = '<p>' . get_string('no_pages', 
                'mod_multipage') . '</p>';      

        $url = new moodle_url('/mod/multipage/add_page.php', 
                array('courseid' => $courseid,
                'multipageid' => $multipageid,
                'sequence' => $sequence));
    
        $html .= html_writer::link($url, get_string('add_page', 'mod_multipage'));
        
        return $html;
    }
    /**
     * Returns the html to show the number of pages
     *
     * @param int $numpages the number of pages
     * @return string html
     */
    public function fetch_num_pages($numpages) {
            
        return get_string('numpages', 'mod_multipage', $numpages);
    }

    /**
     * Show the current page
     *
     * @param object $data object instance of current page
     * @return string html representation of page object
     */
    public function show_page($data) {
        
        $html = '';
        // Show page content
        $html .= html_writer::start_div('mod_multipage_content');
        $html .= $this->output->heading($data->pagetitle, 4);
        $html .= $data->pagecontents;
        $html .= html_writer::end_div();     
        return $html;
    }

/**
     * Returns the link to the a content page
     *
     * @param string $courseid
     * @param string $moduleid
     * @param string $pagesequence

     * @return string
     */
    public function fetch_firstpage_link($courseid, 
            $multipageid, $pageid) {

        $html = '';
        $html .= html_writer::start_div('mod_multipage_nav_links');

        $url = new moodle_url('/mod/multipage/showpage.php',
                    array('courseid' => $courseid, 
                    'multipageid' => $multipageid, 
                    'pageid' => $pageid));
        $html .= html_writer::link($url, 
                    get_string('firstpagelink', 'mod_multipage'));

        $html .=  html_writer::end_div();

        return $html;
    }    

}