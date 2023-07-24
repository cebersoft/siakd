function alertbox(text){
  $('#myModal').modal('show');
  $("#myModal").find('.modal-body').html(text);
  bC = '<button type="button" class="btn btn-info" id="bC" data-dismiss="modal")>Close</button>';
  $("#myModal").find('.modal-footer').html(bC);
  return false;
}