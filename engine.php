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
		
		const FEATURE_SUPPORT_DATABASE = 2;
	
		// Declare variables
		private $modules = array();
		private $database_module;
		private $database_connection;

	
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
			
			// Check for a module which handles database functions
			$this->database_module = -1;
			for ($i = 0; $i < count($this->modules); $i++) {
				if ($this->modules[$i] != null) {
					if ($this->modules[$i]->getFeatures() == Engine::FEATURE_SUPPORT_DATABASE) {
						$this->database_module = $i;
					}
				}
			}

			if ($this->database_module == -1) {
				die("No database module installed!");
			} else {
				$this->database_connection = $this->modules[$this->database_module]->getDatabaseConnection();
			}
		}
	
		/**
		 * The destructor for the class
		 *
		 * @return mixed Nothing
		 *
		 */	
		public function __destruct() {
			
		}
	
		private function updateDatabaseConfig($db_host, $db_username, $db_password, $db_name) {
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			if ($this->database_module != null) {
				$rvalue = $this->modules[$this->database_module]->updateDatabaseConfig($db_host, $db_username, $db_password, $db_name);	
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
				include('page-firstrun-database.php');
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
	
		public function getUserFirstName() {
			$username = "";
			if (isset($_SESSION['username'])) {
				$username = $_SESSION['username'];
			}
			$result = $this->modules[$this->database_module]->queryDatabase("SELECT firstname FROM accounts WHERE username = '".$username."';");
			
			if (count($result) > 0) {
				foreach ($result as $resultrow) {
					$firstname = $resultrow[0];	
				}
				$rvalue = $firstname;
			} else {
				$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
			}
			return $rvalue;
		}
		
		public function getUserAccessLevel() {
			$username = "";
			if (isset($_SESSION['username'])) {
				$username = $_SESSION['username'];
			}
			$result = $this->modules[$this->database_module]->queryDatabase("SELECT accesslevel FROM accounts WHERE username = '".$username."';");
			
			if (count($result) > 0) {
				foreach ($result as $resultrow) {
					$accesslevel = $resultrow[0];	
				}
				$rvalue = $accesslevel;
			} else {
				$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
			}
			return $rvalue;
		}
	
		public function attemptLogin($username, $password) {
			$result = $this->modules[$this->database_module]->queryDatabase("SELECT password, accesslevel FROM accounts WHERE username = '".$username."';");
			
			if (count($result) > 0) {
				foreach ($result as $resultrow) {
					$cpassword = $resultrow[0];	
					$accesslevel = $resultrow[1];
				}
				if (crypt($password, $cpassword) == $cpassword) {
					$rvalue = Engine::USER_STATUS_VALID_LOGIN;
					$this->loginUser($username, $password, $accesslevel);
				} else {
					$rvalue = Engine::USER_STATUS_INVALID_LOGIN;
				}
			} else {
				$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
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
			$result = $this->modules[$this->database_module]->queryDatabase("SELECT * FROM accounts WHERE accesslevel = ".Engine::USER_ACCOUNT_TYPE_ADMIN.";");
			
			if (count($result) > 0) {
				$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
			} else {
				$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
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
		
		public function listModules() {
			$rvalue = array();
			
			for ($i = 0; $i < count($this->modules); $i++) {
				if ($this->modules[$i] != null) {
					$rvalue[] = array("name" => $this->modules[$i]->getName(),
						"version" => $this->modules[$i]->getVersion(),
						"author" => $this->modules[$i]->getAuthor(),
						"description" => $this->modules[$i]->getDescription());
				}
			}
			
			return $rvalue;
		}
	}
?>