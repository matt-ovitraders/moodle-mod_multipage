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
 * @copyright  2018 Richard Jones <richardnz@outlook.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_newmodule
 * @see https://github.com/justinhunt/moodle-mod_pairwork
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
    public function fetch_editing_links($courseid, $multipageid) {
    
        $html = html_writer::start_div(
                'mod_multipage' . '_page_edit');

        $url = new moodle_url('/mod/multipage/edit.php', 
                array('courseid' => $courseid, 
                      'multipageid' => $multipageid));
        $html .= html_writer::link($url, 
                get_string('manage_pages', 'mod_multipage'));        
        $html .= html_writer::end_div();

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

    /**
     * Show the home, previous and next links
     *
     * @param int $courseid
     * @param object $data - the current page object
     * @return string html representation of navigation links
     */
    public function show_page_nav_links($courseid, $data) {
        
        $links = array();

        $html = html_writer::start_div('mod_multipage_page_links');      
        // Home link
        $return_view = new moodle_url('/mod/multipage/view.php', 
                array('n' => $data->multipageid));
        $links[] = html_writer::link($return_view, 
                    get_string('homelink',  'mod_multipage'));
        
        // Previous page (if any)
        if ($data->prevpageid != 0) {
            $prev_url = new moodle_url('/mod/multipage/showpage.php',
                    array('courseid' => $courseid, 
                    'multipageid' => $data->multipageid, 
                    'pageid' => $data->prevpageid));
            $links[] = html_writer::link($prev_url, 
                    get_string('gotoprevpage', 'mod_multipage'));
        } else {
          // Just put out the link text
            $links[] = get_string('gotoprevpage', 'mod_multipage');  
        }
    
        // Next page (if any)
        if ($data->nextpageid != 0) {
            $next_url = new moodle_url('/mod/multipage/showpage.php',
                    array('courseid' => $courseid, 
                    'multipageid' => $data->multipageid, 
                    'pageid' => $data->nextpageid));
            $links[] = html_writer::link($next_url, 
                 get_string('gotonextpage', 'mod_multipage'));
        } else {
          // Just put out the link text
            $links[] = get_string('gotonextpage', 'mod_multipage');  
        }

        $html .= html_writer::alist($links, null, 'ul');
        $html .= html_writer::end_div();  // pagelinks 
        
        return $html;    
    }

    /**
     * Show the page editing links
     *
     * @param int $courseid
     * @param int $data object the page data
     * @return string html representation of editing links
     */
    public function show_page_edit_links($courseid, $data) {
        
        $links = array();

        $html = html_writer::start_div('mod_multipage_edit_links');      
        
        $add_url = new moodle_url('/mod/multipage/add_page.php',
                array('courseid' => $courseid, 
                'multipageid' => $data->multipageid, 
                'sequence' => $data->sequence + 1));        
        $links[] = html_writer::link($add_url, 
                get_string('gotoaddpage', 'mod_multipage'));
    
        $edit_url = new moodle_url('/mod/multipage/edit_page.php',
                array('courseid' => $courseid, 
                'multipageid' => $data->multipageid, 
                'sequence' => $data->sequence));
        $links[] = html_writer::link($edit_url, 
                get_string('gotoeditpage', 'mod_multipage'));

        $delete_url = new moodle_url('/mod/multipage/delete_page.php',
                array('courseid' => $courseid, 
                'multipageid' => $data->multipageid,
                'sequence' => $data->sequence, 
                'returnto' => 'view'));
        $links[] = html_writer::link($delete_url, 
                get_string('gotodeletepage', 'mod_multipage'));
        
        // Page management
        $edit_url = new moodle_url('/mod/multipage/edit.php', 
                array('courseid' => $courseid, 
                      'multipageid' => $data->multipageid));
        $links[] = html_writer::link($edit_url, 
                get_string('manage_pages', 'mod_multipage'));        

        $html .= html_writer::alist($links, null, 'ul');

        $html .= html_writer::end_div();  // pagelinks 
        
        return $html;    
    }
    
    /**
     * Returns a list of pages and editing actions
     *
     * @param string $courseid - current course
     * @param object $multipageid - current instance id
     * @param object $context  - module context
     * @return string html link
     */
    public function page_management($courseid, 
            $multipage, $context) {
    
        $activityname = format_string($multipage->name, true);   
        $this->page->set_title($activityname);

        $table = new html_table();
        $table->head = array(
                get_string('sequence', 'mod_multipage'),
                get_string('pagetitle', 'mod_multipage'),
                get_string('prevpage', 'mod_multipage'),
                get_string('nextpage', 'mod_multipage'),
                get_string('actions', 'mod_multipage'));
        $table->align = 
                array('left', 'left', 'left', 'left', 'left');
        $table->wrap = 
                array('', 'nowrap', '', 'nowrap', '');
        $table->tablealign = 'center';
        $table->cellspacing = 0;
        $table->cellpadding = '2px';
        $table->width = '80%';
        $table->data = array();
        $numpages = 
                \mod_multipage\local\pages::count_pages(
                $multipage->id);
        $sequence = 1;
        
        while ($sequence <= $numpages) {
            $pageid = 
                    \mod_multipage\local\pages::
                    get_page_id_from_sequence($multipage->id, 
                    $sequence);
            $url = new moodle_url('/mod/lesson/edit.php', array(
                'courseid'     => $courseid,
                'multipageid'   => $multipage->id
            ));
            $data = array();
            $all_data = \mod_multipage\local\pages::
                    get_page_record($pageid);
            // Change page id's to sequence numbers for display
            $prevpage = \mod_multipage\local\pages::
                    get_page_sequence_from_id($all_data->prevpageid);
            $nextpage = \mod_multipage\local\pages::
                    get_page_sequence_from_id($all_data->nextpageid);
            $data[] = $all_data->sequence;        
            $data[] = $all_data->pagetitle;
            $data[] = $prevpage;
            $data[] = $nextpage;

            if(has_capability('mod/multipage:manage', 
                    $context)) {
                $data[] = $this->page_action_links(
                        $courseid, $multipage->id, $all_data);
            } else {
                $data[] = '';
            }
            $table->data[] = $data;
            $sequence++;
        }

        return html_writer::table($table);
    }
    /**
     * Returns HTML to display action links for a page
     *
     * @param $courseid - current course
     * @param $multipageid - current module instance id
     * @param $data - a multipage page record
     * @return string, a set of page action links
     */
    public function page_action_links(
            $courseid, $multipageid, $data) {
        global $CFG;
        $actions = array();

        $url = new moodle_url('/mod/multipage/edit_page.php', 
                array('courseid' => $courseid,
                'multipageid' => $multipageid, 
                'sequence' => $data->sequence));

        $label = get_string('gotoeditpage', 'mod_multipage');

        // Standard Moodle icons used here
        $img = $this->output->pix_icon('t/edit', $label);
        $actions[] = html_writer::link($url, $img, array('title' => $label));

        // Preview page = show page
        $url = new moodle_url('/mod/multipage/showpage.php', 
                array('courseid' => $courseid,
                'multipageid' => $multipageid, 
                'pageid' => $data->id));
        $label = get_string('showpage', 'mod_multipage');
        $img = $this->output->pix_icon('t/preview', $label);
        $actions[] = html_writer::link($url, $img, array('title' => $label));
        
        // Delete page
        $url = new moodle_url('/mod/multipage/delete_page.php',
                array('courseid' => $courseid,
                'multipageid' => $multipageid, 
                'sequence' => $data->sequence,
                'returnto' => 'edit'));
        $label = get_string('gotodeletepage', 'mod_multipage');
        $img = $this->output->pix_icon('t/delete', $label);
        $actions[] = html_writer::link($url, $img, array('title' => $label));

        // Move page up
        if ($data->sequence != 1) {
         $url = new moodle_url('/mod/multipage/edit.php', 
                array('courseid' => $courseid,
                'multipageid' => $multipageid, 
                'sequence' => $data->sequence,
                'action' => 'move_up'));
        $label = get_string('move_up', 'mod_multipage');
        $img = $this->output->pix_icon('t/up', $label);
        $actions[] = html_writer::link($url, $img, array('title' => $label));   
        }

        // Move down
        if (!\mod_multipage\local\pages::is_last_page($data)) {
         $url = new moodle_url('/mod/multipage/edit.php', 
                array('courseid' => $courseid,
                'multipageid' => $multipageid,
                'sequence' => $data->sequence, 
                'action' => 'move_down'));
        $label = get_string('move_down', 'mod_multipage');
        $img = $this->output->pix_icon('t/down', $label);
        $actions[] = html_writer::link($url, $img, array('title' => $label));   
        }
        return implode(' ', $actions);
    } 
   /**
     * Returns HTML to display a report tab
     *
     * @param $context - module contex
     * @param $cmid - course module id
     * @return string, a set of tabs
     */
    public function show_reports_tab($courseid, $multipageid) {

        $tabs = $row = $inactive = $activated = array();
        $currenttab = '';
        $viewpage = new moodle_url('/mod/multipage/view.php',
        array('n'=> $multipageid));
        $reportspage = new moodle_url('/mod/multipage/reports.php',
        array('courseid' => $courseid, 'multipageid' => $multipageid));
        
        $row[] = new tabobject('view', $viewpage, get_string('viewtab', 'mod_multipage'));
        $row[] = new tabobject('reports', $reportspage, get_string('reportstab', 'mod_multipage'));

        $tabs[] = $row;
        
        print_tabs($tabs, $currenttab, $inactive, $activated);

    }
    /**
     * Returns HTML to a basic report
     *
     * @param $data - a set of module fields
     * @return string, html table
     */
    public function show_basic_report($records) {
 
        $table = new html_table();
        $table->head = array(
                get_string('moduleid', 'mod_multipage'),
                get_string('multipagename', 'mod_multipage'),
                get_string('title', 'mod_multipage'),
                get_string('timecreated', 'mod_multipage'));
        $table->align = 
                array('left', 'left', 'left');
        $table->wrap = 
                array('nowrap', '', 'nowrap');
        $table->tablealign = 'left';
        $table->cellspacing = 0;
        $table->cellpadding = '2px';
        $table->width = '80%';  
        foreach ($records as $record) {
            $data = array();
            $data[] = $record->id;
            $data[] = $record->name;
            $data[] = $record->title;
            $data[] = $record->timecreated;
            $table->data[] = $data;
        }
       
        return html_writer::table($table);
    }
}