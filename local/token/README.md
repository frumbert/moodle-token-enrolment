# TOKEN

Generate one or more enrolment tokens.

Some defaults are assumed - for instance it will create a new cohort for the course if one doesn't already exist (name = "token_external_<course-id>")

## Requires

    * blocks/token_enrol_manager

## Parameters

	* **courseid** *INT* The course id you want to generate tokens for
	* **count** *INT* The amount of token you want to generate (1-500) - default 1
	* **limit** *INT* The amount of times a token can be re-used (1-500) - default 1
	* **expiry** *INT* The expiry date (must be in the future, as unix timestamp) - default 0
	* **prefix** *TEXT* Prefix tokens by this (0-4 characters) string - default empty
	* **cohort** *TEXT* name of cohort to add users to, optional default "token_external"

## Outputs

	Array - each token code generated.

	[{"token":"f67sdf67st"},{"token":"4ty32u432"},{"token":"f8s094h32jk"}]

License
=======
GPL2 - same as moodle
