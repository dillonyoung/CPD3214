<h1>About</h1>
<p>Simple CMS has been created by Dillon Young for the CPD-3214 term project.</p>
<p>Below is a list of the installed modules.</p>
<?php
	$modules = $engine->listModules();
	foreach($modules as $module) {
		echo "<b>".$module['name']."</b> ".$module['version']." created by ".$module['author']."<br />";
		echo "<p>".$module['description']."</p>";
	}
?>