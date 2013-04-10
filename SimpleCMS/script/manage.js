/**
* Description: Contains managements features for the application
* Filename...: manage.js
* Author.....: Dillon Young (C0005790)
* 
*/

var selectedPost = null;
var timeOut;
var numberOfPostsToLoad = 5;
var numberOfUsersToLoad = 5;

// Check to see if the document is ready
$(document).ready(function () {

	// Load the site detials
	loadSiteDetails();

	// Load the post list
	loadPostList();

	// Load the user list
	loadUserList();

	// Update the button styles
	$('button').button();

	// Register a click listener for the update site details button
	$('button#btn_manage_updatesitedetails').click(function () {

		// Update the site details
		updateSiteDetails();
	});

	// Register a click listener for the load more posts button
	$('button#btn_manage_loadmoreposts').click(function () {

		// Load more posts
		loadPostList();
	});

	// Register a click listener for the load more users button
	$('button#btn_manage_loadmoreusers').click(function () {

		// Load more users
		loadUserList();
	});

	// Register a click listener for the new post button
	$('button#btn_manage_newpost').click(function () {

		// Show the new post form
		$('#newposttype').fadeIn(500);
		$(this).attr('disabled', 'disabled');
	});

	// Register a change listener for the post type combo box
	$('select#cbo_posttype_select').change(function () {

		// Hide the text post entry
		$('#newtextpostentry').fadeOut(500);

		// Hide the image post entry
		$('#newimagepostentry').fadeOut(500);

		// Determine which post type should be displayed
		switch ($(this).val()) {
			case "textpost":
				$('#newtextpostentry').fadeIn(500);
				break;
			case 'imagepost':
				$('#newimagepostentry').fadeIn(500);
				break;
			default:
				break;
		}
	});

	// Register a click listener for the cancel new text post button
	$('button#btn_manage_cancel_newtextpost').click(function () {

		// Update the screen
		$('#newtextpostentry').fadeOut(500);
		$('#newposttype').fadeOut(500);
		$('button#btn_manage_newpost').removeAttr('disabled');
		$('#txt_newtextpost_title').val('');
		$('#txt_newtextpost_body').val('');
		$('select#cbo_posttype_select').val('');
		$('select#cbo_newtextpost_category').val('');
	});

	// Register a click listener for the submit new text post button
	$('button#btn_manage_submit_newtextpost').click(function () {

		// Create a new request object
		var postData = new Object();
		postData.title = $('#txt_newtextpost_title').val();
		postData.body = $('#txt_newtextpost_body').val();
		postData.category = $('#cbo_newtextpost_category').val();
		postData.type = 'textpost';
		postData.mode = 1;
		postData.id = 0;

		// Convert the object to json
		var query = JSON.stringify(postData);

		// Attempt to submit the new post
		$.ajax({
			type: "POST",
			url: "post.php",
			dataType: "json",
			data: { json: query },
			success: function (data) {

				// Get the reponse data
				var response = data;

				// Check to see the response data
				if (response.status == 0) {

					// Display an error message to the user
					displayMessage("There was an error in the post", 2);
				} else if (response.status == -1) {

					// Display an error message to the user
					displayMessage("One or more fields are blank", 2);
				} else if (response.status == -2) {

					// Display an error message to the user
					displayMessage("There was an error submitting your post, please try again", 2);
				} else if (response.status == 1) {

					// Update the screen display
					$('#newtextpostentry').fadeOut(500);
					$('#newposttype').fadeOut(500);
					$('button#btn_manage_newpost').removeAttr('disabled');

					// Display a sucess message to the user
					displayMessage("Your new post has been successfully posted", 1);

					// Reset the entry form
					$('#txt_newtextpost_title').val('');
					$('#txt_newtextpost_body').val('');
					$('select#cbo_posttype_select').val('');

					// Load the post list
					loadPostList(0);
				}
			}
		});
	});

	// Register a click listener for the cancel new image post button
	$('button#btn_manage_cancel_newimagepost').click(function () {

		// Update the screen
		$('#newimagepostentry').fadeOut(500);
		$('#newposttype').fadeOut(500);
		$('button#btn_manage_newpost').removeAttr('disabled');
		$('#txt_newimagepost_title').val('');
		$('#txt_newimagepost_body').val('');
		$('select#cbo_posttype_select').val('');
		$('#txt_newimagepost_file').val('');
		$('txt_newimagepost_filename').val('');
		$('select#cbo_newimagepost_category').val('');
	});

	// Register a click listener for the submit new image post button
	$('button#btn_manage_submit_newimagepost').click(function () {

		// Portions of the below code is based on the code from http://stackoverflow.com/questions/2320069/jquery-ajax-file-upload
		var file = document.getElementById('txt_newimagepost_file');

		// Check to see if an image has been selected
		if (file.files[0] == null) {
			displayMessage("No image has been selected", 2);
			return false;
		}

		var filedetails = file.files[0];

		// Check to see if the file is of a supported file type
		if (filedetails.type != 'image/png' && filedetails.type != 'image/jpg' && !filedetails.type != 'image/gif' && filedetails.type != 'image/jpeg') {
			displayMessage("The selected file type is unsupport", 2);
			$(this).val('');
			return false;
		}

		// Check to see if the size of the file is valid
		if (filedetails.size > 10000000) {
			displayMessage("The selected file is to large", 2);
			$(this).val('');
			return false;
		}

		// Attempt to upload the image
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function () {
			if (xhr.readyState == 4 && xhr.status == 200) {

				// Check the response status
				var response = $.parseJSON(xhr.responseText);
				if (response.status == 0) {
					$('#txt_newimagepost_filename').val(response.filename);

					// Create a new request object
					var postData = new Object();
					postData.title = $('#txt_newimagepost_title').val();
					postData.body = $('#txt_newimagepost_body').val();
					postData.filename = $('#txt_newimagepost_filename').val();
					postData.category = $('#cbo_newimagepost_category').val();
					postData.type = 'imagepost';
					postData.mode = 1;
					postData.id = 0;

					// Convert the object to json
					var query = JSON.stringify(postData);

					// Attempt to submit the new post
					$.ajax({
						type: "POST",
						url: "post.php",
						dataType: "json",
						data: { json: query },
						success: function (data) {

							// Get the reponse data
							var response = data;

							// Check to see the response data
							if (response.status == 0) {

								// Display an error message to the user
								displayMessage("There was an error in the post", 2);
							} else if (response.status == -1) {

								// Display an error message to the user
								displayMessage("One or more fields are blank", 2);
							} else if (response.status == -2) {

								// Display an error message to the user
								displayMessage("There was an error submitting your post, please try again", 2);
							} else if (response.status == 1) {

								// Update the screen display
								$('#newimagepostentry').fadeOut(500);
								$('#newposttype').fadeOut(500);
								$('button#btn_manage_newpost').removeAttr('disabled');

								// Display a sucess message to the user
								displayMessage("Your new post has been successfully posted", 1);

								// Reset the entry form
								$('#txt_newimagepost_title').val('');
								$('#txt_newimagepost_body').val('');
								$('select#cbo_posttype_select').val('');
								$('#txt_newimagepost_file').val('');
								$('txt_newimagepost_filename').val('');
								$('select#cbo_newimagepost_category').val('');

								// Load the post list
								loadPostList(0);
							}
						}
					});

					return true;
				} else {
					$('#txt_newimagepost_filename').val('');
					displayMessage('There was an error uploading the image', 2);
					return false;
				}
			}
		}
		xhr.open('POST', 'fileupload.php', true);
		xhr.setRequestHeader("X-File-Name", filedetails.name);
		xhr.setRequestHeader("X-File-Type", filedetails.type);
		xhr.setRequestHeader("Content-Type", "application/octet-stream");
		xhr.send(filedetails);

	});
});

