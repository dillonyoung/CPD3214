
<?php
	include_once('engine.php');

	$engine = new Engine;
	
	include('header-configure.php');

	// Declare variables
	$database_host = "localhost";
	$database_username = "";
	$database_password = "";
	$database_name = "";
	$user_username = "admin";
	$user_password1 = "";
	$user_password2 = "";
	$error_host = "";
	$error_username = "";
	$error_password = "";
	$error_name = "";
	$error_password1 = "";
	$error_password2 = "";

	// Check to see if the database configuration file exists
	if (file_exists("database.config")) {
	
		// Check to see if the page has been submitted and the section is for configuring the user
		if (isset($_POST['submit']) && isset($_GET['action']) && $_GET['action'] == 'configuser') {
		
			// Get the values entered by the user
			$user_username = $_POST['user_username'];
			$user_password1 = $_POST['user_password1'];
			$user_password2 = $_POST['user_password2'];
		
			// Check to see if any of the values are blank
			if (empty($user_username)) { $error_username = "The username can not be blank"; }
			if (empty($user_password1)) { $error_password1 = "The password can not be blank"; }
			if (empty($user_password2)) { $error_password2 = "The password can not be blank"; }
			
			if (empty($error_username) && empty($error_password1) && empty($error_password2)) {
			
				// Check to see if the passwords match
				if ($user_password1 != $user_password2) {
					$error_password1 = "Passwords entered do not match";
					displayAdminForm($user_username, $user_password1, $user_password2, $error_username, $error_password1, $error_password2);
				} else {
					$engine->addUser($user_username, $user_password1, Engine::USER_ACCOUNT_TYPE_ADMIN);
					displayFinished();
				}
			} else {
				displayAdminForm($user_username, $user_password1, $user_password2, $error_username, $error_password1, $error_password2);	
			} 
		} else {
			displayAdminForm($user_username, $user_password1, $user_password2, $error_username, $error_password1, $error_password2);
		}
	} else {
	
		// Check to see if the page has been submitted and the section is for configuring the database
		if (isset($_POST['submit']) && isset($_GET['action']) && $_GET['action'] == 'configdatabase') {
		
			// Get the values entered by the user
			$database_host = $_POST['database_host'];
			$database_username = $_POST['database_username'];
			$database_password = $_POST['database_password'];
			$database_name = $_POST['database_name'];
		
			// Check to see if any of the values are blank
			if (empty($database_host)) { $error_host = "The database host can not be blank"; }
			if (empty($database_username)) { $error_username = "The database username can not be blank"; }
			if (empty($database_password)) { $error_password = "The database password can not be blank"; }
			if (empty($database_name)) { $error_name = "The database name can not be blank"; }
			
			if (empty($error_host) && empty($error_username) && empty($error_password) && empty($error_name)) {
			
				// Update the database configuration and check to see if it is valid
				$engine->updateDatabaseConfig($database_host, $database_username, $database_password, $database_name);

				$rvalue = $engine->testDatabaseConnection();

				if ($rvalue == Engine::DATABASE_ERROR_INVALID_USERNAME_PASSWORD) {
					$error_username = "Could not connect with the supplied username";
					$error_password = "Could not connect with the supplied password";
					displayDatabaseForm($database_host, $database_username, $database_password, $database_name, $error_host, $error_username, $error_password, $error_name);
				} elseif ($rvalue == Engine::DATABASE_ERROR_NO_DATABASE) {
					$error_name = "Selected database does not exist";
					displayDatabaseForm($database_host, $database_username, $database_password, $database_name, $error_host, $error_username, $error_password, $error_name);
				} elseif ($rvalue == Engine::DATABASE_ERROR_COULD_NOT_SAVE_CONFIG) {
					$error_name = "Could not save database configuration";
					displayDatabaseForm($database_host, $database_username, $database_password, $database_name, $error_host, $error_username, $error_password, $error_name);
				} elseif ($rvalue == Engine::DATABASE_ERROR_COULD_NOT_CREATE_DATABASE) {
					$error_name = "Could not create database tables";
					displayDatabaseForm($database_host, $database_username, $database_password, $database_name, $error_host, $error_username, $error_password, $error_name);				
				} else {
					displayAdminForm($user_username, $user_password1, $user_password2, $error_username, $error_password1, $error_password2);
				}
			} else {
				displayDatabaseForm($database_host, $database_username, $database_password, $database_name, $error_host, $error_username, $error_password, $error_name);
			}
		} else {
			displayDatabaseForm($database_host, $database_username, $database_password, $database_name);
		}
	}

	/**
	 * Displays the database configuration form to the user
	 *
	 * @param mixed $db_host The database host name
	 * @param mixed $db_username The database username
	 * @param mixed $db_password The database password
	 * @param mixed $db_name The database name
	 * @param mixed $err_host The error description for the host name
	 * @param mixed $err_username The error description for the username
	 * @param mixed $err_password The error description for the password
	 * @param mixed $err_name The error description for the database name
	 * @return mixed Nothing
	 *
	 */
	function displayDatabaseForm($db_host, $db_username, $db_password, $db_name, $err_host = "", $err_username = "", $err_password = "", $err_name = "") {
?>
<br />
<div class="window_hold">
<div class="window">
<div class="window_header">Configure Database (Step 1 of 3)</div>
<div class="window_body">
<p>Please enter the below details for your MySQL database. Once you have finished entering the database details please click on the continue button.</p>
<?php
	if (!empty($err_host) || !empty($err_username) || !empty($err_password) || !empty($err_name)) {
		echo "<p class=\"error\">There were one or more errors with the below information. Please check the information and try again.</p>";	
	}
?>
<form action="?action=configdatabase" method="post" name="database_configure">
<p><label>Database Host: </label><input type="text" name="database_host" value="<?php echo $db_host; ?>"><label class="error"><?php echo $err_host; ?></label></p>
<p><label>Database Username: </label><input type="text" name="database_username" value="<?php echo $db_username; ?>"><label class="error"><?php echo $err_username; ?></label></p>
<p><label>Database Password: </label><input type="password" name="database_password" value="<?php echo $db_password; ?>"><label class="error"><?php echo $err_password; ?></label></p>
<p><label>Database Name: </label><input type="text" name="database_name" value="<?php echo $db_name; ?>"><label class="error"><?php echo $err_name; ?></label></p>
<br />
<p><input type="submit" name="submit" value="Continue"></p>
</form>
</div>
</div>
</div>
<?php
	}

	/**
	 * Displays the administrator configuration form to the user
	 *
	 * @param mixed $user_username The username for the administrator
	 * @param mixed $user_password1 The first password for the administrator
	 * @param mixed $user_password2 The second password for the administrator
	 * @param mixed $err_username The error description for the administrator username
	 * @param mixed $err_password1 The error description for the administrator password1
	 * @param mixed $err_password2 The error description for the administrator password2
	 * @return mixed Nothing
	 *
	 */
	function displayAdminForm($user_username, $user_password1, $user_password2, $err_username, $err_password1, $err_password2) {
?>
<br />
<div class="window_hold">
<div class="window">
<div class="window_header">Configure Administrator Account (Step 2 of 3)</div>
<div class="window_body">
<p>Please enter an username and password to be used for the administrator account. Once you have finished entering the user details please click on the continue button.</p>
<?php
	if (!empty($err_username) || !empty($err_password1) || !empty($err_password2)) {
		echo "<p class=\"error\">There were one or more errors with the below information. Please check the information and try again.</p>";	
	}
?>
<form action="?action=configuser" method="post" name="user_configure">
<p><label>Username: </label><input type="text" name="user_username" value="<?php echo $user_username; ?>"><label class="error"><?php echo $err_username; ?></label></p>
<p><label>Password: </label><input type="password" name="user_password1" value="<?php echo $user_password1; ?>"><label class="error"><?php echo $err_password1; ?></label></p>
<p><label>Confirm Password: </label><input type="password" name="user_password2" value="<?php echo $user_password2; ?>"><label class="error"><?php echo $err_password2; ?></label></p>
<br />
<p><input type="submit" name="submit" value="Continue"></p>
</form>
</div>
</div>
</div>
<?php	
	}

	/**
	 * Displays the finished configuring the system screen to the user
	 *
	 * @return mixed Nothing
	 *
	 */
	function displayFinished() {
?><br />
<div class="window_hold">
<div class="window">
<div class="window_header">Configuration Finished (Step 3 of 3)</div>
<div class="window_body">
<p>The system has been successfully configured. Please click below to access the site.</p>
<br /><br />
<p><a href="./">Click here to access the site</a></p>
<br />
</div>
</div>
</div>
<?php	
	}

	include('footer.php');
?>