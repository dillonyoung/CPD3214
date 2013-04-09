<?php
	/**
	 * Description: Registers a new user with the system
	 * Filename...: register.php
	 * Author.....: Dillon Young (C0005790)
	 * 
	 */	
	
	// Set the initial status
	$status = 0;
	
	// Include the engine class
	include_once('engine.php');
	
	// Create a new instance of the engine class
	$engine = new Engine;

	// Check to ensure that the application is configured
	if ($engine->isConfigured()) {
		
		// Check to ensure that the user is not currently logged in
		if ($engine->checkUserLoggedIn() == Engine::USER_STATUS_NOT_LOGGED_IN) {
			
			// Build the registration details
			$details = array('user_firstname' => '', 'user_lastname' => '', 'user_username' => '', 'user_password1' => '', 'user_password2' => '');
			include('header.php');
			
			// Display the registration form
			displayRegistrationForm($details);
			
			// Include the footer file
			include('footer.php');
		} else {
			
			// Redirect the user back to the main page
			header('location: ./');
		}
	} else {
		
		// Redirect the user to the configuration page
		header('location: ./configure.php');
	}
	
	/**
	 * Displays the registration page to the user
	 *
	 * @param $details An array of user details
	 *
	 */	
	function displayRegistrationForm($details) {
	?>
		<br />
		<div class="window_hold">
		<div class="window">
		<div class="window_header">Register New Account</div>
		<div class="window_body">
		<p>Please fill out the below form to register for a new account. Once you are done please click on the Register button.</p>
		<form action="./register.php" method="post" name="register_user">
		<p><label>First Name: </label><input type="text" name="txt_user_firstname" id="txt_user_firstname" value="<?php echo $details['user_firstname']; ?>"><label class="error" id="err_user_firstname"></label></p>
		<p><label>Last Name: </label><input type="text" name="txt_user_lastname" id="txt_user_lastname" value="<?php echo $details['user_lastname']; ?>"><label class="error" id="err_user_lastname"></label></p>
		<p><label>Username: </label><input type="text" name="txt_user_username" id="txt_user_username" value="<?php echo $details['user_username']; ?>"><label class="error" id="err_user_username"></label></p>
		<p><label>Password: </label><input type="password" name="txt_user_password1" id="txt_user_password1" value="<?php echo $details['user_password1']; ?>"><label class="error" id="err_user_password1"></label></p>
		<p><label>Confirm Password: </label><input type="password" name="txt_user_password2" id="txt_user_password2" value="<?php echo $details['user_password2']; ?>"><label class="error" id="err_user_password2"></label></p>
		<p><img src="./captcha.php" id="captcha_image"/><button id="btn_register_newcaptcha">Refresh</button></p>
		<p><label>Captcha Code: </label><input type="text" name="txt_user_captcha" id="txt_user_captcha" value=""><label class="error" id="err_user_captcha"></label></p>

		<br />
		<p><button id="btn_register_register">Register</button>&nbsp;&nbsp;&nbsp;<button id="btn_register_cancel">Cancel</button></p>
		</form>
		</div>
		</div>
		</div>
	<?php	
	}
?>