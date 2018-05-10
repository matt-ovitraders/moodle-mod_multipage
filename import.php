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
 * Imports multipage pages from csv format
 *
 * @package    mod_multipage
 * @copyright  2018 Richard Jones <richardnz@outlook.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_newmodule
 *
 */

require_once('../../config.php');
require_once($CFG->libdir.'/csvlib.class.php');
require_once('import_page_form.php');

$courseid = required_param('courseid', PARAM_INT); 
$multipageid = required_param('multipageid', PARAM_INT); 

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('multipage', $multipageid, 
        $courseid, false, MUST_EXIST);

//set up the page
$PAGE->set_url('/mod/multipage/import.php', 
        array('courseid' => $courseid, 
        'multipageid' => $multipageid));

$coursecontext = context_course::instance($courseid);
$modulecontext = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/multipage:importpages', $modulecontext);

$PAGE->set_context($modulecontext);
$PAGE->set_pagelayout('course');
$PAGE->set_heading(format_string($course->fullname));

$return_view = new moodle_url('/mod/multipage/view.php', 
        array('n' => $multipageid));
$today = time();
$today = make_timestamp(date('Y', $today), 
        date('m', $today), date('d', $today), 0, 0, 0);

$mform = new import_page_form(null, 
        array('courseid' => $courseid, 
        'multipageid' => $multipageid));

if ($formdata = $mform->get_data()) {
    $content = $mform->get_file_content('userfile');
    $all_data = json_decode($content, true);
    $json_error = (json_last_error() === JSON_ERROR_NONE);

    if ( ($all_data) && ($json_error) ) {
        $result = \mod_multipage\local\reporting::
                import_pages($multipageid, $all_data);
        if ($result) {
            redirect($return_view, 
                    get_string('pages_imported', 'mod_multipage'), 2);
        }
    }
    redirect($return_view, 
            get_string('pages_not_imported', 'mod_multipage'), 2);
}
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('importlink', 'mod_multipage'), 2);
$mform->display();
echo $OUTPUT->footer();
