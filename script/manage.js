$(document).ready(function () {
    $('button#btn_manage_newpost').click(function () {
        $('#newposttype').fadeIn(500);
        $(this).attr('disabled', 'disabled');
    });

    $('select#cbo_posttype_select').change(function () {

        $('#newtextpostentry').fadeOut(500);
        switch ($(this).val()) {
            case "textpost":
                $('#newtextpostentry').fadeIn(500);
                break;
            default:
                break;
        }
    });

    $('button#btn_manage_submit_newtextpost').click(function () {

        var postData = new Object();
        postData.title = $('#txt_newtextpost_title').val();
        postData.body = $('#txt_newtextpost_body').val();
        postData.type = 'textpost';
        var query = JSON.stringify(postData);

        $.ajax({
            type: "POST",
            url: "post.php",
            dataType: "json",
            data: { json: query },
            success: function (data) {
                var response = data;

                if (response.status == 0) {
                    displayMessage("There was an error in the post", 2);
                } else if (response.status == -1) {
                    displayMessage("One or more fields are blank", 2);
                } else if (response.status == -2) {
                    displayMessage("There was an error submitting your post, please try again", 2);
                } else if (response.status == 1) {
                    $('#newtextpostentry').fadeOut(500);
                    $('#newposttype').fadeOut(500);
                    $('button#btn_manage_newpost').removeAttr('disabled');
                    displayMessage("Your new post has been successfully posted", 1);
                    $('#txt_newtextpost_title').val('');
                    $('#txt_newtextpost_body').val('');
                }
            }
        });
    });
});