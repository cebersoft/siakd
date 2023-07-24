function ajaxSubmit(frm,baseUrl,destUrl,resUrl,opt){
    form=$("#"+frm+" fieldset");
    //$("#myModal").modal('show');
    $('#myModal').modal({backdrop: 'static',keyboard: false});
    $('.modal-dialog').removeClass('modal-lg');
    $("#myModal").find('.modal-body').html('<img src="'+$('#loadingbar').attr('src')+'">');
    btC = '<button type="button" class="btn btn-info" id="btC" data-dismiss="modal")>OK</button>';
    $("#myModal").find('.modal-footer').html(btC);
    elm={};
    $.each($('#'+frm)[0].elements,
        function(i,o) {
        var _this=$(o);
            elm[_this.attr('id')]=$('#'+_this.attr('id')).val();
        }
    );
    $.ajax({
        type:'POST',
        url: baseUrl+destUrl,
        data:elm,
        success: function(response) {
          if(opt=="s"){
                nav(baseUrl+resUrl);
          }else{
              arrResp=response.split('|');
              if(arrResp[0]!='F'){
                linkRes = baseUrl+resUrl+arrResp[0];
                if(opt=="i"){
                    $("#"+frm)[0].reset();
                    if($("[sel-2]").length>0){$("[sel-2]").select2('val', '');}
                    txt = "<br>Lihat datanya di <a href='"+linkRes+"' target='_blank'>sini</a></br>";
                    $("#myModal").find('.modal-body').html(arrResp[1]+txt);
                }else if(opt=="u"){
                    $("#btC").click(function() {
                      nav(linkRes);
                    });
                    txt = "<br>Lihat datanya di <a href='"+linkRes+"'>sini</a></br>";
                    $("#myModal").find('.modal-body').html(arrResp[1]+txt);
                }
              }else{
                $("#myModal").find('.modal-body').html(arrResp[1]);
              }
            }
        }
    });
    return false;
}

function ajaxUpload(file,baseUrl,destUrl,targetImage,srcImage){
    //$("#myModal").modal('show');
	$('#myModal').modal({backdrop: 'static',keyboard: false});
    $('.modal-dialog').removeClass('modal-lg');
    $("#myModal").find('.modal-body').html('<img src="'+$('#loadingbar').attr('src')+'">');
    btC = '<button type="button" class="btn btn-info" id="btC" data-dismiss="modal")>OK</button>';
    $("#myModal").find('.modal-footer').html(btC);
    var file_data = $("#"+file)[0].files[0];
    var form_data = new FormData();    
    form_data.append('file', file_data);
    if(file_data){
      $.ajax({
        url: baseUrl+destUrl,
        dataType: 'text',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,                         
        type: 'post',
        success: function(response){
          $("#myModal").find('.modal-body').html(response);
          $("#"+file).val('');
          if(targetImage){
            d = new Date();
            $("#"+targetImage).attr("src",baseUrl+srcImage+"&new="+d.getTime());
          }
        }
      });
      return false;
    }else{
      $("#myModal").find('.modal-body').html("Tidak ada file yang diupload");
      return false;
    }
}