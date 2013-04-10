/**
* Description: Contains post viewing features for the application
* Filename...: view.js
* Author.....: Dillon Young (C0005790)
* 
*/

var timeOut;
var numberOfCommentsToLoad = 5;
var refreshTime;
var autoRefresh = false;

// Check to see if the document is ready
$(document).ready(function () {

    // Update the styles on the buttons
    $('button').button();

    // Load the post details
    loadPostDetails();

    // Load the post comments
    loadPostComments();

    // Register a click listener to the comment button
    $('button#btn_comment_post').click(function () {

        // Post a comment
        postComment();
    });

    // Register a click listener for the load more comments button
    $('button#btn_loadmorecomments').click(function () {

        // Load additional comments
        loadPostComments();
    });

    // Register a click listener for the auto refresh button
    $('button#btn_autorefresh').click(function () {

        // Toggle the auto refresh
        toggleAutoRefresh();
    });

    // Start the auto refresh
    toggleAutoRefresh();
});

/**
 * Post a new comment to the application
 *
 */
function postComment() {

    // Build a new request object
    var postData = new Object();
    postData.postid = $('#postid').text();
    postData.comment = $('#txt_comment').val();
    postData.mode = 1;

    // Convert the object to json
    var query = JSON.stringify(postData);

    // Attempt to post the comment
    $.ajax({
        type: "POST",
        url: "comment.php",
        dataType: "json",
        data: { json: query },
        success: function (data) {

            // Get the response data
            var response = data;

            // Check to see the response status
            if (response.status == 0 || response.status == -1) {

                // Display an error message to the user
                displayMessage("The comment can not be blank, please try again", 2);
            } else if (response.status == 1) {

                // Display a success message to the user
                displayMessage("Your comment has been successfully posted", 1);
                $('#txt_comment').val('');

                // Load the comments
                loadPostComments(0);
            }
        }
    });
}

/**
 * Load the post details
 *
 */
function loadPostDetails() {

    // Build a new post request object
    var postData = new Object();
    postData.id = $('#postid').text();

    // Convert the object to json
    var query = JSON.stringify(postData);

    // Attempt to load the requested post details
    $.ajax({
        type: "POST",
        url: "viewpost.php",
        dataType: "json",
        data: { json: query },
        success: function (data) {

            // Get the response data
            var response = data;

            // Check to see the response status
            if (response.status == 0 || response.status == -1) {

                // Display an error message to the user
                displayMessage("There was an error loading the post", 2);
            } else if (response.status == 1) {

                // Display the post details to the user
                $('#postdetail').html(response.postdata).fadeIn(1000);
                updateDateTime();
                timeout = setInterval(function () { updateDateTime() }, 1000);
            }
        }
    });
}

/**
 * Load the comments for the current post
 *
 */
