/**
* Description: Contains support features for the application
* Filename...: script.js
* Author.....: Dillon Young (C0005790)
* 
*/

// Check to see if the document is ready
$(document).ready(function () {

    // Get the login status of the current user
    $.getJSON('login-status.php', function (result) {

        // Check to see if the user is logged in or not
        if (result.status == 0 || result.status == -1) {

            // Display the login dialog
            showLoginDialog();
        } else if (result.status == 1) {

            // Display the logout dialog
            showLogoutDialog(result);
        }

    });

    // Add a listener to the title of the page
    $('h1#site_title').click(function () {
        window.location.assign('./');
    });
});

/**
 * Show the login form dialog
 *
 */
function showLoginDialog() {

    // Build the login form
    var loginform;
    loginform = '<a href=\"#\" id=\"login\">Login</a>&nbsp;&nbsp;&nbsp;<a href=\"./register.php\" id=\"register\">Register</a><div id=\"login_form\">' +
                '<p>Username: <input type=\"text\" name=\"txt_login_username\" id=\"txt_login_username\" /></p>' +
                '<p>Password: <input type=\"password\" name=\"txt_login_password\" id=\"txt_login_password\" /></p>' +
                '<p><button id=\"btn_login_login\">Login</button>&nbsp;<button id=\"btn_login_cancel\">Cancel</button></div>';

    // Add the login form to the page
    $('#login_area').html(loginform);

    // Update the style of the buttons
    $('button').button();

    // Register a click listener for the login link
    $('a#login').click(function () {

        // Update the screen display
        $('a#login').css("display", "none");
        $('a#register').css("display", "none");
        $('#login_form').css("display", "block");
    });

    // Register a click listener for the cancel link
    $('button#btn_login_cancel').click(function () {

        // Update the screen display
        $('a#login').css("display", "block");
        $('a#register').css("display", "block");
        $('#login_form').css("display", "none");
    });

    // Register a click listener for the login button
    $('button#btn_login_login').click(function () {

        // Build a new request object
        var loginData = new Object();
        loginData.username = $('#txt_login_username').val();
        loginData.password = $('#txt_login_password').val();

        // Convert the object to json
        var query = JSON.stringify(loginData);

        // Attempt to login the current user
        $.ajax({
            type: "POST",
            url: "login.php",
            dataType: "json",
            data: { json: query },
            success: function (data) {

                // Get the response data
                var response = data;

                // Check to see the response status
                if (response.status == 0 || response.status == -1) {

                    // Display an error message to the user
                    displayMessage("Your login attempt was unsuccessful", 2);
                } else if (response.status == -5) {

                    // Display an error message stating the account is locked
                    displayMessage("Your account is currently locked, please contact support", 2);
                } else if (response.status == 1) {

                    // Display a success message to the user
                    displayMessage("You have successfully logged in", 1);

                    // Show the logout form
                    showLogoutDialog(response);

                    // Update the page display
                    updatePageDisplay();
                }
            }
        });

    });
}

/**
* Show the login form dialog
*
* @param result The request result
*/
function showLogoutDialog(result) {

    // Build the logout form
    var loginform;
    loginform = result.username + '&nbsp;&nbsp;';
    if (result.level == 3) {
        loginform += '<a href=\"manage.php\">Manage</a>&nbsp;&nbsp;';
    }
    loginform += '<a href=\"#\" id=\"logout\">Logout</a>';

    // Add the logout form to the page
    $('#login_area').html(loginform);

    // Register a click listener to the logout link
    $('a#logout').click(function () {

        // Create a new request object
        var loginData = new Object();
        loginData.username = $('#txt_login_username').val();
        loginData.password = $('#txt_login_password').val();

        // Convert the object to json
        var query = JSON.stringify(loginData);

        // Attempt to log the current user out
        $.ajax({
            type: "POST",
            url: "logout.php",
            dataType: "json",
            data: { json: query },
            success: function (data) {

                // Get the response data
                var response = data;

                // Check to see the response data
                if (response.status == 0 || response.status == -1) {

                    // Diaplay an error message to the user
                    displayMessage("An error has occurred while attempting to log you out", 2);
                } else if (response.status == 1) {

                    // Display a success message to the user
                    displayMessage("You have been successfully logged out", 1);

                    // Show the login form
                    showLoginDialog();

                    // Update the page display
                    updatePageDisplay();
                }
            }
        });
    });
}

/**
 * Update the page display
 *
 */
function updatePageDisplay() {

    // Check to ensure the page has a comments section
    if ($('#postcommenthead').length > 0) {

        // Load the post details
        loadPostDetails();

        // Load the comment details
        loadPostComments();
    }
}