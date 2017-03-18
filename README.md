# moodle-token-enrolment
enrol users into moodle courses using one-time (or many-use) tokens.
Note: Assumes email-based registrations with auto-generated passwords

## possible uses
- Put a page / block behind a paywall that allows a user (such as a teacher) to generate tokens for their learners
    - Use WooCommerce or MarketPress? Try https://github.com/frumbert/wooMoodleTokenEnrolment
- Stop general registrations and limit them to only people who know the token code (sort of like a password for allowing registrations)
- Prevent your learners from having to self-enrol in courses once they are signed up (so you don't have to also train them on how that works)
- ... Profit?

## screenshots

Custom registration page
![Registration page screenshot](http://i.imgur.com/LLXLsWp.png)

Viewing Tokens
![Viewing generated tokens](http://i.imgur.com/almNlUg.png)

## demo
there's a demo that lets you quickly enrol a dummy user into a moodle site, and a list of tokens you can try.
http://wp2moodle.coursesuite.ninja/token-enrolment/.

##installation
Note: last checked with Moodle 3.1.1+ but should be fine with 2.7 or above.
extract the folders into moodle; there's a block, an auth plugin, and an enrolment plugin. You'll need to activate the plugins.

## what the plugins do
the block plugin lets admins or token managers quickly access the tools for generating and reporting on tokens

the auth plugin lets users sign up with their email and a token. it generates a password and emails that to them but logs them in directly and opens the course the token is for. You can modify the email message using language string customisation. this requires the enrol plugin to be active and that token enrolment is enabled for the course(s).

the enrol plugin is where the actual enrolment work happens, and has functionality of the course enrol screen.

the local plugin is a webservice that lets you generate tokens. Use the usual moodle webservices to generate a token for a user that has `block/enrol_token_manager:createtokens` capability (of use an admin account if you're not the paranoid type).

##setup
0. Install via site administration, then enable the plugin instances via the various Plugins sub-features (e.g. Site Admin > Plugins > Enrol > Manage enrolment plugins, etc)
1. Create a new role (or modify the Manager role) to incorporate the following capabilities:
    * block/enrol_token_manager:createtokens
    * block/enrol_token_manager:revoketokens
    * block/enrol_token_manager:viewtokens
2. add the token manager block to a page such as your site homepage (you'll only see the block if you have permission to it)
3. generate some tokens for a course.
4. in that course, add the token enrolment as an enrolment type, also enable manual enrolments.
5. as a student, enrol into the course using a token.
6. as a manager, use "view tokens" from the token managent block to verify the seat / slot is taken.

### optional steps
7. Edit your language pack to change the various strings (such as the default mailbody for generated tokens)
8. Change your default logon page to /auth/token/login.php so that users can use the tokens to register
9. Add custom profile fields to the user - they will also show up on the token logon/registration page.

## license
GPL3, same as Moodle3.
