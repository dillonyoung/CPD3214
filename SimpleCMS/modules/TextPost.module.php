<?php
	class TextPost {
		private $MODULE_VERSION = "1.13.0311";
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
		
		public function editPost($data) {
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			if (isset($data['type'])) {
				if ($data['type'] == $this->MODULE_FEATURE) {
					$result = $this->database_module->queryDatabase("UPDATE scms_posts SET title = '".$data['title']."', details = '".$data['details']."' " .
						"WHERE id = ".$data['id'].";");
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
		
		public function deletePost($data) {
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			if (isset($data['type'])) {
				if ($data['type'] == $this->MODULE_FEATURE) {
					$result = $this->database_module->queryDatabase("DELETE FROM scms_posts WHERE id = ".$data['id'].";");
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
		
		public function displayPost($data) {
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			if (isset($data['type'])) {
				if ($data['type'] == $this->MODULE_FEATURE) {
					$result = $this->database_module->queryDatabase("SELECT * FROM scms_posts WHERE id = ".$data['id'].";");
					if (count($result) > 0) {
						foreach ($result as $resultrow) {
							$title = $resultrow[1];
							$body = $resultrow[2];
							$date = $resultrow[3];
							$authorid = $resultrow[4];
							
							$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
						}
					} else {
						$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
					}
				}	
			}
			
			if ($rvalue == Engine::DATABASE_ERROR_NO_ERROR) {
				$result = $this->database_module->queryDatabase("SELECT firstname FROM scms_accounts WHERE id = ".$authorid.";");
				if (count($result) > 0) {
					foreach ($result as $resultrow) {
						$author = $resultrow[0];
						
						$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
					}
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
				}
				
				$postdata = "<h1>$title</h1>";
				$postdata .= "<p>".nl2br($body)."</p>";
				$postdata .= "<span class=\"footer\">Written by $author&nbsp;</span>&nbsp;<span class=\"formatteddate\">0 seconds</span><span>&nbsp;ago</span>";
				$postdata .= "<div class=\"postdate\">".strtotime($date)."</div>";
			} else {
				$postdata = "";	
			}
			
			return $postdata;
		}
		
		public function createPostPreview($data) {
			$rvalue = "";
			$rvalue = htmlentities($data);
			return $rvalue;	
		}
	}
?>