<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Ajax 4 Controller -> Controller untuk submit via ajax (4)
*/
class Ajax4Controller extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('WsConfig');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Kaprodi');
		Zend_Loader::loadClass('ProdiInfo0');
		Zend_Loader::loadClass('ProdiSkpiLabel');
		Zend_Loader::loadClass('ProdiCapaianKkni');
		Zend_Loader::loadClass('ProdiCapaianAsosiasi');
		Zend_Loader::loadClass('ProdiCapaianPtMajor');
		Zend_Loader::loadClass('ProdiCapaianPtMinor');
		Zend_Loader::loadClass('ProdiCapaianPtOther');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('ConfigKurikulum');
		Zend_Loader::loadClass('FeederKls');
		Zend_Loader::loadClass('FeederKrs');
		Zend_Loader::loadClass('FeederProdi');
		Zend_Loader::loadClass('FeederMhs');
		Zend_Loader::loadClass('FeederMhsOut');
		Zend_Loader::loadClass('FeederMk');
		Zend_Loader::loadClass('FeederDsn');
		Zend_Loader::loadClass('FeederPaketKelas');
		Zend_Loader::loadClass('FeederAkm');
		Zend_Loader::loadClass('Validation');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Zend_Session');
		$auth = Zend_Auth::getInstance();
		$ses_ac = new Zend_Session_Namespace('ses_ac');
		if (($auth->hasIdentity())and($ses_ac->uname)) {

		}else{
			echo "F|Sesi anda sudah habis. Silakan login ulang!|";
		}
		// disabel layout
		$this->_helper->layout->disableLayout();
	}
	
	// PT pofile
	function updptAction(){
		$user = new Menu();
		$menu = "pt/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd_pt = trim($request['kd_pt']);
			$nm = trim($request['nm']);
			$nick = trim($request['nick']);
			$almt = $this->_helper->string->esc_quote(trim($request['almt']));
			$kota = $this->_helper->string->esc_quote(trim($request['kota']));
			$email = trim($request['email']);
			$web = trim($request['web']);
			$telp = str_replace("_", "", trim($request['telp']));
			$fax = str_replace("_", "", trim($request['fax']));
			$visi = $this->_helper->string->esc_quote($request['visi']);
			$misi = $this->_helper->string->esc_quote($request['misi']);
			$old_kd = trim($request['old_kd']);
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiDigit($old_kd)=='F'){
				$err++;
				$msg=$msg."<strong>- Kode PT lama tidak boleh kosong dan hanya diperbolehkan angka</strong><br>";
			}
			if($vd->validasiDigit($kd_pt)=='F'){
				$err++;
				$msg=$msg."<strong>- Kode PT tidak boleh kosong dan hanya diperbolehkan angka</strong><br>";
			}
			if($vd->validasiLength($nm,1,50)=='F'){
				$err++;
				$msg=$msg."<strong>- Nama PT tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($almt,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Alamat tidak boleh kosong dan maksimal 100 karakter</strong><br>";
			}
			if($vd->validasiLength($kota,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Kota tidak boleh kosong</strong><br>";
			}
			if($vd->validasiEmail($email)=='F'){
				$err++;
				$msg=$msg."<strong>- Format email harus benar</strong><br>";
			}
			if($vd->validasiLength($web,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Website tidak boleh kosong</strong><br>";
			}
			if ($vd->validasiLength($telp,7,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Nomor telepon tidak benar, isikan 0 di setiap isian bila belum ada</strong><br>";
			}
			if ($vd->validasiLength($fax,7,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Fax tidak benar, isikan 0 di setiap isian bila belum ada</strong><br>";
			}
			if($err==0){
				// set database
				$profile = new Profile();
				$updPt = $profile->updPt($kd_pt,$nm,$almt,$kota,$visi,$misi,$nick,$email,$web,$telp,$fax,$old_kd);
				// update session
				$getProfil = $profile->fetchAll();
				if($getProfil){
					foreach ($getProfil as $dataProfil) {
						$kd_pt=$dataProfil['kode_pt'];
						$nm_pt=$dataProfil['nama_pt'];
						$alamat=$dataProfil['alamat'];
						$kota=$dataProfil['kota'];
						$nick=$dataProfil['nickname'];
						$web=$dataProfil['web'];
					}
				}
				$ses_ac = new Zend_Session_Namespace('ses_ac');
				$ses_ac->kd_pt = $kd_pt;
				$ses_ac->nm_pt = $nm_pt;
				$ses_ac->alamat = $alamat.", ".$kota;
				$ses_ac->nick = $nick;
				$ses_ac->web = $web;
				echo $updPt;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	function upllogoAction(){
		$user = new Menu();
		$menu = "pt/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			 if (0<$_FILES["file"]["error"] ) {
		        echo "Error: ". $_FILES["file"]["error"] . "<br>";
		    }
		    else {
		    	$temp = explode(".", $_FILES["file"]["name"]);
				$newfilename = 'logo.' . strtolower(end($temp));
				$path = __FILE__;
				$filePath = str_replace('academic/application/controllers/Ajax4Controller.php','public/img',$path);
				$target_dir = $filePath;
				$target_file = str_replace("'", "", $target_dir ."/'". $newfilename);
				$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
				$mimes = array('image/png');
				$msg="";
				if(!in_array($_FILES['file']['type'],$mimes)){
				   	echo "File harus berformat PNG! <br>";
				}else{
					if ($_FILES["file"]["size"] > (100*1024)) {
					    echo "File Logo tidak boleh lebih dari 100 KB";
					}else{
						move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
						echo "Upload Logo PT Sukses!";
					}
				}
			}
		}
	}
	
	// prodi
	function insprdAction(){
		$user = new Menu();
		$menu = "prodi/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd = trim($request['kd']);
			$nm = $this->_helper->string->esc_quote(trim($request['nm']));
			$jenjang = trim($request['jenjang']);
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiDigit($kd)=='F')or($vd->validasiLength($kd, 1, 10)=='F')){
				$err++;
				$msg=$msg."<strong>- Kode prodi tidak boleh kosong dan hanya diperbolehkan angka </strong><br>";
			}
			if($vd->validasiLength($nm,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Nama prodi tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($jenjang,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Jenjang prodi tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$prodi = new Prodi();
				$setProdi = $prodi->setProdi($kd, $nm, $jenjang);
				echo $setProdi;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	function delprdAction(){
		$user = new Menu();
		$menu = "prodi/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$kd = $param[0];
			$prodi = new Prodi();
			$delProdi = $prodi->delProdi($kd);
			echo $delProdi;
		}
	}
	
	function updprdAction(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd = trim($request['kd']);
			$nm = $this->_helper->string->esc_quote(trim($request['nm']));
			$jenjang = trim($request['jenjang']);
			$old_kd = trim($request['old_kd']);
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiDigit($old_kd)=='F')or($vd->validasiLength($old_kd, 1, 10)=='F')){
				$err++;
				$msg=$msg."<strong>- Kode prodi lama tidak boleh kosong dan hanya diperbolehkan angka </strong><br>";
			}
			if(($vd->validasiDigit($kd)=='F')or($vd->validasiLength($kd, 1, 10)=='F')){
				$err++;
				$msg=$msg."<strong>- Kode prodi tidak boleh kosong dan hanya diperbolehkan angka </strong><br>";
			}
			if($vd->validasiLength($nm,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Nama prodi tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($jenjang,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Jenjang prodi tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$prodi = new Prodi();
				$updProdi = $prodi->updProdi($kd, $nm, $jenjang,$old_kd);
				echo $updProdi;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	// info prodi
	function insprdinfoAction(){
		$user = new Menu();
		$menu = "prodi/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd = trim($request['kd']);
			$gelar_id = $this->_helper->string->esc_quote(trim($request['gelar_id']));
			$gelar_en = $this->_helper->string->esc_quote(trim($request['gelar_en']));
			$jenis_pend_id = $this->_helper->string->esc_quote(trim($request['jenis_pend_id']));
			$jenis_pend_en = $this->_helper->string->esc_quote(trim($request['jenis_pend_en']));
			$req_pend_id = $this->_helper->string->esc_quote(trim($request['req_pend_id']));
			$req_pend_en = $this->_helper->string->esc_quote(trim($request['req_pend_en']));
			$bahasa_id = $this->_helper->string->esc_quote(trim($request['bahasa_id']));
			$bahasa_en = $this->_helper->string->esc_quote(trim($request['bahasa_en']));
			$lanjut_id = $this->_helper->string->esc_quote(trim($request['lanjut_id']));
			$lanjut_en = $this->_helper->string->esc_quote(trim($request['lanjut_en']));
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiDigit($kd)=='F')or($vd->validasiLength($kd, 1, 10)=='F')){
				$err++;
				$msg=$msg."<strong>- Kode prodi tidak boleh kosong dan hanya diperbolehkan angka </strong><br>";
			}
			if($vd->validasiLength($gelar_id,1,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Gelar (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($gelar_en,1,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Gelar (english) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($jenis_pend_id,1,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Jenis Pendidikan (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($jenis_pend_en,1,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Jenis Pendidikan (english) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($bahasa_id,1,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Bahasa (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($bahasa_en,1,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Bahasa (english) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($lanjut_id,1,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Studi lanjutan (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($lanjut_en,1,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Studi lanjutan (english) tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$prodiinfo0 = new ProdiInfo0();
				$setProdiInfo = $prodiinfo0->setProdiInfo0($kd,$gelar_id,$gelar_en,$jenis_pend_id,$jenis_pend_en,$req_pend_id,$req_pend_en,$bahasa_id,$bahasa_en,$lanjut_id,$lanjut_en);
				echo $setProdiInfo;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delprdinfoAction(){
		$user = new Menu();
		$menu = "prodi/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$kd = $param[0];
			$prodiinfo = new ProdiInfo0();
			$delProdiInfo = $prodiinfo->delProdiInfo0($kd);
			echo $delProdiInfo;
		}
	}

	function updprdinfoAction(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd = trim($request['kd']);
			$gelar_id = $this->_helper->string->esc_quote(trim($request['gelar_id']));
			$gelar_en = $this->_helper->string->esc_quote(trim($request['gelar_en']));
			$jenis_pend_id = $this->_helper->string->esc_quote(trim($request['jenis_pend_id']));
			$jenis_pend_en = $this->_helper->string->esc_quote(trim($request['jenis_pend_en']));
			$req_pend_id = $this->_helper->string->esc_quote(trim($request['req_pend_id']));
			$req_pend_en = $this->_helper->string->esc_quote(trim($request['req_pend_en']));
			$bahasa_id = $this->_helper->string->esc_quote(trim($request['bahasa_id']));
			$bahasa_en = $this->_helper->string->esc_quote(trim($request['bahasa_en']));
			$lanjut_id = $this->_helper->string->esc_quote(trim($request['lanjut_id']));
			$lanjut_en = $this->_helper->string->esc_quote(trim($request['lanjut_en']));
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiDigit($kd)=='F')or($vd->validasiLength($kd, 1, 10)=='F')){
				$err++;
				$msg=$msg."<strong>- Kode prodi tidak boleh kosong dan hanya diperbolehkan angka </strong><br>";
			}
			if($vd->validasiLength($gelar_id,1,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Gelar (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($gelar_en,1,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Gelar (english) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($jenis_pend_id,1,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Jenis Pendidikan (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($jenis_pend_en,1,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Jenis Pendidikan (english) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($bahasa_id,1,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Bahasa (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($bahasa_en,1,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Bahasa (english) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($lanjut_id,1,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Studi lanjutan (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($lanjut_en,1,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Studi lanjutan (english) tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$prodiinfo = new ProdiInfo0();
				$updProdiInfo0 = $prodiinfo->updProdiInfo0($kd,$gelar_id,$gelar_en,$jenis_pend_id,$jenis_pend_en,$req_pend_id,$req_pend_en,$bahasa_id,$bahasa_en,$lanjut_id,$lanjut_en,$kd);
				echo $updProdiInfo0;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	// prodi label skpi
	function insprdskpilabelAction(){
		$user = new Menu();
		$menu = "prodi/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd = trim($request['kd']);
			$capaian_kkni_id = $this->_helper->string->esc_quote(trim($request['capaian_kkni_id']));
			$capaian_kkni_en = $this->_helper->string->esc_quote(trim($request['capaian_kkni_en']));
			$capaian_asosiasi_id = $this->_helper->string->esc_quote(trim($request['capaian_asosiasi_id']));
			$capaian_asosiasi_en = $this->_helper->string->esc_quote(trim($request['capaian_asosiasi_en']));
			$capaian_pt_major_id = $this->_helper->string->esc_quote(trim($request['capaian_pt_major_id']));
			$capaian_pt_major_en = $this->_helper->string->esc_quote(trim($request['capaian_pt_major_en']));
			$capaian_pt_minor_id = $this->_helper->string->esc_quote(trim($request['capaian_pt_minor_id']));
			$capaian_pt_minor_en = $this->_helper->string->esc_quote(trim($request['capaian_pt_minor_en']));
			$capaian_pt_other_id = $this->_helper->string->esc_quote(trim($request['capaian_pt_other_id']));
			$capaian_pt_other_en = $this->_helper->string->esc_quote(trim($request['capaian_pt_other_en']));

			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiDigit($kd)=='F')or($vd->validasiLength($kd, 1, 10)=='F')){
				$err++;
				$msg=$msg."<strong>- Kode prodi tidak boleh kosong dan hanya diperbolehkan angka </strong><br>";
			}
			if($vd->validasiLength($capaian_kkni_id,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Capaian KKNI (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($capaian_kkni_en,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Capaian KKNI (english) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($capaian_asosiasi_id,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Capaian Asosiasi (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($capaian_asosiasi_en,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Capaian Asosiasi (english) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($capaian_pt_major_id,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Capaian Perguruan Tinggi (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($capaian_pt_major_en,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Capaian Perguruan Tinggi (english) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($capaian_pt_minor_id,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Capaian Perguruan Tinggi (Pendukung) (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($capaian_pt_minor_en,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Capaian Perguruan Tinggi (Pendukung) (english) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($capaian_pt_other_id,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Capaian Lain (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($capaian_pt_other_en,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Capaian Lain (english) tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$prodilabelskpi = new ProdiSkpiLabel();
				$setProdiLabaelSkpi = $prodilabelskpi->setProdiSkpiLabel($kd,$capaian_kkni_id,$capaian_kkni_en,$capaian_asosiasi_id,$capaian_asosiasi_en,$capaian_pt_major_id,$capaian_pt_major_en,$capaian_pt_minor_id,$capaian_pt_minor_en,$capaian_pt_other_id,$capaian_pt_other_en);
				echo $setProdiLabaelSkpi;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	function delprdskpilabelAction(){
		$user = new Menu();
		$menu = "prodi/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$kd = $param[0];
			$prodiskpilabel = new ProdiSkpiLabel();
			$delProdiSkpiLabel = $prodiskpilabel->delProdiSkpiLabel($kd);
			echo $delProdiSkpiLabel;
		}
	}
	function updprdskpilabelAction(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id = trim($request['id']);
			$kd = trim($request['kd']);
			$capaian_kkni_id = $this->_helper->string->esc_quote(trim($request['capaian_kkni_id']));
			$capaian_kkni_en = $this->_helper->string->esc_quote(trim($request['capaian_kkni_en']));
			$capaian_asosiasi_id = $this->_helper->string->esc_quote(trim($request['capaian_asosiasi_id']));
			$capaian_asosiasi_en = $this->_helper->string->esc_quote(trim($request['capaian_asosiasi_en']));
			$capaian_pt_major_id = $this->_helper->string->esc_quote(trim($request['capaian_pt_major_id']));
			$capaian_pt_major_en = $this->_helper->string->esc_quote(trim($request['capaian_pt_major_en']));
			$capaian_pt_minor_id = $this->_helper->string->esc_quote(trim($request['capaian_pt_minor_id']));
			$capaian_pt_minor_en = $this->_helper->string->esc_quote(trim($request['capaian_pt_minor_en']));
			$capaian_pt_other_id = $this->_helper->string->esc_quote(trim($request['capaian_pt_other_id']));
			$capaian_pt_other_en = $this->_helper->string->esc_quote(trim($request['capaian_pt_other_en']));
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiDigit($kd)=='F')or($vd->validasiLength($kd, 1, 10)=='F')){
				$err++;
				$msg=$msg."<strong>- Kode prodi tidak boleh kosong dan hanya diperbolehkan angka </strong><br>";
			}
			if($vd->validasiLength($capaian_kkni_id,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Capaian KKNI (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($capaian_kkni_en,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Capaian KKNI (english) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($capaian_asosiasi_id,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Capaian Asosiasi (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($capaian_asosiasi_en,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Capaian Asosiasi (english) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($capaian_pt_major_id,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Capaian Perguruan Tinggi (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($capaian_pt_major_en,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Capaian Perguruan Tinggi (english) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($capaian_pt_minor_id,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Capaian Perguruan Tinggi (Pendukung) (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($capaian_pt_minor_en,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Capaian Perguruan Tinggi (Pendukung) (english) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($capaian_pt_other_id,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Capaian Lain (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($capaian_pt_other_en,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Capaian Lain (english) tidak boleh kosong</strong><br>";
			}
			
			if($err==0){
				// set database
				$prodiinfo = new ProdiSkpiLabel();
				$updProdiSkpiLabel = $prodiinfo->updProdiSkpiLabel($kd,$capaian_kkni_id,$capaian_kkni_en,$capaian_asosiasi_id,$capaian_asosiasi_en,$capaian_pt_major_id,$capaian_pt_major_en,$capaian_pt_minor_id,$capaian_pt_minor_en,$capaian_pt_other_id,$capaian_pt_other_en,$id);
				echo $updProdiSkpiLabel;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	// prodi capaian kkni
	function insprdcapaian1Action(){
		$user = new Menu();
		$menu = "prodi/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id_skpi_label = trim($request['id']);
			$urutan = $this->_helper->string->esc_quote(trim($request['urutan']));
			$is_numbered = $this->_helper->string->esc_quote(trim($request['is_numbered']));
			$keterangan_id = $this->_helper->string->esc_quote(trim($request['keterangan_id']));
			$keterangan_en = $this->_helper->string->esc_quote(trim($request['keterangan_en']));
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($urutan,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Urutan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($is_numbered,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Pilihan Ada Nomer? tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($keterangan_id,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Keterangan (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($keterangan_en,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Keterangan (english) tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$prodicapaian1 = new ProdiCapaianKkni();
				$setProdiCapaianKkni = $prodicapaian1->setProdiCapaianKkni($id_skpi_label,$urutan,$is_numbered,$keterangan_id,$keterangan_en);
				echo $setProdiCapaianKkni;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	function delprdcapaian1Action(){
		$user = new Menu();
		$menu = "prodi/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$kd = $param[0];
			$prodiskpilabel = new ProdiCapaianKkni();
			$delProdiCapaianKkni = $prodiskpilabel->delProdiCapaianKkni($kd);
			echo $delProdiCapaianKkni;
		}
	}
	function updprdcapaian1Action(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id = trim($request['id']);
			$id_skpi_label = trim($request['id_prodi_skpi_label']);
			$urutan = $this->_helper->string->esc_quote(trim($request['urutan']));
			$is_numbered = $this->_helper->string->esc_quote(trim($request['is_numbered']));
			$keterangan_id = $this->_helper->string->esc_quote(trim($request['keterangan_id']));
			$keterangan_en = $this->_helper->string->esc_quote(trim($request['keterangan_en']));
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($urutan,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Urutan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($is_numbered,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Pilihan Ada Nomer? tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($keterangan_id,1,1000)=='F'){
				$err++;
				$msg=$msg."<strong>- Keterangan (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($keterangan_en,1,1000)=='F'){
				$err++;
				$msg=$msg."<strong>- Keterangan (english) tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$prodikkni = new ProdiCapaianKkni();
				$updProdiCapaianKkni = $prodikkni->updProdiCapaianKkni($id_skpi_label,$urutan,$is_numbered,$keterangan_id,$keterangan_en,$id);
				echo $updProdiCapaianKkni;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	//prodi capaian asosiasi
	function insprdcapaian2Action(){
		$user = new Menu();
		$menu = "prodi/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id_skpi_label = trim($request['id']);
			$urutan = $this->_helper->string->esc_quote(trim($request['urutan']));
			$is_numbered = $this->_helper->string->esc_quote(trim($request['is_numbered']));
			$keterangan_id = $this->_helper->string->esc_quote(trim($request['keterangan_id']));
			$keterangan_en = $this->_helper->string->esc_quote(trim($request['keterangan_en']));
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($urutan,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Urutan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($is_numbered,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Pilihan Ada Nomer? tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($keterangan_id,1,1000)=='F'){
				$err++;
				$msg=$msg."<strong>- Keterangan (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($keterangan_en,1,1000)=='F'){
				$err++;
				$msg=$msg."<strong>- Keterangan (english) tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$prodicapaian2 = new ProdiCapaianAsosiasi();
				$setProdiCapaianAsosiasi = $prodicapaian2->setProdiCapaianAsosiasi($id_skpi_label,$urutan,$is_numbered,$keterangan_id,$keterangan_en);
				echo $setProdiCapaianAsosiasi;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	function delprdcapaian2Action(){
		$user = new Menu();
		$menu = "prodi/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$kd = $param[0];
			$prodiskpilabel = new ProdiCapaianAsosiasi();
			$delProdiCapaianAsosiasi = $prodiskpilabel->delProdiCapaianAsosiasi($kd);
			echo $delProdiCapaianAsosiasi;
		}
	}
	function updprdcapaian2Action(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id = trim($request['id']);
			$id_skpi_label = trim($request['id_prodi_skpi_label']);
			$urutan = $this->_helper->string->esc_quote(trim($request['urutan']));
			$is_numbered = $this->_helper->string->esc_quote(trim($request['is_numbered']));
			$keterangan_id = $this->_helper->string->esc_quote(trim($request['keterangan_id']));
			$keterangan_en = $this->_helper->string->esc_quote(trim($request['keterangan_en']));
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($urutan,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Urutan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($is_numbered,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Pilihan Ada Nomer? tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($keterangan_id,1,1000)=='F'){
				$err++;
				$msg=$msg."<strong>- Keterangan (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($keterangan_en,1,1000)=='F'){
				$err++;
				$msg=$msg."<strong>- Keterangan (english) tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$prodiasosiasi = new ProdiCapaianAsosiasi();
				$updProdiCapaianAsosiasi = $prodiasosiasi->updProdiCapaianAsosiasi($id_skpi_label,$urutan,$is_numbered,$keterangan_id,$keterangan_en,$id);
				echo $updProdiCapaianAsosiasi;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	// prodi capaian pt major
	function insprdcapaian3Action(){
		$user = new Menu();
		$menu = "prodi/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id_skpi_label = trim($request['id']);
			$urutan = $this->_helper->string->esc_quote(trim($request['urutan']));
			$is_numbered = $this->_helper->string->esc_quote(trim($request['is_numbered']));
			$keterangan_id = $this->_helper->string->esc_quote(trim($request['keterangan_id']));
			$keterangan_en = $this->_helper->string->esc_quote(trim($request['keterangan_en']));
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($urutan,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Urutan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($is_numbered,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Pilihan Ada Nomer? tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($keterangan_id,1,1000)=='F'){
				$err++;
				$msg=$msg."<strong>- Keterangan (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($keterangan_en,1,1000)=='F'){
				$err++;
				$msg=$msg."<strong>- Keterangan (english) tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$prodicapaian3 = new ProdiCapaianPtMajor();
				$setProdiCapaianPtMajor = $prodicapaian3->setProdiCapaianPtMajor($id_skpi_label,$urutan,$is_numbered,$keterangan_id,$keterangan_en);
				echo $setProdiCapaianPtMajor;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	function delprdcapaian3Action(){
		$user = new Menu();
		$menu = "prodi/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$kd = $param[0];
			$prodiskpilabel = new ProdiCapaianPtMajor();
			$delProdiCapaianPtMajor = $prodiskpilabel->delProdiCapaianPtMajor($kd);
			echo $delProdiCapaianPtMajor;
		}
	}
	function updprdcapaian3Action(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id = trim($request['id']);
			$id_skpi_label = trim($request['id_prodi_skpi_label']);
			$urutan = $this->_helper->string->esc_quote(trim($request['urutan']));
			$is_numbered = $this->_helper->string->esc_quote(trim($request['is_numbered']));
			$keterangan_id = $this->_helper->string->esc_quote(trim($request['keterangan_id']));
			$keterangan_en = $this->_helper->string->esc_quote(trim($request['keterangan_en']));
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($urutan,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Urutan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($is_numbered,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Pilihan Ada Nomer? tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($keterangan_id,1,1000)=='F'){
				$err++;
				$msg=$msg."<strong>- Keterangan (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($keterangan_en,1,1000)=='F'){
				$err++;
				$msg=$msg."<strong>- Keterangan (english) tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$prodiptmajor = new ProdiCapaianPtMajor();
				$updProdiCapaianPtMajor = $prodiptmajor->updProdiCapaianPtMajor($id_skpi_label,$urutan,$is_numbered,$keterangan_id,$keterangan_en,$id);
				echo $updProdiCapaianPtMajor;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	// prodi capaian pt minor
	function insprdcapaian4Action(){
		$user = new Menu();
		$menu = "prodi/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id_skpi_label = trim($request['id']);
			$urutan = $this->_helper->string->esc_quote(trim($request['urutan']));
			$is_numbered = $this->_helper->string->esc_quote(trim($request['is_numbered']));
			$keterangan_id = $this->_helper->string->esc_quote(trim($request['keterangan_id']));
			$keterangan_en = $this->_helper->string->esc_quote(trim($request['keterangan_en']));
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($urutan,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Urutan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($is_numbered,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Pilihan Ada Nomer? tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($keterangan_id,1,1000)=='F'){
				$err++;
				$msg=$msg."<strong>- Keterangan (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($keterangan_en,1,1000)=='F'){
				$err++;
				$msg=$msg."<strong>- Keterangan (english) tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$prodicapaian4 = new ProdiCapaianPtMinor();
				$setProdiCapaianPtMinor = $prodicapaian4->setProdiCapaianPtMinor($id_skpi_label,$urutan,$is_numbered,$keterangan_id,$keterangan_en);
				echo $setProdiCapaianPtMinor;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	function delprdcapaian4Action(){
		$user = new Menu();
		$menu = "prodi/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$kd = $param[0];
			$prodiskpilabel = new ProdiCapaianPtMinor();
			$delProdiCapaianPtMinor = $prodiskpilabel->delProdiCapaianPtMinor($kd);
			echo $delProdiCapaianPtMinor;
		}
	}
	function updprdcapaian4Action(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id = trim($request['id']);
			$id_skpi_label = trim($request['id_prodi_skpi_label']);
			$urutan = $this->_helper->string->esc_quote(trim($request['urutan']));
			$is_numbered = $this->_helper->string->esc_quote(trim($request['is_numbered']));
			$keterangan_id = $this->_helper->string->esc_quote(trim($request['keterangan_id']));
			$keterangan_en = $this->_helper->string->esc_quote(trim($request['keterangan_en']));
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($urutan,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Urutan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($is_numbered,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Pilihan Ada Nomer? tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($keterangan_id,1,1000)=='F'){
				$err++;
				$msg=$msg."<strong>- Keterangan (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($keterangan_en,1,1000)=='F'){
				$err++;
				$msg=$msg."<strong>- Keterangan (english) tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$prodiptminor = new ProdiCapaianPtMinor();
				$updProdiCapaianPtMinor = $prodiptminor->updProdiCapaianPtMinor($id_skpi_label,$urutan,$is_numbered,$keterangan_id,$keterangan_en,$id);
				echo $updProdiCapaianPtMinor;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	// prodi capaian pt other
	function insprdcapaian5Action(){
		$user = new Menu();
		$menu = "prodi/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id_skpi_label = trim($request['id']);
			$urutan = $this->_helper->string->esc_quote(trim($request['urutan']));
			$is_numbered = $this->_helper->string->esc_quote(trim($request['is_numbered']));
			$keterangan_id = $this->_helper->string->esc_quote(trim($request['keterangan_id']));
			$keterangan_en = $this->_helper->string->esc_quote(trim($request['keterangan_en']));
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($urutan,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Urutan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($is_numbered,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Pilihan Ada Nomer? tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($keterangan_id,1,1000)=='F'){
				$err++;
				$msg=$msg."<strong>- Keterangan (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($keterangan_en,1,1000)=='F'){
				$err++;
				$msg=$msg."<strong>- Keterangan (english) tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$prodicapaian5 = new ProdiCapaianPtOther();
				$setProdiCapaianPtOther = $prodicapaian5->setProdiCapaianPtOther($id_skpi_label,$urutan,$is_numbered,$keterangan_id,$keterangan_en);
				echo $setProdiCapaianPtOther;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	function delprdcapaian5Action(){
		$user = new Menu();
		$menu = "prodi/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$kd = $param[0];
			$prodiskpilabel = new ProdiCapaianPtOther();
			$delProdiCapaianPtOther = $prodiskpilabel->delProdiCapaianPtOther($kd);
			echo $delProdiCapaianPtOther;
		}
	}
	function updprdcapaian5Action(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id = trim($request['id']);
			$id_skpi_label = trim($request['id_prodi_skpi_label']);
			$urutan = $this->_helper->string->esc_quote(trim($request['urutan']));
			$is_numbered = $this->_helper->string->esc_quote(trim($request['is_numbered']));
			$keterangan_id = $this->_helper->string->esc_quote(trim($request['keterangan_id']));
			$keterangan_en = $this->_helper->string->esc_quote(trim($request['keterangan_en']));
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($urutan,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Urutan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($is_numbered,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Pilihan Ada Nomer? tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($keterangan_id,1,1000)=='F'){
				$err++;
				$msg=$msg."<strong>- Keterangan (indonesia) tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($keterangan_en,1,1000)=='F'){
				$err++;
				$msg=$msg."<strong>- Keterangan (english) tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$prodiptother = new ProdiCapaianPtOther();
				$updProdiCapaianPtOther = $prodiptother->updProdiCapaianPtOther($id_skpi_label,$urutan,$is_numbered,$keterangan_id,$keterangan_en,$id);
				echo $updProdiCapaianPtOther;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	// kaprodi
	function inskaprodiAction(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			$request = $this->getRequest()->getPost();
			$prd = $this->_helper->string->esc_quote(trim($request['prd']));
			$dsn = $this->_helper->string->esc_quote(trim($request['dsn']));
			$tgl = $this->_helper->string->esc_quote(trim($request['tgl']));
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiLength($dsn,1,100)=='F')or($dsn=='null')){
				$err++;
				$msg=$msg."<strong>- Data kaprodi tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($tgl,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Tanggal tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// start uploading
				if (0<$_FILES["filez1"]["error"]) {
					$msg= "F|Error: ". $_FILES["filez1"]["error"];
					echo $msg;
				}
				else {
					$kprd=new Kaprodi();
					$setKaprodi=$kprd->setKaprodi($dsn,$prd,$tgl);
					$arrSetKaprodi=explode("|", $setKaprodi);
					if($arrSetKaprodi[0]!='F'){
						// insert temp database
						$filename=$dsn;
						$newfilename = $filename.'.png';
						$path = __FILE__;
						$filePath = str_replace('academic/application/controllers/Ajax4Controller.php','public/file/dsn/ttd',$path);						$target_dir1=$file_url.'//nik';
						$target_dir = $filePath;
						$target_file = str_replace("'", "", $target_dir ."/". $newfilename);
						$uploadOk = 1;
						$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
						$mimes = array('image/png');
						$msg="";
						if(!in_array($_FILES['filez1']['type'],$mimes)){
							$msg=$msg."File Tanda tangan harus PNG! <br> nama:".$_FILES["filez1"]["name"];
							$uploadOk = 0;
						}
						// Check if file already exists
						if (file_exists($target_file)) {
							$msg= $msg."File sudah ada, silakan coba lagi<br>";
							$uploadOk = 0;
						}
						// Check file size
						if ($_FILES["filez1"]["size"] > (1024*2000)) {
							$msg= $msg."File maksimal 2 MB<br>";
							$uploadOk = 0;
						}
						// Check if $uploadOk is set to 0 by an error
						if ($uploadOk != 0) {
							if (move_uploaded_file($_FILES["filez1"]["tmp_name"], $target_file)){
								$msg=$setKaprodi.".<br>File berhasil diupload";
								echo $msg;
							}else{
								$delKaprodi=$kprd->delKaprodi($arrSetKaprodi[0]);
								$msg= "F|Maaf terjadi error saat upload file, silakan coba lagi.". $_FILES["filez1"]["error"];
								echo $msg;
							}
						}else{
							$delKaprodi=$kprd->delKaprodi($arrSetKaprodi[0]);
							$msg= "F|Maaf terjadi error saat upload : <br>".$msg;
							echo $msg;
						}
					}else{
						echo $setKaprodi;
					}
				}
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delkaprodiAction(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    		$id = $param[0];
			$dsn= $param[1];
	    		// unlink foto
	    		unlink('../public/file/dsn/ttd/'.$dsn.'.png');
			$kaprodi = new Kaprodi();
			$delKaprodi = $kaprodi->delKaprodi($id);
			echo $delKaprodi;
		}
	}
	
	// User
	function insuserAction(){
		$user = new Menu();
		$menu = "user/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$uname = trim($request['uname']);
			$nm = trim($request['nm']);
			$pwd1 = trim($request['pwd1']);
			$pwd2 = trim($request['pwd2']);
			$superadm = trim($request['superadm']);
			$email = trim($request['email']);
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($uname,1,25)=='F'){
				$err++;
				$msg=$msg."<strong>- Username tidak boleh kosong</strong><br>";
			}
			if($vd->validasiAlNumNoSpace($uname)=='F'){
				$err++;
				$msg=$msg."<strong>- Username hanya boleh mengandung huruf dan angka tanpa spasi</strong><br>";
			}
			if($vd->validasiLength($nm,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Nama tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($pwd1,1,30)=='F'){
				$err++;
				$msg=$msg."<strong>- Password tidak boleh kosong</strong><br>";
			}
			if(md5($pwd1)!=md5($pwd2)){
				$err++;
				$msg=$msg."<strong>- Password tidak sama</strong><br>";
			}
			if($err==0){
				// set database
				$user_acc = new User();
				$setUserAcc = $user_acc->setUserAcc($uname, $pwd1, $nm, $superadm, $email);
				echo $setUserAcc;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	function deluserAction(){
		$user = new Menu();
		$menu = "user/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$username = $param[0];
			$user_acc = new User();
			$delUserAcc = $user_acc->delUserAcc($username);
			echo $delUserAcc;
		}
	}

	function upduserAction(){
		$user = new Menu();
		$menu = "user/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$uname = trim($request['uname']);
			$nm = trim($request['nm']);
			$email = trim($request['email']);
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($uname,1,25)=='F'){
				$err++;
				$msg=$msg."<strong>- Username tidak boleh kosong</strong><br>";
			}
			if($vd->validasiAlNum($uname)=='F'){
				$err++;
				$msg=$msg."<strong>- Username hanya boleh mengandung huruf dan angka</strong><br>";
			}
			if($vd->validasiLength($nm,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Nama tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$user_acc = new User();
				$updUserAcc = $user_acc->updUserAcc($uname, $nm, $email);
				echo $updUserAcc;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	function upduserpwdAction(){
		$user = new Menu();
		$menu = "user/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$uname = trim($request['uname']);
			$pwd1 = trim($request['pwd1']);
			$pwd2 = trim($request['pwd2']);
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($uname,1,25)=='F'){
				$err++;
				$msg=$msg."<strong>- Username tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($pwd1,1,30)=='F'){
				$err++;
				$msg=$msg."<strong>- Password tidak boleh kosong</strong><br>";
			}
			if(md5($pwd1)!=md5($pwd2)){
				$err++;
				$msg=$msg."<strong>- Password tidak sama</strong><br>";
			}
			if($err==0){
				// set database
				$user_acc = new User();
				$updPasswordUserAcc = $user_acc->updPasswordUserAcc($uname, $pwd1);
				echo $updPasswordUserAcc;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	// User akses
	function insaksesAction(){
		$user = new Menu();
		$menu = "userakses/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$uname = $request['uname'];
			$id_menu = $request['id_menu'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($uname,1,25)=='F'){
				$err++;
				$msg=$msg."<strong>- Username tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($id_menu,1,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Akses menu tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$user_acc = new User();
				$setAksesAcc = $user_acc->setAksesAcc($uname, $id_menu);
				echo $setAksesAcc;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	function delaksesAction(){
		$user = new Menu();
		$menu = "userakses/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$username = $param[0];
	    	$id_menu = $param[1];
			$user_acc = new User();
			$delAksesAcc = $user_acc->delAksesAcc($username,$id_menu);
			echo $delAksesAcc;
		}
	}
	
	// Config feeder
	function updwsconfAction(){
		$user = new Menu();
		$menu = "zzfconfig/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// makes disable layout
			$this->_helper->getHelper('layout')->disableLayout();
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$frm=$this->_request->get('frm');
			$live = $request['live'.$frm];
			$url = trim($request['url'.$frm]);
			$uname = trim($request['uname'.$frm]);
			$pwd = trim($request['pwd'.$frm]);
			// set database
			$wsConfig = new WsConfig();
			$updWsConfig = $wsConfig->updWsConfig($url,$uname,$pwd,$live);
			echo $updWsConfig;
		}
	}

	function gentokenAction(){
		$user = new Menu();
		$menu = "zzfconfig/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// makes disable layout
			$this->_helper->getHelper('layout')->disableLayout();
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$frm=$this->_request->get('frm');
			$live = $request['live'.$frm];
			$url = trim($request['url'.$frm]);
			$uname = trim($request['uname'.$frm]);
			$pwd = trim($request['pwd'.$frm]);
			// generate
			if (empty($uname)) {
				echo "F|Username kosong tidak dapat membuat token";
			} else {
				$wsConfig = new WsConfig();
				$result_string = $wsConfig->getToken($uname, $pwd, $url);
				$result = json_decode($result_string, true);
				if(is_array($result)){
					if($result['error_desc']){
						echo "F|".$result['error_desc'];
					}else{
						$res_data=$result['data'];
						$token=$res_data['token'];
						if($token!=''){
							$ses_feeder =  new Zend_Session_Namespace('ses_feeder');
							$ses_feeder->token=$token;
							$ses_feeder->url=$url;
							$ses_feeder->uname=$uname;
							$ses_feeder->pwd=$pwd;
							// profil PT
							$result_string2 = $wsConfig->getProfilPT($token, $uname, $url);
							$result2=json_decode($result_string2,true);
							if($result2['error_desc']){
								echo "F|".$result2['error_desc'];
							}else{
								$res_data2=$result2['data'];
								$id_pt="";
								$nm_pt="";
								foreach ($res_data2 as $dtRes_data2){
									$id_pt=$dtRes_data2['id_perguruan_tinggi'];
									$nm_pt=$dtRes_data2['nama_perguruan_tinggi'];
								}
								if($id_pt!=''){
									$ses_feeder->id_sp=$id_pt;
									$ses_feeder->nm_lemb=$nm_pt;
									// time out
									$ses_feeder->setExpirationSeconds(1800,'token');
									echo "T|Token berhasil dibuat";
								}
							}
						}else{
							echo "F|Token tidak terbuat";	
						}
					}
				}else{
					echo "F|Error tidak terdefinisi";
				}
			}		
		}
	}
	
	function addkurfeederAction(){
		$user = new Menu();
		$menu = "zzfconfig/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kur = trim($request['kur']);
			$prd = trim($request['prd']);
			$per = trim($request['per']);
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($prd, 1, 10)=='F'){
				$err++;
				$msg=$msg."<strong>- prodi tidak boleh kosong </strong><br>";
			}
			if($vd->validasiLength($per,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Periode tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($kur,1,50)=='F'){
				$err++;
				$msg=$msg."<strong>- Kurikulum feeder tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$ckur = new ConfigKurikulum();
				$setKurikulumProdi = $ckur->setKurikulumProdi($prd, $per, $kur);
				echo $setKurikulumProdi;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	function delkurfeederAction(){
		$user = new Menu();
		$menu = "zzfconfig/index";
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
	    	$ckur = new ConfigKurikulum();
			$delKurikulumProdi = $ckur->delKurikulumProdi($prd, $per);
			echo $delKurikulumProdi;
		}
	}

	// mahasiswa-feeder
	function showfmhsAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$akt = $request['akt'];
		$prd = $request['prd'];
		$opt = $request['opt'];
		// set session
		$param = new Zend_Session_Namespace('param_fmhs');
		$param->akt=$akt;
		$param->prd=$prd;
		$param->opt=$opt;
	}
	
	function insfmhsAction() {
		$user = new Menu();
		$menu = "zzfmhs/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// makes disable layout
			$this->_helper->getHelper('layout')->disableLayout();
			// session id sp
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			$id_sp=$ses_feeder->id_sp;
			$token=$ses_feeder->token;
			$url=$ses_feeder->url;
			// start inserting
			$request = $this->getRequest()->getPost();
			$x = $request['x'];
			$kode_prodi=$request['prd'];
			$nim=array();
			for($i=0;$i<$x;$i++){
				if($request['nim_'.$i]) {
					$nim[]=$request['nim_'.$i];
				}
			}
			// validation
			if(count($nim)==0){
				echo "F|Tidak ada data mahasiswa yang akan ditransfer. Cheklist dahulu mahasiswa yang akan ditransfer datanya|";
			}else{
				if(!$token){
					echo "F|Masa token telah habis, silakan generate ulang token!";
				}else{
					// get data mahasiswa
					$feederMhs = new FeederMhs();
					$getDataMhsFeeder=$feederMhs->getMahasiswaByNim($nim);
					$msg_sukses="";
					$msg_error="";
					$n_sukses=0;
					$n_error=0;
					//-- prodi/sms
					$feederProdi=new FeederProdi();
					$getProdi = $feederProdi->getProdi($token, "kode_program_studi='$kode_prodi'", "", 1, 0, $url);
					$getProdi = json_decode($getProdi,true);
					if(!$getProdi['data']){
						$n_error++;
						$msg_e="Prodi tidak ditemukan!";
						$msg_error=$msg_error."<br>".$msg_e;
					}else{
						foreach ($getProdi['data'] as $dtProdi){
							$id_prodi = $dtProdi['id_prodi'];		
						}
						// loop mahasiswa
						foreach ($getDataMhsFeeder as $dtMhs){
							// collect data
							$data['nama_mahasiswa']=$dtMhs['nm_mhs'];
							$data['tempat_lahir']=$dtMhs['tmp_lahir'];
							$data['tanggal_lahir']=$dtMhs['tgl_lahir'];
							$data['jenis_kelamin']=$dtMhs['jenis_kelamin'];
							$data['id_agama']=$dtMhs['id_agama'];
							$data['nik']=str_replace('.', '', $dtMhs['nik']);
							$data['nisn']=$dtMhs['nisn'];
							if($dtMhs['nisn']==''){
								$data['nisn']="-";
							}
							$data['npwp']=$dtMhs['npwp'];
							$data['jalan']=$dtMhs['alamat'];
							$data['dusun']=$dtMhs['dusun'];
							$data['rt']=$dtMhs['rt'];
							$data['rw']=$dtMhs['rw'];
							$data['kelurahan']=$dtMhs['kelurahan'];
							$data['id_wilayah'] = $dtMhs['id_wil'];
							$data['kode_pos'] = $dtMhs['zip'];
							$data['email'] = $dtMhs['email_lain'];
							$data['handphone'] = $dtMhs['large_kontak'];
							$data['kewarganegaraan']=$dtMhs['id_kwn'];						
							$data['id_kebutuhan_khusus_ayah'] = '0';
							$data['id_kebutuhan_khusus_ibu'] = '0';
							$data['id_kebutuhan_khusus_mahasiswa'] = '0';
							$data['nama_ayah'] = $dtMhs['nm_ayah'];
							$data['nama_ibu_kandung'] = $dtMhs['nm_ibu'];
							$data['id_pekerjaan_ayah'] = $dtMhs['job_ayah'];
							$data['id_pekerjaan_ibu'] = $dtMhs['job_ibu'];
							if($dtMhs['kps']=='t'){
								$kps=1;
							}else{
								$kps=0;
							}
							$data['penerima_kps'] = $kps;
							if($dtMhs['id_alat_transport']!=''){
								$data['id_alat_transportasi'] = $dtMhs['id_alat_transport'];
							}
							if($dtMhs['id_jenis_tinggal']!=''){
								$data['id_jenis_tinggal'] = $dtMhs['id_jenis_tinggal'];
							}
							// sms
							$data2['id_prodi'] = $id_prodi;
							$data2['id_perguruan_tinggi'] = $id_sp;
							$data2['id_jenis_daftar'] = $dtMhs['jns_masuk'];
							$data2['id_pembiayaan']='1';
							$data2['id_jalur_daftar'] = '12';
							$data2['nim'] = $dtMhs['nim'];
							$nim = $dtMhs['nim'];
							if($dtMhs['tgl_masuk']==''){
								$data2['tanggal_daftar'] = $dtMhs['id_angkatan'].'-09-01';	
							}else{
								$data2['tanggal_daftar'] = $dtMhs['tgl_masuk'];
							}
							$data2['id_periode_masuk'] = $dtMhs['semester_mulai'];
							if ($dtMhs['jns_masuk']!='1') {
								$data2['sks_diakui'] = intval($dtMhs['sks_diakui']);
							}
							$data2['biaya_masuk']=1;
							// insert data via ws
							$result = $feederMhs->setMhs($url, $token, $data);
							$result = json_decode($result,true);
							if (is_array($result)) {
								if ($result['error_code']==0) {
									$n_sukses++;
									$msg_s="Data biodata mahasiswa ".$data['nama_mahasiswa']." berhasil ditambah";
									$msg_sukses=$msg_sukses."<br>".$msg_s;
									$data2['id_mahasiswa'] = $result['data']['id_mahasiswa'];
									// inserting mhs_pt
									$result2 = $feederMhs->setMhsPT($url, $token, $data2);
									$result2 = json_decode($result2,true);
									if (is_array($result2)) {
										if ($result2['error_code']==0) {
											$msg_s = "Histori pendidikan mahasiswa ".$data['nama_mahasiswa']." NIM: ".$data2['nim']." berhasil ditambahkan";
											$msg_sukses=$msg_sukses."<br>".$msg_s;
										} else {
											$n_error++;
											$msg_e="Error ".$result2['error_desc']." (".$data['nama_mahasiswa'].") Histori pendidikan : ".$result2['error_desc'].'-'.$result2['error_code'];
											$msg_error = $msg_error."<br>".$msg_e;
										}
									}else{
										$n_error++;
										$msg_e="Errors ".$result2['error_code']." (".$data['nama_mahasiswa']." / NIM: ".$data2['nim'].") Biodata: ".$result['error_desc']."<br>Histori pendidikan:</strong> ".$result2['error_desc'];
										$msg_error=$msg_error."<br>".$msg_e;
									}
								}else{
									if (($result['error_code']==200)or($result['error_code']==400)or($result['error_code']==1209)) {
										$nmMhs=str_replace("'","''",$data['nama_mahasiswa']);
										$temp_pd = $feederMhs->getBiodataMahasiswa($token, "upper(nama_mahasiswa) like upper('%".$nmMhs."%') and tanggal_lahir='".date_format(date_create($data['tanggal_lahir']),'d-m-Y')."'", '', 1, 0, $url);
										$temp_pd=json_decode($temp_pd,true);
										$id_mhs="";
										foreach ($temp_pd['data'] as $temp){
											$id_mhs = $temp['id_mahasiswa'];
										}
										$data2['id_mahasiswa']=$id_mhs;
										$result2 = $feederMhs->setMhsPT($url, $token, $data2);
										$result2 = json_decode($result2,true);
										if (is_array($result2)) {
											if ($result2['error_code']==0) {
												$n_sukses++;
												$msg_s="Data profil mahasiswa sudah ada sebelumnya, histori pendidikan mahasiswa ".$data['nama_mahasiswa']." berhasil ditambahkan dengan NIM ".$data2['nim'];
												$msg_sukses=$msg_sukses."<br>".$msg_s;
											} else {
												$n_error++;
												$msg_e="Errors ".$result2['error_code']." (".$data['nama_mahasiswa']." / NIM: ".$data2['nim'].") Biodata: ".$result['error_desc']."<br>Histori pendidikan:</strong> ".$result2['error_desc'];
												$msg_error=$msg_error."<br>".$msg_e;
											}
										}else{
											$n_error++;
											$msg_e="Error ".$result2['error_code']." (".$data['nama_mahasiswa']." / NIM: ".$data2['nim'].") Biodata: ".$result['error_desc']."<br>Histori pendidikan:</strong> ".$result2['error_desc'];
											$msg_error=$msg_error."<br>".$msg_e;
										}
									}else{
										$n_error++;
										$msg_e="Error ".$result['error_code']." (".$data['nama_mahasiswa']." / NIM: ".$data2['nim'].") Biodata: ".$result['error_desc'];
										$msg_error=$msg_error."<br>".$msg_e;
									}
								}
							}else{
								$msg_e= "Error ".$result['error_code'].":".$result['error_desc'];
								$msg_error = $msg_error."<br>".$msg_e;
							}
						}
						echo "T|".$n_sukses." data Sukses : <br>".$msg_sukses."<br>---------<br>".$n_error." Data Error: <br>".$msg_error;
					}
				}
			}
		}
	}
	
	function updfmhsAction() {
		$user = new Menu();
		$menu = "zzfmhs/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// makes disable layout
			$this->_helper->getHelper('layout')->disableLayout();
			// get param
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    		$nim = $param[0];
	    		$kd_prodi = $param[1];
			// session id sp
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			$id_sp=$ses_feeder->id_sp;
			$token=$ses_feeder->token;
			$url=$ses_feeder->url;
			// collect data from sia
			$feederMhs = new FeederMhs();
			$getDataMhsFeeder=$feederMhs->getMahasiswaByNim($nim);
			$msg_sukses="";
			$msg_error="";
			$n_sukses=0;
			$n_error=0;
			foreach ($getDataMhsFeeder as $dtMhs){
				// collect data
				$data['jenis_kelamin']=$dtMhs['jenis_kelamin'];
				$data['id_agama']=$dtMhs['id_agama'];
				$data['nik']=str_replace('.', '', $dtMhs['nik']);
				$data['nisn']=$dtMhs['nisn'];
				if($dtMhs['nisn']==''){
					$data['nisn']="-";
				}
				$data['npwp']=$dtMhs['npwp'];
				$data['jalan']=$dtMhs['alamat'];
				$data['dusun']=$dtMhs['dusun'];
				$data['rt']=$dtMhs['rt'];
				$data['rw']=$dtMhs['rw'];
				$data['kelurahan']=$dtMhs['kelurahan'];
				$data['id_wilayah'] = $dtMhs['id_wil'];
				$data['kode_pos'] = $dtMhs['zip'];
				$data['email'] = $dtMhs['email_lain'];
				$data['handphone'] = $dtMhs['large_kontak'];
				$data['kewarganegaraan']=$dtMhs['id_kwn'];
				$data['id_kebutuhan_khusus_ayah'] = '0';
				$data['id_kebutuhan_khusus_ibu'] = '0';
				$data['id_kebutuhan_khusus_mahasiswa'] = '0';
				$data['nama_ayah'] = $dtMhs['nm_ayah'];
				$data['id_pekerjaan_ayah'] = $dtMhs['job_ayah'];
				$data['id_pekerjaan_ibu'] = $dtMhs['job_ibu'];
				if($dtMhs['kps']=='t'){
					$kps=1;
				}else{
					$kps=0;
				}
				$data['penerima_kps'] = $kps;
				if($dtMhs['id_alat_transport']!=''){
					$data['id_alat_transportasi'] = $dtMhs['id_alat_transport'];
				}
				if($dtMhs['id_jenis_tinggal']!=''){
					$data['id_jenis_tinggal'] = $dtMhs['id_jenis_tinggal'];
				}
				//--
				$data2['id_jenis_daftar'] = $dtMhs['jns_masuk'];
				$data2['id_pembiayaan']='1';
				$data2['id_jalur_daftar'] = '12';
				$nim = $dtMhs['nim'];
				if($dtMhs['tgl_masuk']==''){
					$data2['tanggal_daftar'] = $dtMhs['id_angkatan'].'-09-01';	
				}else{
					$data2['tanggal_daftar'] = $dtMhs['tgl_masuk'];
				}
				$data2['id_periode_masuk'] = $dtMhs['semester_mulai'];
				if ($dtMhs['jns_masuk']=='2') {
					$data2['sks_diakui'] = $dtMhs['sks_diakui'];
				}
				// get id pd
				$getDataMhsPT = $feederMhs->getListMahasiswa($token, "nim='$nim'", "", 1, 0, $url);
				$getDataMhsPT = json_decode($getDataMhsPT,true);
				$id_mahasiswa="";
				$id_registrasi_mahasiswa="";
				$id_prodi="";
				$feederProdi=new FeederProdi();
				$getProdi = $feederProdi->getProdi($token, "kode_program_studi='$kd_prodi'", "", 1, 0, $url);
				$getProdi = json_decode($getProdi,true);
				foreach ($getProdi['data'] as $dtProdi){
					$id_prodi = $dtProdi['id_prodi'];		
				}
				foreach ($getDataMhsPT['data'] as $dtMhsPT){
					$id_mahasiswa=$dtMhsPT['id_mahasiswa'];
					$id_registrasi_mahasiswa=$dtMhsPT['id_registrasi_mahasiswa'];
				}
				$key['id_mahasiswa']=$id_mahasiswa;
				$key2['id_registrasi_mahasiswa']=$id_registrasi_mahasiswa;
				$data2['id_mahasiswa']=$id_mahasiswa;
				$data2['id_perguruan_tinggi'] = $id_sp;
				$data2['id_prodi'] = $id_prodi;
				// update data via ws
				$result = $feederMhs->updMhs($url, $token, $key,$data);
				$result = json_decode($result,true);
				if (is_array($result)) {
					if ($result['error_code']==0) {
						$n_sukses++;
						$msg_s = "Sukses update data profil mahasiswa : ".$dtMhs['nm_mhs'];
						$msg_sukses=$msg_sukses."<br>".$msg_s;
						// update mahasiswa pt
						$result2 = $feederMhs->updMhsPT($url, $token, $key2, $data2);
						$result2 = json_decode($result2,true);
						if ($result2['error_code']==0) {
							$msg_s = "Histori pendidikan mahasiswa ".$dtMhs['nm_mhs']." NIM: ".$nim." berhasil diubah";
							$msg_sukses=$msg_sukses."<br>".$msg_s;
						} else {
							$n_error++;
							$msg_e="Error ".$result2['error_code'].": Histori pendidikan ".$dtMhs['nm_mhs']." : ".$result2['error_desc'];
							$msg_error = $msg_error."<br>".$msg_e;
						}
					} else {
						$n_error++;
						$msg_e = "Error data mahasiswa : ".$dtMhs['nm_mhs']." : ".$result['error_desc'];
						$msg_error=$msg_error."<br>".$msg_e;
					}
				} else {
					$msg_e= $result['error_code'].":".$dtResult['error_desc'];
					$msg_error=$msg_error."<br>".$msg_e;
				}
			}
			echo "T|".$n_sukses." data Sukses : <br>".$msg_sukses."<br>---------<br>".$n_error." Data Error: <br>".$msg_error;
		}
	}
	
	// kelas-feeder
	function showfklsAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$per = $request['per'];
		$prd = $request['prd'];
		$opt = $request['opt'];
		// set session
		$param = new Zend_Session_Namespace('param_fkls');
		$param->per=$per;
		$param->prd=$prd;
		$param->opt=$opt;
	}
	
	function insfklsAction() {
		$user = new Menu();
		$menu = "zzfkls/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// makes disable layout
			$this->_helper->getHelper('layout')->disableLayout();
			// session id sp
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			$id_sp=$ses_feeder->id_sp;
			$token=$ses_feeder->token;
			$url=$ses_feeder->url;
			// start inserting
			$request = $this->getRequest()->getPost();
			$x = $request['x'];
			$id_kur=$request['id_kur'];
			$kode_prodi=$request['prd'];
			$kd_paket=array();
			for($i=0;$i<$x;$i++){
				if($request['pkls_'.$i]) {
					$kd_paket[]=$request['pkls_'.$i];
				}
			}
			// validation
			if(count($kd_paket)==0){
				echo "F|Tidak ada data kelas yang akan ditransfer. Cheklist dahulu kelas yang akan ditransfer datanya|";
			}else{
				if(!$token){
					echo "F|Masa token telah habis, silakan generate ulang token!";
				}else{
					// get data paket kelas
					$feederKls = new FeederKls();
					$getDataKelas=$kd_paket;
					$msg_sukses="";
					$msg_error="";
					$n_sukses=0;
					$n_error=0;
					//-- prodi/sms
					$feederProdi=new FeederProdi();
					$getProdi = $feederProdi->getProdi($token, "kode_program_studi='$kode_prodi'", "", 1, 0, $url);
					$getProdi = json_decode($getProdi,true);
					if(!$getProdi['data']){
						$n_error++;
						$msg_e="Prodi tidak ditemukan!";
						$msg_error=$msg_error."<br>".$msg_e;
					}else{
						foreach ($getProdi['data'] as $dtProdi){
							$id_prodi = $dtProdi['id_prodi'];
						}
						// loop paket kelas
						foreach ($getDataKelas as $dtKls){
							$arrDtKls = explode("###", $dtKls);
							$feederMk=new FeederMk();
							$kd_mk=str_replace(" ", "", preg_replace("/[^a-zA-Z 0-9]+/", "",$arrDtKls[1]));
							$dtMkFeeder =$feederMk->getMatkul($token, "kode_mata_kuliah='$kd_mk'", "", 100, 0, $url);
							$dtMkFeeder = json_decode($dtMkFeeder,true);
							if(!$dtMkFeeder['data']){
								$n_error++;
								$msg_e="Mata kuliah dengan kode ".$kd_mk." tidak ditemukan";
								$msg_error=$msg_error."<br>".$msg_e;
							}else{
								$id_mk="";
								$nm_mk="";
								foreach ($dtMkFeeder['data'] as $dtMk){
									$id_mk_master=$dtMk['id_matkul'];
									$getMkKur=$feederMk->getMatkulKurikulum($token, "id_matkul='$id_mk_master' and id_kurikulum='$id_kur'", "", 1, 0, $url);
									$getMkKur=json_decode($getMkKur,true);
									if($getMkKur['data']){
										$id_mk=$dtMk['id_matkul'];
										$nm_mk=$dtMk['nama_mata_kuliah'];
									}
								}
								if($id_mk==""){
									$n_error++;
									$msg_e="Mata kuliah dengan kode ".$kd_mk." tidak ditemukan di kurikulum".$id_kur;
									$msg_error=$msg_error."<br>".$msg_e;
								}else{
									// collect data
									$data['id_prodi']=$id_prodi;
									$data['id_semester']=$arrDtKls[2];
									$data['id_matkul']=$id_mk;
									$data['nama_kelas_kuliah']=$arrDtKls[0];
									// insert data via ws
									$result = $feederKls->setKelasKuliah($url, $token, $data);
									$result = json_decode($result,true);
									if (is_array($result)) {
										if ($result['error_code']==0) {
											$n_sukses++;
											$msg_s="Data kelas kuliah ".$arrDtKls[0]." - ".$kd_mk."(".$nm_mk.") - ".$arrDtKls[2]." berhasil ditambah";
											$msg_sukses=$msg_sukses."<br>".$msg_s;
										}else{
											$n_error++;
											$msg_e="Error ".$result['error_code']." (".$arrDtKls[0]." / MK: ".$kd_mk."-".$nm_mk."): ".$result['error_desc'];
											$msg_error=$msg_error."<br>".$msg_e;
										}
									}else{
										$msg_e= "Error ".$result['error_code'].":".$result['error_desc'];
										$msg_error = $msg_error."<br>".$msg_e;
									}	
								}
							}
						}
						echo "T|".$n_sukses." data Sukses : <br>".$msg_sukses."<br>---------<br>".$n_error." Data Error: <br>".$msg_error;
					}
				}
			}
		}
	}
	
	function delmasfklsAction() {
		$user = new Menu();
		$menu = "zzfkls/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// makes disable layout
			$this->_helper->getHelper('layout')->disableLayout();
			// session id sp
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			$id_sp=$ses_feeder->id_sp;
			$token=$ses_feeder->token;
			$url=$ses_feeder->url;
			// start deleting
			$request = $this->getRequest()->getPost();
			$param = $request['param'];
	    		$id_smt = $param[0];
			$id_sms=$param[1];
			// validation
			if(!$token){
				echo "F|Masa token telah habis, silakan generate ulang token!";
			}else{
				// delete
				$msg_sukses="";
				$msg_error="";
				$n_error=0;
				$n_sukses=0;
				$feederKls=new FeederKls();
				$feederKrs=new FeederKrs();
				$feederDsn=new FeederDsn();
				$getKls=$feederKls->getKelasKuliahBySmtSms($url, $token, $id_smt, $id_sms);
				$records=array();
				$records2=array();
				$records3=array();
				$arrKelas=array();
				$i=1;
				foreach ($getKls as $dtKls){
					//if($i<=20){
						$arrKelas[] = array('id_kls'=>$dtKls['id_kls']);
						$records3[] = array('id_kls'=>$dtKls['id_kls']);	
					//}
					$i++;
				}
				$feederKrs=new FeederKrs();
				foreach ($arrKelas as $dtArrKls){
					$getKrs=$feederKrs->getKrsByIdKls($url, $token, $dtArrKls['id_kls']);
					$i=1;
					foreach ($getKrs as $dtKrs) {
						if($i<=1){
							$records[]=array('id_kls'=>$dtArrKls['id_kls'],'id_reg_pd'=>$dtKrs['id_reg_pd']);
						}
					}
					$getAjar=$feederDsn->getKlsDsnByIdKls($url, $token, $dtArrKls['id_kls']);
					foreach ($getAjar as $dtAjar) {
						$records2[]=array('id_ajar'=>$dtAjar['id_ajar']);;
					}
				}
				// delete data via ws
				$result = $feederKrs->delKrs($url, $token, $records);
				if ($result['result']) {
					if ($result['result']['error_desc']==NULL) {
						$n_sukses++;
						$msg_s="Data KRS dan nilai kelas berhasil dihapus massal";
						$msg_sukses=$msg_sukses."<br>".$msg_s;
					}else{
						$n_error++;
						$msg_e="Error hapus massal: ".$result['result']['error_code']." : ".$result['result']['error_desc'];
						$msg_error=$msg_error."<br>".$msg_e;
					}
				}else{
					$msg_e= "Tidak ada data KRS dan Nilai yang akan dihapus";
					$msg_error = $msg_error."<br>".$msg_e;
				}
				$result2= $feederDsn->delAjarDsn($url, $token, $records2);
				if ($result2['result']) {
					if ($result2['result']['error_desc']==NULL) {
						$n_sukses++;
						$msg_s="Data Ajar Dosen berhasil dihapus massal";
						$msg_sukses=$msg_sukses."<br>".$msg_s;
					}else{
						$n_error++;
						$msg_e="Error hapus massal ajar dosen : ".$result['result']['error_code']." : ".$result['result']['error_desc'];
						$msg_error=$msg_error."<br>".$msg_e;
					}
				}else{
					$msg_e= "Tidak ada data ajar dosen yang akan dihapus";
					$msg_error = $msg_error."<br>".$msg_e;
				}
				$result3 = $feederKls->delKlsKuliah($url, $token, $records3);
				if ($result3['result']) {
					if ($result3['result']['error_desc']==NULL) {
						$n_sukses++;
						$msg_s="Data kelas berhasil dihapus massal";
						$msg_sukses=$msg_sukses."<br>".$msg_s;
					}else{
						$n_error++;
						$msg_e="Error hapus massal kelas: ".$result['result']['error_code']." : ".$result['result']['error_desc'];
						$msg_error=$msg_error."<br>".$msg_e;
					}
				}else{
					$msg_e= "Tidak ada data kelas yang akan dihapus";
					$msg_error = $msg_error."<br>".$msg_e;
				}
				echo "T| data Sukses dihapus : <br>".$msg_sukses."<br>---------<br> Data Error: <br>".$msg_error;
			}
		}
	}
	
	// 	KRS-feeder
	function insfkrsAction() {
		$user = new Menu();
		$menu = "zzfkrs/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// makes disable layout
			$this->_helper->getHelper('layout')->disableLayout();
			// session id sp
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			$id_sp=$ses_feeder->id_sp;
			$token=$ses_feeder->token;
			$url=$ses_feeder->url;
			// start inserting
			$request = $this->getRequest()->getPost();
			$x = $request['x'];
			$kode_prodi=$request['prd'];
			$id_kls = $request['id_kls'];
			$nim_krs=array();
			$j=0;
			for($i=0;$i<$x;$i++){
				if($request['kul_'.$i]) {
					$arrKul = explode("###", $request['kul_'.$i]);
					$nim_krs[$j]=$arrKul[0];
					$n_angka[$j]=$arrKul[1];
					$n_huruf[$j]=$arrKul[2];
					$n_index[$j]=$arrKul[3];
					$j++;
				}
			}
			// validation
			if(count($nim_krs)==0){
				echo "F|Tidak ada data KRS yang akan ditransfer. Cheklist dahulu KRS mahasiswa yang akan ditransfer datanya|";
			}else{
				if(!$token){
					echo "F|Masa token telah habis, silakan generate ulang token!";
				}else{
					// get data krs
					$getDataNim=$nim_krs;
					$msg_sukses="";
					$msg_error="";
					$n_sukses=0;
					$n_error=0;
					//-- prodi/sms
					$feederProdi=new FeederProdi();
					$getProdi = $feederProdi->getProdi($token, "kode_program_studi='$kode_prodi'", "", 1, 0, $url);
					$getProdi = json_decode($getProdi,true);
					if(!$getProdi['data']){
						$n_error++;
						$msg_e="Prodi tidak ditemukan!";
						$msg_error=$msg_error."<br>".$msg_e;
					}else{
						foreach ($getProdi['data'] as $dtProdi){
							$id_prodi = $dtProdi['id_prodi'];
						}
						// loop nim krs
						$feederMhs = new FeederMhs();
						$feederKrs = new FeederKrs();
						$j=0;
						foreach ($getDataNim as $dtNim){
							$getRegPd = $feederMhs->getListRiwayatPendidikanMahasiswa($token, "id_prodi='$id_prodi' and trim(nim)=trim('$dtNim')", "", 1, 0, $url);
							$getRegPd = json_decode($getRegPd,true);
							if(!$getRegPd['data']){
								$n_error++;
								$msg_e="Mahasiswa dengan NIM ".$dtNim." tidak ditemukan";
								$msg_error=$msg_error."<br>".$msg_e;
							}else{
								foreach ($getRegPd['data'] as $dtRegPd){
									$id_reg_pd=$dtRegPd['id_registrasi_mahasiswa'];
								}
								// collect data
								$data['id_kelas_kuliah']=$id_kls;
								$data['id_registrasi_mahasiswa']=$id_reg_pd;
								$data['nilai_angka']=$n_angka[$j];
								$data['nilai_huruf']=$n_huruf[$j];
								$data['nilai_indeks']=$n_index[$j];
								// insert data via ws
								$result = $feederKrs->setKrs($url, $token, $data);
								$result = json_decode($result,true);
								if (is_array($result)) {
									if ($result['error_code']==0) {
										$n_sukses++;
										$msg_s="Data KRS untuk mahasiswa NIM ".$dtNim." berhasil ditambah";
										$msg_sukses=$msg_sukses."<br>".$msg_s;
									}else{
										$n_error++;
										$msg_e="Error ".$result['error_code']." (".$dtNim."): ".$result['error_desc'];
										$msg_error=$msg_error."<br>".$msg_e;
									}
								}else{
									$msg_e= "Error ".$result['error_code'].":".$result['error_desc'];
									$msg_error = $msg_error."<br>".$msg_e;
								}	
							}
							$j++;
						}
						echo "F|".$n_sukses." data Sukses : <br>".$msg_sukses."<br>---------<br>".$n_error." Data Error: <br>".$msg_error;
					}
				}
			}
		}
	}
	
	function delmasfkrsAction() {
		$user = new Menu();
		$menu = "zzfkrs/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// makes disable layout
			$this->_helper->getHelper('layout')->disableLayout();
			// session id sp
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			$id_sp=$ses_feeder->id_sp;
			$token=$ses_feeder->token;
			$url=$ses_feeder->url;
			// start deleting
			$request = $this->getRequest()->getPost();
			$param = $request['param'];
	    		$id_smt = $param[0];
			$id_sms=$param[1];
			// validation
			if(!$token){
				echo "F|Masa token telah habis, silakan generate ulang token!";
			}else{
				// delete
				$msg_sukses="";
				$msg_error="";
				$n_error=0;
				$n_sukses=0;
				$feederKls=new FeederKls();
				$getKls=$feederKls->getKelasKuliahBySmtSms($url, $token, $id_smt, $id_sms);
				$arrKelas=array();
				foreach ($getKls as $dtKls){
					$arrKelas[] = array('id_kls'=>$dtKls['id_kls']);
				}
				$feederKrs=new FeederKrs();
				$records=array();
				foreach ($arrKelas as $dtArrKls){
					$getKrs=$feederKrs->getKrsByIdKls($url, $token, $dtArrKls['id_kls']);
					foreach ($getKrs as $dtKrs) {
						$records[]=array('id_kls'=>$dtArrKls['id_kls'],'id_reg_pd'=>$dtKrs['id_reg_pd']);
					}
				}
				// delete data via ws
				$result = $feederKrs->delKrs($url, $token, $records);
				if ($result['result']) {
					if ($result['result']['error_desc']==NULL) {
						$n_sukses++;
						$msg_s="Data KRS dan nilai kelas berhasil dihapus massal";
						$msg_sukses=$msg_sukses."<br>".$msg_s;
					}else{
						$n_error++;
						$msg_e="Error hapus massal: ".$result['result']['error_code']." : ".$result['result']['error_desc'];
						$msg_error=$msg_error."<br>".$msg_e;
					}
				}else{
					$msg_e= "Tidak ada data KRS dan Nilai yang akan dihapus";
					$msg_error = $msg_error."<br>".$msg_e;
				}
				echo "T| data Sukses dihapus : <br>".$msg_sukses."<br>---------<br> Data Error: <br>".$msg_error;
			}
		}
	}
	
	// 	Nilai-feeder
	function insfnlmAction() {
		$user = new Menu();
		$menu = "zzfnlm/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// makes disable layout
			$this->_helper->getHelper('layout')->disableLayout();
			// session id sp
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			$id_sp=$ses_feeder->id_sp;
			$token=$ses_feeder->token;
			$url=$ses_feeder->url;
			// start inserting
			$request = $this->getRequest()->getPost();
			$y = $request['y'];
			$kode_prodi=$request['prd'];
			$id_kls = $request['id_kls'];
			$nim_nlm=array();
			for($i=0;$i<$y;$i++){
				if($request['nlm_'.$i]) {
					$nim_nlm[]=$request['nlm_'.$i];
				}
			}
			// validation
			if(count($nim_nlm)==0){
				echo "F|Tidak ada data nilai yang akan diupdate. Cheklist dahulu nilai mahasiswa yang akan diupdate datanya|";
			}else{
				if(!$token){
					echo "F|Masa token telah habis, silakan generate ulang token!";
				}else{
					// get data krs
					$getDataNim=$nim_nlm;
					$msg_sukses="";
					$msg_error="";
					$n_sukses=0;
					$n_error=0;
					$n=1;
					//-- prodi/sms
					$feederProdi=new FeederProdi();
					$getProdi = $feederProdi->getProdi($token, "kode_program_studi='$kode_prodi'", "", 1, 0, $url);
					$getProdi = json_decode($getProdi,true);
					if(!$getProdi['data']){
						$n_error++;
						$msg_e="Prodi tidak ditemukan!";
						$msg_error=$msg_error."<br>".$msg_e;
					}else{
						foreach ($getProdi['data'] as $dtProdi){
							$id_prodi = $dtProdi['id_prodi'];
						}
						// loop nim nilai
						$feederMhs = new FeederMhs();
						$feederKrs = new FeederKrs();
						foreach ($getDataNim as $dtNim){
							$arrNlm=explode("###", $dtNim);
							$id_reg_mhs=$arrNlm[0];
							$n_angka=floatval($arrNlm[1]);
							$n_huruf=$arrNlm[2];
							$bobot=$arrNlm[3];
							// collect data
							$key['id_kelas_kuliah']=$id_kls;
							$key['id_registrasi_mahasiswa']=$id_reg_mhs;
							$data['nilai_angka']=$n_angka;
							$data['nilai_huruf']=$n_huruf;
							$data['nilai_indeks']=$bobot;
							// insert data via ws
							$result = $feederKrs->setNlm($url, $token, $key, $data);
							$result = json_decode($result,true);
							if (is_array($result)) {
								if ($result['error_code']==0) {
									$n_sukses++;
									$msg_s="Data nilai untuk mahasiswa baris ke-".$n." berhasil diupdate";
									$msg_sukses=$msg_sukses."<br>".$msg_s;
								}else{
									$n_error++;
									$msg_e="Error baris ke-".$n.":".$result['error_code'].":".$result['error_desc'];
									$msg_error=$msg_error."<br>".$msg_e;
								}
							}else{
								$msg_e= "Error ".$result['error_code'].":".$result['error_desc'];
								$msg_error = $msg_error."<br>".$msg_e;
							}
							$n++;
						}
						echo "T|".$n_sukses." data Sukses : <br>".$msg_sukses."<br>---------<br>".$n_error." Data Error: <br>".$msg_error;
					}
				}
			}
		}
	}
	
	// dosen-feeder
	function showfdsnAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$per = $request['per'];
		$prd = $request['prd'];
		$opt = $request['opt'];
		// set session
		$param = new Zend_Session_Namespace('param_fdsn');
		$param->per=$per;
		$param->prd=$prd;
		$param->opt=$opt;
	}

	function showfdsn2Action(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$per = $request['per'];
		$prd = $request['prd'];
		$opt = $request['opt'];
		// set session
		$param = new Zend_Session_Namespace('param_fdsn2');
		$param->per=$per;
		$param->prd=$prd;
		$param->opt=$opt;
	}

	
	function insfdsnAction() {
		$user = new Menu();
		$menu = "zzfdsn/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// makes disable layout
			$this->_helper->getHelper('layout')->disableLayout();
			// session id sp
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			$id_sp=$ses_feeder->id_sp;
			$token=$ses_feeder->token;
			$url=$ses_feeder->url;
			// start inserting
			$request = $this->getRequest()->getPost();
			$x = $request['x'];
			$kode_prodi=$request['prd'];
			$id_kur=$request['id_kur'];
			$ajar_dsn=array();
			$nidn_dsn = array();
			$oldnidn_dsn = array();
			$pkls = array();
			for($i=0;$i<$x;$i++){
				if($request['ajar_'.$i]) {
					$ajar_dsn[]=$request['ajar_'.$i];
					$nidn_dsn[]=$request['nidn_'.$i];
					$pkls[]=$request['pkls_'.$i];
					$oldnidn_dsn[]=$request['oldnidn_'.$i];
				}
			}
			// validation
			if(count($ajar_dsn)==0){
				echo "F|Tidak ada data dosen mengajar yang akan ditransfer. Cheklist dahulu dosen mengajar yang akan ditransfer datanya|";
			}else{
				if(!$token){
					echo "F|Masa token telah habis, silakan generate ulang token!";
				}else{
					// get data krs
					$getDataAjar=$ajar_dsn;
					$msg_sukses="";
					$msg_error="";
					$n_sukses=0;
					$n_error=0;
					//-- prodi/sms
					$feederProdi=new FeederProdi();
					$getProdi = $feederProdi->getProdi($token, "kode_program_studi='$kode_prodi'", "", 1, 0, $url);
					$getProdi = json_decode($getProdi,true);
					if(!$getProdi['data']){
						$n_error++;
						$msg_e="Prodi tidak ditemukan!";
						$msg_error=$msg_error."<br>".$msg_e;
					}else{
						foreach ($getProdi['data'] as $dtProdi){
							$id_prodi = $dtProdi['id_prodi'];
						}
						// loop nidn
						$feederDsn = new FeederDsn();
						$feederKls = new FeederKls();
						$feederMk = new FeederMk();
						$feederPkls = new FeederPaketkelas();
						$i=0;
						foreach ($getDataAjar as $dtAjar){
							$arrAjar=explode("###", $dtAjar);
							$id_nm_kls = $arrAjar[0];
							$kd_mk = $arrAjar[1];
							$id_smt=$arrAjar[2];
							$ttp_muka=$arrAjar[3];
							if($ttp_muka==0){
								$ttp_muka=14;
							}
							$nidn = $nidn_dsn[$i];
							$paket = $pkls[$i];
							$oldnidn = $oldnidn_dsn[$i];
							$thn_ajaran = substr($id_smt, 0,4);
							// get reg
							$getPenugasan = $feederDsn->GetDetilPenugasanDosen($token, "trim(nidn)=trim('$nidn') and id_perguruan_tinggi='$id_sp' and id_tahun_ajaran='$thn_ajaran'", "", 1, 0, $url);
							$getPenugasan = json_decode($getPenugasan,true);
							if(!$getPenugasan['data']){
								$n_error++;
								$msg_e="Dosen dengan NIDN ".$nidn." tidak diberi penugasan pada tahun ajaran ".$thn_ajaran.". Silakan ke halaman forlap!";
								$msg_error=$msg_error."<br>".$msg_e;
							}else{
								foreach ($getPenugasan['data'] as $dtPenugasan){
									$id_reg_ptk =$dtPenugasan['id_registrasi_dosen'];
								}
								// get data MK
								$getMk = $feederMk->getMatkul($token, "trim(kode_mata_kuliah)=trim('$kd_mk')", "", 100, 0, $url);
								$getMk = json_decode($getMk,true);
								if(!$getMk['data']){
									$n_error++;
									$msg_e="Mata kuliah dengan kode ".$kd_mk." tidak ditemukan";
									$msg_error=$msg_error."<br>".$msg_e;
								}else{
									$id_mk="";
									$nm_mk="";
									$sks_mk=0;
									foreach ($getMk['data'] as $dtMk){
										$id_mk_found=$dtMk['id_matkul'];
										$getMkKur=$feederMk->getMatkulKurikulum($token, "id_matkul='$id_mk_found' and id_kurikulum='$id_kur'", "", 1, 0, $url);
										$getMkKur=json_decode($getMkKur,true);
										if($getMkKur['data']){
											$id_mk=$dtMk['id_matkul'];
											$nm_mk=$dtMk['nama_mata_kuliah'];
											$sks_mk=$dtMk['sks_mata_kuliah'];
										}
									}
									if($id_mk==""){
										$n_error++;
										$msg_e="Mata kuliah dengan kode ".$kd_mk." tidak ditemukan di kurikulum yang sudah diset";
										$msg_error=$msg_error."<br>".$msg_e;
									}else{
										// collect data kelas
										$getKls = $feederKls->getDetailKelasKuliah($token, "id_prodi='$id_prodi' and id_semester='$id_smt' and trim(nama_kelas_kuliah)=trim('$id_nm_kls') and id_matkul='$id_mk'", "", 1000, 0, $url);
										$getKls = json_decode($getKls,true);
										$id_kls="";
										foreach ($getKls['data'] as $dtGetKls){
											$id_kls=$dtGetKls['id_kelas_kuliah'];
										}
										if($id_kls==""){
											$n_error++;
											$msg_e="Kelas dengan nama ".$id_nm_kls.", kode mata kuliah ".$kd_mk.", semester ".$id_smt." tidak ditemukan".$id_kur;
											$msg_error=$msg_error."<br>".$msg_e;
										}else{
											// update nind lap di paket kelas
											$updFPkls = $feederPkls->updNidnLapPaketKelas($nidn, $paket);
											if($updFPkls=='F'){
												$n_error++;
												$msg_e="Gagal update NIDN di SIA".$nidn." untuk kelas ".$id_nm_kls." kode mata kuliah ".$kd_mk." semester ".$id_smt;
												$msg_error=$msg_error."<br>".$msg_e;
											}else{
												// insert data via ws
												$temp_data['id_registrasi_dosen'] = $id_reg_ptk;
												$temp_data['id_kelas_kuliah'] = $id_kls;
												$temp_data['sks_substansi_total'] = $sks_mk;
												$temp_data['rencana_minggu_pertemuan'] = $ttp_muka;
												$temp_data['realisasi_minggu_pertemuan'] = $ttp_muka;
												$temp_data['id_jenis_evaluasi'] = 1;
												$result = $feederDsn->setDosenKelas($url, $token, $temp_data);
												$result = json_decode($result,true);
												if (is_array($result)) {
													if ($result['error_code']==0) {
														$n_sukses++;
														$msg_s="Data Mengajar dosen untuk dosen NIDN ".$nidn." kode mata kuliah ".$kd_mk." kelas ".$id_nm_kls." berhasil ditambah";
														$msg_sukses=$msg_sukses."<br>".$msg_s;
													}else{
														// rollback update paket kelas
														$updFPkls = $feederPkls->updNidnLapPaketKelas($oldnidn, $paket);
														$n_error++;
														$msg_e="Error ".$result['error_code']." (".$nidn."): ".$result['error_desc'];
														$msg_error=$msg_error."<br>".$msg_e;
													}
												}else{
													$msg_e= "Error ".$result['error_code'].":".$result['error_desc'];
													$msg_error = $msg_error."<br>".$msg_e;
												}
											}
										}
									}
								}
							}
							//}
							$i++;
						}
						echo "T|".$n_sukses." data Sukses : <br>".$msg_sukses."<br>---------<br>".$n_error." Data Error: <br>".$msg_error;
					}
				}
			}
		}
	}

	function insfdsn2Action() {
		$user = new Menu();
		$menu = "zzfdsn2/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// makes disable layout
			$this->_helper->getHelper('layout')->disableLayout();
			// session id sp
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			$id_sp=$ses_feeder->id_sp;
			$token=$ses_feeder->token;
			$url=$ses_feeder->url;
			// start inserting
			$request = $this->getRequest()->getPost();
			$x = $request['x'];
			$kode_prodi=$request['prd'];
			$id_kur=$request['id_kur'];
			$ajar_dsn=array();
			$nidn_dsn = array();
			$oldnidn_dsn = array();
			$pkls = array();
			for($i=0;$i<$x;$i++){
				if($request['ajar_'.$i]) {
					$ajar_dsn[]=$request['ajar_'.$i];
					$nidn_dsn[]=$request['nidn_'.$i];
					$pkls[]=$request['pkls_'.$i];
					$oldnidn_dsn[]=$request['oldnidn_'.$i];
				}
			}
			// validation
			if(count($ajar_dsn)==0){
				echo "F|Tidak ada data dosen mengajar yang akan ditransfer. Cheklist dahulu dosen mengajar yang akan ditransfer datanya|";
			}else{
				if(!$token){
					echo "F|Masa token telah habis, silakan generate ulang token!";
				}else{
					// get data krs
					$getDataAjar=$ajar_dsn;
					$msg_sukses="";
					$msg_error="";
					$n_sukses=0;
					$n_error=0;
					//-- prodi/sms
					$feederProdi=new FeederProdi();
					$getProdi = $feederProdi->getProdi($token, "kode_program_studi='$kode_prodi'", "", 1, 0, $url);
					$getProdi = json_decode($getProdi,true);
					if(!$getProdi['data']){
						$n_error++;
						$msg_e="Prodi tidak ditemukan!";
						$msg_error=$msg_error."<br>".$msg_e;
					}else{
						foreach ($getProdi['data'] as $dtProdi){
							$id_prodi = $dtProdi['id_prodi'];
						}
						// loop nidn
						$feederDsn = new FeederDsn();
						$feederKls = new FeederKls();
						$feederMk = new FeederMk();
						$feederPkls = new FeederPaketkelas();
						$i=0;
						foreach ($getDataAjar as $dtAjar){
							$arrAjar=explode("###", $dtAjar);
							$id_nm_kls = $arrAjar[0];
							$kd_mk = $arrAjar[1];
							$id_smt=$arrAjar[2];
							$ttp_muka=$arrAjar[3];
							if($ttp_muka==0){
								$ttp_muka=14;
							}
							$nidn = $nidn_dsn[$i];
							$paket = $pkls[$i];
							$oldnidn = $oldnidn_dsn[$i];
							$thn_ajaran = substr($id_smt, 0,4);
							// get reg
							$getPenugasan = $feederDsn->GetDetilPenugasanDosen($token, "trim(nidn)=trim('$nidn') and id_perguruan_tinggi='$id_sp' and id_tahun_ajaran='$thn_ajaran'", "", 1, 0, $url);
							$getPenugasan = json_decode($getPenugasan,true);
							if(!$getPenugasan['data']){
								$n_error++;
								$msg_e="Dosen dengan NIDN ".$nidn." tidak diberi penugasan pada tahun ajaran ".$thn_ajaran.". Silakan ke halaman forlap!";
								$msg_error=$msg_error."<br>".$msg_e;
							}else{
								foreach ($getPenugasan['data'] as $dtPenugasan){
									$id_reg_ptk =$dtPenugasan['id_registrasi_dosen'];
								}
								// get data MK
								$getMk = $feederMk->getMatkul($token, "trim(kode_mata_kuliah)=trim('$kd_mk')", "", 100, 0, $url);
								$getMk = json_decode($getMk,true);
								if(!$getMk['data']){
									$n_error++;
									$msg_e="Mata kuliah dengan kode ".$kd_mk." tidak ditemukan";
									$msg_error=$msg_error."<br>".$msg_e;
								}else{
									$id_mk="";
									$nm_mk="";
									$sks_mk=0;
									foreach ($getMk['data'] as $dtMk){
										$id_mk_found=$dtMk['id_matkul'];
										$getMkKur=$feederMk->getMatkulKurikulum($token, "id_matkul='$id_mk_found' and id_kurikulum='$id_kur'", "", 1, 0, $url);
										$getMkKur=json_decode($getMkKur,true);
										if($getMkKur['data']){
											$id_mk=$dtMk['id_matkul'];
											$nm_mk=$dtMk['nama_mata_kuliah'];
											$sks_mk=$dtMk['sks_mata_kuliah'];
										}
									}
									if($id_mk==""){
										$n_error++;
										$msg_e="Mata kuliah dengan kode ".$kd_mk." tidak ditemukan di kurikulum yang sudah diset";
										$msg_error=$msg_error."<br>".$msg_e;
									}else{
										// collect data kelas
										$getKls = $feederKls->getDetailKelasKuliah($token, "id_prodi='$id_prodi' and id_semester='$id_smt' and trim(nama_kelas_kuliah)=trim('$id_nm_kls') and id_matkul='$id_mk'", "", 1000, 0, $url);
										$getKls = json_decode($getKls,true);
										$id_kls="";
										foreach ($getKls['data'] as $dtGetKls){
											$id_kls=$dtGetKls['id_kelas_kuliah'];
										}
										if($id_kls==""){
											$n_error++;
											$msg_e="Kelas dengan nama ".$id_nm_kls.", kode mata kuliah ".$kd_mk.", semester ".$id_smt." tidak ditemukan".$id_kur;
											$msg_error=$msg_error."<br>".$msg_e;
										}else{
											// insert data via ws
											$temp_data['id_registrasi_dosen'] = $id_reg_ptk;
											$temp_data['id_kelas_kuliah'] = $id_kls;
											$temp_data['sks_substansi_total'] = $sks_mk;
											$temp_data['rencana_minggu_pertemuan'] = $ttp_muka;
											$temp_data['realisasi_minggu_pertemuan'] = $ttp_muka;
											$temp_data['id_jenis_evaluasi'] = 1;
											$result = $feederDsn->setDosenKelas($url, $token, $temp_data);
											$result = json_decode($result,true);
											if (is_array($result)) {
												if ($result['error_code']==0) {
													$n_sukses++;
													$msg_s="Data Mengajar dosen untuk dosen NIDN ".$nidn." kode mata kuliah ".$kd_mk." kelas ".$id_nm_kls." berhasil ditambah";					$msg_sukses=$msg_sukses."<br>".$msg_s;																			}else{
													$n_error++;
													$msg_e="Error ".$result['error_code']." (".$nidn."): ".$result['error_desc'];
													$msg_error=$msg_error."<br>".$msg_e;
												}
											}else{
												$msg_e= "Error ".$result['error_code'].":".$result['error_desc'];
												$msg_error = $msg_error."<br>".$msg_e;
											}
										}
									}
								}
							}
							//}
							$i++;
						}
						echo "T|".$n_sukses." data Sukses : <br>".$msg_sukses."<br>---------<br>".$n_error." Data Error: <br>".$msg_error;
					}
				}
			}
		}
	}

	function updnidnlapAction() {
		$user = new Menu();
		$menu = "zzfdsn/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// makes disable layout
			$this->_helper->getHelper('layout')->disableLayout();
			// session id sp
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			$id_sp=$ses_feeder->id_sp;
			$token=$ses_feeder->token;
			$url=$ses_feeder->url;
			// start inserting
			$request = $this->getRequest()->getPost();
			$x = $request['x'];
			$nidn_dsn = array();
			$pkls = array();
			for($i=0;$i<$x;$i++){
				if($request['ajar_'.$i]) {
					$nidn_dsn[]=$request['nidn_'.$i];
					$pkls[]=$request['pkls_'.$i];
				}
			}
			// validation
			if(count($nidn_dsn)==0){
				echo "F|Tidak ada data yang akan diupdate NIDN nya. Cheklist dahulu|";
			}else{
				if(!$token){
					echo "F|Masa token telah habis, silakan generate ulang token!";
				}else{
					// get data krs
					$msg_sukses="";
					$msg_error="";
					$n_sukses=0;
					$n_error=0;
					// loop nidn
					$feederPkls = new FeederPaketkelas();
					$i=0;
					foreach ($pkls as $dtPaketKls){
						$nidn = $nidn_dsn[$i];
						$paket = $pkls[$i];
						// update nind lap di paket kelas
						$updFPkls = $feederPkls->updNidnLapPaketKelas($nidn, $paket);
						if($updFPkls=='F'){
							$n_error++;
							$msg_e="Gagal update NIDN di SIA :".$nidn;
							$msg_error=$msg_error."<br>".$msg_e;
						}else{
							$n_sukses++;
							$msg_sukses=$msg_sukses."<br>"."NIDN berhasil diubah";	
						}
						$i++;
					}
					echo "T|".$n_sukses." data Sukses : <br>".$msg_sukses."<br>---------<br>".$n_error." Data Error: <br>".$msg_error;
				}
			}
		}
	}
	
	// akm
	function showfakmAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$per = $request['per'];
		$prd = $request['prd'];
		$akt = $request['akt'];
		$smt_m = $request['smt_m'];
		$opt = $request['opt'];
		// set session
		$param = new Zend_Session_Namespace('param_fakm');
		$param->per=$per;
		$param->prd=$prd;
		$param->akt=$akt;
		$param->smt_m=$smt_m;
		$param->opt=$opt;
	}
	function riwayatmhsAction(){
	//$this->_helper->getHelper('layout')->disableLayout();
	error_reporting(E_ALL);
	$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			$id_sp=$ses_feeder->id_sp;
			$token=$ses_feeder->token;
			$url=$ses_feeder->url;
		//echo $id_sp;
		//echo $url;
		$riwayat = new FeederAkm();
		echo $riwayat->getIdRegisterMahasiswa($token,"nim='A211001'",'1','0',$url);

	}
	function insfakmAction() {
		$user = new Menu();
		$menu = "zzfakm/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// makes disable layout
			$this->_helper->getHelper('layout')->disableLayout();
			// session id sp
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			$id_sp=$ses_feeder->id_sp;
			$token=$ses_feeder->token;
			$url=$ses_feeder->url;
			// start inserting
			$request = $this->getRequest()->getPost();
			$x = $request['x'];
			$kode_prodi=$request['prd'];
			$id_smt=$request['smt'];
			$dtReg=array();
			for($i=0;$i<$x;$i++){
				if($request['reg_'.$i]) {
					$dtReg[]=$request['reg_'.$i];
				}
			}
			// validation
			if(count($dtReg)==0){
				echo "F|Tidak ada data AKM yang akan ditransfer. Cheklist dahulu AKM yang akan ditransfer|";
			}else{
				if(!$token){
					echo "F|Masa token telah habis, silakan generate ulang token!";
				}else{
					// get data akm
					$getDataAkm=$dtReg;
					$msg_sukses="";
					$msg_error="";
					$n_sukses=0;
					$n_error=0;
					//-- prodi/sms
					$feederProdi=new FeederProdi();
					$getProdi = $feederProdi->getProdi($token, "kode_program_studi='$kode_prodi'", "", 1, 0, $url);
					$getProdi = json_decode($getProdi,true);
					if(!$getProdi['data']){
						$n_error++;
						$msg_e="Prodi tidak ditemukan!";
						$msg_error=$msg_error."<br>".$msg_e;
					}else{
						foreach ($getProdi['data'] as $dtProdi){
							$id_prodi = $dtProdi['id_prodi'];
						}
						// loop nim akm
						$feederAkm = new FeederAkm();
						$n=1;
						foreach ($getDataAkm as $dtAkm){
							$arrDtAkm=explode('###', $dtAkm);
							$id_reg_pd=$arrDtAkm[1];
							$stAkm=$arrDtAkm[2];
							$sksAkm=intval($arrDtAkm[3]);
							$ipsAkm=floatval($arrDtAkm[4]);
							$skstotAkm=intval($arrDtAkm[5]);
							$ipkAkm=floatval($arrDtAkm[6]);
							$biayaAkm=floatval($arrDtAkm[7]);
							if ($biayaAkm==0){
								$biayaAkm=0.01;
							}
							//get id register
							$riwayat = new FeederAkm();
							$getMhsreg = $riwayat->getIdRegisterMahasiswa($token,"nim='".$id_reg_pd."'",'1','0',$url);
							$getMhsreg = json_decode($getMhsreg,true);
							foreach ($getMhsreg['data'] as $dtMhs){
								$idRegMhs = $dtMhs['id_registrasi_mahasiswa'];

							}
							// collect data
							$data['id_registrasi_mahasiswa']=$idRegMhs;
							$data['id_semester']=$id_smt;
							$data['id_status_mahasiswa']=$stAkm;
							$data['sks_semester']=$sksAkm;
							$data['ips']=$ipsAkm;
							$data['total_sks']=$skstotAkm;
							$data['ipk']=$ipkAkm;
							$data['biaya_kuliah_smt']=$biayaAkm;
							$data['id_pembiayaan']=1;
							// insert data via ws
							$result = $feederAkm->setPerkuliahan($url, $token, $data);
							$result = json_decode($result,true);
							if (is_array($result)) {
								if ($result['error_code']==0) {
									$n_sukses++;
									$msg_s="Data AKM baris ke ".$n." berhasil ditambah";
									$msg_sukses=$msg_sukses."<br>".$msg_s;
								}else{
									$n_error++;
									$msg_e="Error ".$result['error_code']." baris ke ".$n." : ".$result['error_desc']."-".$sksAkm."-".$ipsAkm."-".$skstotAkm."-".$ipkAkm."-".$id_reg_pd;
									$msg_error=$msg_error."<br>".$msg_e;
								}
							}else{
								$msg_e= "Error ".$result['error_code'].":".$result['error_desc'];
								$msg_error = $msg_error."<br>".$msg_e;
							}
							$n++;
						}
						echo "T|".$n_sukses." data Sukses : <br>".$msg_sukses."<br>---------<br>".$n_error." Data Error: <br>".$msg_error;
					}
				}
			}
		}
	}
	
	function updfakmAction() {
		$user = new Menu();
		$menu = "zzfakm/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// makes disable layout
			$this->_helper->getHelper('layout')->disableLayout();
			// session id sp
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			$id_sp=$ses_feeder->id_sp;
			$token=$ses_feeder->token;
			$url=$ses_feeder->url;
			// start inserting
			$request = $this->getRequest()->getPost();
			$y = $request['y'];
			$kode_prodi=$request['prd'];
			$id_smt=$request['smt'];
			$dtReg=array();
			for($i=0;$i<$y;$i++){
				if($request['dtreg_'.$i]) {
					$dtReg[]=$request['dtreg_'.$i];
				}
			}
			// validation
			if(count($dtReg)==0){
				echo "F|Tidak ada data AKM yang akan diupdate. Cheklist dahulu AKM yang akan diupdate".$y."|";
			}else{
				if(!$token){
					echo "F|Masa token telah habis, silakan generate ulang token!";
				}else{
					// get data akm
					$getDataAkm=$dtReg;
					$msg_sukses="";
					$msg_error="";
					$n_sukses=0;
					$n_error=0;
					//-- prodi/sms
					$feederProdi=new FeederProdi();
					$getProdi = $feederProdi->getProdi($token, "kode_program_studi='$kode_prodi'", "", 1, 0, $url);
					$getProdi = json_decode($getProdi,true);
					if(!$getProdi['data']){
						$n_error++;
						$msg_e="Prodi tidak ditemukan!";
						$msg_error=$msg_error."<br>".$msg_e;
					}else{
						foreach ($getProdi['data'] as $dtProdi){
							$id_prodi = $dtProdi['id_prodi'];
						}
						// loop nim akm
						$feederMhs = new FeederMhs();
						$feederAkm = new FeederAkm();
						$n=1;
						foreach ($getDataAkm as $dtAkm){
							$arrDtAkm=explode('###', $dtAkm);
							$id_reg_pd=$arrDtAkm[0];
							$stAkm=$arrDtAkm[1];
							$sksAkm=intval($arrDtAkm[2]);
							$ipsAkm=floatval($arrDtAkm[3]);
							$skstotAkm=intval($arrDtAkm[4]);
							$ipkAkm=floatval($arrDtAkm[5]);
							$biayaAkm=floatval($arrDtAkm[6]);
							if ($biayaAkm==0){
								$biayaAkm=0.01;
							}
							// collect data
							$key['id_registrasi_mahasiswa']=$id_reg_pd;
							$key['id_semester']=$id_smt;
							$data['id_status_mahasiswa']=$stAkm;
							$data['sks_semester']=$sksAkm;
							$data['ips']=$ipsAkm;
							$data['total_sks']=$skstotAkm;
							$data['ipk']=$ipkAkm;
							$data['biaya_kuliah_smt']=$biayaAkm;
							$data['id_pembiayaan']=1;
							$result=$feederAkm->updPerkuliahan($url, $token, $key, $data);
							$result = json_decode($result,true);
							if (is_array($result)) {
								if ($result['error_code']==0) {
									$n_sukses++;
									$msg_s="Data AKM baris ke-".$n." berhasil diupdate";
									$msg_sukses=$msg_sukses."<br>".$msg_s;
								}else{
									$n_error++;
									$msg_e="Error ".$result['error_code']." baris ke-".$n."): ".$result['error_desc'];
									$msg_error=$msg_error."<br>".$msg_e;
								}
							}else{
								$msg_e= "Error ".$result['error_code'].":".$result['error_desc'];
								$msg_error = $msg_error."<br>".$msg_e;
							}
							$n++;
						}
						echo "T|".$n_sukses." data Sukses : <br>".$msg_sukses."<br>---------<br>".$n_error." Data Error: <br>".$msg_error;
					}
				}
			}
		}
	}
	
	
	// mahasiswa-feeder
	function showfalmAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$akt = $request['akt'];
		$prd = $request['prd'];
		$opt = $request['opt'];
		// set session
		$param = new Zend_Session_Namespace('param_falm');
		$param->akt=$akt;
		$param->prd=$prd;
		$param->opt=$opt;
	}
	
	function insfalmAction() {
		$user = new Menu();
		$menu = "zzfalm/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// makes disable layout
			$this->_helper->getHelper('layout')->disableLayout();
		// session id sp
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			$id_sp=$ses_feeder->id_sp;
			$token=$ses_feeder->token;
			$url=$ses_feeder->url;
			// start inserting
			$request = $this->getRequest()->getPost();
			$x = $request['x'];
			$kode_prodi=$request['prd'];
			$nim=array();
			for($i=0;$i<$x;$i++){
				if($request['nim_'.$i]) {
					$nim[]=$request['nim_'.$i];
				}
			}
			// validation
			if(count($nim)==0){
				echo "F|Tidak ada data mahasiswa keluar yang akan ditransfer. Cheklist dahulu mahasiswa yang akan ditransfer datanya|";
			}else{
				if(!$token){
					echo "F|Masa token telah habis, silakan generate ulang token!";
				}else{
					// get data mahasiswa
					$feederMhs = new FeederMhs();
					$getDataMhs=$feederMhs->getMahasiswaByNim($nim);
					$msg_sukses="";
					$msg_error="";
					$n_sukses=0;
					$n_error=0;
					// loop nim
					$feederMhs = new FeederMhs();
					$feederMhsOut = new FeederMhsOut();
					foreach ($getDataMhs as $dtMhs){
						// get id pd
						$nim_mhs=$dtMhs['nim'];
						$getDataMhsPT = $feederMhs->getListMahasiswa($token, "nim='$nim_mhs'", "", 1, 0, $url);
						$getDataMhsPT = json_decode($getDataMhsPT,true);
						$id_registrasi_mahasiswa="";
						foreach ($getDataMhsPT['data'] as $dtMhsPT){
							$id_registrasi_mahasiswa=$dtMhsPT['id_registrasi_mahasiswa'];
						}
						if($id_registrasi_mahasiswa==""){
							$n_error++;
							$msg_e="Mahasiswa dengan NIM ".$nim_mhs." tidak ditemukan";
							$msg_error=$msg_error."<br>".$msg_e;
						}else{
							// collect data
							$key['id_registrasi_mahasiswa']=$id_registrasi_mahasiswa;
							$data['id_registrasi_mahasiswa']=$id_registrasi_mahasiswa;
							$data['id_jenis_keluar']=$dtMhs['id_jns_keluar'];
							$data['tanggal_keluar']=$dtMhs['tgl_keluar'];
							$periode=new Periode();
							$getPeriode=$periode->getPeriodeByTgl($dtMhs['tgl_keluar']);
							$id_periode="";
							foreach ($getPeriode as $dtPeriode){
							    $arrThn=explode("-",$dtPeriode['tahun_ajaran']);
							    $thn=$arrThn[0];
							    if($dtPeriode['id_smt']=='GASAL'){
							        $smt='1';
							    }elseif ($dtPeriode['id_smt']=='GENAP'){
							        $smt='2';
							    }else{
							        $smt='3';
							    }
							    $id_periode=$thn.''.$smt;
							}
							$data['id_periode_keluar']=$id_periode;
							if($dtMhs['id_jns_keluar']=='1'){
								$data['jalur_skripsi']='1';
								$data['nomor_sk_yudisium']=$dtMhs['sk_yudisium'];
								$data['tanggal_sk_yudisium']=$dtMhs['tgl_sk_yudisium'];;
								$data['ipk']=$dtMhs['ipk'];
								$data['nomor_ijazah']=$dtMhs['no_ijazah'];
								$data['judul_skripsi']=$dtMhs['judul_skripsi'];
							}
							$result=$feederMhsOut->setMhsOut($url, $token, $data);
							$result=json_decode($result,true);
							if (is_array($result)) {
								if ($result['error_code']==0) {
									$n_sukses++;
									$msg_s="Data mahasiswa keluar NIM ".$dtMhs['nim']." berhasil ditambahkan";
									$msg_sukses=$msg_sukses."<br>".$msg_s;
								}else{
									$n_error++;
									$msg_e="Error ".$result['error_code']." (".$dtMhs['nim']."): ".$result['error_desc'];
									$msg_error=$msg_error."<br>".$msg_e;
								}
							}else{
								$msg_e= "Error ".$result['error_code'].":".$result['error_desc'];
								$msg_error = $msg_error."<br>".$msg_e;
							}
						}
					}
					echo "T|".$n_sukses." data Sukses : <br>".$msg_sukses."<br>---------<br>".$n_error." Data Error: <br>".$msg_error;
				}
			}
		}
	}
	
	// nilai transfer
	function showfntrfAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$akt = $request['akt'];
		$prd = $request['prd'];
		// set session
		$param = new Zend_Session_Namespace('param_fntrf');
		$param->akt=$akt;
		$param->prd=$prd;
	}
}
