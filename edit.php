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
 * Edit a lesson and its pages
 *
 * @package   mod_multipage
 * @copyright 2018 Richard Jones https://richardnz.net
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
use \mod_multipage\local;

global $DB;
//fetch URL parameters.
$courseid = required_param('courseid', PARAM_INT);
$multipageid = required_param('multipageid', PARAM_INT); 
$sequence = optional_param('sequence', 0, PARAM_INT); 
$action = optional_param('action', 'none', PARAM_TEXT);

// Set course related variables.
$moduleinstance  = $DB->get_record('multipage', array('id' => $multipageid), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('multipage', $multipageid, $courseid, false, MUST_EXIST);

//set up the page
$PAGE->set_url('/mod/multipage/edit.php', 
        array('courseid' => $courseid, 
              'multipageid' => $multipageid,));

require_login($course, true, $cm);
$coursecontext = context_course::instance($courseid);
$modulecontext = context_module::instance($cm->id);

$PAGE->set_context($modulecontext);
$PAGE->set_pagelayout('course');

$renderer = $PAGE->get_renderer('mod_multipage');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('multipage_editing', 'mod_multipage'), 2);

// Check the action:
// The up and down arrows are only shown for the relevant
// sequence positions so we don't have to check that
if ( ($sequence != 0) && ($action != 'none') ) {
    if($action == 'move_up') {
    \mod_multipage\local\pages::move_page_up(
            $multipageid, $sequence);                            
    } else if ($action == 'move_down') { 
    \mod_multipage\local\pages::move_page_down(
            $multipageid, $sequence);                        
    }
}
echo $renderer->page_management($course->id, 
        $moduleinstance, $modulecontext);

// Home & export links.
$links = array();
$return_view = new moodle_url('/mod/multipage/view.php', 
        array('n' => $multipageid));
$links[] = html_writer::link($return_view, 
        get_string('homelink',  'mod_multipage'));

if(has_capability('mod/multipage:exportpages', $modulecontext)) {
    $return_export = new moodle_url('/mod/multipage/export.php',
            array('courseid' => $courseid,
            'multipageid' => $multipageid));
    $links[] = html_writer::link($return_export, 
            get_string('exportlink',  'mod_multipage'));
}
if(has_capability('mod/multipage:importpages', $modulecontext)) {
    $return_export = new moodle_url('/mod/multipage/import.php',
            array('courseid' => $courseid,
            'multipageid' => $multipageid));
    $links[] = html_writer::link($return_export, 
            get_string('importlink',  'mod_multipage'));
}

echo $renderer->page_management_links($links);

echo $OUTPUT->footer();
