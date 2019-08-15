<?php

defined('MOODLE_INTERNAL') || die();

$plugin->version  = 2019081500;   // The (date) version of this plugin
$plugin->requires  = 2013050100;        // Requires this Moodle version

$plugin->component = 'block_enrol_token_manager';

$plugin->dependencies = array(
    'enrol_token' => 2013080502,
    'filter_censor' => 2013050100
);