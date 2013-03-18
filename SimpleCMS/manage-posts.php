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
<option value="youtubepost">Youtube Post</option>
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
	$rvalue = $engine->listCategories();
	if ($rvalue != Engine::DATABASE_ERROR_COULD_NOT_ACCESS_DATABASE) {
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
<div id="postlist"></div>
<div id="dialog-confirm" title="Empty the recycle bin?">
  <p>These items will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>