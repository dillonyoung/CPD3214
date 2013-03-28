<?php
	/**
	 * Description: Displays the about page for the management console
	 * Filename...: manage-about.php
	 * Author.....: Dillon Young (C0005790)
	 * 
	 */
?>
<h1>About</h1>
<p>Simple CMS has been created by Dillon Young for the CPD-3214 term project.</p>
<p>Below is a list of the installed modules.</p>
<?php
	
	// Get a list of the installed modules
	$modules = $engine->listModules();
	
	// Loop through the list and output the module details
	foreach($modules as $module) {
		echo "<b>".$module['name']."</b> ".$module['version']." created by ".$module['author']."<br />";
		echo "<p>".$module['description']."</p>";
	}
?>