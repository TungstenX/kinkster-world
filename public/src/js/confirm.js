function showConfirmModal(question, functionName, id, which) {
  $('#confirm-modal').find('#modal-question').text(question)
  $('#confirm-modal').find('#modal-yes').prop("disabled", false);
  $('#confirm-modal').find('#modal-yes').removeAttr('onclick');
  $('#confirm-modal').find('#modal-yes').attr('onClick', functionName + '(this);');
  $('#confirm-modal').find('#modal-yes').attr('data-id', id);
  $('#confirm-modal').find('#modal-yes').attr('data-which', which);
  $('#confirm-modal').find('#modal-error')
    .css("display","none")
    .text("");
  $('#confirm-modal').modal("show");
}
