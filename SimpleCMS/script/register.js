/**
* Description: Contains the support features for the registration process
* Filename...: register.js
* Author.....: Dillon Young (C0005790)
* 
*/

// Check to see if the document is ready
$(document).ready(function () {

    // Update the styles on the buttons
    $('button').button();

    // Register a click listener for the new captcha button
    $('button#btn_register_newcaptcha').click(function () {

        // Refresh the captcha
        refreshCaptcha();
        return false;
    });

    // Register a click listener for the cancel button
    $('button#btn_register_cancel').click(function () {

        // Naviagte back to the previous page
        window.history.back();
        return false;
    });

    // Register a click listener for the register button
    $('button#btn_register_register').click(function () {

        // Disable the registration form elements
        disableRegistrationForm();

        // Build the register data object
        var registerData = new Object();
        registerData.firstname = $('#txt_user_firstname').val();
        registerData.lastname = $('#txt_user_lastname').val();
        registerData.username = $('#txt_user_username').val();
        registerData.password1 = $('#txt_user_password1').val();
        registerData.password2 = $('#txt_user_password2').val();
        registerData.captcha = $('#txt_user_captcha').val();

        // Convert the object to json
        var query = JSON.stringify(registerData);

        // Attempt to register the user
        $.ajax({
            type: "POST",
            url: "register-user.php",
            dataType: "json",
            data: { json: query },
            success: function (data) {

                // Get the response data
                var response = data;

                // Check the response status for errors
                if (response.status == 0 || response.status == -1) {

                    // Display an error message to the user
                    displayMessage("Your registration attempt was unsuccessful", 2);

                    // Update the registration error message
                    updateRegistrationErrorMessages(response);

                    // Refresh the captcha
                    refreshCaptcha();

                    // Enable the registration form elements
                    enableRegistrationForm();
                } else if (response.status == 1) {

                    // Display a success message to the user
                    displayMessage("You have successfully registered, you will be redirected back to the previous page", 1);

                    // Show the logout dialog to the user
                    showLogoutDialog(response);
                    window.setTimeout(function () { redirectUser(); }, 5000);
                }
            }
        });

        return false;
    });
});

/**
 * Refresh the current captcha
 *
 */
function refreshCaptcha() {
    var d = new Date();
    $("#captcha_image").attr("src", "./captcha.php?" + d.getTime());
    $('#txt_user_captcha').val('');
}

/**
 * Update the registration error messages
 *
 */
function updateRegistrationErrorMessages(data) {
    $('#err_user_firstname').text(data.error.firstname);
    $('#err_user_lastname').text(data.error.lastname);
    $('#err_user_username').text(data.error.username);
    $('#err_user_password1').text(data.error.password1);
    $('#err_user_password2').text(data.error.password2);
    $('#err_user_captcha').text(data.error.captcha);
}

/**
 * Disable the registration form elements
 *
 */
function disableRegistrationForm() {
    $('#txt_user_firstname').prop('disabled', true);
    $('#txt_user_lastname').prop('disabled', true);
    $('#txt_user_username').prop('disabled', true);
    $('#txt_user_password1').prop('disabled', true);
    $('#txt_user_password2').prop('disabled', true);
    $('#txt_user_captcha').prop('disabled', true);
    $('#btn_register_newcaptcha').prop('disabled', true);
    $('#btn_register_register').prop('disabled', true);
    $('#btn_register_cancel').prop('disabled', true);
}

/**
 * Enable the registration form elements
 *
 */
function enableRegistrationForm() {
    $('#txt_user_firstname').prop('disabled', false);
    $('#txt_user_lastname').prop('disabled', false);
    $('#txt_user_username').prop('disabled', false);
    $('#txt_user_password1').prop('disabled', false);
    $('#txt_user_password2').prop('disabled', false);
    $('#txt_user_captcha').prop('disabled', false);
    $('#btn_register_newcaptcha').prop('disabled', false);
    $('#btn_register_register').prop('disabled', false);
    $('#btn_register_cancel').prop('disabled', false);
}