/**
 * Load the site details
 */
function loadSiteDetails() {

	// Check to ensure that the site details should be displayed
	if ($('#txt_manage_sitetitle').length > 0) {

		// Build the request object
		var listData = new Object();
		listData.start = 0;
		listData.size = numberOfPostsToLoad;

		// Convert the object to json
		var query = JSON.stringify(listData);

		// Attempt to load the post list
		$.ajax({
			type: "POST",
			url: "loaddetails.php",
			dataType: "json",
			data: { json: query },
			success: function (data) {

				// Get the response data
				var response = data;

				// Check to see the response status
				if (response.status == 0 || response.status == -1 || response.status == -2) {

					// Display error message
					displayMessage('An error occurred while getting the site details', 2);
				} else {

					var details = response.details;

					// Update the site details form
					$('#txt_manage_sitetitle').val(details.title);
					$('#txt_manage_sitedesc').val(details.description);
				}
			}
		});
	}
}

/**
 * Updates the site details
 */
function updateSiteDetails() {

	// Check to ensure that the site details should be displayed
	if ($('#txt_manage_sitetitle').length > 0) {

		// Build the request object
		var siteData = new Object();
		siteData.title = $('#txt_manage_sitetitle').val();
		siteData.description = $('#txt_manage_sitedesc').val();

		// Convert the object to json
		var query = JSON.stringify(siteData);

		// Attempt to load the post list
		$.ajax({
		    type: "POST",
		    url: "savedetails.php",
		    dataType: "json",
		    data: { json: query },
		    success: function (data) {

		        // Get the response data
		        var response = data;

		        // Check to see the response status
		        if (response.status == 0 | response.status == -2) {

		            // Display error message
		            displayMessage('An error occurred while updating the site details', 2);
		        } else if (response.status == -1) {

		            // The fields can not be blank
		            displayMessage('One or more of the fields are blank', 2);
		        } else {

		            // Display success message
		            displayMessage('The site details have been updated', 1);

		            // Update the site header
		            $('#site_title').html(siteData.title);
		            $('header h4').html(siteData.description);
		            window.document.title = siteData.title;
		        }
		    }
		});
	}
}

