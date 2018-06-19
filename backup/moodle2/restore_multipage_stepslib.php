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
 * Define all the restore steps that will be used by the restore_multipage_activity_task
 *
* @package    mod_multipage
 * @copyright  2018 Richard Jones <richardnz@outlook.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_newmodule
 *
 */
use \mod_simplelesson\local\pages;
defined('MOODLE_INTERNAL') || die();
/**
 * Structure step to restore one multipage activity
 *
 * @package   mod_multipage
 * @category  backup
 * @copyright 2016 Your Name <your@email.address>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_multipage_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines structure of path elements to be processed during the restore
     *
     * @return array of {@link restore_path_element}
     */
    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('multipage',
                '/activity/multipage');
        $paths[] = new restore_path_element('multipage_page',
                '/activity/multipage/pages/page');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process the given restore path element data
     *
     * @param array $data parsed element data
     */
    protected function process_multipage($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        if (empty($data->timecreated)) {
            $data->timecreated = time();
        }

        if (empty($data->timemodified)) {
            $data->timemodified = time();
        }

        // Create the multipage instance.
        $newitemid = $DB->insert_record('multipage', $data);
        $this->apply_activity_instance($newitemid);
    }

    protected function process_multipage_page($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->multipageid = $this->get_new_parentid('multipage');

        // We'll remap all the prevpageid and nextpageid at the end
        // when we know how :)

        $newitemid = $DB->insert_record('multipage_pages', $data);
        $this->set_mapping('multipage_page', $oldid, $newitemid, true);

    }
    /**
     * Post-execution actions
     */
    protected function after_execute() {
        // Add multipage related files to file areas
        $this->add_related_files('mod_multipage', 'intro', null);
        $this->add_related_files('mod_multipage', 'pagecontents', 'multipage_pages');

        // Remap the page links (prev, next).
        // Fix up page id's using the sequence number.

        $multipageid = $this->get_new_parentid('simplelesson');

        // How many pages to fix?
        $pagecount = pages::count_pages($multipageid);

        for ($p = 1; $p <= $pagecount; $p++) {
            $newpageid = pages::get_page_id_from_sequence($multipageid,
            $p);
            $nextpageid = ($p == $pagecount) ? 0 :
                    pages::get_page_id_from_sequence($multipageid,
                            $p + 1);
            $prevpageid = ($p == 1) ? 0 :
                    pages::get_page_id_from_sequence($multipageid,
                            $p - 1);

            $DB->set_field('multipage_pages', 'nextpageid',
                    $nextpageid,
                    array('id' => $newpageid,
                    'multipageid' => $multipageid));
            $DB->set_field('multipage_pages', 'prevpageid',
                    $prevpageid,
                    array('id' => $newpageid,
                    'multipageid' => $multipageid));
        }
    }
}
