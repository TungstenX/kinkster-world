//function showPostModal(postBody, display) {
//  $('#post-body').val(postBody);
//  $('#modal-save').prop("disabled", false);
//  $('#modal-error')
//  .css("display","none")
//  .text("");
//  displayHtml = display.toLowerCase().replace(/\b[a-z]/g, function(letter) {
//    return letter.toUpperCase();
//  });
//  $('#post-body-audience').html(displayHtml + ' <span class="caret"></span>');
//  $('#post-body-audience').val(display);
//  if(postBody && postBody.length > 0)
//  {
//    $('.modal-title').text("Edit Post");
//    $('#label-post-body').text("Edit the Post");
//    $('#modal-save').html("Save changes");
//  }
//  else
//  {
//    $('.modal-title').text("New Post");
//    $('#label-post-body').text("Create a Post");
//    $('#modal-save').html("Post");
//  }
//  $('#edit-modal').modal();
//}

/**
* TODO: Currently used by elements in the users section
**/
function unfriend(obj) {
//  console.log($(obj).data('id'));
  $.ajax({
    method: 'POST',
    url: urlUnfriend,
    data: {friendid: $(obj).data('id'), _token: token}
  })
  .done(function (msg) {
    if($(obj).data('which') === 'new') {
//    console.log($('#users').find('[data-friendid="' + $(obj).data('id') + '"]'));
      $('#users').find('[data-friendid="' + $(obj).data('id') + '"]')
        .attr('class',"bi bi-people");
    }
    else if('friend') {
      $('#friends').find('[data-friendid="' + $(obj).data('id') + '"]')
        .attr('class',"bi bi-people");
    }
    $('#confirm-modal').modal("hide");
  })
  .fail(function(msg) {
    jsonMsg = JSON.stringify(msg);
    console.log(jsonMsg);
    code = msg['status'];
    errorMsg = msg['responseJSON']['message'];
    console.log(errorMsg);
    $('#confirm-modal').find('#modal-error')
      .css("display","inline")
      .text(errorMsg);
  });
}
function postOrCancelFriendRequest(dlgTitle, eventTarget, which) {
  if($(eventTarget).hasClass('bi-people-fill')) {
    //do something
    showConfirmModal(dlgTitle, "unfriend", eventTarget.parentNode.parentNode.dataset['friendid'], which);
    return;
  }

  $.ajax({
    method: 'POST',
    url: urlNewFriend,
    data: {friendid: eventTarget.parentNode.parentNode.dataset['friendid'], _token: token}
  })
  .done(function (msg) {
    console.log(eventTarget);
    $(eventTarget).attr('class',"bi bi-people-fill");
  })
  .fail(function(msg) {
    jsonMsg = JSON.stringify(msg);
    console.log(jsonMsg);
    code = msg['status'];
    errorMsg = msg['responseJSON']['message'];
    console.log(errorMsg);
    $('#confirm-modal').find('#modal-error')
      .css("display","inline")
      .text(errorMsg);
  });
}

$('#users').find('.interaction').find('.request').on('click', function (event) {
  event.preventDefault();
  postOrCancelFriendRequest("Are you sure you want to cancel the friend request to " + event.target.parentNode.parentNode.dataset['name'], event.target, 'new');
});

$('#friends').find('.interaction').find('.request').on('click', function (event) {
  event.preventDefault();
  postOrCancelFriendRequest("Are you sure you want to unfriend your friend " + event.target.parentNode.parentNode.dataset['name'], event.target, 'friend');
});
