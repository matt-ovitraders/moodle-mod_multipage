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
 * Exports multipage pages to csv format
 *
 * @package    mod_multipage
 * @copyright  2018 Richard Jones <richardnz@outlook.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_newmodule
 *
 */

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/dataformatlib.php');

//$courseid = required_param('courseid', PARAM_INT);
$multipageid = required_param('multipageid', PARAM_INT); 

// Need instance and context
$moduleinstance  = $DB->get_record('multipage', 
       array('id' => $multipageid), '*', MUST_EXIST);
$modulecontext = context_module::instance($moduleinstance->id);
require_login();
require_capability('mod/multipage:exportpages', $modulecontext);
$fields = \mod_multipage\local\reporting::fetch_headers();
$records = \mod_multipage\local\reporting::fetch_page_data($multipageid);
$filename = clean_filename($moduleinstance->name);
$dataformat = 'json';

download_as_dataformat($filename, $dataformat, $fields, $records);
exit;