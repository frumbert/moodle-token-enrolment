<?php

require_once($CFG->libdir . "/externallib.php");

class local_token_external extends external_api {

	public static function generatetokens_parameters() {
		return new external_function_parameters(
			array(
				"course" => new external_value(PARAM_TEXT, "The course idnumber (alphanumeric - not the row id) you want to generate tokens for"),
				"seats" => new external_value(PARAM_INT, "The amount of token you want to generate (1-500)", VALUE_DEFAULT, 1),
				"places" => new external_value(PARAM_INT, "The amount of times a token can be re-used (1-500)", VALUE_DEFAULT, 1),
				"expiry" => new external_value(PARAM_INT, "The expiry date (as unix timestamp)", VALUE_DEFAULT, 0),
				"prefix" => new external_value(PARAM_TEXT, "Prefix tokens by this (0-4 character) string", VALUE_DEFAULT, ""),
				"cohort" => new external_value(PARAM_TEXT, "The cohort idnumber (alphanumeric - not the row id) you want to add the tokens for (created if missing)", VALUE_DEFAULT, "")
			  )
		);
	}

	// return {"token" : [value1,value2,value3]}
	public static function generatetokens_returns() {
		return new external_single_structure(
			array(
				'token' => new external_multiple_structure(
					new external_value(PARAM_TEXT, 'token code')
				)
			)
		);
	}

	public static function generatetokens($course_idnumber, $num_seats = 1, $places_per_seat = 1, $expiry = 0, $prefix = "", $cohort_idnumber = "local_token_webservice") {
		global $CFG, $USER;

		require_once($CFG->dirroot . "/blocks/enrol_token_manager/locallib.php");

		$context = get_context_instance(CONTEXT_USER, $USER->id);
		try {
			self::validate_context($context);
		} catch (Exception $e) {
			$exceptionparam = new stdClass();
			$exceptionparam->message = $e->getMessage();
			$exceptionparam->catid = $course['categoryid'];
			throw new moodle_exception('errorcatcontextnotvalid', 'webservice', '', $exceptionparam);
		}
		$syscontext = context_system::instance();
		require_capability('block/enrol_token_manager:createtokens', $syscontext);

		$params = self::validate_parameters(self::generatetokens_parameters(), array(
			"course" => $course_idnumber,
			"seats" => $num_seats,
			"places" => $places_per_seat,
			"expiry" => $expiry,
			"prefix" => $prefix,
			"cohort" => $cohort_idnumber
		));

		$tokens = enrol_token_manager_create_tokens_external($course_idnumber, $num_seats, $places_per_seat, $expiry, $prefix, $cohort_idnumber);

		return array("token" => $tokens);

	}

}
