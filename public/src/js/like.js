//All the events and methods relating to do the like on a post
$('.like').on('click', function(event) {
  event.preventDefault();
  postId = event.target.parentNode.parentNode.parentNode.dataset['postid'];
  var isLike = event.target.parentNode.previousElementSibling == null;
  $.ajax({
    method: 'POST',
    url: urlLike,
    data: {isLike: isLike, postId: postId, _token: token}
  })
  .done(function() {
    if (isLike) {
      event.target.parentNode.nextElementSibling.innerHTML = '<i class="bi bi-hand-thumbs-down"></i>';
    } else {
      event.target.parentNode.previousElementSibling.innerHTML = '<i class="bi bi-hand-thumbs-up"></i>';
    }
    event.target.outerHTML = isLike ?
      event.target.outerHTML.includes("bi-hand-thumbs-up-fill") ? '<i class="bi bi-hand-thumbs-up"></i>' : '<i class="bi bi-hand-thumbs-up-fill"></i>' :
      event.target.outerHTML.includes("bi-hand-thumbs-down-fill") ? '<i class="bi bi-hand-thumbs-down"></i>' : '<i class="bi bi-hand-thumbs-down-fill"></i>';

  });
});