/**
* Load the post list
*
*/
function loadPostList(position) {

	// Check to ensure that the post list should be displayed
	if ($('#postlist').length > 0) {

		// Check to see if the position is the start or the end
		if (position == 0) {
			position = 0;
		} else {
			position = $('#postlist').children().size();
		}

		// Build the request object
		var listData = new Object();
		listData.start = position;
		listData.size = numberOfPostsToLoad;

		// Convert the object to json
		var query = JSON.stringify(listData);

		// Attempt to load the post list
		$.ajax({
			type: "POST",
			url: "listposts.php",
			dataType: "json",
			data: { json: query },
			success: function (data) {

				// Get the response data
				var response = data;

				// Check to see the response status
				if (response.status == 0 || response.status == -1) {

					// No additional posts
					displayMessage("No more posts are currently available", 3);
				} else {

					// Get the posts data
					var posts = response.posts;

					// Loop through the posts
					for (var i = 0; i < posts.length; i++) {

						// Setup the initial post details
						var element = 'post' + posts[i].id;
						var posthold = $('<div id="' + element + '" class="post"><div>');

						// Check to ensure the post is not already visible
						if ($('#' + element).length == 0) {

							var post = '';

							// Check to see if the post type is a text post
							if (posts[i].type == 4) {
								post += '<h2>' + posts[i].title + '</h2><input type="text" id="txt_title" /><p>' + posts[i].details.replace(/[\r\n]/g, "<br />") + '</p><textarea id="txt_body"></textarea>';
								post += '<span class="buttons"><button id="btn_update_post">Save Changes Post</button>&nbsp;&nbsp;&nbsp;<button id="btn_cancel">Cancel</button></span>';
								post += '<span class="footer">Written by ' + posts[i].author + '&nbsp;</span>&nbsp;<span class="formatteddate">0 seconds</span><span>&nbsp;ago</span>';
								post += '<span class="footer">&nbsp;and filed under ' + posts[i].categoryname + '</span>';
							} else if (posts[i].type == 8) {
								post += '<h2>' + posts[i].title + '</h2><input type="text" id="txt_title" /><img src="./previewimage.php?f=' + posts[i].filename + '" class="manageimage" /><p>' + posts[i].details.replace(/[\r\n]/g, "<br />") + '</p><textarea id="txt_body"></textarea>';
								post += '<span class="buttons"><button id="btn_update_post">Save Changes Post</button>&nbsp;&nbsp;&nbsp;<button id="btn_cancel">Cancel</button></span>';
								post += '<span class="footer">Written by ' + posts[i].author + '&nbsp;</span>&nbsp;<span class="formatteddate">0 seconds</span><span>&nbsp;ago</span>';
								post += '<span class="footer">&nbsp;and filed under ' + posts[i].categoryname + '</span>';
							}
							post += '<div class="postid">' + posts[i].id + '</div>';
							post += '<div class="posttype">' + posts[i].type + '</div>';
							post += '<div class="postdate">' + posts[i].dateposted + '</div>';
							post += '<div class="postcategoryid">' + posts[i].categoryid + '</div>';
							post += '<div class="edit" title="Edit Post"></div>';
							post += '<div class="delete" title="Delete Post"></div>';
							post += '<div class="clear"></div>';

							post = $(post);

							// Determine the position to add the element
							var insertPosition = determinePostInsertPosition(posts[i].dateposted);

							// Check to see if the element is null and the new element should be added to the bottom
							if (insertPosition == null) {

								// Add the post to the screen
								$(posthold).hide().appendTo('#postlist').fadeIn(2000);
								$(posthold).append(post);
							} else {

								// Add the post to the screen in the correct position
								$(insertPosition).before($(posthold));
								$(posthold).append(post);
							}

							// Register a mouse enter listener for the post
							$('#' + element).on('mouseenter', function () {

								// Update the display
								$(this).clearQueue();
								$(this).animate({
									backgroundColor: '#dddddd'
								}, 1000);
								$(this).children('.edit').fadeIn(1000);
								$(this).children('.delete').fadeIn(1000);
							});

							// Register a mouse leave listener for the post
							$(posthold).on('mouseleave', function () {

								// Update the display
								$(this).animate({
									backgroundColor: '#ffffff'
								}, 1000);
								$(this).children('.edit').fadeOut(500);
								$(this).children('.delete').fadeOut(500);
							});

							// Register a click listener for the delete button on the post
							$(posthold).children('.delete').on('click', function () {

								// Delete the post
								deletePost($(this).parent());
							});

							// Register a click listener for the edit button on the post
							$(posthold).children('.edit').on('click', function () {

								// Edit the post
								editPost($(this).parent());
							});

							// Register a click listener for the update post button on the post
							$(posthold).children('.buttons').children('#btn_update_post').on('click', function () {

								// Update the content of the post
								updateEditPost($(this).parent().parent());
							});

							// Register a click listener for the cancel update button on the post
							$(posthold).children('.buttons').children('#btn_cancel').on('click', function () {

								// Cancel the edit
								cancelEditPost($(this).parent().parent());
							});
						} else {

						}
					}

					// Update the date and time on the posts
					updateDateTime();

					// Set the timer to update the date time in the post footer if not set
					if (timeOut == null) {
						timeOut = setInterval(function () { updateDateTime() }, 1000);
					}
				}
			}
		});
	}
}

