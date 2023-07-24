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
		Zend_Loader::loadClass('Dosen');
		Zend_Loader::loadClass('PendDosen');
		Zend_Loader::loadClass('Kurikulum');
		Zend_Loader::loadClass('Matkul');
		Zend_Loader::loadClass('MatkulKurikulum');
		Zend_Loader::loadClass('Ajar');
		Zend_Loader::loadClass('Ekuivalensi');
		Zend_Loader::loadClass('Konversi');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Validation');
		Zend_Loader::loadClass('PHPExcel');
		Zend_Loader::loadClass('PHPExcel_Cell_AdvancedValueBinder');
		Zend_Loader::loadClass('PHPExcel_IOFactory');
		$auth = Zend_Auth::getInstance();
		$ses_ac = new Zend_Session_Namespace('ses_ac');
		if (($auth->hasIdentity())and($ses_ac->uname)) {

		}else{
			echo "F|Sesi anda sudah habis. Silakan login ulang!|";
		}
		// disabel layout
		$this->_helper->layout->disableLayout();
	}

	private function esc_quote($param){
		$param = addslashes($param);
		$param = str_replace("\'","''",$param);
		$param = str_replace('\"','"',$param);
		//$param = addslashes($param);
		return $param;
	}
	// Mahasiswa
	function showmhsAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$akt = $request['akt'];
		$prd = $request['prd'];
		$stat = $request['stat'];
		// set session
		$param = new Zend_Session_Namespace('param_mhs');
		$param->akt=$akt;
		$param->prd=$prd;
		$param->stat=$stat;
	}

	function insmhsAction(){
		$user = new Menu();
		$menu = "mahasiswa/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$nm = $this->esc_quote(trim($request['nm']));
			$tmplhr = $this->esc_quote(trim($request['tmplhr']));
			$tgllhr = $request['tgllhr'];
			$jk = trim($request['jk']);
			$agm = $request['agm'];
			$kwn = $request['kwn'];
			$nik = $request['nik'];
			$kota = $this->esc_quote(trim($request['kota']));
			$ayah = $this->esc_quote(trim($request['ayah']));
			$ibu = $this->esc_quote(trim($request['ibu']));
			$job_a = $request['job_a'];
			$job_i = $request['job_i'];
			$nim = trim($request['nim']);
			$akt = $request['akt'];
			$dw = $request['dw'];
			$prd = $request['prd'];
			$tglmsk = $request['tglmsk'];
			$stat_msk = $request['stat_msk'];
			$as_sk = $this->esc_quote(trim($request['as_sk']));
			$as_prd = $this->esc_quote(trim($request['as_prd']));
			$sks = $request['sks'];
			$em_k = trim($request['em_k']);
			$em_l = trim($request['em_l']);
			$hp= $this->esc_quote(trim($request['hp']));
			$idwil =trim($request['id_wil']);
			$nisn= $this->esc_quote(trim($request['nisn']));
			$npwp= $this->esc_quote(trim($request['npwp']));
			$jalan= $this->esc_quote(trim($request['jalan']));
			$dusun= $this->esc_quote(trim($request['dsn']));
			$rt= $this->esc_quote(trim($request['rt']));
			$rw= $this->esc_quote(trim($request['rw']));
			$kel= $this->esc_quote(trim($request['kel']));
			$zip= $this->esc_quote(trim($request['zip']));
			$kps= $this->esc_quote(trim($request['kps']));
			$trans= $this->esc_quote(trim($request['trans']));
			$tinggal= $this->esc_quote(trim($request['tinggal']));
			// $almt= $this->esc_quote(trim($request['almt']));
			$almt=$jalan.". RT : ".$rt." RW : ".$rw." ".$kel;
			if(!$idwil){
				$idwil="999999";
			}
			$stat_mhs="A";
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiLength($nm,3,50)=='F')or($vd->validasiRegexNama($nm)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama minimal 3 huruf dan tidak boleh mengandung angka dan simbol</strong><br>";
			}
			if(($vd->validasiLength($tmplhr,1,50)=='F')or($vd->validasiRegexNama($tmplhr)=='F')){
				$err++;
				$msg=$msg."<strong>- Tempat lahir tidak boleh kosong dan tidak boleh mengandung angka dan simbol</strong><br>";
			}
			if($vd->validasiDate($tgllhr)=='F'){
				$err++;
				$msg=$msg."<strong>- Tanggal lahir tidak boleh kosong dan format harus benar</strong><br>";
			}
			if($vd->validasiLength($jk,1,1)=='F'){
				$err++;
				$msg=$msg."<strong>- Jenis kelamin tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($agm,1,5)=='F'){
				$err++;
				$msg=$msg."<strong>- Agama tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($kwn,1,5)=='F'){
				$err++;
				$msg=$msg."<strong>- Kewarganegaraan tidak boleh kosong</strong><br>";
			}
			if ($vd->validasiRegex($nik,"/^[0-9]{2}+.+[0-9]{4}+.+[0-9]{6}+.+[0-9]{4}$/")=='F'){
				$err++;
				$msg=$msg."<strong>- NIK tidak benar, isikan 0 di setiap isian bila tidak ada NIK</strong><br>";
			}
			if($vd->validasiLength($almt,5,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Alamat di antara 5-200 karakter </strong><br>";
			}
			if($vd->validasiLength($kota,2,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Kota di antara 2-40 karakter </strong><br>";
			}
			if(($vd->validasiLength($ayah,3,50)=='F')or($vd->validasiRegexNama($ayah)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama ayah minimal 3 huruf dan tidak boleh mengandung angka dan simbol</strong><br>";
			}
			if(($vd->validasiLength($ibu,3,50)=='F')or($vd->validasiRegexNama($ibu)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama ibu minimal 3 huruf dan tidak boleh mengandung angka dan simbol</strong><br>";
			}
			if($vd->validasiLength($dw,1,30)=='F'){
				$err++;
				$msg=$msg."<strong>- Dosen Wali tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($akt,1,4)=='F'){
				$err++;
				$msg=$msg."<strong>- Angkatan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($prd,1,10)=='F'){
				$err++;
				$msg=$msg."<strong>- Program studi tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($nim,4,20)=='F'){
				$err++;
				$msg=$msg."<strong>- NIM harus di antara 4-20 karakter</strong><br>";
			}
			if($vd->validasiDate($tglmsk)=='F'){
				$err++;
				$msg=$msg."<strong>- Tanggal masuk tidak boleh kosong dan format harus benar</strong><br>";
			}
			if ($vd->validasiLength($hp,1,50)=='F'){
				$err++;
				$msg=$msg."<strong>- Kontak tidak boleh kosong, maksimal 50 karakter</strong><br>";
			}
			if(($vd->validasiEmail($em_k)=='F')or($vd->validasiEmail($em_l)=='F')){
				$err++;
				$msg=$msg."<strong>- Format email tidak benar</strong><br>";
			}
			if(($vd->validasiLength($em_k,5,100)=='F')or($vd->validasiLength($em_l,5,100)=='F')){
				$err++;
				$msg=$msg."<strong>- Email di antara 5-100 karakter</strong><br>";
			}
			if($vd->validasiLength($nisn,1,30)=='F'){
				$err++;
				$msg=$msg."<strong>- NISN tidak boleh kosong, maksimal 30 karakter </strong><br>";
			}
			if($vd->validasiLength($jalan,5,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Jalan di antara 5-100 karakter </strong><br>";
			}
			if($vd->validasiLength($kel,5,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Kelurahan di antara 5-100 karakter </strong><br>";
			}
			if($err==0){
				// set database
				$mahasiswa = new Mahasiswa();
				$setMhs = $mahasiswa->setMahasiswa2($nm,$jk,$tmplhr,$tgllhr,$agm,$kwn,$almt,$kota,$idwil,$ayah,$ibu,$job_a,$job_i,$nik,$em_k,$em_l,$hp,$nim,$tglmsk,$stat_msk,$prd,$akt,$sks,$as_sk,$as_prd,$stat_mhs,$dw,$nisn,$npwp,$jalan,$dusun,$rt,$rw,$kel,$zip,$kps,$trans,$tinggal);
				echo $setMhs;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delmhsAction(){
		$user = new Menu();
		$menu = "mahasiswa/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    		$id = $param[0];
	    		// unlink foto
	    		unlink('../public/file/mhs/foto/'.$id.'.jpg');
			$mahasiswa = new Mahasiswa();
			$delMhs = $mahasiswa->delMahasiswa($id);
			echo $delMhs;
		}
	}

	function updmhsAction(){
		$user = new Menu();
		$menu = "mahasiswa/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start updating
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$nm = $this->esc_quote(trim($request['nm']));
			$tmplhr = $this->esc_quote(trim($request['tmplhr']));
			$tgllhr = $request['tgllhr'];
			$jk = trim($request['jk']);
			$agm = $request['agm'];
			$kwn = $request['kwn'];
			$nik = $request['nik'];
			$kota = $this->esc_quote(trim($request['kota']));
			$ayah = $this->esc_quote(trim($request['ayah']));
			$ibu = $this->esc_quote(trim($request['ibu']));
			$job_a = $request['job_a'];
			$job_i = $request['job_i'];
			$nim = trim($request['nim']);
			$akt = $request['akt'];
			$dw = $request['dw'];
			$prd = $request['prd'];
			$tglmsk = $request['tglmsk'];
			$stat_msk = $request['stat_msk'];
			$as_sk = $this->esc_quote(trim($request['as_sk']));
			$as_prd = $this->esc_quote(trim($request['as_prd']));
			$sks = $request['sks'];
			$em_k = trim($request['em_k']);
			$em_l = trim($request['em_l']);
			$hp= $this->esc_quote(trim($request['hp']));
			$idwil =trim($request['id_wil']);
			if(!$idwil){
				$idwil="999999";
			}
			$stat_mhs="A";
			$oldNim = trim($request['oldnim']);
			$idMhs = trim($request['idmhs']);
			$nisn= $this->esc_quote(trim($request['nisn']));
			$npwp= $this->esc_quote(trim($request['npwp']));
			$jalan= $this->esc_quote(trim($request['jalan']));
			$dusun= $this->esc_quote(trim($request['dsn']));
			$rt= $this->esc_quote(trim($request['rt']));
			$rw= $this->esc_quote(trim($request['rw']));
			$kel= $this->esc_quote(trim($request['kel']));
			$zip= $this->esc_quote(trim($request['zip']));
			$kps= $this->esc_quote(trim($request['kps']));
			$trans= $this->esc_quote(trim($request['trans']));
			$tinggal= $this->esc_quote(trim($request['tinggal']));
			// $almt= $this->esc_quote(trim($request['almt']));
			$almt=$jalan.". RT : ".$rt." RW : ".$rw." ".$kel;
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiLength($nm,3,50)=='F')or($vd->validasiRegexNama($nm)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama minimal 3 huruf dan tidak boleh mengandung angka dan simbol</strong><br>";
			}
			if(($vd->validasiLength($tmplhr,1,50)=='F')or($vd->validasiRegexNama($tmplhr)=='F')){
				$err++;
				$msg=$msg."<strong>- Tempat lahir tidak boleh kosong dan tidak boleh mengandung angka dan simbol</strong><br>";
			}
			if($vd->validasiDate($tgllhr)=='F'){
				$err++;
				$msg=$msg."<strong>- Tanggal lahir tidak boleh kosong dan format harus benar</strong><br>";
			}
			if($vd->validasiLength($jk,1,1)=='F'){
				$err++;
				$msg=$msg."<strong>- Jenis kelamin tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($agm,1,5)=='F'){
				$err++;
				$msg=$msg."<strong>- Agama tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($kwn,1,5)=='F'){
				$err++;
				$msg=$msg."<strong>- Kewarganegaraan tidak boleh kosong</strong><br>";
			}
			if ($vd->validasiRegex($nik,"/^[0-9]{2}+.+[0-9]{4}+.+[0-9]{6}+.+[0-9]{4}$/")=='F'){
				$err++;
				$msg=$msg."<strong>- NIK tidak benar, isikan 0 di setiap isian bila tidak ada NIK</strong><br>";
			}
			if($vd->validasiLength($almt,5,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Alamat di antara 5-200 karakter </strong><br>";
			}
			if($vd->validasiLength($kota,2,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Kota di antara 2-40 karakter </strong><br>";
			}
			if(($vd->validasiLength($ayah,3,50)=='F')or($vd->validasiRegexNama($ayah)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama ayah minimal 3 huruf dan tidak boleh mengandung angka dan simbol</strong><br>";
			}
			if(($vd->validasiLength($ibu,3,50)=='F')or($vd->validasiRegexNama($ibu)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama ibu minimal 3 huruf dan tidak boleh mengandung angka dan simbol</strong><br>";
			}
			if($vd->validasiLength($dw,1,30)=='F'){
				$err++;
				$msg=$msg."<strong>- Dosen Wali tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($akt,1,4)=='F'){
				$err++;
				$msg=$msg."<strong>- Angkatan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($prd,1,10)=='F'){
				$err++;
				$msg=$msg."<strong>- Program studi tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($nim,4,20)=='F'){
				$err++;
				$msg=$msg."<strong>- NIM harus di antara 4-20 karakter</strong><br>";
			}
			if($vd->validasiDate($tglmsk)=='F'){
				$err++;
				$msg=$msg."<strong>- Tanggal masuk tidak boleh kosong dan format harus benar</strong><br>";
			}
			if ($vd->validasiLength($hp,1,50)=='F'){
				$err++;
				$msg=$msg."<strong>- Kontak tidak boleh kosong, maksimal 50 karakter</strong><br>";
			}
			if(($vd->validasiEmail($em_k)=='F')or($vd->validasiEmail($em_l)=='F')){
				$err++;
				$msg=$msg."<strong>- Format email tidak benar</strong><br>";
			}
			if(($vd->validasiLength($em_k,5,100)=='F')or($vd->validasiLength($em_l,5,100)=='F')){
				$err++;
				$msg=$msg."<strong>- Email di antara 5-100 karakter</strong><br>";
			}
			if($vd->validasiLength($nisn,1,30)=='F'){
				$err++;
				$msg=$msg."<strong>- NISN tidak boleh kosong, maksimal 30 karakter </strong><br>";
			}
			if($vd->validasiLength($jalan,5,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Jalan di antara 5-100 karakter </strong><br>";
			}
			if($vd->validasiLength($kel,5,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Kelurahan di antara 5-100 karakter </strong><br>";
			}
			if($err==0){
				// set database
				$mahasiswa = new Mahasiswa();
				$updMhs = $mahasiswa->updMahasiswa2($nm,$jk,$tmplhr,$tgllhr,$agm,$kwn,$almt,$kota,$idwil,$ayah,$ibu,$job_a,$job_i,$nik,$em_k,$em_l,$hp,$nim,$tglmsk,$stat_msk,$prd,$akt,$sks,$as_sk,$as_prd,$dw,$oldNim,$idMhs,$nisn,$npwp,$jalan,$dusun,$rt,$rw,$kel,$zip,$kps,$trans,$tinggal);
				echo $updMhs;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}	
	}

	function uplmhspicAction(){
		$user = new Menu();
		$menu = "mahasiswa/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			 if (0<$_FILES["file"]["error"] ) {
		        echo "Error: ". $_FILES["file"]["error"] . "<br>";
		    }
		    else {
		    	$id_mhs = $this->_request->get('id');
		    	$temp = explode(".", $_FILES["file"]["name"]);
				$newfilename = $id_mhs . '.' . strtolower(end($temp));
				$path = __FILE__;
				$filePath = str_replace('academic/application/controllers/AjaxController.php','public/file/mhs/foto',$path);
				$target_dir = $filePath;
				$target_file = str_replace("'", "", $target_dir ."/'". $newfilename);
				$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
				$mimes = array('image/jpeg','image/jpg');
				$msg="";
				if(!in_array($_FILES['file']['type'],$mimes)){
				   	echo "File harus berformat JPG! <br>";
				}else{
					if ($_FILES["file"]["size"] > (200*1024)) {
					    echo "File Foto tidak boleh lebih dari 200 KB";
					}else{
						move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
						echo "Upload Foto Mahasiswa Sukses!";
					}
				}
			}
		}
	}

	function uplmhsAction(){
	    $user = new Menu();
	    $menu = "mahasiswa/new";
	    $getMenu = $user->cekUserMenu($menu);
	    if ($getMenu=="F"){
	        echo "F|Anda tidak memiliki akses";
	    } else {
	        // disabel layout
	        $this->_helper->layout->disableLayout();
	        if (0<$_FILES["file"]["error"] ) {
	            echo "Error: ". $_FILES["file"]["error"] . "<br>";
	        }
            else {
                $id=$this->_request->get('id');
                $temp = explode(".", $_FILES["file"]["name"]);
                $newfilename = md5(round(microtime(true))) . '.' . end($temp);
                $x=rand(100000,999999);
                $path = __FILE__;
                $filePath = str_replace('controllers/AjaxController.php','temps',$path);
                $target_dir = $filePath;
                $target_file = str_replace("'", "", $target_dir ."/". $newfilename);
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
                        $arr_data=array();
                        foreach ($cell_collection as $cell) {
                            $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                            $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                            $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
                            if ($row < 2) {
                                $header[$row][$column] = $data_value;
                            } else {
                                $arr_data[$row][$column] = $data_value;
                            }
                        }
                        if (count($arr_data)>0) {
                            $n=1;
			    $msg="";
                            foreach ($arr_data as $key => $value) {
                                $nim = $this->esc_quote(trim($value['B']));
                                if($nim!=''){
                                    $nm = $this->esc_quote(trim($value['C']));
                                    $tmplhr = $this->esc_quote(trim($value['E']));
                                    $tgllhr= date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($value['F']));
                                    $jk = trim($value['D']);
                                    $arrAgm = explode("#:", $value['G']);
                                    $agm=end($arrAgm);
                                    $arrKwn=explode("#:", $value['H']);
                                    $kwn = end($arrKwn);
                                    $nik = $value['O'];
                                    $almt= $this->esc_quote(trim($value['I']));
                                    $kota = $this->esc_quote(trim($value['J']));
                                    $ayah = $this->esc_quote(trim($value['K']));
                                    $ibu = $this->esc_quote(trim($value['L']));
                                    $arr_job_a = explode("#:", $value['M']);
                                    $job_a=end($arr_job_a);
                                    $arr_job_i = explode("#:", $value['N']);
                                    $job_i=end($arr_job_i);
                                    $arr_masuk = explode("#:", $value['T']);
                                    $akt = $arr_masuk[3];
                                    $prd= $arr_masuk[2];
                                    $stat_msk=$arr_masuk[1];
                                    $arr_dw = explode("#:", $value['X']);
                                    $dw=end($arr_dw);
                                    $tglmsk= date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($value['S']));
                                    $as_sk = $this->esc_quote(trim($value['V']));
                                    $as_prd = $this->esc_quote(trim($value['W']));
                                    $sks = $value['U'];
                                    $em_k = trim($value['P']);
                                    $em_l = trim($value['Q']);
                                    $hp= $this->esc_quote(trim($value['R']));
                                    $idwil="999999";
                                    $stat_mhs="A";
                                    // validation
                                    $err=0;
                                    $vd = new Validation();
                                    if(($vd->validasiLength($nm,3,50)=='F')or($vd->validasiRegexNama($nm)=='F')){
                                        $err++;
                                        $msg=$msg."<strong>- Nama minimal 3 huruf dan tidak boleh mengandung angka dan simbol</strong><br>";
                                    }
                                    if(($vd->validasiLength($tmplhr,1,50)=='F')or($vd->validasiRegexNama($tmplhr)=='F')){
                                        $err++;
                                        $msg=$msg."<strong>- Tempat lahir tidak boleh kosong dan tidak boleh mengandung angka dan simbol</strong><br>";
                                    }
                                    if($vd->validasiDate2($tgllhr)=='F'){
                                        $err++;
                                        $msg=$msg."<strong>- Tanggal lahir tidak boleh kosong dan format harus benar</strong><br>";
                                    }
                                    if($vd->validasiLength($jk,1,1)=='F'){
                                        $err++;
                                        $msg=$msg."<strong>- Jenis kelamin tidak boleh kosong</strong><br>";
                                    }
                                    if($vd->validasiLength($agm,1,5)=='F'){
                                        $err++;
                                        $msg=$msg."<strong>- Agama tidak boleh kosong</strong><br>";
                                    }
                                    if($vd->validasiLength($kwn,1,5)=='F'){
                                        $err++;
                                        $msg=$msg."<strong>- Kewarganegaraan tidak boleh kosong</strong><br>";
                                    }
                                    if ($vd->validasiLength($kwn,1,16)=='F'){
                                        $err++;
                                        $msg=$msg."<strong>- NIK tidak benar, isikan 0 di setiap isian bila tidak ada NIK</strong><br>";
                                    }
                                    if($vd->validasiLength($almt,5,200)=='F'){
                                        $err++;
                                        $msg=$msg."<strong>- Alamat di antara 5-200 karakter </strong><br>";
                                    }
                                    if($vd->validasiLength($kota,2,40)=='F'){
                                        $err++;
                                        $msg=$msg."<strong>- Kota di antara 2-40 karakter </strong><br>";
                                    }
                                    if(($vd->validasiLength($ayah,3,50)=='F')or($vd->validasiRegexNama($ayah)=='F')){
                                        $err++;
                                        $msg=$msg."<strong>- Nama ayah minimal 3 huruf dan tidak boleh mengandung angka dan simbol</strong><br>";
                                    }
                                    if(($vd->validasiLength($ibu,3,50)=='F')or($vd->validasiRegexNama($ibu)=='F')){
                                        $err++;
                                        $msg=$msg."<strong>- Nama ibu minimal 3 huruf dan tidak boleh mengandung angka dan simbol</strong><br>";
                                    }
                                    if($vd->validasiLength($dw,1,30)=='F'){
                                        $err++;
                                        $msg=$msg."<strong>- Dosen Wali tidak boleh kosong</strong><br>";
                                    }
                                    if($vd->validasiLength($akt,1,4)=='F'){
                                        $err++;
                                        $msg=$msg."<strong>- Angkatan tidak boleh kosong</strong><br>";
                                    }
                                    if($vd->validasiLength($prd,1,10)=='F'){
                                        $err++;
                                        $msg=$msg."<strong>- Program studi tidak boleh kosong</strong><br>";
                                    }
                                    if($vd->validasiLength($nim,4,20)=='F'){
                                        $err++;
                                        $msg=$msg."<strong>- NIM harus di antara 4-20 karakter</strong><br>";
                                    }
                                    if($vd->validasiDate2($tglmsk)=='F'){
                                        $err++;
                                        $msg=$msg."<strong>- Tanggal masuk tidak boleh kosong dan format harus benar</strong><br>";
                                    }
                                    if ($vd->validasiLength($hp,1,50)=='F'){
                                        $err++;
                                        $msg=$msg."<strong>- Kontak tidak boleh kosong, maksimal 50 karakter</strong><br>";
                                    }
                                    if(($vd->validasiEmail($em_k)=='F')or($vd->validasiEmail($em_l)=='F')){
                                        $err++;
                                        $msg=$msg."<strong>- Format email tidak benar</strong><br>";
                                    }
                                    if(($vd->validasiLength($em_k,5,100)=='F')or($vd->validasiLength($em_l,5,100)=='F')){
                                        $err++;
                                        $msg=$msg."<strong>- Email di antara 5-100 karakter</strong><br>";
                                    }
                                    if($err==0){
                                        $mahasiswa = new Mahasiswa();
                                        $setMhs = $mahasiswa->setMahasiswa($nm,$jk,$tmplhr,$tgllhr,$agm,$kwn,$almt,$kota,$idwil,$ayah,$ibu,$job_a,$job_i,$nik,$em_k,$em_l,$hp,$nim,$tglmsk,$stat_msk,$prd,$akt,$sks,$as_sk,$as_prd,$stat_mhs,$dw);
                                        $arrResult=explode('|', $setMhs);
                                        $msg=$msg."Baris ke-".$n." : ".$arrResult[1]."<br>";
                                    }else{
                                        $msg="Baris ke-".$n." : ".$msg;
                                    }
                                    $n++;
                                }
                            }
                            if($n==1){
                                $msg= "Maaf tidak ada baris yang dapat diproses, silakan cek kembali";
                            }
                            echo $msg;
                        }else{
                            $msg= "Maaf tidak ada baris yang dapat diproses, silakan cek kembali";
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
	
	function resetpwdmhsAction(){
		$user = new Menu();
		$menu = "mahasiswa/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
			$nim = $param[0];
			$mhs = new Mahasiswa();
			$updPwdMahasiswa = $mhs->updPwdMahasiswa($nim, $nim);
			echo $updPwdMahasiswa;
		}
	}

	// Dosen
	function showdsnAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$kat = $request['kat'];
		$stat = $request['stat'];
		$a_hb= $request['a_hb'];
		// set session
		$param = new Zend_Session_Namespace('param_dsn');
		$param->kat=$kat;
		$param->statdsn=$stat;
		$param->a_hb=$a_hb;
	}

	function insdsnAction(){
		$user = new Menu();
		$menu = "dosen/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$nm = $this->esc_quote(trim($request['nm']));
			$g_dpn = $this->esc_quote(trim($request['g_dpn']));
			$g_blk = $this->esc_quote(trim($request['g_blk']));
			$tmplhr = $this->esc_quote(trim($request['tmplhr']));
			$tgllhr = $request['tgllhr'];
			$jk = trim($request['jk']);
			$agm = $request['agm'];
			$kwn = $request['kwn'];
			$nik = $request['nik'];
			$aktif="t";
			$alamat= $this->esc_quote(trim($request['almt']));
			$kota = $this->esc_quote(trim($request['kota']));
			$em_k = trim($request['em_k']);
			$em_l = trim($request['em_l']);
			$hp = str_replace("_", "", $request['hp']);
			$nidn = trim($request['nidn']);
			$a_hb = $request['a_hb'];
			$id_kat = $request['idKat'];
			$jab = $request['jab'];
			$pang = $request['pang'];
			$dw = $request['a_dw'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiLength($nm,3,50)=='F')or($vd->validasiRegexNama($nm)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama minimal 3 huruf dan tidak boleh mengandung angka dan simbol</strong><br>";
			}
			if(($vd->validasiLength($tmplhr,1,50)=='F')or($vd->validasiRegexNama($tmplhr)=='F')){
				$err++;
				$msg=$msg."<strong>- Tempat lahir tidak boleh kosong dan tidak boleh mengandung angka dan simbol</strong><br>";
			}
			if($vd->validasiDate($tgllhr)=='F'){
				$err++;
				$msg=$msg."<strong>- Tanggal lahir tidak boleh kosong dan format harus benar</strong><br>";
			}
			if($vd->validasiLength($jk,1,1)=='F'){
				$err++;
				$msg=$msg."<strong>- Jenis kelamin tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($agm,1,5)=='F'){
				$err++;
				$msg=$msg."<strong>- Agama tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($kwn,1,5)=='F'){
				$err++;
				$msg=$msg."<strong>- Kewarganegaraan tidak boleh kosong</strong><br>";
			}
			if ($vd->validasiRegex($nik,"/^[0-9]{2}+.+[0-9]{4}+.+[0-9]{6}+.+[0-9]{4}$/")=='F'){
				$err++;
				$msg=$msg."<strong>- NIK tidak benar, isikan 0 di setiap isian bila tidak ada NIK</strong><br>";
			}
			if($vd->validasiLength($alamat,5,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Alamat di antara 5-200 karakter </strong><br>";
			}
			if($vd->validasiLength($kota,2,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Kota di antara 2-40 karakter </strong><br>";
			}
			if ($vd->validasiLength($hp,13,16)=='F'){
				$err++;
				$msg=$msg."<strong>- Nomor HP tidak benar, isikan 0 di setiap isian bila tidak ada nomor HP</strong><br>";
			}
			if(($vd->validasiEmail($em_k)=='F')or($vd->validasiEmail($em_l)=='F')){
				$err++;
				$msg=$msg."<strong>- Format email tidak benar</strong><br>";
			}
			if(($vd->validasiLength($em_k,5,50)=='F')or($vd->validasiLength($em_l,5,50)=='F')){
				$err++;
				$msg=$msg."<strong>- Email di antara 5-50 karakter</strong><br>";
			}
			if($vd->validasiLength($a_hb,1,5)=='F'){
				$err++;
				$msg=$msg."<strong>- Home Base tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($id_kat,1,5)=='F'){
				$err++;
				$msg=$msg."<strong>- Kelompok dosen tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($jab,1,5)=='F'){
				$err++;
				$msg=$msg."<strong>- Jabatan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($pang,1,5)=='F'){
				$err++;
				$msg=$msg."<strong>- Pangkat tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($dw,1,5)=='F'){
				$err++;
				$msg=$msg."<strong>- Dosen wali tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$dosen = new Dosen();
				$insdsn = $dosen->setDosen($nm,$g_dpn,$g_blk,$nidn,$a_hb,$id_kat,$tmplhr,$tgllhr,$jk,$agm,$kwn,$aktif,$alamat,$kota,$nik,$hp,$em_k,$em_l,$jab,$pang,$dw);
				echo $insdsn;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function deldsnAction(){
		$user = new Menu();
		$menu = "dosen/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$kd = $param[0];
	    	// unlink foto
	    	unlink('../public/file/dsn/foto/'.$kd.'.jpg');
			$dosen = new Dosen();
			$delDosen = $dosen->delDosen($kd);
			echo $delDosen;
		}
	}

	function upddsnAction(){
		$user = new Menu();
		$menu = "dosen/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$nm = $this->esc_quote(trim($request['nm']));
			$g_dpn = $this->esc_quote(trim($request['g_dpn']));
			$g_blk = $this->esc_quote(trim($request['g_blk']));
			$tmplhr = $this->esc_quote(trim($request['tmplhr']));
			$tgllhr = $request['tgllhr'];
			$jk = trim($request['jk']);
			$agm = $request['agm'];
			$kwn = $request['kwn'];
			$nik = $request['nik'];
			$aktif="t";
			$alamat= $this->esc_quote(trim($request['almt']));
			$kota = $this->esc_quote(trim($request['kota']));
			$em_k = trim($request['em_k']);
			$em_l = trim($request['em_l']);
			$hp = str_replace("_", "", $request['hp']);
			$nidn = trim($request['nidn']);
			$a_hb = $request['a_hb'];
			$id_kat = $request['idKat'];
			$jab = $request['jab'];
			$pang = $request['pang'];
			$dw = $request['a_dw'];
			$kd = $request['kd'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiLength($nm,3,50)=='F')or($vd->validasiRegexNama($nm)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama minimal 3 huruf dan tidak boleh mengandung angka dan simbol</strong><br>";
			}
			if(($vd->validasiLength($tmplhr,1,50)=='F')or($vd->validasiRegexNama($tmplhr)=='F')){
				$err++;
				$msg=$msg."<strong>- Tempat lahir tidak boleh kosong dan tidak boleh mengandung angka dan simbol</strong><br>";
			}
			if($vd->validasiDate($tgllhr)=='F'){
				$err++;
				$msg=$msg."<strong>- Tanggal lahir tidak boleh kosong dan format harus benar</strong><br>";
			}
			if($vd->validasiLength($jk,1,1)=='F'){
				$err++;
				$msg=$msg."<strong>- Jenis kelamin tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($agm,1,5)=='F'){
				$err++;
				$msg=$msg."<strong>- Agama tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($kwn,1,5)=='F'){
				$err++;
				$msg=$msg."<strong>- Kewarganegaraan tidak boleh kosong</strong><br>";
			}
			if ($vd->validasiRegex($nik,"/^[0-9]{2}+.+[0-9]{4}+.+[0-9]{6}+.+[0-9]{4}$/")=='F'){
				$err++;
				$msg=$msg."<strong>- NIK tidak benar, isikan 0 di setiap isian bila tidak ada NIK</strong><br>";
			}
			if($vd->validasiLength($alamat,5,200)=='F'){
				$err++;
				$msg=$msg."<strong>- Alamat di antara 5-200 karakter </strong><br>";
			}
			if($vd->validasiLength($kota,2,40)=='F'){
				$err++;
				$msg=$msg."<strong>- Kota di antara 2-40 karakter </strong><br>";
			}
			if ($vd->validasiLength($hp,13,16)=='F'){
				$err++;
				$msg=$msg."<strong>- Nomor HP tidak benar, isikan 0 di setiap isian bila tidak ada nomor HP</strong><br>";
			}
			if(($vd->validasiEmail($em_k)=='F')or($vd->validasiEmail($em_l)=='F')){
				$err++;
				$msg=$msg."<strong>- Format email tidak benar</strong><br>";
			}
			if(($vd->validasiLength($em_k,5,50)=='F')or($vd->validasiLength($em_l,5,50)=='F')){
				$err++;
				$msg=$msg."<strong>- Email di antara 5-50 karakter</strong><br>";
			}
			if($vd->validasiLength($a_hb,1,5)=='F'){
				$err++;
				$msg=$msg."<strong>- Home Base tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($id_kat,1,5)=='F'){
				$err++;
				$msg=$msg."<strong>- Kelompok dosen tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($jab,1,5)=='F'){
				$err++;
				$msg=$msg."<strong>- Jabatan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($pang,1,5)=='F'){
				$err++;
				$msg=$msg."<strong>- Pangkat tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($dw,1,5)=='F'){
				$err++;
				$msg=$msg."<strong>- Dosen wali tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$dosen = new Dosen();
				$upddosen = $dosen->updDosen($nm,$g_dpn,$g_blk,$nidn,$a_hb,$id_kat,$tmplhr,$tgllhr,$jk,$agm,$kwn,$aktif,$alamat,$kota,$nik,$hp,$em_k,$em_l,$jab,$pang,$dw,$kd);
				echo $upddosen;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	function updstdsnAction(){
		$user = new Menu();
		$menu = "dosen/status";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
			$stat = $param[0];
	    	$kd = $param[1];
			$dosen = new Dosen();
			$updStatDosen = $dosen->updStatDosen($stat, $kd);
			echo $updStatDosen;
		}
	}

	function upldsnpicAction(){
		$user = new Menu();
		$menu = "dosen/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			 if (0<$_FILES["file"]["error"] ) {
		        echo "Error: ". $_FILES["file"]["error"] . "<br>";
		    }
		    else {
		    	$kd_dosen = $this->_request->get('id');
		    	$temp = explode(".", $_FILES["file"]["name"]);
				$newfilename = $kd_dosen . '.' . strtolower(end($temp));
				$path = __FILE__;
				$filePath = str_replace('academic/application/controllers/AjaxController.php','public/file/dsn/foto',$path);
				$target_dir = $filePath;
				$target_file = str_replace("'", "", $target_dir ."/'". $newfilename);
				$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
				$mimes = array('image/jpeg','image/jpg');
				$msg="";
				if(!in_array($_FILES['file']['type'],$mimes)){
				   	echo "File harus berformat JPG! <br>";
				}else{
					if ($_FILES["file"]["size"] > (200*1024)) {
					    echo "File Foto tidak boleh lebih dari 200 KB";
					}else{
						move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
						echo "Upload Foto Dosen Sukses!";
					}
				}
			}
		}
	}
	
	function resetpwddsnAction(){
		$user = new Menu();
		$menu = "dosen/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
			$kd_dsn = $param[0];
			$dsn = new Dosen();
			$updPwdDosen = $dsn->updPwdDosen($kd_dsn, $kd_dsn);
			echo $updPwdDosen;
		}
	}
	
	// pendidikan dosen
	function inspdsnAction(){
		$user = new Menu();
		$menu = "pendidikandosen/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$kd_dosen = $this->esc_quote(trim($request['kd']));
			$jenjang = $this->esc_quote(trim($request['jenjang']));
			$thnmsk = $request['thnmsk'];
			$thnlls = $request['thnlls'];
			$inst= $this->esc_quote(trim($request['inst']));
			$jur = $this->esc_quote(trim($request['jur']));
			$cttn = $this->esc_quote(trim($request['cttn']));
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($thnmsk>$thnlls){
				$err++;
				$msg=$msg."<strong>- Tahun lulus harus lebih besar atau sama dengan tahun masuk</strong><br>";
			}
			if($vd->validasiLength($kd_dosen,1,50)=='F'){
				$err++;
				$msg=$msg."<strong>- Kode dosen tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($inst,1,50)=='F'){
				$err++;
				$msg=$msg."<strong>- Institusi tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($jenjang,1,5)=='F'){
				$err++;
				$msg=$msg."<strong>- Jenjang pendidikan tidak boleh kosong</strong><br>";
			}
			if($vd->validasiBetween($thnlls, 1950, 3000)=='F'){
				$err++;
				$msg=$msg."<strong>- Tahun lulus tidak boleh kosong</strong><br>";
			}
			if($vd->validasiBetween($thnmsk, 1950, 3000)=='F'){
				$err++;
				$msg=$msg."<strong>- Tahun masuk tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$pendDosen = new PendDosen();
				$setPendDosen = $pendDosen->setPendDosen($kd_dosen,$jenjang,$thnmsk,$thnlls,$jur,$inst,$cttn);
				echo $setPendDosen;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	function delpdsnAction(){
		$user = new Menu();
		$menu = "dosen/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$id = $param[0];
			$pendDosen = new PendDosen();
			$delPendDosen = $pendDosen->delPendDosen($id);
			echo $delPendDosen;
		}
	}

	// Kurikulum
	function showkurAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$prd = $request['prd'];
		// set session
		$param = new Zend_Session_Namespace('param_kur');
		$param->prd=$prd;
	}

	function inskurAction(){
		$user = new Menu();
		$menu = "kurikulum/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd_kurikulum = trim($request['kd']);
			$nm_kurikulum = $this->esc_quote(trim($request['nm']));
			$kd_periode = $request['periode'];
			$kd_prodi = $request['prd'];
			$smt = $request['smt'];
			$sks_l = $request['sks_l'];
			$sks_w = $request['sks_w'];
			$sks_p = $request['sks_p'];
			$status = "t";
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiLength($kd_kurikulum,3,5)=='F')or($vd->validasiAlNum($kd_kurikulum)=='F')){
				$err++;
				$msg=$msg."<strong>- Kode kurikulum minimal 3 karakter dan maksimal 5 karakter (huruf dan atau angka)</strong><br>";
			}
			if(($vd->validasiLength($nm_kurikulum,3,20)=='F')or($vd->validasiAlNum($nm_kurikulum)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama kurikulum minimal 3 karakter dan maksimal 20 karakter (huruf dan atau angka)</strong><br>";
			}
			if($vd->validasiLength($kd_periode,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Periode akademik berlaku tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($kd_prodi,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Prodi tidak boleh kosong</strong><br>";
			}
			if($vd->validasiBetween($smt,2,10)=='F'){
				$err++;
				$msg=$msg."<strong>- Semester normal harus diantara 2 dan 10</strong><br>";
			}
			if($vd->validasiBetween($sks_l,10,150)=='F'){
				$err++;
				$msg=$msg."<strong>- SKS lulus harus diantara 10 dan 180</strong><br>";
			}
			if($vd->validasiBetween($sks_w,10,150)=='F'){
				$err++;
				$msg=$msg."<strong>- SKS wajib harus diantara 10 dan 180</strong><br>";
			}
			if($vd->validasiBetween($sks_p,0,10)=='F'){
				$err++;
				$msg=$msg."<strong>- SKS pilihan harus diantara 0 dan 10</strong><br>";
			}
			if($sks_p==""){
				$sks_p=0;
			}
			if($sks_l!=$sks_w+$sks_p){
				$err++;
				$msg=$msg."<strong>- SKS Lulus harus = SKS wajib + SKS pilihan</strong><br>";	
			}
			if($err==0){
				// set database
				$kurikulum = new Kurikulum();
				$setkur = $kurikulum->setKur($kd_kurikulum,$nm_kurikulum,$kd_periode,$status,$kd_prodi,$smt,$sks_l,$sks_w, $sks_p);
				echo $setkur;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delkurAction(){
		$user = new Menu();
		$menu = "kurikulum/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$kd = $param[0];
			$kurikulum = new Kurikulum();
			$delKurikulum = $kurikulum->delKur($kd);
			echo $delKurikulum;
		}
	}

	function updkurAction(){
		$user = new Menu();
		$menu = "kurikulum/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id_kurikulum = trim($request['id']);
			$kd_kurikulum = trim($request['kd']);
			$nm_kurikulum = $this->esc_quote(trim($request['nm']));
			$kd_periode = $request['periode'];
			$kd_prodi = $request['prd'];
			$smt = $request['smt'];
			$sks_l = $request['sks_l'];
			$sks_w = $request['sks_w'];
			$sks_p = $request['sks_p'];
			$status = "t";
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiLength($kd_kurikulum,3,5)=='F')or($vd->validasiAlNum($kd_kurikulum)=='F')){
				$err++;
				$msg=$msg."<strong>- Kode kurikulum minimal 3 karakter dan maksimal 5 karakter (huruf dan atau angka)</strong><br>";
			}
			if(($vd->validasiLength($nm_kurikulum,3,20)=='F')or($vd->validasiAlNum($nm_kurikulum)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama kurikulum minimal 3 karakter dan maksimal 20 karakter (huruf dan atau angka)</strong><br>";
			}
			if($vd->validasiLength($kd_periode,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Periode akademik berlaku tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($kd_prodi,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Prodi tidak boleh kosong</strong><br>";
			}
			if($vd->validasiBetween($smt,2,10)=='F'){
				$err++;
				$msg=$msg."<strong>- Semester normal harus diantara 2 dan 10</strong><br>";
			}
			if($vd->validasiBetween($sks_l,10,150)=='F'){
				$err++;
				$msg=$msg."<strong>- SKS lulus harus diantara 10 dan 180</strong><br>";
			}
			if($vd->validasiBetween($sks_w,10,150)=='F'){
				$err++;
				$msg=$msg."<strong>- SKS wajib harus diantara 10 dan 180</strong><br>";
			}
			if($vd->validasiBetween($sks_p,0,10)=='F'){
				$err++;
				$msg=$msg."<strong>- SKS pilihan harus diantara 0 dan 10</strong><br>";
			}
			if($sks_p==""){
				$sks_p=0;
			}
			if($sks_l!=$sks_w+$sks_p){
				$err++;
				$msg=$msg."<strong>- SKS Lulus harus = SKS wajib + SKS pilihan</strong><br>";	
			}
			if($err==0){
				// set database
				$kurikulum = new Kurikulum();
				$updKur = $kurikulum->updKur($kd_kurikulum,$nm_kurikulum,$kd_periode,$status,$kd_prodi,$smt,$sks_l,$sks_w, $sks_p, $id_kurikulum);
				echo $updKur;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	// Mata Kuliah
	function showmatkulAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$prd = $request['prd'];
		// set session
		$param = new Zend_Session_Namespace('param_mk');
		$param->prd=$prd;
	}

	function insmatkulAction(){
		$user = new Menu();
		$menu = "matkul/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$nm_mk = $this->esc_quote(trim($request['nm_mk']));
			$kd_prodi = $request['prd'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiLength($nm_mk,3,200)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama mata kuliah kurikulum minimal 3 karakter dan maksimal 200 karakter (huruf dan atau angka)</strong><br>";
			}
			if($vd->validasiLength($kd_prodi,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Prodi penanggung jawab tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$mk = new Matkul();
				$setMatkul = $mk->setMatkul($nm_mk,$kd_prodi);
				echo $setMatkul;
				// set session for result
				$param = new Zend_Session_Namespace('param');
				$param->prd=array($kd_prodi);
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delmatkulAction(){
		$user = new Menu();
		$menu = "matkul/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$id = $param[0];
			$matkul = new Matkul();
			$delMatkul = $matkul->delMatkul($id);
			echo $delMatkul;
		}
	}

	function updmatkulAction(){
		$user = new Menu();
		$menu = "matkul/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id_mk = $request['id_mk'];
			$nm_mk = $this->esc_quote(trim($request['nm_mk']));
			$kd_prodi = $request['prd'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiLength($nm_mk,3,200)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama mata kuliah kurikulum minimal 3 karakter dan maksimal 200 karakter (huruf dan atau angka)</strong><br>";
			}
			if($vd->validasiLength($kd_prodi,1,20)=='F'){
				$err++;
				$msg=$msg."<strong>- Prodi penanggung jawab tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$mk = new Matkul();
				$updMatkul = $mk->updMatkul($nm_mk,$kd_prodi,$id_mk);
				echo $updMatkul;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	// Mata Kuliah kurikulum
	function insmkkurAction(){
		$user = new Menu();
		$menu = "matkulkurikulum/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$kd_mk = $this->esc_quote(trim($request['kd_mk']));
			$id_mk = trim($request['id_mk']);
			$nm_mk = trim($request['nm_mk']);
			$id_kur = $request['id_kur'];
			$jns_mk = $request['jns_mk'];
			$smt = $request['smt'];
			$sks_tm = $request['sks_tm'];
			$sks_p = $request['sks_p'];
			$sks_pl = $request['sks_pl'];
			$sks_s = $request['sks_s'];
			$id_kat = $request['id_kat'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiLength($kd_mk,3,20)=='F')or($vd->validasiAlNum($kd_mk)=='F')){
				$err++;
				$msg=$msg."<strong>- Kode mata kuliah minimal 3 karakter dan maksimal 20 karakter (huruf dan atau angka)</strong><br>";
			}
			if(($vd->validasiLength($id_mk,1,9)=='F')or($vd->validasiLength($nm_mk,1,200)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama mata kuliah tidak boleh kosong</strong><br>";
			}
			if(($vd->validasiLength($jns_mk,1,2)=='F')or($vd->validasiLength($smt,1,2)=='F')){
				$err++;
				$msg=$msg."<strong>- Jenis mata kuliah dan semester tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($id_kat,1,2)=='F'){
				$err++;
				$msg=$msg."<strong>- Kelompok mata kuliah tidak boleh kosong</strong><br>";
			}
			if($sks_tm==""){
				$sks_tm=0;
			}
			if($sks_p==""){
				$sks_p=0;
			}
			if($sks_pl==""){
				$sks_pl=0;
			}
			if($sks_s==""){
				$sks_s=0;
			}
			if (($sks_tm==0)and($sks_p==0)and($sks_pl==0)and($sks_s==0)){
				$err++;
				$msg=$msg."<strong>- SKS harus terisi minimal salah satu jenis SKS</strong><br>";	
			}
			if($err==0){
				// set database
				$mkkur = new MatkulKurikulum();
				$setMatkulKurikulum = $mkkur->setMatkulKurikulum($id_mk,$id_kur,$kd_mk,$sks_tm, $sks_p, $sks_pl, $sks_s, $jns_mk, $smt, $id_kat);
				echo $setMatkulKurikulum;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delmkkurAction(){
		$user = new Menu();
		$menu = "matkulkurikulum/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$id = $param[0];
			$mkkur = new MatkulKurikulum();
			$delMatkulKurikulum = $mkkur->delMatkulKurikulum($id);
			echo $delMatkulKurikulum;
		}
	}

	function updmkkurAction(){
		$user = new Menu();
		$menu = "matkulkurikulum/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start updating
			$request = $this->getRequest()->getPost();
			$kd_mk = $this->esc_quote(trim($request['kd_mk']));
			$id_mk = trim($request['id_mk']);
			$nm_mk = trim($request['nm_mk']);
			$id_kur = $request['id_kur'];
			$jns_mk = $request['jns_mk'];
			$smt = $request['smt'];
			$sks_tm = $request['sks_tm'];
			$sks_p = $request['sks_p'];
			$sks_pl = $request['sks_pl'];
			$sks_s = $request['sks_s'];
			$id_kat = $request['id_kat'];
			$id_mk_kur = $request['id_mk_kur'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if(($vd->validasiLength($kd_mk,3,20)=='F')or($vd->validasiAlNum($kd_mk)=='F')){
				$err++;
				$msg=$msg."<strong>- Kode mata kuliah minimal 3 karakter dan maksimal 20 karakter (huruf dan atau angka)</strong><br>";
			}
			if(($vd->validasiLength($id_mk,1,9)=='F')or($vd->validasiLength($nm_mk,1,200)=='F')){
				$err++;
				$msg=$msg."<strong>- Nama mata kuliah tidak boleh kosong</strong><br>";
			}
			if(($vd->validasiLength($jns_mk,1,2)=='F')or($vd->validasiLength($smt,1,2)=='F')){
				$err++;
				$msg=$msg."<strong>- Jenis mata kuliah dan semester tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($id_kat,1,2)=='F'){
				$err++;
				$msg=$msg."<strong>- Kelompok mata kuliah tidak boleh kosong</strong><br>";
			}
			if($sks_tm==""){
				$sks_tm=0;
			}
			if($sks_p==""){
				$sks_p=0;
			}
			if($sks_pl==""){
				$sks_pl=0;
			}
			if($sks_s==""){
				$sks_s=0;
			}
			if (($sks_tm==0)and($sks_p==0)and($sks_pl==0)and($sks_s==0)){
				$err++;
				$msg=$msg."<strong>- SKS harus terisi minimal salah satu jenis SKS</strong><br>";	
			}
			if($err==0){
				// set database
				$mkkur = new MatkulKurikulum();
				$updMatkulKurikulum = $mkkur->updMatkulKurikulum($id_kur,$kd_mk,$sks_tm, $sks_p, $sks_pl, $sks_s, $jns_mk, $smt, $id_kat, $id_mk_kur);
				echo $updMatkulKurikulum;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	// ekuivalensi
	function insekvAction(){
		$user = new Menu();
		$menu = "ekuivalensi/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id_kur_lm = $request['id_kur_lm'];
			$id_kur_br = $request['id_kur_br'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($id_kur_lm,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Kurikulum lama tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($id_kur_br,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Kurikulum baru tidak boleh kosong</strong><br>";
			}
			if($id_kur_br==$id_kur_lm){
				$err++;
				$msg=$msg."<strong>- Kurikulum tidak boleh sama</strong><br>";
			}
			if($err==0){
				// set database
				$ekuivalensi = new Ekuivalensi();
				$setEkuivalensi = $ekuivalensi->setEkuivalensi($id_kur_lm,$id_kur_br);
				echo $setEkuivalensi;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	function delekvAction(){
		$user = new Menu();
		$menu = "ekuivalensi/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$id = $param[0];
			$ekuivalensi = new Ekuivalensi();
			$delEkuivalensi = $ekuivalensi->delEkuivalensi($id);
			echo $delEkuivalensi;
		}
	}
	
	function insekv1Action(){
		$user = new Menu();
		$menu = "ekuivalensi/detil";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id_ekv = $request['id_ekv'];
			$id_mk_kur_lm = $request['id_mk_kur_lm'];
			$id_mk_kur_br = $request['id_mk_kur_br'];
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($id_ekv,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Ekuivalensi tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($id_mk_kur_lm,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Mata kuliah lama tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($id_mk_kur_br,1,100)=='F'){
				$err++;
				$msg=$msg."<strong>- Mata kuliah baru tidak boleh kosong</strong><br>";
			}
			if($err==0){
				// set database
				$ekuivalensi = new Ekuivalensi();
				$setEkuivalensiDtl = $ekuivalensi->setEkuivalensiDtl($id_ekv,$id_mk_kur_lm,$id_mk_kur_br);
				echo $setEkuivalensiDtl;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}
	
	function delekv1Action(){
		$user = new Menu();
		$menu = "ekuivalensi/detil";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$id = $param[0];
	    	$id_mk_kur_lm = $param[1];
	    	$id_mk_kur_br = $param[2];
			$ekuivalensi = new Ekuivalensi();
			$delEkuivalensiDtl = $ekuivalensi->delEkuivalensiDtl($id,$id_mk_kur_lm,$id_mk_kur_br);
			echo $delEkuivalensiDtl;
		}
	}

	// Ajar
	function insajarAction(){
		$user = new Menu();
		$menu = "ajar/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$id_mk_kur = $this->esc_quote(trim($request['id_mk_kur']));
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
				$ajar = new Ajar();
				$setAjar = $ajar->setAjar($kd_dsn,$id_mk_kur);
				echo $setAjar;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delajarAction(){
		$user = new Menu();
		$menu = "ajar/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$id = $param[0];
			$ajar = new Ajar();
			$delAjar = $ajar->delAjar($id);
			echo $delAjar;
		}
	}

	// nilai konversi
	function inskonvAction(){
		$user = new Menu();
		$menu = "konversi/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$arrNim = explode('###', $request['nim']);
			$nim = $arrNim[0];
			$id_mk_kur = $request['id_mk_kur'];
			$sks = $request['sks'];
			$sks_new = $request['sks_new'];
			$indeks_new = $request['indeks_new'];
			$kd_mk_old = $request['kd_mk_old'];
			$nm_mk_old = $request['nm_mk_old'];
			$sks_old = $request['sks_old'];
			$indeks_old = $request['indeks_old'];
			$sks_deducted = $sks-$sks_new;
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($nim,3,20)=='F'){
				$err++;
				$msg=$msg."<strong>- NIM tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($id_mk_kur,1,25)=='F'){
				$err++;
				$msg=$msg."<strong>- Data Mata Kuliah tidak boleh kosong</strong><br>";
			}
			if($vd->validasiBetween($sks_new,1,8)=='F'){
				$err++;
				$msg=$msg."<strong>- SKS diakui harus lebih dari 0</strong><br>";
			}
			if($vd->validasiLength($indeks_new,1,8)=='F'){
				$err++;
				$msg=$msg."<strong>- Nilai diakui tidak boleh kosong</strong><br>";
			}
			if($vd->validasiBetween($sks_old,1,8)=='F'){
				$err++;
				$msg=$msg."<strong>- SKS asal harus lebih dari 0</strong><br>";
			}
			if($vd->validasiLength($indeks_old,1,8)=='F'){
				$err++;
				$msg=$msg."<strong>- Nilai asal tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($kd_mk_old,1,35)=='F'){
				$err++;
				$msg=$msg."<strong>- kode mata kuliah asal tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($nm_mk_old,1,60)=='F'){
				$err++;
				$msg=$msg."<strong>- nama mata kuliah asal tidak boleh kosong</strong><br>";
			}
			if($sks_deducted<0){
				$err++;
				$msg=$msg."<strong>- SKS diakui tidak boleh lebih besar dari SKS default</strong><br>";	
			}
			if($err==0){
				// set database
				$konversi = new Konversi();
				$setKonversi = $konversi->setKonversi($nim,$id_mk_kur,$sks_deducted,$indeks_new,$kd_mk_old,$nm_mk_old,$indeks_old,$sks_old);
				echo $setKonversi;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function delkonvAction(){
		$user = new Menu();
		$menu = "konversi/delete";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// set database
			$request = $this->getRequest()->getPost();
			// gets value from ajax request
			$param = $request['param'];
	    	$nim = $param[0];
	    	$id_mk_kur = $param[1];
			$konversi = new Konversi();
			$delKonversi = $konversi->delKonversi($nim,$id_mk_kur);
			echo $delKonversi;
		}
	}

	function updkonvAction(){
		$user = new Menu();
		$menu = "konversi/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// start inserting
			$request = $this->getRequest()->getPost();
			$nim=$request['nim'];
			$id_mk_kur = $request['id_mk_kur'];
			$id_mk_kur_old = $request['id_mk_kur_old'];
			$sks = $request['sks'];
			$sks_new = $request['sks_new'];
			$indeks_new = $request['indeks_new'];
			$kd_mk_old = $request['kd_mk_old'];
			$nm_mk_old = $request['nm_mk_old'];
			$sks_old = $request['sks_old'];
			$indeks_old = $request['indeks_old'];
			$sks_deducted = $sks-$sks_new;
			// validation
			$err=0;
			$msg="";
			$vd = new Validation();
			if($vd->validasiLength($nim,3,20)=='F'){
				$err++;
				$msg=$msg."<strong>- NIM tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($id_mk_kur,1,25)=='F'){
				$err++;
				$msg=$msg."<strong>- Data Mata Kuliah tidak boleh kosong</strong><br>";
			}
			if($vd->validasiBetween($sks_new,1,8)=='F'){
				$err++;
				$msg=$msg."<strong>- SKS diakui harus lebih dari 0</strong><br>";
			}
			if($vd->validasiLength($indeks_new,1,8)=='F'){
				$err++;
				$msg=$msg."<strong>- Nilai diakui tidak boleh kosong</strong><br>";
			}
			if($vd->validasiBetween($sks_old,1,8)=='F'){
				$err++;
				$msg=$msg."<strong>- SKS asal harus lebih dari 0</strong><br>";
			}
			if($vd->validasiLength($indeks_old,1,8)=='F'){
				$err++;
				$msg=$msg."<strong>- Nilai asal tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($kd_mk_old,1,35)=='F'){
				$err++;
				$msg=$msg."<strong>- kode mata kuliah asal tidak boleh kosong</strong><br>";
			}
			if($vd->validasiLength($nm_mk_old,1,60)=='F'){
				$err++;
				$msg=$msg."<strong>- nama mata kuliah asal tidak boleh kosong</strong><br>";
			}
			if($sks_deducted<0){
				$err++;
				$msg=$msg."<strong>- SKS diakui tidak boleh lebih besar dari SKS default</strong><br>";
			}
			if($err==0){
				// set database
				$konversi = new Konversi();
				$updKonversi = $konversi->updKonversi($nim,$id_mk_kur,$sks_deducted,$indeks_new,$kd_mk_old,$nm_mk_old,$indeks_old,$sks_old,$nim,$id_mk_kur_old);
				echo $updKonversi;
			}else{
				echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
			}
		}
	}

	function rekapmhsAction(){
    		// makes disable layout
    		$this->_helper->getHelper('layout')->disableLayout();
    		$request = $this->getRequest()->getPost();
    		// gets value from ajax request
    		$prodi=$request['prodi'];
    		$angkatan=$request['angkatan'];
    		$jns=$request['jns'];
    		// set session
    		$param = new Zend_Session_Namespace('param_rekap_mhs');
    		$param->prodi=$prodi;
    		$param->angkatan=$angkatan;
    		$param->jns=$jns;
    	}
}
