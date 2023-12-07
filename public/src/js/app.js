
$('#pic-thumbnail').on('click mouseover', function(event) {
  event.preventDefault();
  $('#pic-modal').modal();
});

$(".dropdown-menu li a").click(function(){
  $(this).parents(".dropdown").find('.btn').html($(this).text() + ' <span class="caret"></span>');
  $(this).parents(".dropdown").find('.btn').val($(this).data('value'));
  hiddenInputId = "f_" + $(this).parents(".dropdown").find('.btn').attr('id');
  $(this).parents(".dropdown").find('#' + hiddenInputId).val($(this).data('value'));

});

