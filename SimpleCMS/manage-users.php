<?php
	/**
	 * Description: Creates the manage users page for the management console
	 * Filename...: manage-users.php
	 * Author.....: Dillon Young (C0005790)
	 * 
	 */	
?>
<h1>Users</h1>
<p>Below is a list of the currently registered users on the site ordered by login name. If you would like to lock an user account move your mouse over the user account and click on the lock icon (<img src="./images/lock.png">). If you would like to unlock an user account move your mouse over the user account and click on the unlock icon (<img src="./images/unlock.png">).</p>
<div id="userlist"></div>
<div id="buttonhold">
<button id="btn_manage_loadmoreusers" name="btn_manage_loadmoreusers">Load More Users</button>
</div>
<div id="dialog-confirm" title="">
  <p></p>
</div>