/**
* Description: Contains the main support features for the application
* Filename...: main.js
* Author.....: Dillon Young (C0005790)
* 
*/

var timeOut;
var numberOfPostsToLoad = 5;

// Check to see if the document is ready
$(document).ready(function () {

    // Load the post on the main page
    loadMainPostList();

    // Prepare the footer of the posts
    preparePostFooter();

    // Update the button styles
    $('button').button();

    // Register a click listener for the load more posts button
    $('button#btn_loadmoreposts').click(function () {

        // Load additional posts
        loadMainPostList();
    });
});

/**
 * Load the main posts list
 *
 */
function loadMainPostList(position) {

    // Check to ensure the main posts should be displayed
    if ($('#mainpostlist').length > 0) {

        // Check to see if the position is the start or the end
        if (position == 0) {
            position = 0;
        } else {
            position = $('#mainpostlist').children().size();
        }

        // Build the request object
        var listData = new Object();
        listData.start = position;
        listData.size = numberOfPostsToLoad;

        // Convert the object to json
        var query = JSON.stringify(listData);

        // Request the list of posts
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

                    // Get the posts
                    var posts = response.posts;

                    // Loop through the posts
                    for (var i = 0; i < posts.length; i++) {

                        // Setup the initial post details
                        var element = 'post' + posts[i].id;
                        var posthold = $('<div id="' + element + '" class="post"><div>');

                        // Check to ensure that the post does not already exist
                        if ($('#' + element).length == 0) {

                            var post = '';

                            // Check to see if the post type a text post
                            if (posts[i].type == 4) {
                                post += '<h2><a href="./view.php?post=' + posts[i].id + '">' + posts[i].title + '</a></h2><p>' + posts[i].details.replace(/[\r\n]/g, "<br />") + '</p>';
                                post += '<span class="footer">Written by ' + posts[i].author + '&nbsp;</span>&nbsp;<span class="formatteddate">0 seconds</span><span>&nbsp;ago</span>';
                                post += '<span class="footer">&nbsp;and filed under ' + posts[i].categoryname + '</span>';
                                post += '<span class="comments">' + posts[i].comments + '</span>';
                            } else if (posts[i].type == 8) {
                                post += '<h2><a href="./view.php?post=' + posts[i].id + '">' + posts[i].title + '</a></h2><img src="./previewimage.php?f=' + posts[i].filename + '" class="image" /><p>' + posts[i].details.replace(/[\r\n]/g, "<br />") + '</p>';
                                post += '<span class="footer">Written by ' + posts[i].author + '&nbsp;</span>&nbsp;<span class="formatteddate">0 seconds</span><span>&nbsp;ago</span>';
                                post += '<span class="footer">&nbsp;and filed under ' + posts[i].categoryname + '</span>';
                                post += '<span class="comments">' + posts[i].comments + '</span>';
                            }
                            post += '<div class="postid">' + posts[i].id + '</div>';
                            post += '<div class="posttype">' + posts[i].type + '</div>';
                            post += '<div class="postdate">' + posts[i].dateposted + '</div>';
                            post += '<div class="clear"></div>';

                            // Format the date
                            formatDate(posts[i].dateposted);

                            // Add the post to the screen
                            post = $(post);

                            // Determine the position to add the element
                            var insertPosition = determinePostInsertPosition(posts[i].dateposted);

                            // Check to see if the element is null and the new element should be added to the bottom
                            if (insertPosition == null) {

                                // Add the post to the screen
                                $(posthold).hide().appendTo('#mainpostlist').fadeIn(2000);
                                $(posthold).append(post);
                            } else {

                                // Add the post to the screen in the correct position
                                $(insertPosition).before($(posthold));
                                $(posthold).append(post);
                            }
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
    $('#mainpostlist').children().each(function () {
        var postdate = $(this).children('.postdate').html();

        // Check to see if the date of the new post is after the current post
        if (date > postdate && insertBeforeElement == null) {
            insertBeforeElement = $(this);
        }
    });

    // Return the value
    return insertBeforeElement;
}

/**
 * Update the date time in the footer of the posts
 *
 */
function updateDateTime() {

    // Get the main post list
    var posts = $('#mainpostlist');

    // Loop through the posts and update the date time
    posts.children().each(function () {
        var n = formatDate($(this).children('.postdate').html());
        $(this).children('.formatteddate').html(n);

    });
}

/**
 * Prepare the post footer date time
 *
 */
function preparePostFooter() {

    // Check to ensure the post detail should be displayed
    if ($('#postdetail').length > 0) {

        // Update the post date time
        updatePostDateTime();

        // Set the timer to update the date time in the post footer if not set
        if (timeOut == null) {
            timeOut = setInterval(function () { updatePostDateTime() }, 1000);
        }
    }
}

/**
 * Update the post date time
 *
 */
function updatePostDateTime() {

    // Get the post detail
    var posts = $('#postdetail');

    // Update the footer with the updated date time
    var n = formatDate($(posts).children('.postdate').html());
    $(posts).children('.formatteddate').html(n);
}