function loadPostComments(position) {

    // Check to see if the position is the start or the end
    if (position == 0) {
        position = 0;
    } else {
        position = $('#postcomments').children().size();
    }

    // Build a post request object
    var postData = new Object();
    postData.id = $('#postid').text();
    postData.start = position;
    postData.size = numberOfCommentsToLoad;

    // Convert the object to json
    var query = JSON.stringify(postData);

    // Attempt to load the comments for the current post
    $.ajax({
        type: "POST",
        url: "viewcomments.php",
        dataType: "json",
        data: { json: query },
        success: function (data) {

            // Get the response data
            var response = data;

            // Check to see the response status
            if (response.status == 0 || response.status == -1) {

                // Display an error message to the user
                displayMessage("There was an error loading the comments", 2);
            } else if (response.status == -2) {

                // Check to see if there are comments already in the list
                if ($('#postcomments').children().size() == 0) {

                    // Display a message stating no has commented yet
                    $('#postnocomments').fadeIn(1000);
                    $('#nouserloggedin').fadeOut(500);
                    $('#postnewcomment').fadeIn(1000);
                } else {

                    // No additional comments
                    displayMessage("No more comments are currently available", 3);
                }
            } else if (response.status == 1) {


                // Check to see if the current user is not logged in and hide the input form
                if (response.userid == 0) {

                    $('#postnewcomment').hide();
                    $('#nouserloggedin').show();
                } else {

                    $('#postnewcomment').show();
                    $('#nouserloggedin').hide();
                }

                // Show the comments
                $('#postnocomments').hide();
                //$('#nouserloggedin').fadeOut(500);
                //$('#postnewcomment').fadeIn(1000);
                $('#postcomments').fadeIn(1000);

                // Get the list of comments
                var comments = response.comments;

                // Loop through the comments
                for (var i = 0; i < comments.length; i++) {

                    // Setup the initial comment details
                    var element = 'comment' + comments[i].id;
                    var commenthold = $('<div id="' + element + '" class="comment"><div>');

                    // Check to ensure the comment does not already exist
                    if ($('#' + element).length == 0) {

                        // Build the comment
                        var comment = '';
                        comment += '<img src="./images/user.png" />';
                        if (!isNaN(comments[i].details)) {
                            comment += '<p>' + comments[i].details + '</p>';
                        } else {
                            comment += '<p>' + comments[i].details.replace(/[\r\n]/g, "<br />") + '</p>';
                        }
                        comment += '<div class="space"></div>';
                        comment += '<span class="footer">Written by ' + comments[i].author + '&nbsp;</span>&nbsp;<span class="formatteddate">0 seconds</span><span>&nbsp;ago</span>';
                        comment += '<div class="postid">' + comments[i].id + '</div>';
                        comment += '<div class="postdate">' + comments[i].dateposted + '</div>';

                        // Check to see if the user is an admin
                        if (response.admin) {
                            comment += '<div class="delete" title="Delete Comment"></div>';
                        }
                        comment += '<div class="clear"></div>';
                        comment = $(comment);

                        // Determine the position to add the element
                        var insertPosition = determineCommentInsertPosition(comments[i].dateposted);

                        // Check to see if the element is null and the new element should be added to the bottom
                        if (insertPosition == null) {

                            // Add the comment to the screen
                            $(commenthold).hide().appendTo('#postcomments').fadeIn(2000);
                            $(commenthold).append(comment);
                        } else {

                            // Add the comment to the screen in the correct position
                            $(insertPosition).before($(commenthold));
                            $(commenthold).append(comment);
                        }

                        // Check to see if the current user is an admin
                        if (response.admin) {

                            // Register a mouse enter listener for the comment
                            $('#' + element).on('mouseenter', function () {

                                // Update the display
                                $(this).clearQueue();
                                $(this).animate({
                                    backgroundColor: '#dddddd'
                                }, 1000);
                                $(this).children('.delete').fadeIn(1000);
                            });

                            // Register a mouse leave listener for the comment
                            $(commenthold).on('mouseleave', function () {

                                // Update the display
                                $(this).animate({
                                    backgroundColor: '#ffffff'
                                }, 1000);
                                $(this).children('.delete').fadeOut(500);
                            });

                            // Register a click listener for the delete button on the comment
                            $(commenthold).children('.delete').on('click', function () {

                                // Delete the comment
                                deleteComment($(this).parent());
                            });
                        }
                    }
                }
            }
        }
    });
}

/**
 * Determines the position in the list of comments to insert the new comment
 *
 * @param date The date for the new new comment
 * @return Returns the element to insert before or null if the new comment should go at the bottom of the list
 *
 */
function determineCommentInsertPosition(date) {

    // Declare variable
    var insertBeforeElement = null;

    // Loop through the current comments to determine if the new comment should go before any of them
    $('#postcomments').children().each(function () {
        var commentdate = $(this).children('.postdate').html();

        // Check to see if the date of the new comment is after the current comment
        if (date > commentdate && insertBeforeElement == null) {
            insertBeforeElement = $(this);
        }
    });

    // Return the value
    return insertBeforeElement;
}

/**
* Update the date time for the post and comments
*
*/
function updateDateTime() {

    // Update the date time for the post
    var posts = $('#postdetail');
    var n = formatDate($(posts).children('.postdate').html());
    $(posts).children('.formatteddate').html(n);

    // Get the comment list
    var posts = $('#postcomments');

    // Loop through the comments and update the date time for the comment
    posts.children().each(function () {
        var n = formatDate($(this).children('.postdate').html());
        $(this).children('.formatteddate').html(n);

    });
}


/**
* Delete the selected comment
*
* @param comment The reference to the select comment
*
*/
function deleteComment(comment) {

    // Create the comment object
    var commentData = new Object();
    commentData.id = $(comment).children('.postid').html();
    commentData.type = 1;

    // Convert the object to json
    var query = JSON.stringify(commentData);

    // Attempt to delete the selected post
    $.ajax({
        type: "POST",
        url: "deletecomment.php",
        dataType: "json",
        data: { json: query },
        success: function (data) {

            // Get the response data
            var response = data;

            // Check to see the response status
            if (response.status == 0 || response.status == -1) {

                // Display an error message to the user
                displayMessage("There was an error deleting the select comment, please try again", 2);
            } else {

                // Display a success message to the user
                displayMessage("The selected comment has been successfully deleted", 1);
                $(comment).fadeOut(1000);
            }
        }
    });

}

/**
 * Toggles the auto refreshing of comments on and off
 *
 */
function toggleAutoRefresh() {

    // Check to see if auto refresh is enabled
    if (autoRefresh) {

        // Turn off the auto refresh
        autoRefresh = false;
        clearInterval(refreshTime);
        $('button#btn_autorefresh span').text('Turn Auto Refresh On');
    } else {

        // Turn on the auto refresh
        autoRefresh = true;
        refreshTime = setInterval(function () { loadPostComments(0) }, 10000);
        $('button#btn_autorefresh span').text('Turn Auto Refresh Off');
    }

    // Update the styles on the buttons
    $('button').button();
}