/**
* Load the user list
*
*/
function loadUserList() {

	// Check to ensure the user list should be displayed
	if ($('#userlist').length > 0) {

		// Build the request object
		var listData = new Object();
		listData.start = $('#userlist').children().size();
		listData.size = numberOfUsersToLoad;

		// Convert the object to json
		var query = JSON.stringify(listData);

		// Attempt to load the user list
		$.ajax({
			type: "POST",
			url: "listusers.php",
			dataType: "json",
			data: { json: query },
			success: function (data) {

				// Get the response data
				var response = data;

				// Check to see the response status
				if (response.status == 0 || response.status == -1) {

					// No additional users
					displayMessage("No more users are currently available", 3);
				} else {

					// Load the users
					var users = response.users;

					// Loop through the users
					for (var i = 0; i < users.length; i++) {

						// Setup the initial user details
						var element = 'user' + users[i].id;
						var userhold = $('<div id="' + element + '" class="user"><div>');

						// Check to ensure the user is not already exist
						if ($('#' + element).length == 0) {

							var user = '';

							// Build the user details
							user += '<h2>' + users[i].username + '</h2>';
							user += '<p>First Name: ' + users[i].firstname + '</p>';
							user += '<p>Last Name: ' + users[i].lastname + '</p>';
							user += '<span class="footer">Registered&nbsp;</span>&nbsp;<span class="formatteddate">0 seconds</span><span>&nbsp;ago</span>';

							user += '<div class="userid">' + users[i].id + '</div>';
							user += '<div class="dateregistered">' + users[i].dateregistered + '</div>';
							user += '<div class="accountstatus">' + users[i].accountstatus + '</div>';
							user += '<div class="unlock" title="Unlock User"></div>';
							user += '<div class="lock" title="Lock User"></div>';
							user += '<div class="clear"></div>';

							// Add the user to the screen
							user = $(user);
							$(userhold).hide().appendTo('#userlist').fadeIn(2000);
							$(userhold).append(user);

							// Register a mouse enter listener for the user
							$('#' + element).on('mouseenter', function () {

								// Update the screen
								$(this).clearQueue();
								$(this).animate({
									backgroundColor: '#dddddd'
								}, 1000);

								// Update the lock status controls based on the account
								var status = $(this).children('.accountstatus').text();
								if (status == 1) {
									$(this).children('.lock').fadeIn(1000);
								} else if (status == 2) {
									$(this).children('.unlock').fadeIn(1000);
								}
							});

							// Register a mouse leave listener for the user
							$(userhold).on('mouseleave', function () {

								// Update the screen
								$(this).animate({
									backgroundColor: '#ffffff'
								}, 1000);

								// Update the lock status controls based on the account
								var status = $(this).children('.accountstatus').text();
								if (status == 1) {
									$(this).children('.lock').fadeOut(500);
								} else if (status == 2) {
									$(this).children('.unlock').fadeOut(500);
								}
							});

							// Register a click listener on the lock button for the user
							$(userhold).children('.lock').on('click', function () {

								// Lock the user
								lockUser($(this).parent());
							});

							// Register a click listener on the unlock button for the user
							$(userhold).children('.unlock').on('click', function () {

								// Unlock the user
								unlockUser($(this).parent());
							});
						} else {

						}
					}

					// Update the date and time on the posts
					updateDateTime();

					// Set the timer to update the date time in the post footer if not set
					if (timeOut == null) {
						timeOut = setInterval(function () { updateDateTime() }, 1000);
					}
				}
			}
		});

	}
}

