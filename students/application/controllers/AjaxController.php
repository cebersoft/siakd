<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Ajax Controller -> Controller untuk submit via ajax
*/
class AjaxController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Praregister');
		Zend_Loader::loadClass('Register');
		Zend_Loader::loadClass('Kuliah');
		Zend_Loader::loadClass('KuliahTA');
		Zend_Loader::loadClass('Perwalian');
		Zend_Loader::loadClass('Pkrs');
		Zend_Loader::loadClass('SurveyMhs');
		Zend_Loader::loadClass('TugasMhs');
		Zend_Loader::loadClass('DiskusiMhs');
		Zend_Loader::loadClass('QuizMhs');
		Zend_Loader::loadClass('Kuisioner');
		Zend_Loader::loadClass('JudulTA');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Validation');
		$auth = Zend_Auth::getInstance();
		$ses_std = new Zend_Session_Namespace('ses_std');
		if (($auth->hasIdentity())and($ses_std->uname)) {
			// global var
			$this->uname=Zend_Auth::getInstance()->getIdentity()->nim;
			$this->id=Zend_Auth::getInstance()->getIdentity()->id_mhs;
		}else{
			echo "F|Sesi anda sudah habis. Silakan login ulang!|";
		}
		// disabel layout
		$this->_helper->layout->disableLayout();
	}
	
	function updpwdAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// start updating
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$nim=$this->uname;
		$old = $request['old_pwd'];
		$new1=$request['new_pwd1'];
		$new2=$request['new_pwd2'];
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($new1!=$new2){
			$err++;
			$msg=$msg."<strong>- Password baru tidak sama</strong><br>";
		}
		if($vd->validasiLength($new1,1,20)=='F'){
			$err++;
			$msg=$msg."<strong>- Password baru tidak boleh kosong dan maksimal 20 karakter</strong><br>";
		}
		if($err==0){
			// set database
			$mahasiswa = new Mahasiswa();
			$updPwd = $mahasiswa->updPwd2Mahasiswa($new1, $nim, $old);			
			echo $updPwd;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function inspraregAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$nim = $this->uname;
		$kd_periode = $request['per'];
		$kd_reg = $request['kd_reg'];
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($nim,1,100)=='F'){
			$err++;
			$msg=$msg."<strong>- NIM tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($kd_periode,1,100)=='F'){
			$err++;
			$msg=$msg."<strong>- Periode akademik tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($kd_reg,1,100)=='F'){
			$err++;
			$msg=$msg."<strong>- Status registrasi tidak boleh kosong</strong><br>";
		}
		if($err==0){
			// set database
			$register = new Praregister();
			$setRegister = $register->setPraRegister($nim,$kd_periode,$kd_reg);
			echo $setRegister;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}

	function delpraregAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    		$nim = $param[0];
    		$kd_periode = $param[1];
		$register = new Praregister();
		$delRegister = $register->delPraRegister($nim,$kd_periode);
		echo $delRegister;
	}

	function insregAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$nim = $this->uname;
		$kd_periode = $request['per'];
		$kd_reg = $request['kd_reg'];
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($nim,1,100)=='F'){
			$err++;
			$msg=$msg."<strong>- NIM tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($kd_periode,1,100)=='F'){
			$err++;
			$msg=$msg."<strong>- Periode akademik tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($kd_reg,1,100)=='F'){
			$err++;
			$msg=$msg."<strong>- Status registrasi tidak boleh kosong</strong><br>";
		}
		if($err==0){
			// set database
			$register = new Register();
			$setRegister = $register->setRegister($nim,$kd_periode,$kd_reg);
			// log
			$arrSetRegister=explode("|", $setRegister);
			if($arrSetRegister[0]!='F'){
				$setLog=$register->setRegisterLog(2, '', $this->uname, $nim, $kd_periode, "Input registrasi", "Data kode registrasi: ".$kd_reg);
			}
			echo $setRegister;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}

	function delregAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    		$nim = $param[0];
    		$kd_periode = $param[1];
		$register = new Register();
		$delRegister = $register->delRegister($nim,$kd_periode);
		echo $delRegister;
	}
	
	function inskrsAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$n = $request['n'];
		$nim = $this->uname;
		$paket=array();
		for($i=0;$i<$n;$i++){
			if($request['paket_'.$i]) {
				$paket[]=$request['paket_'.$i];
			}
		}
		// validation
		$err=0;
		$msg="";
		if(count($paket)==0){
			$err++;
			$msg=$msg."<strong>- Tidak ada paket kelas yang ditambahkan<br>";
		}
		$vd = new Validation();
		if($err==0){
			$approved="f";
			// set database
			$kuliah = new Kuliah();
			$msgins="";
			foreach ($paket as $dtPaket) {
				$setKuliah =$kuliah->setKuliah($nim,$dtPaket,$approved);
				// log
				$arrSetKuliah=explode("|", $setKuliah);
				if($arrSetKuliah[0]!='F'){
					$setLog=$kuliah->setKuliahLog(2, '', $this->uname , '', $arrSetKuliah[0], 'Input KRS', 'data paket kelas : '.$dtPaket);
				}
			}
			echo $setKuliah;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function delkrsAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    	$kd_kuliah = $param[0];
		$kuliah = new Kuliah();
		$delKuliah = $kuliah->delKuliah($kd_kuliah);
		echo $delKuliah;
	}
	
	function inskrstaAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$nim = $this->uname;
		$kd_paket = $request['pkt_kls'];
		$per_mulai = $request['per_mulai'];
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($kd_paket,1,100)=='F'){
			$err++;
			$msg=$msg."<strong>- Paket Kelas TA tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($per_mulai,1,100)=='F'){
			$err++;
			$msg=$msg."<strong>- Periode mulai TA tidak boleh kosong</strong><br>";
		}
		if($err==0){
			$approved="f";
			// set database
			$kuliahTA = new KuliahTA();
			$setKuliahTA =$kuliahTA->setKuliahTA($nim,$kd_paket,$per_mulai,$approved);
			// log
			$arrSetKuliahTA=explode("|", $setKuliahTA);
			if($arrSetKuliahTA[0]!='F'){
				$setLog=$kuliahTA->setKuliahTALog(2, '', $this->uname, '', $arrSetKuliahTA[0], 'Input KRS TA', 'data paket kelas TA : '.$kd_paket);
			}
			echo $setKuliahTA;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function delkrstaAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    		$kd_kuliah = $param[0];
		$kuliahTA = new KuliahTA();
		$delKuliahTA = $kuliahTA->delKuliahTA($kd_kuliah);
		echo $delKuliahTA;
	}
	
	function updkrsAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$kd_kuliah = $request['kd'];
		$sks_take = $request['sks_take'];
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($kd_kuliah,1,100)=='F'){
			$err++;
			$msg=$msg."<strong>- Kode kuliah tidak boleh kosong</strong><br>";
		}
		if(intval($sks_take)==0){
			$err++;
			$msg=$msg."<strong>- SKS diambil tidak boleh kosong</strong><br>";	
		}
		if($err==0){
			// set database
			$kuliah = new Kuliah();
			$updSKSKuliah =$kuliah->updSKSKuliah($sks_take,$kd_kuliah);
			// log
			$arrUpdSKSKuliah=explode("|", $updSKSKuliah);
			if($arrUpdSKSKuliah[0]!='F'){
				$setLog=$kuliah->setKuliahLog(2, '', $this->uname, '', $kd_kuliah, 'Edit SKS : '.$sks_take, 'data sks diubah : '.$sks_take);
			}
			echo $updSKSKuliah;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function inspwlAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$kd_periode = $request['per'];
		$isi = $this->_helper->string->esc_quote($request['isi']);
		$asal="M";
		$nim=$this->uname;
		$sender=$this->_helper->string->esc_quote($request['nm_mhs']);
		$receiver=$this->_helper->string->esc_quote($request['dw']);
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength(trim($isi),1,200)=='F'){
			$err++;
			$msg=$msg."<strong>- Isi feedback tidak boleh kosong, maksimal 200 karakter</strong><br>";
		}
		if($err==0){
			// set database
			$perwalian = new Perwalian();
			$setPerwalian=$perwalian->setPerwalianFeed($asal, $kd_periode, $nim, $isi, $sender, $receiver);
			echo $setPerwalian;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}

	function inspkrsaddAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$n = $request['n'];
		$nim = $this->uname;
		$paket=array();
		for($i=0;$i<$n;$i++){
			if($request['paket_'.$i]) {
				$paket[]=$request['paket_'.$i];
			}
		}
		// validation
		$err=0;
		$msg="";
		if(count($paket)==0){
			$err++;
			$msg=$msg."<strong>- Tidak ada paket kelas yang ditambahkan<br>";
		}
		$vd = new Validation();
		if($err==0){
			$approved="f";
			// set database
			$pkrs = new Pkrs();
			$msgins="";
			$i=1;
			foreach ($paket as $dtPaket) {
				$setPkrsAdd =$pkrs->setPkrs($nim, $dtPaket, 0, 0, 'i', 'f', '');
				$arrMsg=explode("|", $setPkrsAdd);
				$msgins=$msgins."<br> Data Mata kuliah ke -".$i.":".$arrMsg[1];
				$i++;
			}
			echo "T|".$msgins;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function inspkrstaaddAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$nim = $this->uname;
		$kd_paket = $request['pkt_kls'];
		$per_mulai = $request['per_mulai'];
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($kd_paket,1,100)=='F'){
			$err++;
			$msg=$msg."<strong>- Paket Kelas TA tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($per_mulai,1,100)=='F'){
			$err++;
			$msg=$msg."<strong>- Periode mulai TA tidak boleh kosong</strong><br>";
		}
		if($err==0){
			$approved="f";
			// set database
			$pkrs = new Pkrs();
			$setPkrsTaAdd=$pkrs->setPkrs($nim, $kd_paket, 0, 0, "i", "t", $per_mulai);
			echo $setPkrsTaAdd;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function delpkrsAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    	$nim = $param[0];
    	$kd_paket = $param[1];
		$pkrs = new Pkrs();
		$delPkrs = $pkrs->delPkrs($nim, $kd_paket);
		echo $delPkrs;
	}
	
	function inspkrsdropAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    	$nim = $param[0];
    	$kd_paket = $param[1];
    	$sks_ded=$param[2];
		$ta = $this->_request->get('ta');
		if($ta){
			$aTa='t';
			$permulai=$param[3];
		}else{
			$aTa='f';
			$permulai="";
		}
		$pkrs = new Pkrs();
		$setPkrsDrop = $pkrs->setPkrs($nim, $kd_paket, $sks_ded, 0, 'd', $aTa, $permulai);
		echo $setPkrsDrop;
	}
	
	function inspkrsupdAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$kd_kuliah = $request['kd'];
		$sks_take = $request['sks_take'];
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($kd_kuliah,1,100)=='F'){
			$err++;
			$msg=$msg."<strong>- Kode kuliah tidak boleh kosong</strong><br>";
		}
		if(intval($sks_take)==0){
			$err++;
			$msg=$msg."<strong>- SKS diambil tidak boleh kosong</strong><br>";	
		}
		if($err==0){
			// set database
			$kuliah = new Kuliah();
			$pkrs=new Pkrs();
			$getKuliah=$kuliah->getKuliahByKd($kd_kuliah);
			if($getKuliah){
				foreach ($getKuliah as $dtKuliah) {
					$sks_def=$dtKuliah['sks_tm']+$dtKuliah['sks_prak']+$dtKuliah['sks_prak_lap']+$dtKuliah['sks_sim'];
					$sks_ded=$sks_def-$sks_take;
					$setpkrsupd=$pkrs->setPkrs($dtKuliah['nim'], $dtKuliah['kd_paket_kelas'], $sks_ded, 0, 'u', 'f', 'f');
				}
			}
			echo $setpkrsupd;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function updpkrsAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$nim=$this->uname;
		$kd_paket = $request['pkt'];
		$sks_taken = $request['sks_take'];
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($kd_paket,1,100)=='F'){
			$err++;
			$msg=$msg."<strong>- Paket kelas tidak boleh kosong</strong><br>";
		}
		if(intval($sks_taken)==0){
			$err++;
			$msg=$msg."<strong>- SKS diambil tidak boleh kosong</strong><br>";	
		}
		if($err==0){
			// set database
			$pkrs = new Pkrs();
			$updSKSPkrs =$pkrs->updSksPkrs($nim, $kd_paket, $sks_taken);
			echo $updSKSPkrs;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}

	function inssurvAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$nim = $request['nim'];
		$idsurv = $request['idsurv'];
		$nilai = $request['rating'];
		$comment = $this->_helper->string->esc_quote($request['comm']);
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if(intval($nilai)==0){
			$err++;
			$msg=$msg."<strong>- Anda belum mengisi penilaian<br>";
		}
		if($vd->validasiLength(trim($comment),1,200)=='F'){
			$err++;
			$msg=$msg."<strong>- Masukan dan saran tidak boleh kosong, maksimal 200 karakter</strong><br>";
		}
		if($err==0){
			// set database
			$surveyMhs = new SurveyMhs();
			$setSurv=$surveyMhs->setSurveyDtl($idsurv, $nim, $nilai, $comment);
			echo $setSurv;
		}else{
			echo "T|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}

	function inskuisAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$nimhsmsmh = $this->uname;
		$telpomsmh = $this->_helper->string->esc_quote($request['telpomsmh']);
		$emailmsmh = $this->_helper->string->esc_quote($request['emailmsmh']);
		$tahun_lulus = $this->_helper->string->esc_quote($request['tahun_lulus']);
		$f21=$this->_helper->string->esc_quote($request['v21']);
		$f22=$this->_helper->string->esc_quote($request['v22']);
		$f23=$this->_helper->string->esc_quote($request['v23']);
		$f24=$this->_helper->string->esc_quote($request['v24']);
		$f25=$this->_helper->string->esc_quote($request['v25']);
		$f26=$this->_helper->string->esc_quote($request['v26']);
		$f27=$this->_helper->string->esc_quote($request['v27']);
		$f301=$this->_helper->string->esc_quote($request['v301']);
		$f302 =$this->_helper->string->esc_quote($request['v302']);
		$f303 =$this->_helper->string->esc_quote($request['v303']);
		$f401=$this->_helper->string->esc_quote($request['v401']);
		$f402=$this->_helper->string->esc_quote($request['v402']);
		$f403=$this->_helper->string->esc_quote($request['v403']);
		$f404=$this->_helper->string->esc_quote($request['v404']);
		$f405=$this->_helper->string->esc_quote($request['v405']);
		$f406=$this->_helper->string->esc_quote($request['v406']);
		$f407=$this->_helper->string->esc_quote($request['v407']);
		$f408=$this->_helper->string->esc_quote($request['v408']);
		$f409=$this->_helper->string->esc_quote($request['v409']);
		$f410=$this->_helper->string->esc_quote($request['v410']);
		$f411=$this->_helper->string->esc_quote($request['v411']);
		$f412=$this->_helper->string->esc_quote($request['v412']);
		$f413=$this->_helper->string->esc_quote($request['v413']);
		$f414=$this->_helper->string->esc_quote($request['v414']);
		$f415=$this->_helper->string->esc_quote($request['v415']);
		$f416=$this->_helper->string->esc_quote($request['v416']);
		$f6 =$this->_helper->string->esc_quote($request['v6']);
		$f501=$this->_helper->string->esc_quote($request['v501']);
		$f502 =$this->_helper->string->esc_quote($request['v502']);
		$f503 =$this->_helper->string->esc_quote($request['v503']);
		$f7 =$this->_helper->string->esc_quote($request['v7']);
		$f7a=$this->_helper->string->esc_quote($request['v7a']);
		$f8=$this->_helper->string->esc_quote($request['v8']);
		$f901=$this->_helper->string->esc_quote($request['v901']);
		$f902=$this->_helper->string->esc_quote($request['v902']);
		$f903=$this->_helper->string->esc_quote($request['v903']);
		$f904=$this->_helper->string->esc_quote($request['v904']);
		$f905=$this->_helper->string->esc_quote($request['v905']);
		$f906=$this->_helper->string->esc_quote($request['v906']);
		$f1001=$this->_helper->string->esc_quote($request['v1001']);
		$f1002=$this->_helper->string->esc_quote($request['v1002']);
		$f1101=$this->_helper->string->esc_quote($request['v1101']);
		$f1102=$this->_helper->string->esc_quote($request['v1102']);
		$f1201=$this->_helper->string->esc_quote($request['v1201']);
		$f1202=$this->_helper->string->esc_quote($request['v1202']);
		$f1301 =$this->_helper->string->esc_quote($request['v1301']);
		$f1302 =$this->_helper->string->esc_quote($request['v1302']);
		$f1303 =$this->_helper->string->esc_quote($request['v1303']);
		$f14=$this->_helper->string->esc_quote($request['v14']);
		$f15=$this->_helper->string->esc_quote($request['v15']);
		$f1601=$this->_helper->string->esc_quote($request['v1601']);
		$f1602=$this->_helper->string->esc_quote($request['v1602']);
		$f1603=$this->_helper->string->esc_quote($request['v1603']);
		$f1604=$this->_helper->string->esc_quote($request['v1604']);
		$f1605=$this->_helper->string->esc_quote($request['v1605']);
		$f1606=$this->_helper->string->esc_quote($request['v1606']);
		$f1607=$this->_helper->string->esc_quote($request['v1607']);
		$f1608=$this->_helper->string->esc_quote($request['v1608']);
		$f1609=$this->_helper->string->esc_quote($request['v1609']);
		$f1610=$this->_helper->string->esc_quote($request['v1610']);
		$f1611=$this->_helper->string->esc_quote($request['v1611']);
		$f1612=$this->_helper->string->esc_quote($request['v1612']);
		$f1613=$this->_helper->string->esc_quote($request['v1613']);
		$f1614=$this->_helper->string->esc_quote($request['v1614']);
		$f1701=$this->_helper->string->esc_quote($request['v1701']);
		$f1702b=$this->_helper->string->esc_quote($request['v1702b']);
		$f1703=$this->_helper->string->esc_quote($request['v1703']);
		$f1704b=$this->_helper->string->esc_quote($request['v1704b']);
		$f1705=$this->_helper->string->esc_quote($request['v1705']);
		$f1705a=$this->_helper->string->esc_quote($request['v1705a']);
		$f1706=$this->_helper->string->esc_quote($request['v1706b']); // --- anomali
		$f1706ba=$this->_helper->string->esc_quote($request['v1706ba']);
		$f1707=$this->_helper->string->esc_quote($request['v1707']);
		$f1708b=$this->_helper->string->esc_quote($request['v1708b']);
		$f1709=$this->_helper->string->esc_quote($request['v1709']);
		$f1710b=$this->_helper->string->esc_quote($request['v1710b']);
		$f1711=$this->_helper->string->esc_quote($request['v1711']);
		$f1711a="";
		$f1712b=$this->_helper->string->esc_quote($request['v1712b']);
		$f1712a="";
		$f1713=$this->_helper->string->esc_quote($request['v1713']);
		$f1714b=$this->_helper->string->esc_quote($request['v1714b']);
		$f1715=$this->_helper->string->esc_quote($request['v1715']);
		$f1716b=$this->_helper->string->esc_quote($request['v1716b']);
		$f1717=$this->_helper->string->esc_quote($request['v1717']);
		$f1718b=$this->_helper->string->esc_quote($request['v1718b']);
		$f1719=$this->_helper->string->esc_quote($request['v1719']);
		$f1720b=$this->_helper->string->esc_quote($request['v1720b']);
		$f1721=$this->_helper->string->esc_quote($request['v1721']);
		$f1722b=$this->_helper->string->esc_quote($request['v1722b']);
		$f1723=$this->_helper->string->esc_quote($request['v1723']);
		$f1724b=$this->_helper->string->esc_quote($request['v1724b']);
		$f1725=$this->_helper->string->esc_quote($request['v1725']);
		$f1726b=$this->_helper->string->esc_quote($request['v1726b']);
		$f1727=$this->_helper->string->esc_quote($request['v1727']);
		$f1728b=$this->_helper->string->esc_quote($request['v1728b']);
		$f1729=$this->_helper->string->esc_quote($request['v1729']);
		$f1730b=$this->_helper->string->esc_quote($request['v1730b']);
		$f1731=$this->_helper->string->esc_quote($request['v1731']);
		$f1732b=$this->_helper->string->esc_quote($request['v1732b']);
		$f1733=$this->_helper->string->esc_quote($request['v1733']);
		$f1734b=$this->_helper->string->esc_quote($request['v1734b']);
		$f1735=$this->_helper->string->esc_quote($request['v1735']);
		$f1736b=$this->_helper->string->esc_quote($request['v1736b']);
		$f1737=$this->_helper->string->esc_quote($request['v1737']);
		$f1737a=$this->_helper->string->esc_quote($request['v1737a']);
		$f1738=$this->_helper->string->esc_quote($request['v1738b']);  // ---- anomali
		$f1738ba=$this->_helper->string->esc_quote($request['v1738ba']);
		$f1739=$this->_helper->string->esc_quote($request['v1739']);
		$f1740b=$this->_helper->string->esc_quote($request['v1740b']);
		$f1741=$this->_helper->string->esc_quote($request['v1741']);
		$f1742b=$this->_helper->string->esc_quote($request['v1742b']);
		$f1743=$this->_helper->string->esc_quote($request['v1743']);
		$f1744b=$this->_helper->string->esc_quote($request['v1744b']);
		$f1745=$this->_helper->string->esc_quote($request['v1745']);
		$f1746b=$this->_helper->string->esc_quote($request['v1746b']);
		$f1747=$this->_helper->string->esc_quote($request['v1747']);
		$f1748b=$this->_helper->string->esc_quote($request['v1748b']);
		$f1749=$this->_helper->string->esc_quote($request['v1749']);
		$f1750b=$this->_helper->string->esc_quote($request['v1750b']);
		$f1751=$this->_helper->string->esc_quote($request['v1751']);
		$f1752b=$this->_helper->string->esc_quote($request['v1752b']);
		$f1753=$this->_helper->string->esc_quote($request['v1753']);
		$f1754b=$this->_helper->string->esc_quote($request['v1754b']);
		$val=$f21.'|'.$f22.'|'.$f23.'|'.$f24.'|'.$f25.'|'.$f26.'|'.$f27.'|'.$f301.'|'.$f302 .'|'.$f303 .'|'.$f401.'|'.$f402.'|'.$f403.'|'.$f404.'|'.$f405.'|'.$f406.'|'.$f407.'|'.$f408.'|'.$f409.'|'.$f410.'|'.$f411.'|'.$f412.'|'.$f413.'|'.$f414.'|'.$f415.'|'.$f416.'|'.$f6 .'|'.$f501.'|'.$f502 .'|'.$f503 .'|'.$f7 .'|'.$f7a.'|'.$f8.'|'.$f901.'|'.$f902.'|'.$f903.'|'.$f904.'|'.$f905.'|'.$f906.'|'.$f1001.'|'.$f1002.'|'.$f1101.'|'.$f1102.'|'.$f1201.'|'.$f1202.'|'.$f1301 .'|'.$f1302 .'|'.$f1303 .'|'.$f14.'|'.$f15.'|'.$f1601.'|'.$f1602.'|'.$f1603.'|'.$f1604.'|'.$f1605.'|'.$f1606.'|'.$f1607.'|'.$f1608.'|'.$f1609.'|'.$f1610.'|'.$f1611.'|'.$f1612.'|'.$f1613.'|'.$f1614.'|'.$f1701.'|'.$f1702b.'|'.$f1703.'|'.$f1704b.'|'.$f1705.'|'.$f1705a.'|'.$f1706.'|'.$f1706ba.'|'.$f1707.'|'.$f1708b.'|'.$f1709.'|'.$f1710b.'|'.$f1711.'|'.$f1711a.'|'.$f1712b.'|'.$f1712a.'|'.$f1713.'|'.$f1714b.'|'.$f1715.'|'.$f1716b.'|'.$f1717.'|'.$f1718b.'|'.$f1719.'|'.$f1720b.'|'.$f1721.'|'.$f1722b.'|'.$f1723.'|'.$f1724b.'|'.$f1725.'|'.$f1726b.'|'.$f1727.'|'.$f1728b.'|'.$f1729.'|'.$f1730b.'|'.$f1731.'|'.$f1732b.'|'.$f1733.'|'.$f1734b.'|'.$f1735.'|'.$f1736b.'|'.$f1737.'|'.$f1737a.'|'.$f1738.'|'.$f1738ba.'|'.$f1739.'|'.$f1740b.'|'.$f1741.'|'.$f1742b.'|'.$f1743.'|'.$f1744b.'|'.$f1745.'|'.$f1746b.'|'.$f1747.'|'.$f1748b.'|'.$f1749.'|'.$f1750b.'|'.$f1751.'|'.$f1752b.'|'.$f1753.'|'.$f1754b;
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($nimhsmsmh,1,200)=='F'){
			$err++;
			$msg=$msg."<strong>- NIM tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength(trim($telpomsmh),1,200)=='F'){
			$err++;
			$msg=$msg."<strong>- No Telepon tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength(trim($emailmsmh),1,200)=='F'){
			$err++;
			$msg=$msg."<strong>- Email tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength(trim($tahun_lulus),1,200)=='F'){
			$err++;
			$msg=$msg."<strong>- Tahun lulus tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength(trim($tahun_lulus),1,200)=='F'){
			$err++;
			$msg=$msg."<strong>- Tahun lulus tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength(trim($f21),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f21 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f22),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f22 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f23),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f23 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f24),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f24 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f25),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f25 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f26),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f26 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f27),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f27 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f301),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f301 tidak boleh kosong</strong><br>';}
		if(trim($f301)=='1'){
			if($vd->validasiLength(trim($f302 ),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f302  tidak boleh kosong jika Anda memilih pilihan (1) </strong><br>';}
		}elseif (trim($f301)=='2'){
			if($vd->validasiLength(trim($f303 ),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f303  tidak boleh kosong jika Anda memilih pilihan (2)</strong><br>';}
		}
		if((trim($f301)=='1')or(trim($f301)=='2')){
			$err4=0;
			if($vd->validasiLength(trim($f401),1,200)=='F'){$err4++;}
			if($vd->validasiLength(trim($f402),1,200)=='F'){$err4++;}
			if($vd->validasiLength(trim($f403),1,200)=='F'){$err4++;}
			if($vd->validasiLength(trim($f404),1,200)=='F'){$err4++;}
			if($vd->validasiLength(trim($f405),1,200)=='F'){$err4++;}
			if($vd->validasiLength(trim($f406),1,200)=='F'){$err4++;}
			if($vd->validasiLength(trim($f407),1,200)=='F'){$err4++;}
			if($vd->validasiLength(trim($f408),1,200)=='F'){$err4++;}
			if($vd->validasiLength(trim($f409),1,200)=='F'){$err4++;}
			if($vd->validasiLength(trim($f410),1,200)=='F'){$err4++;}
			if($vd->validasiLength(trim($f411),1,200)=='F'){$err4++;}
			if($vd->validasiLength(trim($f412),1,200)=='F'){$err4++;}
			if($vd->validasiLength(trim($f413),1,200)=='F'){$err4++;}
			if($vd->validasiLength(trim($f414),1,200)=='F'){$err4++;}
			if($vd->validasiLength(trim($f415),1,200)=='F'){$err4++;}
			if($err4==15){
				$err++;
				$msg=$msg."<strong>Pilihlah setidaknya satu pada pertanyaan F4</strong><br>";
			}
			if($vd->validasiLength(trim($f501),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f501 tidak boleh kosong</strong><br>';}
			if(trim($f501)=='1'){
				if($vd->validasiLength(trim($f502 ),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f502  tidak boleh kosong, jika anda memilih pilihan 1</strong><br>';}
			}elseif (trim($f501)=='2'){
				if($vd->validasiLength(trim($f503 ),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f503  tidak boleh kosong, jika anda memilih pilhan 2</strong><br>';}
			}
			if($vd->validasiLength(trim($f6 ),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f6  tidak boleh kosong</strong><br>';}
			if($vd->validasiLength(trim($f7 ),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f7  tidak boleh kosong</strong><br>';}
			if($vd->validasiLength(trim($f7a),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f7a tidak boleh kosong</strong><br>';}
		}
		//if($vd->validasiLength(trim($f416),1,200)=='F'){$err4++;}
		if($vd->validasiLength(trim($f8),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f8 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1201),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1201  tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1301),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1301  tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1302),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1302  tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1303),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1303  tidak boleh kosong</strong><br>';}
		//if($vd->validasiLength(trim($f14),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f14 tidak boleh kosong</strong><br>';}
		//if($vd->validasiLength(trim($f15),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f15 tidak boleh kosong</strong><br>';}
		if(trim($f8)=='1'){
			if($vd->validasiLength(trim($f1101),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1101 tidak boleh kosong, jika anda memilih pilihan 1 di F8</strong><br>';}
			//if($vd->validasiLength(trim($f1301),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1301  tidak boleh kosong, jika anda memilih pilihan 1 di F8</strong><br>';}
			//if($vd->validasiLength(trim($f1302),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1302  tidak boleh kosong, jika anda memilih pilihan 1 di F8</strong><br>';}
			//if($vd->validasiLength(trim($f1303),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1303  tidak boleh kosong, jika anda memilih pilihan 1 di F8</strong><br>';}
			//if($vd->validasiLength(trim($f14),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f14 tidak boleh kosong, jika anda memilih pilihan 1 di F8</strong><br>';}
			//if($vd->validasiLength(trim($f15),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f15 tidak boleh kosong, jika anda memilih pilihan 1 di F8</strong><br>';}
			$err16=0;
			if($vd->validasiLength(trim($f1601),1,200)=='F'){$err16++;}
			if($vd->validasiLength(trim($f1602),1,200)=='F'){$err16++;}
			if($vd->validasiLength(trim($f1603),1,200)=='F'){$err16++;}
			if($vd->validasiLength(trim($f1604),1,200)=='F'){$err16++;}
			if($vd->validasiLength(trim($f1605),1,200)=='F'){$err16++;}
			if($vd->validasiLength(trim($f1606),1,200)=='F'){$err16++;}
			if($vd->validasiLength(trim($f1607),1,200)=='F'){$err16++;}
			if($vd->validasiLength(trim($f1608),1,200)=='F'){$err16++;}
			if($vd->validasiLength(trim($f1609),1,200)=='F'){$err16++;}
			if($vd->validasiLength(trim($f1610),1,200)=='F'){$err16++;}
			if($vd->validasiLength(trim($f1611),1,200)=='F'){$err16++;}
			if($vd->validasiLength(trim($f1612),1,200)=='F'){$err16++;}
			if($vd->validasiLength(trim($f1613),1,200)=='F'){$err16++;}
			if($err16==13){
				$err++;
				$msg=$msg."<strong>Pilihlah setidaknya satu pada pertanyaan F16, jika anda memilih pilihan 1 di F8 </strong><br>";
			}
		}elseif (trim($f8)=='2'){
			$err9=0;
			if($vd->validasiLength(trim($f901),1,200)=='F'){$err9++;}
			if($vd->validasiLength(trim($f902),1,200)=='F'){$err9++;}
			if($vd->validasiLength(trim($f903),1,200)=='F'){$err9++;}
			if($vd->validasiLength(trim($f904),1,200)=='F'){$err9++;}
			if($vd->validasiLength(trim($f905),1,200)=='F'){$err9++;}
			if($err9==5){
				$err++;
				$msg=$msg."<strong>Pilihlah setidaknya satu pada pertanyaan F9, jika anda memilih pilihan 2 di F8 </strong><br>";
			}
			if($vd->validasiLength(trim($f1001),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1001 tidak boleh kosong, jika anda memilih pilihan 2 di F8</strong><br>';}
		}
		//if($vd->validasiLength(trim($f1002),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1002 tidak boleh kosong</strong><br>';}
		//if($vd->validasiLength(trim($f1102),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1102 tidak boleh kosong</strong><br>';}
		//if($vd->validasiLength(trim($f1614),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1614 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1701),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1701 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1702b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1702b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1703),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1703 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1704b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1704b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1705),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1705 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1705a),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1705a tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1706),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1706 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1706ba),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1706ba tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1707),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1707 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1708b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1708b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1709),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1709 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1710b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1710b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1711),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1711 tidak boleh kosong</strong><br>';}
		//if($vd->validasiLength(trim($f1711a),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1711a tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1712b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1712b tidak boleh kosong</strong><br>';}
		//if($vd->validasiLength(trim($f1712a),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1712a tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1713),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1713 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1714b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1714b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1715),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1715 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1716b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1716b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1717),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1717 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1718b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1718b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1719),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1719 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1720b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1720b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1721),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1721 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1722b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1722b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1723),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1723 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1724b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1724b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1725),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1725 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1726b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1726b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1727),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1727 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1728b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1728b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1729),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1729 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1730b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1730b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1731),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1731 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1732b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1732b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1733),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1733 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1734b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1734b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1735),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1735 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1736b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1736b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1737),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1737 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1737a),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1737a tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1738),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1738 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1738ba),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1738ba tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1739),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1739 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1740b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1740b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1741),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1741 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1742b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1742b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1743),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1743 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1744b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1744b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1745),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1745 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1746b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1746b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1747),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1747 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1748b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1748b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1749),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1749 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1750b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1750b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1751),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1751 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1752b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1752b tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1753),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1753 tidak boleh kosong</strong><br>';}
		if($vd->validasiLength(trim($f1754b),1,200)=='F'){$err++;$msg=$msg.'<strong>Isian f1754b tidak boleh kosong</strong><br>';}
		if($err==0){
			// set database
			$kuis = new Kuisioner();
			$setKuis=$kuis->setKuis($nimhsmsmh, $telpomsmh, $emailmsmh, $tahun_lulus, $val);
			echo $setKuis;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg."<br>".$f401;
		}
	}

	// -- LMS
	function instugasmhsAction(){
	    // disabel layout
            $this->_helper->layout->disableLayout();
            // start inserting
            $request = $this->getRequest()->getPost();
            $id_tgs = $this->_helper->string->esc_quote(trim($request['id_tugas']));
	    $kd_kul = $this->_helper->string->esc_quote(trim($request['kd_kuliah']));
	    $rsp = $this->_helper->string->esc_quote(trim($request['rsp']));
	    $link = $this->_helper->string->esc_quote(trim($request['link']));
            $err=0;
	    $msg="";
	    $vd = new Validation();
	    if($vd->validasiLength($rsp,1,100)=='F'){
		$err++;
		$msg=$msg."<strong>- Respon tugas tidak boleh kosong maksimal 100 karakter</strong><br>";
	    }
            if($err==0){
                // start uploading
                if ((0<$_FILES["filez1"]["error"])) {
                    $msg= "F|Error: ". $_FILES["filez1"]["error"];
                    echo $msg;
                }else {
		    $tgsMhs=new TugasMhs();
                    $setTgs=$tgsMhs->setTugasMhs($id_tgs,$kd_kul,$rsp,$_FILES["filez1"]["name"],$link);
                    $arrSetTgs=explode("|", $setTgs);
                    if($arrSetTgs[0]!='F'){
                        $arrName = explode(".", $_FILES["filez1"]["name"]);
                        $newfilename=$arrSetTgs[0].".".end($arrName);
                        $file_url=Zend_Registry::get('FILE_URL');
                        $target_dir=$file_url.'/tugasmhs';
                        $target_file = str_replace("'", "", $target_dir ."/". $newfilename);
                        $uploadOk = 1;
                        $fileType = pathinfo($target_file,PATHINFO_EXTENSION);
                        $mimes = array('application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-powerpoint','application/vnd.openxmlformats-officedocument.presentationml.presentation');
                        $msg="";
                        if(!in_array($_FILES['filez1']['type'],$mimes)){
                            $msg=$msg."File Harus PDF, Ms.Word, Excel! <br> nama:".$_FILES["filez1"]["name"];
                            $uploadOk = 0;
                        }
                        if ($_FILES["filez1"]["size"] > (1024*20000)) {
                            $msg= $msg."File  maksimal 20 MB<br>";
                            $uploadOk = 0;
                        }
                        // Check if $uploadOk is set to 0 by an error
                        if ($uploadOk != 0) {
                            if (move_uploaded_file($_FILES["filez1"]["tmp_name"], $target_file)){
                                $msg=$arrSetTgs[0]."|Data berhasil disimpan . <br>File berhasil diupload";
                                echo $msg;
                            }else{
                                $delTugas=$tgsMhs->delTugasMhs($arrSetTgs[0]);
                                $msg= "F|Maaf terjadi error saat upload, silakan coba lagi.". $_FILES["filez1"]["error"];
                                echo $msg;
                            }
                        }else{
                            $delTugas=$tgsMhs->delTugasMhs($arrSetTgs[0]);
                            $msg= "F|Maaf terjadi error saat upload : <br>".$msg;
                            echo $msg;
                        }
                    }else{
                        echo $setTgs;
                    }
                }
            }else{
                echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
            }
    }

    function insdiskusimhsAction(){
	    // disabel layout
            $this->_helper->layout->disableLayout();
            // start inserting
            $request = $this->getRequest()->getPost();
            $id_disk = $this->_helper->string->esc_quote(trim($request['id_diskusi']));
	    $kd_kul = $this->_helper->string->esc_quote(trim($request['kd_kuliah']));
	    $rsp = $this->_helper->string->esc_quote(trim($request['rsp']));
            $err=0;
	    $msg="";
	    $vd = new Validation();
	    if($vd->validasiLength($rsp,1,1000)=='F'){
		$err++;
		$msg=$msg."<strong>- Respon tugas tidak boleh kosong maksimal 1000 karakter</strong><br>";
	    }
            if($err==0){
		    $diskMhs=new DiskusiMhs();
	            $setDisk=$diskMhs->setDiskusiMhs($id_disk,$kd_kul,$rsp);
                    echo $setDisk;
            }else{
                echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
            }
    }

    function genquizmhsAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    		$kd_kuliah = $param[0];
		$id_quiz0 = $param[1];
		$quizMhs = new QuizMhs();
		$genQuiz0Mhs= $quizMhs ->genQuiz0Mhs($kd_kuliah,$id_quiz0);
		echo $genQuiz0Mhs;
    }

    function delquizmhsAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    		$id = $param[0];
		$quizMhs = new QuizMhs();
		$delQuiz0Mhs= $quizMhs ->delQuiz0Mhs($id);
		echo $delQuiz0Mhs;
    }

    function insjudultaAction(){
	    // disabel layout
            $this->_helper->layout->disableLayout();
            // start inserting
            $request = $this->getRequest()->getPost();
	    $kd_kul = $this->_helper->string->esc_quote(trim($request['kd_kuliah']));
	    $judul = $this->_helper->string->esc_quote(trim($request['judul']));
            $err=0;
	    $msg="";
	    $vd = new Validation();
	    if($vd->validasiLength($judul,1,10000)=='F'){
		$err++;
		$msg=$msg."<strong>- Judul tidak boleh kosong</strong><br>";
	    }
            if($err==0){
		    $judulTa=new JudulTA();
	            $setJudul=$judulTa->setJudulTA($kd_kul,$judul);
                    echo $setJudul;
            }else{
                echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
            }
    }

    	function deljudultaAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    		$id = $param[0];
		$judulTa = new JudulTA();
		$delJudulTA = $judulTa->delJudulTA($id);
		echo $delJudulTA;
	}
}
