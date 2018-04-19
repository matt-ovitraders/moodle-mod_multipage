<?php

namespace mod_multipage\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_multipage page_created event class.
 *
 * @package    mod_multipage
 * @since      Moodle 3.4
 * @copyright  2018 Richard Jones
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class page_created extends \core\event\base {

    /**
     * Set basic properties for the event.
     */
    protected function init() {
        $this->data['objecttable'] = 'multipage_pages';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Returns a localised string
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventpagecreated', 'mod_multipage');
    }
    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' has created a page with the ".
                "id '$this->objectid' in the multipage activity with course module id '$this->contextinstanceid'.";
    }
}