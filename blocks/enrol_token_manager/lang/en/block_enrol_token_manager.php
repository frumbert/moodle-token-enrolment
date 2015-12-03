<?php

// $string['cohortinstructions'] = '<p>A \'cohort\' is a way of grouping people who use tokens. You can create any grouping types that you\'d like.</p>' .
//                                '<p>For example, you may create a cohort such as \'BHP\', \'Main Office,\' or \'Give-aways.\'</p>';


$string['createtokens'] = 'Create Tokens';
$string['emailaddressprompt'] = 'Email address to send the tokens to';
$string['emailsubject'] = 'Pre-paid Coupons for your online learning';

$string['enrol_token_manager:addinstance'] = 'Add a new login block';
$string['enrol_token_manager:createtokens'] = 'Create Enrolment Tokens';
$string['enrol_token_manager:revoketokens'] = 'Revoke Enrolment Tokens';
$string['enrol_token_manager:viewtokens'] = 'View Enrolment Tokens';

$string['linkTextCreateTokens'] = 'Create new enrolment tokens';
$string['linkTextRevokeTokens'] = 'Revoke enrolment tokens';
$string['linkTextViewTokens'] = 'View enrolment token details';
$string['nopagepermission'] = 'You do not have suitable persmissions to view the page';

$string['noticetext'] = '<p>Hello,</p>' .
                        '<p>Please find below {$a->tokennumber} Pre-Paid Coupon Code{$a->tokennumberplural} that can be used to enrol into the <strong>{$a->coursename}</strong> on-line course.</p>' .
                        '<p>Simply go to <a href="{$a->wwwroot}">{$a->wwwroot}</a> and use your Coupon Code to enrol into and complete your course.</p>' .
                        '<p>Your Coupon Code{$a->tokennumberplural}: <strong>{$a->tokens}</strong></p>' .
                        '<p>PS. Each Coupon Code is valid for {$a->seatspertoken} enrolment{$a->seatspertokenplural} into the course.</p>' .
                        '<p>Regards,<br>' .
                        '{$a->adminsignoff}</p>';

$string['pageNameCreateTokens'] = 'Create Enrolment Tokens';
$string['pageNameViewRevokeTokens'] = 'View and Revoke Enrolment Tokens';
$string['pluginname'] = 'Enrol Token Manager';

// $string['prefixinstructions'] = '<p>If you\'d like the tokens to all start with the same thing then please enter a some text here, or else leave this blank to generate fully random tokens.</p>';


$string['promptcourse'] = 'Generate tokens for this course';
$string['promptcourse_help'] = 'The course you select must have the <em>manual enrolment</em> method enabled';

$string['cohort_selection'] = 'Cohort container';
$string['cohortor_help'] = 'Users who enrol using a token generated on this page will be added to a Cohort; you can choose an existing cohort, or create a new one.';

$string['token_generation'] = 'Token generation';
$string['cohort_view_filter'] = 'Filters';

$string['promptcohortexisting'] = 'Add token users to this existing cohort';
$string['promptcohortnew'] = 'Create new cohort named';

$string['promptfiltercreatedby'] = 'Created by';
$string['promptfiltercreationdatemin'] = 'Created from';
$string['promptfiltercreationdatemax'] = 'to';
$string['promptfilterexpirydatemin'] = 'Expires from';
$string['promptfilterexpirydatemax'] = 'to';

$string['promptexpirydate'] = 'Token expiry date (if enabled).';

$string['promptfiltertoken'] = 'Token code';
$string['tokeninstructions_help'] = 'You can use \'*\' to search for any characters (e.g. <em>token*</em> will match token1, token12, token_bob, etc), and \'?\' to represent a single unknown character (e.g. <em>to?en</em> will match token, tojen, to6en, etc). You can use both together (e.g. <em>to?en*</em>)';

$string['promptfiltercohort'] = 'Cohort (group)';
$string['promptfiltercourse'] = 'Course';

$string['promptprefix'] = 'Short token prefix';
$string['promptprefix_help'] = 'Generated tokens will begin with the text you enter here (default: blank)';

$string['promptseats'] = 'Enrolments for each Token';
$string['promptseats_help'] = 'How many times each token can be used for an enrolment (suggested: 1; must be more than 0 and less than 201)';

$string['prompttokennum'] = 'Number of tokens to produce';
$string['prompttokennum_help'] = 'Enter the number of tokens you want to generate using these details (example: 10; must be more than 0 and less than 501)';

$string['revoketokens'] = 'Revoke Selected Tokens';
$string['seatsoutofrange'] = 'The number of enrolments per Token cannnot be negative or larger than 200';
$string['tokensoutofrange'] = 'The number of Tokens to produce cannnot be negative or larger than 500';
$string['viewtokens'] = 'View Tokens';

$string['token_delete_error'] = 'There was a problem revoking the tokens - please double-check that they have in fact been deleted by re-searching for them';