/**
* Determines the position in the list of posts to insert the new post
*
* @param date The date for the new new post
* @return Returns the element to insert before or null if the new post should go at the bottom of the list
*
*/
function determinePostInsertPosition(date) {

	// Declare variable
	var insertBeforeElement = null;

	// Loop through the current posts to determine if the new post should go before any of them
	$('#postlist').children().each(function () {
		var postdate = $(this).children('.postdate').html();

		// Check to see if the date of the new comment is after the current comment
		if (date > postdate && insertBeforeElement == null) {
			insertBeforeElement = $(this);
		}
	});

	// Return the value
	return insertBeforeElement;
}

/**
* Update the date time display
*
*/
function updateDateTime() {

	// Check to see if the post list is visible
	if ($('#postlist').length > 0) {

		// Get the post list
		var posts = $('#postlist');

		// Loop through the posts and update the date time
		posts.children().each(function () {
			var n = formatDate($(this).children('.postdate').html());
			$(this).children('.formatteddate').html(n);
		});
	}

	// Check to see if the user list is visible
	if ($('#userlist').length > 0) {

		// Get the user list
		var posts = $('#userlist');

		// Loop through the users and update the date time
		posts.children().each(function () {
			var n = formatDate($(this).children('.dateregistered').html());
			$(this).children('.formatteddate').html(n);
		});
	}
}

