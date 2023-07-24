function tabel(tbl){
  $('#'+tbl).dataTable({
      bAutoWidth: false,
		"aoColumn": [
		  { "bSortable": false },
		  null, null,null, null, null,
		  { "bSortable": false }
		],
		"aaSorting": []
    });
}