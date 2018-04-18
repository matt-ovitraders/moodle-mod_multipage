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
 * Prints a particular instance of multipage
 *
 * @package    mod_multipage
 * @copyright  2018 Richard Jones <richardnz@outlook.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_newmodule
 * @see https://github.com/justinhunt/moodle-mod_pairwork
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // ... multipage instance ID - it should be named as the first character of the module.

if ($id) {
    $cm         = get_coursemodule_from_id('multipage', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $multipage  = $DB->get_record('multipage', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $multipage  = $DB->get_record('multipage', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $multipage->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('multipage', $multipage->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);

// Log the module viewed event
$event = \mod_multipage\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));

$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $multipage);
$event->trigger();

//Set completion
//if we got this far, we can consider the activity "viewed"
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Print the page header.
$PAGE->set_url('/mod/multipage/view.php', array('id' => $cm->id));
$PAGE->requires->yui_module('moodle-block_fruit-fruitbowl',
        'M.block_fruit.fruitbowl.init',
        array(array(
            'key1' => 'value1',
            'key2' => 'value2',
        )));   
$renderer = $PAGE->get_renderer('mod_multipage');
echo $renderer->header($multipage->title, $course->fullname);

// Show reports tab if permission exists and admin has allowed
$config = get_config('mod_multipage');
if ($config->enablereports) {
    if(has_capability('mod/multipage:viewreportstab', $modulecontext)) {
        echo $renderer->show_reports_tab($course->id, $multipage->id);
    }
}

// Output the introduction as the first page
if ($multipage->intro) {
    echo $renderer->fetch_intro($multipage, $cm->id);
}

// Do we have any pages?
$numpages = mod_multipage\local\pages::count_pages($multipage->id);

// Add a link to the first page
if ($numpages > 0) {
    // Get the record # for the first page.
    $pageid = \mod_multipage\local\pages::
            get_page_id_from_sequence($multipage->id, 1);
    echo $renderer->fetch_firstpage_link($course->id, 
            $multipage->id, $pageid);
}

//if we are teacher we see stuff.
if(has_capability('mod/multipage:manage', $modulecontext)) {
    
    // If there are no pages, we add a add_page link
    if($numpages == 0) {
        echo $renderer->add_first_page_link($course->id, $multipage->id, 0);    
    } else {
        echo $renderer->fetch_num_pages($numpages);          
    }
    
    // The teacher sees the edit links
    echo $renderer->fetch_editing_links($course->id, $multipage->id);
}
    

// Finish the page.
echo $OUTPUT->footer();
