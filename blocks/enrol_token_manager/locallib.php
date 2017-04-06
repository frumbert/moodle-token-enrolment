<?php

defined('MOODLE_INTERNAL') || die();

function enrol_token_manager_contains_naughty_words($token) {
	global $CFG;

	// setup naughty words filter
	static $badwords = '-';
	if ($badwords == '-') {
		$badwords = (empty($CFG->filter_censor_badwords)) ? explode(',', get_string('badwords', 'filter_censor')) : explode(',', $CFG->filter_censor_badwords);
		foreach ($badwords as &$badword) $badword = trim($badword);
	}

	// see if any naughty words exist in the token
	foreach ($badwords as $badword) {
		if (stripos($token, $badword) !== false) return true;
	}

	return false;
}

// generate a number of string, prefixed with $prefix, that do not already exist in the database or contain filtered words
function enrol_token_manager_generate_token_data($tokennumber, $prefix) {
	global $DB;
	$tokens = [];
	$characters = '023456789abcdefghjkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ';
	$len_characters = strlen($characters);

	if (strlen($prefix) > 4) $prefix = substr($dprefix, 0, 4);
	for ($count = 0; ($count < $tokennumber); ++$count) {
		for ($goodToken = false; ($goodToken === false); /* empty */ ) {
			$goodToken = false;
			$token = $prefix;
			while (strlen($token) < 9) $token.= $characters[rand(0, $len_characters - 1)];
			if (enrol_token_manager_contains_naughty_words($token) === false) {
				if ($DB->count_records('enrol_token_tokens', array('id' => $token)) === 0) {
					$goodToken = true;
				}
			}
		}
		$tokens[] = $token;
	}
	return $tokens;
}

function enrol_token_manager_create_cohort_id($cohort_name, $cohort_idnumber) {
	global $DB;
	$context = context_system::instance();
	$cohort = new stdClass();
	$cohort->contextid = $context->id;
	$cohort->name = $cohort_name;
	$cohort->idnumber = $cohort_idnumber;
	$cohort->description = 'Created by enrol_token_manager';
	$cohort->descriptionformat = 1;
	$cohort->component = '';
	$cohort->timecreated = time();
	$cohort->timemodified = $cohort->timecreated;
	$cohortid = $DB->insert_record('cohort', $cohort);
	return $cohortid;
}

function enrol_token_manager_insert_tokens($cohort_id, $course_id, $tokens, $places_per_seat, $expirydate) {
	global $DB, $USER;
	$expiry_date = ($expirydate == 0) ? 0 : ($expirydate + (24 * 60 * 60)); // date specified is inclusive
	if (($transaction = $DB->start_delegated_transaction()) === null) throw new coding_exception('Invalid delegated transaction object');
	try {
		foreach ($tokens as $token) {
			$tokenRec = new stdClass();
			$tokenRec->id = $token;
			$tokenRec->cohortid = $cohort_id;
			$tokenRec->courseid = $course_id;
			$tokenRec->numseats = $places_per_seat;
			$tokenRec->seatsavailable = $places_per_seat;
			$tokenRec->createdby = $USER->id;
			$tokenRec->timecreated = time();
			$tokenRec->timeexpire = $expiry_date;
			if ($DB->insert_record_raw('enrol_token_tokens', $tokenRec, false, false, true) === false) throw new Excpetion('enrol_token_manager: token storage failed');
		}
		$transaction->allow_commit();
	} catch(Exception $e) {
		$transaction->rollback($e);
		notice("There was an error storing the generated tokens into the database. Please try again.");
		exit();
	}
}

function enrol_token_manager_create_tokens_external($course_idnumber, $num_seats, $places_per_seat, $expirydate, $prefix, $cohort_idnumber) {
	global $DB;

	// look up row id's from idnumbers
	$course_id = $DB->get_field("course", "id", array("idnumber" => $course_idnumber), MUST_EXIST);

	if (($cohort_id = $DB->get_field("cohort", "id", array("idnumber" => $cohort_idnumber), IGNORE_MISSING)) == false) {
		$cohort_id = enrol_token_manager_create_cohort_id("token_external_" . $cohort_idnumber, $cohort_idnumber);
	}

	// make an array of tokens
	$tokens = enrol_token_manager_generate_token_data($num_seats, $prefix);

	// save them into the database
	enrol_token_manager_insert_tokens($cohort_id, $course_id, $tokens, $places_per_seat, $expirydate);

	// return the tokens
	return $tokens;
}
