<?php
require ('../../config.php');

require_once ($CFG->libdir . '/formslib.php');
require_once ($CFG->libdir . '/accesslib.php');
require_once ($CFG->libdir . '/datalib.php');
require_once ($CFG->dirroot . '/cohort/lib.php');
require_once ($CFG->libdir . '/outputcomponents.php');

class view_enrol_tokens_form extends moodleform
{
    function definition() {
        global $DB;

        $mform = $this->_form;

        // course
        $dbcourses = $DB->get_records_sql('SELECT id, fullname FROM {course} INNER JOIN (SELECT DISTINCT courseid FROM {enrol_token_tokens}) s WHERE s.courseid = id');
        if (count($dbcourses) > 0) {
            $courses = array(0 => 'Select...');
            foreach ($dbcourses as $dbcourse) $courses[$dbcourse->id] = $dbcourse->fullname;
            $mform->addElement('select', 'course', get_string('promptfiltercourse', 'block_enrol_token_manager'), $courses);
        }

        // filters
        $mform->addElement('header', 'filter', get_string('cohort_view_filter', 'block_enrol_token_manager'));
        $mform->setExpanded('header',false); // close

        $mform->addElement('text', 'token', get_string('promptfiltertoken', 'block_enrol_token_manager'), 'maxlength="12" size="12"');
        $mform->addHelpButton('token','promptfiltertoken','block_enrol_token_manager');
        $mform->setType('token', PARAM_TEXT);

        // cohorts
        $dbcohorts = $DB->get_records_sql('SELECT id, name FROM {cohort} INNER JOIN (SELECT DISTINCT cohortid FROM {enrol_token_tokens}) s WHERE s.cohortid = id');
        if (count($dbcohorts) > 0) {
            $cohorts = array(0 => 'Select...');
            foreach ($dbcohorts as $dbcohort) $cohorts[$dbcohort->id] = $dbcohort->name;

            $mform->addElement('select', 'cohort', get_string('promptfiltercohort', 'block_enrol_token_manager'), $cohorts);
        }

        // creation date range
        $mform->addElement('date_selector', 'creationdatemin', get_string('promptfiltercreationdatemin', 'block_enrol_token_manager'), array('optional' => true));
        $mform->addElement('date_selector', 'creationdatemax', get_string('promptfiltercreationdatemax', 'block_enrol_token_manager'), array('optional' => true));

        // expiry date range
        $mform->addElement('date_selector', 'expirydatemin', get_string('promptfilterexpirydatemin', 'block_enrol_token_manager'), array('optional' => true));
        $mform->addElement('date_selector', 'expirydatemax', get_string('promptfilterexpirydatemax', 'block_enrol_token_manager'), array('optional' => true));

        // create by
        $dbusers = $DB->get_records_sql('SELECT id, username FROM {user} INNER JOIN (SELECT DISTINCT createdby FROM {enrol_token_tokens}) s WHERE s.createdby = id');
        if (count($dbusers) > 0) {
            $users = array(0 => 'Select...');
            foreach ($dbusers as $dbuser) $users[$dbuser->id] = $dbuser->username;

            $mform->addElement('select', 'createdby', get_string('promptfiltercreatedby', 'block_enrol_token_manager'), $users);
        }


        // buttons
        $this->add_action_buttons(false, get_string('viewtokens', 'block_enrol_token_manager'));
    }

    function definition_after_data() {
        $mform = $this->_form;
        $mform->applyFilter('token', 'trim');
    }
}

function appendSqlWhereClause(&$existingClause, $newClause) {
    $existingClause.= (($existingClause != '') ? ' AND ' : '') . $newClause;
}

////////////////////////////////////////////////////////////////////////////////
$context = context_system::instance();

$site = get_site();

$pagename = get_string('pageNameViewRevokeTokens', 'block_enrol_token_manager');
$pageurl = '/blocks/enrol_token_manager/viewrevoke_tokens.php';

// page setup
$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->add_body_class('viewrevokeenroltokens');
$PAGE->set_title("$site->fullname: $pagename");
$PAGE->set_heading('View/Revoke Enrolment Tokens');
$PAGE->navbar->add($pagename);

echo $OUTPUT->header();

// check users capabilities to view this page
if (has_capability('block/enrol_token_manager:viewtokens', $context) === false) {
    notice(get_string('nopagepermission', 'block_enrol_token_manager'), '/');
    exit;
}

$canRevoke = has_capability('block/enrol_token_manager:revoketokens', context_system::instance());

