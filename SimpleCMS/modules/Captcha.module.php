<?php
	/**
	 * Description: A captcha generator and processing module
	 * Filename...: Captcha.module.php
	 * Author.....: Dillon Young (C0005790)
	 * 
	 */	
	class Captcha {
		
		// Declare module detail variables
		private $MODULE_VERSION = "1.13.0317";
		private $MODULE_NAME = "Captcha";
		private $MODULE_AUTHOR = "Dillon Young";
		private $MODULE_DESCRIPTION = "A wrapper module for the captcha system using PNG images";
		private $MODULE_FEATURE = 2048;
		
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
		 * Generates a captcha image
		 *
		 * @return mixed This is the return value description
		 *
		 */		
		public function createCaptcha() {
			
			// Declare the string of possible captcha characters
			$characters = "abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ1234567890";
			
			// Generate a random 8 character captcha string
			$captcha_code = "";
			for ($count = 0; $count < 8; $count++) {
				$pos = rand(0, strlen($characters) - 1);
				$captcha_code .= substr($characters, $pos, 1);	
			}
			
			// Update the session with the new captcha code	
			$_SESSION['captcha_code'] = $captcha_code;
				
			// Set the size details for the captcha image
			$width = 300; 
			$height = 70;  

			// Create an image
			$image = imagecreatetruecolor($width, $height);  

			// Create the different colours used in the captcha
			$white = ImageColorAllocate($image, 255, 255, 255); 
			$black = ImageColorAllocate($image, 0, 0, 0); 
			$grey = ImageColorAllocate($image, 204, 204, 204); 
			$yellow = ImageColorAllocate($image, 255, 255, 0);

			// Fill the background in with black
			ImageFill($image, 0, 0, $black); 
				
			// Set the font to Arial
			$font = 'arial.ttf';

			// Write the captcha code on to the image
			imagettftext($image, 40, 0, 10, 50, $yellow, $font, $captcha_code);

			// Draw a border around the image
			ImageRectangle($image,0,0,$width-1,$height-1, $white); 
			
			// Loop to draw vertical lines
			for ($count = 0; $count < 10; $count++) {
				imageline($image, ($width / 10) * $count, 0, ($width / 10) * ($count + 1), $height, $white); 
				imageline($image, ($width / 10) * ($count + 1), 0, ($width / 10) * $count, $height, $white); 
			}
			
			// Loop to draw horizontal lines
			for ($count = 0; $count < 3; $count++) {
				imageline($image, 0, ($height / 3) * $count, $width, ($height / 3) * $count, $white); 
			}
				
			// Set the content type
			header("Content-Type: image/png"); 

			// Write the image contents
			imagepng($image); 
				
			// Destroy the temporary image
			imagedestroy($image); 
		}
		
		/**
		 * Checks an entered captcha value against the stored value
		 *
		 * @param $data The entered captcha value to be checked
		 * @return Returns a status code on the success of the check
		 *
		 */		
		public function checkCaptcha($data) {
			
			// Set the initial status value
			$rvalue = Engine::NO_ERROR_STATUS;

			// Check to see if the entered captcha code matches does not match the stored code
			if (strcmp($data, $_SESSION['captcha_code']) != 0) {
				$rvalue = Engine::CAPTCHA_NO_MATCH;
			}
			
			// Return the status
			return $rvalue;
		}
	}
?>