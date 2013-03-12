<?php

include_once('engine.php');

$engine = new Engine;

include('header.php');

if ($engine->checkUserLoggedIn()) {
	$rvalue = $engine->attemptLogout();
	if ($rvalue == Engine::USER_STATUS_HAS_BEEN_LOGGED_OUT) {
		displayMessage("You have been successfully logged out. Please wait while you are redirected back to the main page.");
		$engine->redirectPage("./");
	} else {
		displayMessage("There was an error while attempting to log you out. Please try again.");
	}
} else {
	
}

function displayMessage($message) {
?>
<div class="window_hold">
<div class="window">
<div class="window_header">Logout</div>
<div class="window_body">
<p><?php echo $message; ?></p>
<br />
</div>
</div>
</div>
<?php	
}
include('footer.php');
?>