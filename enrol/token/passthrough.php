<?php
require_once ('../../config.php');
require_once ('./lib.php');

$retVal = true;

$tokenParam = optional_param('token', null, PARAM_ALPHANUM);

// if token value is POSTed in then it has come from a form. else it has come from querystring
if (isset($_POST['token']) === true) $SESSION->tokenFrom = (empty($_SERVER['HTTP_REFERER']) === false) ? $_SERVER['HTTP_REFERER'] : $CFG->wwwroot;

if (empty($tokenParam) === true) {
    $retVal = 2;
} else {
    $plugin = new enrol_token_plugin();
    // try enrolment, and return back here after user logs in
    $courseId = 0;
    $retVal = $plugin->doEnrolment($tokenParam, $courseId, "{$FULLME}?token={$tokenParam}");
}

$goToUrl = empty($SESSION->tokenFrom) ? $CFG->wwwroot : $SESSION->tokenFrom;

unset($SESSION->tokenFrom);

// if there are no errors, redirect to course page
if ($retVal === true) {
    require_once ("{$CFG->dirroot}/course/lib.php");
    redirect(course_get_url($courseId));
}

// errors
// overwrite any existing tokenerr query parameter
$params = array();
parse_str(parse_url($goToUrl, PHP_URL_QUERY), $params);
$params['tokenerr'] = $retVal;

redirect(strtok($goToUrl, '?') . '?' . http_build_query($params));
?>
