<?php
require ('../../config.php');

require_once ($CFG->libdir . '/formslib.php');
require_once ($CFG->libdir . '/accesslib.php');
require_once ($CFG->libdir . '/datalib.php');
require_once ($CFG->dirroot . '/cohort/lib.php');

require_once ($CFG->dirroot . '/blocks/enrol_token_manager/locallib.php');

class create_enrol_tokens_form extends moodleform
{
	function definition() {
		$mform = $this->_form;

		// course
		$courses = array();
		$dbcourses = get_courses();
		foreach ($dbcourses as $dbcourseid => $dbcourse) {
			 if ($dbcourse->id > 1) { // 1 = system course
				$courses[$dbcourseid] = $dbcourse->fullname;
			 }
		}

		$mform->addElement('select', 'course', get_string('promptcourse', 'block_enrol_token_manager'), $courses);
		$mform->addHelpButton('course', 'promptcourse', 'block_enrol_token_manager');

		// cohorts
		$context = context_system::instance();
		$cohorts = array();
		$dbcohorts = cohort_get_cohorts($context->id);
		foreach ($dbcohorts['cohorts'] as $dbcohort) $cohorts[$dbcohort->id] = $dbcohort->name;

		// cohort selection
		$mform->addElement('header', 'cohorts', get_string('cohort_selection', 'block_enrol_token_manager'));
		$mform->addElement('select', 'cohortexisting', get_string('promptcohortexisting', 'block_enrol_token_manager'), $cohorts);
		$mform->addElement('static', 'cohortor', '', 'OR');
		$mform->addHelpButton('cohortor', 'cohortor', 'block_enrol_token_manager');
		$mform->addElement('text', 'cohortnew', get_string('promptcohortnew', 'block_enrol_token_manager'), 'maxlength="253" size="25"');
		$mform->setType('cohortnew', PARAM_CLEANHTML);

		// token prefix
		$mform->addElement('header', 'tokens', get_string('token_generation', 'block_enrol_token_manager'));
		$mform->addElement('text', 'prefix', get_string('promptprefix', 'block_enrol_token_manager'), 'maxlength="4" size="4"');
		$mform->addHelpButton('prefix', 'promptprefix', 'block_enrol_token_manager');

		// $mform->addElement('static', 'prefixinstructions', get_string('prefixinstructions', 'block_enrol_token_manager'));
		$mform->setType('prefix', PARAM_ALPHANUMEXT);

		// seats per token
		$mform->addElement('text', 'seatspertoken', get_string('promptseats', 'block_enrol_token_manager'), 'maxlength="5" size="3"');
		$mform->addHelpButton('seatspertoken', 'promptseats', 'block_enrol_token_manager');
		$mform->setType('seatspertoken', PARAM_INT);

		// number of tokens
		$mform->addElement('text', 'tokennumber', get_string('prompttokennum', 'block_enrol_token_manager'), 'maxlength="4" size="3"');
		$mform->addHelpButton('tokennumber', 'prompttokennum', 'block_enrol_token_manager');
		$mform->setType('tokennumber', PARAM_INT);

		// expiry date
		$mform->addElement('date_selector', 'expirydate', get_string('promptexpirydate', 'block_enrol_token_manager'), array('optional' => true));

		// email to
		$mform->addElement('text', 'emailaddress', get_string('emailaddressprompt', 'block_enrol_token_manager'), 'maxlength="128" size="50"');
		$mform->setType('emailaddress', PARAM_EMAIL);

		// email subject
		$mform->addElement('text', 'mailsubject', get_string('mailsubject', 'block_enrol_token_manager'), 'size="50"');
		$mform->setDefault('mailsubject', get_string('mailsubject_default', 'block_enrol_token_manager'));
		$mform->setType('mailsubject', PARAM_TEXT);
		$mform->addHelpButton('mailsubject', 'mailsubject', 'block_enrol_token_manager');

		// email body
		$mform->addElement('textarea', 'mailbody', get_string('mailbody', 'block_enrol_token_manager'), array('cols'=>100,'rows'=>7));
		$mform->setDefault('mailbody', get_string('mailbody_default', 'block_enrol_token_manager'));
		$mform->setType('mailbody', PARAM_RAW);
		$mform->addHelpButton('mailbody', 'mailbody', 'block_enrol_token_manager');

		// buttons
		$this->add_action_buttons(false, get_string('createtokens', 'block_enrol_token_manager'));
	}

	function definition_after_data() {
		$mform = $this->_form;
		$mform->applyFilter('cohortnew', 'trim');
		$mform->applyFilter('emailaddress', 'trim');
		$mform->applyFilter('prefix', 'trim');
	}

