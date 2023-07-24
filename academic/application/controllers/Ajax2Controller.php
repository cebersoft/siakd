<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Ajax 2 Controller -> Controller untuk submit via ajax (2)
*/
class Ajax2Controller extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('KalenderAkd');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Ruangan');
		Zend_Loader::loadClass('Indeks');
		Zend_Loader::loadClass('RangeNilai');
		Zend_Loader::loadClass('AturanNilai');
		Zend_Loader::loadClass('Kelas');
		Zend_Loader::loadClass('Paketkelas');
		Zend_Loader::loadClass('Jadwal');
		Zend_Loader::loadClass('Kuliah');
		Zend_Loader::loadClass('StatMasuk');
		Zend_Loader::loadClass('StatReg');
		Zend_Loader::loadClass('Nmkelas');
		Zend_Loader::loadClass('TimTeaching');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('JadwalUjian');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Validation');
		
		$auth = Zend_Auth::getInstance();
		$ses_ac = new Zend_Session_Namespace('ses_ac');
		if (($auth->hasIdentity())and($ses_ac->uname)) {

		}else{
			echo "F|Sesi anda sudah habis. Silakan login ulang!|";
		}
		// disabel layout
		$this->_helper->layout->disableLayout();
	}

	// Periode akademik
	function insperAction(){
		$user = new Menu();
		$menu = "periode/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$thn_awal = $request['thn_awal'];
			$thn_akhir = $request['thn_akhir'];
			$id_smt = trim($request['id_smt']);
			$id_stat = trim($request['id_stat']);
			$tgl = $request['tgl'];
			if($tgl){
				$arrTgl = explode('-', $tgl);
				$tgl_awal = trim($arrTgl[0]);
				$tgl_akhir = trim($arrTgl[1]);
			}
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiBetween($thn_awal,2008,2100)=='F'){
				$err++;
				$msg=$msg."<strong>- Tahun awal harus lebih dari 2008</strong><br>";
			}
			if($vd->validasiBetween($thn_akhir,2008,2100)=='F'){
				$err++;
				$msg=$msg."<strong>- Tahun akhir harus lebih dari 2008</strong><br>";
			}
			if($vd->validasiLength($id_smt,1,10)=='F'){
				$err++;
				$msg=$msg."<strong>- Semester tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($id_stat,1,10)=='F'){
				$err++;
				$msg=$msg."<strong>- Status periode tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($tgl,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Rentang periode tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$periode = new Periode();
				$setPeriode = $periode->setPeriode($thn_awal,$thn_akhir,$id_smt,$id_stat,$tgl_awal,$tgl_akhir);
				echo $setPeriode;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delperAction(){
		$user = new Menu();
		$menu = "periode/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$kd = $param[0];
			$periode = new Periode();
			$delPeriode = $periode->delPeriode($kd);
			echo $delPeriode;
		}
	}

	function updperAction(){
		$user = new Menu();
		$menu = "periode/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$tgl = $request['tgl'];
			$kd_per = $request['kd_per'];
			if($tgl){
				$arrTgl = explode('-', $tgl);
				$tgl_awal = trim($arrTgl[0]);
				$tgl_akhir = trim($arrTgl[1]);
			}
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($tgl,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Rentang periode tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$periode = new Periode();
				$updPeriode = $periode->updPeriode($tgl_awal,$tgl_akhir,$kd_per);
				echo $updPeriode;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function updperstatAction(){
		$user = new Menu();
		$menu = "periode/geser";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$id = $param[0];
			$periode = new Periode();
			$movePeriode = $periode->movePeriode($id);
			echo $movePeriode;
		}
	}
	
	// 	kalender akademik
	function inskalAction(){
		$user = new Menu();
		$menu = "kalender/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd_periode = $request['per'];
			$startdate = $request['startdate'];
			$enddate = trim($request['enddate']);
			$aktAkd = trim($request['aktAkd']);
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($kd_periode,1,30)=='F'){
				$err++;
				$msg=$msg."<strong>- Periode akademik tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($aktAkd,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Agenda akademik tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($startdate,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Tanggal mulai tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($enddate,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Tanggal selesai tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$kalender = new KalenderAkd();
				$setKalenderAkd = $kalender->setKalenderAkd($kd_periode, $aktAkd, $startdate, $enddate);
				echo $setKalenderAkd;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	function delkalAction(){
		$user = new Menu();
		$menu = "kalender/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$kd_periode = $param[0];
	    	$kd_aktivitas = $param[1];
			$kalender = new KalenderAkd();
			$delKalenderAkd = $kalender->delKalenderAkd($kd_periode, $kd_aktivitas);
			echo $delKalenderAkd;
		}
	}
	
	function updkalAction(){
		$user = new Menu();
		$menu = "kalender/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd_periode = $request['per'];
			$startdate = $request['startdate'];
			$enddate = trim($request['enddate']);
			$aktAkd = trim($request['aktAkd']);
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($kd_periode,1,30)=='F'){
				$err++;
				$msg=$msg."<strong>- Periode akademik tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($aktAkd,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Agenda akademik tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($startdate,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Tanggal mulai tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($enddate,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Tanggal selesai tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$kalender = new KalenderAkd();
				$updKalenderAkd = $kalender->updKalenderAkd($kd_periode, $aktAkd, $startdate, $enddate);
				echo $updKalenderAkd;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	// Angkatan
	function insaktAction(){
		$user = new Menu();
		$menu = "angkatan/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$akt = $request['akt'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiBetween($akt,2008,2100)=='F'){
				$err++;
				$msg=$msg."<strong>- Angkatan tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$angkatan = new Angkatan();
				$setAngkatan = $angkatan->setAngkatan($akt);
				echo $setAngkatan;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delaktAction(){
		$user = new Menu();
		$menu = "periode/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$id = $param[0];
			$angkatan = new Angkatan();
			$delAngkatan = $angkatan->delAngkatan($id);
			echo $delAngkatan;
		}
	}

	// ruangan
	function showroomAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$kat_room = $request['kat_room'];
		// set session
		$param = new Zend_Session_Namespace('param_room');
		$param->kat_room=$kat_room;
	}

	function insroomAction(){
		$user = new Menu();
		$menu = "ruangan/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd_ruangan = $this->_helper->string->esc_quote(trim($request['kd']));
			$nm_ruangan = $this->_helper->string->esc_quote(trim($request['nm']));
			$kpsts = $request['kpsts'];
			$kpsts_u = $request['kpsts_u'];
			$id_kat = $request['id_kat'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if ($kpsts_u==''){
				$kpsts_u=0;
			}
			if(($vd->validasiAlNum($kd_ruangan)=='F')or($vd->validasiLength($kd_ruangan,2,20)=='F')){
				$err++;
				$msg=$msg."<strong>- Kode ruangan harus di antara 2 dan 20 karakter tanpa simbol</strong><br>";
			}
			if(($vd->validasiAlNum($nm_ruangan)=='F')or($vd->validasiLength($nm_ruangan,3,60)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama ruangan harus di antara 3 dan 60 karakter tanpa simbol</strong><br>";
			}
			if($vd->validasiBetween($kpsts,1,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Kapasitas ruangan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($id_kat,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Kategori ruangan tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$ruangan = new Ruangan();
				$setRuangan = $ruangan->setRuangan($kd_ruangan,$nm_ruangan,$kpsts,$kpsts_u,$id_kat);
				echo $setRuangan;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delroomAction(){
		$user = new Menu();
		$menu = "ruangan/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$kd = $param[0];
			$ruangan = new Ruangan();
			$delRuangan = $ruangan->delRuangan($kd);
			echo $delRuangan;
		}
	}

	function updroomAction(){
		$user = new Menu();
		$menu = "ruangan/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$old_kd_ruangan = $this->_helper->string->esc_quote(trim($request['old_kd']));
			$kd_ruangan = $this->_helper->string->esc_quote(trim($request['kd']));
			$nm_ruangan = $this->_helper->string->esc_quote(trim($request['nm']));
			$kpsts = $request['kpsts'];
			$kpsts_u = $request['kpsts_u'];
			$id_kat = $request['id_kat'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if ($kpsts_u==''){
				$kpsts_u=0;
			}
			if(($vd->validasiAlNum($kd_ruangan)=='F')or($vd->validasiLength($kd_ruangan,2,20)=='F')){
				$err++;
				$msg=$msg."<strong>- Kode ruangan harus di antara 2 dan 20 karakter tanpa simbol</strong><br>";
			}
			if(($vd->validasiAlNum($nm_ruangan)=='F')or($vd->validasiLength($nm_ruangan,3,60)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama ruangan harus di antara 3 dan 60 karakter tanpa simbol</strong><br>";
			}
			if($vd->validasiBetween($kpsts,1,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Kapasitas ruangan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($id_kat,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Kategori ruangan tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$ruangan = new Ruangan();
				$updRuangan = $ruangan->updRuangan($kd_ruangan,$nm_ruangan,$kpsts,$kpsts_u,$id_kat,$old_kd_ruangan);
				echo $updRuangan;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	// indeks nilai
	function insidxAction(){
		$user = new Menu();
		$menu = "indeks/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$indeks = $this->_helper->string->esc_quote(trim($request['indeks']));
			$bobot = floatval($request['bbt']);
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($indeks,1,5)=='F'){
				$err++;
				$msg=$msg."<strong>- Indeks nilai tidak boleh kosong dan maksimal 5 karakter</strong><br>";
			}
			if($vd->validasiBetween($bobot, 0, 4)=='F'){
				$err++;
				$msg=$msg."<strong>- bobot indeks harus di antara 0 dan 4</strong><br>";
			}
			if($err==0){
				// set database
				$idx = new Indeks();
				$setIndeks = $idx->setIndeks($indeks, $bobot);
				echo $setIndeks;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	function delidxAction(){
		$user = new Menu();
		$menu = "indeks/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$id = $param[0];
			$indeks = new Indeks();
			$delIndeks = $indeks->delIndeks($id);
			echo $delIndeks;
		}
	}
	
	// range nilai
	function insrangeAction(){
		$user = new Menu();
		$menu = "rangenilai/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id_range = $this->_helper->string->esc_quote(trim($request['id']));
			$nm_range = $this->_helper->string->esc_quote(trim($request['nm']));
			$ind_tunda = $request['id_tunda'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiAlNumNoSpace($id_range)=='F')or($vd->validasiLength($id_range,1,5)=='F')){
				$err++;
				$msg=$msg."<strong>- Kode range tidak boleh kosong dan nilai maksimal 5 karakter (angka dan huruf) tanpa simbol</strong><br>";
			}
			if(($vd->validasiAlNum($nm_range)=='F')or($vd->validasiLength($nm_range,1,15)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama range tidak boleh kosong dan maksimal 15 karakter tanpa simbol</strong><br>";
			}
			if($vd->validasiLength($ind_tunda,1,8)=='F'){
				$err++;
				$msg=$msg."<strong>- Indeks nilai tunda tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$rangeNilai = new RangeNilai();
				$setRange = $rangeNilai->setRangeNilaiHdr($id_range, $nm_range, $ind_tunda);
				echo $setRange;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	function delrangeAction(){
		$user = new Menu();
		$menu = "rangenilai/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$id = $param[0];
			$rangeNilai = new RangeNilai();
			$delRangeNilai = $rangeNilai->delRangeNilai($id);
			echo $delRangeNilai;
		}
	}

	function updrangeAction(){
		$user = new Menu();
		$menu = "rangenilai/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$old_id_range = $this->_helper->string->esc_quote(trim($request['old_id']));
			$new_id_range = $this->_helper->string->esc_quote(trim($request['id']));
			$nm_range = $this->_helper->string->esc_quote(trim($request['nm']));
			$ind_tunda = $request['id_tunda'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($old_id_range,1,5)=='F'){
				$err++;
				$msg=$msg."<strong>- Kode range lama tidak boleh kosong</strong><br>";
			}
			if(($vd->validasiAlNumNoSpace($new_id_range)=='F')or($vd->validasiLength($new_id_range,1,5)=='F')){
				$err++;
				$msg=$msg."<strong>- Kode range tidak boleh kosong dan nilai maksimal 5 karakter (angka dan huruf) tanpa simbol</strong><br>";
			}
			if(($vd->validasiAlNum($nm_range)=='F')or($vd->validasiLength($nm_range,1,15)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama range tidak boleh kosong dan maksimal 15 karakter tanpa simbol</strong><br>";
			}
			if($vd->validasiLength($ind_tunda,1,8)=='F'){
				$err++;
				$msg=$msg."<strong>- Indeks nilai tunda tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$rangeNilai = new RangeNilai();
				$updRange = $rangeNilai->updRangeNilaiHdr($new_id_range, $nm_range, $ind_tunda, $old_id_range);
				echo $updRange;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	function insrangedtlAction(){
		$user = new Menu();
		$menu = "rangenilai/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id_range_hdr = $this->_helper->string->esc_quote(trim($request['id_hdr']));
			$id_indeks = $this->_helper->string->esc_quote(trim($request['indeks']));
			$nilai = floatval($request['nilai']);
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiAlNumNoSpace($id_range_hdr)=='F')or($vd->validasiLength($id_range_hdr,1,5)=='F')){
				$err++;
				$msg=$msg."<strong>- Kode range tidak boleh kosong dan nilai maksimal 5 karakter (angka dan huruf) tanpa simbol</strong><br>";
			}
			if($vd->validasiLength($id_indeks,1,8)=='F'){
				$err++;
				$msg=$msg."<strong>- Indeks tidak boleh kosong</strong><br>";
			}
			if($vd->validasiBetween($nilai, 0, 100)=='F'){
				$err++;
				$msg=$msg."<strong>- Nilai minimal harus di antara 0 dan 100</strong><br>";
			} 
			if($err==0){
				// set database
				$rangeNilai = new RangeNilai();
				$setRangeDTL = $rangeNilai->setRangeNilaiDtl($id_range_hdr, $nilai, $id_indeks);
				echo $setRangeDTL;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delrangedtlAction(){
		$user = new Menu();
		$menu = "rangenilai/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$id = $param[0];
			$rangeNilai = new RangeNilai();
			$delRangeNilaiDtl = $rangeNilai->delRangeNilaiDtl($id);
			echo $delRangeNilaiDtl;
		}
	}
	
	// aturan nilai
	function insruleAction(){
		$user = new Menu();
		$menu = "rulenilai/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd_prodi = $request['prd'];
			$kd_periode = $request['per'];
			$id_range = $request['range'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($kd_prodi,1,8)=='F'){
				$err++;
				$msg=$msg."<strong>- Kode prodi tidak boleh kosong dan nilai maksimal 8 karakter</strong><br>";
			}
			if($vd->validasiLength($kd_periode,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Periode akademik tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($id_range, 1, 5)=='F'){
				$err++;
				$msg=$msg."<strong>- Range nilai tidak boleh kosong</strong><br>";
			} 
			if($err==0){
				// set database
				$ruleNilai = new AturanNilai();
				$setRuleNilai= $ruleNilai->setAturanNilai($kd_prodi, $kd_periode, $id_range);
				echo $setRuleNilai;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	function delruleAction(){
		$user = new Menu();
		$menu = "rulenilai/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$prd = $param[0];
	    	$per = $param[1];
			$ruleNilai = new AturanNilai();
			$delAturanNilai = $ruleNilai->delAturanNilai($prd, $per);
			echo $delAturanNilai;
		}
	}
	
	// kelas
	function showklsAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$frm=$this->_request->get('frm');
		$prd = $request['prd'.$frm];
		$per = $request['per'.$frm];
		$jns = $request['jns'.$frm];
		// set session
		$param = new Zend_Session_Namespace('param_kls');
		$param->prd=$prd;
		$param->per=$per;
		$param->jns=$jns;
	}

	function insklsAction(){
		$user = new Menu();
		$menu = "kelas/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id_ajar = $request['id_ajar'];
			$kd_periode = $request['per'];
			$jenis = $request['jns'];
			$ttpmk = intval($request['ttpmk']);
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($id_ajar,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Mata Kuliah - Dosen tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($kd_periode,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Periode akademik tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($jenis,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Jenis Kelas tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$kelas = new Kelas();
				$setKelas = $kelas->setKelas($id_ajar,$kd_periode,$jenis,$ttpmk);
				echo $setKelas;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delklsAction(){
		$user = new Menu();
		$menu = "kelas/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$kd = $param[0];
			$kelas = new Kelas();
			$delKelas = $kelas->delKelas($kd);
			echo $delKelas;
		}
	}

	function updklsAction(){
		$user = new Menu();
		$menu = "kelas/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd_kls = $request['kd_kls'];
			$nm_p1 = trim($request['par1']);
			$p_p1 = floatval($request['pers1']);
			$nm_p2 = trim($request['par2']);
			$p_p2 = floatval($request['pers2']);
			$nm_p3 = trim($request['par3']);
			$p_p3 = floatval($request['pers3']);
			$nm_p4 = trim($request['par4']);
			$p_p4 = floatval($request['pers4']);
			$nm_p5 = trim($request['par5']);
			$p_p5 = floatval($request['pers5']);
			$nm_p6 = trim($request['par6']);
			$p_p6 = floatval($request['pers6']);
			$nm_p7 = trim($request['par7']);
			$p_p7 = floatval($request['pers7']);
			$nm_p8 = trim($request['par8']);
			$p_p8 = floatval($request['pers8']);
			$p_uts = floatval($request['persUTS']);
			$p_uas = floatval($request['persUAS']);
			$note = $this->_helper->string->esc_quote($request['note']);
			$ttpmk = intval($request['ttpmk']);
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			$tot_par=$p_p1+$p_p2+$p_p3+$p_p4+$p_p5+$p_p6+$p_p7+$p_p8+$p_uts+$p_uas;
			if($tot_par!=100){
				$err++;
				$msg=$msg."<strong>- Jumlah parameter harus tepat 100. Total persentase parameter saat ini=".$tot_par."</strong><br>";
			}
			if(($vd->validasiLength($nm_p1,1,20)=='F')or($vd->validasiLength($nm_p2,1,20)=='F')or($vd->validasiLength($nm_p3,1,20)=='F')or($vd->validasiLength($nm_p4,1,20)=='F')or($vd->validasiLength($nm_p5,1,20)=='F')or($vd->validasiLength($nm_p6,1,20)=='F')or($vd->validasiLength($nm_p7,1,20)=='F')or($vd->validasiLength($nm_p8,1,20)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama parameter tidak boleh kosong dan lebih dari 20 karakter</strong><br>";
			}
			if(($vd->validasiAlNum($nm_p1)=='F')or($vd->validasiAlNum($nm_p2)=='F')or($vd->validasiAlNum($nm_p3)=='F')or($vd->validasiAlNum($nm_p4)=='F')or($vd->validasiAlNum($nm_p5)=='F')or($vd->validasiAlNum($nm_p6)=='F')or($vd->validasiAlNum($nm_p7)=='F')or($vd->validasiAlNum($nm_p8)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama Parameter hanya boleh mengandung huruf dan angka</strong><br>";
			}
			if($err==0){
				// set database
				$kelas = new Kelas();
				$updKelas = $kelas->updKelas($nm_p1,$nm_p2,$nm_p3,$nm_p4,$nm_p5,$nm_p6,$nm_p7,$nm_p8,$p_p1,$p_p2,$p_p3,$p_p4,$p_p5,$p_p6,$p_p7,$p_p8,$p_uts,$p_uas,$note,$ttpmk,$kd_kls);
				echo $updKelas;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	// paket kelas
	function inspklsAction(){
		$user = new Menu();
		$menu = "paketkelas/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd_kls = $request['kd_kls'];
			$nm_kls = $request['nm_kls'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($kd_kls,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Kode kelas tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($nm_kls,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Nama kelas tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$paketkelas = new Paketkelas();
				$setPaketKelas = $paketkelas->setPaketKelas($kd_kls,$nm_kls);
				echo $setPaketKelas;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delpklsAction(){
		$user = new Menu();
		$menu = "paketkelas/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$kd = $param[0];
			$paketkelas = new Paketkelas();
			$delPaketKelas = $paketkelas->delPaketKelas($kd);
			echo $delPaketKelas;
		}
	}

	// Jadwal kuliah
	function insjdwlAction(){
		$user = new Menu();
		$menu = "jadwal/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$frm=$this->_request->get('frm');
			$kd_paket_kelas = $request['kd_pkt_'.$frm];
			$kd_periode = $request['per_'.$frm];
			$id_slot = $request['id_slot_'.$frm];
			$id_hari = $request['id_hari_'.$frm];
			$id_ruangan = $request['id_room_'.$frm];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($kd_paket_kelas,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Paket kelas (nama kelas, dosen, mata kuliah) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($id_ruangan,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Ruangan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($id_slot,1,2)=='F'){
				$err++;
				$msg=$msg."<strong>- Slot tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$jadwal = new Jadwal();
				$setJadwal = $jadwal->setJadwal($kd_periode,$id_hari,$id_slot,$id_ruangan,$kd_paket_kelas);
				echo $setJadwal;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function deljdwlAction(){
		$user = new Menu();
		$menu = "jadwal/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$kd_periode = $param[0];
	    	$id_hari = $param[1];
	    	$id_slot = $param[2];
	    	$kd_ruangan = $param[3];
			$jadwal = new Jadwal();
			$delJadwal = $jadwal->delJadwal($kd_periode,$id_hari,$id_slot,$kd_ruangan);
			echo $delJadwal;
		}
	}

	function updjdwlAction(){
		$user = new Menu();
		$menu = "jadwal/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd_paket_kelas = $request['paket'];
			$kd_periode = $request['per'];
			$old_id_slot = $request['old_sl'];
			$new_id_slot = $request['new_sl'];
			$old_id_hari = $request['old_hr'];
			$new_id_hari = $request['new_hr'];
			$old_kd_ruangan = $request['old_ro'];
			$new_kd_ruangan = $request['new_ro'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($kd_paket_kelas,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Paket kelas (nama kelas, dosen, mata kuliah) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($new_kd_ruangan,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Ruangan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($new_id_slot,1,2)=='F'){
				$err++;
				$msg=$msg."<strong>- Slot tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$jadwal = new Jadwal();
				$updJadwal = $jadwal->updJadwal($kd_periode,$new_id_hari,$new_id_slot,$new_kd_ruangan,$kd_paket_kelas,$old_id_hari,$old_id_slot,$old_kd_ruangan);
				echo $updJadwal;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	// status masuk
	function insstatmskAction(){
		$user = new Menu();
		$menu = "status/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id = $request['id_stat'];
			$nm_stat = $this->_helper->string->esc_quote($request['nm_stat']);
			$jns_msk = $request['jns'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiBetween(intval($id), 0, 99)=='F')or($vd->validasiLength($id, 1, 2)=='F')){
				$err++;
				$msg=$msg."<strong>- Kode status tidak boleh kosong, maksimal 2 digit angka</strong><br>";
			}
			if(($vd->validasiLength($nm_stat,1,15)=='F')or($vd->validasiAlNum($nm_stat)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama status tidak boleh kosong dan tidak boleh mengandung simbol</strong><br>";
			}
			if($vd->validasiLength($jns_msk, 1, 99)=='F'){
				$err++;
				$msg=$msg."<strong>- Jalur masuk tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$statMsk = new StatMasuk();
				$setStatMasuk = $statMsk->setStatMasuk($id, $nm_stat, $jns_msk);
				echo $setStatMasuk;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	function delstatmskAction(){
		$user = new Menu();
		$menu = "status/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$id = $param[0];
			$statMsk = new StatMasuk();
			$delStatMasuk = $statMsk->delStatMasuk($id);
			echo $delStatMasuk;
		}
	}
	
	// status her registrasi
	function insstatregAction(){
		$user = new Menu();
		$menu = "status/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id = $request['id_stat_reg'];
			$nm_stat = $this->_helper->string->esc_quote($request['nm_stat_reg']);
			$stat_akt = $request['stat_akt'];
			$krs = $request['krs'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiBetween(intval($id), 0, 99)=='F')or($vd->validasiLength($id, 2, 2)=='F')){
				$err++;
				$msg=$msg."<strong>- Kode status tidak boleh kosong, terdiri dari 2 digit angka</strong><br>";
			}
			if(($vd->validasiLength($nm_stat,1,15)=='F')or($vd->validasiAlNum($nm_stat)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama status tidak boleh kosong dan tidak boleh mengandung simbol</strong><br>".$nm_stat;
			}
			if($vd->validasiLength($stat_akt, 1, 99)=='F'){
				$err++;
				$msg=$msg."<strong>- Status keaktifan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($krs, 1, 2)=='F'){
				$err++;
				$msg=$msg."<strong>- Akses KRS tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$statReg = new StatReg();
				$setStatReg= $statReg->setStatReg($id, $nm_stat, $stat_akt, $krs);
				echo $setStatReg;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	function delstatregAction(){
		$user = new Menu();
		$menu = "status/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$id = $param[0];
			$statReg = new StatReg();
			$delStatReg = $statReg->delStatReg($id);
			echo $delStatReg;
		}
	}
	
	// nama kelas
	function insnmklsAction(){
		$user = new Menu();
		$menu = "nmkelas/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id = $request['id_nm'];
			$nm_kls = $this->_helper->string->esc_quote($request['nm']);
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiLength($id, 1, 5)=='F')or($vd->validasiAlNumNoSpace($id)=='F')){
				$err++;
				$msg=$msg."<strong>- Kode nama kelas tidak boleh kosong, maksimal 5 karakter tanpa simbol dan spasi</strong><br>";
			}
			if(($vd->validasiLength($nm_kls,1,30)=='F')or($vd->validasiAlNum($nm_kls)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama kelas tidak boleh kosong dan tidak boleh mengandung simbol</strong><br>";
			}
			if($err==0){
				// set database
				$nmKls = new Nmkelas();
				$setNmKelas = $nmKls->setNmKelas($id, $nm_kls);
				echo $setNmKelas;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	function delnmklsAction(){
		$user = new Menu();
		$menu = "nmkelas/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$id = $param[0];
			$nmKls = new Nmkelas();
			$delNmKelas = $nmKls->delNmKelas($id);
			echo $delNmKelas;
		}
	}

	function insjdwlujianAction(){
		$user = new Menu();
		$menu = "jadwalujian/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd_paket_kelas = $request['kd_pkt'];
			$kd_periode = $request['per'];
			$id_slot = $request['id_slot'];
			$tgl = $request['tgl'];
			$id_ruangan = $request['id_room'];
			$jns_ujian=$request['jns'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($kd_paket_kelas,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Paket kelas (nama kelas, dosen, mata kuliah) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($id_ruangan,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Ruangan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($id_slot,1,2)=='F'){
				$err++;
				$msg=$msg."<strong>- Slot tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($tgl,1,30)=='F'){
				$err++;
				$msg=$msg."<strong>- Tanggal tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$jadwal = new JadwalUjian();
				$setJadwal = $jadwal->setJadwal($kd_periode, $tgl, $id_slot, $id_ruangan, $kd_paket_kelas, $jns_ujian);
				echo $setJadwal;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	function deljdwlujianAction(){
		$user = new Menu();
		$menu = "jadwalujian/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
			$kd_periode = $param[0];
			$tgl = $param[1];
			$id_slot = $param[2];
			$kd_ruangan = $param[3];
			$jadwal = new JadwalUjian();
			$delJadwal = $jadwal->delJadwal($kd_periode, $tgl, $id_slot, $kd_ruangan);
			echo $delJadwal;
		}
	}
	
	function updjdwlujianAction(){
		$user = new Menu();
		$menu = "jadwalujian/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd_paket_kelas = $request['paket'];
			$kd_periode = $request['per'];
			$old_id_slot = $request['old_sl'];
			$new_id_slot = $request['new_sl'];
			$old_tgl = $request['old_tgl'];
			$new_tgl = $request['new_tgl'];
			$old_kd_ruangan = $request['old_ro'];
			$new_kd_ruangan = $request['new_ro'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($kd_paket_kelas,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Paket kelas (nama kelas, dosen, mata kuliah) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($new_kd_ruangan,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Ruangan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($new_tgl,1,30)=='F'){
				$err++;
				$msg=$msg."<strong>- Tanggal tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($new_id_slot,1,2)=='F'){
				$err++;
				$msg=$msg."<strong>- Slot tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$jadwal = new JadwalUjian();
				$updJadwal = $jadwal->updJadwal($kd_periode,$new_tgl,$new_id_slot, $new_kd_ruangan, $kd_paket_kelas, $old_tgl, $old_id_slot, $old_kd_ruangan);
				echo $updJadwal." ".$old_id_slot."-".$old_kd_ruangan."-".$old_tgl;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	// tim teaching
	function insttAction(){
		$user = new Menu();
		$menu = "paketkelas/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd_kls = $request['kd_kls2'];
			$kd_dosen = $request['dsn'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($kd_kls,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Kode kelas tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($kd_dosen,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Data dosen tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$tt = new TimTeaching();
				$setTt = $tt->setTimTeaching($kd_kls,$kd_dosen);
				echo $setTt;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delttAction(){
		$user = new Menu();
		$menu = "paketkelas/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    		$id = $param[0];
			$tt = new TimTeaching();
			$delTt = $tt->delTimTeaching($id);
			echo $delTt;
		}
	}
}
?>