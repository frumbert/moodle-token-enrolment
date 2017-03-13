<?php
class block_enrol_token_manager extends block_base {

	function init() {
		$this->title = get_string('pluginname', 'block_enrol_token_manager');
	}

	function applicable_formats() {
		return array('site' => true);
	}

	function can_hide_show_instance() {
		return true;
	}

	function get_content() {

		if ($this->content !== NULL) {
			return $this->content;
		}

		$context = context_system::instance();

		$this->content = new stdClass();
		$this->content->footer = '';
		$this->content->text = '';
		$content = array();

		$canview = (has_capability('block/enrol_token_manager:viewtokens', $context) === true);
		$cancreate = (has_capability('block/enrol_token_manager:createtokens', $context) === true);
		$canrevoke = (has_capability('block/enrol_token_manager:revoketokens', $context) === true);

		// view tokens link
		if ($canview) {
			$content[] = html_writer::link(new moodle_url('/blocks/enrol_token_manager/viewrevoke_tokens.php'), get_string('linkTextViewTokens', 'block_enrol_token_manager'));
		}

		// create tokens link
		if ($cancreate) {
			$content[] = html_writer::link(new moodle_url('/blocks/enrol_token_manager/create_tokens.php'), get_string('linkTextCreateTokens', 'block_enrol_token_manager'));
		}

		// revoke tokens link
		if ($canrevoke) {
			$content[] = html_writer::link(new moodle_url('/blocks/enrol_token_manager/viewrevoke_tokens.php'), get_string('linkTextRevokeTokens', 'block_enrol_token_manager'));
		}

		if ($cancreate || $canrevoke || $canview) {
		  $this->content->text = html_writer::alist($content);
		}

		return $this->content;
	}
}
