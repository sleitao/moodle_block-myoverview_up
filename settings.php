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
 * Settings for the myoverview_up block
 *
 * @package    block_myoverview_up
 * @copyright  2019 Tom Dickman <tomdickman@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot . '/blocks/myoverview_up/lib.php');

    // Presentation options heading.
    $settings->add(new admin_setting_heading('block_myoverview_up/appearance',
            get_string('appearance', 'admin'),
            ''));

    // Display Course Categories on Dashboard course items (cards, lists, summary items).
    $settings->add(new admin_setting_configcheckbox(
            'block_myoverview_up/displaycategories',
            get_string('displaycategories', 'block_myoverview_up'),
            get_string('displaycategories_help', 'block_myoverview_up'),
            1));

    // Enable / Disable available layouts.
    $choices = array(BLOCK_MYOVERVIEW_UP_VIEW_CARD => get_string('card', 'block_myoverview_up'),
            BLOCK_MYOVERVIEW_UP_VIEW_LIST => get_string('list', 'block_myoverview_up'),
            BLOCK_MYOVERVIEW_UP_VIEW_SUMMARY => get_string('summary', 'block_myoverview_up'));
    $settings->add(new admin_setting_configmulticheckbox(
            'block_myoverview_up/layouts',
            get_string('layouts', 'block_myoverview_up'),
            get_string('layouts_help', 'block_myoverview_up'),
            $choices,
            $choices));
    unset ($choices);

    // Enable / Disable course filter items.
    $settings->add(new admin_setting_heading('block_myoverview_up/availablegroupings',
            get_string('availablegroupings', 'block_myoverview_up'),
            get_string('availablegroupings_desc', 'block_myoverview_up')));

    $settings->add(new admin_setting_configcheckbox(
            'block_myoverview_up/displaygroupingallincludinghidden',
            get_string('allincludinghidden', 'block_myoverview_up'),
            '',
            0));

    $settings->add(new admin_setting_configcheckbox(
            'block_myoverview_up/displaygroupingall',
            get_string('all', 'block_myoverview_up'),
            '',
            1));

    $settings->add(new admin_setting_configcheckbox(
            'block_myoverview_up/displaygroupinginprogress',
            get_string('inprogress', 'block_myoverview_up'),
            '',
            1));

    $settings->add(new admin_setting_configcheckbox(
            'block_myoverview_up/displaygroupingpast',
            get_string('past', 'block_myoverview_up'),
            '',
            1));

    $settings->add(new admin_setting_configcheckbox(
            'block_myoverview_up/displaygroupingfuture',
            get_string('future', 'block_myoverview_up'),
            '',
            1));

    $settings->add(new admin_setting_configcheckbox(
            'block_myoverview_up/displaygroupingcustomfield',
            get_string('customfield', 'block_myoverview_up'),
            '',
            0));

    $choices = \core_customfield\api::get_fields_supporting_course_grouping();
    if ($choices) {
        $choices  = ['' => get_string('choosedots')] + $choices;
        $settings->add(new admin_setting_configselect(
                'block_myoverview_up/customfiltergrouping',
                get_string('customfiltergrouping', 'block_myoverview_up'),
                '',
                '',
                $choices));
    } else {
        $settings->add(new admin_setting_configempty(
                'block_myoverview_up/customfiltergrouping',
                get_string('customfiltergrouping', 'block_myoverview_up'),
                get_string('customfiltergrouping_nofields', 'block_myoverview_up')));
    }
    $settings->hide_if('block_myoverview_up/customfiltergrouping', 'block_myoverview_up/displaygroupingcustomfield');

    $settings->add(new admin_setting_configcheckbox(
            'block_myoverview_up/displaygroupingfavourites',
            get_string('favourites', 'block_myoverview_up'),
            '',
            1));

    $settings->add(new admin_setting_configcheckbox(
            'block_myoverview_up/displaygroupinghidden',
            get_string('hiddencourses', 'block_myoverview_up'),
            '',
            1));
}
