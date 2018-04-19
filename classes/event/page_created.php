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
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventpagecreated', 'mod_multipage');
    }
}