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
 * Edit a page
 *
 * @package   mod_multipage
 * @copyright 2018 Richard Jones https://richardnz.net
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('edit_page_form.php');

//fetch URL parameters
$courseid = required_param('courseid', PARAM_INT);
$multipageid = required_param('multipageid', PARAM_INT); 
$sequence = required_param('sequence', PARAM_INT); 

// Set course related variables
$moduleinstance  = $DB->get_record('multipage', array('id' => $multipageid), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('multipage', $multipageid, $courseid, false, MUST_EXIST);

//set up the page
$PAGE->set_url('/mod/multipage/edit_page.php', 
        array('courseid' => $courseid, 
              'multipageid' => $multipageid, 
              'sequence' => $sequence));

require_login($course, true, $cm);
$coursecontext = context_course::instance($courseid);
$modulecontext = context_module::instance($cm->id);

$PAGE->set_context($modulecontext);
$PAGE->set_pagelayout('course');
$pageid = \mod_multipage\local\pages::get_page_id_from_sequence($multipageid, $sequence);
$return_showpage = new moodle_url(
        '/mod/multipage/showpage.php', 
        array('courseid' => $courseid, 
        'multipageid' => $multipageid, 
        'pageid' => $pageid));
// Page link data
$page_titles = \mod_multipage\local\pages::fetch_page_titles(
                $multipageid,
                $pageid, true);

$mform = new multipage_edit_page_form(null, 
        array('courseid' => $courseid, 
              'multipageid' => $multipageid,
              'pageid'  => $pageid,
              'sequence' => $sequence,
              'context' => $modulecontext,
              'page_titles' => $page_titles));

//if the cancel button was pressed
if ($mform->is_cancelled()) {
    redirect($return_showpage, get_string('cancelled'), 2);
}

//if we have data, then our job here is to save it and return
if ($data = $mform->get_data()) {
    $data->sequence = $sequence;
    $data->multipageid = $multipageid;
    $data->nextpageid = (int) $data->nextpageid;
    $data->prevpageid = (int) $data->prevpageid;  
    $data->show_toggle = (int) $data->show_toggle;
    $data->id = $pageid;
    \mod_multipage\local\pages::update_page_record($data, $modulecontext);
    redirect($return_showpage, 
            get_string('page_updated', 'mod_multipage'), 2);
}

$data = new stdClass();
$data = \mod_multipage\local\pages::get_page_record($pageid);
$data->id = $pageid;
$pagecontentsoptions = multipage_get_editor_options($modulecontext);

$data = file_prepare_standard_editor(
    $data, 
    'pagecontents',
    $pagecontentsoptions, 
    $modulecontext, 
    'mod_multipage', 
    'pagecontents',
    $data->id);
    
$mform->set_data($data); 
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('page_editing', 'mod_multipage'), 2);
$mform->display();
echo $OUTPUT->footer();
return;
