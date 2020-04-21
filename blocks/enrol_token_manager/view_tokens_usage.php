<?php
require ('../../config.php');

$CFG->debug = (E_ALL | E_STRICT);
$CFG->debugdisplay = 1;

require_once ($CFG->libdir . '/formslib.php');
require_once ($CFG->libdir . '/accesslib.php');
require_once ($CFG->libdir . '/datalib.php');
require_once ($CFG->dirroot . '/cohort/lib.php');
require_once ($CFG->libdir . '/outputcomponents.php');

class view_enrol_token_usage_form extends moodleform
{
	function definition() {
		global $DB;

		$mform = $this->_form;

		// filters
		$mform->addElement('header', 'filter', get_string('filter'));

		$mform->addElement('text', 'token', get_string('promptfiltertoken', 'block_enrol_token_manager'), 'maxlength="12" size="12"');
		$mform->addHelpButton('token','promptfiltertoken','block_enrol_token_manager');
		$mform->setType('token', PARAM_TEXT);

		// buttons
		$this->add_action_buttons(false, get_string('viewusers', 'block_enrol_token_manager'));
	}

	function definition_after_data() {
		$mform = $this->_form;
		$mform->applyFilter('token', 'trim');
	}
}

function appendSqlWhereClause(&$existingClause, $newClause) {
	$existingClause.= (($existingClause != '') ? ' AND ' : '') . $newClause;
}


$context = context_system::instance();

$site = get_site();

$pagename = get_string('pageNameViewTokenUsage', 'block_enrol_token_manager');
$pageurl = '/blocks/enrol_token_manager/view_token_usage.php';

// page setup
$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->add_body_class('viewtokenusage');
$PAGE->set_title("$site->fullname: $pagename");
$PAGE->set_heading(get_string('view_token_usage', 'block_enrol_token_manager'));
$PAGE->navbar->add($pagename);

echo $OUTPUT->header();

// check users capabilities to view this page
if (has_capability('block/enrol_token_manager:viewtokens', $context) === false) {
	notice(get_string('nopagepermission', 'block_enrol_token_manager'), '/');
	exit;
}

$form = new view_enrol_token_usage_form();

$form->display();

if (($data = $form->get_data()) !== null) {

	// build SQL statement from given options
	$where = '';
	if ($data->token != '') $where = "WHERE t.token LIKE ?";
	$fields = 't.timecreated, c.id courseid, c.fullname coursename, ' .
				\user_picture::fields('u', ['idnumber'], 'userid') .
				get_extra_user_fields_sql($context, 'u', '', ['email', 'idnumber']) .
				' ';
	$from = '{user} u INNER JOIN {enrol_token_log} t ON u.id = t.userid INNER JOIN {enrol_token_tokens} tt ON tt.id = t.token INNER JOIN {course} c ON tt.courseid = c.id';
	$order = 't.timecreated DESC';

   // echo "SELECT {$fields} FROM {$from} {$where} ORDER BY {$order}";

	$data = $DB->get_records_sql("SELECT {$fields} FROM {$from} {$where} ORDER BY {$order}", [str_replace(['*', '?'], ['%', '_'], $data->token)]);
	if (count($data) === 0) {
		$OUTPUT->error_text('No records');
	} else {
		$table = new html_table();
		$table->id = 'viewtokenusage';
		$table->head = ['User','Date used',get_string('course')];
		$rows = [];
		foreach ($data as $record) {

            $url = new \moodle_url('/user/view.php', array('id' => $record->userid, 'course' => $record->courseid));
            $user = \html_writer::link($url, fullname($record));
            $coursename = $record->coursename;
            $date = userdate($record->timecreated);

			$rows[] = [$user, $date, $coursename];
		}
		$table->data = $rows;
		echo html_writer::table($table);

	}
}

echo $OUTPUT->footer();