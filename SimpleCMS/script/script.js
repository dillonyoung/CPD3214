$(document).ready(function () {

    $.getJSON('login-status.php', function (result) {

        if (result.status == 0 || result.status == -1) {
            showLoginDialog(result);
        } else if (result.status == 1) {
            showLogoutDialog(result);
        }

    });

    $('h1#site_title').click(function () {
        window.location.assign('./');
    });
});

function showLoginDialog(result) {
    var loginform;
    loginform = '<a href=\"#\" id=\"login\">Login</a>&nbsp;&nbsp;&nbsp;<a href=\"./register.php\" id=\"register\">Register</a><div id=\"login_form\">' +
                '<p>Username: <input type=\"text\" name=\"txt_login_username\" id=\"txt_login_username\" /></p>' +
                '<p>Password: <input type=\"password\" name=\"txt_login_password\" id=\"txt_login_password\" /></p>' +
                '<p><button id=\"btn_login_login\">Login</button>&nbsp;<button id=\"btn_login_cancel\">Cancel</button></div>';
    $('#login_area').html(loginform);
    $('button').button();

    $('a#login').click(function () {
        $('a#login').css("display", "none");
        $('a#register').css("display", "none");
        $('#login_form').css("display", "block");
    });

    $('button#btn_login_cancel').click(function () {
        $('a#login').css("display", "block");
        $('a#register').css("display", "block");
        $('#login_form').css("display", "none");
    });

    $('button#btn_login_login').click(function () {
        var loginData = new Object();
        loginData.username = $('#txt_login_username').val();
        loginData.password = $('#txt_login_password').val();
        var query = JSON.stringify(loginData);

        $.ajax({
            type: "POST",
            url: "login.php",
            dataType: "json",
            data: { json: query },
            success: function (data) {
                var response = data;

                if (response.status == 0 || response.status == -1) {
                    displayMessage("Your login attempt was unsuccessful", 2);
                } else if (response.status == -5) {
                    displayMessage("Your account is currently locked, please contact support", 2);
                } else if (response.status == 1) {
                    displayMessage("You have successfully logged in", 1);
                    showLogoutDialog(response);
                }
            }
        });

    });
}

function showLogoutDialog(result) {
    var loginform;
    loginform = result.username + '&nbsp;&nbsp;';
    if (result.level == 3) {
        loginform += '<a href=\"manage.php\">Manage</a>&nbsp;&nbsp;';
    }
    loginform += '<a href=\"#\" id=\"logout\">Logout</a>';
    $('#login_area').html(loginform);

    $('a#logout').click(function () {
        var loginData = new Object();
        loginData.username = $('#txt_login_username').val();
        loginData.password = $('#txt_login_password').val();
        var query = JSON.stringify(loginData);

        $.ajax({
            type: "POST",
            url: "logout.php",
            dataType: "json",
            data: { json: query },
            success: function (data) {
                var response = data;

                if (response.status == 0 || response.status == -1) {
                    displayMessage("An error has occurred while attempting to log you out", 2);
                } else if (response.status == 1) {
                    displayMessage("You have been successfully logged out", 1);
                    showLoginDialog(response);
                }
            }
        });
    });
}