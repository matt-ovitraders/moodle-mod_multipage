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
 * Form for importing multipage pages
 *
 * @package   mod_multipage
 * @copyright 2018 Richard Jones https://richardnz.net
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../lib/formslib.php');
require_once('lib.php');
/**
 * Define the import page file elements
 */
class import_page_form extends moodleform {
    
    function definition() {

        $mform =& $this->_form;
        
        $mform->addElement('filepicker', 'userfile', 
                get_string('filetoimport', 'mod_multipage'));
        $mform->addHelpButton('userfile', 'filetoimport', 'mod_multipage');
        $options = array();
        $submit_string = get_string('submit');
        // Hidden fields
        $mform->addElement('hidden', 'courseid', $this->_customdata['courseid']);
        $mform->addElement('hidden', 'multipageid', $this->_customdata['multipageid']);
        $mform->setType('courseid', PARAM_INT);
        $mform->setType('multipageid', PARAM_INT); 
        
        $this->add_action_buttons(false, $submit_string);
    }
}