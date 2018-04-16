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
 * Shows a multipage page
 *
 * @package    mod_multipage
 * @copyright  2018 Richard Jones <richardnz@outlook.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_newmodule
 * @see https://github.com/justinhunt/moodle-mod_pairwork
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$courseid = required_param('courseid', PARAM_INT); 
$multipageid  = required_param('multipageid', PARAM_INT);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('multipage', $multipageid, 
        $courseid, false, MUST_EXIST);

//set up the page
$PAGE->set_url('/mod/multipage/reports.php', 
        array('courseid' => $courseid, 'multipageid' => $multipageid));

require_login($course, true, $cm);
$coursecontext = context_course::instance($courseid);
$modulecontext = context_module::instance($cm->id);

require_capability('mod/multipage:viewreportstab',$modulecontext);

$PAGE->set_context($modulecontext);
$PAGE->set_pagelayout('course');
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('mod_multipage');
echo $renderer->show_reports_tab($courseid, $multipageid);
$data = mod_multipage\local\reporting::fetch_module_data($courseid);
echo $renderer->show_basic_report($data);

// Home link
$return_view = new moodle_url('/mod/multipage/view.php', 
        array('n' => $multipageid));
echo html_writer::link($return_view, 
        get_string('homelink',  'mod_multipage'));

echo $OUTPUT->footer();