<?php
	class TextPost {
		private $MODULE_VERSION = "1.0.301";
		private $MODULE_NAME = "TextPost";
		private $MODULE_AUTHOR = "Dillon Young";
		private $MODULE_DESCRIPTION = "A wrapper module for a text post";
		private $MODULE_FEATURE = 4;
		
		private $database_module;
		
		public function __construct() {

		}
		
		public function __destruct() {

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
		
		public function setDatabaseModule($database) {
			$this->database_module = $database;
		}
		
		public function addPost($data) {
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			if (isset($data['type'])) {
				if ($data['type'] == $this->MODULE_FEATURE) {
					$result = $this->database_module->queryDatabase("INSERT INTO scms_posts (title, details, author, type, category) " .
						"VALUES('".$data['title']."', '".$data['details']."', ".$data['author'].", ".$data['type'].", ".$data['category'].");");
					if (count($result) > 0) {
						$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
					} else {
						$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
					}
					return $rvalue;
				}	
			}	
			return $rvalue;
		}
		
		public function testDatabase() {
			$result = $this->database_module->queryDatabase("SELECT * FROM scms_accounts");
			echo "yes";
			print_r($result);		
		}
	}
?>