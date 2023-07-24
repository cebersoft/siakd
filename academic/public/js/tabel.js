function tabel(tbl,paging,lengthChange,searching,ordering,info,autoWidth){
  $('#'+tbl).DataTable({
      "paging": paging,
      "lengthChange": lengthChange,
      "searching": searching,
      "ordering": ordering,
      "info": info,
      "autoWidth": autoWidth,
      "language": {
            "lengthMenu": "Menampilkan _MENU_ baris per halaman",
            "zeroRecords": "Maaf, data tidak ditemukan",
            "info": "Menampilkan _PAGE_ dari _PAGES_ . Total : _MAX_ baris",
            "infoEmpty": "Tidak ada baris tersedia",
            "infoFiltered": "(disaring dari _MAX_ total baris)"
        }
    });
}