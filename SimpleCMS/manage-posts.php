<?php
	/**
	 * Description: Creates the manage posts page for the management console
	 * Filename...: manage-posts.php
	 * Author.....: Dillon Young (C0005790)
	 * 
	 */	
?>
<h1>Posts</h1>
<p>Use the below options to manage posts.</p>
<nav id="top">
<button id="btn_manage_newpost">New Post</button>
</nav>
<div id="newposttype">
<label for="cbo_posttype_select">Select Post Type:</label>
<select name="cbo_posttype_select" id="cbo_posttype_select">
<option value="">Select Post Type</option>
<option value="textpost">Text Post</option>
<option value="imagepost">Image Post</option>
</select>
</div>
<div id="newtextpostentry">
<label for="txt_newtextpost_title">Title:</label>
<input type="text" name="txt_newtextpost_title" id="txt_newtextpost_title" />
<div class="space"></div>
<label for="txt_newtextpost_body">Post:</label>
<textarea name="txt_newtextpost_body" id="txt_newtextpost_body"></textarea>
<label for="cbo_newtextpost_category">Category:</label>
<select id="cbo_newtextpost_category">
<option value="">Select Category</option>
<?php
	
	// Get a list of categories
	$rvalue = $engine->listCategories();
	
	// Check to ensure no error occurred
	if ($rvalue != Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE) {
		
		// Loop through the categories and add them to the page
		foreach ($rvalue as $row) {
			echo "<option value=\"".$row['id']."\">".$row['name']."</option>\n";
		}
	}
?>
</select>
<div class="space"></div>
<button id="btn_manage_submit_newtextpost">Submit Post</button>
<button id="btn_manage_cancel_newtextpost">Cancel</button>
<span></span>
</div>

<div id="newimagepostentry">
<label for="txt_newimagepost_title">Title:</label>
<input type="text" name="txt_newimagepost_title" id="txt_newimagepost_title" />
<div class="space"></div>
<label for="txt_newimagepost_body">Post:</label>
<textarea name="txt_newimagepost_body" id="txt_newimagepost_body"></textarea>
<label for="txt_newimagepost_file">Image:</label>
<input type="file" name="txt_newimagepost_file" id="txt_newimagepost_file" />
<input type="hidden" name="txt_newimagepost_filename" id="txt_newimagepost_filename" />
<label for="cbo_newimagepost_category">Category:</label>
<select id="cbo_newimagepost_category">
<option value="">Select Category</option>
<?php
	
	// Get a list of categories
	$rvalue = $engine->listCategories();
	
	// Check to ensure no error occurred
	if ($rvalue != Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE) {
		
		// Loop through the categories and add them to the page
		foreach ($rvalue as $row) {
			echo "<option value=\"".$row['id']."\">".$row['name']."</option>\n";
		}
	}
?>
</select>
<div class="space"></div>
<button id="btn_manage_submit_newimagepost">Submit Post</button>
<button id="btn_manage_cancel_newimagepost">Cancel</button>
<span></span>
</div>

<div id="postlist"></div>
<div id="buttonhold">
<button id="btn_manage_loadmoreposts" name="btn_manage_loadmoreposts">Load More Posts</button>
</div>
<div id="dialog-confirm" title="">
  <p></p>
</div>
