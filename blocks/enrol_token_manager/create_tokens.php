<?php
require ('../../config.php');

require_once ($CFG->libdir . '/formslib.php');
require_once ($CFG->libdir . '/accesslib.php');
require_once ($CFG->libdir . '/datalib.php');
require_once ($CFG->dirroot . '/cohort/lib.php');

class create_enrol_tokens_form extends moodleform
{
    function definition() {
        $mform = $this->_form;

        // course
        $courses = array();
        $dbcourses = get_courses();
        foreach ($dbcourses as $dbcourseid => $dbcourse) {
            if ($dbcourse->id > 1) {
                 // 1 = system course, ala front-page. probably don't want it there!
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
        $mform->addHelpButton('prefix', 'promptprefix_help', 'block_enrol_token_manager');

        // $mform->addElement('static', 'prefixinstructions', get_string('prefixinstructions', 'block_enrol_token_manager'));
        $mform->setType('prefix', PARAM_ALPHANUMEXT);

        // seats per token
        $mform->addElement('text', 'seatspertoken', get_string('promptseats', 'block_enrol_token_manager'), 'maxlength="3" size="3"');
        $mform->addHelpButton('seatspertoken', 'promptseats', 'block_enrol_token_manager');
        $mform->setType('seatspertoken', PARAM_INT);

        // number of tokens
        $mform->addElement('text', 'tokennumber', get_string('prompttokennum', 'block_enrol_token_manager'), 'maxlength="3" size="3"');
        $mform->addHelpButton('tokennumber', 'prompttokennum', 'block_enrol_token_manager');
        $mform->setType('tokennumber', PARAM_INT);

        // expiry date
        $mform->addElement('date_selector', 'expirydate', get_string('promptexpirydate', 'block_enrol_token_manager'), array('optional' => true));

        // email to
        $mform->addElement('text', 'emailaddress', get_string('emailaddressprompt', 'block_enrol_token_manager'), 'maxlength="128" size="50"');
        $mform->setType('emailaddress', PARAM_EMAIL);

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
        if (($data['seatspertoken'] < 1) || ($data['seatspertoken'] > 200)) $errors['seatspertoken'] = get_string('seatsoutofrange', 'block_enrol_token_manager');

        // number of token
        if (($data['tokennumber'] < 1) || ($data['tokennumber'] > 200)) $errors['tokennumber'] = get_string('tokensoutofrange', 'block_enrol_token_manager');

        // email address
        if ((isset($data['emailaddress']) === true) && (trim($data['emailaddress'] != ''))) {
            if (validate_email($data['emailaddress']) === false) $errors['emailaddress'] = get_string('invalidemail');
        }

        return $errors;
    }
}

function enrolTokenManagerTokenContainsNaughtyWords($token) {
    global $CFG;

    // setup naughty words filter
    static $badwords = '-';
    if ($badwords == '-') {
        $badwords = (empty($CFG->filter_censor_badwords)) ? explode(',', get_string('badwords', 'filter_censor')) : explode(',', $CFG->filter_censor_badwords);

        foreach ($badwords as & $badword) $badword = trim($badword);
    }

    // see if any naughty words exist in the token
    foreach ($badwords as $badword) {
        if (stripos($token, $badword) !== false) return true;
    }

    return false;
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
    $tokens = array();

    // ensure prefix is valid
    if (strlen($data->prefix) > 4) $data->prefix = substr($data->prefix, 0, 4);

    // generate number of tokens required
    for ($count = 0; ($count < $data->tokennumber); ++$count) {

        // (re-)generate each token until it is good (no bad words and not already existing)
        for ($goodToken = false; ($goodToken === false);
         /* empty */
        ) {
            $goodToken = false;

            static $characters = '023456789abcdefghjkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ';

            // generate random token
            $token = $data->prefix;
            while (strlen($token) < 9) $token.= $characters[rand(0, strlen($characters) - 1) ];

            // if token contains no naughty words...
            if (enrolTokenManagerTokenContainsNaughtyWords($token) === false) {

                // check the db to see if token exists already
                if ($DB->count_records('enrol_token_tokens', array('id' => $token)) === 0) $goodToken = true;
            }
        }

        // add generated token to list
        $tokens[] = $token;
    }

    // add tokens to persistent data store in a transaction so we can rollback if any can't be added
    if (($transaction = $DB->start_delegated_transaction()) === null) throw new coding_exception('Invalid delegated transaction object');

    try {

        // if a new cohort has been specified...
        $cohortid = (empty($data->cohortid) === true) ? 0 : $data->cohortid;
         // default to cohort in drop down selection list
        if (empty($data->cohortnew) === false) {
            $cohort = new stdClass();
            $cohort->contextid = $context->id;
            $cohort->name = $data->cohortnew;
            $cohort->idnumber = preg_replace('/\s+/', '', strtolower(substr($data->cohortnew, 0, 99)));
             // remove whitespace from shortened, lowercased name
            $cohort->description = '<p>' . $cohort->name . '</p>';
            $cohort->descriptionformat = 1;
            $cohort->component = '';
            $cohort->timecreated = time();
            $cohort->timemodified = $cohort->timecreated;

            // add record and get new id
            $cohortid = $DB->insert_record('cohort', $cohort);
        }

        foreach ($tokens as $token) {

            // add token usage record
            $tokenRec = new stdClass();
            $tokenRec->id = $token;
            $tokenRec->cohortid = $cohortid;
            $tokenRec->courseid = $data->course;
            $tokenRec->numseats = $data->seatspertoken;
            $tokenRec->seatsavailable = $tokenRec->numseats;
            $tokenRec->createdby = $USER->id;
            $tokenRec->timecreated = time();
            $tokenRec->timeexpire = ($data->expirydate == 0) ? 0 : ($data->expirydate + (24 * 60 * 60));
             // expiry date plus 24 hours (end of the expiry day)
            if ($DB->insert_record_raw('enrol_token_tokens', $tokenRec, false, false, true) === false) throw new Excpetion('token storage failed');
        }

        // commit the transaction
        $transaction->allow_commit();
    }
    catch(Exception $e) {
        $transaction->rollback($e);

        notice("There was an error storing the generated tokens into the database. Please try again.");

        exit();
    }

    $course = get_course($data->course);
    $data->coursename = $course->fullname;
    $data->tokennumberplural = ($data->tokennumber > 1) ? 's' : '';
    $data->seatspertokenplural = ($data->seatspertoken > 1) ? 's' : '';
    $data->wwwroot = $CFG->wwwroot;
    $data->tokens = implode(', ', $tokens);
    $data->adminsignoff = generate_email_signoff();
     // moodlelib

    // get text to use for on-screen notice and email
    $messagehtml = get_string('noticetext', 'block_enrol_token_manager', $data);

    $messagetext = html_to_text($messagehtml, 75, false);

    // queue email for sending if required
    if ((isset($data->emailaddress) === true) && (trim($data->emailaddress != ''))) {

        // create a fake user to send email to because the email recipient may not be a system user (yet)
        $fakeUser = new stdClass();
        $fakeUser->email = $data->emailaddress;
        $fakeUser->deleted = false;
        $fakeUser->id = 0;
        $fakeUser->mailformat = 1;

        // if email fails to send - warn token creating user on screen
        if (email_to_user($fakeUser,
         // why not core_user::get_noreply_user
        core_user::get_support_user,
         // generate_email_supportuser(),
        get_string('emailsubject', 'block_enrol_token_manager'), $messagetext, $messagehtml) === false) {
            $messagehtml = $OUTPUT->error_text('Warning - there was a problem automatcially emailing these token codes. ' . 'Please copy the message below and paste it into a manually generated email.') . $messagehtml;
        }
    }

    // show on-screen notice - same as email
    notice($messagehtml, $pageurl);
}

echo $OUTPUT->footer();
?>