	function validation($data, $files) {
		$errors = parent::validation($data, $files);

		// seats per token
		if (($data['seatspertoken'] < 1) || ($data['seatspertoken'] > 10000)) $errors['seatspertoken'] = get_string('seatsoutofrange', 'block_enrol_token_manager');

		// number of token
		if (($data['tokennumber'] < 1) || ($data['tokennumber'] > 10000)) $errors['tokennumber'] = get_string('tokensoutofrange', 'block_enrol_token_manager');

		// email address
		if ((isset($data['emailaddress']) === true) && (trim($data['emailaddress'] != ''))) {
			if (validate_email($data['emailaddress']) === false) $errors['emailaddress'] = get_string('invalidemail');
		}

		return $errors;
	}
}



////////////////////////////////////////////////////////////////////////////////
$context = context_system::instance();

$site = get_site();

$pagename = get_string('pageNameCreateTokens', 'block_enrol_token_manager');
$pageurl = '/blocks/enrol_token_manager/create_tokens.php';

// page setup
$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->add_body_class('createenroltokens');
$PAGE->set_title("$site->fullname: $pagename");
$PAGE->set_heading('Create Enrolment Tokens');
$PAGE->navbar->add($pagename);

echo $OUTPUT->header();

// check users capabilities to view this page
if (has_capability('block/enrol_token_manager:createtokens', $context) === false) {
	notice(get_string('nopagepermission', 'block_enrol_token_manager'), '/');
	exit;
}

$form = new create_enrol_tokens_form();

if (($data = $form->get_data()) === null) $form->display();
else {

	// make an array of tokens
	$tokens = enrol_token_manager_generate_token_data($data->tokennumber, $data->prefix);

	// get or create the cohort
	$cohortid = (empty($data->cohortid) === true) ? 0 : $data->cohortid;
	if (!empty($data->cohortnew)) {
		$cohort_idnumber = preg_replace('/\s+/', '', strtolower(substr($data->cohortnew, 0, 99)));
		$cohortid = enrol_token_manager_create_cohort_id($data->cohortnew, $cohort_idnumber);
	} else {
		$cohortid = $data->cohortexisting;
	}

	// store the tokens in the database (transacted)
	enrol_token_manager_insert_tokens($cohortid, $data->course, $tokens, $data->seatspertoken, $data->expirydate);

	// construct a summary of this action to send
	$course = get_course($data->course);
	$data->coursename = $course->fullname;
	$data->tokennumberplural = ($data->tokennumber > 1) ? 's' : '';
	$data->seatspertokenplural = ($data->seatspertoken > 1) ? 's' : '';
	$data->wwwroot = $CFG->wwwroot;
	$data->tokens = implode(', ', $tokens);
	$data->adminsignoff = generate_email_signoff();

	// get text to use for on-screen notice and email
	$array = (array) $data;
	$array = array_combine(
		array_map(function($k){ return '{'.$k.'}'; }, array_keys($array)),
		$array
	);
	$message_display = get_string('noticetext', 'block_enrol_token_manager', $data);
	// print_r($array);

	$messagehtml = str_replace(array_keys($array), $array, format_text($data->mailbody));
	$messagetext = html_to_text($messagehtml, 75, false);

	// queue email for sending if required
	if ((isset($data->emailaddress) === true) && (trim($data->emailaddress != ''))) {

		// create a fake user to send email to because the email recipient may not be a system user (yet)
		$fakeUser = new stdClass();
		$fakeUser->id = -1;
		$fakeUser->deleted = false;
		$fakeUser->mailformat = 1;
		$fakeUser->email = $data->emailaddress;
		$fakeUser->firstname = "";
		$fakeUser->username = "";
		$fakeUser->lastname =  "";
		$fakeUser->confirmed = 1;
		$fakeUser->suspended = 0;
		$fakeUser->deleted = 0;
		$fakeUser->picture = 0;
		$fakeUser->auth = "manual";
		$fakeUser->firstnamephonetic = "";
		$fakeUser->lastnamephonetic =  "";
		$fakeUser->middlename =  "";
		$fakeUser->alternatename =  "";
		$fakeUser->imagealt =  "";
		$fakeUser->maildisplay = 1;
		$fakeUser->emailstop = 0;


		// if email fails to send - warn token creating user on screen
		if (email_to_user($fakeUser, core_user::get_support_user(), $data->mailsubject, $messagetext, $messagehtml) === false) {
			$messagehtml = $OUTPUT->error_text('Warning - there was a problem automatcially emailing these token codes. ' . 'Please copy the message below and paste it into a manually generated email.');
		}
	}


	notice($message_display, $pageurl);
	echo "<p><a href='/blocks/enrol_token_manager/viewrevoke_tokens.php'>View or revoke tokens</a></p>";

}

echo $OUTPUT->footer();
?>
