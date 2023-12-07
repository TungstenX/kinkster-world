// All the events and methods related to posting an update
var postId = 0;
var postBodyElement = null;

function showPostModal(postBody, display) {
  $('#post-body').val(postBody);
  $('#modal-save').prop("disabled", false);
  $('#modal-error')
  .css("display","none")
  .text("");
  displayHtml = display.toLowerCase().replace(/\b[a-z]/g, function(letter) {
    return letter.toUpperCase();
  });
  $('#post-body-audience').html(displayHtml + ' <span class="caret"></span>');
  $('#post-body-audience').val(display);
  if(postBody && postBody.length > 0)
  {
    $('.modal-title').text("Edit Post");
    $('#label-post-body').text("Edit the Post");
    $('#modal-save').html("Save changes");
  }
  else
  {
    $('.modal-title').text("New Post");
    $('#label-post-body').text("Create a Post");
    $('#modal-save').html("Post");
  }
  $('#edit-modal').modal("show");
}

$('#fake-body').on('click', function () {
  event.preventDefault();
  postId = 0;
  postBodyElement = event.target.parentNode.parentNode.parentNode.nextElementSibling.childNodes[1].childNodes[3];
//  console.log(event.target.parentNode.parentNode.parentNode.nextElementSibling.childNodes[1]);
//  console.log(postBodyElement);
  display = event.target.dataset['display'];
  showPostModal("", display);
});

$('#fake-button').on('click', function () {
  event.preventDefault();
  postId = 0;
  postBodyElement = event.target.parentNode.parentNode.nextElementSibling.childNodes[1].childNodes[3];
//  console.log(event.target.parentNode.parentNode.nextElementSibling.childNodes[1]);
//  console.log(postBodyElement);
  display = event.target.dataset['display'];
  showPostModal("", display);
});

$('.post').find('.interaction').find('.edit').on('click', function (event) {
    event.preventDefault();
    postBodyElement = event.target.parentNode.parentNode.parentNode.childNodes[3];
    var postBody = postBodyElement.textContent;
    postId = event.target.parentNode.parentNode.parentNode.dataset['postid'];
    display = event.target.parentNode.parentNode.parentNode.dataset['display'];
    showPostModal(postBody, display);
});

//Not currently used
function formatErrorMessage(jqXHR, exception) {

    if (jqXHR.status === 0) {
        return ('Not connected.\nPlease verify your network connection.');
    } else if (jqXHR.status == 404) {
        return ('The requested page not found. [404]');
    } else if (jqXHR.status == 500) {
        return ('Internal Server Error [500].');
    } else if (exception === 'parsererror') {
        return ('Requested JSON parse failed.');
    } else if (exception === 'timeout') {
        return ('Time out error.');
    } else if (exception === 'abort') {
        return ('Ajax request aborted.');
    } else {
        return ('Uncaught Error.\n' + jqXHR.responseText);
    }
}

function handleErrorMessage(msg) {
  jsonMsg = JSON.stringify(msg);
  console.log(jsonMsg);
  code = msg['status'];
  errorMsg = msg['responseJSON']['message'];
  //      alert(code);
  //{"readyState":4,"responseText":"{\"message\":\"The body field is required.\",\"errors\":{\"body\":[\"The body field is required.\"]}}","responseJSON":{"message":"The body field is required.","errors":{"body":["The body field is required."]}},"status":422,"statusText":"Unprocessable Content"}
  $('#modal-error')
    .css("display","inline")
    .text(errorMsg);
  $(this).prop("disabled", false);
}

$('#modal-save').on('click', function () {
  $(this).prop("disabled", true);

  if(postId == 0) {
    $.ajax({
      method: 'POST',
      url: urlNewPost,
      data: {body: $('#post-body').val(), display: $('#post-body-audience').val(), _token: token}
    })
    .done(function (msg) {
      eraser = $("<i></i>")
        .addClass("bi bi-eraser");
      del = $("<a></a>")
        .attr("href", msg['del_route'])
        .append(eraser);
      //Todo, bind event handler to these below
      /*pencil = $("<i></i>")
        .addClass("bi bi-pencil");
      edit = $("<a></a>")
        .addClass("edit")
        .attr("href", "#")
        .append(pencil);
      thumbsDown = $("<i></i>")
        .addClass("bi bi-hand-thumbs-down");
      dislike = $("<a></a>")
        .addClass("like")
        .attr("href", "#")
        .append(thumbsDown);
      thumbsUp = $("<i></i>")
        .addClass("bi bi-hand-thumbs-up");
      like = $("<a></a>")
        .addClass("like")
        .attr("href", "#")
        .append(thumbsUp);*/
      interaction = $("<div></div>")
        .addClass("interaction")
        .append("[refresh page for buttons] | " , del);

      accessInfo = $("<i></i>")
        .css("float","left")
        .addClass("bi bi-" + (msg['display'] == 'public' ? 'globe' : (msg['display'] == 'friends' ? 'people' : (msg['display'] == 'circle' ? 'circle' : 'slash-circle'))));
      innerInfoLeft = $("<div></div>")
        .css("float","left")
        .append(accessInfo, "<strong>" + msg['user_name'] + "</strong> <br> now");
      innerInfoRight = $("<div></div>")
        .css("float","right")
        .append('<img src="' + msg['profile_route'] + '" class="img-thumbnail" id="post-thumbnail" style="height: 50px;">');
      info = $("<div></div>")
        .addClass("info")
        .css("overflow","auto")
        .append(innerInfoLeft, innerInfoRight);
      post = $("<p></p>")
        .css("clear", "left")
        .text(msg['content']);
      article = $("<article></article>")
        .addClass("post")
        .attr("data-postid", msg['id'])
        .attr("data-display", msg['display'])
        .append(info, post, interaction);

      $(postBodyElement).before(article);
      $('#edit-modal').modal('hide');
    })
    .fail(handleErrorMessage);
  } else {
    $.ajax({
      method: 'POST',
      url: urlEdit,
      data: {body: $('#post-body').val(), display: $('#post-body-audience').val(), postId: postId, _token: token}
    })
    .done(function (msg) {
      $(postBodyElement).text(msg['new_body']);
      $('#edit-modal').modal('hide');
    })
    .fail(handleErrorMessage);
  }
});
