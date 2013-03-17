<?php
	class Captcha {
		private $MODULE_VERSION = "1.13.0317";
		private $MODULE_NAME = "Captcha";
		private $MODULE_AUTHOR = "Dillon Young";
		private $MODULE_DESCRIPTION = "A wrapper module for the captcha system using PNG images";
		private $MODULE_FEATURE = 2048;
		
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
		
		public function createCaptcha() {
			$characters = "abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ1234567890";
				
			$captcha_code = "";
			for ($count = 0; $count < 8; $count++) {
				$pos = rand(0, strlen($characters) - 1);
				$captcha_code .= substr($characters, $pos, 1);	
			}
				
			$_SESSION['captcha_code'] = $captcha_code;
				
			$width = 300; 
			$height = 70;  

			$image = imagecreatetruecolor($width, $height);  

			$white = ImageColorAllocate($image, 255, 255, 255); 
			$black = ImageColorAllocate($image, 0, 0, 0); 
			$grey = ImageColorAllocate($image, 204, 204, 204); 
			$yellow = ImageColorAllocate($image, 255, 255, 0);

			ImageFill($image, 0, 0, $black); 
				
			$font = 'arial.ttf';

			imagettftext($image, 40, 0, 10, 50, $yellow, $font, $captcha_code);

			//Throw in some lines to make it a little bit harder for any bots to break 
			ImageRectangle($image,0,0,$width-1,$height-1, $white); 
				
			for ($count = 0; $count < 10; $count++) {
				imageline($image, ($width / 10) * $count, 0, ($width / 10) * ($count + 1), $height, $white); 
				imageline($image, ($width / 10) * ($count + 1), 0, ($width / 10) * $count, $height, $white); 
			}
			for ($count = 0; $count < 3; $count++) {
				imageline($image, 0, ($height / 3) * $count, $width, ($height / 3) * $count, $white); 
			}
				
			header("Content-Type: image/png"); 


			imagepng($image); 
				
			imagedestroy($image); 
		}
		
		public function checkCaptcha($data) {
			$rvalue = Engine::NO_ERROR_STATUS;

			if (strcmp($data, $_SESSION['captcha_code']) != 0) {
				$rvalue = Engine::CAPTCHA_NO_MATCH;
			}
			
			return $rvalue;
		}
	}
?>