// do revokes if they have been submitted
if (($canRevoke === true) && (isset($_REQUEST) === true) && (isset($_REQUEST['rev']) === true)) {
    $revokeTokens = array_keys($_REQUEST['rev']);
    if ($DB->delete_records_list('enrol_token_tokens', 'id', $revokeTokens) === true) {

      echo $OUTPUT->notification(count($revokeTokens) . ' enrolment tokens ' . ((count($revokeTokens) > 1) ? 'were' : 'was') . ' revoked', 'notifysuccess');

    } else {

      echo $OUTPUT->error_text(get_string('token_delete_error','block_enrol_token_manager'));

    }
}

$form = new view_enrol_tokens_form();

$form->display();

if (($data = $form->get_data()) !== null) {

    // build SQL statement from given options
    $where = '';

    if ($data->token != '') appendSqlWhereClause($where, "t.id LIKE '" . str_replace(array('*', '?'), array('%', '_'), $data->token) . "'");

    if ($data->course != 0) appendSqlWhereClause($where, "t.courseid = {$data->course}");

    if ($data->cohort != 0) appendSqlWhereClause($where, "t.cohortid = {$data->cohort}");

    if ($data->creationdatemin != 0) appendSqlWhereClause($where, "t.timecreated >= {$data->creationdatemin}");

    if ($data->creationdatemax != 0) appendSqlWhereClause($where, "t.timecreated <= " . ($data->creationdatemax + (24 * 60 * 60)));

    if ($data->expirydatemin != 0) appendSqlWhereClause($where, "t.timeexpire >= {$data->creationdatemin}");

    if ($data->expirydatemax != 0) appendSqlWhereClause($where, "t.timeexpire <= " . ($data->creationdatemax + (24 * 60 * 60)));

    if ($data->createdby != 0) appendSqlWhereClause($where, "t.createdby = {$data->createdby}");

    $tokens = $DB->get_records_sql('SELECT t.id, c.fullname, o.name, CONVERT(' . $DB->sql_concat('t.seatsavailable', "' of '", 't.numseats') . ', CHAR(20)) seats, u.username, ' . $DB->sql_concat("CONVERT(CASE t.timecreated WHEN 0 THEN '' ELSE DATE(FROM_UNIXTIME(t.timecreated)) END, CHAR(20))", "' -> '", "CONVERT(CASE t.timeexpire WHEN 0 THEN '' ELSE DATE(FROM_UNIXTIME(t.timeexpire)) END, CHAR(20))") . " validityPeriod " . 'FROM {enrol_token_tokens} t ' . 'LEFT JOIN {course} c ON c.id = t.courseid ' . 'LEFT JOIN {cohort} o ON o.id = t.cohortid ' . 'LEFT JOIN {user} u ON u.id = t.createdby ' . (($where != '') ? ('WHERE ' . $where . ' ') : '') . 'ORDER BY t.timecreated, t.id', null);

    if (count($tokens) === 0) {

      $OUTPUT->error_text('No matching tokens found. Try changing the filters.');

    } else {

        $tableHeaders = array('Token', 'Course', 'Cohort', 'Seats', 'Created By', 'Valid');

        // user has capapbility to revoke tokens...
        if ($canRevoke === true) {
            // adds a check-all style box
            $tableHeaders[] = 'Revoke ' . html_writer::checkbox("betmrca", '', false, '', array('id' => 'blockEnrolTokenManagerRevokeCheckAll', 'onclick' => 'var chk = this.checked; [].forEach.call(document.querySelectorAll("#blockEnrolTokenManagerResults tbody input[type=\'checkbox\']"),function(el) { if (chk) { el.setAttribute("checked",true) } else { el.removeAttribute("checked") } })'));

            // add revoke checkbox to each entry
            foreach ($tokens as & $token) $token->revoke = html_writer::checkbox("rev[{$token->id}]", 1, false);
        }

        $table = new html_table();
        $table->head = $tableHeaders;
        $table->data = $tokens;
        $table->id = 'blockEnrolTokenManagerResults';

        $actionButton = html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('revoketokens', 'block_enrol_token_manager')));

        $output = '';

        // top revoke button
        if ($canRevoke === true) $output.= $actionButton;

        // table
        $output.= html_writer::table($table);

        // bottom revoke button
        if (($canRevoke === true) && (count($tokens) > 10)) $output.= $actionButton;

        $attributes = array('method' => 'post', 'action' => new moodle_url($_SERVER['PHP_SELF']), 'id' => 'viewRevokeForm');
        $output = html_writer::tag('form', $output, $attributes);

        echo $output;
    }
}

echo $OUTPUT->footer();
?>
