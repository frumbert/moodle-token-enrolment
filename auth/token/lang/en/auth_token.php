<?php
$string['pluginname'] = 'Token Login';
$string['auth_tokendescription'] = 'Allows a user to either login to an existing acocunt or to create a new account using a token';

$string['autologinfailure'] = 'Your account appears to have been created ok but there was a problem automatically signing you in. Please try logging in manually or use the site contact details to request help.';

$string['createaccountinstructions'] = 'Enter your email address and token code. A password will be emailed to you.';

$string['nameentryinstructions'] = 'The name you enter below will appear on your certificate when you complete a course. Please be <strong>sure</strong> to correctly enter your real names.';

$string['signupfailure'] = 'There was a problem signing you up. Please use the site contact details form to have an account created for you.';

$string['signup_tokencode'] = 'Token code';
$string['signup_email'] = 'Email';

$string['signup_registerusing'] = 'Register using a token';
$string['login_existingusers'] = 'Existing users log in';

$string['signup_tokencode_desc'] = 'Token code description';
$string['signup_missingtoken'] = 'You need to enter your token';

$string['signup_token_expired'] = 'Sorry, this token has expired';

$string['signup_passwordemailed'] = 'Will be emailed to you';

$string['signup_userregoemail'] = 'Hi {$a->firstname},

Someone (probably you) has registered a new acount on the
site \'{$a->sitename}\'. The account is ready to use, and you
can log in again using these details:

    URL: {$a->link}
    Username: {$a->username}
    Password: {$a->password}

Cheers from the \'{$a->sitename}\' administrator,
{$a->signoff}';