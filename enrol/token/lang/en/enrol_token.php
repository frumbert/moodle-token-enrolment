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
 * Strings for component 'enrol_token', language 'en'.
 *
 * @package    enrol_token
 * @copyright  2013 CourseSuite
 * @link http://coursesuite.ninja
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['customwelcomemessage'] = 'Custom welcome message';
$string['customwelcomemessage_help'] = 'A custom welcome message may be added as plain text or Moodle-auto format, including HTML tags and multi-lang tags.

The following placeholders may be included in the message:

* Course name {$a->coursename}
* Link to user\'s profile page {$a->profileurl}';
$string['databaseerror'] = 'Sorry, a system error occurred whilst enroling with your token.';
$string['defaultrole'] = 'Default role assignment';
$string['defaultrole_desc'] = 'Select role which should be assigned to users during token enrolment';
$string['enrolenddate'] = 'End date';
$string['enrolenddate_help'] = 'If enabled, users can enrol themselves until this date only.';
$string['enrolenddaterror'] = 'Enrolment end date cannot be earlier than start date';
$string['enrolme'] = 'Enrol';
$string['enrolperiod'] = 'Enrolment duration';
$string['enrolperiod_desc'] = 'Default length of time that the enrolment is valid. If set to zero, the enrolment duration will be unlimited by default.';
$string['enrolperiod_help'] = 'Length of time that the enrolment is valid, starting with the moment the user enrols themselves. If disabled, the enrolment duration will be unlimited.';
$string['enrolstartdate'] = 'Start date';
$string['enrolstartdate_help'] = 'If enabled, users can enrol themselves from this date onward only.';
$string['expiredaction'] = 'Enrolment expiration action';
$string['expiredaction_help'] = 'Select action to carry out when user enrolment expires. Please note that some user data and settings are purged from course during course unenrolment.';
$string['expirymessageenrollersubject'] = 'Token enrolment expiry notification';
$string['expirymessageenrollerbody'] = 'Token enrolment in the course \'{$a->course}\' will expire within the next {$a->threshold} for the following users:

{$a->users}

To extend their enrolment, go to {$a->extendurl}';
$string['expirymessageenrolledsubject'] = 'Token enrolment expiry notification';
$string['expirymessageenrolledbody'] = 'Dear {$a->user},

This is a notification that your enrolment in the course \'{$a->course}\' is due to expire on {$a->timeend}.

If you need help, please contact {$a->enroller}.';
$string['ipthrottlingperiod'] = 'IP throttling period (mins)';
$string['ipthrottlingperiod_desc'] = 'The period, in minutes, that a client IP address can be used to enter 10 tokens before they are disallowed from entering more';
$string['ipthrottlingperiod_help'] = 'Use this value to stop possible token-guessing attempts. ' .
                                     'For instance, a value of 20 means that an IP address can only be used to try a maximum of 10 tokens in a 20 minute period. ' .
                                     'Lower values are more restrictive ' .
                                     'A value of 0 turns IP throttling off.';
$string['longtimenosee'] = 'Unenrol inactive after';
$string['longtimenosee_help'] = 'If users haven\'t accessed a course for a long time, then they are automatically unenrolled. This parameter specifies that time limit.';
$string['messageprovider:expiry_notification'] = 'Token enrolment expiry notifications';
$string['newenrols'] = 'Allow new enrolments';
$string['newenrols_desc'] = 'Allow users to token enrol into new courses by default.';
$string['newenrols_help'] = 'This setting determines whether a user can enrol into this course.';
$string['noseatsavailable'] = 'Sorry, that token can no longer be used for enrolments.';
$string['notenrolable'] = 'Sorry, you can\'t enrol enrol in that course using a token.';
$string['pluginname'] = 'Token enrolment';
$string['pluginname_desc'] = 'The token enrolment plugin allows users to use a generated token to enrol in a course. Internally the enrolment is done via the manual enrolment plugin which has to be enabled in the same course.';
$string['role'] = 'Default assigned role';
$string['sendcoursewelcomemessage'] = 'Send course welcome message';
$string['sendcoursewelcomemessage_help'] = 'If enabled, users receive a welcome message via email when they token-enrol in a course.';
$string['status'] = 'Enable existing enrolments';
$string['status_desc'] = 'Enable token enrolment method in new courses.';
$string['status_help'] = 'If disabled all existing token enrolments are suspended and new users can not enrol.';
$string['tokeninput'] = 'Has your employer or job network agency issued you with an access token? Enter it below.';
$string['tokenexpired'] = 'Sorry, that token has expired and can no longer be used for enrolments.';
$string['toomanyattempts'] = 'Too many tokens have been entered in a short time. You must now wait some time before entering any other tokens.';
$string['token:config'] = 'Configure token enrol instances';
$string['token:manage'] = 'Manage enrolled users';
$string['token:unenrol'] = 'Unenrol users from course';
$string['token:unenrolself'] = 'Unenrol self from the course';
$string['tokendoesntexist'] = 'Sorry, that token is not valid for enrolment into this course.';
$string['unenrol'] = 'Unenrol user';
$string['unenrolselfconfirm'] = 'Do you really want to unenrol yourself from course "{$a}"?';
$string['unenroluser'] = 'Do you really want to unenrol "{$a->user}" from course "{$a->course}"?';
$string['userthrottlingperiod'] = 'User throttling period (mins)';
$string['userthrottlingperiod_desc'] = 'The period, in minutes, that a user account can be used to enter 10 tokens before they are disallowed from entering more';
$string['userthrottlingperiod_help'] = 'Use this value to stop possible token-guessing attempts. ' .
                                       'For instance, a value of 10 means that a user account can only be used to try a maximum of 10 tokens in a 10 minute period. ' .
                                       'Lower values are more restrictive ' .
                                       'A value of 0 turns user throttling off.';
$string['welcometocourse'] = 'Welcome to {$a}';
$string['welcometocoursetext'] = 'Welcome to {$a->coursename}!

If you have not done so already, you should edit your profile page:

  {$a->profileurl}';
$string['enrol_header'] = 'Enrol using a token';
$string['enrol_label'] = 'Token:';