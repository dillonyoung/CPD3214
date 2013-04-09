<?php
	/**
	 * Description: The main engine file for the Simple CMS project
	 * Filename...: engine.php
	 * Author.....: Dillon Young (C0005790)
	 * 
	 */	
	class Engine {
	
		// Declare constants
		const ENGINE_VERSION = "1.13.0311";
		const DATABASE_ERROR_NO_ERROR = 0;
		const DATABASE_ERROR_INVALID_USERNAME_PASSWORD = 1;
		const DATABASE_ERROR_COULD_NOT_CREATE_DATABASE = 2;
		const DATABASE_ERROR_COULD_NOT_SAVE_CONFIG = 3;
		const DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE = 4;
		const DATABASE_ERROR_COULD_NOT_CLOSE_CONNECTION = 5;
		const DATABASE_ERROR_QUERY_ERROR = 6;
		const DATABASE_ERROR_NO_QUERY_RESULTS = 7;
		const DATABASE_ERROR_NO_DATABASE = 8;
		const DATABASE_ERROR_USER_EXISTS = 9;
		const USER_ACCOUNT_TYPE_ADMIN = 3;
		const USER_ACCOUNT_TYPE_MODERATOR = 2;
		const USER_ACCOUNT_TYPE_NORMAL = 1;
		const USER_ACCOUNT_STATUS_UNLOCKED = 1;
		const USER_ACCOUNT_STATUS_LOCKED = 2;
		const USER_STATUS_NOT_LOGGED_IN = 31;
		const USER_STATUS_LOGGED_IN = 32;
		const USER_STATUS_VALID_LOGIN = 33;
		const USER_STATUS_INVALID_LOGIN = 34;
		const USER_STATUS_HAS_BEEN_LOGGED_OUT = 35;
		const USER_STATUS_NOT_AUTORIZED = 36;
		const USER_STATUS_ACCOUNT_LOCKED = 37;
		const POST_NO_TYPE_CONFIGURED = 40;
		const POST_NOT_EXISTS = 50;
		const CAPTCHA_NO_MATCH = 70;
		const NO_ERROR_STATUS = 0;
		
		// Declare feature constants
		const FEATURE_SUPPORT_DATABASE = 2;
		const FEATURE_SUPPORT_TEXT_POST = 4;
		const FEATURE_SUPPORT_IMAGE_POST = 8;
		const FEATURE_SUPPORT_YOUTUBE_POST = 16;
		const FEATURE_SUPPORT_FILE_UPLOAD = 32;
		const FEATURE_SUPPORT_CAPTCHA = 2048;
	
		// Declare variables
		private $modules = array();
		private $database_module;
		private $textpost_module;
		private $imagepost_module;
		private $captcha_module;
		private $fileupload_module;

		/**
		 * The constructor for the engine
		 *
		 */	
		public function __construct() {
		
			// Turn on error reporting
			ini_set('display_errors', 1); 
			error_reporting(E_ALL);
		
			// Start a session and initialize the database connection
			session_start();
			$_SESSION['running'] = true;
			$this->database_connection = null;

			// Load any installed modules
			$this->loadModules();
			
			// Check for modules which support required features
			$this->database_module = -1;
			$this->textpost_module = -1;
			$this->imagepost_module = -1;
			$this->captcha_module = -1;
			$this->fileupload_module = -1;
			
			// Loop through the modules and find required modules
			for ($i = 0; $i < count($this->modules); $i++) {
				
				// Check to ensure the module is not null
				if ($this->modules[$i] != null) {
					
					// Check to see if the module supports database features
					if ($this->modules[$i]->getFeatures() == Engine::FEATURE_SUPPORT_DATABASE) {
						$this->database_module = $i;
					}
					
					// Check to see if the module supports text post features
					if ($this->modules[$i]->getFeatures() == Engine::FEATURE_SUPPORT_TEXT_POST) {
						$this->textpost_module = $i;	
					}
					
					// Check to see if the module supports image post features
					if ($this->modules[$i]->getFeatures() == Engine::FEATURE_SUPPORT_IMAGE_POST) {
						$this->imagepost_module = $i;	
					}
					
					// Check to see if the module supports captcha features
					if ($this->modules[$i]->getFeatures() == Engine::FEATURE_SUPPORT_CAPTCHA) {
						$this->captcha_module = $i;	
					}
					
					// Check to see if the module supports file upload features
					if ($this->modules[$i]->getFeatures() == Engine::FEATURE_SUPPORT_FILE_UPLOAD) {
						$this->fileupload_module = $i;	
					}
				}
			}

			// Check to see if a database module is installed
			if ($this->database_module == -1) {
				die("No database module installed!");
			} else {
				
				// Update the database connection with the connection from the module
				$this->database_connection = $this->modules[$this->database_module]->getDatabaseConnection();
			}
			
			// Check to see if a text post module is installed
			if ($this->textpost_module == -1) {
				die("No text post module installed");	
			} else {
				
				// Update the database reference for the module
				$this->modules[$this->textpost_module]->setDatabaseModule($this->modules[$this->database_module]);
			}
			
			// Check to see if a image post module is installed
			if ($this->imagepost_module == -1) {
				die("No image post module installed");	
			} else {
				
				// Update the database reference for the module
				$this->modules[$this->imagepost_module]->setDatabaseModule($this->modules[$this->database_module]);
			}
			
			// Check to see if a captcha module is installed
			if ($this->captcha_module == -1) {
				die("No captcha module installed");	
			} else {
				
				// Update the database reference for the module
				$this->modules[$this->captcha_module]->setDatabaseModule($this->modules[$this->database_module]);
			}
			
			// Check to see if a file upload module is installed
			if ($this->fileupload_module == -1) {
				die("No file upload module installed");	
			} else {
				
				// Update the database reference for the module
				$this->modules[$this->fileupload_module]->setDatabaseModule($this->modules[$this->database_module]);
			}
		}
	
		/**
		 * The destructor for the engine
		 *
		 */	
		public function __destruct() {
			
		}
		
		/**
		 * Determines if a selected module type is currently installed
		 *
		 * @param $type The type of module to be checked
		 * @return Returns true or false as to whether the selected module is installed
		 *
		 */		
		private function isModuleInstalled($type) {
			
			// Set the initial value
			$rvalue = false;
			
			// Check to see which module type has been selected
			switch ($type) {
				case Engine::FEATURE_SUPPORT_DATABASE:
				
					// Check to ensure that the module is installed
					if ($this->database_module != -1) {
						$rvalue = true;
					}
					break;
				case Engine::FEATURE_SUPPORT_TEXT_POST:
				
					// Check to ensure that the module is installed
					if ($this->textpost_module != -1) {
						$rvalue = true;
					}
					break;
				case Engine::FEATURE_SUPPORT_IMAGE_POST:
					
					// Check to ensure that the module is installed
					if ($this->imagepost_module != -1) {
						$rvalue = true;
					}
					break;
				case Engine::FEATURE_SUPPORT_CAPTCHA:
				
					// Check to ensure that the module is installed
					if ($this->captcha_module != -1) {
						$rvalue = true;
					}
					break;
				case Engine::FEATURE_SUPPORT_FILE_UPLOAD:
					
					// Check to ensure that the module is installed
					if ($this->fileupload_module != -1) {
						$rvalue = true;
					}
					break;
					
			}	
			
			// Return the result
			return $rvalue;
		}
	
		/**
		 * Updates the database configuration information
		 *
		 * @param $db_host The database host name
		 * @param $db_username The database username
		 * @param $db_password The database password
		 * @param $db_name The name of the database
		 * @return Returns the status of updating the configuration
		 *
		 */		
		public function updateDatabaseConfig($db_host, $db_username, $db_password, $db_name) {
			
			// Set the initial status
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to ensure that a database module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_DATABASE)) {
				
				// Attempt to update the database configuration
				$rvalue = $this->modules[$this->database_module]->updateConfiguration($db_host, $db_username, $db_password, $db_name);	
			}
			return $rvalue;
		}
		
		/**
		 * Tests the current database configuration
		 *
		 * @return Returns the status code for the test
		 *
		 */		
		public function testDatabaseConnection() {
			
			// Set the initial status
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to ensure that a database module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_DATABASE)) {
				
				// Test the database connection
				$rvalue = $this->modules[$this->database_module]->testConnection();		
			}
			
			// Check to ensure an error did not happen
			if ($rvalue == Engine::DATABASE_ERROR_NO_ERROR) {
				$this->createDatabaseTables();	
			}
			
			// Return the result
			return $rvalue;
		}
	
		/**
		 * Create the database tables
		 *
		 */		
		private function createDatabaseTables() {
			
			// Check to see if the comments table exists and if so drop it
			if ($this->modules[$this->database_module]->queryDatabase("DESC scms_comments;")) {
				$this->modules[$this->database_module]->queryDatabase("DROP TABLE scms_comments;");
			}
			
			// Check to see if the posts table exists and if so drop it
			if ($this->modules[$this->database_module]->queryDatabase("DESC scms_posts;")) {
				$this->modules[$this->database_module]->queryDatabase("DROP TABLE scms_posts;");
			}
			
			// Check to see if the categories table exists and if so drop it
			if ($this->modules[$this->database_module]->queryDatabase("DESC scms_categories;")) {
				$this->modules[$this->database_module]->queryDatabase("DROP TABLE scms_categories;");
			}
			
			// Check to see if the accounts table exists and if so drop it
			if ($this->modules[$this->database_module]->queryDatabase("DESC scms_accounts;")) {
				$this->modules[$this->database_module]->queryDatabase("DROP TABLE scms_accounts;");
			}
			
			// Check to see if the files table exists and if so drop it
			if ($this->modules[$this->database_module]->queryDatabase("DESC scms_files;")) {
				$this->modules[$this->database_module]->queryDatabase("DROP TABLE scms_files;");	
			}
			
			// Create the database tables
			$this->modules[$this->database_module]->queryDatabase("CREATE TABLE scms_accounts (id BIGINT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), username VARCHAR(50) NOT NULL, UNIQUE (username), password VARCHAR(50) NOT NULL, email VARCHAR(100), firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50), accesslevel INT NOT NULL, dateregistered DATETIME NOT NULL DEFAULT NOW(), accountstatus INT NOT NULL);");
			$this->modules[$this->database_module]->queryDatabase("CREATE TABLE scms_categories (id BIGINT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), name VARCHAR(200) NOT NULL, UNIQUE (name));");
			$this->modules[$this->database_module]->queryDatabase("CREATE TABLE scms_posts (id BIGINT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), title VARCHAR(200) NOT NULL, details TEXT NOT NULL, filename TEXT NOT NULL, dateposted DATETIME NOT NULL DEFAULT NOW(), author BIGINT NOT NULL, FOREIGN KEY (author) REFERENCES scms_accounts(id), type INT NOT NULL, category BIGINT NOT NULL, FOREIGN KEY (category) REFERENCES scms_categories(id));");
			$this->modules[$this->database_module]->queryDatabase("CREATE TABLE scms_comments (id BIGINT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), post BIGINT NOT NULL, FOREIGN KEY (post) REFERENCES scms_posts(id), dateposted DATETIME NOT NULL DEFAULT NOW(), author BIGINT NOT NULL, FOREIGN KEY (author) REFERENCES scms_accounts(id), comment TEXT NOT NULL);");
			$this->modules[$this->database_module]->queryDatabase("CREATE TABLE scms_files (id BIGINT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), filename VARCHAR(200) NOT NULL, filedata MEDIUMBLOB NOT NULL, filetype VARCHAR(200) NOT NULL);");
			
		}
		
		/**
		 * Check to see if the application is currently configured
		 *
		 * @return Returns the status of if the application is configured
		 *
		 */		
		public function isConfigured() {
			
			// Set the initial value
			$rvalue = false;
			
			// Check to see if a database connection is available
			if ($this->database_connection) {
				
				// Check to see if an admin user exists
				if ($this->checkIfAdminUserExists() == Engine::DATABASE_ERROR_NO_ERROR) {
					$rvalue = true;	
				}
			}
			
			// Return the results
			return $rvalue;
		}
		
		/**
		 * Check to see if the current user is an administrator
		 *
		 * @return Returns the status of the users admin access
		 *
		 */		
		public function isUserAdmin() {
			
			// Set initial value
			$rvalue = false;
			
			// Check to see if the access level is set
			if (isset($_SESSION['accesslevel'])) {
				
				// Check to see if the user is an administrator
				if ($_SESSION['accesslevel'] == Engine::USER_ACCOUNT_TYPE_ADMIN) {
					$rvalue = true;	
				}
			}
			
			// Return the status
			return $rvalue;	
		}
	
		/**
		 * Sets any required cookies for the application
		 *
		 */		
		public function setCookies() {
		
		}
	
		/**
		 * Gets the current page title
		 *
		 */		
		public function getPageTitle() {
			echo "Simple CMS";	
		}
	
		/**
		 * Gets the current site title
		 *
		 */		
		public function getSiteTitle() {
			echo "Simple CMS";	
		}
	
		/**
		 * Gets the current site description
		 *
		 */		
		public function getSiteDescription() {
			echo "A simple CMS system";	
		}
	
		/**
		 * Gets the current engine information
		 *
		 */		
		public function getEngineInformation() {
			echo "Powered by Simple CMS (".Engine::ENGINE_VERSION.")";
		}
	
		/**
		 * Gets the first name for the current user
		 *
		 * @return Returns either the first name of the user or the error status code
		 *
		 */		
		public function getUserFirstName() {
			
			// Check to see if the user is currently logged in
			$username = "";
			if (isset($_SESSION['username'])) {
				$username = $_SESSION['username'];
			}
			
			// Set the initial status
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to ensure that a database module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_DATABASE)) {
				
				// Query the database to get the first name
				$result = $this->modules[$this->database_module]->queryDatabase("SELECT firstname FROM scms_accounts WHERE username = '".$username."';");
			}
			
			// Check to see if there are results
			if (count($result) > 0) {
				
				// Loop through the results and get the first name
				foreach ($result as $resultrow) {
					$firstname = $resultrow[0];	
				}
				
				$rvalue = $firstname;
			} else {
				$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
			}
			
			// Return the result
			return $rvalue;
		}
		
		/**
		 * Gets the access level for the current user
		 *
		 * @return Returns either the access level or the error status code
		 *
		 */		
		public function getUserAccessLevel() {
			
			// Check to see if the user is currently logged in
			$username = "";
			if (isset($_SESSION['username'])) {
				$username = $_SESSION['username'];
			}
			
			// Set the initial status
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to ensure that a database module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_DATABASE)) {
				
				// Query the database to get the access level
				$result = $this->modules[$this->database_module]->queryDatabase("SELECT accesslevel FROM scms_accounts WHERE username = '".$username."';");
			}
			
			// Check to see if there are results
			if (count($result) > 0) {
				
				// Loop through the results and get the access level
				foreach ($result as $resultrow) {
					$accesslevel = $resultrow[0];	
				}
				
				$rvalue = $accesslevel;
			} else {
				$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
			}
			
			// Return the result
			return $rvalue;
		}
		
		/**
		 * Gets the account status for the current user
		 *
		 * @return Returns either the account status or the error status code
		 *
		 */		
		public function getUserAccountStatus() {
			
			// Check to see if the user is currently logged in
			$username = "";
			if (isset($_SESSION['username'])) {
				$username = $_SESSION['username'];
			}
			
			// Set the initial status
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to ensure that a database module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_DATABASE)) {
				
				// Query the database to get the account status
				$result = $this->modules[$this->database_module]->queryDatabase("SELECT accountstatus FROM scms_accounts WHERE username = '".$username."';");
			}
			
			// Check to see if there are results
			if (count($result) > 0) {
				
				// Loop through the results and get the access level
				foreach ($result as $resultrow) {
					$accesslevel = $resultrow[0];	
				}
				
				$rvalue = $accesslevel;
			} else {
				$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
			}
			
			// Return the result
			return $rvalue;
		}
		
		/**
		 * Gets the user ID for the current user
		 *
		 * @return Returns either the user ID or the error status code
		 *
		 */		
		public function getUserID() {
			
			// Check to see if the user is currently logged in
			$username = "";
			if (isset($_SESSION['username'])) {
				$username = $_SESSION['username'];
			}
			
			// Set the initial status
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to ensure that a database module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_DATABASE)) {
				
				// Query the database to get the user ID
				$result = $this->modules[$this->database_module]->queryDatabase("SELECT id FROM scms_accounts WHERE username = '".$username."';");
			}
			
			// Check to see if there are results
			if (count($result) > 0) {
				
				// Loop through the results and get the user ID
				foreach ($result as $resultrow) {
					$userid = $resultrow[0];	
				}
				
				$rvalue = $userid;
			} else {
				$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
			}
			
			// Return the result
			return $rvalue;
		}
		
		/**
		 * Gets the name for a selected category
		 *
		 * @param $data The ID for the selected category
		 * @return Returns either the category name or the error status code
		 *
		 */		
		public function getCategoryName($data) {
			
			// Set the initial status
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to ensure that a database module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_DATABASE)) {
				
				// Query the database to get the category name
				$result = $this->modules[$this->database_module]->queryDatabase("SELECT name FROM scms_categories WHERE id = ".$data.";");
			}
			
			// Check to see if there are results
			if (count($result) > 0) {
				
				// Loop through the results and get the category name
				foreach ($result as $resultrow) {
					$userid = $resultrow[0];	
				}
				
				$rvalue = $userid;
			} else {
				$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
			}
			
			// Return the result
			return $rvalue;
		}
		
		/**
		 * Attempts to login a selected user into the application
		 *
		 * @param $username The selected username
		 * @param $password The selected password
		 * @return Returns the status of the login attempt
		 *
		 */		
		public function attemptLogin($username, $password) {
			
			// Set the initial status
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to ensure that a database module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_DATABASE)) {
				
				// Query the database to get the account details
				$result = $this->modules[$this->database_module]->queryDatabase("SELECT password, accesslevel, accountstatus FROM scms_accounts WHERE username = '".$username."';");
			}
			
			// Check to see if there are results
			if (count($result) > 0) {
				
				// Loop through the results and get the account details
				foreach ($result as $resultrow) {
					$cpassword = $resultrow[0];	
					$accesslevel = $resultrow[1];
					$accountstatus = $resultrow[2];
				}
				
				// Check to see if the selected account is currently unlocked
				if ($accountstatus == Engine::USER_ACCOUNT_STATUS_UNLOCKED) {
					
					// Check to see if the entered password matches the password on file
					if (crypt($password, $cpassword) == $cpassword) {
						$rvalue = Engine::USER_STATUS_VALID_LOGIN;
						
						// Login the user to the application
						$this->loginUser($username, $password, $accesslevel);
					} else {
						$rvalue = Engine::USER_STATUS_INVALID_LOGIN;
					}
				} else {
					$rvalue = Engine::USER_STATUS_ACCOUNT_LOCKED;
				}
			} else {
				$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
			}
			
			// Return the result
			return $rvalue;
		}
		
		/**
		 * Attempts to logout the current user from the application
		 *
		 * @return Returns the status of the logout attempt
		 *
		 */		
		public function attemptLogout() {
			
			// Set the initial status
			$rvalue = Engine::USER_STATUS_NOT_LOGGED_IN;
			
			// Check to see if the user is logged in
			if (isset($_SESSION['username'])) {
				
				// Logout the user
				$rvalue = $this->logoutUser($_SESSION['username']);
			}
			
			// Return the result
			return $rvalue;
		}
		
		/**
		 * Login the selected user to the application
		 *
		 * @param $username The username for the selected user
		 * @param $password The password for the selected user
		 * @param $accesslevel The access level for the selected user
		 *
		 */		
		private function loginUser($username, $password, $accesslevel) {
			$_SESSION['username'] = $username;
			$_SESSION['accesslevel'] = $accesslevel;
			$_SESSION['ipaddress'] = $_SERVER['REMOTE_ADDR'];	
		}
		
		/**
		 * Logout the selected user from the application
		 *
		 * @param $username The username for the selected user
		 * @return Returns the logout status code
		 *
		 */		
		private function logoutUser($username) {
			
			// Set the initial status
			$rvalue = Engine::NO_ERROR_STATUS;
			
			// Check to see if the logged in user matches the selected user
			if ($_SESSION['username'] == $username) {
				
				// Destroy the session
				session_destroy();
				$rvalue = Engine::USER_STATUS_HAS_BEEN_LOGGED_OUT;
			} else {
				$rvalue = Engine::USER_STATUS_NOT_LOGGED_IN;
			}
			
			// Return the result
			return $rvalue;
		}
		
		/**
		 * Attempt to lock the selected user account
		 *
		 * @param $userid The ID of the user account to be locked
		 * @return Returns the result status code
		 *
		 */		
		public function attemptLockUser($userid) {
			
			// Set the initial status
			$rvalue = Engine::NO_ERROR_STATUS;
			
			// Check to see if the user is an administrator
			if ($this->isUserAdmin()) {
				
				// Change the account status for the selected user
				$rvalue = $this->changeAccountStatus($userid, Engine::USER_ACCOUNT_STATUS_LOCKED);
			} else {
				$rvalue = Engine::USER_STATUS_NOT_AUTHORIZED;
			}
			
			// Return the result
			return $rvalue;
		}
		
		/**
		 * Attempt to unlock the selected user account
		 *
		 * @param $userid The ID of the user account to be unlocked
		 * @return Returns the result status code
		 *
		 */		
		public function attemptUnlockUser($userid) {
			
			// Set the initial status
			$rvalue = Engine::NO_ERROR_STATUS;
			
			// Check to see if the user is an administrator
			if ($this->isUserAdmin()) {
				
				// Change the account status for the selected user
				$rvalue = $this->changeAccountStatus($userid, Engine::USER_ACCOUNT_STATUS_UNLOCKED);
			} else {
				$rvalue = Engine::USER_STATUS_NOT_AUTHORIZED;
			}
			
			// Return the result
			return $rvalue;
		}
		
		/**
		 * Change the account status for a selected user
		 *
		 * @param $userid The ID of the user account to be changed
		 * @param $status The status to be applied to the user
		 * @return Returns the result status code
		 *
		 */		
		private function changeAccountStatus($userid, $status) {
			
			// Set the initial status
			$rvalue = Engine::NO_ERROR_STATUS;
			
			// Modify the selected user account status in the database
			$rvalue = $this->modules[$this->database_module]->queryDatabase("UPDATE scms_accounts SET accountstatus = ".$status." WHERE id = ".$userid.";");
			
			// Return the result
			return $rvalue;
		}
		
		public function redirectPage($URL, $time = 3) {
			$timer = $time * 1000;
			echo "<script type=\"text/Javascript\">";
			echo "setTimeout(\"location.href = '".$URL."';\",".$timer.");";
			echo "</script>";
		}
		
		/**
		 * Check the log in status for the current user
		 *
		 * @return Returns the login status for the user
		 *
		 */		
		public function checkUserLoggedIn() {
			
			// Set the initial status
			$rvalue = Engine::USER_STATUS_NOT_LOGGED_IN;
			
			// Check to see if the user is logged in
			if (isset($_SESSION['username'])) {
				$rvalue = Engine::USER_STATUS_LOGGED_IN;
			}	
			
			// Return the result
			return $rvalue;
		}
		
		/**
		 * Adds a new user to the application
		 *
		 * @param $username The username for the selected user
		 * @param $password The password for the selected user
		 * @param $accesslevel The access level for the selected user
		 * @param $firstname The first name for the selected user
		 * @param $lastname The last name for the selected user
		 * @return Returns the result status code
		 *
		 */		
		public function addUser($username, $password, $accesslevel, $firstname = 'Administrator', $lastname = '') {		
			
			// Set the initial status
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to ensure that a database module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_DATABASE)) {
				
				// Query the database to add the user
				$result = $this->modules[$this->database_module]->queryDatabase("INSERT INTO scms_accounts (username, password, firstname, lastname, accesslevel, accountstatus) VALUES('".$username."', '".crypt($password)."', '$firstname', '$lastname', ".$accesslevel.", ".Engine::USER_ACCOUNT_STATUS_UNLOCKED.");");
				
				// Check to see if the user was added successfully
				if (count($result) > 0) {
					$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
				}
			}
			
			// Return the result
			return $rvalue;
		}
		
		/**
		 * Check to see if a user with admin access exists
		 *
		 * @return Returns the result status code
		 *
		 */		
		private function checkIfAdminUserExists() {
			
			// Set the initial status
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to ensure that a database module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_DATABASE)) {
				
				// Query the database to check for an admin user
				$result = $this->modules[$this->database_module]->queryDatabase("SELECT * FROM scms_accounts WHERE accesslevel = ".Engine::USER_ACCOUNT_TYPE_ADMIN.";");
				
				// Check to see if there is an admin user
				if (count($result) > 0) {
					$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
				}
			}
			
			// Return the result
			return $rvalue;
		}
		
		/**
		 * Check to see if a user exists with the selected username
		 *
		 * @param $data The selected username to be checked
		 * @return Returns the result status code
		 *
		 */		
		public function checkIfUserExists($data) {
			
			// Set the initial status
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to ensure that a database module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_DATABASE)) {
				
				// Query the database to check for an existing user
				$result = $this->modules[$this->database_module]->queryDatabase("SELECT * FROM scms_accounts WHERE username = '".$data."';");
				
				// Check to see if there is an existing user
				if (count($result) > 0) {
					$rvalue = Engine::DATABASE_ERROR_USER_EXISTS;
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
				}
			}
			
			// Return the result
			return $rvalue;	
		}
		
		/**
		 * Adds a post category to the application
		 *
		 * @param $data The category to be added to the application
		 *
		 */		
		public function addCategory($data) {
			$rvalue = $this->insertCategory($data);	
		}
		
		/**
		 * Inserts a new category into the application
		 *
		 * @param $data The category to be added to the application
		 * @return Returns the result status code
		 *
		 */		
		private function insertCategory($data) {
			
			// Set the initial status
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to ensure that a database module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_DATABASE)) {
				
				// Query the database to insert the category
				$result = $this->modules[$this->database_module]->queryDatabase("INSERT INTO scms_categories (name) VALUES('$data');");
				
				// Check to see if the category has been inserted
				if (count($result) > 0) {
					$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
				}
			}
			
			// Return the result
			return $rvalue;	
		}
		
		/**
		 * Creates a list of posts based on the selected details
		 *
		 * @param $start The starting index
		 * @param $size The number of posts to return
		 * @return Returns a list of posts or an error status code
		 *
		 */		
		public function listPosts($start, $size) {
			
			// Set the initial status
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to ensure that a database module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_DATABASE)) {
				
				// Query the database to select the posts
				$result = $this->modules[$this->database_module]->queryDatabase("SELECT * FROM scms_posts ORDER BY dateposted DESC LIMIT ".$start.", ".$size.";");
				
				// Check to see if there were results returned
				if (count($result) > 0) {
					
					// Initialize the results array
					$rvalue = array();
					$count = 0;
					
					// Loop through the results and add the details to the array
					foreach ($result as $row) {
						
						// Get the first name for the author of the post
						$authorresult = $this->modules[$this->database_module]->queryDatabase("SELECT firstname FROM scms_accounts WHERE id = ".$row[5].";");

						foreach ($authorresult as $item) {
							$author = $item[0];	
						}
						
						// Get the comment count for the post
						$commentresult = $this->modules[$this->database_module]->queryDatabase("SELECT COUNT(*) FROM scms_comments WHERE post = ".$row[0].";");
						$comments = 0;
						if (count($commentresult) > 0) {
							foreach ($commentresult as $item) {
								$comments = $item[0];
							}
						}	
						
						// Check to see which feature is required for the post
						if ($row[6] == Engine::FEATURE_SUPPORT_TEXT_POST) {
							$details = $this->modules[$this->textpost_module]->createPostPreview($row[2]);
						} elseif ($row[6] == Engine::FEATURE_SUPPORT_IMAGE_POST) {
							$details = $this->modules[$this->imagepost_module]->createPostPreview($row[2]);
						}
						
						// Update the post details
						$category = $this->getCategoryName($row[7]);
						$rvalue[$count] = array("id" => $row[0],
							"title" => $row[1],
							"details" => $details,
							"dateposted" => strtotime($row[4]),
							"author" => $author,
							"type" => $row[6],
							"filename" => $row[3],
							"categoryname" => $category,
							"categoryid" => $row[7],
							"comments" => $comments);
						$count++;
					}					
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
				}
			}
			
			// Return the result
			return $rvalue;
		}
		
		/**
		 * Creates a list of comments based on the selected details
		 *
		 * @param $data An array of data specifying the post details
		 * @return Returns a list of comments or an error status code
		 *
		 */		
		public function listComments($data) {
			
			// Set the initial status
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to ensure that a database module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_DATABASE)) {
				
				// Query the database to select the comments for the selected post
				$result = $this->modules[$this->database_module]->queryDatabase("SELECT * FROM scms_comments WHERE post = ".$data['id']." ORDER BY dateposted DESC LIMIT ".$data['start'].", ".$data['size'].";");
				
				// Check to see if there were results returned
				if (count($result) > 0) {
					
					// Initialize the results array
					$rvalue = array();
					$count = 0;
					
					// Loop through the results and add the details to the array
					foreach ($result as $row) {
						
						// Get the first name of the author of the comment
						$authorresult = $this->modules[$this->database_module]->queryDatabase("SELECT firstname FROM scms_accounts WHERE id = ".$row[3].";");
						foreach ($authorresult as $item) {
							$author = $item[0];	
						}

						// Build the result
						$rvalue[$count] = array("id" => $row[0],
							"details" => $row[4],
							"dateposted" => strtotime($row[2]),
							"author" => $author);
						$count++;
					}					
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
				}
			}
			
			// Return the results
			return $rvalue;
		}
		
		/**
		 * Creates a list of users based on the selected details
		 *
		 * @param $start The starting index
		 * @param $size The number of users to return
		 * @return Returns a list of users or an error status code
		 *
		 */		
		public function listUsers($start, $size) {
			
			// Set the initial status
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to ensure that a database module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_DATABASE)) {
				
				// Query the database to select the users
				$result = $this->modules[$this->database_module]->queryDatabase("SELECT * FROM scms_accounts ORDER BY username LIMIT ".$start.", ".$size.";");
				
				// Check to see if there were results returned
				if (count($result) > 0) {
					
					// Initialize the results array
					$rvalue = array();
					$count = 0;
					
					// Loop through the results and add the results to the array
					foreach ($result as $row) {
						$rvalue[$count] = array("id" => $row[0],
							"username" => $row[1],
							"firstname" => $row[4],
							"lastname" => $row[5],
							"accesslevel" => $row[6],
							"dateregistered" => strtotime($row[7]),
							"accountstatus" => $row[8]);
						$count++;
					}
					
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
				}
			}
			
			// Return the results
			return $rvalue;
		}
		
		/**
		 * Creates a list of the post categories in the application
		 *
		 * @return Returns a list of categories or an error status code
		 *
		 */		
		public function listCategories() {
			
			// Set the initial status
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to ensure that a database module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_DATABASE)) {
				
				// Query the database to select the categories
				$result = $this->modules[$this->database_module]->queryDatabase("SELECT * FROM scms_categories;");
				
				// Check to see if there were results returned
				if (count($result) > 0) {
					
					// Initialize the results array
					$rvalue = array();
					$count = 0;
					
					// Loop through the results and add the results to the array
					foreach ($result as $row) {
						$rvalue[$count] = array("id" => $row[0],
							"name" => $row[1]);
						$count++;
					}
					
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
				}
			}
			
			// Return the results
			return $rvalue;
		}
		
		/**
		 * Submit a new comment on a post
		 *
		 * @param $data An array containing the comment details
		 * @return Returns the result status code
		 *
		 */		
		public function submitNewComment($data) {
			
			// Set the initial status
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to ensure that a database module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_DATABASE)) {
				
				// Query the database to insert the new comment
				$result = $this->modules[$this->database_module]->queryDatabase("INSERT INTO scms_comments (post, author, comment) VALUES(".$data['postid'].", ".$data['authorid'].", '".$data['comment']."');");
				
				// Check to ensure the comment was submitted successfully
				if (count($result) > 0) {
					$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
				}
			}
			
			// Return the results
			return $rvalue;
		}
		
		/**
		 * Submit a new post to the application
		 *
		 * @param $data An array containing the post details
		 * @return Returns the result status code
		 *
		 */		
		public function submitNewPost($data) {
			
			// Set the initial status
			$rvalue = Engine::NO_ERROR_STATUS;
			
			// Check to see if the type is set
			if (isset($data['type'])) {
				
				// Check to see which type of post has been submitted
				switch ($data['type']) {
					case Engine::FEATURE_SUPPORT_TEXT_POST:
						
						// Submit the new text post
						$rvalue = $this->modules[$this->textpost_module]->addPost($data);
						break;
					case Engine::FEATURE_SUPPORT_IMAGE_POST:
						
						// Submit the new image post
						$rvalue = $this->modules[$this->imagepost_module]->addPost($data);
						break;	
					default:
						$rvalue = Engine::POST_NO_TYPE_CONFIGURED;
						break;
				}
			} else {
				$rvalue = Engine::POST_NO_TYPE_CONFIGURED;	
			}
			
			// Return the results
			return $rvalue;
		}
		
		/**
		 * Edits the details of an existing post
		 *
		 * @param $data An array containing the post details
		 * @return Returns the result status code
		 *
		 */		
		public function editExistingPost($data) {
			
			// Set the initial status code
			$rvalue = Engine::NO_ERROR_STATUS;
			
			// Check to see if the type is set
			if (isset($data['type'])) {
				
				// Check to see which type of post has been submitted
				switch ($data['type']) {
					case Engine::FEATURE_SUPPORT_TEXT_POST:
						
						// Edit the existing text post
						$rvalue = $this->modules[$this->textpost_module]->editPost($data);
						break;
					default:
						$rvalue = Engine::POST_NO_TYPE_CONFIGURED;
						break;
				}
			} else {
				$rvalue = Engine::POST_NO_TYPE_CONFIGURED;	
			}
			
			// Return the results
			return $rvalue;
		}
		
		/**
		 * Deletes an existing post
		 *
		 * @param $data An array containing the post details
		 * @return Returns the results status code
		 *
		 */		
		public function deleteExistingPost($data) {
			
			// Set the initial status code
			$rvalue = Engine::NO_ERROR_STATUS;
			
			// Check to see if the type is set
			if (isset($data['type'])) {
				
				// Check to see which type of post is to be deleted
				switch ($data['type']) {
					case Engine::FEATURE_SUPPORT_TEXT_POST:
						
						// Delete the existing text post
						$rvalue = $this->modules[$this->textpost_module]->deletePost($data);
						break;
					case Engine::FEATURE_SUPPORT_IMAGE_POST:

						// Delete the existing image post
						$rvalue = $this->modules[$this->imagepost_module]->deletePost($data);
						break;
					default:
						$rvalue = Engine::POST_NO_TYPE_CONFIGURED;
						break;
				}
			} else {
				$rvalue = Engine::POST_NO_TYPE_CONFIGURED;	
			}
			
			// Return the results
			return $rvalue;
		}
		
		
		/**
		 * Deletes an existing comment
		 *
		 * @param $data An array containing the comment details
		 * @return Returns the results status code
		 *
		 */		
		public function deleteExistingComment($data) {
			
			// Set the initial status code
			$rvalue = Engine::NO_ERROR_STATUS;
			
			// Check to see if the type is set
			if (isset($data['type'])) {
				
				// Delete the selected comment
				$result = $this->modules[$this->database_module]->queryDatabase("DELETE FROM scms_comments WHERE id = ".$data['id'].";");
				
				// Check on the success of the delete
				if (count($result) > 0) {
					$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
				}
			} else {
				$rvalue = Engine::POST_NO_TYPE_CONFIGURED;	
			}
			
			// Return the results
			return $rvalue;
		}
		
		/**
		 * Check to see if a post exists in the application
		 *
		 * @return Returns the result status code
		 *
		 */		
		public function checkIfPostExists() {
			
			// Set the initial status code
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to see if the get post has been set
			if (isset($_GET['post'])) {
				
				// Format the post value
				$post = htmlentities(addslashes($_GET['post']));
				
				// Check to ensure that a database module is installed
				if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_DATABASE)) {
					
					// Query the database to check to see if the post exists
					$result = $this->modules[$this->database_module]->queryDatabase("SELECT * FROM scms_posts WHERE id = ".$post.";");
					
					// Check to see if the post exists or not
					if (count($result) > 0) {
						$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
					} else {
						$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
					}
				}
			} else {
				$rvalue == Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
			}
			
			// Return the results
			return $rvalue;
		}
		
		/**
		 * Displays the selected post
		 *
		 * @param $data An array containing the post details
		 * @return Returns the post details or an error status code
		 *
		 */		
		public function displaySelectedPost($data) {
			
			// Set the initial status code
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Format the id value
			$post = htmlentities(addslashes($data['id']));
			$type = 0;
			
			// Check to ensure that a database module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_DATABASE)) {
				
				// Query the database to load the selected post type
				$result = $this->modules[$this->database_module]->queryDatabase("SELECT type FROM scms_posts WHERE id = ".$post.";");
			}
			
			// Check to see if there were results returned
			if (count($result) > 0) {
				
				// Loop through the results and get the type
				foreach ($result as $resultrow) {
					$type = $resultrow[0];	
				}
			} else {
				$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
			}

			// Build the display data array
			$data = array("id" => $post, "type" => $type);
			
			// Check to see which type of post is to be displayed
			switch ($type) {
				case Engine::FEATURE_SUPPORT_TEXT_POST:
					
					// Display the text post
					$rvalue = $this->modules[$this->textpost_module]->displayPost($data);
					break;
				case Engine::FEATURE_SUPPORT_IMAGE_POST:
					
					// Display the image post
					$rvalue = $this->modules[$this->imagepost_module]->displayPost($data);
					break;
				default:
					$rvalue = Engine::POST_NO_TYPE_CONFIGURED;
					break;
			}
			
			// Return the result
			return $rvalue;
		}
		
		/**
		 * Creates a new captcha
		 *
		 * @return Returns the result status code
		 *
		 */		
		public function createNewCaptcha() {
			
			// Set the initial status
			$rvalue = Engine::NO_ERROR_STATUS;
			
			// Check to ensure that a captcha module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_CAPTCHA)) {

				// Create the captcha
				$rvalue = $this->modules[$this->captcha_module]->createCaptcha();
			}
			
			// Return the result
			return $rvalue;
		}
		
		/**
		 * Checks an entered captcha code against the currently stored one
		 *
		 * @param $data The captcha code to check
		 * @return Returns the result status code
		 *
		 */		
		public function checkEnteredCaptcha($data) {
			
			// Set the initial status
			$rvalue = Engine::NO_ERROR_STATUS;
			
			// Check to ensure that a captcha module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_CAPTCHA)) {

				// Check the captcha code
				$rvalue = $this->modules[$this->captcha_module]->checkCaptcha($data);	
			}
			
			// Return the result
			return $rvalue;	
		}
		
		/**
		 * Submits a new file to the database
		 *
		 * @param $data The data for the file upload
		 * @return Returns the result status code
		 *
		 */		
		public function submitNewFileUpload($data) {
			
			// Set the initial status
			$rvalue = Engine::NO_ERROR_STATUS;
			
			// Check to ensure that a file upload module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_FILE_UPLOAD)) {

				// Save the content of the selected file to the database
				$rvalue = $this->modules[$this->fileupload_module]->addFile($data);	
			}
			
			// Return the result
			return $rvalue;	
		}
		
		/**
		 * Generates an image which has been stored in the database
		 *
		 * @param $data The data array containing the data for the image preview
		 * @return Returns the result status code
		 *
		 */		
		public function previewImage($data) {
			
			// Set the initial status
			$rvalue = Engine::NO_ERROR_STATUS;
			
			// Check to ensure that a file upload module is installed
			if ($this->isModuleInstalled(Engine::FEATURE_SUPPORT_FILE_UPLOAD)) {

				// Read the content of the selected file from the database
				$rvalue = $this->modules[$this->fileupload_module]->readFile($data);	
			}
			
			// Return the result
			return $rvalue;	
		}
		
		/**
		 * Loads the installed modules for the application
		 *
		 */		
		private function loadModules() {
			
			// Check to see if the folder exists
			if (file_exists('./modules')) {
				
				// Open the folder for reading
				if ($handle = opendir('./modules')) {
					
					// Loop through the files
					while (false !== ($entry = readdir($handle))) {
						
						// Check to ensure that the file is module by checking the filename structure
						if (substr($entry, -11) == ".module.php") {
							
							// Attempt to initialize the module
							$this->modules[] = $this->initializeModule($entry);
						}	
					}	
				}
			}
		}
		
		/**
		 * Attempts to initialize a selected module
		 *
		 * @param $module The filename for the selected module
		 * @return Returns the handle to the module
		 *
		 */		
		private function initializeModule($module) {
			
			// Get the name of the module
			$moduleName = str_replace(".module.php", "", $module);
			$moduleClass = null;
			
			// Attempt to load the module
			include_once('./modules/'.$module);
			
			// Check to ensure that a class exists for the module
			if (class_exists($moduleName, false)) {
				
				// Attempt to create an instance of the module
				$moduleClass = new $moduleName;
				
				// Check to see if the module loaded by checking for a method
				if (!method_exists($moduleClass, 'getVersion')) {
					$moduleClass = null;	
				}
			} else {
				$moduleClass = null;
			}
			
			// Return the handle to the module
			return $moduleClass;
		}
		
		/**
		 * List all of the installed modules
		 *
		 * @return Returns an array of the installed modules
		 *
		 */		
		public function listModules() {
			
			// Initialize the array
			$rvalue = array();
			
			// Loop through the modules 
			for ($i = 0; $i < count($this->modules); $i++) {
				
				// Check to ensure the module is loaded
				if ($this->modules[$i] != null) {
					
					// Get the module details
					$rvalue[] = array("name" => $this->modules[$i]->getName(),
						"version" => $this->modules[$i]->getVersion(),
						"author" => $this->modules[$i]->getAuthor(),
						"description" => $this->modules[$i]->getDescription());
				}
			}
			
			// Return the list of modules
			return $rvalue;
		}
	}
?>