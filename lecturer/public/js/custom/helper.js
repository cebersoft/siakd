function nav(url) {
  //$("#myModal").modal('show');
  $('#myModal').modal({backdrop: 'static',keyboard: false});
  $('.modal-dialog').removeClass('modal-lg');
  $("#myModal").find('.modal-body').html('<img src="'+$('#loadingbar').attr('src')+'">');
  location.href = url;
}
function confirm(text,baseUrl,destUrl,arrParam){
  urll=baseUrl+destUrl;
  sUrl=document.URL;
  //$("#myModal").modal('show');
  $('#myModal').modal({backdrop: 'static',keyboard: false});
  $('.modal-dialog').removeClass('modal-lg');
	$("#myModal").find('.modal-body').html(text);
	bY = '<button type="button" id="btY" class="btn btn-warning")>Ya</button>';
	bN = '<button type=button id="btN" class="btn btn-default" data-dismiss="modal">Tidak</button>';
	$("#myModal").find('.modal-footer').html(bY+bN);
	$("#btY").click(function() {
    $("#myModal").find('.modal-body').html('<img src="'+$('#loadingbar').attr('src')+'">');
    $.ajax({
      type:'POST',
      url: urll,
      data:{param:arrParam},
      success: function(response) {
      	arrResp=response.split('|');
          $("#myModal").find('.modal-body').html(arrResp[1]);
          bOK = '<button type="button" class="btn btn-info" id="btOk" data-dismiss="modal")>OK</button>';
    		  $("#myModal").find('.modal-footer').html(bOK);
      		  $("#btOk").click(function() {
      			nav(sUrl);
      		});
      }
    });
    return false;
  });
}
function hbox(baseUrl,destUrl,arrCaller,arrParam){
  //$('#myModal').modal('show');
  $('#myModal').modal({backdrop: 'static',keyboard: false});
  $('.modal-dialog').addClass('modal-lg');
  $("#myModal").find('.modal-body').html('<img src="'+$('#loadingbar').attr('src')+'">');
  bC = '<button type="button" class="btn btn-info" id="bC" data-dismiss="modal")>Close</button>';
  $("#myModal").find('.modal-footer').html(bC);
  valPrm={};
  if(arrParam){
    for (i=0; i<arrParam.length; i++){
      valPrm[i]=$('#'+arrParam[i]).val();
    };
  }
  $.ajax({
    type:'POST',
    url: baseUrl+'/'+destUrl,
    data: {caller:arrCaller,param:valPrm},
    success: function(response) {
      $("#myModal").find('.modal-body').html(response);
    }
  });
  return false;
}

function ibox(text){
	  $('#myModal').modal({backdrop: 'static',keyboard: false});
	  $("#myModal").find('.modal-body').html(text);
	  return false;
}