/**
* Update the selected post
*
* @param post The reference to the post element
*
*/
function updateEditPost(post) {

	// Create the post request object
	var postData = new Object();
	postData.title = $(post).children('#txt_title').val();
	postData.body = $(post).children('#txt_body').val();
	postData.category = $(post).children('.postcategoryid').html();
	postData.type = 'textpost';
	postData.mode = 2
	postData.id = $(post).children('.postid').html();

	// Convert the object to json
	var query = JSON.stringify(postData);

	// Attempt to edit the selected post
	$.ajax({
		type: "POST",
		url: "post.php",
		dataType: "json",
		data: { json: query },
		success: function (data) {

			// Get the response data
			var response = data;

			// Check to see the response data
			if (response.status == 0) {

				// Display an error message to the user
				displayMessage("There was an error in the post", 2);
			} else if (response.status == -1) {

				// Display an error message to the user
				displayMessage("One or more fields are blank", 2);
			} else if (response.status == -2) {

				// Display an error message to the user
				displayMessage("There was an error updating your post, please try again", 2);
			} else if (response.status == 1) {

				// Update the post display
				$(post).children('h2').html($(post).children('#txt_title').val());
				$(post).children('p').html($(post).children('#txt_body').val().replace(/[\r\n]/g, "<br />"));

				// Cancel the edit
				cancelEditPost(post);

				// Display a success message to the user
				displayMessage("Your changes have been successfully saved", 1);
			}
		}
	});
}

/**
* Cancel editing the post
*
* @param post The reference to the post element
*
*/
function cancelEditPost(post) {

	// Check to see if a post is currently selected
	if (selectedPost != null) {

		// Create a select object
		var selectedData = new Object();
		selectedData.id = $(selectedPost).children('.postid').html();
		selectedData.type = $(selectedPost).children('.posttype').html();

		// Check to see if the object type is text type
		if (selectedData.type == 4) {
			$(selectedPost).children('#txt_title').hide();
			$(selectedPost).children('#txt_body').hide();
			$(selectedPost).children('.buttons').hide();
			$(selectedPost).children('h2').fadeIn(1000);
			$(selectedPost).children('p').fadeIn(1000);
		}

		selectedPost = null;
	}
}

/**
* Edit the selected post
*
* @param post The reference to the selected post
*
*/
function editPost(post) {

	// Cancel editing the post
	cancelEditPost(post);

	// Set the selected post
	selectedPost = post;

	// Create a post object
	var postData = new Object();
	postData.id = $(post).children('.postid').html();
	postData.type = $(post).children('.posttype').html();

	// Check to see if the post type is text post
	if (postData.type == 4) {

		// Update the screen
		$(post).children('h2').hide();
		$(post).children('p').hide();
		$(post).children('#txt_title').val($(post).children('h2').html());
		$(post).children('#txt_body').val($(post).children('p').html().replace(/<br>/g, "\r\n"));
		$(post).children('#txt_title').fadeIn(1000);
		$(post).children('#txt_body').fadeIn(1000);
		$(post).children('.buttons').fadeIn(1000);
		$('button').button();
	} else if (postData.type == 8) {

		// Display a message dialog prompting the user to confirm they want to delete the selected post
		$('#dialog-confirm').attr('title', 'Edit Post');
		$('#dialog-confirm').html("This action is not currently supported for image posts.");
		$("#dialog-confirm").dialog({
			resizable: false,
			width: 400,
			height: 140,
			modal: true,
			buttons: {
				"OK": function () {

					$(this).dialog("close");
				}
			}
		});
	}
}

