<?php
$columns = ''; //  ($showLogon) ? 'twocolumns' : '';

// $pluginconfig = get_config('auth_token');
// $legacyconfig = get_config('auth/token');
// $config = (object)array_merge((array)$legacyconfig, (array)$config);

if (!empty($CFG->loginpasswordautocomplete)) {
		$autocomplete = 'autocomplete="off"';
} else {
		$autocomplete = '';
}
if (empty($CFG->authloginviaemail)) {
	$strusername = get_string('username');
} else {
	$strusername = get_string('usernameemail');
}

$frmusername = false;
if (isset($frm) and isset($frm->username)) {
	$frmusername = $frm->username;
}

if (isset($authplugin->config->logonpage_intro) && !empty($authplugin->config->logonpage_intro)) {
?>
<div class="loginbox clearfix token-help">
	<p><?php echo $authplugin->config->logonpage_intro; ?></p>
</div>
<?php } ?>
<div class="loginbox clearfix <?php echo $columns ?>">
	<div class="signuppanel">
		<h2><?php print_string("signup_registerusing","auth_token") ?></h2>
		<div class="subcontent">
		<?php $mform_signup->display(); ?>
		</div>
	</div>
	<div class="loginpanel">
		<h2><?php print_string("login_existingusers","auth_token") ?></h2>
			<div class="subcontent loginsub">
				<?php
					if (!empty($errormsg)) {
							echo html_writer::start_tag('div', array('class' => 'loginerrors'));
							echo html_writer::link('#', $errormsg, array('id' => 'loginerrormessage', 'class' => 'accesshide'));
							echo $OUTPUT->error_text($errormsg);
							echo html_writer::end_tag('div');
					}
				?>
				<form action="<?php echo $CFG->httpswwwroot; ?>/login/index.php" method="post" id="login" <?php echo $autocomplete; ?> class="mform">

				<fieldset class="hidden">

						<div class="fitem fitem_ftext">
							<div class="fitemtitle"><label for="username"><?php echo($strusername) ?></label></div>
							<div class="felement ftext"><input type="text" name="username" id="username" size="30" value="<?php if ($frmusername) p($frmusername) ?>" /></div>
						</div>

						<div class="clearer"><!-- --></div>

						<div class="fitem fitem_ftext">
							<div class="fitemtitle"><label for="password"><?php print_string("password") ?></label></div>
							<div class="felement ftext"><input type="password" name="password" id="password" size="30" value="" <?php echo $autocomplete; ?> /></div>
						</div>

							<?php if (isset($CFG->rememberusername) and $CFG->rememberusername == 2) { ?>
						<div class="clearer"><!-- --></div>

						<div class="fitem fitem_ftext">
							<div class="fitemtitle"><label>&nbsp;</label></div>
							<div class="felement fcheckbox">
									<input type="checkbox" name="rememberusername" id="rememberusername" value="1" <?php if ($frmusername) {echo 'checked="checked"';} ?> />
									<label for="rememberusername"><?php print_string('rememberusername', 'admin') ?></label>
							</div>
						</div>
							<?php } ?>

					</fieldset>
					<fieldset class="hidden">

						<div id="fitem_id_submitbutton" class="fitem fitem_actionbuttons fitem_fsubmit">
							<div class="felement fsubmit"><input type="submit" id="loginbtn" value="<?php print_string("login") ?>" /></div>
						</div>

						<div class="fitem fitem_ftext">
							<div class="fitemtitle"><label>&nbsp;</label></div>
							<div class="felement fstatic"><a href="/login/forgot_password.php"><?php print_string("forgotten") ?></a></div>
						</div>

						<div class="fdescription required">
								<?php
										echo get_string("cookiesenabled");
										echo $OUTPUT->help_icon('cookiesenabled');
								?>
						</div>
						</fieldset>
				</form>

		 </div>
<?php if (!empty($potentialidps)) { ?>
		<div class="subcontent potentialidps">
				<h6><?php print_string('potentialidps', 'auth'); ?></h6>
				<div class="potentialidplist">
<?php foreach ($potentialidps as $idp) {
		echo  '<div class="potentialidp"><a href="' . $idp['url']->out() . '" title="' . $idp['name'] . '">' . $OUTPUT->render($idp['icon'], $idp['name']) . $idp['name'] . '</a></div>';
} ?>
				</div>
		</div>
<?php } ?>
</div>
