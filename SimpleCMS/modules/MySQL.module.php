<?php
	/**
	 * Description: A MySQL processing and control module
	 * Filename...: MySQL.module.php
	 * Author.....: Dillon Young (C0005790)
	 * 
	 */	
	class MySQL {
		
		// Declare module detail variables
		private $MODULE_VERSION = "1.13.0306";
		private $MODULE_NAME = "MySQL";
		private $MODULE_AUTHOR = "Dillon Young";
		private $MODULE_DESCRIPTION = "A wrapper module for using a MySQL database";
		private $MODULE_FEATURE = 2;
		
		// Declare module variables
		private $database_host;
		private $database_username;
		private $database_password;
		private $database_name;
		private $database_connection;
		
		/**
		 * The constructor for the module
		 *
		 */	
		public function __construct() {
			$this->database_connection = null;
			$this->loadDatabaseConfiguration();
		}
		
		/**
		 * The destructor for the module
		 *
		 */	
		public function __destruct() {
			$this->closeDatabaseConnection();
		}
		
		/**
		 * Get the version number of the module
		 *
		 * @return Returns the version number of the module
		 *
		 */		
		public function getVersion() {
			return $this->MODULE_VERSION;
		}	
		
		/**
		 * Gets the name of the module
		 *
		 * @return Returns the name of the module
		 *
		 */		
		public function getName() {
			return $this->MODULE_NAME;	
		}
		
		/**
		 * Gets the name of the author of the module
		 *
		 * @return Returns the name of the author of the module
		 *
		 */		
		public function getAuthor() {
			return $this->MODULE_AUTHOR;
		}
		
		/**
		 * Gets the supported features of the module
		 *
		 * @return Returns the supported features of the module
		 *
		 */		
		public function getFeatures() {
			return $this->MODULE_FEATURE;
		}
		
		/**
		 * Gets the description of the module
		 *
		 * @return Gets the description of the module
		 *
		 */		
		public function getDescription() {
			return $this->MODULE_DESCRIPTION;	
		}
		
		/**
		 * Gets the current database connection
		 *
		 * @return Returns a reference to the current database connection
		 *
		 */		
		public function getDatabaseConnection() {
			
			// Check to see if the database connection is not null
			if ($this->database_connection != null) {
				return $this->database_connection;
			} else {
				return null;	
			}
		}
		
		/**
		 * Queries the database and returns the results
		 *
		 * @param $query The SQL query to be executed
		 * @return Returns the results of executing the SQL statement
		 *
		 */		
		public function queryDatabase($query) {
			
			// Initialize the return array
			$rvalue = array();
			
			// Select the database
			$selectdb = mysql_select_db($this->database_name, $this->database_connection);
			
			// Check to ensure the database was selected
			if ($selectdb) {
				
				// Execute the selected query
				$queryresult = mysql_query($query);
				
				// Check to see if the query results were boolean
				if ($queryresult === true || $queryresult === false) {
					return $queryresult;
				} else {
					
					// Process the results and add them to the array
					if ($queryresult) {
						if (mysql_num_rows($queryresult) > 0) {
							while($row = mysql_fetch_row($queryresult)) {
								$rvalue[] = $row;
							}
						}
					}
				}
			}
			
			// Return the results
			return $rvalue;
		}
		
		/**
		 * Updates the database configuration information
		 *
		 * @param $db_host The database host name
		 * @param $db_username The database username
		 * @param $db_password The database password
		 * @param $db_name The name of the database
		 * @return mixed This is the return value description
		 *
		 */		
		public function updateConfiguration($db_host, $db_username, $db_password, $db_name) {
			
			// Update the database configuration
			$this->database_host = $db_host;
			$this->database_username = $db_username;
			$this->database_password = $db_password;
			$this->database_name = $db_name;
		}
		
		/**
		 * Tests the database configuration
		 *
		 * @return Returns the a status code on the success of the test
		 *
		 */		
		public function testConnection() {
			
			// Set initial value
			$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
			
			// Attempt to connection to the MySQL server
			$this->database_connection = mysql_connect($this->database_host, $this->database_username, $this->database_password);
			
			// Check to see if the connection failed
			if (!$this->database_connection) { 
				$rvalue = Engine::DATABASE_ERROR_INVALID_USERNAME_PASSWORD;
				$this->database_connection = null;
			} else {
				
				// Attempt to select the database
				$db_selected = mysql_select_db($this->database_name, $this->database_connection);
				
				// Check to see if the database select failed
				if (!$db_selected) {
					$rvalue = Engine::DATABASE_ERROR_NO_DATABASE;
				} else {
					$this->saveDatabaseConfiguration();
					$rvalue = Engine::DATABASE_ERROR_NO_ERROR;	
				}
			}
			
			// Return the result status
			return $rvalue;
		}
		
		/**
		 * Load the database configuration information
		 *
		 */		
		private function loadDatabaseConfiguration() {
			
			// Check to see if the database configuration file exists
			if (file_exists("database.config")) {
				
				// Attempt to read the contents of the file
				$filedata = file("database.config");
				
				// Update the database configuration information with the information from the file
				if (count($filedata) == 4) {
					$this->database_host = base64_decode($filedata[0]);
					$this->database_username = base64_decode($filedata[1]);
					$this->database_password = base64_decode($filedata[2]);
					$this->database_name = base64_decode($filedata[3]);
					$this->openDatabaseConnection();
				}
			}
		}
		
		/**
		 * Saves the current database configuration information
		 *
		 * @return Returns the status of the save process
		 *
		 */		
		private function saveDatabaseConfiguration() {
			
			// Set the initial status value
			$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
			
			// Attempt to open the database configuration file
			$database_file = fopen("database.config", "wt");
			
			// Check to ensure the file is writable
			if (is_writable("database.config")) {
				
				// Write the database configuration to the file
				fwrite($database_file, base64_encode($this->database_host)."\n");
				fwrite($database_file, base64_encode($this->database_username)."\n");
				fwrite($database_file, base64_encode($this->database_password)."\n");
				fwrite($database_file, base64_encode($this->database_name)."\n");
			} else {
				$rvalue = Engine::DATABASE_ERROR_COULD_NOT_SAVE_CONFIG;
			}
			fclose($database_file);
			
			// Check to see if the file could not be saved and ensure the file is removed
			if ($rvalue == Engine::DATABASE_ERROR_COULD_NOT_SAVE_CONFIG) {
				unlink("database.config");
			}
			
			// Return the status
			return $rvalue;
		}
		
		/**
		 * Opens a database connection using current database configuration
		 *
		 * @return Returns the status of the database connection
		 *
		 */		
		private function openDatabaseConnection() {
			
			// Set the initial status value
			$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
			
			// Attempt to connect to the database
			$this->database_connection = @mysql_connect($this->database_host, $this->database_username, $this->database_password);
			
			// Check to see if the connection failed
			if (!$this->database_connection) { 
				$rvalue = Engine::DATABASE_ERROR_INVALID_USERNAME_PASSWORD;
				$this->database_connection = null;
			} else {
				
				// Attempt to select the database
				$db_selected = mysql_select_db($this->database_name, $this->database_connection);
				
				// Check to see if the database select failed
				if (!$db_selected) {
					$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_ERROR;	
				}
			}
			
			// Return the status
			return $rvalue;
		}
		
		/**
		 * Close the current database connection
		 *
		 * @return Returns the status of closing the database connection
		 *
		 */		
		private function closeDatabaseConnection() {
			
			// Set the initial status value
			$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
			
			// Check to ensure that the database connection is not already closed
			if ($this->database_connection != null) {
				
				// Attempt to close the connection
				if (mysql_close($this->database_connection)) {
					$this->database_connection = null;
				} else {
					$rvalue = Engine::DATABASE_ERROR_COULD_NOT_CLOSE_CONNECTION;
				}	
			}
			
			// Return the status
			return $rvalue;
		}
	}
?>