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
		Zend_Loader::loadClass('Dosen');
		Zend_Loader::loadClass('Perwalian');
		Zend_Loader::loadClass('Kelas');
		Zend_Loader::loadClass('Nilai');
		Zend_Loader::loadClass('Kuliah');
		Zend_Loader::loadClass('KuliahTA');
		Zend_Loader::loadClass('Pkrs');
		Zend_Loader::loadClass('Kbm');
		Zend_Loader::loadClass('Absensi');
		Zend_Loader::loadClass('BahanAjar');
		Zend_Loader::loadClass('Tugas');
		Zend_Loader::loadClass('TugasMhs');
		Zend_Loader::loadClass('Diskusi');
		Zend_Loader::loadClass('DiskusiMhs');
		Zend_Loader::loadClass('Rps');
		Zend_Loader::loadClass('Quiz');
		Zend_Loader::loadClass('JudulTA');
		Zend_Loader::loadClass('PrpUjianTa');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Validation');
		Zend_Loader::loadClass('PHPExcel');
		Zend_Loader::loadClass('PHPExcel_Cell_AdvancedValueBinder');
		Zend_Loader::loadClass('PHPExcel_IOFactory');
		$auth = Zend_Auth::getInstance();
		$ses_lec = new Zend_Session_Namespace('ses_lec');
		if (($auth->hasIdentity())and($ses_lec->uname)) {
			// global var
			$this->kd_dsn=Zend_Auth::getInstance()->getIdentity()->kd_dosen;
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
		$kd_dsn=$this->kd_dsn;
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
			$dosen = new Dosen();
			$updPwd = $dosen->updPwdDosen2($new1, $kd_dsn, $old);			
			echo $updPwd;
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
		$asal="D";
		$nim = $request['nim'];
		$sender=$this->_helper->string->esc_quote($request['dw']);
		$receiver=$this->_helper->string->esc_quote($request['nm_mhs']);
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
		$param = new Zend_Session_Namespace('param_dsn_kls');
		$param->prd=$prd;
		$param->per=$per;
		$param->jns=$jns;
	}
	
	function updklsAction(){
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
	
	function updnlmAction(){
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
	
	function fixnlmAction(){
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

	function fixnlmmassAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$z = $request['z'];
		$stat=1;
		$kd_kul=array();
		$nim=array();
		for($i=0;$i<$z;$i++){
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
				$msg=$msg."<br>Data ke ".($i+1)." : ".$arrMsg[1];
				$i++;
			}
			echo "T|".$msg;
		}
	}

	function unfixnlmmassAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$z = $request['z'];
		$stat=0;
		$kd_kul=array();
		$nim=array();
		for($i=0;$i<$z;$i++){
			if($request['kul2_'.$i]) {
				$kd_kul[]=$request['kul2_'.$i];
				$nim[]=$request['nim_'.$i];
			}
		}
		$nilai = new Nilai();
		// validation
		if(count($kd_kul)==0){
			echo "F|Tidak ada data nilai yang akan diubah statusnya. Cheklist dahulu mahasiswa yang akan di-unfix nilainya|";
		}else{
			$msg="";
			$i=0;
			foreach ($kd_kul as $dtKul){
				$updStatNilai = $nilai->updStatNilai($stat,$dtKul);
				$arrMsg=explode('|', $updStatNilai);
				$msg=$msg."<br>Data ke ".($i+1)." : ".$arrMsg[1];
				$i++;
			}
			echo "T|".$msg;
		}
	}

	function delnlmAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
		$kd_kuliah = $param[0];
		$nilai = new Nilai();
		$updNilai =$nilai->updNilai(0,0,0,0,0,0,0,0,0,0,$kd_kuliah);
		echo "T|Berhasil menghapus seluruh komponen nilai";
	}
	
	function uplnlmAction(){
		if (0<$_FILES["file"]["error"] ) {
	        echo "Error: ". $_FILES["file"]["error"] . "<br>";
	    }
	    else {
	    	$temp = explode(".", $_FILES["file"]["name"]);
			$newfilename = md5(round(microtime(true))) . '.' . end($temp);
			$x=rand(100000,999999);
			$path = __FILE__;
			$filePath = str_replace('controllers/AjaxController.php','temps',$path);
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
	
	function showmhswaliAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$akt = $request['akt'];
		$prd = $request['prd'];
		$stat = $request['stat'];
		// set session
		$param = new Zend_Session_Namespace('param_dsn_mhs');
		$param->akt=$akt;
		$param->prd=$prd;
		$param->stat=$stat;
	}
	
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
		$param = new Zend_Session_Namespace('param_dsn_reg');
		$param->prd=$prd;
		$param->per=$per;
		$param->akt=$akt;
	}
	
	function updkrsstatAction(){
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
			$setLog=$kuliah->setKuliahLog(3, '', '', $this->kd_dsn, $kd_kuliah, $aksi, '-');
		}
		echo $updAppKuliah;
	}
	
	function updkrsstattaAction(){
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
			$setLog=$kuliahTA->setKuliahTALog(3, '' , '', $this->kd_dsn, $kd_kuliah, $aksi, '-');
		}
		echo $updAppKuliahTA;
	}

	function updkrsmassAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$x = $request['x'];
		$approved='t';
		$kd_kul=array();
		$mk=array();
		for($i=0;$i<$x;$i++){
			if($request['kul_'.$i]) {
				$kd_kul[]=$request['kul_'.$i];
				$mk[]=$request['mk_'.$i];
			}
		}
		$kuliah = new Kuliah();
		$kuliahTa= new KuliahTA();
		// validation
		if(count($kd_kul)==0){
			echo "F|Tidak ada data KRS yang akan di-approve. Cheklist dahulu kelas yang akan diapprove|";
		}else{
			$i=0;
			$msg="";
			foreach ($kd_kul as $dtKul){
				$updKul = $kuliah->updAppKuliah($approved, $dtKul);
				$arrMsg=explode('|', $updKul);
				if($arrMsg[0]=='F'){
					$updKulTA = $kuliahTa->updAppKuliahTA($approved, $dtKul);
					$arrMsg=explode('|', $updKulTA);
					// log
					$arrUpdAppKuliahTA=explode("|", $updKulTA);
					if($arrUpdAppKuliahTA[0]!='F'){
						if($approved=='f'){
							$aksi="Disapprove";
						}else{
							$aksi="Approve";
						}
						$setLog=$kuliahTa->setKuliahTALog(3, '', '', $this->kd_dsn, $dtKul, $aksi, '-');
					}
				}else{
					// log
					$arrUpdAppKuliah=explode("|", $updKul);
					if($arrUpdAppKuliah[0]!='F'){
						if($approved=='f'){
							$aksi="Disapprove";
						}else{
							$aksi="Approve";
						}
						$setLog=$kuliah->setKuliahLog(3, '', '', $this->kd_dsn, $dtKul, $aksi, '-');
					}
				}
				$msg=$msg."<br>Mata kuliah ".$mk[$i]." : ".$arrMsg[1];					
				$i++;
			}
			echo "T|".$msg;
		}
	}

	function execpkrsAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
		$nim = $param[0];
		$pkt = $param[1];
		$ta = $param[2];
		$pkrs=new Pkrs();
		$exec = $pkrs->execPkrs($nim, $pkt, $ta);
		echo $exec;
	}
	
	function cancpkrsAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
		$nim = $param[0];
		$pkt = $param[1];
		$ta = $param[2];
		$pkrs=new Pkrs();
		$cancelPkrs = $pkrs->cancelPkrs($nim, $pkt, $ta);
		echo $cancelPkrs;
	}

	function inskbmAction(){
		// start database
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

	function delkbmAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
		$id_perkuliahan = $param[0];
		$kbm = new Kbm();
		$delKbm =$kbm->delKbm($id_perkuliahan);
		echo $delKbm;
	}

	function updkbmAction(){
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

	// absensi
	function updabsAction(){
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

	function updabsmassAction(){
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

	function insrpsAction(){
	    // disabel layout
            $this->_helper->layout->disableLayout();
            // start inserting
            $request = $this->getRequest()->getPost();
            $capaian = $this->_helper->string->esc_quote(trim($request['capaian']));
	    $mk_kur = $this->_helper->string->esc_quote(trim($request['mk_kur']));
	    $per = $this->_helper->string->esc_quote(trim($request['per']));
	    $err=0;
	    $msg="";
	    $vd = new Validation();
	    if($vd->validasiLength($capaian,1,500)=='F'){
		$err++;
		$msg=$msg."<strong>- Capaian pembelajaran tidak boleh kosong maksimal 500 karakter</strong><br>";
	    }
	    if($err==0){
		    $rps=new Rps();
                    $setRps=$rps->setRps($mk_kur,$per,$capaian);
                    echo $setRps;
            }else{
                echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
            }
    	}

	function delrpsAction(){
            // disabel layout
       	    $this->_helper->layout->disableLayout();
            // set database
            $request = $this->getRequest()->getPost();
            // gets value from ajax request
            $param = $request['param'];
            $id = $param[0];
            $rps = new Rps();
            $delRps=$rps->delRps($id);
            echo $delRps;
    }

    function updrpsAction(){
	    // disabel layout
            $this->_helper->layout->disableLayout();
            // start inserting
            $request = $this->getRequest()->getPost();
            $capaian = $this->_helper->string->esc_quote(trim($request['capaian']));
	    $id = $this->_helper->string->esc_quote(trim($request['id']));
	    $err=0;
	    $msg="";
	    $vd = new Validation();
	    if($vd->validasiLength($capaian,1,200)=='F'){
		$err++;
		$msg=$msg."<strong>- Capaian pembelajaran tidak boleh kosong maksimal 200 karakter</strong><br>";
	    }
	    $per="";
	    if($err==0){
		    $rps=new Rps();
                    $updRps=$rps->updRps($id,$per,$capaian);
                    echo $updRps;
            }else{
                echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
            }
    	}

	function insrpsdetilAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$id_rps = $request['id_rps'];
		$mg = intval($request['mg']);
		$kmp = $this->_helper->string->esc_quote(trim($request['kmp']));
		$bhn = $this->_helper->string->esc_quote(trim($request['bhn']));
		$bnt = $this->_helper->string->esc_quote(trim($request['bnt']));
		$krt = $this->_helper->string->esc_quote(trim($request['krt']));
		$bbt = floatval($request['bbt']);
		$ind = $this->_helper->string->esc_quote(trim($request['ind']));
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($id_rps,1,100)=='F'){
			$err++;
			$msg=$msg."<strong>- Data RPS tidak boleh kosong</strong><br>";
		}
		if ($mg<=0){
			$err++;
			$msg=$msg."<strong>- Minggu tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($kmp,1,500)=='F'){
			$err++;
			$msg=$msg."<strong>- Kemampuan akhir tidak boleh kosong dan tidak boleh lebih dari 500 karakter </strong><br>";
		}
		if($vd->validasiLength($bhn,1,500)=='F'){
			$err++;
			$msg=$msg."<strong>- Bahan ajar tidak boleh kosong dan tidak boleh lebih dari 500 karakter </strong><br>";
		}
		if($vd->validasiLength($bnt,1,500)=='F'){
			$err++;
			$msg=$msg."<strong>- Metode pembelajaran tidak boleh kosong dan tidak boleh lebih dari 500 karakter </strong><br>";
		}
		if($vd->validasiLength($krt,1,500)=='F'){
			$err++;
			$msg=$msg."<strong>- Kriteria penilaian tidak boleh kosong dan tidak boleh lebih dari 500 karakter </strong><br>";
		}
		if($vd->validasiLength($ind,1,500)=='F'){
			$err++;
			$msg=$msg."<strong>- Indikator tidak boleh kosong dan tidak boleh lebih dari 500 karakter </strong><br>";
		}
		if ($bbt<=0){
			$err++;
			$msg=$msg."<strong>- Bobot Penilaian tidak boleh kosong</strong><br>";
		}
		if($err==0){
			// set database
			$rps = new Rps();
			$setRpsDtl = $rps->setRpsDtl($id_rps,$mg,$kmp,$bhn,$bnt,$krt,$bbt,$ind);
			echo $setRpsDtl;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}

	function delrpsdetilAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
		$id_rps_detil = $param[0];
		$rps = new Rps();
		$delRpsDtl = $rps->delRpsDtl($id_rps_detil);
		echo $delRpsDtl;
	}

	// LMS -----------------------------------------------------------------------
	function showklslmsAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$frm=$this->_request->get('frm');
		$prd = $request['prd'.$frm];
		$per = $request['per'.$frm];
		$jns = $request['jns'.$frm];
		// set session
		$param = new Zend_Session_Namespace('param_dsn_kls_lms');
		$param->prd=$prd;
		$param->per=$per;
		$param->jns=$jns;
	}

	function insbahanAction(){
	    // disabel layout
            $this->_helper->layout->disableLayout();
            // start inserting
            $request = $this->getRequest()->getPost();
            $kd_kls = $this->_helper->string->esc_quote(trim($request['kd_kelas']));
	    $rps = $this->_helper->string->esc_quote(trim($request['rps']));
            $jdl = $this->_helper->string->esc_quote(trim($request['jdl']));
	    $ket = $this->_helper->string->esc_quote(trim($request['ket']));
	    $link = $this->_helper->string->esc_quote(trim($request['link']));
            $kd_dsn=$this->kd_dsn;
	    $err=0;
	    $msg="";
	    $vd = new Validation();
	    if($vd->validasiLength($rps,1,100)=='F'){
		$err++;
		$msg=$msg."<strong>- Minggu tidak boleh kosong</strong><br>";
	    }
	    if($vd->validasiLength($jdl,1,100)=='F'){
		$err++;
		$msg=$msg."<strong>- Judul tidak boleh kosong maksimal 100 karakter</strong><br>";
	    }
	    if($vd->validasiLength($ket,1,100)=='F'){
		$err++;
		$msg=$msg."<strong>- Keterangan tidak boleh kosong maksimal 100 karakter</strong><br>";
	    }
            if($err==0){
                // start uploading
                if ((0<$_FILES["filez1"]["error"])) {
                    $msg= "F|Error: ". $_FILES["filez1"]["error"];
                    echo $msg;
                }else {
		    $bhn=new BahanAjar();
                    $setBhn=$bhn->setBahanAjar($kd_kls, $jdl, $_FILES["filez1"]["name"], $ket, $link, $kd_dsn,$rps);
                    $arrSetBhn=explode("|", $setBhn);
                    if($arrSetBhn[0]!='F'){
                        $arrName = explode(".", $_FILES["filez1"]["name"]);
                        $newfilename=$arrSetBhn[0].".".end($arrName);
                        $file_url=Zend_Registry::get('FILE_URL');
                        $target_dir=$file_url.'/materi';
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
                                $msg=$arrSetBhn[0]."|Data berhasil disimpan . <br>File berhasil diupload";
                                echo $msg;
                            }else{
                                $delBhn=$bhn->delBahanAjar($arrSetBhn[0]);
                                $msg= "F|Maaf terjadi error saat upload, silakan coba lagi.". $_FILES["filez1"]["error"];
                                echo $msg;
                            }
                        }else{
                            $delBhn=$bhn->delBahanAjar($arrSetBhn[0]);
                            $msg= "F|Maaf terjadi error saat upload : <br>".$msg;
                            echo $msg;
                        }
                    }else{
                        echo $setBhn;
                    }
                }
            }else{
                echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
            }
    }

    function delbahanAction(){
            // disabel layout
       	    $this->_helper->layout->disableLayout();
            // set database
            $request = $this->getRequest()->getPost();
            // gets value from ajax request
            $param = $request['param'];
            $id = $param[0];
            $bhn = new BahanAjar();
            $getBhn=$bhn->getBahanAjarById($id);
            if($getBhn){
                $id_file="";
                $file_url=Zend_Registry::get('FILE_URL');
                $target_dir = $file_url."/materi";
                foreach ($getBhn as $dt){
                    $arrFile=explode(".", $dt['nm_file']);
                    $ext=end($arrFile);
                    $id_file=$dt['id_bahan_ajar'];
                    $target_file = str_replace("'", "", $target_dir ."/". $id_file.".".$ext);
                    unlink($target_file);
                }
            }
            $delBhn=$bhn->delBahanAjar($id);
            echo $delBhn;
    }

    function instugasAction(){
	    // disabel layout
            $this->_helper->layout->disableLayout();
            // start inserting
            $request = $this->getRequest()->getPost();
            $kd_pkt = $this->_helper->string->esc_quote(trim($request['kd_paket_kelas']));
	    $rps = $this->_helper->string->esc_quote(trim($request['rps']));
	    $prm = $this->_helper->string->esc_quote(trim($request['prm']));
            $jdl = $this->_helper->string->esc_quote(trim($request['jdl']));
	    $knt = $this->_helper->string->esc_quote(trim($request['knt']));
	    $tgl1 = $this->_helper->string->esc_quote(trim($request['tgl1']));
	    $tgl2 = $this->_helper->string->esc_quote(trim($request['tgl2']));
	    $start = $this->_helper->string->esc_quote(trim($request['start']));
	    $end = $this->_helper->string->esc_quote(trim($request['end']));
	    $link = $this->_helper->string->esc_quote(trim($request['link']));
	    $id_kel = $this->_helper->string->esc_quote(trim($request['id_kel']));
            $kd_dsn=$this->kd_dsn;
	    $err=0;
	    $msg="";
	    $vd = new Validation();
	    if($vd->validasiLength($rps,1,100)=='F'){
		$err++;
		$msg=$msg."<strong>- Minggu tidak boleh kosong</strong><br>";
	    }
	    if($vd->validasiLength($jdl,1,100)=='F'){
		$err++;
		$msg=$msg."<strong>- Judul tidak boleh kosong maksimal 100 karakter</strong><br>";
	    }
	    if($vd->validasiLength($knt,1,100)=='F'){
		$err++;
		$msg=$msg."<strong>- Instruksi tugas tidak boleh kosong maksimal 100 karakter</strong><br>";
	    }
	    if($vd->validasiLength($tgl1,1,100)=='F'){
		$err++;
		$msg=$msg."<strong>- Tanggal terbit tugas tidak boleh kosong</strong><br>";
	    }
	    if($vd->validasiLength($tgl2,1,100)=='F'){
		$err++;
		$msg=$msg."<strong>- Deadline tidak boleh kosong</strong><br>";
	    }
            if($err==0){
                // start uploading
                if ((0<$_FILES["filez1"]["error"])) {
                    $msg= "F|Error: ". $_FILES["filez1"]["error"];
                    echo $msg;
                }else {
		    $tgs=new Tugas();
                    $setTgs=$tgs->setTugas($kd_pkt,$jdl,$knt,$tgl1.' '.$start,$tgl2,$prm,$kd_dsn,$rps,$_FILES["filez1"]["name"],$link,$id_kel);
                    $arrSetTgs=explode("|", $setTgs);
                    if($arrSetTgs[0]!='F'){
                        $arrName = explode(".", $_FILES["filez1"]["name"]);
                        $newfilename=$arrSetTgs[0].".".end($arrName);
                        $file_url=Zend_Registry::get('FILE_URL');
                        $target_dir=$file_url.'/tugas';
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
                                $delTugas=$tgs->delTugas($arrSetTgs[0]);
                                $msg= "F|Maaf terjadi error saat upload, silakan coba lagi.". $_FILES["filez1"]["error"];
                                echo $msg;
                            }
                        }else{
                            $delTugas=$tgs->delTugas($arrSetTgs[0]);
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

    function deltugasAction(){
            // disabel layout
       	    $this->_helper->layout->disableLayout();
            // set database
            $request = $this->getRequest()->getPost();
            // gets value from ajax request
            $param = $request['param'];
            $id = $param[0];
            $tgs = new Tugas();
            $getTgs=$tgs->getTugasById($id);
            if($getTgs){
                $id_file="";
                $file_url=Zend_Registry::get('FILE_URL');
                $target_dir = $file_url."/tugas";
                foreach ($getTgs as $dt){
                    $arrFile=explode(".", $dt['nm_file']);
                    $ext=end($arrFile);
                    $id_file=$dt['id_tugas'];
                    $target_file = str_replace("'", "", $target_dir ."/". $id_file.".".$ext);
                    unlink($target_file);
                }
            }
            $delTgs=$tgs->delTugas($id);
            echo $delTgs;
    }

    	function updnltugasAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$n = $request['n'];
		$id=array();
		$nilai=array();
		for($i=0;$i<$n;$i++){
			if($request['id_tgs_mhs_'.$i]) {
				$id[]=$request['id_tgs_mhs_'.$i];
				$nilai[]=$request['nl_'.$i];
			}
		}
		$tugasMhs = new TugasMhs();
		// validation
		if(count($id)==0){
			echo "F|Tidak ada data nilai yang akan diubah.|";
		}else{
			$msg="";
			$x=1;
			for ($i=0;$i<$n;$i++){
				$updNl = $tugasMhs->updNlTugasMhs($id[$i],$nilai[$i]);
				$arrMsg=explode('|', $updNl );
				$msg=$msg."<br>Baris ke ".$x." : ".$arrMsg[1];
				$x++;
			}
			echo "T|".$msg;
		}
	}

	function uplnltugasAction(){
		if (0<$_FILES["file"]["error"] ) {
	        echo "Error: ". $_FILES["file"]["error"] . "<br>";
	    }
	    else {
	    	$temp = explode(".", $_FILES["file"]["name"]);
			$newfilename = md5(round(microtime(true))) . '.' . end($temp);
			$x=rand(100000,999999);
			$path = __FILE__;
			$filePath = str_replace('controllers/AjaxController.php','temps',$path);
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
					$tugas = new TugasMhs();
					$arr_data=array();
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
						$n_error=0;
						foreach ($arr_data as $key => $value) {
							$nim = $value['B'];
							$nilai=floatval($value['E']);
							$id_tugas_mhs = $value['R'];
							$getTugas = $tugas->getTugasMhsById($id_tugas_mhs);
							if(count($getTugas)==0){
								$msg=$msg."Data mahasiswa baris ke ".$n." tidak valid! Gunakan template yang sudah diunduh<br>";
								$n_error++;
							}
							if($nilai>100){
								$msg=$msg."Baris ke-".$n.": Ada nilai yang lebih dari 100<br>";
								$n_error++;
							}
							$n++;
						}
						$n=1;
						if($n_error==0){
							$tugasMhs = new TugasMhs();
							foreach ($arr_data as $key => $value) {
								$nim = $value['B'];
								$nilai=floatval($value['E']);
								$id = $value['R'];
								$updNilai =$tugasMhs->updNlTugasMhs($id,$nilai);
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

	function insdiskusiAction(){
	    // disabel layout
            $this->_helper->layout->disableLayout();
            // start inserting
            $request = $this->getRequest()->getPost();
            $kd_pkt = $this->_helper->string->esc_quote(trim($request['kd_paket_kelas']));
	    $rps = $this->_helper->string->esc_quote(trim($request['rps']));
	    $prm = $this->_helper->string->esc_quote(trim($request['prm']));
            $jdl = $this->_helper->string->esc_quote(trim($request['jdl']));
	    $knt = $this->_helper->string->esc_quote(trim($request['knt']));
	    $tgl1 = $this->_helper->string->esc_quote(trim($request['tgl1']));
	    $tgl2 = $this->_helper->string->esc_quote(trim($request['tgl2']));
	    $id_kel = $this->_helper->string->esc_quote(trim($request['id_kel']));
            $kd_dsn=$this->kd_dsn;
	    $err=0;
	    $msg="";
	    $vd = new Validation();
	    if($vd->validasiLength($rps,1,100)=='F'){
		$err++;
		$msg=$msg."<strong>- Minggu tidak boleh kosong</strong><br>";
	    }
	    if($vd->validasiLength($jdl,1,100)=='F'){
		$err++;
		$msg=$msg."<strong>- Judul tidak boleh kosong maksimal 100 karakter</strong><br>";
	    }
	    if($vd->validasiLength($knt,1,500)=='F'){
		$err++;
		$msg=$msg."<strong>- Materi diskusi tidak boleh kosong maksimal 500 karakter</strong><br>";
	    }
	    if($vd->validasiLength($tgl1,1,100)=='F'){
		$err++;
		$msg=$msg."<strong>- Tanggal terbit tidak boleh kosong</strong><br>";
	    }
	    if($vd->validasiLength($tgl2,1,100)=='F'){
		$err++;
		$msg=$msg."<strong>- Kadaluwarsa tidak boleh kosong</strong><br>";
	    }
            if($err==0){
		    $disk=new Diskusi();
                    $setDisk=$disk->setDiskusi($kd_pkt,$jdl,$knt,$tgl1,$tgl2,$prm,$kd_dsn,$rps,$id_kel);
                    echo $setDisk;
            }else{
                echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
            }
    }


    function deldiskusiAction(){
            // disabel layout
       	    $this->_helper->layout->disableLayout();
            // set database
            $request = $this->getRequest()->getPost();
            // gets value from ajax request
            $param = $request['param'];
            $id = $param[0];
            $dsk = new Diskusi();
            $delDsk=$dsk->delDiskusi($id);
            echo $delDsk;
    }

    function updnldiskusiAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$n = $request['n'];
		$id=array();
		$nilai=array();
		for($i=0;$i<$n;$i++){
			if($request['id_disk_mhs_'.$i]) {
				$id[]=$request['id_disk_mhs_'.$i];
				$nilai[]=$request['nl_'.$i];
			}
		}
		$diskMhs = new DiskusiMhs();
		// validation
		if(count($id)==0){
			echo "F|Tidak ada data nilai yang akan diubah.|";
		}else{
			$msg="";
			$x=1;
			for ($i=0;$i<$n;$i++){
				$updNl = $diskMhs->updNlDiskusiMhs($id[$i],$nilai[$i]);
				$arrMsg=explode('|', $updNl );
				$msg=$msg."<br>Baris ke ".$x." : ".$arrMsg[1];
				$x++;
			}
			echo "T|".$msg;
		}
	}

	function insquiz0Action(){
	    // disabel layout
            $this->_helper->layout->disableLayout();
            // start inserting
            $request = $this->getRequest()->getPost();
            $kd_pkt = $this->_helper->string->esc_quote(trim($request['kd_paket_kelas']));
	    $rps = $this->_helper->string->esc_quote(trim($request['rps']));
	    $prm = $this->_helper->string->esc_quote(trim($request['prm']));
            $nm = $this->_helper->string->esc_quote(trim($request['nm']));
	    $tgl = $this->_helper->string->esc_quote(trim($request['tgl']));
	    $time1 = $this->_helper->string->esc_quote(trim($request['time1']));
	    $time2 = $this->_helper->string->esc_quote(trim($request['time2']));
	    $id_kel = $this->_helper->string->esc_quote(trim($request['id_kel']));
            $kd_dsn=$this->kd_dsn;
	    $err=0;
	    $msg="";
	    $vd = new Validation();
	    if($vd->validasiLength($rps,1,100)=='F'){
		$err++;
		$msg=$msg."<strong>- Minggu tidak boleh kosong</strong><br>";
	    }
	    if($vd->validasiLength($nm,1,100)=='F'){
		$err++;
		$msg=$msg."<strong>- Nama Quiz tidak boleh kosong maksimal 100 karakter</strong><br>";
	    }
	    if($vd->validasiLength($tgl,1,100)=='F'){
		$err++;
		$msg=$msg."<strong>- Tanggal quiz tidak boleh kosong</strong><br>";
	    }
	    if(($vd->validasiTime($time1,1,100)=='F')or($vd->validasiTime($time2)=='F')){
		$err++;
		$msg=$msg."<strong>- waktu quiz tidak boleh kosong dan harus berformat hh:mm</strong><br>";
	    }
	    if(strtotime($time2)-strtotime($time1)<5){
		$err++;
		$msg=$msg."<strong>- waktu quiz kurang dari 5 menit, set waktu selesai lebih besar minimal 5 menit daripada waktu mulai</strong><br>";	
	    }
            if($err==0){
		    $quiz=new Quiz();
                    $setQuiz0=$quiz->setQuiz0($kd_pkt,$kel,$nm,$tgl,$time1,$time2,$kd_dsn,$rps,$prm);
                    echo $setQuiz0;
            }else{
                echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
            }
    }


    function delquiz0Action(){
            // disabel layout
       	    $this->_helper->layout->disableLayout();
            // set database
            $request = $this->getRequest()->getPost();
            // gets value from ajax request
            $param = $request['param'];
            $id = $param[0];
            $quiz = new Quiz();
            $delQuiz=$quiz->delQuiz0($id);
            echo $delQuiz;
    }

    function insquiz1Action(){
	    // disabel layout
            $this->_helper->layout->disableLayout();
            // start inserting
            $request = $this->getRequest()->getPost();
            $id_quiz0 = $this->_helper->string->esc_quote(trim($request['id_quiz0']));
	    $quest = $this->_helper->string->esc_quote(trim($request['quest']));
	    $img="";
	    $ord = intval($request['ord']);
            $err=0;
	    $msg="";
	    $vd = new Validation();
	    if($vd->validasiLength($quest,1,10000)=='F'){
		$err++;
		$msg=$msg."<strong>- Soal tidak boleh kosong</strong><br>";
	    }
	    if($err==0){
		    $quiz=new Quiz();
                    $setQuiz1=$quiz->setQuiz1($id_quiz0,$quest,$img,$ord);
                    echo $setQuiz1;
            }else{
                echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
            }
    }


    function delquiz1Action(){
            // disabel layout
       	    $this->_helper->layout->disableLayout();
            // set database
            $request = $this->getRequest()->getPost();
            // gets value from ajax request
            $param = $request['param'];
            $id = $param[0];
            $quiz = new Quiz();
            $delQuiz=$quiz->delQuiz1($id);
            echo $delQuiz;
    }

    function updjawabanquiz1Action(){
            // disabel layout
       	    $this->_helper->layout->disableLayout();
            // set database
            $request = $this->getRequest()->getPost();
            // gets value from ajax request
            $param = $request['param'];
            $id = $param[0];
	    $jwb = $param[1];
            $quiz = new Quiz();
            $updQuiz=$quiz->updJawabanQuiz1($id,$jwb);
            echo $updQuiz;
    }

    function insquiz2Action(){
	    // disabel layout
            $this->_helper->layout->disableLayout();
            // start inserting
            $request = $this->getRequest()->getPost();
            $id_quiz1 = $this->_helper->string->esc_quote(trim($request['id_quiz1']));
	    $choice = $this->_helper->string->esc_quote(trim($request['choice']));
	    $img="";
	    $ord = intval($request['ord']);
            $err=0;
	    $msg="";
	    $vd = new Validation();
	    if($vd->validasiLength($choice,1,10000)=='F'){
		$err++;
		$msg=$msg."<strong>- Jawaban tidak boleh kosong</strong><br>";
	    }
	    if($err==0){
		    $quiz=new Quiz();
                    $setQuiz2=$quiz->setQuiz2($id_quiz1,$choice,$img,$ord);
                    echo $setQuiz2;
            }else{
                echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
            }
    }


    function delquiz2Action(){
            // disabel layout
       	    $this->_helper->layout->disableLayout();
            // set database
            $request = $this->getRequest()->getPost();
            // gets value from ajax request
            $param = $request['param'];
            $id = $param[0];
            $quiz = new Quiz();
            $delQuiz=$quiz->delQuiz2($id);
            echo $delQuiz;
    }

    // judul ta
    function showjudultaAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$frm=$this->_request->get('frm');
		$prd = $request['prd'.$frm];
		$per = $request['per'.$frm];
		// set session
		$param = new Zend_Session_Namespace('param_dsn_judulta');
		$param->prd=$prd;
		$param->per=$per;
	}
    
    function appjudultaAction(){
            // disabel layout
       	    $this->_helper->layout->disableLayout();
            // set database
            $request = $this->getRequest()->getPost();
            // gets value from ajax request
            $param = $request['param'];
            $id = $param[0];
	    $stat = $param[1];
	    $kd_dsn=$this->kd_dsn;
            $judulTa = new JudulTA();
            $updStatJudulTA=$judulTa->updStatJudulTA($id,$stat,$kd_dsn);
            echo $updStatJudulTA;
    }

    function appprpujiantaAction(){
            // disabel layout
       	    $this->_helper->layout->disableLayout();
            // set database
            $request = $this->getRequest()->getPost();
            // gets value from ajax request
            $param = $request['param'];
            $id = $param[0];
	    $num = $param[1];
	    $kd_dsn=$this->kd_dsn;
            $prp = new PrpUjianTa();
            $updStat=$prp->appPrpPemb($id,$num);
            echo $updStat;
    }
}