/**
* Delete the selected post
*
* @param post The reference to the select post
*
*/
function deletePost(post) {

	// Display a message dialog prompting the user to confirm they want to delete the selected post
	$('#dialog-confirm').attr('title', 'Delete Post');
	$('#dialog-confirm').html("Are you sure you want to delete the selected post? This action can not be undone.");
	$("#dialog-confirm").dialog({
		resizable: false,
		width: 400,
		height: 140,
		modal: true,
		buttons: {
			"Yes": function () {

				// Create the post object
				var postData = new Object();
				postData.id = $(post).children('.postid').html();
				postData.type = $(post).children('.posttype').html();

				// Convert the object to json
				var query = JSON.stringify(postData);

				// Attempt to delete the selected post
				$.ajax({
					type: "POST",
					url: "deletepost.php",
					dataType: "json",
					data: { json: query },
					success: function (data) {

						// Get the response data
						var response = data;

						// Check to see the response status
						if (response.status == 0 || response.status == -1) {

							// Display an error message to the user
							displayMessage("There was an error deleting the select post, please try again", 2);
						} else {

							// Display a success message to the user
							displayMessage("The selected post has been successfully deleted", 1);
							$(post).fadeOut(1000);
						}
					}
				});
				$(this).dialog("close");
			},
			"No": function () {
				$(this).dialog("close");
			}
		}
	});
}

/**
* Locks the selected user account
*
* @param user The reference to the selected user
*
*/
function lockUser(user) {

	// Display a message dialog prompting the user to confirm they want to lock the selected user account
	$('#dialog-confirm').attr('title', 'Lock User Account');
	$('#dialog-confirm').html("Are you sure you want to lock the selected user account?");
	$("#dialog-confirm").dialog({
		resizable: false,
		width: 400,
		height: 140,
		modal: true,
		buttons: {
			"Yes": function () {

				// Create the user object
				var userData = new Object();
				userData.userid = $(user).children('.userid').html();

				// Convert the object to json
				var query = JSON.stringify(userData);

				// Attempt to lock the selected user
				$.ajax({
					type: "POST",
					url: "lockuser.php",
					dataType: "json",
					data: { json: query },
					success: function (data) {

						// Get the response data
						var response = data;

						// Check to see the response status
						if (response.status == 0 || response.status == -1) {

							// Display an error message to the user
							displayMessage("There was an error locking the selected user account, please try again", 2);
						} else {

							// Display a success message to the user
							displayMessage("The selected user account has been successfully locked", 1);
							$(user).children('.accountstatus').html(2);
						}
					}
				});
				$(this).dialog("close");
			},
			"No": function () {
				$(this).dialog("close");
			}
		}
	});
}

/**
* Unlocks the selected user account
*
* @param user The reference to the selected user
*
*/
function unlockUser(user) {

	// Display a message dialog prompting the user to confirm they want to unlock the selected user account
	$('#dialog-confirm').attr('title', 'Unlock User Account');
	$('#dialog-confirm').html("Are you sure you want to unlock the selected user account?");
	$("#dialog-confirm").dialog({
		resizable: false,
		width: 400,
		height: 140,
		modal: true,
		buttons: {
			"Yes": function () {

				// Create the user object
				var userData = new Object();
				userData.userid = $(user).children('.userid').html();

				// Convert the object to json
				var query = JSON.stringify(userData);

				// Attempt to unlock the selected user
				$.ajax({
					type: "POST",
					url: "unlockuser.php",
					dataType: "json",
					data: { json: query },
					success: function (data) {

						// Get the response data
						var response = data;

						// Check to see the response status
						if (response.status == 0 || response.status == -1) {

							// Display an error message to the user
							displayMessage("There was an error unlocking the selected user account, please try again", 2);
						} else {

							// Display a success message to the user
							displayMessage("The selected user account has been successfully unlocked", 1);
							$(user).children('.accountstatus').html(1);
						}
					}
				});
				$(this).dialog("close");
			},
			"No": function () {
				$(this).dialog("close");
			}
		}
	});
}