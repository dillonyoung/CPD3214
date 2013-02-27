<?php
	class MySQL {
		private $MODULE_VERSION = "1.0.203";
		private $MODULE_NAME = "MySQL";
		private $MODULE_AUTHOR = "Dillon Young";
		private $MODULE_DESCRIPTION = "A wrapper module for using a MySQL database";
		private $MODULE_FEATURE = 2;
		
		private $database_host;
		private $database_username;
		private $database_password;
		private $database_name;
		
		public function __construct() {
			$this->loadDatabaseConfiguration();
		}
		
		public function __destruct() {
			$this->closeDatabaseConnection();
		}
		
		public function getVersion() {
			return $this->MODULE_VERSION;
		}	
		
		public function getName() {
			return $this->MODULE_NAME;	
		}
		
		public function getAuthor() {
			return $this->MODULE_AUTHOR;
		}
		
		public function getFeatures() {
			return $this->MODULE_FEATURE;
		}
		
		public function getDescription() {
			return $this->MODULE_DESCRIPTION;	
		}
		
		public function getDatabaseConnection() {
			return $this->database_connection;	
		}
		
		public function queryDatabase($query) {
			$rvalue = array();
			$selectdb = mysql_select_db($this->database_name, $this->database_connection);
			if ($selectdb) {
				$queryresult = mysql_query($query);
				if ($queryresult) {
					if (mysql_num_rows($queryresult) > 0) {
						while($row = mysql_fetch_row($queryresult)) {
							$rvalue[] = $row;
						}
					}
				}
			}
			return $rvalue;
		}
		
		public function updateDatabaseConfig($db_host, $db_username, $db_password, $db_name) {
			
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
					
					// REDO CODE HERE!!!!!!!
					//// Attempt to create the database since it does not exist
					//$db_created = mysql_query("CREATE DATABASE " . $this->database_name, $this->database_connection);
					//if ($db_created) {
					//	$this->createDatabaseTables();
					//	$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
					//} else {
					//	$rvalue = Engine::DATABASE_ERROR_COULD_NOT_CREATE_DATABASE;
					//	$this->database_connection = null;
					//}	
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_ERROR;	
				}
			}
			
			// Return the result status
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
	}
?>