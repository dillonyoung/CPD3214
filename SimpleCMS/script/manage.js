var selectedPost = null;

$(document).ready(function () {

    loadPostList();

    loadUserList();

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

    $('button#btn_manage_cancel_newtextpost').click(function () {
        $('#newtextpostentry').fadeOut(500);
        $('#newposttype').fadeOut(500);
        $('button#btn_manage_newpost').removeAttr('disabled');
        $('#txt_newtextpost_title').val('');
        $('#txt_newtextpost_body').val('');
        $('select#cbo_posttype_select').val('');
    });

    $('button#btn_manage_submit_newtextpost').click(function () {

        var postData = new Object();
        postData.title = $('#txt_newtextpost_title').val();
        postData.body = $('#txt_newtextpost_body').val();
        postData.type = 'textpost';
        postData.mode = 1;
        postData.id = 0;
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
        listData.size = 30;
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
                                post += '<h2>' + posts[i].title + '</h2><input type="text" id="txt_title" /><p>' + posts[i].details.replace(/[\r\n]/g, "<br />") +'</p><textarea id="txt_body"></textarea>';
                                post += '<span class="buttons"><button id="btn_update_post">Save Changes Post</button>&nbsp;&nbsp;&nbsp;<button id="btn_cancel">Cancel</button></span>';
                                post += '<span class="footer">Written by ' + posts[i].author + '&nbsp;</span>&nbsp;<span class="formatteddate">0 seconds</span><span>&nbsp;ago</span>';
                            }
                            post += '<div class="postid">' + posts[i].id + '</div>';
                            post += '<div class="posttype">' + posts[i].type + '</div>';
                            post += '<div class="postdate">' + posts[i].dateposted + '</div>';
                            post += '<div class="edit" title="Edit Post"></div>';
                            post += '<div class="delete" title="Delete Post"></div>';
                            post += '<div class="clear"></div>';
                            //formatDate(posts[i].dateposted);

                            post = $(post);
                            $(posthold).hide().prependTo('#postlist').fadeIn(2000);
                            $(posthold).append(post);
                            $('#' + element).on('mouseenter', function () {
                                $(this).clearQueue();
                                $(this).animate({
                                    backgroundColor: '#dddddd'
                                }, 1000);
                                $(this).children('.edit').fadeIn(1000);
                                $(this).children('.delete').fadeIn(1000);
                            });

                            $(posthold).on('mouseleave', function () {
                                $(this).animate({
                                    backgroundColor: '#ffffff'
                                }, 1000);
                                $(this).children('.edit').fadeOut(500);
                                $(this).children('.delete').fadeOut(500);
                            });

                            $(posthold).children('.delete').on('click', function () {
                                deletePost($(this).parent());
                            });

                            $(posthold).children('.edit').on('click', function () {
                                editPost($(this).parent());
                            });

                            $(posthold).children('.buttons').children('#btn_update_post').on('click', function () {
                                updateEditPost($(this).parent().parent());
                            });

                            $(posthold).children('.buttons').children('#btn_cancel').on('click', function () {
                                cancelEditPost($(this).parent().parent());
                            });
                        } else {

                        }
                    }
                    updateDateTime();
                    var timeout = setInterval(function () { updateDateTime() }, 1000);
                }
            }
        });

    }
}

