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
 * Defines report classes
 *
 * @package    mod_multipage
 * @copyright  2016 Richard Jones <richardnz@outlook.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_newmodule
 * @see https://github.com/justinhunt/moodle-mod_pairwork
 */

namespace mod_multipage\local;

defined('MOODLE_INTERNAL') || die;

class reporting  {

    /*
     * Basic Report
     *
     */
    public static function fetch_formatted_field($field, $record) {
    global $DB;
        switch($field){
            case 'id':
                $ret = $record->id;
                break;

            case 'name':
                $ret = $record->name;
                break;
            
            case 'timecreated':
                $ret = date("Y-m-d H:i:s",$record->timecreated);
                break;
           default:
               if(property_exists($record, $field)){
                   $ret=$record->{$field};
               }else{
                   $ret = '';
               }
        }
        return $ret;
    }
    
    public static function fetch_module_data(){
        global $DB;
        $records = $DB->get_records(MOD_PAIRWORK_TABLE,array());
        $data = array();
        foreach ($records as $record) {
            $data['id'] = self::fetch_formatted_field('id', $record);
            $data['name'] = self::fetch_formatted_field('name', $record);
            $data['time_created'] = self::
                    fetch_formatted_field('time_created', $record);
        }
        return $data;
    }
}