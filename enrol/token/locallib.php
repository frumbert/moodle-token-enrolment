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
 * token enrol plugin implementation.
 *
 * @package    enrol_token
 * @copyright  2013 CourseSuite
 * @link http://coursesuite.ninja
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once ("$CFG->libdir/formslib.php");

class enrol_token_enrol_form extends moodleform
{
    protected $instance;

    /**
     * Overriding this function to get unique form id for multiple token enrolments.
     *
     * @return string form identifier
     */
    protected function get_form_identifier() {
        $formid = $this->_customdata->id . '_' . get_class($this);
        return $formid;
    }

    public function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;
        $this->instance = $instance;

        $mform->addElement('html', '<div id="tokenenrolarea">');

        $mform->addElement('text', 'enroltoken', get_string('tokeninput', 'enrol_token'), array('id' => 'enroltoken_' . $instance->id));
        $mform->setType('enroltoken', PARAM_ALPHANUMEXT);

        $mform->addElement('submit', 'submitbutton', get_string('enrolme', 'enrol_token'));

        $mform->addElement('html', '</div>');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', $instance->courseid);

        $mform->addElement('hidden', 'instance');
        $mform->setType('instance', PARAM_INT);
        $mform->setDefault('instance', $instance->id);
    }

    public function validation($data, $files) {
        return parent::validation($data, $files);
    }

    public function setElementError($element, $msg) {
        $this->_form->setElementError($element, $msg);
    }
}
