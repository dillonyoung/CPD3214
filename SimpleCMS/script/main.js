
$(document).ready(function () {

    loadMainPostList();

});

function loadMainPostList() {

    if ($('#mainpostlist').length > 0) {
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
                                post += '<h2>' + posts[i].title + '</h2><p>' + posts[i].details.replace(/[\r\n]/g, "<br />") +'</p>';
                                post += '<span class="footer">Written by ' + posts[i].author + '&nbsp;</span>&nbsp;<span class="formatteddate">0 seconds</span><span>&nbsp;ago</span>';
                            }
                            post += '<div class="postid">' + posts[i].id + '</div>';
                            post += '<div class="posttype">' + posts[i].type + '</div>';
                            post += '<div class="postdate">' + posts[i].dateposted + '</div>';
                            post += '<div class="clear"></div>';
                            formatDate(posts[i].dateposted);

                            post = $(post);
                            $(posthold).hide().prependTo('#mainpostlist').fadeIn(2000);
                            $(posthold).append(post);
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
    var posts = $('#mainpostlist');
    posts.children().each(function () {
        var d = $(this).children('.postdate').html();
        var n = formatDate(d);
        $(this).children('.formatteddate').html(n);

    });
}