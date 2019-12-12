<?php

$string['createtokens'] = 'Create Tokens';

$string['enrol_token_manager:addinstance'] = 'Add a new login block';
$string['enrol_token_manager:createtokens'] = 'Create Enrolment Tokens';
$string['enrol_token_manager:revoketokens'] = 'Revoke Enrolment Tokens';
$string['enrol_token_manager:viewtokens'] = 'View Enrolment Tokens';

$string['linkTextCreateTokens'] = 'Create new enrolment tokens';
$string['linkTextRevokeTokens'] = 'Revoke enrolment tokens';
$string['linkTextViewTokens'] = 'View enrolment token details';
$string['view_token_usage'] = 'View token usage';
$string['nopagepermission'] = 'You do not have suitable persmissions to view the page';

$string['noticetext'] = '<p>Hello,</p>' .
                        '<p>Please find below {$a->tokennumber} token code{$a->tokennumberplural} that can be used to enrol into the <strong>{$a->coursename}</strong> course.</p>' .
                        '<p>Simply go to <a href="{$a->wwwroot}">{$a->wwwroot}</a> and use your token to enrol into and complete your course.</p>' .
                        '<p>Your Coupon Code{$a->tokennumberplural}: <strong>{$a->tokens}</strong></p>' .
                        '<p>Each token is valid for {$a->seatspertoken} enrolment{$a->seatspertokenplural} into the course.</p>' .
                        '<p>Regards,<br>' .
                        '{$a->adminsignoff}</p>';

$string['pageNameCreateTokens'] = 'Create Enrolment Tokens';
$string['pageNameViewRevokeTokens'] = 'View and Revoke Enrolment Tokens';
$string['pluginname'] = 'Enrol Token Manager';

// $string['prefixinstructions'] = '<p>If you\'d like the tokens to all start with the same thing then please enter a some text here, or else leave this blank to generate fully random tokens.</p>';


$string['promptcourse'] = 'Generate tokens for this course';
$string['promptcourse_help'] = 'The course you select must have the <em>manual enrolment</em> method enabled';

$string['cohort_selection'] = 'Cohort container';
$string['cohortor'] = 'OR';
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
$string['tokeninstructions_help'] = 'You can use &#42; to search for any characters (e.g. <em>token&#42;</em> will match token1, token12, token_bob, etc), and "?" to represent a single unknown character (e.g. <em>to?en</em> will match token, tojen, to6en, etc). You can use both together (e.g. <em>to?en*</em>)';
$string['promptfiltertoken_help'] = 'You can use <b>&#42;</b> to search for any characters (e.g. <em>token&#42;</em> will match token1, token12, token_bob, etc), and <b>?</b> to represent a single unknown character (e.g. <em>to?en</em> will match token, tojen, to6en, etc). You can use both together (e.g. <em>to?en*</em>)';

$string['promptfiltercohort'] = 'Cohort (group)';
$string['promptfiltercourse'] = 'Course';

$string['promptprefix'] = 'Short token prefix';
$string['promptprefix_help'] = 'Generated tokens will begin with the text you enter here (default: blank)';

$string['promptseats'] = 'Enrolments for each Token';
$string['promptseats_help'] = 'How many times each token can be used for an enrolment (suggested: 1; must be more than 0 and less than 501)';

$string['prompttokennum'] = 'Number of tokens to produce';
$string['prompttokennum_help'] = 'Enter the number of tokens you want to generate using these details (example: 10; must be more than 0 and less than 501)';

$string['revoketokens'] = 'Revoke Selected Tokens';
$string['seatsoutofrange'] = 'The number of enrolments per Token cannnot be zero, negative or larger than 10000';
$string['tokensoutofrange'] = 'The number of Tokens to produce cannnot be zero, negative or larger than 10000';
$string['viewtokens'] = 'View Tokens';
$string['viewusers'] = 'View Users';

$string['token_delete_error'] = 'There was a problem revoking the tokens - please double-check that they have in fact been deleted by re-searching for them';

$string['emailaddressprompt'] = 'Send tokens to this email';

$string['mailsubject'] = 'Mail subject';
$string['mailsubject_help'] = 'Subject of email to be sent after tokens are generated';

$string['mailbody'] = 'Mail body';
$string['mailbody_help'] = '<p>Body of email to be sent after tokens are generated. You can use the following merge fields:</p>
*{coursename}* - the full name of the course

*{tokennumber}* - the number of tokens that were generated

*{tokennumberplural}* - the letter "s" if there is more than one token

*{seatspertoken}* - the number of seats that were generated

*{seatspertokenplural}* - the letter "s" if there is more than one seat

*{wwwroot}* - the url of this server up until (but not including) the trailing slash (e.g. http://foo.com but not http://foo.com/ )

*{tokens}* - a comma seperated list of the tokens (or a single token value, if there is only one)

*{adminsignoff}* - the site-wide standard email sign-off (configured elsewhere)

*{emailaddress}* - the email address you are sending to

HTML / Markdown is ok.
';

$string['mailsubject_default'] = 'Your registration token(s) for Moodle';
$string['mailbody_default'] = 'Hello,

Please find below {tokennumber} token(s) that can be used to enrol onto Moodle.

{tokens}

Cheers,
{adminsignoff}';