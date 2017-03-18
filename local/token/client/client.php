<?php

const TOKEN = "ebeddeb100386cba678e2dc691620edb";
const SERVER = "http://moodle311.dev";


/* ------------------- call the function ------------------- */
function wscompletioncall($token, $functionname, $params) {
    require_once('./curl.php');
    $serverurl = SERVER . "/webservice/rest/server.php?wstoken=$token&wsfunction=$functionname&moodlewsrestformat=json";
    $resp = (new curl)->post($serverurl, $params);
    return $resp;
}

header('Content-Type: text/plain');

$params = array(
	"course" => "mycourse2",
	"seats" => 10,
	"prefix" => "free",
	"cohort" => "april2017"
);
$data = wscompletioncall(TOKEN, 'local_token_generatetokens', $params);

$obj = json_encode(json_decode($data), JSON_NUMERIC_CHECK);

print_r($obj);

// e.g. {"token":["freefbTGr","free0WgWT","freeWKqgm","freewzk2p","freehYOro","free6AvO8","freex4krK","freeLsG0o","freeAW7Zd","freetwMMx"]}