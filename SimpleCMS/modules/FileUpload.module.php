<?php
	/**
	 * Description: A file upload processing module
	 * Filename...: FileUpload.module.php
	 * Author.....: Dillon Young (C0005790)
	 * 
	 */	
	class FileUpload {
		
		// Declare module detail variables
		private $MODULE_VERSION = "1.13.0403";
		private $MODULE_NAME = "FileUpload";
		private $MODULE_AUTHOR = "Dillon Young";
		private $MODULE_DESCRIPTION = "A wrapper module for file uploads storing the files in the database";
		private $MODULE_FEATURE = 32;
		
		// Declare module variables
		private $database_module;
		
		/**
		 * The constructor for the module
		 *
		 */		
		public function __construct() {

		}
		
		/**
		 * The destructor for the module
		 *
		 */		
		public function __destruct() {

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
		 * Sets the reference to the database module
		 *
		 * @param $database The reference to the database module
		 *
		 */		
		public function setDatabaseModule($database) {
			$this->database_module = $database;
		}
		
		/**
		 * Adds a new file to the database
		 *
		 * @param $data An array containing the data for the file
		 * @return Returns a status code on the success of adding the file
		 *
		 */		
		public function addFile($data) {
			
			// Set the initial status value
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to see if the type of post matches the supported features of the module
			if (isset($data['type']) && $data['type'] == $this->MODULE_FEATURE) {
				
				$filedata = addslashes($data['filedata']);
				
				// Attempt to insert the new image into the database
				$result = $this->database_module->queryDatabase("INSERT INTO scms_files (filename, filedata, filetype) " .
					"VALUES('".$data['filename']."', '".$filedata."', '".$data['filetype']."');");

				// Check on the success of the insert
				if (count($result) > 0) {
					$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
				}
			}	
			
			// Return the value
			return $rvalue;
		}
		
		public function readFile($data) {
			
			// Set the initial status value
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to see if the type of post matches the supported features of the module
			if (isset($data['type']) && $data['type'] == $this->MODULE_FEATURE) {

				// Attempt to insert the new image into the database
				$result = $this->database_module->queryDatabase("SELECT * FROM scms_files WHERE filename = '".$data['filename']."';");

				// Check on the success of the insert
				if (count($result) > 0) {
					
					// Loop through the results and get the type
					foreach ($result as $resultrow) {
						$type = $resultrow[3];
						$data = $resultrow[2];
					}

					// Set the content type
					header("Content-Type: ".$type); 
					header("Content-length: ".strlen($data));
					echo $data;
					$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
				}
			}	
			
			// Return the value
			//return $rvalue;
		}
	}
?>