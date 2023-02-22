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
 * Contains the class for the My overview block.
 *
 * @package    block_myoverview_up
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * My overview block class.
 *
 * @package    block_myoverview_up
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_myoverview_up extends block_base {

    /**
     * Init.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_myoverview_up');
    }

    /**
     * Returns the contents.
     *
     * @return stdClass contents of block
     */
    public function get_content() {
        global $CFG, $USER;
        if (isset($this->content)) {
            return $this->content;
        }
        $group = get_user_preferences('block_myoverview_up_user_grouping_preference');
        $sort = get_user_preferences('block_myoverview_up_user_sort_preference');
        $view = get_user_preferences('block_myoverview_up_user_view_preference');
        $paging = get_user_preferences('block_myoverview_up_user_paging_preference');
        $customfieldvalue = get_user_preferences('block_myoverview_up_user_grouping_customfieldvalue_preference');

        $this->content->footer = '';
        $this->content = new stdClass();

        $courses = enrol_get_all_users_courses($USER->id);  
        $this->content->text = '<div class="courses-container">';

        $renderable = new \block_myoverview_up\output\main($group, $sort, $view, $paging, $customfieldvalue);
        $renderer = $this->page->get_renderer('block_myoverview_up');

        
        $this->content->text = $renderer->render($renderable);
        
        $hidden_courses= array();
        
    foreach ($courses as $course) {
        $context = context_course::instance($course->id);
        
        // Check if the user does not have update capability for the course
        if (!has_capability('moodle/course:update', $context)) {
            
            // Check if the course is hidden but the user (student) is enrolled
            if ($course->visible == 0 && is_enrolled($context, $USER, '', true)) {
                // Add the course to the hidden courses array
                $hidden_courses[] = $course;
            }
        }  
    }
    
    // Display the hiddden courses if any were found
    if (!empty($hidden_courses)) {
        $subtitle = get_string('hidden_courses' , 'block_myoverview_up');
        $this->content->text .= "<strong>$subtitle:</strong><br>";
        
        // Loop through the hidden courses and display them
        foreach ($hidden_courses as $course) {
            $context = context_course::instance($course->id);
            $this->content->text .= html_writer::tag('a', $course->fullname, array('href' => $CFG->wwwroot . '/course/view.php?id=' . $course->id, 'class' => 'grayout')) . '<br>';
        }
    }


    }

    /**
     * Locations where block can be displayed.
     *
     * @return array
     */
    public function applicable_formats() {
        return array('my' => true);
    }

    /**
     * Allow the block to have a configuration page.
     *
     * @return boolean
     */
    public function has_config() {
        return true;
    }

    /**
     * Return the plugin config settings for external functions.
     *
     * @return stdClass the configs for both the block instance and plugin
     * @since Moodle 3.8
     */
    public function get_config_for_external() {
        // Return all settings for all users since it is safe (no private keys, etc..).
        $configs = get_config('block_myoverview_up');

        // Get the customfield values (if any).
        if ($configs->displaygroupingcustomfield) {
            $group = get_user_preferences('block_myoverview_up_user_grouping_preference');
            $sort = get_user_preferences('block_myoverview_up_user_sort_preference');
            $view = get_user_preferences('block_myoverview_up_user_view_preference');
            $paging = get_user_preferences('block_myoverview_up_user_paging_preference');
            $customfieldvalue = get_user_preferences('block_myoverview_up_user_grouping_customfieldvalue_preference');

            $renderable = new \block_myoverview_up\output\main($group, $sort, $view, $paging, $customfieldvalue);
            $customfieldsexport = $renderable->get_customfield_values_for_export();
            if (!empty($customfieldsexport)) {
                $configs->customfieldsexport = json_encode($customfieldsexport);
            }
        }

        return (object) [
            'instance' => new stdClass(),
            'plugin' => $configs,
        ];
    }

    /**
     * Disable block editing on the my courses page.
     *
     * @return boolean
     */
    public function instance_can_be_edited() {
        if ($this->page->blocks->is_known_region(BLOCK_POS_LEFT) || $this->page->blocks->is_known_region(BLOCK_POS_RIGHT)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Hide the block header on the my courses page.
     *
     * @return boolean
     */
    public function hide_header() {
        if ($this->page->blocks->is_known_region(BLOCK_POS_LEFT) || $this->page->blocks->is_known_region(BLOCK_POS_RIGHT)) {
            return false;
        } else {
            return true;
        }
    }
}

