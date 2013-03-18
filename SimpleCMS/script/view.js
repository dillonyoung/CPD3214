$(document).ready(function () {

    $('button').button();

    loadPostDetails();

    loadPostComments();

    $('button#btn_comment_post').click(function () {
        postComment();
    });
});

function postComment() {
    var postData = new Object();
    postData.postid = $('#postid').text();
    postData.comment = $('#txt_comment').val();
    postData.mode = 1;
    var query = JSON.stringify(postData);

    $.ajax({
        type: "POST",
        url: "comment.php",
        dataType: "json",
        data: { json: query },
        success: function (data) {
            var response = data;

            if (response.status == 0 || response.status == -1) {
                displayMessage("The comment can not be blank, please try again", 2);
            } else if (response.status == 1) {
                displayMessage("Your comment has been successfully posted", 1);
                $('#txt_comment').val('');
                loadPostComments();
            }
        }
    });
}

function loadPostDetails() {

    var postData = new Object();
    postData.id = $('#postid').text();
    var query = JSON.stringify(postData);

    $.ajax({
        type: "POST",
        url: "viewpost.php",
        dataType: "json",
        data: { json: query },
        success: function (data) {
            var response = data;

            if (response.status == 0 || response.status == -1) {
                displayMessage("There was an error loading the post", 2);
            } else if (response.status == 1) {
                $('#postdetail').html(response.postdata).fadeIn(1000);
                updateDateTime();
                var timeout = setInterval(function () { updateDateTime() }, 1000);
            }
        }
    });
}

function loadPostComments() {
    var postData = new Object();
    postData.id = $('#postid').text();
    postData.start = 0;
    postData.size = 30;
    var query = JSON.stringify(postData);

    $.ajax({
        type: "POST",
        url: "viewcomments.php",
        dataType: "json",
        data: { json: query },
        success: function (data) {
            var response = data;

            if (response.status == 0 || response.status == -1) {
                displayMessage("There was an error loading the comments", 2);
            } else if (response.status == -2) {
                $('#postcomments').html('<p>No one has yet commented on this post. Be the first to do so.</p>');
                $('#postcomments').fadeIn(1000);
                $('#nouserloggedin').fadeOut(500);
                $('#postnewcomment').fadeIn(1000);
            } else if (response.status == 1) {
                $('#nouserloggedin').fadeOut(500);
                $('#postnewcomment').fadeIn(1000);
                $('#postcomments').fadeIn(1000);

                var comments = response.comments;
                for (var i = 0; i < comments.length; i++) {
                    var element = 'comment' + i;
                    var commenthold = $('<div id="' + element + '" class="comment"><div>');

                    if ($('#' + element).length == 0) {
                        var comment = '';
                        
                        comment += '<p>' + comments[i].details.replace(/[\r\n]/g, "<br />") + '</p>';
                        comment += '<span class="footer">Written by ' + comments[i].author + '&nbsp;</span>&nbsp;<span class="formatteddate">0 seconds</span><span>&nbsp;ago</span>';
                        comment += '<div class="postid">' + comments[i].id + '</div>';
                        comment += '<div class="postdate">' + comments[i].dateposted + '</div>';
                        comment += '<div class="clear"></div>';

                        comment = $(comment);
                        $(commenthold).hide().prependTo('#postcomments').fadeIn(2000);
                        $(commenthold).append(comment);
                    }
                }
            }

            if (response.userid == 0) {
                $('#nouserloggedin').fadeIn(1000);
                $('#postnewcomment').fadeOut(500);
            } else {

            }
        }
    });
}

function updateDateTime() {
    var posts = $('#postdetail');
    var d = $(posts).children('.postdate').html();
    var n = formatDate(d);
    $(posts).children('.formatteddate').html(n);

    var posts = $('#postcomments');
    posts.children().each(function () {
        var d = $(this).children('.postdate').html();
        var n = formatDate(d);
        $(this).children('.formatteddate').html(n);

    });
}