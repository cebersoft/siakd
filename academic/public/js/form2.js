function ajaxSubmitUpload(frm,arrFile,baseUrl,destUrl,resUrl,opt){
    form=$("#"+frm+" fieldset");
    //$("#myModal").modal('show');
    $('#myModal').modal({backdrop: 'static',keyboard: false});
    $('.modal-dialog').removeClass('modal-lg');
    $("#myModal").find('.modal-body').html('<img src="'+$('#loadingbar').attr('src')+'">');
    btC = '<button type="button" class="btn btn-info" id="btC" data-dismiss="modal")>OK</button>';
    $("#myModal").find('.modal-footer').html(btC);
    var file_data=new Array();
    for (i=0;i<arrFile.length;i++){
    	file_data[i]=$("#"+arrFile[i])[0].files[0];
    }
    /*var file_data = $("#"+file)[0].files[0];
    var file_data2 = $("#"+file2)[0].files[0];
    var file_data3 = $("#"+file3)[0].files[0];
    var file_data4 = $("#"+file4)[0].files[0];
    var file_data5 = $("#"+file5)[0].files[0];
    var file_data6 = $("#"+file6)[0].files[0];
    var file_data7 = $("#"+file7)[0].files[0];
    var file_data8 = $("#"+file8)[0].files[0];
    */
    f=$("#"+frm);
    var form_data = new FormData();
    elm={};
    $.each($('#'+frm)[0].elements,
        function(i,o) {
       var _this=$(o);
            elm[_this.attr('id')]=$('#'+_this.attr('id')).val();
            form_data.append(_this.attr('id'), $('#'+_this.attr('id')).val());
       }
    );
    j=1;
    for (i=0;i<arrFile.length;i++){
    	form_data.append('filez'+j, file_data[i]);
    	j=j+1;
    }
    /*form_data.append('filez', file_data);
    form_data.append('filez2', file_data2);
    form_data.append('filez3', file_data3);
    form_data.append('filez4', file_data4);
    form_data.append('filez5', file_data5);
    form_data.append('filez6', file_data6);
    form_data.append('filez7', file_data7);
    form_data.append('filez8', file_data8);
    */
	$.ajax({
        type:'POST',
        url: baseUrl+destUrl,
        dataType: 'text',
        cache: false,
    	contentType: false,
    	processData: false,
    	data: form_data,  
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
                    txt = "<br>Silakan klik di <a href='"+linkRes+"' target='_blank'>sini</a> atau OK untuk melihat data</strong></br>";
                    $("#myModal").find('.modal-body').html(arrResp[1]+txt);
                }else if(opt=="u"){
                    $("#btC").click(function() {
                      nav(linkRes);
                    });
                    txt = "<br>Silakan klik di <a href='"+linkRes+"'>sini</a> atau OK untuk melihat data</strong></br>";
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