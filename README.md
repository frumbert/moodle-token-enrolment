# moodle-token-enrolment
enrol users into moodle courses using one-time (or many-use) tokens.

## demo
there's a demo that lets you quickly enrol a dummy user into a moodle site, and a list of tokens you can try.
http://wp2moodle.coursesuite.ninja/token-enrolment/

##installation

extract the folders into moodle; there's a block, and an enrolment plugin. You'll need to activate the plugins.

##setup

1. Create a new role (or modify the Manager role) to incorporate the following capabilities:
    * block/enrol_token_manager:createtokens
    * block/enrol_token_manager:revoketokens
    * block/enrol_token_manager:viewtokens
2. add the token manager block to a page (you'll only see the block if you have permission to it)
3. generate some tokens for a course.
4. in that course, add the token enrolment as an enrolment type, also enable manual enrolments.
5. as a student, enrol into the course using a token.
6. as a manager, use "view tokens" from the token managent block to verify the seat / slot is taken.

## license
GPL2, same as Moodle3.
