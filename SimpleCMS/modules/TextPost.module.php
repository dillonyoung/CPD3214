<?php
	/**
	 * Description: A text post processing module
	 * Filename...: TextPost.module.php
	 * Author.....: Dillon Young (C0005790)
	 * 
	 */	
	class TextPost {
		
		// Declare module detail variables
		private $MODULE_VERSION = "1.13.0311";
		private $MODULE_NAME = "TextPost";
		private $MODULE_AUTHOR = "Dillon Young";
		private $MODULE_DESCRIPTION = "A wrapper module for a text post";
		private $MODULE_FEATURE = 4;
		
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
		 * Adds a new post to the database
		 *
		 * @param $data An array containing the data for the posts
		 * @return Returns a status code on the success of adding the post
		 *
		 */		
		public function addPost($data) {
			
			// Set the initial status value
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to see if the type of post matches the supported features of the module
			if (isset($data['type']) && $data['type'] == $this->MODULE_FEATURE) {
					
				// Attempt to insert the new post into the database
				$result = $this->database_module->queryDatabase("INSERT INTO scms_posts (title, details, filename, author, type, category) " .
					"VALUES('".$data['title']."', '".$data['details']."', '<blank>', ".$data['author'].", ".$data['type'].", ".$data['category'].");");

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
		
		/**
		 * Edits an existing post in the database
		 *
		 * @param $data An array containing the data for the post
		 * @return Returns a status code on the success of editing the post
		 *
		 */		
		public function editPost($data) {
			
			// Set the initial status value
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to see if the type of post matches the supported features of the module
			if (isset($data['type']) && $data['type'] == $this->MODULE_FEATURE) {
					
				// Attempt to update the selected post in the database
				$result = $this->database_module->queryDatabase("UPDATE scms_posts SET title = '".$data['title']."', details = '".$data['details']."' " .
					"WHERE id = ".$data['id'].";");
					
				// Check on the success of the update
				if (count($result) > 0) {
					$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
				}
			}		
			
			// Return the value
			return $rvalue;
		}
		
		/**
		 * Deletes an existing post from the database
		 *
		 * @param $data An array containing the data for the post
		 * @return Returns a status code on the success of deleting the post
		 *
		 */		
		public function deletePost($data) {
			
			// Set the initial status value
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to see if the type of post matches the supported features of the module
			if (isset($data['type']) && $data['type'] == $this->MODULE_FEATURE) {
				
				// Attempt to delete the selected post from the database
				$result = $this->database_module->queryDatabase("DELETE FROM scms_comments WHERE post = ".$data['id'].";");
				$result = $this->database_module->queryDatabase("DELETE FROM scms_posts WHERE id = ".$data['id'].";");
				
				// Check on the success of the delete
				if (count($result) > 0) {
					$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
				}
			}	

			// Return the value
			return $rvalue;
		}
		
		/**
		 * Builds the HTML to display a selected post
		 *
		 * @param $data An array containing the data for the post
		 * @return Returns either the formatted HTML or a status code if an error occurred
		 *
		 */		
		public function displayPost($data) {
			
			// Set the initial status value
			$rvalue = Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE;
			
			// Check to see if the type of post matches the supported features of the module
			if (isset($data['type']) && $data['type'] == $this->MODULE_FEATURE) {
				
				// Attempt to read the selected post details from the database
				$result = $this->database_module->queryDatabase("SELECT * FROM scms_posts WHERE id = ".$data['id'].";");
				
				// Check on the success of the select
				if (count($result) > 0) {
					
					// Get the values from the results
					foreach ($result as $resultrow) {
						$title = $resultrow[1];
						$body = $resultrow[2];
						$date = $resultrow[4];
						$authorid = $resultrow[5];
							
						$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
					}
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
				}
			}	
			
			// Check to ensure that no error has occurred
			if ($rvalue == Engine::DATABASE_ERROR_NO_ERROR) {
				
				// Attempt to read the user account details from the database
				$result = $this->database_module->queryDatabase("SELECT firstname FROM scms_accounts WHERE id = ".$authorid.";");
				
				// Check on the success of the select
				if (count($result) > 0) {
					
					// Get the values from the results
					foreach ($result as $resultrow) {
						$author = $resultrow[0];
						
						$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
					}
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
				}
				
				// Attempt to read the comment count for the selected post from the database
				$result = $this->database_module->queryDatabase("SELECT COUNT(*) FROM scms_comments WHERE post = ".$data['id'].";");
				
				// Check on the success of the select
				if (count($result) > 0) {
					
					// Get the values from the results
					foreach ($result as $resultrow) {
						$comment = $resultrow[0];
						
						$rvalue = Engine::DATABASE_ERROR_NO_ERROR;
					}
				} else {
					$rvalue = Engine::DATABASE_ERROR_NO_QUERY_RESULTS;
				}
				
				// Build the post HTML
				$postdata = "<h1>$title</h1>";
				$postdata .= "<p>".nl2br($body)."</p>";
				$postdata .= "<span class=\"footer\">Written by $author&nbsp;</span>&nbsp;<span class=\"formatteddate\">0 seconds</span><span>&nbsp;ago</span>";
				$postdata .= "<div class=\"postdate\">".strtotime($date)."</div>";
				$postdata .= "<div class=\"postcommentcount\">".$comment."</div>";
			} else {
				$postdata = "";	
			}
			
			// Return the value
			return $postdata;
		}
		
		/**
		 * Creates a preview for a selected post
		 *
		 * @param $data The post body to be formatted
		 * @return Returns the formatted post body
		 *
		 */		
		public function createPostPreview($data) {
			$rvalue = "";
			$rvalue = htmlentities($data);
			return $rvalue;	
		}
	}
?>