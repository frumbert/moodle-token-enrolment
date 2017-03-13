<?php
require ('../../config.php');

$context = context_system::instance();
$site = get_site();
$loginsite = get_string("loginsite");

// page setup
$PAGE->https_required();
$PAGE->set_url("$CFG->httpswwwroot/auth/token/login.php");
$PAGE->set_context($context);
$PAGE->set_title("$site->fullname: $loginsite");
$PAGE->set_heading($site->fullname);
$PAGE->navbar->add($loginsite);
// $PAGE->requires->css('auth/token/token.css');

// make sure we really are on the https page when https login required
$PAGE->verify_https_required();

$courseid = optional_param('id', '', PARAM_INT);
$redirectto = optional_param('redirectto', '', PARAM_RAW);
$tokenqs = optional_param('token', '', PARAM_RAW);
$urlextra = '';

// $showLogon = ($noredirect == 1);

if (!empty($redirectto)) {
	// $SESSION->coursehomeurl = $redirectto;
	$urlextra = "&redirectto=$redirectto";
}

$authplugin = get_auth_plugin("token");
$mform_signup = $authplugin->signup_form();

if (!empty($tokenqs)) {
	if (!empty($authplugin->config->registerredirect)) {
		$SESSION->wantsurl = $authplugin->config->registerredirect;
	}
}

if (isset($courseid) && $courseid > 0) {
	  $SESSION->wantsurl = $CFG->wwwroot.'/course/view.php?id='.$courseid . $urlextra;
}

// fix up forwarding url
if ((isset($SESSION->wantsurl) === true) && ((strncmp($SESSION->wantsurl, $CFG->httpswwwroot . $_SERVER['PHP_SELF'], strlen($CFG->httpswwwroot . $_SERVER['PHP_SELF'])) === 0) || (strncmp($SESSION->wantsurl, $CFG->wwwroot . $_SERVER['PHP_SELF'], strlen($CFG->wwwroot . $_SERVER['PHP_SELF'])) === 0))) {
	// don't ever want to return to alternate login url with a successful login - go to nowhere (home page) instead
	$SESSION->wantsurl = NULL;
}

// if $mform_signup->is_submitted() ...
// if $mform_signup->is_cancelled() ...
// $mform_signup->get_data() will be empty until $mform_signup->is_validated() returns true


if (($user = $mform_signup->get_data()) !== null) {
	$user->confirmed = 1;
	$user->lang = current_language();
	$user->firstaccess = time();
	$user->timecreated = time();
	$user->mnethostid = $CFG->mnet_localhost_id;
	$user->auth = 'token';  // $CFG->registerauth;


	 // will either redirect to site home or a notice of failure
	$authplugin->user_signup($user, false);
	exit;
}

// set error message if passed from moodle login page
$errormsg = null;
if (isset($_REQUEST['errorcode']) === true) {
	switch ($_REQUEST['errorcode']) {
		case (1):
			$errormsg = get_string("cookiesnotenabled");
			break;

		case (2):
			$errormsg = get_string('username') . ': ' . get_string("invalidusername");
			break;

		case (3):
			$errormsg = get_string("invalidlogin");
			break;

		case (4):
			$errormsg = get_string('sessionerroruser', 'error');
			break;
	}
}

$autocomplete = (!empty($CFG->loginpasswordautocomplete)) ? 'autocomplete="off"' : '';

echo $OUTPUT->header();

// prevent logging when already logged in, we do not want them to relogin by accident because sesskey would be changed
if (isloggedin() and !isguestuser()) {
	echo $OUTPUT->box_start();
	$logout = new single_button(new moodle_url($CFG->httpswwwroot.'/login/logout.php', array('sesskey'=>sesskey(),'loginpage'=>1)), get_string('logout'), 'post');
	$continue = new single_button(new moodle_url($CFG->httpswwwroot.'/login/index.php', array('cancel'=>1)), get_string('cancel'), 'get');
	echo $OUTPUT->confirm(get_string('alreadyloggedin', 'error', fullname($USER)), $logout, $continue);
	echo $OUTPUT->box_end();
} else {
	include("login_form.php");
	if ($errormsg) {
		$PAGE->requires->js_init_call('M.util.focus_login_error', null, true);
	} else if (!empty($CFG->loginpageautofocus)) {
		//focus username or password
		$PAGE->requires->js_init_call('M.util.focus_login_form', null, true);
	}
}

echo $OUTPUT->footer();