function loadUserList() {

    if ($('#userlist').length > 0) {

        var listData = new Object();
        listData.start = 0;
        listData.size = 30;
        var query = JSON.stringify(listData);

        $.ajax({
            type: "POST",
            url: "listusers.php",
            dataType: "json",
            data: { json: query },
            success: function (data) {
                var response = data;

                if (response.status == 0 || response.status == -1) {
                    displayMessage("One or more fields are blank", 2);
                } else {
                    var users = response.users;
                    for (var i = 0; i < users.length; i++) {
                        var element = 'user' + i;
                        var userhold = $('<div id="' + element + '" class="user"><div>');

                        if ($('#' + element).length == 0) {
                            var user = '';

                            user += '<h2>' + users[i].username + '</h2>';
                            user += '<p>First Name: ' + users[i].firstname + '</p>';
                            user += '<p>Last Name: ' + users[i].lastname + '</p>';
                            user += '<span class="footer">Registered&nbsp;</span>&nbsp;<span class="formatteddate">0 seconds</span><span>&nbsp;ago</span>';

                            user += '<div class="userid">' + users[i].id + '</div>';
                            user += '<div class="dateregistered">' + users[i].dateregistered + '</div>';
                            user += '<div class="unlock" title="Unlock User"></div>';
                            user += '<div class="lock" title="Lock User"></div>';
                            user += '<div class="clear"></div>';
                            //formatDate(users[i].dateregistered);

                            user = $(user);
                            $(userhold).hide().prependTo('#userlist').fadeIn(2000);
                            $(userhold).append(user);
                            $('#' + element).on('mouseenter', function () {
                                $(this).clearQueue();
                                $(this).animate({
                                    backgroundColor: '#dddddd'
                                }, 1000);
                                $(this).children('.lock').fadeIn(1000);
                            });

                            $(userhold).on('mouseleave', function () {
                                $(this).animate({
                                    backgroundColor: '#ffffff'
                                }, 1000);
                                $(this).children('.lock').fadeOut(500);
                            });

                            $(userhold).children('.delete').on('click', function () {
                                deletePost($(this).parent());
                            });

                            $(userhold).children('.edit').on('click', function () {
                                editPost($(this).parent());
                            });
                        } else {

                        }
                    }
                    updateDateTime();
                    var timeout = setInterval(function () { updateDateTime() }, 1000);
                }
            }
        });

    }
}

function updateDateTime() {
    if ($('#postlist').length > 0) {
        var posts = $('#postlist');
        posts.children().each(function () {
            var d = $(this).children('.postdate').html();
            var n = formatDate(d);
            $(this).children('.formatteddate').html(n);
        });
    }

    if ($('#userlist').length > 0) {
        var posts = $('#userlist');
        posts.children().each(function () {
            var d = $(this).children('.dateregistered').html();
            var n = formatDate(d);
            $(this).children('.formatteddate').html(n);
        });
    }
}

function updateEditPost(post) {

    var postData = new Object();
    postData.title = $(post).children('#txt_title').val();
    postData.body = $(post).children('#txt_body').val();
    postData.type = 'textpost';
    postData.mode = 2
    postData.id = $(post).children('.postid').html();
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
                displayMessage("There was an error updating your post, please try again", 2);
            } else if (response.status == 1) {
                $(post).children('h2').html($(post).children('#txt_title').val());
                $(post).children('p').html($(post).children('#txt_body').val().replace(/[\r\n]/g, "<br />"));
                cancelEditPost(post);
                displayMessage("Your changes have been successfully saved", 1);
            }
        }
    });
}

function cancelEditPost(post) {

    if (selectedPost != null) {

        var selectedData = new Object();
        selectedData.id = $(selectedPost).children('.postid').html();
        selectedData.type = $(selectedPost).children('.posttype').html();

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

function editPost(post) {

    cancelEditPost(post);

    selectedPost = post;

    var postData = new Object();
    postData.id = $(post).children('.postid').html();
    postData.type = $(post).children('.posttype').html();

    if (postData.type == 4) {
        $(post).children('h2').hide();
        $(post).children('p').hide();
        $(post).children('#txt_title').val($(post).children('h2').html());
        $(post).children('#txt_body').val($(post).children('p').html().replace(/<br>/g, "\r\n"));
        $(post).children('#txt_title').fadeIn(1000);
        $(post).children('#txt_body').fadeIn(1000);
        $(post).children('.buttons').fadeIn(1000);
        $('button').button();
    }
}

function deletePost(post) {
    $('#dialog-confirm').attr('title', 'Delete Post');
    $('#dialog-confirm').html("Are you sure you want to delete the selected post? This action can not be undone.");
    $("#dialog-confirm").dialog({
        resizable: false,
        width: 400,
        height: 140,
        modal: true,
        buttons: {
            "Yes": function () {
                var postData = new Object();
                postData.id = $(post).children('.postid').html();
                postData.type = $(post).children('.posttype').html();
                var query = JSON.stringify(postData);

                $.ajax({
                    type: "POST",
                    url: "deletepost.php",
                    dataType: "json",
                    data: { json: query },
                    success: function (data) {
                        var response = data;

                        if (response.status == 0 || response.status == -1) {
                            displayMessage("There was an error deleting the select post, please try again", 2);
                        } else {
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