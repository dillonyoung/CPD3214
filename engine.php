<?php
	/**
	 * Author.....: Dillon Young
	 * Date.......: 02-19-2013
	 * Version....: 1.13.0220
	 * Description: The main PHP file for the SimpleCMS PHP term project
	 */
	class Engine {
	
		// Declare constants
		const ENGINE_VERSION = "1.13.0219";
		const DATABASE_ERROR_NO_ERROR = 0;
		const DATABASE_ERROR_INVALID_USERNAME_PASSWORD = 1;
		const DATABASE_ERROR_COULD_NOT_CREATE_DATABASE = 2;
		const DATABASE_ERROR_COULD_NOT_SAVE_CONFIG = 3;
		const DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE = 4;
		const DATABASE_ERROR_COULD_NOT_CLOSE_CONNECTION = 5;
		const DATABASE_ERROR_QUERY_ERROR = 6;
		const DATABASE_ERROR_NO_QUERY_RESULTS = 7;
		const USER_ACCOUNT_TYPE_ADMIN = 2;
		const USER_ACCOUNT_TYPE_NORMAL = 1;
		const USER_STATUS_NOT_LOGGED_IN = 31;
		const USER_STATUS_LOGGED_IN = 32;
		const USER_STATUS_VALID_LOGIN = 33;
		const USER_STATUS_INVALID_LOGIN = 34;
		const USER_STATUS_HAS_BEEN_LOGGED_OUT = 35;
		const NO_ERROR_STATUS = 0;
	
		// Declare variables
		private $modules = array();
		private $database_connection;
		private $database_host;
		private $database_username;
		private $database_password;
		private $database_name;
	
		/**
		 * The constructor for the class
		 *
		 * @return mixed Nothing
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
			for ($i = 0; $i < count($this->modules); $i++) {
				if ($this->modules[$i] != null) {

				}
			}
		
			// Load the database configuration
			$this->loadDatabaseConfiguration();
		}
	
		/**
		 * The destructor for the class
		 *
		 * @return mixed Nothing
		 *
		 */	
		public function __destruct() {
			$this->closeDatabaseConnection();
		}
	
		/**
		 * Updates the database configuration information and tests the connection
		 *
		 * @param mixed $db_host The database host name to be used
		 * @param mixed $db_username The database username to be used
		 * @param mixed $db_password The database password to be used
		 * @param mixed $db_name The database name to be used
		 * @return mixed Returns the status code of the database connection check
		 *
		 */	
		private function updateDatabaseConfig($db_host, $db_username, $db_password, $db_name) {
		
			// Update the database configuration
			$this->database_host = $db_host;
			$this->database_username = $db_username;
			$this->database_password = $db_password;
			$this->database_name = $db_name;
		
			// Test the connection and if successful save the configuration
			$rvalue = $this->testDatabaseConnection();
			if ($rvalue == Engine::DATABASE_ERROR_NO_ERROR) {
				$rvalue = $this->saveDatabaseConfiguration();
			}
		
			// Return the result status
			return $rvalue;
		}
	
		/**
		 * Tests the database connection using the stored database configuration information
		 *
		 * @return mixed Returns the status code of the database test
		 *
		 */	
		private function testDatabaseConnection() {
		
			// Set initial value
			$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
		
			// Attempt to connection to the MySQL server
			$this->database_connection = mysql_connect($this->database_host, $this->database_username, $this->database_password);
			if (!$this->database_connection) { 
				$rvalue = Engine::DATABASE_ERROR_INVALID_USERNAME_PASSWORD;
				$this->database_connection = null;
			} else {
			
				// Attempt to select the database
				$db_selected = mysql_select_db($this->database_name, $this->database_connection);
				if (!$db_selected) {
				
					// Attempt to create the database since it does not exist
					$db_created = mysql_query("CREATE DATABASE " . $this->database_name, $this->database_connection);
					if ($db_created) {
						$this->createDatabaseTables();
						$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
					} else {
						$rvalue = Engine::DATABASE_ERROR_COULD_NOT_CREATE_DATABASE;
						$this->database_connection = null;
					}	
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_ERROR;	
				}
			}
		
			// Return the result status
			return $rvalue;
		}
	
		private function saveDatabaseConfiguration() {
			$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
			$database_file = fopen("database.config", "wt");
			if (is_writable("database.config")) {
				fwrite($database_file, base64_encode($this->database_host)."\n");
				fwrite($database_file, base64_encode($this->database_username)."\n");
				fwrite($database_file, base64_encode($this->database_password)."\n");
				fwrite($database_file, base64_encode($this->database_name)."\n");
			} else {
				$rvalue = Engine::DATABASE_ERROR_COULD_NOT_SAVE_CONFIG;
			}
			fclose($database_file);
			if ($rvalue == Engine::DATABASE_ERROR_COULD_NOT_SAVE_CONFIG) {
				unlink("database.config");
			}
			return $rvalue;
		}
	
		private function createDatabaseTables() {
			$rvalue = mysql_select_db($this->database_name, $this->database_connection);
			if ($rvalue) {
				$rvalue = mysql_query("CREATE TABLE accounts (id BIGINT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), username VARCHAR(50) NOT NULL, UNIQUE (username), password VARCHAR(50) NOT NULL, email VARCHAR(100), firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50), accesslevel INT NOT NULL, dateregistered DATETIME NOT NULL DEFAULT NOW());", $this->database_connection);
				$rvalue = mysql_query("CREATE TABLE categories (id BIGINT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), name VARCHAR(200) NOT NULL, UNIQUE (name))");
				$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
			} else {
				$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			}
			return $rvalue;
		}
	
		public function loadPageContents() {
			if ($this->database_connection) {
				if ($this->checkIfAdminUserExists() == Engine::DATABASE_ERROR_NO_ERROR) {
					$_SESSION['configured'] = true;	
				}
			}
			if (isset($_SESSION['configured'])) {
				echo "Configured";
			} else {
				include('page_firstrun_database.php');
			}	
		}
	
		public function setCookies() {
		
		}
	
		public function getPageTitle() {
			echo "Simple CMS";	
		}
	
		public function getSiteTitle() {
			echo "Simple CMS";	
		}
	
		public function getSiteDescription() {
			echo "A simple CMS system";	
		}
	
		public function getEngineInformation() {
			echo "Powered by Simple CMS (".Engine::ENGINE_VERSION.")";
		}
	
		public function getUserFirstName($username) {
			$rvalue = mysql_select_db($this->database_name, $this->database_connection);
			if ($rvalue) {
				$rvalue = mysql_query("SELECT firstname FROM accounts WHERE username = '".$username."';");
				if ($rvalue) {
					if (mysql_num_rows($rvalue) > 0) {
						while($row = mysql_fetch_array($rvalue)) {
							$firstname = $row['firstname'];
						}
						$rvalue = $firstname;
					} else {
						$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
					}
				} else {
					$rvalue = Engine::DATABASE_ERROR_QUERY_ERROR;	
				}	
			} else {
				$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;	
			}
			return $rvalue;
		}
	
		public function attemptAdminLogin($username, $password) {
			$rvalue = mysql_select_db($this->database_name, $this->database_connection);
			if ($rvalue) {
				$rvalue = mysql_query("SELECT password FROM accounts WHERE username = '".$username."' AND accesslevel = ".Engine::USER_ACCOUNT_TYPE_ADMIN.";");
				if ($rvalue) {
					if (mysql_num_rows($rvalue) > 0) {
						while($row = mysql_fetch_array($rvalue)) {
							$cpassword = $row['password'];
						}
						if (crypt($password, $cpassword) == $cpassword) {
							$rvalue = Engine::USER_STATUS_VALID_LOGIN;
							$this->loginUser($username, $password, Engine::USER_ACCOUNT_TYPE_ADMIN);
						} else {
							$rvalue = Engine::USER_STATUS_INVALID_LOGIN;
						}
					} else {
						$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
					}
				} else {
					$rvalue = Engine::DATABASE_ERROR_QUERY_ERROR;	
				}	
			} else {
				$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;	
			}
			return $rvalue;
		}
	
		public function attemptLogout() {
			$rvalue = Engine::USER_STATUS_NOT_LOGGED_IN;
			if (isset($_SESSION['username'])) {
				$rvalue = $this->logoutUser($_SESSION['username']);
			}
			return $rvalue;
		}
	
		private function loginUser($username, $password, $accesslevel) {
			$_SESSION['username'] = $username;
			$_SESSION['accesslevel'] = $accesslevel;
			$_SESSION['ipaddress'] = $_SERVER['REMOTE_ADDR'];	
		}
		
		private function logoutUser($username) {
			$rvalue = Engine::NO_ERROR_STATUS;
			if ($_SESSION['username'] == $username) {
				session_destroy();
				$rvalue = Engine::USER_STATUS_HAS_BEEN_LOGGED_OUT;
			} else {
				$rvalue = Engine::USER_STATUS_NOT_LOGGED_IN;
			}
			return $rvalue;
		}
		
		public function redirectPage($URL, $time = 3) {
			$timer = $time * 1000;
			echo "<script type=\"text/Javascript\">";
			echo "setTimeout(\"location.href = '".$URL."';\",".$timer.");";
			echo "</script>";
		}
	
		private function printMessage($message) {
			echo $message."<br />";	
		}
	
		public function checkUserLoggedIn() {
			$rvalue = Engine::USER_STATUS_NOT_LOGGED_IN;
			if (isset($_SESSION['username'])) {
				$rvalue = Engine::USER_STATUS_LOGGED_IN;
			}	
			return $rvalue;
		}
	
		private function addUser($username, $password, $accesslevel) {
			$rvalue = mysql_select_db($this->database_name, $this->database_connection);
			if ($rvalue) {
				$rvalue = mysql_query("INSERT INTO accounts (username, password, firstname, accesslevel) VALUES('".$username."', '".crypt($password)."', 'Administrator', ".$accesslevel.");");
				if ($rvalue) {
					$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
				} else {
					$rvalue = Engine::DATABASE_ERROR_QUERY_ERROR;
				}
			} else {
				$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;	
			}
			return $rvalue;
		}
	
		private function checkIfAdminUserExists() {
			$rvalue = mysql_select_db($this->database_name, $this->database_connection);
			if ($rvalue) {
				$rvalue = mysql_query("SELECT * FROM accounts WHERE accesslevel = ".Engine::USER_ACCOUNT_TYPE_ADMIN.";");
				if ($rvalue) {
					if (mysql_num_rows($rvalue) > 0) {
						$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
					} else {
						$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
					}
				} else {
					$rvalue = Engine::DATABASE_ERROR_QUERY_ERROR;	
				}	
			} else {
				$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;	
			}
			return $rvalue;
		}
	
		private function loadDatabaseConfiguration() {
			if (file_exists("database.config")) {
				$filedata = file("database.config");
				if (count($filedata) == 4) {
					$this->database_host = base64_decode($filedata[0]);
					$this->database_username = base64_decode($filedata[1]);
					$this->database_password = base64_decode($filedata[2]);
					$this->database_name = base64_decode($filedata[3]);
					$this->openDatabaseConnection();
				}
			}
		}
	
		private function openDatabaseConnection() {
			$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
			$this->database_connection = mysql_connect($this->database_host, $this->database_username, $this->database_password);
			if (!$this->database_connection) { 
				$rvalue = Engine::DATABASE_ERROR_INVALID_USERNAME_PASSWORD;
				$this->database_connection = null;
			} else {
				$db_selected = mysql_select_db($this->database_name, $this->database_connection);
				if (!$db_selected) {
					$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_ERROR;	
				}
			}
			return $rvalue;
		}
	
		private function closeDatabaseConnection() {
			$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
			if (mysql_close($this->database_connection)) {
				$this->database_connection = null;
			} else {
				$rvalue = Engine::DATABASE_ERROR_COULD_NOT_CLOSE_CONNECTION;
			}	
			return $rvalue;
		}
	
		private function loadModules() {
		
			if (file_exists('./modules')) {
				if ($handle = opendir('./modules')) {
					while (false !== ($entry = readdir($handle))) {
						if (substr($entry, -11) == ".module.php") {
							$this->modules[] = $this->initializeModule($entry);
						}	
					}	
				}
			} else {

			}
		}
	
		private function initializeModule($module) {
			$moduleName = str_replace(".module.php", "", $module);
			$moduleClass = null;
		
			include_once('./modules/'.$module);
			if (class_exists($moduleName, false)) {
				$moduleClass = new $moduleName;
				if (!method_exists($moduleClass, 'getVersion')) {
					$moduleClass = null;	
				}
			} else {
				$moduleClass = null;
			}
			return $moduleClass;
		}
	}
?>