<?php
defined('MOODLE_INTERNAL') || die();

require_once ($CFG->libdir . '/authlib.php');

class auth_plugin_token extends auth_plugin_base
{
    function auth_plugin_token() {
        $this->authtype = 'token';
        $this->config = get_config('auth/token');
    }


    // user login handler, after any form errors are handled
    function user_login($username, $password) {
        global $CFG, $DB;
        if (($user = $DB->get_record('user', array('username' => $username, 'mnethostid' => $CFG->mnet_localhost_id))) !== false) return validate_internal_user_password($user, $password);
        return false;
    }

    function user_update_password($user, $newpassword) {
        return update_internal_user_password(get_complete_user_data('id', $user->id), $newpassword);
    }

    function prevent_local_passwords() {
        return false;
    }

    function can_signup() {
        return true;
    }

    // shim to internal function which expects an array, not an object
    function truncate_user_obj($userobj) {
        $user_array = truncate_userinfo((array) $userobj);
        $obj = new stdClass();
        foreach($user_array as $key=>$value) {
            $obj->{$key} = $value;
        }
        return $obj;
    }

    // form post handler on signup form after everything validates
    function user_signup($user, $notify = false) {

        global $CFG, $DB, $SESSION;
        require_once ($CFG->dirroot . '/user/profile/lib.php');
        require_once ($CFG->dirroot . '/enrol/token/lib.php');

        $lang = empty($user->lang) ? $CFG->lang : $user->lang;
        $site  = get_site();
        $supportuser = core_user::get_support_user();
        $newpassword = generate_password();

        $newuser = new stdClass();
        $newuser->auth = 'token'; // since technially this auth plugin is a skin
        $newuser->firstname = $user->firstname;
        $newuser->lastname = $user->lastname;
        $newuser->password = hash_internal_user_password($newpassword, false);
        $newuser->policyagreed =  1; // might need to put this in somewhere
        $newuser->username = $user->email;
        $newuser->email = $user->email;
        $newuser->lastip = getremoteaddr();
        $newuser->timecreated = time();
        $newuser->timemodified = $newuser->timecreated;
        $newuser->mnethostid = $CFG->mnet_localhost_id;

        $newuser = truncate_user_obj($newuser);
        if (($newuser->id = $DB->insert_record('user', $newuser)) === false) {
            notice(get_string('signupfailure', 'auth_token'), $CFG->wwwroot);
            return false;
        }
        $user = get_complete_user_data('id', $newuser->id);
        \core\event\user_created::create_from_userid($user->id)->trigger();

        // ok, so I'm going to re-use the $newuser object for our email message, since it's mostly what we want
        $a = new stdClass();
        $a->username = $user->email;
        $a->password = $newpassword;
        $a->sitename = format_string($site->fullname);
        $a->link = $CFG->wwwroot . '/login/';
        $a->signoff = generate_email_signoff();

        $message = (string)new lang_string('signup_userregoemail', '', $a, $lang);
        $subject = format_string($site->fullname) .': '. (string)new lang_string('newusernewpasswordsubj', '', $a, $lang);

        // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
        email_to_user($user, $supportuser, $subject, $message);

        // log the user in immediately
        if ((($user = authenticate_user_login($user->username, $newpassword)) === false) || (complete_user_login($user) == null)) {
            notice(get_string('autologinfailure', 'auth_token'), $CFG->wwwroot);
            return false;
        }

        // if user was heading to a site page redirect to there, otherwise redirect to site home page
        redirect((empty($SESSION->wantsurl) === true) ? $CFG->wwwroot : $SESSION->wantsurl);

        return true;
    }

    function signup_form() {
        global $CFG;

        // use the token auth plugin custom signup page - not the default
        require_once ($CFG->dirroot . '/auth/token/signup_form.php');

        return new login_signup_form(null, null, 'post', '', array('autocomplete' => 'off'));
    }

    function user_update($olduser, $newuser) {
        global $DB;

        $newuser->username = $newuser->email;
        return ($DB->update_record('user', $newuser) !== false);
    }
}

