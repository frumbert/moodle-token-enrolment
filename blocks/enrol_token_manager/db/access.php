<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'block/enrol_token_manager:addinstance' => array(
        'riskbitmask' => RISK_DATALOSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'manager' => CAP_ALLOW
        ),

        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ),

    'block/enrol_token_manager:createtokens' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),

    'block/enrol_token_manager:revoketokens' => array(
        'riskbitmask' => RISK_DATALOSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),

    'block/enrol_token_manager:viewtokens' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
    ),
);
