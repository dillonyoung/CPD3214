<?php
class Engine {
	const ENGINE_VERSION = "1.13.0219";
	const DATABASE_ERROR_NO_ERROR = 0;
	const DATABASE_ERROR_INVALID_USERNAME_PASSWORD = 1;
	const DATABASE_ERROR_COULD_NOT_CREATE_DATABASE = 2;
	const DATABASE_ERROR_COULD_NOT_SAVE_CONFIG = 3;
	const DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE = 4;
	const DATABASE_ERROR_COULD_NOT_CLOSE_CONNECTION = 5;
	const DATABASE_ERROR_QUERY_ERROR = 6;
	const USER_ACCOUNT_TYPE_ADMIN = 2;
	const USER_ACCOUNT_TYPE_NORMAL = 1;
	private $modules = array();
	private $database_connection;
	private $database_host;
	private $database_username;
	private $database_password;
	private $database_name;
	
	public function __construct() {
		ini_set('display_errors', 1); 
		error_reporting(E_ALL);
		
		//if (isset($_SESSION['running'])) {
			session_start();
			$_SESSION['running'] = true;
			$this->database_connection = null;
		//}
		
		//$this->printMessage("Engine Loaded");	
		$this->loadModules();
		for ($i = 0; $i < count($this->modules); $i++) {
			if ($this->modules[$i] != null) {
				//$this->printMessage($this->modules[$i]->getVersion());
			}
		}
		
		$this->loadDatabaseConfiguration();
	}
	
	public function __destruct() {
		$this->closeDatabaseConnection();
	}
	
	private function updateDatabaseConfig($db_host, $db_username, $db_password, $db_name) {
		$this->database_host = $db_host;
		$this->database_username = $db_username;
		$this->database_password = $db_password;
		$this->database_name = $db_name;
		$rvalue = $this->testDatabaseConnection();
		if ($rvalue == Engine::DATABASE_ERROR_NO_ERROR) {
			$rvalue = $this->saveDatabaseConfiguration();
		}
		return $rvalue;
	}
	
	private function testDatabaseConnection() {
		$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
		$this->database_connection = mysql_connect($this->database_host, $this->database_username, $this->database_password);
		if (!$this->database_connection) { 
			$rvalue = Engine::DATABASE_ERROR_INVALID_USERNAME_PASSWORD;
			$this->database_connection = null;
		} else {
			$db_selected = mysql_select_db($this->database_name, $this->database_connection);
			if (!$db_selected) {
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
			$rvalue = mysql_query("CREATE TABLE accounts (id BIGINT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), username VARCHAR(50) NOT NULL, UNIQUE (username), password VARCHAR(50) NOT NULL, email VARCHAR(100), firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50), accesslevel INT NOT NULL);", $this->database_connection);
			$rvalue = mysql_query("CREATE TABLE categories (id BIGINT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), name VARCHAR(200) NOT NULL)");
			$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
		} else {
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
		}
		return $rvalue;
	}
	
	public function loadPageContents() {
		
		if (isset($_SESSION['configured'])) {
			
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
	
	private function printMessage($message) {
		echo $message."<br />";	
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