$(document).ready(function () {

    loadPostList();

    $('button').button();

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
                    $('select#cbo_posttype_select').val('');
                    loadPostList();
                }
            }
        });
    });
});

function loadPostList() {

    if ($('#postlist').length > 0) {
        var listData = new Object();
        listData.start = 0;
        listData.size = 10;
        var query = JSON.stringify(listData);

        $.ajax({
            type: "POST",
            url: "listposts.php",
            dataType: "json",
            data: { json: query },
            success: function (data) {
                var response = data;

                if (response.status == 0 || response.status == -1) {
                    displayMessage("One or more fields are blank", 2);
                } else {
                    var posts = response.posts;
                    for (var i = 0; i < posts.length; i++) {
                        var element = 'post' + i;
                        var posthold = $('<div id="' + element + '" class="post"><div>');

                        if ($('#' + element).length == 0) {
                            var post = '';
                            if (posts[i].type == 4) {
                                post += '<h2>' + posts[i].title + '</h2><p>' + posts[i].details + '</p><h3>Written by ' + posts[i].author + ' ' + posts[i].dateposted + ' </h3>';
                            }
                            post += '<div class="postid">' + posts[i].id + '</div>';
                            post += '<div class="edit"></div>';
                            post += '<div class="delete"></div>';

                            //$(post).appendTo('#postlist');
                            //$('#postlist').prepend(post);
                            post = $(post);
                            $(posthold).hide().prependTo('#postlist').fadeIn(2000);
                            $(posthold).append(post);
                            $('#' + element).on('mouseenter', function () {
                                $(this).children('.edit').fadeIn(1000);
                                $(this).children('.delete').fadeIn(1000);
                            });

                            $(posthold).on('mouseleave', function () {
                                $(this).children('.edit').fadeOut(500);
                                $(this).children('.delete').fadeOut(500);
                            });

                            $(posthold).children('.delete').on('click', function () {
                                //alert($(this).parent().children('.postid').html());
                                deletePost($(this).parent());
                            });
                        } else {

                        }
                    }
                }
            }
        });

    }
}

function deletePost(post) {
    //$(post).fadeOut(1000);
    $('#dialog-confirm').attr('title', 'Delete Post');
    $('#dialog-confirm').html("Are you sure you want to delete the selected post? This action can not be undone.");
    $("#dialog-confirm").dialog({
        resizable: false,
        width: 400,
        height: 140,
        modal: true,
        buttons: {
            "Yes": function () {
                $(this).dialog("close");
                $(post).fadeOut(1000);
            },
            "No": function () {
                $(this).dialog("close");
            }
        }
    });
}