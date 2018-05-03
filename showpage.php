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
 *
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$courseid = required_param('courseid', PARAM_INT); 
$multipageid  = required_param('multipageid', PARAM_INT);
$pageid  = required_param('pageid', PARAM_INT);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('multipage', $multipageid, 
        $courseid, false, MUST_EXIST);

//set up the page
$PAGE->set_url('/mod/multipage/showpage.php', 
        array('courseid' => $courseid, 'multipageid' => $multipageid, 
        'pageid' => $pageid));

require_login($course, true, $cm);
$coursecontext = context_course::instance($courseid);
$modulecontext = context_module::instance($cm->id);

$PAGE->set_context($modulecontext);
$PAGE->set_pagelayout('course');
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();
// Now get this page record
$data = \mod_multipage\local\pages::get_page_record($pageid);

// Prepare page text, re-write urls
$contextid = $modulecontext->id;
$data->pagecontents = file_rewrite_pluginfile_urls($data->pagecontents, 'pluginfile.php',
        $contextid, 'mod_multipage', 'pagecontents', $pageid);

$renderer = $PAGE->get_renderer('mod_multipage');

// Add an index, if permitted
$config = get_config('mod_multipage');
if($config->enableindex) {
    $page_links = \mod_multipage\local\pages::
            fetch_page_links($multipageid, $courseid);
    echo $renderer->fetch_index($page_links);
}

echo $renderer->show_page($data);

// show the panel, if required
if ($data->show_toggle) {
    echo $renderer->show_panel($data);
}
/* Show comments, if required - to implement as an exercise
require_once($CFG->dirroot . '/comment/lib.php');
comment::init();
$options = new stdClass();
$options->area    = 'pagecomments';
$options->context = $modulecontext;
$options->itemid  = $pageid;
$options->component = 'mod_multipage';
$options->showcount = true;
$comment = new comment($options);
$comment->output(false);
*/
echo $renderer->show_page_nav_links($courseid, $data);

if(has_capability('mod/multipage:manage', $modulecontext)) {
    echo $renderer->show_page_edit_links($courseid, $data);
}

echo $OUTPUT->footer();