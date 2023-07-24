<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Ajax 3 Controller -> Controller untuk submit via ajax (3)
*/
class Ajax3Controller extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Paketkelas');
		Zend_Loader::loadClass('Register');
		Zend_Loader::loadClass('Kuliah');
		Zend_Loader::loadClass('Nilai');
		Zend_Loader::loadClass('Kbm');
		Zend_Loader::loadClass('Absensi');
		Zend_Loader::loadClass('AjarTA');
		Zend_Loader::loadClass('KelasTA');
		Zend_Loader::loadClass('KuliahTA');
		Zend_Loader::loadClass('NilaiTA');
		Zend_Loader::loadClass('Dosbim');
		Zend_Loader::loadClass('Dosji');
		Zend_Loader::loadClass('Alumni');
		Zend_Loader::loadClass('MhsOut');
		Zend_Loader::loadClass('Kuisioner');
		Zend_Loader::loadClass('MatkulTaApp');
		Zend_Loader::loadClass('PrpUjianTa');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Validation');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('PHPExcel');
		Zend_Loader::loadClass('PHPExcel_Cell_AdvancedValueBinder');
		Zend_Loader::loadClass('PHPExcel_IOFactory');
		$auth = Zend_Auth::getInstance();
		$ses_ac = new Zend_Session_Namespace('ses_ac');
		if (($auth->hasIdentity())and($ses_ac->uname)) {
			$this->namauser =Zend_Auth::getInstance()->getIdentity()->nama;
		}else{
			echo "F|Sesi anda sudah habis. Silakan login ulang!|";
		}
		// username global
		$this->username=$ses_ac->uname;
		// disabel layout
		$this->_helper->layout->disableLayout();
	}
	// Register
	function showregAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$frm=$this->_request->get('frm');
		$prd = $request['prd'.$frm];
		$per = $request['per'.$frm];
		$akt = $request['akt'.$frm];
		// set session
		$param = new Zend_Session_Namespace('param_reg');
		$param->prd=$prd;
		$param->per=$per;
		$param->akt=$akt;
	}

	function insregAction(){
		$user = new Menu();
		$menu = "register/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$nim = $request['nim_o'];
			$kd_periode = $request['per_o'];
			$kd_reg = $request['kd_reg_o'];
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
					$setLog=$register->setRegisterLog(1, $this->username, '', $nim, $kd_periode, "Input registrasi", "Data kode registrasi : ".$kd_reg);
				}
				echo $setRegister;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function updregAction(){
		$user = new Menu();
		$menu = "register/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$nim = $request['nim_e'];
			$kd_periode = $request['per_e'];
			$kd_reg = $request['kd_reg_e'];
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
				$updRegister = $register->updRegister($nim,$kd_periode,$kd_reg);
				// log
				$arrSetRegister=explode("|", $updRegister);
				if($arrSetRegister[0]!='F'){
					$setLog=$register->setRegisterLog(1, $this->username, '', $nim, $kd_periode, "Edit registrasi", "Data kode registrasi : ".$kd_reg);
				}
				echo $updRegister;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delregAction(){
		$user = new Menu();
		$menu = "register/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
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
	}

	function showlistregAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$prd = $request['prd'];
		$per = $request['per'];
		$akt = $request['akt'];
		$stat = $request['stat'];
		// set session
		$param = new Zend_Session_Namespace('param_listreg');
		$param->prd=$prd;
		$param->per=$per;
		$param->akt=$akt;
		$param->stat=$stat;
	}


	// KRS
	function inskrsAction(){
		$user = new Menu();
		$menu = "krs/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$n = $request['n'];
			$nim = $request['nim'];
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
			if($vd->validasiLength($nim,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- NIM tidak boleh kosong</strong><br>";
			}
			if($err==0){
				$approved="t";
				// set database
				$kuliah = new Kuliah();
				$msgins="";
				foreach ($paket as $dtPaket) {
					$setKuliah =$kuliah->setKuliah($nim,$dtPaket,$approved);
					// log
					$arrSetKuliah=explode("|", $setKuliah);
					if($arrSetKuliah[0]!='F'){
						$setLog=$kuliah->setKuliahLog(1, $this->username, '' , '', $arrSetKuliah[0], 'Input KRS', 'data paket kelas : '.$dtPaket);
					}
				}
				echo $setKuliah;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function copykrsAction(){
		$user = new Menu();
		$menu = "krs/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$nim_from = $request['nim_from'];
			$nim_to = $request['nim_to'];
			$kd_periode = $request['per'];
			$approved="t";
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($nim_from,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- NIM Mahasiswa asal tidak boleh kosong</strong><br>";
			}
			if($err==0){
				$approved="t";
				// set database
				$kuliah = new Kuliah();
				$copyKuliah =$kuliah->copyKuliah($nim_to,$nim_from,$kd_periode,$approved);
				// log
				$arrCopyKuliah=explode("|", $copyKuliah);
				if($arrCopyKuliah[0]=='T'){
					$getKuliah=$kuliah->getKuliahByNimPeriode($nim_to, $kd_periode);
					foreach ($getKuliah as $dtKuliahNimTo){
						$setLog=$kuliah->setKuliahLog(1, $this->username, '' , '', $dtKuliahNimTo['kd_kuliah'], 'Input KRS', 'data paket kelas : '.$dtKuliahNimTo['kd_paket_kelas']);
					}
					$kuliahTa=new KuliahTA();
					$getKuliahTA=$kuliahTa->getKuliahTAByNimPeriode($nim_to, $kd_periode);
					foreach ($getKuliahTA as $dtKuliahTANimTo){
						$setLog=$kuliahTa->setKuliahTALog(1, $this->username, '' , '', $dtKuliahNimTo['kd_kuliah'], 'Input KRS', 'data paket kelas : '.$dtKuliahNimTo['kd_paket_kelas']);
					}
				}
				echo $copyKuliah;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delkrsAction(){
		$user = new Menu();
		$menu = "krs/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$kd_kuliah = $param[0];
			$kuliah = new Kuliah();
			$delKuliah = $kuliah->delKuliah($kd_kuliah);
			echo $delKuliah;
		}
	}

	function updkrsAction(){
		$user = new Menu();
		$menu = "krs/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
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
					$setLog=$kuliah->setKuliahLog(1, $this->username, '' , '', $kd_kuliah, 'Edit SKS : '.$sks_take, 'data sks diubah : '.$sks_take);
				}
				echo $updSKSKuliah;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function updkrsstatAction(){
		$user = new Menu();
		$menu = "krs/updatestat";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$stat = $param[0];
	    	$kd_kuliah = $param[1];
			$kuliah = new Kuliah();
			$updAppKuliah = $kuliah->updAppKuliah($stat,$kd_kuliah);
			// log
			$arrUpdAppKuliah=explode("|", $updAppKuliah);
			if($arrUpdAppKuliah[0]!='F'){
				if($stat=='f'){
					$aksi="Disapprove";
				}else{
					$aksi="Approve";
				}
				$setLog=$kuliah->setKuliahLog(1, $this->username, '' , '', $kd_kuliah, $aksi, '-');
			}
			echo $updAppKuliah;
		}
	}

	// Nilai - Paket Kelas
	function showpklsAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$frm=$this->_request->get('frm');
		$prd = $request['prd'.$frm];
		$per = $request['per'.$frm];
		// set session
		$param = new Zend_Session_Namespace('param_pkls');
		$param->prd=$prd;
		$param->per=$per;
	}

	function updnlmAction(){
		$user = new Menu();
		$menu = "nilai/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd_kuliah = $request['kd_kuliah'];
			$p1 = floatval($request['p1']);
			$p2 = floatval($request['p2']);
			$p3 = floatval($request['p3']);
			$p4 = floatval($request['p4']);
			$p5 = floatval($request['p5']);
			$p6 = floatval($request['p6']);
			$p7 = floatval($request['p7']);
			$p8 = floatval($request['p8']);
			$uts = floatval($request['uts']);
			$uas = floatval($request['uas']);
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($kd_kuliah,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Kode kuliah tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$nilai = new Nilai();
				$updNilai =$nilai->updNilai($p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$uts,$uas,$kd_kuliah);
				echo $updNilai;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function uplnlmAction(){
		$user = new Menu();
		$menu = "nilai/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			 if (0<$_FILES["file"]["error"] ) {
		        echo "Error: ". $_FILES["file"]["error"] . "<br>";
		    }
		    else {
		    	$temp = explode(".", $_FILES["file"]["name"]);
				$newfilename = md5(round(microtime(true))) . '.' . end($temp);
				$x=rand(100000,999999);
				$path = __FILE__;
				$filePath = str_replace('controllers/Ajax3Controller.php','temps',$path);
				$target_dir = $filePath;
				$target_file = str_replace("'", "", $target_dir ."/'". $newfilename);
				$uploadOk = 1;
				$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
				$mimes = array('application/vnd.ms-excel');
				$msg="";
				if(!in_array($_FILES['file']['type'],$mimes)){
				   	$msg=$msg."File harus excel! <br>";
				   	$uploadOk = 0;
				} 
				// Check if file already exists
				if (file_exists($target_file)) {
				    $msg= $msg."File sudah ada, silakan coba lagi<br>";
				    $uploadOk = 0;
				}
				// Check file size
				if ($_FILES["file"]["size"] > 1000000) {
				    $msg= $msg."File terlalu besar<br>";
				    $uploadOk = 0;
				}
				// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
					echo $msg;
				} else {
					if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
				        $uploadOk = 1;
				        // to database
				        $objPHPExcel = PHPExcel_IOFactory::load($target_file);
				        $objPHPExcel->setActiveSheetIndex(0);
						$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
						$jml_row = $objPHPExcel->getActiveSheet()->getHighestRow()-1;
						$kuliah = new Kuliah();
						$arr_data=array();
						foreach ($cell_collection as $cell) {
							$column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
							$row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
							$data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
							if ($row < 11) {
								$header[$row][$column] = $data_value;
							} else {
								$arr_data[$row][$column] = $data_value;
							}
						}
						if (count($arr_data)>0) {
							$n=1;
							$n_error=0;
							foreach ($arr_data as $key => $value) {
								$nim = $value['B'];
								$p1=floatval($value['E']);
								$p2=floatval($value['F']);
								$p3=floatval($value['G']);
								$p4=floatval($value['H']);
								$uts=floatval($value['I']);
								$p5=floatval($value['J']);
								$p6=floatval($value['K']);
								$p7=floatval($value['L']);
								$p8=floatval($value['M']);
								$uas=floatval($value['N']);
								$kd_kuliah = $value['R'];
								$getKuliah = $kuliah->getKuliahByKd($kd_kuliah);
								if(count($getKuliah)==0){
									$msg=$msg."Data mahasiswa baris ke ".$n." tidak valid! Gunakan template yang sudah diunduh<br>";
									$n_error++;
								}
								if(($p1>100)or($p2>100)or($p3>100)or($p4>100)or($p5>100)or($p6>100)or($p7>100)or($p8>100)or($uts>100)or($uas>100)){
									$msg=$msg."Baris ke-".$n.": Ada nilai yang lebih dari 100<br>";
									$n_error++;
								}
								$n++;
							}
							$n=1;
							if($n_error==0){
								$nilai = new Nilai();
								foreach ($arr_data as $key => $value) {
									$nim = $value['B'];
									$p1=floatval($value['E']);
									$p2=floatval($value['F']);
									$p3=floatval($value['G']);
									$p4=floatval($value['H']);
									$uts=floatval($value['I']);
									$p5=floatval($value['J']);
									$p6=floatval($value['K']);
									$p7=floatval($value['L']);
									$p8=floatval($value['M']);
									$uas=floatval($value['N']);
									$kd_kuliah = $value['R'];
									$updNilai =$nilai->updNilai($p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$uts,$uas,$kd_kuliah);
									$arrResult=explode('|', $updNilai);
									$msg=$msg."Baris ke-".$n." : ".$arrResult[1]."<br>";
									$n++;
								}
								echo $msg;
							}else{
								echo $msg;
							}
						}else{
							$msg= "Maaf tidak ada baris nilai yang dapat diproses, silakan cek kembali";
							echo $msg;
						}
				    } else {
				        $msg= "Maaf terjadi error saat upload, silakan coba lagi.";
						echo $msg;
				    }
				    unlink('application/temps/'.$newfilename);
				}
		    }
		}
	}

	function fixnlmAction(){
		$user = new Menu();
		$menu = "nilai/fix";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
			$kd_kuliah = $param[0];
	    	$stat = $param[1];
			$nilai = new Nilai();
			$updStatNilai = $nilai->updStatNilai($stat,$kd_kuliah);
			echo $updStatNilai;
		}
	}

	function fixnlmmassAction(){
		$user = new Menu();
		$menu = "nilai/fix";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$request = $this->getRequest()->getPost();
			$x = $request['x'];
			$stat=1;
			$kd_kul=array();
			$nim=array();
			for($i=0;$i<$x;$i++){
				if($request['kul_'.$i]) {
					$kd_kul[]=$request['kul_'.$i];
					$nim[]=$request['nim_'.$i];
				}
			}
			$nilai = new Nilai();
			// validation
			if(count($kd_kul)==0){
				echo "F|Tidak ada data nilai yang akan diubah statusnya. Cheklist dahulu mahasiswa yang akan di-fix nilainya|";
			}else{
				$msg="";
				$i=0;
				foreach ($kd_kul as $dtKul){
					$updStatNilai = $nilai->updStatNilai($stat,$dtKul);
					$arrMsg=explode('|', $updStatNilai);
					$msg=$msg."<br>NIM ".$nim[$i]." : ".$arrMsg[1];
					$i++;
				}
				echo "T|".$msg;
			}
		}
	}

	function delnlmAction(){
		$user = new Menu();
		$menu = "nilai/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
			$kd_kuliah = $param[0];
			$nilai = new Nilai();
			$updNilai =$nilai->updNilai(0,0,0,0,0,0,0,0,0,0,$kd_kuliah);
			echo "T|Berhasil menghapus seluruh komponen nilai";
		}
	}
	
	// KHS
	function showkhsAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$nim = $request['nim'];
		$per = $request['per'];
		// set session
		$param = new Zend_Session_Namespace('param_khs');
		$param->nim=$nim;
		$param->per=$per;
	}
	
	// transkrip
	function showtrkpAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$nim = $request['nim'];
		$smt = $request['smt'];
		// set session
		$param = new Zend_Session_Namespace('param_trkp');
		$param->nim=$nim;
		$param->smt=$smt;
	}

	// KBM
	function inskbmAction(){
		$user = new Menu();
		$menu = "kbm/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$tgl = $request['tgl'];
			$materi = $this->_helper->string->esc_quote(trim($request['materi']));
			$media = $this->_helper->string->esc_quote(trim($request['media']));
			$kejadian = $this->_helper->string->esc_quote(trim($request['kejadian']));
			$starttime = trim($request['start']);
			$endtime = trim($request['end']);
			$tempat = $this->_helper->string->esc_quote(trim($request['tempat']));
			$kdpaket = trim($request['kd_paket']);
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($kdpaket,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Kode paket kelas tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($tgl,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Tanggal tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($materi,1,500)=='F'){
				$err++;
				$msg=$msg."<strong>- Materi perkuliahan tidak boleh kosong maksimal 500 karakter</strong><br>";
			}
			if($vd->validasiLength($media,1,25)=='F'){
				$err++;
				$msg=$msg."<strong>- Media tidak boleh kosong maksimal 25 karakter</strong><br>";
			}
			if($vd->validasiLength($kejadian,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Kejadian selama perkuliahan tidak boleh kosong maksimal 100 karakter</strong><br>";
			}
			if(($vd->validasiLength($tempat,1,20)=='F')or($vd->validasiAlNum($tempat)=='F')){
				$err++;
				$msg=$msg."<strong>- Tempat tidak boleh kosong dan tidak boleh mengandung simbol maksimal 20 karakter</strong><br>";
			}

			if(($vd->validasiTime($starttime,1,100)=='F')or($vd->validasiTime($endtime)=='F')){
				$err++;
				$msg=$msg."<strong>- waktu perkuliahan tidak boleh kosong dan harus berformat hh:mm</strong><br>";
			}

			if(strtotime($endtime)-strtotime($starttime)<30){
				$err++;
				$msg=$msg."<strong>- waktu perkuliahan kurang dari 30 menit, set waktu selesai lebih besar minimal 30 menit daripada waktu mulai</strong><br>";	
			}

			if($err==0){
				// set database
				$kbm = new Kbm();
				$setKbm =$kbm->setKbm($tgl,$materi,$media,$kejadian,$starttime,$endtime,$tempat,$kdpaket);
				echo $setKbm;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delkbmAction(){
		$user = new Menu();
		$menu = "kbm/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
			$id_perkuliahan = $param[0];
			$kbm = new Kbm();
			$delKbm =$kbm->delKbm($id_perkuliahan);
			echo $delKbm;
		}
	}

	function updkbmAction(){
		$user = new Menu();
		$menu = "kbm/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$tgl = $request['tgl'];
			$materi = $this->_helper->string->esc_quote(trim($request['materi']));
			$media = $this->_helper->string->esc_quote(trim($request['media']));
			$kejadian = $this->_helper->string->esc_quote(trim($request['kejadian']));
			$starttime = trim($request['start']);
			$endtime = trim($request['end']);
			$tempat = $this->_helper->string->esc_quote(trim($request['tempat']));
			$id_perkuliahan = trim($request['id_perkuliahan']);
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($id_perkuliahan,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Kode paket kelas tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($tgl,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Tanggal tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($materi,1,500)=='F'){
				$err++;
				$msg=$msg."<strong>- Materi perkuliahan tidak boleh kosong maksimal 500 karakter</strong><br>";
			}
			if($vd->validasiLength($media,1,25)=='F'){
				$err++;
				$msg=$msg."<strong>- Media tidak boleh kosong maksimal 25 karakter</strong><br>";
			}
			if($vd->validasiLength($kejadian,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Kejadian selama perkuliahan tidak boleh kosong maksimal 100 karakter</strong><br>";
			}
			if(($vd->validasiLength($tempat,1,20)=='F')or($vd->validasiAlNum($tempat)=='F')){
				$err++;
				$msg=$msg."<strong>- Tempat tidak boleh kosong dan tidak boleh mengandung simbol maksimal 20 karakter</strong><br>";
			}

			if(($vd->validasiTime($starttime,1,100)=='F')or($vd->validasiTime($endtime)=='F')){
				$err++;
				$msg=$msg."<strong>- waktu perkuliahan tidak boleh kosong dan harus berformat hh:mm</strong><br>";
			}

			if(strtotime($endtime)-strtotime($starttime)<30){
				$err++;
				$msg=$msg."<strong>- waktu perkuliahan kurang dari 30 menit, set waktu selesai lebih besar minimal 30 menit daripada waktu mulai</strong><br>";	
			}

			if($err==0){
				// set database
				$kbm = new Kbm();
				$updKbm =$kbm->updKbm($tgl,$materi,$media,$kejadian,$starttime,$endtime,$tempat,$id_perkuliahan);
				echo $updKbm;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	// absensi

	function insabsAction(){
		$user = new Menu();
		$menu = "absensi/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id_perkuliahan = $request['id_perk'];
			$nim = $request['nim'];
			$stat = $request['stat_hdr'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($id_perkuliahan,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Kode perkuliahan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($nim,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- NIM tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($stat,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Status kehadiran tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$absensi = new Absensi();
				$setAbsensi =$absensi->setAbsensi($id_perkuliahan,$nim,$stat);
				echo $setAbsensi;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delabsAction(){
		$user = new Menu();
		$menu = "absensi/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
			$id_perkuliahan = $param[0];
			$nim = $param[1];
			$absensi = new Absensi();
			$delAbsensi =$absensi->delAbsensi($id_perkuliahan,$nim);
			echo $delAbsensi;
		}
	}

	function updabsAction(){
		$user = new Menu();
		$menu = "absensi/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
			$id_perkuliahan = $param[0];
			$nim = $param[1];
			$stat = $param[2];
			$absensi = new Absensi();
			$updAbsensi =$absensi->updAbsensi($stat,$id_perkuliahan,$nim);
			echo $updAbsensi;
		}
	}

	function updabsmassAction(){
		$user = new Menu();
		$menu = "absensi/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
			$id_perkuliahan = $param[0];
			$id_stat_hadir = $param[1];
			$absensi = new Absensi();
			$getAbsensi=$absensi->getAbsensiByPerkuliahan($id_perkuliahan);
			if($getAbsensi){
				$msg="";
				foreach ($getAbsensi as $dtAbsensi){
					$updAbsensi =$absensi->updAbsensi($id_stat_hadir,$id_perkuliahan,$dtAbsensi['nim']);
					$arrUpdAbsensi=explode("|", $updAbsensi);
					$msg=$msg."<br>NIM ".$dtAbsensi['nim']." : ".$arrUpdAbsensi[1];
				}
				echo "T|".$msg;
			}else{
				echo "F|Tidak ada data mahasiswa pada perkuliahan ini";
			}
		}
	}

	// lpm
	function showlpmAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$nim = $request['nim'];
		$per = $request['per'];
		// set session
		$param = new Zend_Session_Namespace('param_lpm');
		$param->nim=$nim;
		$param->per=$per;
	}
	

	// Ajar TA
	function insajartaAction(){
		$user = new Menu();
		$menu = "ajarta/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id_mk_kur = $this->_helper->string->esc_quote(trim($request['id_mk_kur']));
			$kd_dsn = $request['kd_dsn'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($id_mk_kur,3,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Mata Kuliah tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($kd_dsn,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Data dosen tidak ada</strong><br>";
			}
			if($err==0){
				// set database
				$ajarTA = new AjarTA();
				$ajarTA = $ajarTA->setAjarTA($kd_dsn,$id_mk_kur);
				echo $ajarTA;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	function delajartaAction(){
		$user = new Menu();
		$menu = "ajarta/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$id = $param[0];
			$ajarTA = new AjarTA();
			$delAjarTA = $ajarTA->delAjarTA($id);
			echo $delAjarTA;
		}
	}
	// kelas TA
	function showklstaAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$frm=$this->_request->get('frm');
		$prd = $request['prd'.$frm];
		$per = $request['per'.$frm];
		// set session
		$param = new Zend_Session_Namespace('param_klsta');
		$param->prd=$prd;
		$param->per=$per;
	}

	function insklstaAction(){
		$user = new Menu();
		$menu = "kelasta/new";
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
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($id_ajar,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Mata Kuliah TA tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($kd_periode,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Periode akademik tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$kelasTA = new KelasTA();
				$setKelasTA = $kelasTA->setKelasTA($id_ajar,$kd_periode);
				echo $setKelasTA;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delklstaAction(){
		$user = new Menu();
		$menu = "kelasta/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$kd = $param[0];
			$kelasTA = new KelasTA();
			$delKelasTA = $kelasTA->delKelasTA($kd);
			echo $delKelasTA;
		}
	}

	function updklstaAction(){
		$user = new Menu();
		$menu = "kelasta/edit";
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
			$note = $this->_helper->string->esc_quote($request['note']);
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			$tot_par=$p_p1+$p_p2+$p_p3+$p_p4+$p_p5+$p_p6+$p_p7+$p_p8;
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
				$kelasTA = new KelasTA();
				$updKelasTA = $kelasTA->updKelasTA($nm_p1,$nm_p2,$nm_p3,$nm_p4,$nm_p5,$nm_p6,$nm_p7,$nm_p8,$p_p1,$p_p2,$p_p3,$p_p4,$p_p5,$p_p6,$p_p7,$p_p8,$note,$kd_kls);
				echo $updKelasTA;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	// dosbim
	function showdsbmAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$frm=$this->_request->get('frm');
		$prd = $request['prd'.$frm];
		$per = $request['per'.$frm];
		// set session
		$param = new Zend_Session_Namespace('param_dsbm');
		$param->prd=$prd;
		$param->per=$per;
	}

	function showdsbm2Action(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$frm=$this->_request->get('frm');
		$prd = $request['prd2'.$frm];
		$per = $request['per2'.$frm];
		// set session
		$param = new Zend_Session_Namespace('param_dsbm2');
		$param->prd=$prd;
		$param->per=$per;
	}

	function insdsbmAction(){
		$user = new Menu();
		$menu = "dosbim/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd_dsn = $request['kd_dsn'];
			$kd_periode = $request['per'];
			$kd_prodi = $request['prd'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($kd_dsn,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Data dosen tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($kd_periode,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Periode akademik tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($kd_prodi,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Program studi tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// start uploading
                		if ((0<$_FILES["filez1"]["error"])) {
                    			$msg= "F|Error: ". $_FILES["filez1"]["error"];
                   			echo "F|".$msg;
                		}else {
					$doc_name=$_FILES["filez1"]["name"];
		    			// set database
					$dosbim = new Dosbim();
					$setDosbim = $dosbim->setDosbim($kd_dsn,$kd_periode,$kd_prodi);
                    			$arrSetDosbim=explode("|", $setDosbim );
                    			if($arrSetDosbim[0]!='F'){
                        			$arrName = explode(".", $_FILES["filez1"]["name"]);
                       				$newfilename=strtolower(str_replace('.','',$kd_dsn).str_replace('/','',$kd_periode).$kd_prodi).".".end($arrName);
                        			$file_url=Zend_Registry::get('FILE_URL');
                        			$target_dir=$file_url.'/sk/dosbim';
                        			$target_file = str_replace("'", "", $target_dir ."/". $newfilename);
                        			$uploadOk = 1;
                        			$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
                        			$mimes = array('application/pdf');
                        			$msg="";
                        			if(!in_array($_FILES['filez1']['type'],$mimes)){
                           				$msg=$msg."File Harus PDF! <br> nama:".$_FILES["filez1"]["name"];
                            				$uploadOk = 0;
                        			}
                        			if ($_FILES["filez1"]["size"] > (1024*5000)) {
                            				$msg= $msg."File  maksimal 5 MB<br>";
                            				$uploadOk = 0;
                        			}
                        			// Check if $uploadOk is set to 0 by an error
                        			if ($uploadOk != 0) {
                            				if (move_uploaded_file($_FILES["filez1"]["tmp_name"], $target_file)){
                                				$msg=$arrSetDosbim[0]."|Data berhasil disimpan . <br>File berhasil diupload : ".$_FILES["filez1"]["name"];
                                				echo $msg;
                            				}else{
                                				$msg= "F|Maaf terjadi error saat upload, silakan coba lagi.". $_FILES["filez1"]["error"];
                                				echo $msg;
                           			 	}
                        			}else{
                            				$msg= "F|Maaf terjadi error saat upload : <br>".$msg;
                            				echo $msg;
                        			}
                    			}else{
                        			echo $setDosbim ;
                    			}
                		}
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function deldsbmAction(){
		$user = new Menu();
		$menu = "dosbim/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    		$kd_dsn = $param[0];
	    		$kd_periode = $param[1];
	    		$kd_prodi = $param[2];
			$dosbim= new Dosbim();
			$delDsbm = $dosbim->delDsbm($kd_dsn,$kd_periode,$kd_prodi);
			echo $delDsbm;
		}
	}

	function insdsbm2Action(){
		$user = new Menu();
		$menu = "dosbim/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd_dsn = $request['kd_dsn'];
			$kd_periode = $request['per'];
			$kd_prodi = $request['prd'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($kd_dsn,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Data dosen tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($kd_periode,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Periode akademik tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($kd_prodi,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Program studi tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// start uploading
                		if ((0<$_FILES["filez1"]["error"])) {
                    			$msg= "F|Error: ". $_FILES["filez1"]["error"];
                   			echo "F|".$msg;
                		}else {
					$doc_name=$_FILES["filez1"]["name"];
		    			// set database
					$dosji = new Dosji();
					$setDosji = $dosji->setDosji($kd_dsn,$kd_periode,$kd_prodi);
                    			$arrSetDosji=explode("|", $setDosji);
                    			if($arrSetDosji[0]!='F'){
                        			$arrName = explode(".", $_FILES["filez1"]["name"]);
                       				$newfilename=strtolower(str_replace('.','',$kd_dsn).str_replace('/','',$kd_periode).$kd_prodi).".".end($arrName);
                        			$file_url=Zend_Registry::get('FILE_URL');
                        			$target_dir=$file_url.'/sk/dosji';
                        			$target_file = str_replace("'", "", $target_dir ."/". $newfilename);
                        			$uploadOk = 1;
                        			$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
                        			$mimes = array('application/pdf');
                        			$msg="";
                        			if(!in_array($_FILES['filez1']['type'],$mimes)){
                           				$msg=$msg."File Harus PDF! <br> nama:".$_FILES["filez1"]["name"];
                            				$uploadOk = 0;
                        			}
                        			if ($_FILES["filez1"]["size"] > (1024*5000)) {
                            				$msg= $msg."File  maksimal 5 MB<br>";
                            				$uploadOk = 0;
                        			}
                        			// Check if $uploadOk is set to 0 by an error
                        			if ($uploadOk != 0) {
                            				if (move_uploaded_file($_FILES["filez1"]["tmp_name"], $target_file)){
                                				$msg=$arrSetDosji[0]."|Data berhasil disimpan . <br>File berhasil diupload : ".$_FILES["filez1"]["name"];
                                				echo $msg;
                            				}else{
                                				$msg= "F|Maaf terjadi error saat upload, silakan coba lagi.". $_FILES["filez1"]["error"];
                                				echo $msg;
                           			 	}
                        			}else{
                            				$msg= "F|Maaf terjadi error saat upload : <br>".$msg;
                            				echo $msg;
                        			}
                    			}else{
                        			echo $setDosji;
                    			}
                		}
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function deldsbm2Action(){
		$user = new Menu();
		$menu = "dosbim/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    		$kd_dsn = $param[0];
	    		$kd_periode = $param[1];
	    		$kd_prodi = $param[2];
			$dosji= new Dosji();
			$delDosji = $dosji->delDosji($kd_dsn,$kd_periode,$kd_prodi);
			echo $delDosji;
		}
	}

	// KRS TA
	function showkrstaAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$frm=$this->_request->get('frm');
		$nim = $request['prd'.$frm];
		$per = $request['per'.$frm];
	}

	function inskrstaAction(){
		$user = new Menu();
		$menu = "krsta/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$nim = $request['nim'];
			$kd_paket = $request['pkt_kls'];
			$per_mulai = $request['per_mulai'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($nim,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- NIM tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($kd_paket,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Paket Kelas TA tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($per_mulai,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Periode mulai TA tidak boleh kosong</strong><br>";
			}
			if($err==0){
				$approved="t";
				// set database
				$kuliahTA = new KuliahTA();
				$setKuliahTA =$kuliahTA->setKuliahTA($nim,$kd_paket,$per_mulai,$approved);
				// log
				$arrSetKuliahTA=explode("|", $setKuliahTA);
				if($arrSetKuliahTA[0]!='F'){
					$setLog=$kuliahTA->setKuliahTALog(1, $this->username, '' , '', $arrSetKuliahTA[0], 'Input KRS TA', 'data paket kelas TA : '.$kd_paket);
				}
				echo $setKuliahTA;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delkrstaAction(){
		$user = new Menu();
		$menu = "krsta/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$kd_kuliah = $param[0];
			$kuliahTA = new KuliahTA();
			$delKuliahTA = $kuliahTA->delKuliahTA($kd_kuliah);
			echo $delKuliahTA;
		}
	}

	function updkrsstattaAction(){
		$user = new Menu();
		$menu = "krsta/updatestat";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$stat = $param[0];
	    	$kd_kuliah = $param[1];
			$kuliahTA = new KuliahTA();
			$updAppKuliahTA = $kuliahTA->updAppKuliahTA($stat,$kd_kuliah);
			// log
			$arrUpdAppKuliahTA=explode("|", $updAppKuliahTA);
			if($arrUpdAppKuliahTA[0]!='F'){
				if($stat=='f'){
					$aksi="Disapprove";
				}else{
					$aksi="Approve";
				}
				$setLog=$kuliahTA->setKuliahTALog(1, $this->username, '' , '', $kd_kuliah, $aksi, '-');
			}
			echo $updAppKuliahTA;
		}
	}

	// atribut dan nilai TA
	function updatrtaAction(){
		$user = new Menu();
		$menu = "atrta/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd_kuliah = $request['kd_kuliah'];
			$p1 = floatval($request['p1']);
			$p2 = floatval($request['p2']);
			$p3 = floatval($request['p3']);
			$p4 = floatval($request['p4']);
			$p5 = floatval($request['p5']);
			$p6 = floatval($request['p6']);
			$p7 = floatval($request['p7']);
			$p8 = floatval($request['p8']);
			$noreg = $this->_helper->string->esc_quote($request['noreg']);
			$judul = $this->_helper->string->esc_quote($request['judul']);
			$pemb1 = $request['pemb1'];
			$pemb2 = $request['pemb2'];
			$pemb3 = $request['pemb3'];
			$penj1 = $request['penj1'];
			$penj2 = $request['penj2'];
			$penj3 = $request['penj3'];
			$penj4 = $request['penj4'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($kd_kuliah,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Kode kuliah tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$nilaiTA = new NilaiTA();
				$updNilaiTA =$nilaiTA->updNilaiTA($p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$pemb1,$pemb2,$pemb3,$noreg,$judul,$penj1,$penj2,$penj3,$penj4,$kd_kuliah);
				echo $updNilaiTA;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function fixatrtaAction(){
		$user = new Menu();
		$menu = "atrta/fix";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
			$kd_kuliah = $param[0];
	    	$stat = $param[1];
			$nilaiTA = new NilaiTA();
			$updStatNilaiTA = $nilaiTA->updStatNilaiTA($stat,$kd_kuliah);
			echo $updStatNilaiTA;
		}
	}

	// Alumni
	function showalmAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$akt = $request['akt'];
		$prd = $request['prd'];
		$startdate = $request['startdate'];
		$enddate = $request['enddate'];
		// set session
		$param = new Zend_Session_Namespace('param_alm');
		$param->akt=$akt;
		$param->prd=$prd;
		$param->startdate=$startdate;
		$param->enddate=$enddate;
	}

	function insalmAction(){
		$user = new Menu();
		$menu = "alumni/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$nim = trim($request['nim']);
			$tgllulus = $request['tgllulus'];
			$nosk = $this->_helper->string->esc_quote(trim($request['nosk']));
			$tglsk = $request['tglsk'];
			$noijz = $this->_helper->string->esc_quote(trim($request['noijz']));
			$judul = $this->_helper->string->esc_quote(trim($request['judul']));
			$ipk = floatval($request['ipk']);
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($nim,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Data mahasiswa tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($tgllulus,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Tanggal lulus tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($nosk,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Nomor SK Yudisium tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($tglsk,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Tanggal SK Yudisium tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($noijz,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Nomor seri ijazah tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($judul,1,2000)=='F'){
				$err++;
				$msg=$msg."<strong>- Judul skripsi tidak boleh kosong</strong><br>";
			}
			if($vd->validasiBetween($ipk,0,4)=='F'){
				$err++;
				$msg=$msg."<strong>- IPK tidak boleh kosong dan di bawah atau sama dengan 4.00</strong><br>";
			}
			if($err==0){
				// set database
				$alumni = new Alumni();
				$setAlumni =$alumni->setAlumni($nim,$tgllulus,$noijz,$nosk,$judul,$tglsk,$ipk);
				echo $setAlumni;
				// set session for result
				$param = new Zend_Session_Namespace('param_alm');
				$mahasiswa = new Mahasiswa();
				$getMhs = $mahasiswa->getMahasiswaByNim($nim);
				foreach ($getMhs as $dtMhs) {
					$akt=$dtMhs['id_angkatan'];
					$prd=$dtMhs['kd_prodi'];
				}
				$param->akt=array($akt);
				$param->prd=array($prd);
				$param->startdate=$tgllulus;
				$param->enddate=$tgllulus;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delalmAction(){
		$user = new Menu();
		$menu = "alumni/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$nim = $param[0];
			$alumni = new Alumni();
			$delAlumni = $alumni->delAlumni($nim);
			echo $delAlumni;
		}
	}
	
	function uplalmAction(){
		$user = new Menu();
		$menu = "alumni/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			 if (0<$_FILES["file"]["error"] ) {
		        echo "Error: ". $_FILES["file"]["error"] . "<br>";
		    }
		    else {
		    	$vd = new Validation();
		    	$temp = explode(".", $_FILES["file"]["name"]);
				$newfilename = md5(round(microtime(true))) . '.' . end($temp);
				$x=rand(100000,999999);
				$path = __FILE__;
				$filePath = str_replace('controllers/Ajax3Controller.php','temps',$path);
				$target_dir = $filePath;
				$target_file = str_replace("'", "", $target_dir ."/'". $newfilename);
				$uploadOk = 1;
				$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
				$mimes = array('application/vnd.ms-excel');
				$msg="";
				if(!in_array($_FILES['file']['type'],$mimes)){
				   	$msg=$msg."File harus excel! <br>";
				   	$uploadOk = 0;
				} 
				// Check if file already exists
				if (file_exists($target_file)) {
				    $msg= $msg."File sudah ada, silakan coba lagi<br>";
				    $uploadOk = 0;
				}
				// Check file size
				if ($_FILES["file"]["size"] > 1000000) {
				    $msg= $msg."File terlalu besar<br>";
				    $uploadOk = 0;
				}
				// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
					echo $msg;
				} else {
					if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
				        $uploadOk = 1;
				        // to database
				        $objPHPExcel = PHPExcel_IOFactory::load($target_file);
				        $objPHPExcel->setActiveSheetIndex(0);
						$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
						$jml_row = $objPHPExcel->getActiveSheet()->getHighestRow()-1;
						$kuliah = new Kuliah();
						$arr_data=array();
						$nosk=$objPHPExcel->getActiveSheet()->getCell('B4')->getValue();
						$tglsk=$objPHPExcel->getActiveSheet()->getCell('B5')->getValue();
						$tglsk= date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($tglsk));
						$n_error=0;
						if((trim($nosk)=='')or($tglsk=='')){
							$msg=$msg."Nomor dan Tanggal SK tidak boleh kosong. Tanggal harus benar formatnya<br>";
							$n_error++;
						}
						foreach ($cell_collection as $cell) {
							$column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
							$row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
							$data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
							if ($row < 7) {
								$header[$row][$column] = $data_value;
							} else {
								$arr_data[$row][$column] = $data_value;
							}
						}
						if (count($arr_data)>0) {
							$n=1;
							if($n_error==0){
								$alumni = new Alumni();
								foreach ($arr_data as $key => $value) {
									$nim = $value['B'];
									$tgllulus=$value['D'];
									$tgllulus= date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($tgllulus));
									$noijz=$value['E'];
									$ipk=floatval($value['F']);
									$judul=$value['G'];
									$upAlumni = $alumni->setAlumni($nim, $tgllulus, $noijz, $nosk, $judul, $tglsk, $ipk);
									$arrResult=explode('|', $upAlumni);
									$msg=$msg."Baris ke-".$n." : ".$arrResult[1]."<br>";
									$n++;
								}
								echo $msg;
							}else{
								echo $msg;
							}
						}else{
							$msg= "Maaf tidak ada baris nilai yang dapat diproses, silakan cek kembali";
							echo $msg;
						}
				    } else {
				        $msg= "Maaf terjadi error saat upload, silakan coba lagi.";
						echo $msg;
				    }
				    unlink('application/temps/'.$newfilename);
				}
		    }
		}
	}
	
	// mahasiswa out
	function showmoutAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$akt = $request['akt'];
		$prd = $request['prd'];
		$startdate = $request['startdate'];
		$enddate = $request['enddate'];
		// set session
		$param = new Zend_Session_Namespace('param_mout');
		$param->akt=$akt;
		$param->prd=$prd;
		$param->startdate=$startdate;
		$param->enddate=$enddate;
	}
	
	function insmoutAction(){
		$user = new Menu();
		$menu = "mhsout/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$nim = trim($request['nim']);
			$tglout = $request['tglout'];
			$jns_kel = $request['jns_kel'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($nim,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Data mahasiswa tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($tglout,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Tanggal keluar tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($jns_kel,1,2)=='F'){
				$err++;
				$msg=$msg."<strong>- Keterangan keluar tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$mhsout = new MhsOut();
				$setMhsOut =$mhsout->setMhsOut($nim, $jns_kel, $tglout);
				echo $setMhsOut;
				// set session for result
				$param = new Zend_Session_Namespace('param_mout');
				$mahasiswa = new Mahasiswa();
				$getMhs = $mahasiswa->getMahasiswaByNim($nim);
				foreach ($getMhs as $dtMhs) {
					$akt=$dtMhs['id_angkatan'];
					$prd=$dtMhs['kd_prodi'];
				}
				$param->akt=array($akt);
				$param->prd=array($prd);
				$param->startdate=$tglout;
				$param->enddate=$tglout;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	function delmoutAction(){
		$user = new Menu();
		$menu = "mhsout/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    		$nim = $param[0];
			$mhsout = new MhsOut();
			$delMhsOut = $mhsout->delMhsOut($nim);
			echo $delMhsOut;
		}
	}

	// tracerid
	function showtrcAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$thn = $request['thn'];
		$prd = $request['prd'];
		// set session
		$param = new Zend_Session_Namespace('param_trc');
		$param->thn=$thn;
		$param->prd=$prd;
	}

	function showntrcAction(){
	    // makes disable layout
	    $this->_helper->getHelper('layout')->disableLayout();
	    $request = $this->getRequest()->getPost();
	    // gets value from ajax request
	    $akt = $request['akt'];
	    $prd = $request['prd'];
	    // set session
	    $param = new Zend_Session_Namespace('param_ntrc');
	    $param->akt=$akt;
	    $param->prd=$prd;
	}

	function deltrcAction(){
		$user = new Menu();
		$menu = "tracerid/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
		    	$nim = $param[0];
			$kuis = new Kuisioner();
			$delKuis = $kuis->delKuis($nim);
			echo $delKuis;
		}
	}

	function showtrc2Action(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$thn = $request['thn'];
		$prd = $request['prd'];
		// set session
		$param = new Zend_Session_Namespace('param_tracer2');
		$param->thn=$thn;
		$param->prd=$prd;
	}

	function showntrc2Action(){
	    // makes disable layout
	    $this->_helper->getHelper('layout')->disableLayout();
	    $request = $this->getRequest()->getPost();
	    // gets value from ajax request
	    $akt = $request['akt'];
	    $prd = $request['prd'];
	    // set session
	    $param = new Zend_Session_Namespace('param_ntrc2');
	    $param->akt=$akt;
	    $param->prd=$prd;
	}

	function deltrc2Action(){
		$user = new Menu();
		$menu = "tracerid2/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
		    	$nim = $param[0];
			$kuis = new Kuisioner();
			$delKuis = $kuis->delNewKuis($nim);
			echo $delKuis;
		}
	}

	function showjadwaltaAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$frm=$this->_request->get('frm');
		$prd = $request['prd'.$frm];
		$per = $request['per'.$frm];
		// set session
		$param = new Zend_Session_Namespace('param_jadwalta');
		$param->prd=$prd;
		$param->per=$per;
	}

	function updjadwaltaAction(){
		$user = new Menu();
		$menu = "jadwalta/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$request = $this->getRequest()->getPost();
			$x = $request['x'];
			$kd_kul=array();
			$pj1=array();
			$pj2=array();
			$pj3=array();
			$pj4=array();
			$tgl=array();
			for($i=0;$i<$x;$i++){
				if($request['kul_'.$i]) {
					$kd_kul[]=$request['kul_'.$i];
					$pj1[]=$request['pj1_'.$i];
					$pj2[]=$request['pj2_'.$i];
					$pj3[]=$request['pj3_'.$i];
					$pj4[]=$request['pj4_'.$i];
					if($request['tgl_'.$i]==''){
						$tgl[]='1900-01-01';
					}else{
						$tgl[]=$request['tgl_'.$i];
					}
				}
			}
			$nilai = new NilaiTA();
			// validation
			if(count($kd_kul)==0){
				echo "F|Tidak ada data yang akan diubah|";
			}else{
				$msg="";
				$i=0;
				$n=1;
				foreach ($kd_kul as $dtKul){
					$upd = $nilai->updPengujiJadwalTA($pj1[$i],$pj2[$i],$pj3[$i],$pj4[$i],$tgl[$i],$dtKul);
					$i++;
					$n++;
				}
				echo "T|Data berhasil diubah : ".$i;
			}
		}
	}

	function insapprovertaAction(){
		$user = new Menu();
		$menu = "ajarta/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id_mk_kur = trim($request['id_mk_kur']);
			$hal = $request['hal'];
			$bags = $request['bags'];
			$arr_bags=explode("|",$bags);
			$bag=$arr_bags[0];
			$sys=$arr_bags[1];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($hal,1,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Perihal tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($bags,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Bagian/Sistem tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$app = new MatkulTaApp();
				$setApp =$app->setApp($id_mk_kur,$hal,$bag,$sys);
				echo $setApp;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delapprovertaAction(){
		$user = new Menu();
		$menu = "tracerid2/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
		    	$id = $param[0];
			$app = new MatkulTaApp();
			$delApp = $app->delApp($id);
			echo $delApp;
		}
	}

	function showverifytaAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$frm=$this->_request->get('frm');
		$prd = $request['prd'.$frm];
		$per = $request['per'.$frm];
		// set session
		$param = new Zend_Session_Namespace('param_verifyta');
		$param->prd=$prd;
		$param->per=$per;
	}

	function showverifyta2Action(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$frm=$this->_request->get('frm');
		$prd = $request['prd'.$frm];
		$per = $request['per'.$frm];
		// set session
		$param = new Zend_Session_Namespace('param_verifyta2');
		$param->prd=$prd;
		$param->per=$per;
	}

	function showverifyta3Action(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$frm=$this->_request->get('frm');
		$prd = $request['prd'.$frm];
		$per = $request['per'.$frm];
		// set session
		$param = new Zend_Session_Namespace('param_verifyta3');
		$param->prd=$prd;
		$param->per=$per;
	}

	function verifytaAction(){
		// $user = new Menu();
		// $menu = "verifyta/index";
		// $getMenu = $user->cekUserMenu($menu);
		// if ($getMenu=="F"){
		//	echo "F|Anda tidak memiliki akses";
		// } else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id_prp1 = trim($request['id_prp_o']);
			$stat = $request['stat_o'];
			$note = $request['ket_o'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($id_prp1,1,200)=='F'){
				$err++;
				$msg=$msg."<strong>- ID tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$prp = new PrpUjianTa();
				$updPrp =$prp->updStatusPrpApproverPemb($id_prp1,$stat,$this->namauser,$note);
				echo $updPrp;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		// }
	}

	function verifytacancelAction(){
		// $user = new Menu();
		// $menu = "verifyta/index";
		// $getMenu = $user->cekUserMenu($menu);
		// if ($getMenu=="F"){
		//	echo "F|Anda tidak memiliki akses";
		// } else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
		    	$id = $param[0];
			$prp = new PrpUjianTa();
			$updPrp = $prp->updStatusPrpApproverPemb($id,0,$this->namauser,'');
			echo $updPrp;
		// }
	}
}
