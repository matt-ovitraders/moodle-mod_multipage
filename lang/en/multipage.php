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
 * English strings for multipage
 *
 * @package    mod_multipage
 * @copyright  2018 Richard Jones <richardnz@outlook.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see https://github.com/moodlehq/moodle-mod_newmodule
 *
 */

defined('MOODLE_INTERNAL') || die();

// General module strings
$string['modulename'] = 'Multipage';
$string['modulenameplural'] = 'Multipages';
$string['modulename_help'] = 'Use the multipage module for a simple sequential display of pages with an optional index | The multipage module allows the creation and addition of multiple pages of content.';
// Capabilities
$string['multipage:manage'] = 'Manage multipage';
$string['multipage:addinstance'] = 'Add a new multipage';
$string['multipage:submit'] = 'Submit multipage';
$string['multipage:view'] = 'View multipage';
$string['multipage:viewreportstab'] = 'View reports';

// Instance settings
$string['multipagefieldset'] = 'Custom example fieldset';
$string['multipagename'] = 'multipage name';
$string['multipagename_help'] = 'This is the content of the help tooltip associated with the multipagename field. Markdown syntax is supported.';
$string['pluginadministration'] = 'multipage administration';
$string['pluginname'] = 'multipage';

// mod_form settings
$string['multipage_settings'] = 'Multi-page settings';
$string['multipage_title'] = 'Title of this resource';

// Capabilities
$string['multipage:manage'] = 'Manage multipage';

// Page management
$string['multipage_editing'] = 'Editing multipage';
$string['manage_pages'] = 'Page management';
$string['sequence'] = 'sequence';
$string['title'] = 'Page title';
$string['nextpage'] = 'Next page';
$string['prevpage'] = 'Previous page';
$string['actions'] = 'Actions';
$string['move_up'] = 'Move up';
$string['move_down'] = 'Move down';
$string['showpage'] = 'Preview page';

// Page editing
$string['edit_page'] = 'Edit page';
$string['add_page'] = 'Add page';
$string['delete_page'] = 'Delete page';
$string['page_adding'] = 'Add a new page';
$string['page_saved'] = 'Page saved';
$string['pagetitle'] = 'Title of the page';
$string['pagecontents'] = 'page content';
$string['getprevpage'] = 'Previous page';
$string['getnextpage'] = 'Next page';
$string['nolink'] = 'none';
$string['no_pages'] = 'There are no pages yet, add a page';
$string['numpages'] = 'Number of pages: {$a}';

// Page navigation
$string['firstpagelink'] = 'First page';
$string['homelink'] = 'Home';
$string['gotonextpage'] = 'Next';
$string['gotoprevpage'] = 'Previous';

// Page editing
$string['gotoaddpage'] = 'Add page';
$string['gotoeditpage'] = 'Edit page';
$string['gotodeletepage'] = 'Delete page';
$string['page_editing'] = 'Editing page';
$string['page_updated'] = 'Page updated';
$string['page_deleted'] = 'Page deleted';
$string['show_toggle'] ='Display sliding panel';
$string['show_toggle_text'] ='Will display if checked';
$string['togglename'] ='Text for sliding panel button';
$string['toggletext'] ='Text inside sliding panel';

// reporting
$string['moduleid'] = 'id';
$string['viewtab'] = 'view';
$string['reportstab'] = 'reports';
$string['timecreated'] = 'Time created';

// Admin settings
$string['enablereports'] = 'Show reports tab';
$string['enablereports_desc'] = 'Check to allow teachers to see reports';
$string['enableindex'] = 'Show page index';
$string['enableindex_desc'] = 'Check to show page index';

// Page index
$string['page_index_header'] = 'Index';

// Settings navigation
$string['namechange'] = 'Name changer';

// Scheduled task
$string['multipagetask'] = 'Multipage task';

// Event
$string['multipageviewed'] = 'Multipage viewed';
$string['eventpagecreated'] = 'Page created';