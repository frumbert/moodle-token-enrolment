<?php

$functions = array(
        'local_token_generatetokens' => array(
                'classname'     => 'local_token_external',
                'methodname' => 'generatetokens',
                'classpath'       => 'local/token/externallib.php',
                'description'     => 'Generate one or more enrolment tokens for a course',
                'type'               => 'read',
        )
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'Generate one or more enrolment tokens for a course' => array(
                'functions' => array ('local_token_generatetokens'),
                'restrictedusers' => 0,
                'enabled'=>1,
        )
);
