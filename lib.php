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
 * Library of interface functions and constants for module courselist
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the courselist specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package   mod_courselist
 * @copyright 2010 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** example constant */
//define('courselist_ULTIMATE_ANSWER', 42);

/**
 * If you for some reason need to use global variables instead of constants, do not forget to make them
 * global as this file can be included inside a function scope. However, using the global variables
 * at the module level is not a recommended.
 */
//global $courselist_GLOBAL_VARIABLE;
//$courselist_QUESTION_OF = array('Life', 'Universe', 'Everything');

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $courselist An object from the form in mod_form.php
 * @return int The id of the newly inserted courselist record
 */
function courselist_add_instance($courselist) {
    global $DB;

    $courselist->timecreated = time();

    # You may have to add extra stuff in here #

    return $DB->insert_record('courselist', $courselist);
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $courselist An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function courselist_update_instance($courselist) {
    global $DB;

    $courselist->timemodified = time();
    $courselist->id = $courselist->instance;

    # You may have to add extra stuff in here #

    return $DB->update_record('courselist', $courselist);
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function courselist_delete_instance($id) {
    global $DB;

    if (! $courselist = $DB->get_record('courselist', array('id' => $id))) {
        return false;
    }

    # Delete any dependent records here #

    $DB->delete_records('courselist', array('id' => $courselist->id));

    return true;
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @todo Finish documenting this function
 */
function courselist_user_outline($course, $user, $mod, $courselist) {
    $return = new stdClass;
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function courselist_user_complete($course, $user, $mod, $courselist) {
    return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in courselist activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function courselist_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function courselist_cron () {
    return true;
}

/**
 * Must return an array of users who are participants for a given instance
 * of courselist. Must include every user involved in the instance,
 * independient of his role (student, teacher, admin...). The returned
 * objects must contain at least id property.
 * See other modules as example.
 *
 * @param int $courselistid ID of an instance of this module
 * @return boolean|array false if no participants, array of objects otherwise
 */
function courselist_get_participants($courselistid) {
    return false;
}

/**
 * This function returns if a scale is being used by one courselist
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $courselistid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 */
function courselist_scale_used($courselistid, $scaleid) {
    global $DB;

    $return = false;

    //$rec = $DB->get_record("courselist", array("id" => "$courselistid", "scale" => "-$scaleid"));
    //
    //if (!empty($rec) && !empty($scaleid)) {
    //    $return = true;
    //}

    return $return;
}

/**
 * Checks if scale is being used by any instance of courselist.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any courselist
 */
function courselist_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('courselist', 'grade', -$scaleid)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Execute post-uninstall custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function courselist_uninstall() {
    return true;
}

function create_courselist($data, $editoroptions = NULL) {
    global $CFG, $DB;

    //check the categoryid - must be given for all new courses
//    $category = $DB->get_record('course_categories', array('id'=>$data->category), '*', MUST_EXIST);

    //check if the shortname already exist
//    if (!empty($data->shortname)) {
//        if ($DB->record_exists('course', array('shortname' => $data->shortname))) {
//            throw new moodle_exception('shortnametaken');
//        }
//    }

    //check if the id number already exist
//    if (!empty($data->idnumber)) {
//        if ($DB->record_exists('course', array('idnumber' => $data->idnumber))) {
//            throw new moodle_exception('idnumbertaken');
//        }
//    }

//    $data->timecreated  = time();
//    $data->timemodified = $data->timecreated;

    // place at beginning of any category
//    $data->sortorder = 0;

//    if ($editoroptions) {
        // summary text is updated later, we need context to store the files first
//        $data->summary = '';
//        $data->summary_format = FORMAT_HTML;
//    }

//    if (!isset($data->visible)) {
        // data not from form, add missing visibility info
//        $data->visible = $category->visible;
//    }
//    $data->visibleold = $data->visible;

    $newcourseid = $DB->insert_record('courselist', $data);
//    $context = get_context_instance(CONTEXT_COURSE, $newcourseid, MUST_EXIST);

//   if ($editoroptions) {
        // Save the files used in the summary editor and store
//        $data = file_postupdate_standard_editor($data, 'summary', $editoroptions, $context, 'course', 'summary', 0);
//        $DB->set_field('course', 'summary', $data->summary, array('id'=>$newcourseid));
//        $DB->set_field('course', 'summaryformat', $data->summary_format, array('id'=>$newcourseid));
//    }

    $courselist = $DB->get_record('courselist', array('id'=>$newcourseid));

    // Setup the blocks
//    blocks_add_default_course_blocks($course);

//    $section = new stdClass();
//    $section->course        = $course->id;   // Create a default section.
//    $section->section       = 0;
//    $section->summaryformat = FORMAT_HTML;
//    $DB->insert_record('course_sections', $section);

//    fix_course_sortorder();

    // update module restrictions
//    if ($course->restrictmodules) {
//        if (isset($data->allowedmods)) {
//            update_restricted_mods($course, $data->allowedmods);
//        } else {
//            if (!empty($CFG->defaultallowedmodules)) {
//                update_restricted_mods($course, explode(',', $CFG->defaultallowedmodules));
//            }
//        }
//    }

    // new context created - better mark it as dirty
//    mark_context_dirty($context->path);

    // Save any custom role names.
//    save_local_role_names($course->id, (array)$data);

    // set up enrolments
//    enrol_course_updated(true, $course, $data);

//    add_to_log(SITEID, 'course', 'new', 'view.php?id='.$course->id, $data->fullname.' (ID '.$course->id.')');

    // Trigger events
//    events_trigger('course_created', $course);

    return $courselist;
}
