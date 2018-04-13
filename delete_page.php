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
 * delete current page, 
 * adjusting sequence numbers as necessary
 *
 * @package   mod_multipage
 * @copyright 2018 Richard Jones https://richardnz.net
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('edit_page_form.php');

global $DB;

//fetch URL parameters
$courseid = required_param('courseid', PARAM_INT);
$multipageid = required_param('multipageid', PARAM_INT); 
// sequence in which pages are added to this lesson
$sequence = required_param('sequence', PARAM_INT);
$returnto = optional_param('returnto', 'view', PARAM_TEXT);

// Set course related variables
$moduleinstance  = $DB->get_record('multipage', array('id' => $multipageid), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('multipage', $multipageid, $courseid, false, MUST_EXIST);

//set up the page
$PAGE->set_url('/mod/multipage/delete_page.php', 
        array('courseid' => $courseid, 
              'multipageid' => $multipageid, 
              'sequence' => $sequence));
require_login($course, true, $cm);
$coursecontext = context_course::instance($courseid);
$modulecontext = context_module::instance($cm->id);

$PAGE->set_context($modulecontext);
$PAGE->set_pagelayout('course');

$return_view = new moodle_url('/mod/multipage/view.php', 
        array('n' => $multipageid));

$return_edit = new moodle_url('/mod/multipage/edit.php', 
        array('courseid' => $courseid, 
        'multipageid' => $multipageid));

// Check if any other pages point to this page and fix their links
$pageid = \mod_multipage\local\pages::get_page_id_from_sequence($multipageid, $sequence);
\mod_multipage\local\pages::fix_page_links($multipageid, $pageid);

// Delete the page
$DB->delete_records('multipage_pages',  
        array('multipageid'=>$multipageid,
        'id' => $pageid));

// Find the sequence number of the current last
$lastpage = 
        \mod_multipage\local\pages::count_pages($multipageid);
$lastpage++; // last page sequence number
// Note the id's of pages to change their sequence numbers
// get_page_id_from sequence only works if sequence is unique.
$pagestochange = array();
// We've deleted a page so lastpage is one short in terms
// of it's sequence number.
for ($p = $sequence + 1; $p <= $lastpage ; $p++) {
    $thispage = \mod_multipage\local\pages::
            get_page_id_from_sequence($multipageid, $p);
    $pagestochange[] = $thispage;
}

// Change sequence numbers (decrement from deleted + 1 to end).
for ($p = 0; $p < sizeof($pagestochange); $p++) {

   \mod_multipage\local\pages::
           decrement_page_sequence($pagestochange[$p]); 
}

// Go back to page where request came from
if ($returnto == 'edit') {
    redirect($return_edit, 
            get_string('page_deleted', 'mod_multipage'), 2);    
}
// default
redirect($return_view, get_string('page_deleted', 'mod_multipage'), 2);
