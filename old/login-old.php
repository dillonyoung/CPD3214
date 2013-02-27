<?php

$username = "";
$password = "";
$error_username = "";
$error_password = "";

include_once('engine.php');

$engine = new Engine;

include('header.php');

if (isset($_POST['submit'])) {
	
	$username = $_POST['user_username'];
	$password = $_POST['user_password'];
	
	$rvalue = $engine->attemptAdminLogin($username, $password);
	if ($rvalue == Engine::USER_STATUS_VALID_LOGIN) {
		displayLoginSuccess();
		$engine->redirectPage("./");
	} else {
		$error_username = "The username and/or password are invalid.";
		displayLoginForm($username, $password, $error_username, $error_password);	
	}
} else {
	displayLoginForm($username, $password, $error_username, $error_password);	
}

function displayLoginForm($user_username, $user_password, $err_username, $err_password) {
?>
<div class="window_hold">
<div class="window">
<div class="window_header">Login</div>
<div class="window_body">
<p>Please enter your username and password into the form below. Once you are done please click on the Login button to access the administration console.</p>
<?php
if (!empty($err_username) || !empty($err_password)) {
	echo "<p class=\"error\">There were one or more errors with the below information. Please check the information and try again.</p>";	
}
?>
<br />
<form action="login.php" method="post" name="login_user">
<p><label>Username: </label><input type="text" name="user_username" value="<?php echo $user_username; ?>"><label class="error"><?php echo $err_username; ?></label></p>
<p><label>Password: </label><input type="password" name="user_password" value="<?php echo $user_password; ?>"><label class="error"><?php echo $err_password; ?></label></p>
<br />
<p class="button"><input type="submit" name="submit" value="Login"></p>
</form>
</div>
</div>
</div>
<?php
}

function displayLoginSuccess() {
?>
<div class="window_hold">
<div class="window">
<div class="window_header">Login</div>
<div class="window_body">
<p>You have been successfully logged in please wait while you are redirected to the management console.</p>
<br />
</div>
</div>
</div>
<?php	
}
include('footer.php');
?>