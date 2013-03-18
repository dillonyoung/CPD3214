$(document).ready(function () {

    $('button').button();

    $('button#btn_register_newcaptcha').click(function () {
        refreshCaptcha();
        return false;
    });

    $('button#btn_register_cancel').click(function () {
        window.history.back();
        return false;
    });

    $('button#btn_register_register').click(function () {

        disableRegistrationForm();

        var registerData = new Object();
        registerData.firstname = $('#txt_user_firstname').val();
        registerData.lastname = $('#txt_user_lastname').val();
        registerData.username = $('#txt_user_username').val();
        registerData.password1 = $('#txt_user_password1').val();
        registerData.password2 = $('#txt_user_password2').val();
        registerData.captcha = $('#txt_user_captcha').val();
        var query = JSON.stringify(registerData);

        $.ajax({
            type: "POST",
            url: "register-user.php",
            dataType: "json",
            data: { json: query },
            success: function (data) {
                var response = data;

                if (response.status == 0 || response.status == -1) {
                    displayMessage("Your registration attempt was unsuccessful", 2);
                    updateRegistrationErrorMessages(response);
                    refreshCaptcha();
                    enableRegistrationForm();
                } else if (response.status == 1) {
                    displayMessage("You have successfully registered, you will be redirected back to the previous page", 1);
                    showLogoutDialog(response);
                    window.setTimeout(function () { redirectUser(); }, 5000);
                }
            }
        });

        return false;
    });
});

function refreshCaptcha() {
    var d = new Date();
    $("#captcha_image").attr("src", "./captcha.php?" + d.getTime());
    $('#txt_user_captcha').val('');
}

function updateRegistrationErrorMessages(data) {
    console.log(data);
    $('#err_user_firstname').text(data.error.firstname);
    $('#err_user_lastname').text(data.error.lastname);
    $('#err_user_username').text(data.error.username);
    $('#err_user_password1').text(data.error.password1);
    $('#err_user_password2').text(data.error.password2);
    $('#err_user_captcha').text(data.error.captcha);
}

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