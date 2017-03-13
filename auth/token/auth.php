<?php
defined('MOODLE_INTERNAL') || die();

require_once ($CFG->libdir . '/authlib.php');

class auth_plugin_token extends auth_plugin_base
{
	function auth_plugin_token() {
		$this->authtype = 'token';

		// is config stored in 'auth/token' or 'auth_token' ?
		$config = get_config('auth_token');
		$legacyconfig = get_config('auth/token');
		$this->config = (object)array_merge((array)$legacyconfig, (array)$config);

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

		// the token the user entered (which is now validated)
		$tokenValue = $user->token;

		$newuser = new stdClass();
		$newuser->auth = 'token'; // since technially this auth plugin is a skin
		$newuser->firstname = $user->firstname;
		$newuser->lastname = $user->lastname;
		$newuser->password = hash_internal_user_password($newpassword, false);
		$newuser->policyagreed =  1; // might need to put this in somewhere
		$newuser->username = $user->email;
		$newuser->email = $user->email;
		$newuser->lastip = getremoteaddr();
		$newuser->confirmed = 1; // seems to need it
		$newuser->timecreated = time();
		$newuser->timemodified = $newuser->timecreated;
		$newuser->mnethostid = $CFG->mnet_localhost_id;

		$newuser = self::truncate_user_obj($newuser);
		if (($newuser->id = $DB->insert_record('user', $newuser)) === false) {
			notice(get_string('signupfailure', 'auth_token'), $CFG->wwwroot);
			return false;
		}
		$user = get_complete_user_data('id', $newuser->id);
		\core\event\user_created::create_from_userid($user->id)->trigger();

		// just the query part of post-login redirect
		$params = (empty($SESSION->wantsurl) === true) ? '' : parse_url($SESSION->wantsurl, PHP_URL_QUERY);

		$a = new stdClass();
		$a->firstname = $user->firstname;
		$a->lastname = $user->lastname;
		$a->username = $user->username;
		$a->password = $newpassword;
		$a->sitename = format_string($site->fullname);
		$a->link = $CFG->wwwroot . '/auth/token/login.php?' . $params;
		$a->signoff = generate_email_signoff();

		$message = (string)new lang_string('signup_userregoemail', 'auth_token', $a, $lang);
		$subject = format_string($site->fullname) .': '. (string)new lang_string('newusernewpasswordsubj', '', $a, $lang);

		// Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
		email_to_user($user, $supportuser, $subject, $message);

		// log the user in immediately
		if ((($user = authenticate_user_login($user->username, $newpassword)) === false) || (complete_user_login($user) == null)) {
			notice(get_string('autologinfailure', 'auth_token'), $CFG->wwwroot);
			return false;
		}

		$return_to_url = (empty($SESSION->wantsurl) === true) ? $CFG->wwwroot : $SESSION->wantsurl;
		if (empty($tokenValue)) { // had no token, so just go on and let Moodle decide what to do next
				redirect($return_to_url);

		} else { // now, actually DO the enrolment for this course / user
			$token_plugin = new enrol_token_plugin();
			$courseId = 0;
			$enrolled_ok = $token_plugin->doEnrolment($tokenValue, $courseId, $return_to_url);

			if ($notify) {

				if (! send_confirmation_email($user)) {
					print_error('auth_token_noemail','auth_token');
				}

				global $CFG, $PAGE, $OUTPUT;
				$emailconfirm = get_string('emailconfirm');
				$PAGE->navbar->add($emailconfirm);
				$PAGE->set_title($emailconfirm);
				$PAGE->set_heading($PAGE->course->fullname);
				echo $OUTPUT->header();
				notice(get_string('emailconfirmsent', '', $user->email), $return_to_url);
			} else {

				if ($enrolled_ok == true) {
					redirect($return_to_url);
				}

				return ($enrolled_ok == true);
			}
		}
	}

	function signup_form() {
		global $CFG;

		// use the token auth plugin custom signup page - not the default
		require_once ($CFG->dirroot . '/auth/token/signup_form.php');

		$mf = new login_signup_form(null, null, 'post', '', array('autocomplete' => 'off'));
		return $mf;
	}

	function user_update($olduser, $newuser) {
		global $DB;

		$newuser->username = $newuser->email;
		return ($DB->update_record('user', $newuser) !== false);
	}

	/**
	 * Prints a form for configuring this authentication plugin.
	 *
	 * This function is called from admin/auth.php, and outputs a full page with
	 * a form for configuring this plugin.
	 *
	 * @param array $config An object containing all the data for this page.
	 * @param string $error
	 * @param array $user_fields
	 * @return void
	 */
	function config_form($config, $err, $user_fields) {
		include 'config.html';
	}

	/**
	 * Processes and stores configuration data for this authentication plugin.
	 *
	 * @param stdClass $config
	 * @return void
	 */
	function process_config($config) {
		set_config('logonpage_intro', $config->logonpage_intro, 'auth_token');
		set_config('registerredirect', $config->registerredirect, 'auth_token');
		return true;
	}

	/**
	 * Returns true if plugin allows confirming of new users.
	 *
	 * @return bool
	 */
	function can_confirm() {
		return false;
	}

	/**
	 * Confirm the new user as registered.
	 *
	 * @param string $username
	 * @param string $confirmsecret
	 */
	function user_confirm($username, $confirmsecret) {
		global $DB;
		$user = get_complete_user_data('username', $username);

		if (!empty($user)) {
			if ($user->confirmed) {
				return AUTH_CONFIRM_ALREADY;

			} else if ($user->auth != $this->authtype) {
				return AUTH_CONFIRM_ERROR;

			} else if ($user->secret == $confirmsecret) {   // They have provided the secret key to get in
				$DB->set_field("user", "confirmed", 1, array("id"=>$user->id));
				if ($user->firstaccess == 0) {
					$DB->set_field("user", "firstaccess", time(), array("id"=>$user->id));
				}
				return AUTH_CONFIRM_OK;
			}
		} else {
			return AUTH_CONFIRM_ERROR;
		}
	}

}

