<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Nilai Controller -> Controller untuk modul halaman nilai mahasiswa
*/
class NilaiController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Kelas');
		Zend_Loader::loadClass('Paketkelas');
		Zend_Loader::loadClass('PaketkelasTA');
		Zend_Loader::loadClass('Kuliah');
		Zend_Loader::loadClass('KuliahTA');
		Zend_Loader::loadClass('Nilai');
		Zend_Loader::loadClass('NilaiTA');
		Zend_Loader::loadClass('Kurikulum');
		Zend_Loader::loadClass('Ekuivalensi');
		Zend_Loader::loadClass('Register');
		Zend_Loader::loadClass('StatReg');
		Zend_Loader::loadClass('AturanNilai');
		Zend_Loader::loadClass('Konversi');
		Zend_Loader::loadClass('Report');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Validation');
		Zend_Loader::loadClass('PHPExcel');
		Zend_Loader::loadClass('PHPExcel_Cell_AdvancedValueBinder');
		Zend_Loader::loadClass('PHPExcel_IOFactory');
		$auth = Zend_Auth::getInstance();
		$ses_ac = new Zend_Session_Namespace('ses_ac');
		$ses_menu = new Zend_Session_Namespace('ses_menu');
		if (($auth->hasIdentity())and($ses_ac->uname)) {
			$this->view->namauser =Zend_Auth::getInstance()->getIdentity()->nama;
			$this->view->username=Zend_Auth::getInstance()->getIdentity()->username;
			$this->view->kd_pt=$ses_ac->kd_pt;
			$this->view->nm_pt=$ses_ac->nm_pt;
			$this->view->menu=$ses_menu->menu;
			$this->nm_pt=$ses_ac->nm_pt;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// treeview
		$this->view->active_tree="07";
		$this->view->active_menu="nilai/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "nilai/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Nilai Mahasiswa";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// destroy session param
			Zend_Session::namespaceUnset('param_pkls');
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
			// get data periode
			$periode = new Periode();
			$this->view->listPeriode=$periode->fetchAll();
			$getPerAktif=$periode->getPeriodeByStatus(0);
			foreach ($getPerAktif as $dtPerAktif) {
				$per_aktif=$dtPerAktif['kd_periode'];
			}
			$this->view->per_aktif=$per_aktif;
		}
	}

	function listAction()
	{
		$user = new Menu();
		$menu = "nilai/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// show data
			$param = new Zend_Session_Namespace('param_pkls');
			$kd_prodi = $param->prd;
			$kd_periode = $param->per;
			$prodi = new Prodi();
			$getProdi = $prodi->getProdiByKd($kd_prodi);
			$periode = new Periode();
			$getPeriode = $periode->getPeriodeByKd($kd_periode);
			if(($getPeriode)and($getProdi)){
				foreach ($getProdi as $dtProdi) {
					$nm_prd=$dtProdi['nm_prodi'];
				}
				// Title Browser
				$this->view->title = "Daftar Paket Kelas Periode ".$kd_periode." Prodi ".$nm_prd;
				// paket kelas
				$paketkelas=new Paketkelas();
				$this->view->listPaket=$paketkelas->getPaketKelasByPeriodeProdi($kd_periode,$kd_prodi);
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Daftar Paket Kelas";	
			}
			// navigation
			$this->_helper->navbar('nilai',0,0,0,0);
		}
	}

	function detilAction(){
		$user = new Menu();
		$menu = "nilai/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// layout
			$this->_helper->layout()->setLayout('second');
			// Title Browser
			$this->view->title = "Daftar Nilai Mahasiswa";
			// get kd paket kelas
			$kd_paket=$this->_request->get('id');
			$paketkelas = new Paketkelas();
			$getPaketKelas=$paketkelas->getPaketKelasByKd($kd_paket);
			if($getPaketKelas){
				foreach ($getPaketKelas as $dtPaket) {
					$kdKelas = $dtPaket['kd_kelas'];
					$this->view->kd_kelas = $dtPaket['kd_kelas'];
					$this->view->kd_paket_kelas = $dtPaket['kd_paket_kelas'];
					$this->view->nm_prodi=$dtPaket['nm_prodi_kur'];
					$this->view->kd_per=$dtPaket['kd_periode'];
					$this->view->nm_kelas=$dtPaket['nm_kelas'];
					$this->view->jns_kelas=$dtPaket['jns_kelas'];
					$this->view->nm_dsn=$dtPaket['nm_dosen'];
					$this->view->nm_mk=$dtPaket['nm_mk'];
					$this->view->kd_mk=$dtPaket['kode_mk'];
					$this->view->sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
				}
				$kelas = new Kelas();
				$getKelas = $kelas->getKelasByKd($kdKelas);
				foreach ($getKelas as $dtKls) {
					$this->view->nm_p1=$dtKls['nm_p1'];
					$this->view->nm_p2=$dtKls['nm_p2'];
					$this->view->nm_p3=$dtKls['nm_p3'];
					$this->view->nm_p4=$dtKls['nm_p4'];
					$this->view->nm_p5=$dtKls['nm_p5'];
					$this->view->nm_p6=$dtKls['nm_p6'];
					$this->view->nm_p7=$dtKls['nm_p7'];
					$this->view->nm_p8=$dtKls['nm_p8'];
					$this->view->p_p1=$dtKls['p_p1'];
					$this->view->p_p2=$dtKls['p_p2'];
					$this->view->p_p3=$dtKls['p_p3'];
					$this->view->p_p4=$dtKls['p_p4'];
					$this->view->p_uts=$dtKls['p_uts'];
					$this->view->p_p5=$dtKls['p_p5'];
					$this->view->p_p6=$dtKls['p_p6'];
					$this->view->p_p7=$dtKls['p_p7'];
					$this->view->p_p8=$dtKls['p_p8'];
					$this->view->p_uas=$dtKls['p_uas'];
					$this->view->p_tot=$dtKls['p_p1']+$dtKls['p_p2']+$dtKls['p_p3']+$dtKls['p_p4']+$dtKls['p_p5']+$dtKls['p_p6']+$dtKls['p_p7']+$dtKls['p_p8']+$dtKls['p_uts']+$dtKls['p_uas'];
				}
				$nilai = new Nilai();
				$getNilai = $nilai->getNilaiByPaket($kd_paket);
				$this->view->listNilai=$getNilai;
				// navigation
				$this->_helper->navbar('nilai/list',0,0,'nilai/export?id='.$kd_paket,'nilai/upload?id='.$kd_paket);
			}else{
				$this->view->eksis="f";
				// navigation
				$this->_helper->navbar('nilai/list',0,0,0,0);
			}
		}
	}

	function newAction(){
		$user = new Menu();
		$menu = "nilai/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get kd paket kelas
			$kd_kuliah=$this->_request->get('id');
			$nilai = new Nilai();
			$getNilai = $nilai->getNilaiByKd($kd_kuliah);
			if($getNilai){
				foreach ($getNilai as $dataNilai) {
					$this->view->nim=$dataNilai['nim'];
					$this->view->nm=$dataNilai['nm_mhs'];
					$kd_paket=$dataNilai['kd_paket_kelas'];
					$this->view->kd_kelas=$dataNilai['kd_kelas'];
					$kdKelas=$dataNilai['kd_kelas'];
					$this->view->kd_kuliah=$dataNilai['kd_kuliah'];
					$this->view->nm_prodi=$dataNilai['nm_prodi'];
					$kdProdi=$dataNilai['kd_prodi'];
					$this->view->kd_per=$dataNilai['kd_periode'];
					$kdPeriode=$dataNilai['kd_periode'];
					$this->view->nm_kelas=$dataNilai['nm_kelas'];
					$this->view->jns_kelas=$dataNilai['jns_kelas'];
					$this->view->kd_dsn=$dataNilai['kd_dosen'];
					$this->view->nm_dsn=$dataNilai['nm_dosen'];
					$this->view->kd_mk=$dataNilai['kode_mk'];
					$this->view->nm_mk=$dataNilai['nm_mk'];
					$this->view->sks=$dataNilai['sks_tm']+$dataNilai['sks_prak']+$dataNilai['sks_prak_lap']+$dataNilai['sks_sim'];
					$this->view->p1=$dataNilai['p1'];
					$this->view->p2=$dataNilai['p2'];
					$this->view->p3=$dataNilai['p3'];
					$this->view->p4=$dataNilai['p4'];
					$this->view->p5=$dataNilai['p5'];
					$this->view->p6=$dataNilai['p6'];
					$this->view->p7=$dataNilai['p7'];
					$this->view->p8=$dataNilai['p8'];
					$this->view->uts=$dataNilai['uts'];
					$this->view->uas=$dataNilai['uas'];
					$this->view->nilai_tot=$dataNilai['nilai_tot'];
					$this->view->index=$dataNilai['index'];
					if($dataNilai['status']=='1'){
						$this->view->status="FIX";
						$this->view->label="success";
					}elseif ($dataNilai['status']=='0'){
						$this->view->status="BELUM FIX";
						$this->view->label="danger";
					}else{
						$this->view->status="DITUNDA";
						$this->view->label="warning";
					}
				}
				// kelas
				$kelas = new Kelas();
				$getKelas = $kelas->getKelasByKd($kdKelas);
				foreach ($getKelas as $dtKls) {
					$this->view->nm_p1=$dtKls['nm_p1'];
					$this->view->nm_p2=$dtKls['nm_p2'];
					$this->view->nm_p3=$dtKls['nm_p3'];
					$this->view->nm_p4=$dtKls['nm_p4'];
					$this->view->nm_p5=$dtKls['nm_p5'];
					$this->view->nm_p6=$dtKls['nm_p6'];
					$this->view->nm_p7=$dtKls['nm_p7'];
					$this->view->nm_p8=$dtKls['nm_p8'];
					$this->view->p_p1=$dtKls['p_p1'];
					$this->view->p_p2=$dtKls['p_p2'];
					$this->view->p_p3=$dtKls['p_p3'];
					$this->view->p_p4=$dtKls['p_p4'];
					$this->view->p_uts=$dtKls['p_uts'];
					$this->view->p_p5=$dtKls['p_p5'];
					$this->view->p_p6=$dtKls['p_p6'];
					$this->view->p_p7=$dtKls['p_p7'];
					$this->view->p_p8=$dtKls['p_p8'];
					$this->view->p_uas=$dtKls['p_uas'];
					$this->view->p_tot=$dtKls['p_p1']+$dtKls['p_p2']+$dtKls['p_p3']+$dtKls['p_p4']+$dtKls['p_p5']+$dtKls['p_p6']+$dtKls['p_p7']+$dtKls['p_p8']+$dtKls['p_uts']+$dtKls['p_uas'];
					$this->view->note=$dtKls['note_dosen'];
				}
				// aturan nilai
				$aturanNilai = new AturanNilai();
				$this->view->listAturan = $aturanNilai->getAturanNilaiByProdiPeriode($kdProdi,$kdPeriode);
				// navigation
				$this->_helper->navbar('nilai/detil?id='.$kd_paket,0,0,0,0);
			}else{
				$this->view->eksis="f";
			}
			$this->view->title="Data Nilai Mahasiswa";
		}
	}

	function uploadAction(){
		$user = new Menu();
		$menu = "nilai/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// message
			$message = new Zend_Session_Namespace('message');
			$this->view->msg = $message->message;
			$kd_paket = $this->_request->get('id');
			// get kd paket kelas
			$kd_paket=$this->_request->get('id');
			$paketkelas = new Paketkelas();
			$getPaketKelas=$paketkelas->getPaketKelasByKd($kd_paket);
			if($getPaketKelas){
				foreach ($getPaketKelas as $dtPaket) {
					$kdKelas = $dtPaket['kd_kelas'];
					$this->view->kd_paket_kelas = $dtPaket['kd_paket_kelas'];
					$this->view->kd_kelas = $dtPaket['kd_kelas'];
					$this->view->nm_prodi=$dtPaket['nm_prodi_kur'];
					$this->view->kd_per=$dtPaket['kd_periode'];
					$this->view->nm_kelas=$dtPaket['nm_kelas'];
					$this->view->jns_kelas=$dtPaket['jns_kelas'];
					$this->view->nm_dsn=$dtPaket['nm_dosen'];
					$this->view->nm_mk=$dtPaket['nm_mk'];
					$this->view->kd_mk=$dtPaket['kode_mk'];
					$this->view->sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
				}
			}else{
				$this->view->eksis="f";
			}
			$this->view->title="Upload Data Nilai Mahasiswa";
			// navigation
			$this->_helper->navbar('nilai/detil?id='.$kd_paket,0,0,0,0);
			// uploading
			if ($this->_request->isPost()){
				$temp = explode(".", $_FILES["fileToUpload"]["name"]);
				$newfilename = md5(round(microtime(true))) . '.' . end($temp);
				$x=rand(100000,999999);
				$path = __FILE__;
				$filePath = str_replace('controllers\NilaiController.php','uploads',$path);
				$target_dir = $filePath;
				$target_file = str_replace("'", "", $target_dir ."\'". $newfilename);
				$uploadOk = 1;
				$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
				$mimes = array('application/vnd.ms-excel');
				if(!in_array($_FILES['fileToUpload']['type'],$mimes)){
				   	$msg="File must be excel!";
				   	$uploadOk = 0;
				} 
				// Check if file already exists
				if (file_exists($target_file)) {
				    $msg= "Sorry, file already exists.";
				    $uploadOk = 0;
				}
				// Check file size
				if ($_FILES["fileToUpload"]["size"] > 1000000) {
				    $msg= "Sorry, your file is too large.";
				    $uploadOk = 0;
				}
				// Check if $uploadOk is set to 0 by an error
				$message = new Zend_Session_Namespace('message');
				if ($uploadOk == 0) {
					$message->message=$msg;
				} else {
				    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
				        $uploadOk = 1;
				        // to database
				        $objPHPExcel = PHPExcel_IOFactory::load($target_file);
				        $objPHPExcel->setActiveSheetIndex(0);
						$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
						$jml_row = $objPHPExcel->getActiveSheet()->getHighestRow()-1;
						$kuliah = new Kuliah();
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
						if ($arr_data) {
							$n=1;
							$n_error=0;
							$msg_error="";
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
									$msg_error="Data mahasiswa baris ke ".$n." tidak valid! Gunakan template yang sudah diunduh<br>".$msg_error;
									$n_error++;
								}
								if(($p1>100)or($p2>100)or($p3>100)or($p4>100)or($p5>100)or($p6>100)or($p7>100)or($p8>100)or($uts>100)or($uas>100)){
									$msg_error="Baris ke-".$n.": Ada nilai yang lebih dari 100<br>".$msg_error;
									$n_error++;
								}
								$n++;
							}
							$n=1;
							$msg_ins="";
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
									$msg_ins="Baris ke-".$n." : ".$updNilai."<br>".$msg_ins;
									$n++;
								}
								unlink('application/uploads/'.$newfilename);
								$message->message=$msg_ins;
							}else{
								$message->message=$msg_error;
							}
						}
				    } else {
				        $msg= "Sorry, there was an error uploading your file.";
						$message->message=$msg;
				    }
				    $message->setExpirationSeconds(5);
				    $this->_redirect('nilai/upload?id='.$kd_paket);
				}
			}
		}
	}

	function exportAction(){
		$user = new Menu();
		$menu = "nilai/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// layout
			$this->_helper->layout->disableLayout();
			// get kd paket kelas
			$kd_paket=$this->_request->get('id');
			$paketkelas = new Paketkelas();
			$getPaketKelas=$paketkelas->getPaketKelasByKd($kd_paket);
			if($getPaketKelas){
				foreach ($getPaketKelas as $dtPaket) {
					$kdKelas = $dtPaket['kd_kelas'];
					$kd_kelas = $dtPaket['kd_kelas'];
					$nm_prodi=$dtPaket['nm_prodi_kur'];
					$kd_per=$dtPaket['kd_periode'];
					$nm_kelas=$dtPaket['nm_kelas'];
					$jns_kelas=$dtPaket['jns_kelas'];
					$nm_dsn=$dtPaket['nm_dosen'];
					$nm_mk=$dtPaket['nm_mk'];
					$kd_mk=$dtPaket['kode_mk'];
					$sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
				}
				$kelas = new Kelas();
				$getKelas = $kelas->getKelasByKd($kdKelas);
				foreach ($getKelas as $dtKls) {
					$nm_p1=$dtKls['nm_p1'];
					$nm_p2=$dtKls['nm_p2'];
					$nm_p3=$dtKls['nm_p3'];
					$nm_p4=$dtKls['nm_p4'];
					$nm_p5=$dtKls['nm_p5'];
					$nm_p6=$dtKls['nm_p6'];
					$nm_p7=$dtKls['nm_p7'];
					$nm_p8=$dtKls['nm_p8'];
					$p_p1=$dtKls['p_p1'];
					$p_p2=$dtKls['p_p2'];
					$p_p3=$dtKls['p_p3'];
					$p_p4=$dtKls['p_p4'];
					$p_uts=$dtKls['p_uts'];
					$p_p5=$dtKls['p_p5'];
					$p_p6=$dtKls['p_p6'];
					$p_p7=$dtKls['p_p7'];
					$p_p8=$dtKls['p_p8'];
					$p_uas=$dtKls['p_uas'];
					$p_tot=$dtKls['p_p1']+$dtKls['p_p2']+$dtKls['p_p3']+$dtKls['p_p4']+$dtKls['p_p5']+$dtKls['p_p6']+$dtKls['p_p7']+$dtKls['p_p8']+$dtKls['p_uts']+$dtKls['p_uas'];
				}
				$nilai = new Nilai();
				$getNilai = $nilai->getNilaiByPaket($kd_paket);
				// export excel
				// konfigurasi excel
				PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
				$objPHPExcel = new PHPExcel();
				$ses_ac = new Zend_Session_Namespace('ses_ac');
				$nm_pt = $ses_ac->nm_pt;
				$objPHPExcel->getProperties()->setCreator($nm_pt)
											 ->setLastModifiedBy("Akademik")
											 ->setTitle("Nilai Mahasiswa")
											 ->setSubject("Sistem Informasi Akademik")
											 ->setDescription("Daftar Nilai Mahasiswa")
											 ->setKeywords("daftar nilai")
											 ->setCategory("Data File");
											 
				// Rename sheet
				$objPHPExcel->getActiveSheet()->setTitle('Export Daftar Nilai');
				$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
															  ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
															  ->setFitToWidth('1')
															  ->setFitToHeight('Automatic')
															  ->SetHorizontalCentered(true);
				// margin is set in inches (0.5cm)
				$margin = 0.5 / 2.54;											  
				$objPHPExcel->getActiveSheet()->getPageMargins()->setTop($margin)
																->setBottom($margin)
																->setLeft($margin)
																->setRight($margin);	
				//set Layout
				$border = array(
				  'borders' => array(
						'allborders' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				);
				$cell_color = array(
		 		   'fill' => array(
				        'type' => PHPExcel_Style_Fill::FILL_SOLID,
				        'color' => array('rgb'=>'CCCCCC')
				    ),
				);
				// properties field excel;
				$objPHPExcel->getActiveSheet()->mergeCells('A1:Q1');
				$objPHPExcel->getActiveSheet()->mergeCells('A2:Q2');
				$objPHPExcel->getActiveSheet()->mergeCells('A3:B3');
				$objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
				$objPHPExcel->getActiveSheet()->mergeCells('A5:B5');
				$objPHPExcel->getActiveSheet()->mergeCells('A6:B6');
				$objPHPExcel->getActiveSheet()->mergeCells('A7:B7');
				$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setSize(14);
				$objPHPExcel->getActiveSheet()->getStyle('A1:Q9')->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(9);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(18);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(0);
				$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A8:Q9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A8:Q9')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				// insert data to excel
				$objPHPExcel->getActiveSheet()->setCellValue('A1','DAFTAR NILAI MAHASISWA');
				$objPHPExcel->getActiveSheet()->setCellValue('A2',strtoupper($nm_pt));
				$objPHPExcel->getActiveSheet()->setCellValue('A3','PROGRAM STUDI');
				$objPHPExcel->getActiveSheet()->setCellValue('A4','PERIODE');
				$objPHPExcel->getActiveSheet()->setCellValue('A5','KELAS');
				$objPHPExcel->getActiveSheet()->setCellValue('A6','DOSEN');
				$objPHPExcel->getActiveSheet()->setCellValue('A7','MATA KULIAH');
				$objPHPExcel->getActiveSheet()->setCellValue('C3',$nm_prodi);
				$objPHPExcel->getActiveSheet()->setCellValue('C4',$kd_per);
				$objPHPExcel->getActiveSheet()->setCellValue('C5',$nm_kelas."(".$jns_kelas.")");
				$objPHPExcel->getActiveSheet()->setCellValue('C6',$nm_dsn);
				$objPHPExcel->getActiveSheet()->setCellValue('C7',$nm_mk."(".$kd_mk.")-".$sks." SKS");
				$objPHPExcel->getActiveSheet()->mergeCells('A9:A10');
				$objPHPExcel->getActiveSheet()->setCellValue('A9','No');
				$objPHPExcel->getActiveSheet()->mergeCells('B9:B10');
				$objPHPExcel->getActiveSheet()->setCellValue('B9','NPM');
				$objPHPExcel->getActiveSheet()->mergeCells('C9:C10');
				$objPHPExcel->getActiveSheet()->setCellValue('C9','Nama');
				$objPHPExcel->getActiveSheet()->mergeCells('D9:D10');
				$objPHPExcel->getActiveSheet()->setCellValue('D9','Angkatan');
				$objPHPExcel->getActiveSheet()->setCellValue('E9',$nm_p1);
				$objPHPExcel->getActiveSheet()->setCellValue('F9',$nm_p2);
				$objPHPExcel->getActiveSheet()->setCellValue('G9',$nm_p3);
				$objPHPExcel->getActiveSheet()->setCellValue('H9',$nm_p4);
				$objPHPExcel->getActiveSheet()->setCellValue('I9',"UTS");
				$objPHPExcel->getActiveSheet()->setCellValue('J9',$nm_p5);
				$objPHPExcel->getActiveSheet()->setCellValue('K9',$nm_p6);
				$objPHPExcel->getActiveSheet()->setCellValue('L9',$nm_p7);
				$objPHPExcel->getActiveSheet()->setCellValue('M9',$nm_p8);
				$objPHPExcel->getActiveSheet()->setCellValue('N9',"UAS");
				$objPHPExcel->getActiveSheet()->setCellValue('O9',"Total");
				$objPHPExcel->getActiveSheet()->getStyle('E9:O9') ->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->setCellValue('E10',$p_p1."%");
				$objPHPExcel->getActiveSheet()->setCellValue('F10',$p_p2."%");
				$objPHPExcel->getActiveSheet()->setCellValue('G10',$p_p3."%");
				$objPHPExcel->getActiveSheet()->setCellValue('H10',$p_p4."%");
				$objPHPExcel->getActiveSheet()->setCellValue('I10',$p_uts."%");
				$objPHPExcel->getActiveSheet()->setCellValue('J10',$p_p5."%");
				$objPHPExcel->getActiveSheet()->setCellValue('K10',$p_p6."%");
				$objPHPExcel->getActiveSheet()->setCellValue('L10',$p_p7."%");
				$objPHPExcel->getActiveSheet()->setCellValue('M10',$p_p8."%");
				$objPHPExcel->getActiveSheet()->setCellValue('N10',$p_uas."%");
				$objPHPExcel->getActiveSheet()->setCellValue('O10',$p_tot."%");
				$objPHPExcel->getActiveSheet()->mergeCells('P9:P10');
				$objPHPExcel->getActiveSheet()->setCellValue('P9','Indeks');
				$objPHPExcel->getActiveSheet()->mergeCells('Q9:Q10');
				$objPHPExcel->getActiveSheet()->setCellValue('Q9','Bobot');
				$i=11;
				$n=1;
				foreach ($getNilai as $data){
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$data['nim']);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$data['nm_mhs']);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$data['id_angkatan']);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$data['p1']);
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$data['p2']);
					$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$data['p3']);
					$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$data['p4']);
					$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$data['uts']);
					$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$data['p5']);
					$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,$data['p6']);
					$objPHPExcel->getActiveSheet()->setCellValue('L'.$i,$data['p7']);
					$objPHPExcel->getActiveSheet()->setCellValue('M'.$i,$data['p8']);
					$objPHPExcel->getActiveSheet()->setCellValue('N'.$i,$data['uas']);
					$objPHPExcel->getActiveSheet()->setCellValue('O'.$i,$data['nilai_tot']);
					$arrIndex = explode('/', $data['index']);
					$objPHPExcel->getActiveSheet()->setCellValue('P'.$i,$arrIndex[0]);
					$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i,$arrIndex[1]);
					$objPHPExcel->getActiveSheet()->setCellValue('R'.$i,$data['kd_kuliah']);
					$objPHPExcel->getActiveSheet()->getStyle('R'.$i)->applyFromArray(array('font'  => array('color'=>array('rgb'=>'FFFFFF'))));
					$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':N'.$i)->applyFromArray($cell_color);
					$i++;
					$n++;
				}
				$objPHPExcel->getActiveSheet()->getStyle('A9:Q'.($i-1))->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('A9:Q'.($i-1))->getFont()->setSize(12);
				$objPHPExcel->getActiveSheet()->getStyle('E11:O'.($i-1))->getNumberFormat()->setFormatCode('#,##0.00');
				// Redirect output to a clientâ€™s web browser (Excel5)
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="Daftar Nilai Mahasiswa.xls"');
				header('Cache-Control: max-age=0');
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$objWriter->save('php://output');
			}else{
				
			}
		}
	}
	
	function khsAction(){
		$user = new Menu();
		$menu = "nilai/khs";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Kartu Hasil Studi Mahasiswa";
			// treeview
			$this->view->active_tree="07";
			$this->view->active_menu="nilai/khs";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// destroy session param
			Zend_Session::namespaceUnset('param_khs');
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
			// get data periode
			$periode = new Periode();
			$this->view->listPeriode=$periode->fetchAll();
			// get data angkatan
			$akt = new Angkatan();
			$this->view->listAkt=$akt->fetchAll();
		}
	}
	
	function pkhsAction(){
		$user = new Menu();
		$menu = "nilai/khs";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Kartu Hasil Studi Mahasiswa";
			// layout
			$this->_helper->layout()->setLayout('second');
			// navigation
			$this->_helper->navbar('nilai/khs',0,0,'nilai/ekhs',0);
			// treeview
			$this->view->active_tree="07";
			$this->view->active_menu="nilai/khs";
			// session
			$param = new Zend_Session_Namespace('param_khs');
			$nim = $param->nim;
			$kd_periode = $param->per;
			// get data mahasiswa
			$mhs = new Mahasiswa();
			$getMhs = $mhs->getMahasiswaByNim($nim);
			foreach ($getMhs as $dtMhs) {
				$nim=$dtMhs['nim'];
				$nm=$dtMhs['nm_mhs'];
				$akt=$dtMhs['id_angkatan'];
				$kd_prd=$dtMhs['kd_prodi'];
				$nm_prd=$dtMhs['nm_prodi'];
				$dw = $dtMhs['nm_dosen_wali'];
			}
			$this->view->nim=$nim;
			$this->view->nm=$nm;
			$this->view->akt=$akt;
			$this->view->kd_prd=$kd_prd;
			$this->view->nm_prd=$nm_prd;
			$this->view->dw=$dw;
			$this->view->per=$kd_periode;
			// get data nilai
			$nilai=new Nilai();
			$getNilai=$nilai->getNilaiByNimPeriode($nim, $kd_periode);
			$arrPer=array();
			foreach ($getNilai as $dtNilai){
				$arrPer[]=$dtNilai['kd_periode'];
			}
			$arrPer=array_values(array_unique($arrPer));
			$this->view->listPer=$arrPer;
			$this->view->listNilai=$getNilai;
			$this->view->x=count($getNilai);
			// create session for excel
			$param->datakhs=$getNilai;
			$param->arrPer=$arrPer;
			$param->nim=$nim;
			$param->nm_mhs=$nm;
			$param->prd=$nm_prd;
			$param->akt=$akt;
			$param->dw=$dw;
		}
	}
	
	function ekhsAction(){
		$user = new Menu();
		$menu = "nilai/khs";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// session
			$param = new Zend_Session_Namespace('param_khs');
			$dataNilai = $param->datakhs;
			$arrPer=$param->arrPer;
			$nim=$param->nim;
			$nm=$param->nm_mhs;
			$prd=$param->prd;
			$akt=$param->akt;
			$dw=$param->dw;
			// image path logo
			$path = __FILE__;
			$imgPath = str_replace('academic/application/controllers/NilaiController.php','public/img/logo.png',$path);
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Administrator")
										 ->setLastModifiedBy("Akademik")
										 ->setTitle("Export KHS")
										 ->setSubject("Sistem Informasi Akademik")
										 ->setDescription("Kartu Hasil Studi")
										 ->setKeywords("khs")
										 ->setCategory("Data File"); 
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Kartu Hasil Studi');
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT)
														  ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
														  ->setFitToWidth('1')
														  ->setFitToHeight('Automatic')
														  ->SetHorizontalCentered(true);
			
			// margin is set in inches (1 cm)
			$margin = 0.5 / 2.54;											  
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop($margin)
															->setBottom($margin)
															->setLeft($margin)
															->setRight($margin);	
	
			//set Layout
			$border = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)));
			$borderDouble = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE)));
			$borderInside = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
			$borderLeft = array('borders' => array('left' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)));
			$borderBottom = array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE)));
			$cell_color = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb'=>'CCCCCC')));
			// column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
			// height
			$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(25);
			$objPHPExcel->getActiveSheet()->getRowDimension(3)->setRowHeight(25);
			
			// drawing logo
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Logo');
			$objDrawing->setDescription('Logo');
			$objDrawing->setPath($imgPath);
			$objDrawing->setHeight(55);
			$objDrawing->setWidth(75);
			$objDrawing->setCoordinates('C1');
			$objDrawing->setOffsetX(15);
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			
			// properties field excel;
			$objPHPExcel->getActiveSheet()->mergeCells('F1:J1');
			$objPHPExcel->getActiveSheet()->mergeCells('C2:J2');
			$objPHPExcel->getActiveSheet()->mergeCells('C3:J3');
			$objPHPExcel->getActiveSheet()->mergeCells('E4:J4');
			$objPHPExcel->getActiveSheet()->mergeCells('E5:J5');
			$objPHPExcel->getActiveSheet()->mergeCells('E6:J6');
			$objPHPExcel->getActiveSheet()->mergeCells('E7:J7');
			$objPHPExcel->getActiveSheet()->mergeCells('C4:D4');
			$objPHPExcel->getActiveSheet()->mergeCells('C5:D5');
			$objPHPExcel->getActiveSheet()->mergeCells('C6:D6');
			$objPHPExcel->getActiveSheet()->mergeCells('C7:D7');
			$objPHPExcel->getActiveSheet()->mergeCells('C8:D8');
			
			$objPHPExcel->getActiveSheet()->getStyle('C2:I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C2:I3')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('C4:I8')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('C2:I8')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C1:I1')->getFont()->setSize(8);
			$objPHPExcel->getActiveSheet()->getStyle('C1:I1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('F1:I1')->getFont()->setItalic(true);
			$objPHPExcel->getActiveSheet()->getStyle('F1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('C3:J3')->applyFromArray(array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE))));
	
			// insert data to excel
			$objPHPExcel->getActiveSheet()->setCellValue('F1','Printed by Administrator : '. date("d m Y h:i:s"));
			$objPHPExcel->getActiveSheet()->setCellValue('C2',strtoupper($this->nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('C3','KARTU HASIL STUDI');
			$objPHPExcel->getActiveSheet()->setCellValue('C5','Nama');
			$objPHPExcel->getActiveSheet()->setCellValue('C6','NPM');
			$objPHPExcel->getActiveSheet()->setCellValue('C7','Prodi/Angkatan');
			$objPHPExcel->getActiveSheet()->setCellValue('C8','Dosen Wali');
			
			$objPHPExcel->getActiveSheet()->setCellValue('E5',': '.$nm);
			$objPHPExcel->getActiveSheet()->setCellValue('E6',': '.$nim);
			$objPHPExcel->getActiveSheet()->setCellValue('E7',': '.$prd." / ".$akt);
			$objPHPExcel->getActiveSheet()->setCellValue('E8',': '.$dw);
				
			$i=10;
			foreach ($arrPer as $dtPer){
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$dtPer);	
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->getFont()->setBold(true);
				$i++;
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,'No');	
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,'Kode');
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,'Mata Kuliah');	
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,'SKS');
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,'Taken');
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,'Nilai');						
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,'AM');	
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,'K x A');
				//konfigurasi field
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':J'.$i)->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':J'.$i)->applyFromArray($cell_color);	
				$objPHPExcel->getActiveSheet()->mergeCells('C'.($i-1).':D'.($i-1));
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':J'.$i)->getFont()->setBold(true);	
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':J'.$i)->applyFromArray($borderBottom);
				$i++;
				$rowAwal=$i;
				$n=1;
				foreach ($dataNilai as $dataExport) {
					if($dataExport['kd_periode']==$dtPer){
						$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$n);	
						$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$dataExport['kode_mk']);
						$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$dataExport['nm_mk']);	
						$sks_def=$dataExport['sks_tm']+$dataExport['sks_prak']+$dataExport['sks_prak_lap']+$dataExport['sks_sim'];
						$sks_take=$dataExport['sks_tm']+$dataExport['sks_prak']+$dataExport['sks_prak_lap']+$dataExport['sks_sim']-$dataExport['sks_deducted'];
						$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$sks_def);
						$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$sks_take);
						$arrIndeks=explode('/', $dataExport['index']);
						if($dataExport['status']==1){
							$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$arrIndeks[0]);
							$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$arrIndeks[1]);
							$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$sks_take*$arrIndeks[1]);
						}else{
							$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,'-');
							$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,'-');
							$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,0);
						}
						//konfigurasi field
						$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':J'.$i)->applyFromArray($borderInside);
						$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray($borderLeft);
						$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->applyFromArray($borderLeft);
						$n++;
						$i++;
					}
				}
				if ($n==1){
					//
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,'IPS');
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,'=J'.$i.'/G'.$i);
					$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()->setFormatCode('#,##0.00');
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,'=SUM(F'.$rowAwal.':F'.($i-1).')');
					$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,'=SUM(G'.$rowAwal.':G'.($i-1).')');
					$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,'=SUM(J'.$rowAwal.':J'.($i-1).')');
					// konfigurasi field excel
					$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':J'.$i)->getFont()->setBold(true);
					$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($border);
					$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray($border);
					$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray($border);
					$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->applyFromArray($borderDouble);
				}
				$i=$i+2;
				
			}
			
			$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
			$objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
			$objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
			$objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);
			
			//Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Kartu Hasil Studi.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}
	
	function transkripAction(){
		$user = new Menu();
		$menu = "nilai/transkrip";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Transkrip Nilai Mahasiswa";
			// treeview
			$this->view->active_tree="07";
			$this->view->active_menu="nilai/transkrip";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// destroy session param
			Zend_Session::namespaceUnset('param_trkp');
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
			// get data angkatan
			$akt = new Angkatan();
			$this->view->listAkt=$akt->fetchAll();
		}
	}
	
	function ptranskripAction(){
		$user = new Menu();
		$menu = "nilai/transkrip";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Transkrip Nilai Mahasiswa";
			// layout
			$this->_helper->layout()->setLayout('second');
			// navigation
			$this->_helper->navbar('nilai/transkrip',0,0,'nilai/etranskrip',0);
			// treeview
			$this->view->active_tree="07";
			$this->view->active_menu="nilai/transkrip";
			// session
			$param = new Zend_Session_Namespace('param_trkp');
			$nim = $param->nim;
			$smtPil = $param->smt;
			$this->view->smtPil=$smtPil;
			$kd_periode="";
			// get data mahasiswa
			$mhs = new Mahasiswa();
			$getMhs = $mhs->getMahasiswaByNim($nim);
			foreach ($getMhs as $dtMhs) {
				$nm=$dtMhs['nm_mhs'];
				$akt=$dtMhs['id_angkatan'];
				$kd_prd=$dtMhs['kd_prodi'];
				$nm_prd=$dtMhs['nm_prodi'];
				$tgllhr=$dtMhs['tgl_lahir_fmt'];
				$tmplhr=$dtMhs['tmp_lahir'];
				$idmhs=$dtMhs['id_mhs'];
				$tglkeluar=$dtMhs['tgl_keluar_fmt'];
			}
			$this->view->nim=$nim;
			$this->view->nm=$nm;
			$this->view->akt=$akt;
			$this->view->kd_prd=$kd_prd;
			$this->view->nm_prd=$nm_prd;
			// get kurikulum mahasiswa
			$nilai = new Nilai();
			$getKurikulumMhs=$nilai->getTranskripKurikulumByNim($nim);
			$kur_mhs="";
			if($getKurikulumMhs){
				foreach($getKurikulumMhs as $dtKurMhs){
					$kur_mhs=$dtKurMhs['id_kurikulum'];
				}
			}
			// smt kur
			$kurikulum = new Kurikulum();
			$getKur=$kurikulum->getKurById($kur_mhs);
			$nSmt=0;
			foreach ($getKur as $dtKur){
				$nSmt=$dtKur['smt_normal'];
				$this->view->smt=$dtKur['smt_normal'];
			}
			// get data transkrip
			$nilaiTrkp=$nilai->getTranskripByNimKurikulum($nim,$kur_mhs);
			$this->view->listNilai= $nilaiTrkp;
			// create session for excel
			$param->data=$nilaiTrkp;
			$param->nSmt=$nSmt;
			$param->nim=$nim;
			$param->nm_mhs=$nm;
			$param->prd=$nm_prd;
			$param->akt=$akt;
			$param->tgllhr=$tgllhr;
			$param->tmplhr=$tmplhr;
			$param->idmhs=$idmhs;
			$param->tglkeluar=$tglkeluar;
		}
	}
	
	function etranskripAction(){
		$user = new Menu();
		$menu = "nilai/transkrip";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// session
			$param = new Zend_Session_Namespace('param_trkp');
			$dataNilai = $param->data;
			$nSmt=$param->nSmt;
			$nim=$param->nim;
			$nm=$param->nm_mhs;
			$prd=$param->prd;
			$akt=$param->akt;
			$smtPil=$param->smt;
			// image path logo
			$path = __FILE__;
			$imgPath = str_replace('academic/application/controllers/NilaiController.php','public/img/logo.png',$path);
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Administrator")
										 ->setLastModifiedBy("Akademik")
										 ->setTitle("Export Transkrip Nilai")
										 ->setSubject("Sistem Informasi Akademik")
										 ->setDescription("Transkrip Nilai")
										 ->setKeywords("transkrip nilai")
										 ->setCategory("Data File"); 
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Transkrip Nilai');
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT)
														  ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
														  ->setFitToWidth('1')
														  ->setFitToHeight('Automatic')
														  ->SetHorizontalCentered(true);
			
			// margin is set in inches (1 cm)
			$margin = 0.5 / 2.54;											  
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop($margin)
															->setBottom($margin)
															->setLeft($margin)
															->setRight($margin);	
	
			//set Layout
			$border = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)));
			$borderDouble = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE)));
			$borderInside = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
			$borderLeft = array('borders' => array('left' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)));
			$borderBottom = array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE)));
			$cell_color = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb'=>'CCCCCC')));
			$yellow_color = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb'=>'FFFF00')));
			// column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(60);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
			// height
			$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(25);
			$objPHPExcel->getActiveSheet()->getRowDimension(3)->setRowHeight(25);
			// properties field excel;
			$objPHPExcel->getActiveSheet()->mergeCells('F1:I1');
			$objPHPExcel->getActiveSheet()->mergeCells('C2:I2');
			$objPHPExcel->getActiveSheet()->mergeCells('C3:I3');
			$objPHPExcel->getActiveSheet()->mergeCells('E4:I4');
			$objPHPExcel->getActiveSheet()->mergeCells('E5:I5');
			$objPHPExcel->getActiveSheet()->mergeCells('E6:I6');
			$objPHPExcel->getActiveSheet()->mergeCells('E7:I7');
			$objPHPExcel->getActiveSheet()->mergeCells('C4:D4');
			$objPHPExcel->getActiveSheet()->mergeCells('C5:D5');
			$objPHPExcel->getActiveSheet()->mergeCells('C6:D6');
			$objPHPExcel->getActiveSheet()->mergeCells('C7:D7');
			
			// drawing logo
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Logo');
			$objDrawing->setDescription('Logo');
			$objDrawing->setPath($imgPath);
			$objDrawing->setHeight(55);
			$objDrawing->setWidth(75);
			$objDrawing->setCoordinates('C1');
			$objDrawing->setOffsetX(15);
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			
			$objPHPExcel->getActiveSheet()->getStyle('C2:I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C2:I3')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('C4:I7')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('C2:I7')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C1:I1')->getFont()->setSize(8);
			$objPHPExcel->getActiveSheet()->getStyle('C1:I1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('F1:I1')->getFont()->setItalic(true);
			$objPHPExcel->getActiveSheet()->getStyle('F1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('C3:I3')->applyFromArray(array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE))));
	
			// insert data to excel
			$objPHPExcel->getActiveSheet()->setCellValue('F1','Printed by Administrator : '. date("d m Y h:i:s"));
			$objPHPExcel->getActiveSheet()->setCellValue('C2',strtoupper($this->nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('C3','TRANSKRIP AKADEMIK');
			$objPHPExcel->getActiveSheet()->setCellValue('C5','Nama');
			$objPHPExcel->getActiveSheet()->setCellValue('C6','NPM');
			$objPHPExcel->getActiveSheet()->setCellValue('C7','Prodi/Angkatan');
			
			$objPHPExcel->getActiveSheet()->setCellValue('E5',': '.$nm);
			$objPHPExcel->getActiveSheet()->setCellValue('E6',': '.$nim);
			$objPHPExcel->getActiveSheet()->setCellValue('E7',': '.$prd." / ".$akt);
				
			$i=9;
			$smtr=0;
			$totSKS_all=0;
			$totKA_all=0;
			if($smtPil=='ALL'){
			for($x=1;$x<=$nSmt;$x++) {
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,'Semester '.$x);	
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->getFont()->setBold(true);
				$i++;
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,'No');	
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,'Kode');
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,'Mata Kuliah');	
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,'SKS');
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,'Nilai');						
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,'AM');	
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,'K x A');
				//konfigurasi field
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->applyFromArray($cell_color);	
				$objPHPExcel->getActiveSheet()->mergeCells('C'.($i-1).':D'.($i-1));
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->getFont()->setBold(true);	
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->applyFromArray($borderBottom);
				$i++;
				$rowAwal=$i;
				$n=1;
				$totSKS_part=0;
				$totKA_part=0;
				foreach ($dataNilai as $dataExport) {
					if($dataExport['smt_def']==$x){
						if((($dataExport['sks_taken'] >= $dataExport['sks_def'])and($dataExport['ori']==1))or($dataExport['ori']==0)){
						// if($dataExport['sks_taken'] >= $dataExport['sks_def']){
							$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$n);	
							$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$dataExport['kode_mk']);
							$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$dataExport['nm_mk']);	
							$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$dataExport['sks_def']);
							if($dataExport['status']==1){
								$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$dataExport['huruf']);
								$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$dataExport['bobot']);
								$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$dataExport['bobot']*$dataExport['sks_def']);
							}else{
								$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,'-');
								$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,'-');
								$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,0);;
							}
							if($dataExport['n_nilai']>1){
								$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->applyFromArray($yellow_color);
							}
							//konfigurasi field
							$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->applyFromArray($borderInside);
							$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray($borderLeft);
							$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray($borderLeft);
							if($dataExport['status']==1){
								$totKA_all=$totKA_all+$dataExport['bobot']*$dataExport['sks_def'];
								$totSKS_all=$totSKS_all+$dataExport['sks_def'];
								$totSKS_part=$totSKS_part+$dataExport['sks_def'];
								$totKA_part=$totKA_part+$dataExport['bobot']*$dataExport['sks_def'];
							}
							$n++;
							$i++;
						}
					}
				}
				if ($n==1){
					//
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,'IPS');
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,'=I'.$i.'/F'.$i);
					$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()->setFormatCode('#,##0.00');
					//$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,'=SUM(F'.$rowAwal.':F'.($i-1).')');
					//$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,'=SUM(I'.$rowAwal.':I'.($i-1).')');
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$totSKS_part);
                                        $objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$totKA_part);
					// konfigurasi field excel
					$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':I'.$i)->getFont()->setBold(true);
					$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($border);
					$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->applyFromArray($border);
					$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->applyFromArray($borderDouble);
				}
				$i=$i+2;
				
			}
			
			// IPK
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+2),'IPK');
			$objPHPExcel->getActiveSheet()->mergeCells('C'.($i+2).':D'.($i+2));
			if($totSKS_all==0){
				$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+2),': '.number_format(0,2,',','.'));	
			}else{
				$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+2),': '.number_format(($totKA_all/$totSKS_all),2,',','.'));
			}
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+2).':E'.($i+2))->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+2).':E'.($i+2))->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
			$objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
			$objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
			$objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);
			}else{
			for($x=$smtPil;$x<=$smtPil;$x++) {
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,'Semester '.$x);	
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->getFont()->setBold(true);
				$i++;
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,'No');	
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,'Kode');
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,'Mata Kuliah');	
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,'SKS');
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,'Nilai');						
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,'AM');	
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,'K x A');
				//konfigurasi field
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->applyFromArray($cell_color);	
				$objPHPExcel->getActiveSheet()->mergeCells('C'.($i-1).':D'.($i-1));
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->getFont()->setBold(true);	
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->applyFromArray($borderBottom);
				$i++;
				$rowAwal=$i;
				$n=1;
				foreach ($dataNilai as $dataExport) {
					if($dataExport['smt_def']==$x){
						if((($dataExport['sks_taken'] >= $dataExport['sks_def'])and($dataExport['ori']==1))or($dataExport['ori']==0)){
						// if($dataExport['sks_taken'] >= $dataExport['sks_def']){
							$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$n);	
							$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$dataExport['kode_mk']);
							$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$dataExport['nm_mk']);	
							$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$dataExport['sks_def']);
							if($dataExport['status']==1){
								$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$dataExport['huruf']);
								$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$dataExport['bobot']);
								$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$dataExport['bobot']*$dataExport['sks_def']);
							}else{
								$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,'-');
								$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,'-');
								$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,0);;
							}
							//konfigurasi field
							$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->applyFromArray($borderInside);
							$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray($borderLeft);
							$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray($borderLeft);
							if($dataExport['status']==1){
								$totKA_all=$totKA_all+$dataExport['bobot']*$dataExport['sks_def'];
								$totSKS_all=$totSKS_all+$dataExport['sks_def'];
							}
							$n++;
							$i++;
						}
					}
				}
				if ($n==1){
					//
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,'IPS');
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,'=I'.$i.'/F'.$i);
					$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()->setFormatCode('#,##0.00');
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,'=SUM(F'.$rowAwal.':F'.($i-1).')');
					$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,'=SUM(I'.$rowAwal.':I'.($i-1).')');
					// konfigurasi field excel
					$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':I'.$i)->getFont()->setBold(true);
					$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($border);
					$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->applyFromArray($border);
					$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->applyFromArray($borderDouble);
				}
				$i=$i+2;
				
			}
			
			// IPK
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+2),'IPK');
			$objPHPExcel->getActiveSheet()->mergeCells('C'.($i+2).':D'.($i+2));
			if($totSKS_all==0){
				$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+2),': '.number_format(0,2,',','.'));	
			}else{
				$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+2),': '.number_format(($totKA_all/$totSKS_all),2,',','.'));
			}
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+2).':E'.($i+2))->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+2).':E'.($i+2))->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
			$objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
			$objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
			$objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);
			}
			//Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Transkrip Nilai.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}
	
	function hsreportAction(){
		$user = new Menu();
		$menu = "nilai/hsreport";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="nilai/hsreport";
			// session chart
			$param = new Zend_Session_Namespace('param_hs_chart');
			$akt = $param->akt;
			$prd = $param->prd;
			$per = $param->per;
			$cht=$param->cht;
			// Title Browser
			$this->view->title = "Report IPS Mahasiswa ".$per;
			if($cht){
				// layout
				$this->_helper->layout()->setLayout('second');
				// navigation
				$this->_helper->navbar("nilai/hsreport",0,0,0,0);
			}else {
				// layout
				$this->_helper->layout()->setLayout('main');
				// navigation
				$this->_helper->navbar(0,0,0,0,0);
			}
	
			// get data angkatan
			$angkatan = new Angkatan();
			$this->view->listAkt=$angkatan->fetchAll();
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
			// get periode
			$periode = new Periode();
			$this->view->listPeriode=$periode->fetchAll();
			if($cht){
				$rep = new Report();
				// axis
				$getTabelX=$rep->getTabel('angkatan');
				$arrTabelX=explode("||", $getTabelX);
				// where axis
				$where=array();
				$whereX[0]['key'] = $arrTabelX[1];
				$whereX[0]['param'] = $akt;
				// data axis
				$arrKolomX=array($arrTabelX[1]);
				$getX= $rep->get($arrTabelX[0], $arrKolomX, $arrKolomX, $arrKolomX, $whereX);
				
				// parameter
				$arrPar=array();
				$ip=0;
				for($k=0;$k<=14;$k++){
					$max=$ip+0.5;
					$arrPar['key'][]=$ip." s/d ".$max;
					$arrPar['label'][]=$ip." s/d ".$max;
					$ip=$ip+0.5;
					$k++;
				}
				
				// data
				$getTabelData=$rep->getTabel('ips');
				$arrTabelData=explode("||", $getTabelData);
				// where data
				$whereD[0]['key'] = $arrTabelX[1];
				$whereD[0]['param'] = $akt;
				$getTabelFil=$rep->getTabel('prodi');
				$arrTabelFil=explode("||", $getTabelFil);
				$whereD[1]['key'] = $arrTabelFil[1];
				$whereD[1]['param'] = $prd;
				$getTabelFil=$rep->getTabel('periode');
				$arrTabelFil=explode("||", $getTabelFil);
				$whereD[2]['key'] = $arrTabelFil[1];
				$whereD[2]['param'] = $per;
				//--
				$arrKolomD=array($arrTabelX[1],'range');
				$orderD=array($arrTabelX[1],'range');
				$groupD=array($arrTabelX[1],'range');
				$getReport= $rep->get($arrTabelData[0], $arrKolomD, $groupD, $orderD,$whereD);
				$this->view->x=$rep->query($arrTabelData[0], $arrKolomD, $groupD, $orderD,$whereD);
				// data
				$array=array();
				$i=0;
				foreach ($getX as $data) {
					$array[$i]['x']=$data[$arrTabelX[1]];
					foreach ($arrPar['key'] as $data2){
						$n=0;
						foreach ($getReport as $data3){
							if(($data3[$arrTabelX[1]]==$data[$arrTabelX[1]])and($data3['range']==$data2)){
								$n=$data3['n'];
							}
						}
						$array[$i][$data2]=$n;
					}
					$i++;
				}
				// view
				$this->view->labels=$arrPar['label'];
				$this->view->key=$arrPar['key'];
				$this->view->data=$array;
				$this->view->chart=$cht;
			}
			// destroy session param
			Zend_Session::namespaceUnset('param_hs_chart');
		}
	}

	function ipkreportAction(){
		$user = new Menu();
		$menu = "nilai/ipkreport";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="nilai/ipkreport";
			// destroy session param export
			// Zend_Session::namespaceUnset('param_ipk_erep');
			// session report
			$param = new Zend_Session_Namespace('param_ipk_rep');
			$akt = $param->akt;
			$prd = $param->prd;
			$nim1=$param->nim1;
			$nim2=$param->nim2;
			// destroy session param
			Zend_Session::namespaceUnset('param_ipk_rep');
			// Title Browser
			if($akt){
				// title
				$this->view->title = "Report IPK Mahasiswa Angkatan ".$akt." Prodi ".$prd;
				$this->_helper->layout()->setLayout('second');
				// navigation
				$this->_helper->navbar("nilai/ipkreport",0,0,"nilai/eipkreport",0);
			}else {
				// title
				$this->view->title = "Report IPK Mahasiswa";
				// layout
				$this->_helper->layout()->setLayout('main');
				// navigation
				$this->_helper->navbar(0,0,0,0,0);
			}
	
			// get data angkatan
			$angkatan = new Angkatan();
			$this->view->listAkt=$angkatan->fetchAll();
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
			if($akt){
				// prodi
				$getProdi=$prodi->getProdiByKd($prd);
				foreach ($getProdi as $dtProdi) {
					$nmPrd=$dtProdi['nm_prodi'];
				}
				$kd_periode="";
				// get mhs
				$mahasiswa= new Mahasiswa();
				$getMhs =$mahasiswa->getMahasiswaByAngkatanProdiRangeNim($akt, $prd, $nim1, $nim2);
				$totMhs=count($getMhs);
				$arrRepIpk=array();
				$nMhs=0;
				$start=microtime(true);
				$end=microtime(true);
				$nilai = new Nilai();
				foreach ($getMhs as $dtMhs){
					$nim=$dtMhs['nim'];
					$arrRepIpk[$nMhs]['nim']=$nim;
					$arrRepIpk[$nMhs]['nm_mhs']=$dtMhs['nm_mhs'];
					$arrRepIpk[$nMhs]['status_mhs']=$dtMhs['status_mhs'];
					$arrRepIpk[$nMhs]['sks_tot']=0;
					$arrRepIpk[$nMhs]['ipk']=0.00;
					// get kurikulum mahasiswa	
					$getKurikulumMhs=$nilai->getTranskripKurikulumByNim($nim);
					$kur_mhs="";
					if($getKurikulumMhs){
						foreach($getKurikulumMhs as $dtKurMhs){
							$kur_mhs=$dtKurMhs['id_kurikulum'];
						}
					}
					// smt kur
					$kurikulum = new Kurikulum();
					$getKur=$kurikulum->getKurById($kur_mhs);
					$nSmt=0;
					foreach ($getKur as $dtKur){
						$nSmt=$dtKur['smt_normal'];
						$this->view->smt=$dtKur['smt_normal'];
					}
					// get data transkrip
					$nilaiTrkp=$nilai->getTranskripByNimKurikulum($nim,$kur_mhs);
					// calculate ipk
					$km_total=0;
					$ipk=0;
					$sks_total=0;
					foreach ($nilaiTrkp as $dtTrkp){
						if((($dtTrkp['sks_taken'] >= $dtTrkp['sks_def'])and($dtTrkp['asal']=='TFR'))or($dtTrkp['asal']=='KRS')){
							$km=floatval($dtTrkp['sks_def']*$dtTrkp['bobot']);
							if($dtTrkp['status']==1){
								$km_total=$km_total+$km;
								$sks_total=$sks_total+$dtTrkp['sks_def'];
							}
						}
					}
					$arrRepIpk[$nMhs]['sks_tot']=$sks_total;
					if($sks_total!=0){
						$ipk=number_format($km_total/$sks_total,2,',','.');
					}else{
						$ipk=0;
					}
					$arrRepIpk[$nMhs]['ipk']=$ipk;
					$nMhs++;
				}
				$end = microtime(true);
				// view
				$this->view->listIpk=$arrRepIpk;
				$this->view->akt=$akt;
				$this->view->totMhs=$totMhs;
				$this->view->mhsNotShown=$totMhs-$nMhs;
				$this->view->elp_time=$end-$start;
				// session for excel
				$param2 = new Zend_Session_Namespace('param_ipk_erep');
				$param2->data=$arrRepIpk;
				$param2->akt=$akt;
				$param2->prd=$nmPrd;
			}
		}
	}
	
	function eipkreportAction(){
		$user = new Menu();
		$menu = "nilai/ipkreport";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// session for export
			$param = new Zend_Session_Namespace('param_ipk_erep');
			$akt=$param->akt;
			$prd=$param->prd;
			$dataRep=$param->data;
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt=$ses_ac->nm_pt;
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt = $ses_ac->nm_pt;
			$objPHPExcel->getProperties()->setCreator($nm_pt)
										 ->setLastModifiedBy("Akademik")
										 ->setTitle("Rekap IPK Mahasiswa")
										 ->setSubject("Sistem Informasi Akademik")
										 ->setDescription("Rekap Perkuliahan")
										 ->setKeywords("rekap ipk")
										 ->setCategory("Data File");
										 
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Rekap IPK');
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT)
														  ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
														  ->setFitToWidth('1')
														  ->setFitToHeight('Automatic')
														  ->SetHorizontalCentered(true);
			// margin is set in inches (0.5cm)
			$margin = 0.5 / 2.54;											  
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop($margin)
															->setBottom($margin)
															->setLeft($margin)
															->setRight($margin);	
			//set Layout
			$border = array(
			  'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$cell_color = array(
	 		   'fill' => array(
			        'type' => PHPExcel_Style_Fill::FILL_SOLID,
			        'color' => array('rgb'=>'CCCCCC')
			    ),
			);
			// properties field excel;
			$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
			$objPHPExcel->getActiveSheet()->mergeCells('A3:F3');
			$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('A2:F3')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A1:F3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getStyle('A1:F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			// insert data to excel
			$objPHPExcel->getActiveSheet()->setCellValue('A1',strtoupper($nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('A2','REKAP INDEKS PRESTASI KUMULATIF');
			$objPHPExcel->getActiveSheet()->setCellValue('A3','PROGRAM STUDI '.$prd.' ANGKATAN '.$akt);
			// data
			$objPHPExcel->getActiveSheet()->setCellValue('A4','NO');
			$objPHPExcel->getActiveSheet()->setCellValue('B4','NIM');
			$objPHPExcel->getActiveSheet()->setCellValue('C4','NAMA MAHASISWA');
			$objPHPExcel->getActiveSheet()->setCellValue('D4','STATUS');
			$objPHPExcel->getActiveSheet()->setCellValue('E4','SKS');
			$objPHPExcel->getActiveSheet()->setCellValue('F4','IPK');
			$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->applyFromArray($cell_color);
			$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$i=5;
			$n=1;
			foreach ($dataRep as $data){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$data['nim']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$data['nm_mhs']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$data['status_mhs']);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$data['sks_tot']);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$data['ipk']);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getNumberFormat()->setFormatCode('#,##0.00');
				$n++;
				$i++;
			}
			
			//Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Rekap IPK.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}

	function matrixAction(){
		$user = new Menu();
		$menu = "nilai/matrix";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// destroy session param export
			Zend_Session::namespaceUnset('param_mtx_erep');
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="nilai/matrix";
			// title
			$this->view->title = "Matriks Nilai Mahasiswa";
			// layout
			$this->_helper->layout()->setLayout('main');
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
	
			// get data angkatan
			$angkatan = new Angkatan();
			$this->view->listAkt=$angkatan->fetchAll();
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
			// get data periode
			$periode= new Periode();
			$this->view->listPeriode=$periode->fetchAll();
		}
	}
	
	function ematrixAction(){
		$user = new Menu();
		$menu = "nilai/matrix";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disable layout
			$this->_helper->layout->disableLayout();
			// session for export
			$param = new Zend_Session_Namespace('param_mtx_erep');
			$akt=$param->akt;
			$prd=$param->prd;
			$per=$param->per;
			// mahasiswa
			$mahasiswa = new Mahasiswa();
			$getMhs = $mahasiswa->getMahasiswaByAngkatanProdi($akt, $prd);
			$listMhs = $getMhs;
			// nilai mhs
			$nilai=new Nilai();
			$arrPaket=array();
			$arrNilai=array();
			$i=0;
			foreach ($getMhs as $dtMhs){
				$listNilai=$nilai->getNilaiByNimPeriode($dtMhs['nim'], $per);
				foreach ($listNilai as $dtNilai){
					$arrPaket[$i]['paket']=$dtNilai['kd_paket_kelas']."||".$dtNilai['kode_mk']."||".$dtNilai['nm_mk']."||".$dtNilai['nm_dosen']."||".$dtNilai['nm_kelas']."/".$dtNilai['jns_kelas'];
					$arrNilai[$i]['kd_paket_kelas']=$dtNilai['kd_paket_kelas'];
					$arrNilai[$i]['nim']=$dtNilai['nim'];
					$arrNilai[$i]['index']=$dtNilai['index'];
					$arrNilai[$i]['status']=$dtNilai['status'];
					$i++;
				}
			}
			// $arrPaket=array_values(array_unique($arrPaket)); // <--- di php 5.3 ke atas tidak perlu, langsung $arrPaket = array_values(array_map("unserialize", array_unique(array_map("serialize", $arrPaket)))); 
			$arrPaket = array_values(array_map("unserialize", array_unique(array_map("serialize", $arrPaket))));
			// ekspor excel
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
					
			$objPHPExcel->getProperties()->setCreator("Administrator")
										 ->setLastModifiedBy("Akademik")
										 ->setTitle("Export Matriks Nilai ")
										 ->setSubject("Sistem Informasi Akademik")
										 ->setDescription("Matriks Nilai")
										 ->setKeywords("matriks nilai")
										 ->setCategory("Data File"); 
										 
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Matriks Nilai');
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
														  ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL)
														  ->setFitToWidth('Automatic')
														  ->setFitToHeight('Automatic')
														  ->SetHorizontalCentered(true);
			
			// margin is set in inches (0.5cm)
			$margin = 0.5 / 2.54;
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop($margin)
															->setBottom($margin)
															->setLeft($margin)
															->setRight($margin);	
			//set Border
			$border = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
			// set array column excel
			$col=array('','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
			$arrColExcel=array();
			for($n=1;$n<=100;$n++){
				if($n<=26){
					$arrColExcel[$n]=$col[$n];
				}else{
					$base=floor($n/26);
					$sisa=$n%26;
					if($sisa==0){
						$sisa=26;
						$base=$base-1;
					}
					$arrColExcel[$n]=$col[$base].''.$col[$sisa];
				}
			}
			// column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(17);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(100);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:B2');
			$objPHPExcel->getActiveSheet()->setCellValue('A1','Matriks Nilai Mahasiswa Angkatan '.$akt.' Prodi '.$prd.' Periode Akademik '.$per);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);	
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($border);
			// looping paket kelas
			$j=3;
			foreach ($arrPaket as $dataPaket){
				$paket=explode('||',$dataPaket['paket']);
				$objPHPExcel->getActiveSheet()->setCellValue($arrColExcel[$j].'2',$paket[1]."/".$paket[2]."/".$paket[3]."/".$paket[4]);
				$objPHPExcel->getActiveSheet()->getColumnDimension($arrColExcel[$j])->setWidth(3);
				$objPHPExcel->getActiveSheet()->getStyle($arrColExcel[$j].'2')->getAlignment()->setTextRotation(90);
				$objPHPExcel->getActiveSheet()->getStyle($arrColExcel[$j].'2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle($arrColExcel[$j].'2')->getFont()->setBold(true);
				$j++;
			}
		
			// looping mahasiswa
			$i=3;
			foreach ($listMhs as $dataMhs){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$dataMhs['nim']);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$dataMhs['nm_mhs']);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->getFont()->setBold(true);
				$i++;
			}
		
			// border
			$objPHPExcel->getActiveSheet()->getStyle('A1:'.$arrColExcel[$j-1].($i-1))->applyFromArray($border);
			// looping nilai
			$a=3;
			foreach ($listMhs as $dataMhs){
				// reset kolom
				$b=3;
				foreach ($arrPaket as $dataPaket){
					$paket=explode('||',$dataPaket['paket']);
					foreach ($arrNilai as $dataNilai){
						if(($dataNilai['nim']==$dataMhs['nim'])and($dataNilai['kd_paket_kelas']==$paket[0])){
							$nilai= explode('/',$dataNilai['index']);
							if ($dataNilai['status']==0){
								$color=array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb'=>'FFFF00')));;
								$objPHPExcel->getActiveSheet()->setCellValue($arrColExcel[$b].$a,$nilai[0]);
							}elseif ($dataNilai['status']==1){
								$color=array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb'=>'00CCFF')));;
								$objPHPExcel->getActiveSheet()->setCellValue($arrColExcel[$b].$a,$nilai[0]);
							}else{
								$color=array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb'=>'FF0000')));;
								$objPHPExcel->getActiveSheet()->setCellValue($arrColExcel[$b].$a,$nilai[0]);
							}
							$objPHPExcel->getActiveSheet()->getStyle($arrColExcel[$b].$a)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$objPHPExcel->getActiveSheet()->getStyle($arrColExcel[$b].$a)->applyFromArray($color);
						}
					}
					// increment kolom
					$b++;
				}
				$a++;
			}
			//$objPHPExcel->getActiveSheet()->setCellValue('A131',$b.'-'.$a.'--'.$j);
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Matriks Nilai.xls"');
			header('Cache-Control: max-age=0');
	
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}

	function rekapAction(){
		$user = new Menu();
		$menu = "nilai/rekap";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// destroy session param export
			Zend_Session::namespaceUnset('param_rekap_erep');
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="nilai/rekap";
			// title
			$this->view->title = "Rekap Nilai Mahasiswa";
			// layout
			$this->_helper->layout()->setLayout('main');
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
			// get data periode
			$periode= new Periode();
			$this->view->listPeriode=$periode->fetchAll();
		}
	}
	
	function prekapAction(){
		$user = new Menu();
		$menu = "nilai/rekap";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// navigation
			$this->_helper->navbar('nilai/rekap',0,0,'nilai/erekap',0);
			// layout
			$this->_helper->layout()->setLayout('second');
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="nilai/rekap";
			// session
			$param = new Zend_Session_Namespace('param_rekap_erep');
			$kd_prodi = $param->prd;
			$kd_periode = $param->per;
			// get prodi
			$prodi = new Prodi();
			$getProdi=$prodi->getProdiByKd($kd_prodi);
			$nm_prd="";
			foreach ($getProdi as $dtProdi){
				$nm_prd=$dtProdi['nm_prodi'];
			}
			// title
			$this->view->title = "Rekap Nilai Periode ".$kd_periode.' Prodi '.$nm_prd;
			// get data paket kelas
			$paket=new Paketkelas();
			$getPaket=$paket->getPaketKelasByPeriodeProdi($kd_periode,$kd_prodi);
			$paketTA=new PaketkelasTA();
			$getPaketTA=$paketTA->getPaketKelasTAByPeriodeProdi($kd_periode, $kd_prodi);
			$getPaketAll=array_merge($getPaket,$getPaketTA);
			$arrPaket=array();
			// get aturan nilai
			$aturan=new AturanNilai();
			$getAturan=$aturan->getAturanNilaiByProdiPeriode($kd_prodi, $kd_periode);
			$this->view->listAturan=$getAturan;
			$i=0;
			foreach ($getPaketAll as $dataPaket){
				$arrPaket[$i]['kode_mk']=$dataPaket['kode_mk'];
				$arrPaket[$i]['nm_mk']=$dataPaket['nm_mk'];
				$arrPaket[$i]['kd_dosen']=$dataPaket['kd_dosen'];
				$arrPaket[$i]['nm_dosen']=$dataPaket['nm_dosen'];
				$arrPaket[$i]['smt_def']=$dataPaket['smt_def'];
				$arrPaket[$i]['nm_kelas']=$dataPaket['nm_kelas'];
				$arrPaket[$i]['jns_kelas']=$dataPaket['jns_kelas'];
				// get data nilai
				$nilai=new Nilai();
				$getNilai=$nilai->getNilaiByPaket($dataPaket['kd_paket_kelas']);
				if(!$getNilai){
					$nilaiTa=new NilaiTA();
					$getNilai=$nilaiTa->getNilaiTAByPaket($dataPaket['kd_paket_kelas']);
				}
				$j=0;
				foreach ($getAturan as $dtAturan){
					$arrPaket[$i][$j]=0;
					$arrPaket[$i]['notfix']=0;
					$arrPaket[$i]['tunda']=0;
					$arrPaket[$i]['fix']=0;
					foreach ($getNilai as $dtNilai){
						if($dtNilai['status']=='0'){
							$arrPaket[$i]['notfix']++;
						}elseif ($dtNilai['status']=='2'){
							$arrPaket[$i]['tunda']++;
						}else{
							$arrPaket[$i]['fix']++;
							$arrIdx=explode('/', $dtNilai['index']);
							if(($dtAturan['indeks']==$arrIdx[0])and($dtAturan['bobot']==$arrIdx[1])){
								$arrPaket[$i][$j]++;
							}	
						}
					}
					$j++;
				}
				$i++;
			}
			$this->view->listPaket=$arrPaket;
			// session for excel
			$param->dataNilai=$arrPaket;
			$param->aturanNilai=$getAturan;
			$param->nmPrd=$nm_prd;
		}
	}

	function erekapAction(){
		$user = new Menu();
		$menu = "nilai/rekap";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// session
			$param = new Zend_Session_Namespace('param_rekap_erep');
			$dataNilai = $param->dataNilai;
			$aturanNilai=$param->aturanNilai;
			$nmPrd=$param->nmPrd;
			$kdPeriode=$param->per;
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt=$ses_ac->nm_pt;
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt = $ses_ac->nm_pt;
			$objPHPExcel->getProperties()->setCreator($nm_pt)
										 ->setLastModifiedBy("Akademik")
										 ->setTitle("Rekap Nilai Mahasiswa")
										 ->setSubject("Sistem Informasi Akademik")
										 ->setDescription("Rekap Nilai")
										 ->setKeywords("rekap nilai")
										 ->setCategory("Data File");
										 
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Rekap Nilai');
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
														  ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
														  ->setFitToWidth('1')
														  ->setFitToHeight('Automatic')
														  ->SetHorizontalCentered(true);
			// margin is set in inches (0.5cm)
			$margin = 0.5 / 2.54;											  
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop($margin)
															->setBottom($margin)
															->setLeft($margin)
															->setRight($margin);
			//set Layout
			$border = array(
			  'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$cell_color = array(
	 		   'fill' => array(
			        'type' => PHPExcel_Style_Fill::FILL_SOLID,
			        'color' => array('rgb'=>'CCCCCC')
			    ),
			);
			// properties field excel;
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(32);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getStyle('A1:F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			// insert data to excel
			$objPHPExcel->getActiveSheet()->setCellValue('A1',strtoupper($nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('A2','REKAP NILAI');
			$objPHPExcel->getActiveSheet()->setCellValue('A3','PROGRAM STUDI '.$nmPrd.' PERIODE AKADEMIK '.$kdPeriode);
			// data
			$objPHPExcel->getActiveSheet()->setCellValue('A4','NO');			
			$objPHPExcel->getActiveSheet()->setCellValue('B4','MATA KULIAH');
			$objPHPExcel->getActiveSheet()->setCellValue('C4','KODE');
			$objPHPExcel->getActiveSheet()->setCellValue('D4','SEMESTER');
			$objPHPExcel->getActiveSheet()->setCellValue('E4','DOSEN');
			$objPHPExcel->getActiveSheet()->setCellValue('F4','KELAS');
			$objPHPExcel->getActiveSheet()->setCellValue('G4','FIX');
			$objPHPExcel->getActiveSheet()->mergeCells('A4:A5');
			$objPHPExcel->getActiveSheet()->mergeCells('B4:B5');
			$objPHPExcel->getActiveSheet()->mergeCells('C4:C5');
			$objPHPExcel->getActiveSheet()->mergeCells('D4:D5');
			$objPHPExcel->getActiveSheet()->mergeCells('E4:E5');
			$objPHPExcel->getActiveSheet()->mergeCells('F4:F5');
			$arrHuruf=array("","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
			$nFix=1;
			foreach ($aturanNilai as $dtAturan){
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$nFix].'5',$dtAturan['indeks']);
				$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$nFix])->setWidth(7);
				$nFix++;
			}
			$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$nFix].'4','TUNDA');
			$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$nFix+1].'4','BLM FIX');
			$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$nFix+2].'4','TOTAL');
			$objPHPExcel->getActiveSheet()->mergeCells($arrHuruf[$nFix].'4:'.$arrHuruf[$nFix].'5');
			$objPHPExcel->getActiveSheet()->mergeCells($arrHuruf[$nFix+1].'4:'.$arrHuruf[$nFix+1].'5');
			$objPHPExcel->getActiveSheet()->mergeCells($arrHuruf[$nFix+2].'4:'.$arrHuruf[$nFix+2].'5');
			//
			$objPHPExcel->getActiveSheet()->mergeCells('A1:'.$arrHuruf[$nFix+2].'1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:'.$arrHuruf[$nFix+2].'2');
			$objPHPExcel->getActiveSheet()->mergeCells('A3:'.$arrHuruf[$nFix+2].'3');
			$objPHPExcel->getActiveSheet()->getStyle('A1:'.$arrHuruf[$nFix+2].'3')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('A2:'.$arrHuruf[$nFix+2].'3')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A1:'.$arrHuruf[$nFix+2].'3')->getFont()->setBold(true);
			//
			$objPHPExcel->getActiveSheet()->mergeCells('G4:'.$arrHuruf[$nFix-1].'4');
			//
			$objPHPExcel->getActiveSheet()->getStyle('A4:'.$arrHuruf[$nFix+2].'5')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A4:'.$arrHuruf[$nFix+2].'5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A4:'.$arrHuruf[$nFix+2].'5')->applyFromArray($cell_color);
			$objPHPExcel->getActiveSheet()->getStyle('A4:'.$arrHuruf[$nFix+2].'5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A4:'.$arrHuruf[$nFix+2].'5')->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('A4:'.$arrHuruf[$nFix+2].'5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$i=6;
			$n=1;
			foreach ($dataNilai as $dtNilai){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$dtNilai['nm_mk']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$dtNilai['kode_mk']);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$dtNilai['smt_def']);
				$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$dtNilai['nm_dosen']);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$dtNilai['nm_kelas']." ".$dtNilai['jns_kelas']);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$j=0;
				$kol=1;
				foreach ($aturanNilai as $dtAturan){
					$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$kol].$i,$dtNilai[$j]);
					$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$kol].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$kol++;
					$j++;
				}
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$kol].$i,$dtNilai['tunda']);
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$kol].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$kol+1].$i,$dtNilai['notfix']);
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$kol+1].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$kol+2].$i,($dtNilai['notfix']+$dtNilai['fix']+$dtNilai['tunda']));
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$kol+2].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.$arrHuruf[$nFix+2].$i)->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.$arrHuruf[$nFix+2].$i) ->getAlignment()->setWrapText(true);
				$n++;
				$i++;
			}
			//Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Rekap Nilai.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}

	function repujianAction(){
		$user = new Menu();
		$menu = "nilai/repujian";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Rekap Nilai UTS/UAS";
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="nilai/repujian";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			$prodi = new Prodi();
			$this->view->listProdi = $prodi->fetchAll();
			$periode = new Periode();
			$this->view->listPeriode = $periode->fetchAll();
		}
	}
	
	function erepujianAction(){
		$user = new Menu();
		$menu = "nilai/repujian";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// layout
			$this->_helper->layout->disableLayout();
			// get kd paket kelas
			$kd_paket=$this->_request->get('pkt');
			$jns=$this->_request->get('jns');
			$paketkelas = new Paketkelas();
			$getPaketKelas=$paketkelas->getPaketKelasByKd($kd_paket);
			if($getPaketKelas){
				foreach ($getPaketKelas as $dtPaket) {
					$kdKelas = $dtPaket['kd_kelas'];
					$kd_kelas = $dtPaket['kd_kelas'];
					$nm_prodi=$dtPaket['nm_prodi_kur'];
					$kd_per=$dtPaket['kd_periode'];
					$nm_kelas=$dtPaket['nm_kelas'];
					$jns_kelas=$dtPaket['jns_kelas'];
					$nm_dsn=$dtPaket['nm_dosen'];
					$nm_mk=$dtPaket['nm_mk'];
					$kd_mk=$dtPaket['kode_mk'];
					$sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
				}
				$kelas = new Kelas();
				$getKelas = $kelas->getKelasByKd($kdKelas);
				foreach ($getKelas as $dtKls) {
					$p_uts=$dtKls['p_uts'];
					$p_uas=$dtKls['p_uas'];
				}
				$nilai = new Nilai();
				$getNilai = $nilai->getNilaiByPaket($kd_paket);
				// export excel
				// konfigurasi excel
				PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
				$objPHPExcel = new PHPExcel();
				$ses_ac = new Zend_Session_Namespace('ses_ac');
				$nm_pt = $ses_ac->nm_pt;
				$objPHPExcel->getProperties()->setCreator($nm_pt)
				->setLastModifiedBy("Akademik")
				->setTitle("Nilai Mahasiswa")
				->setSubject("Sistem Informasi Akademik")
				->setDescription("Daftar Nilai Ujian Mahasiswa")
				->setKeywords("daftar nilai")
				->setCategory("Data File");
	
				// Rename sheet
				if($jns=='1'){
					$objPHPExcel->getActiveSheet()->setTitle('Daftar Nilai UTS');
				}else{
					$objPHPExcel->getActiveSheet()->setTitle('Daftar Nilai UAS');
				}
				$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
				->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
				->setFitToWidth('1')
				->setFitToHeight('Automatic')
				->SetHorizontalCentered(true);
				// margin is set in inches (0.5cm)
				$margin = 0.5 / 2.54;
				$objPHPExcel->getActiveSheet()->getPageMargins()->setTop($margin)
				->setBottom($margin)
				->setLeft($margin)
				->setRight($margin);
				//set Layout
				$border = array(
						'borders' => array(
								'allborders' => array(
										'style' => PHPExcel_Style_Border::BORDER_THIN
								)
						)
				);
				$cell_color = array(
						'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb'=>'CCCCCC')
						),
				);
				// properties field excel;
				$objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
				$objPHPExcel->getActiveSheet()->mergeCells('A2:E2');
				$objPHPExcel->getActiveSheet()->mergeCells('A3:B3');
				$objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
				$objPHPExcel->getActiveSheet()->mergeCells('A5:B5');
				$objPHPExcel->getActiveSheet()->mergeCells('A6:B6');
				$objPHPExcel->getActiveSheet()->mergeCells('A7:B7');
				$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setSize(14);
				$objPHPExcel->getActiveSheet()->getStyle('A1:E9')->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(9);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(18);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A8:E9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A8:E9')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				// insert data to excel
				if($jns=='1'){
					$objPHPExcel->getActiveSheet()->setCellValue('A1','DAFTAR NILAI UTS MAHASISWA');
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('A1','DAFTAR NILAI UAS MAHASISWA');
				}
				$objPHPExcel->getActiveSheet()->setCellValue('A2',strtoupper($nm_pt));
				$objPHPExcel->getActiveSheet()->setCellValue('A3','PROGRAM STUDI');
				$objPHPExcel->getActiveSheet()->setCellValue('A4','PERIODE');
				$objPHPExcel->getActiveSheet()->setCellValue('A5','KELAS');
				$objPHPExcel->getActiveSheet()->setCellValue('A6','DOSEN');
				$objPHPExcel->getActiveSheet()->setCellValue('A7','MATA KULIAH');
				$objPHPExcel->getActiveSheet()->setCellValue('C3',$nm_prodi);
				$objPHPExcel->getActiveSheet()->setCellValue('C4',$kd_per);
				$objPHPExcel->getActiveSheet()->setCellValue('C5',$nm_kelas."(".$jns_kelas.")");
				$objPHPExcel->getActiveSheet()->setCellValue('C6',$nm_dsn);
				$objPHPExcel->getActiveSheet()->setCellValue('C7',$nm_mk."(".$kd_mk.")-".$sks." SKS");
				$objPHPExcel->getActiveSheet()->mergeCells('A9:A10');
				$objPHPExcel->getActiveSheet()->setCellValue('A9','No');
				$objPHPExcel->getActiveSheet()->mergeCells('B9:B10');
				$objPHPExcel->getActiveSheet()->setCellValue('B9','NPM');
				$objPHPExcel->getActiveSheet()->mergeCells('C9:C10');
				$objPHPExcel->getActiveSheet()->setCellValue('C9','Nama');
				$objPHPExcel->getActiveSheet()->mergeCells('D9:D10');
				$objPHPExcel->getActiveSheet()->setCellValue('D9','Angkatan');
				$objPHPExcel->getActiveSheet()->setCellValue('E9','NILAI');
				$objPHPExcel->getActiveSheet()->getStyle('E9') ->getAlignment()->setWrapText(true);
				if($jns=='1'){
					$objPHPExcel->getActiveSheet()->setCellValue('E10',$p_uts."%");
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('E10',$p_uas."%");
				}
				$objPHPExcel->getActiveSheet()->getStyle('E10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$i=11;
				$n=1;
				foreach ($getNilai as $data){
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$data['nim']);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$data['nm_mhs']);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$data['id_angkatan']);
					if($jns=='1'){
						$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$data['uts']);
					}else{
						$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$data['uas']);
					}
					$objPHPExcel->getActiveSheet()->getStyle('A'.$i.'C'.$i) ->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$i++;
					$n++;
				}
				$objPHPExcel->getActiveSheet()->getStyle('A9:E'.($i-1))->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('A9:E'.($i-1))->getFont()->setSize(12);
				$objPHPExcel->getActiveSheet()->getStyle('E11:E'.($i-1))->getNumberFormat()->setFormatCode('#,##0.00');
				// Redirect output to a client web browser (Excel5)
				header('Content-Type: application/vnd.ms-excel');
				if($jns=='1'){
					header('Content-Disposition: attachment;filename="Daftar Nilai UTS.xls"');
				}else{
					header('Content-Disposition: attachment;filename="Daftar Nilai UAS.xls"');
				}
				header('Cache-Control: max-age=0');
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$objWriter->save('php://output');
			}else{
	
			}
		}
	}

	function sumujianAction(){
		$user = new Menu();
		$menu = "nilai/sumujian";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// destroy session param export
			Zend_Session::namespaceUnset('param_sumujian_erep');
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="nilai/sumujian";
			// title
			$this->view->title = "Rekap Nilai UTS/UAS (Summary)";
			// layout
			$this->_helper->layout()->setLayout('main');
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
			// get data periode
			$periode= new Periode();
			$this->view->listPeriode=$periode->fetchAll();
		}
	}
	
	function psumujianAction(){
		$user = new Menu();
		$menu = "nilai/sumujian";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// navigation
			$this->_helper->navbar('nilai/sumujian',0,0,'nilai/esumujian',0);
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="nilai/sumujian";
			// layout
			$this->_helper->layout()->setLayout('second');
			// session
			$param = new Zend_Session_Namespace('param_sumujian_erep');
			$kd_prodi = $param->prd;
			$kd_periode = $param->per;
			// get prodi
			$prodi = new Prodi();
			$getProdi=$prodi->getProdiByKd($kd_prodi);
			$nm_prd="";
			foreach ($getProdi as $dtProdi){
				$nm_prd=$dtProdi['nm_prodi'];
			}
			// title
			$this->view->title = "Rekap Nilai UTS/UAS ".$kd_periode.' Prodi '.$nm_prd." (Summary)";
			// get data paket kelas
			$paket=new Paketkelas();
			$getPaket=$paket->getPaketKelasByPeriodeProdi($kd_periode,$kd_prodi);
			$arrPaket=array();
			// get aturan nilai
			$aturan=new AturanNilai();
			$getAturan=$aturan->getAturanNilaiByProdiPeriode($kd_prodi, $kd_periode);
			$this->view->listAturan=$getAturan;
			$i=0;
			foreach ($getPaket as $dataPaket){
				$arrPaket[$i]['kode_mk']=$dataPaket['kode_mk'];
				$arrPaket[$i]['nm_mk']=$dataPaket['nm_mk'];
				$arrPaket[$i]['kd_dosen']=$dataPaket['kd_dosen'];
				$arrPaket[$i]['nm_dosen']=$dataPaket['nm_dosen'];
				$arrPaket[$i]['smt_def']=$dataPaket['smt_def'];
				$arrPaket[$i]['nm_kelas']=$dataPaket['nm_kelas'];
				$arrPaket[$i]['jns_kelas']=$dataPaket['jns_kelas'];
				// get data nilai
				$nilai=new Nilai();
				$getNilai=$nilai->getNilaiByPaket($dataPaket['kd_paket_kelas']);
				$j=0;
				$arrPaket[$i]['n_uts']=0;
				$arrPaket[$i]['n_uas']=0;
				$arrPaket[$i]['not_uts']=0;
				$arrPaket[$i]['not_uas']=0;
				foreach ($getNilai as $dtNilai){
					if($dtNilai['uts']!='0'){
						$arrPaket[$i]['n_uts']++;
					}else{
						$arrPaket[$i]['not_uts']++;
					}
					if($dtNilai['uas']!='0'){
						$arrPaket[$i]['n_uas']++;
					}else{
						$arrPaket[$i]['not_uas']++;
					}
					$j++;
				}
				$i++;
			}
			$this->view->listPaket=$arrPaket;
			// session for excel
			$param->dataNilai=$arrPaket;
			$param->nmPrd=$nm_prd;
		}
	}
	
	function esumujianAction(){
		$user = new Menu();
		$menu = "nilai/sumujian";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// session
			$param = new Zend_Session_Namespace('param_sumujian_erep');
			$dataNilai = $param->dataNilai;
			$nmPrd=$param->nmPrd;
			$kdPeriode=$param->per;
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt=$ses_ac->nm_pt;
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt = $ses_ac->nm_pt;
			$objPHPExcel->getProperties()->setCreator($nm_pt)
			->setLastModifiedBy("Akademik")
			->setTitle("Rekap Nilai Ujian Mahasiswa (Summary)")
			->setSubject("Sistem Informasi Akademik")
			->setDescription("Rekap UTS UAS")
			->setKeywords("rekap nilai ujian")
			->setCategory("Data File");
				
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Rekap UTS UAS');
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
			->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
			->setFitToWidth('1')
			->setFitToHeight('Automatic')
			->SetHorizontalCentered(true);
			// margin is set in inches (0.5cm)
			$margin = 0.5 / 2.54;
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop($margin)
			->setBottom($margin)
			->setLeft($margin)
			->setRight($margin);
			//set Layout
			$border = array(
					'borders' => array(
							'allborders' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
							)
					)
			);
			$cell_color = array(
					'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb'=>'CCCCCC')
					),
			);
			// properties field excel;
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(32);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getStyle('A1:K3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1:K3')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A1:K3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:K1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:K2');
			$objPHPExcel->getActiveSheet()->mergeCells('A3:K3');
			// insert data to excel
			$objPHPExcel->getActiveSheet()->setCellValue('A1',strtoupper($nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('A2','REKAP NILAI UTS/UAS (SUMMARY)');
			$objPHPExcel->getActiveSheet()->setCellValue('A3','PROGRAM STUDI '.$nmPrd.' PERIODE AKADEMIK '.$kdPeriode);
			// data
			$objPHPExcel->getActiveSheet()->setCellValue('A4','NO');
			$objPHPExcel->getActiveSheet()->setCellValue('B4','MATA KULIAH');
			$objPHPExcel->getActiveSheet()->setCellValue('C4','KODE');
			$objPHPExcel->getActiveSheet()->setCellValue('D4','SEMESTER');
			$objPHPExcel->getActiveSheet()->setCellValue('E4','DOSEN');
			$objPHPExcel->getActiveSheet()->setCellValue('F4','KELAS');
			$objPHPExcel->getActiveSheet()->setCellValue('G4','UTS');
			$objPHPExcel->getActiveSheet()->setCellValue('I4','UAS');
			$objPHPExcel->getActiveSheet()->setCellValue('K4','TOTAL');
			$objPHPExcel->getActiveSheet()->setCellValue('G5','NULL');
			$objPHPExcel->getActiveSheet()->setCellValue('H5','FIX');
			$objPHPExcel->getActiveSheet()->setCellValue('I5','NULL');
			$objPHPExcel->getActiveSheet()->setCellValue('J5','FIX');
			$objPHPExcel->getActiveSheet()->mergeCells('A4:A5');
			$objPHPExcel->getActiveSheet()->mergeCells('B4:B5');
			$objPHPExcel->getActiveSheet()->mergeCells('C4:C5');
			$objPHPExcel->getActiveSheet()->mergeCells('D4:D5');
			$objPHPExcel->getActiveSheet()->mergeCells('E4:E5');
			$objPHPExcel->getActiveSheet()->mergeCells('F4:F5');
			$objPHPExcel->getActiveSheet()->mergeCells('G4:H4');
			$objPHPExcel->getActiveSheet()->mergeCells('I4:J4');
			$objPHPExcel->getActiveSheet()->mergeCells('K4:K5');
			$i=6;
			$n=1;
			$objPHPExcel->getActiveSheet()->getStyle('A4:K5')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A4:K5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A4:K5')->applyFromArray($cell_color);
			$objPHPExcel->getActiveSheet()->getStyle('A4:K5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A4:K5')->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('A4:K5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			foreach ($dataNilai as $dtNilai){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$dtNilai['nm_mk']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$dtNilai['kode_mk']);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$dtNilai['smt_def']);
				$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$dtNilai['nm_dosen']);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$dtNilai['nm_kelas']." ".$dtNilai['jns_kelas']);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$dtNilai['not_uts']);
				$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$dtNilai['n_uts']);
				$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$dtNilai['not_uas']);
				$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$dtNilai['n_uas']);
				$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,$dtNilai['n_uas']+$dtNilai['not_uas']);
				$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i) ->getAlignment()->setWrapText(true);
				$n++;
				$i++;
			}
			//Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Rekap Nilai Ujian Summary.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}

	function etranskrip2Action(){
		$user = new Menu();
		$menu = "nilai/transkrip";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// session
			$param = new Zend_Session_Namespace('param_trkp');
			$dataNilai = $param->data;
			$nSmt=$param->nSmt;
			$nim=$param->nim;
			$nm=$param->nm_mhs;
			$prd=$param->prd;
			$akt=$param->akt;
			$smtPil=$param->smt;
			$tmplhr=$param->tmplhr;
			$tgllhr=$param->tgllhr;
			$idmhs=$param->idmhs;
			$tglkeluar=$param->tglkeluar;
			// image path foto
			$path = __FILE__;
			$imgPath = str_replace('academic/application/controllers/NilaiController.php','public/file/mhs/foto/'.$idmhs.'.jpg',$path);
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Administrator")
										 ->setLastModifiedBy("Akademik")
										 ->setTitle("Export Transkrip Nilai")
										 ->setSubject("Sistem Informasi Akademik")
										 ->setDescription("Transkrip Nilai")
										 ->setKeywords("transkrip nilai")
										 ->setCategory("Data File"); 
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Transkrip Nilai');
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT)
														  ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
														  ->setFitToWidth('1')
														  ->setFitToHeight('Automatic')
														  ->SetHorizontalCentered(true);
			
			// margin is set in inches (1 cm)
			$margin = 0.5 / 2.54;											  
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop($margin)
															->setBottom($margin)
															->setLeft($margin)
															->setRight($margin);	
	
			//set Layout
			$border = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)));
			$borderDouble = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE)));
			$borderInside = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
			$borderLeft = array('borders' => array('left' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)));
			$borderBottom = array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE)));
			$cell_color = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb'=>'CCCCCC')));
			$yellow_color = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb'=>'FFFF00')));
			$box = array('borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
			// column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(6);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(3);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(6);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(6);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(3);
			$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(6);
			$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(9);
			// height
			$objPHPExcel->getActiveSheet()->getRowDimension(8)->setRowHeight(9);
			$objPHPExcel->getActiveSheet()->getRowDimension(9)->setRowHeight(9);
			$objPHPExcel->getActiveSheet()->getRowDimension(10)->setRowHeight(9);
			$objPHPExcel->getActiveSheet()->getRowDimension(5)->setRowHeight(22);
			$objPHPExcel->getActiveSheet()->getRowDimension(11)->setRowHeight(22);
			// properties field excel;
			$objPHPExcel->getActiveSheet()->mergeCells('P2:T2');
			$objPHPExcel->getActiveSheet()->mergeCells('P3:T3');
			$objPHPExcel->getActiveSheet()->mergeCells('P4:T4');
			$objPHPExcel->getActiveSheet()->mergeCells('P5:T5');
			//
			$objPHPExcel->getActiveSheet()->mergeCells('C4:M4');
			$objPHPExcel->getActiveSheet()->mergeCells('C5:M5');
			$objPHPExcel->getActiveSheet()->mergeCells('C6:M6');
			$objPHPExcel->getActiveSheet()->mergeCells('C7:M7');
			//
			$objPHPExcel->getActiveSheet()->mergeCells('O11:T11');
			$objPHPExcel->getActiveSheet()->mergeCells('O12:T12');
			
			$objPHPExcel->getActiveSheet()->getStyle('C4:M7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('O11:T12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('P2:T5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('P2:T5')->getFont()->setSize(10);
			$objPHPExcel->getActiveSheet()->getStyle('C4:M5')->getFont()->setSize(18);
			$objPHPExcel->getActiveSheet()->getStyle('C6:M7')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('O11:T11')->getFont()->setSize(18);
			$objPHPExcel->getActiveSheet()->getStyle('O12:T12')->getFont()->setSize(11);
			$objPHPExcel->getActiveSheet()->getStyle('E16:T20')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E16:T20')->getFont()->setSize(13);
			$objPHPExcel->getActiveSheet()->getStyle('E16:T20')->getFont()->setBold(true);
			// foto
			if(file_exists($imgPath)){
				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Foto');
				$objDrawing->setDescription('foto');
				$objDrawing->setPath($imgPath);
				$objDrawing->setHeight(160);
				$objDrawing->setWidth(120);
				$objDrawing->setCoordinates('B15');
				$objDrawing->setOffsetX(0);
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			}else{
				$objPHPExcel->getActiveSheet()->getStyle('B15:C22')->applyFromArray($box);
			}
			// insert data to excel
			$objPHPExcel->getActiveSheet()->setCellValue('P2','Jl. Soekarno  Hatta 354');
			$objPHPExcel->getActiveSheet()->setCellValue('P3','Bandung 40266 Indonesia');
			$objPHPExcel->getActiveSheet()->setCellValue('P4','Telp.022-7566484 Fax.022-7566666');
			$objPHPExcel->getActiveSheet()->setCellValue('P5','Email : stfindonesia@gmail.com');
			$objPHPExcel->getActiveSheet()->getStyle('P2:P5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->setCellValue('C4','Y A Y A S A N  H A Z A N A H');
			$objPHPExcel->getActiveSheet()->setCellValue('C5','SEKOLAH TINGGI FARMASI INDONESIA');
			$objPHPExcel->getActiveSheet()->setCellValue('O11','TRANSKRIP AKADEMIK');
			$objPHPExcel->getActiveSheet()->getStyle('A1:T12')->getFont()->setName('Arial Rounded MT Bold');
			$objPHPExcel->getActiveSheet()->setCellValue('E16','Nama Mahasiswa');
			$objPHPExcel->getActiveSheet()->setCellValue('J16',': '.$nm);
			$objPHPExcel->getActiveSheet()->setCellValue('E17','NPM');
			$objPHPExcel->getActiveSheet()->setCellValue('J17',': '.$nim);
			$objPHPExcel->getActiveSheet()->setCellValue('E18','Tempat/Tanggal Lahir');
			$objPHPExcel->getActiveSheet()->setCellValue('J18',': '.$tmplhr.', '.$tgllhr);
			$objPHPExcel->getActiveSheet()->setCellValue('E19','Program Studi');
			$objPHPExcel->getActiveSheet()->setCellValue('J19',': '.$prd);
			$objPHPExcel->getActiveSheet()->setCellValue('E20','Tahun Masuk');
			$objPHPExcel->getActiveSheet()->setCellValue('J20',': '.$akt);
			$objPHPExcel->getActiveSheet()->setCellValue('A25','No');
			$objPHPExcel->getActiveSheet()->mergeCells('A25:A26');
			$objPHPExcel->getActiveSheet()->setCellValue('B25','Kode');
			$objPHPExcel->getActiveSheet()->mergeCells('B25:B26');
			$objPHPExcel->getActiveSheet()->setCellValue('C25','Mata Kuliah');
			$objPHPExcel->getActiveSheet()->mergeCells('C25:G26');
			$objPHPExcel->getActiveSheet()->setCellValue('I25','SKS');
			$objPHPExcel->getActiveSheet()->mergeCells('H25:H26');
			$objPHPExcel->getActiveSheet()->mergeCells('I25:I26');
			$objPHPExcel->getActiveSheet()->setCellValue('J25','Nilai');
			$objPHPExcel->getActiveSheet()->mergeCells('J25:J26');
			$objPHPExcel->getActiveSheet()->getStyle('A25:J26')->applyFromArray($box);
			$objPHPExcel->getActiveSheet()->setCellValue('K25','No');
			$objPHPExcel->getActiveSheet()->mergeCells('K25:K26');
			$objPHPExcel->getActiveSheet()->setCellValue('L25','Kode');
			$objPHPExcel->getActiveSheet()->mergeCells('L25:L26');
			$objPHPExcel->getActiveSheet()->setCellValue('M25','Mata Kuliah');
			$objPHPExcel->getActiveSheet()->mergeCells('M25:Q26');
			$objPHPExcel->getActiveSheet()->setCellValue('S25','SKS');
			$objPHPExcel->getActiveSheet()->mergeCells('R25:R26');
			$objPHPExcel->getActiveSheet()->mergeCells('S25:S26');
			$objPHPExcel->getActiveSheet()->setCellValue('T25','Nilai');
			$objPHPExcel->getActiveSheet()->mergeCells('T25:T26');
			$objPHPExcel->getActiveSheet()->getStyle('K25:T26')->applyFromArray($box);
			$objPHPExcel->getActiveSheet()->getStyle('A25:T26')->getFont()->setName('Tahoma');
			$objPHPExcel->getActiveSheet()->getStyle('A25:T26')->getFont()->setSize(9);
			$objPHPExcel->getActiveSheet()->getStyle('A25:T26')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A25:T26')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A25:T26')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$n_mk=76;
			$a=28;
			$b=28;
			$n=1;
			$kmTot=0;
			$sksTot=0;
			foreach ($dataNilai as $dt){
				$kmTot=$kmTot+(floatval($dt['sks_def']*$dt['bobot']));
				$sksTot=$sksTot+$dt['sks_def'];
				if($n<=($n_mk/2)){
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$a,$n);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$a,$dt['kode_mk']);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$a,$dt['nm_mk']);
					$objPHPExcel->getActiveSheet()->setCellValue('I'.$a,$dt['sks_def']);
					if($dt['status']==1){
						$objPHPExcel->getActiveSheet()->setCellValue('J'.$a,$dt['huruf']);
					}
					$objPHPExcel->getActiveSheet()->getStyle('A'.$a)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('I'.$a.':J'.$a)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					if($dt['n_nilai']>1){
						// mengulang warna kuning
						$objPHPExcel->getActiveSheet()->getStyle('A'.$a.':J'.$a)->applyFromArray($yellow_color);
					}
					$a++;
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('K'.$b,$n);
					$objPHPExcel->getActiveSheet()->setCellValue('L'.$b,$dt['kode_mk']);
					$objPHPExcel->getActiveSheet()->setCellValue('M'.$b,$dt['nm_mk']);
					$objPHPExcel->getActiveSheet()->setCellValue('S'.$b,$dt['sks_def']);
					if($dt['status']==1){
						$objPHPExcel->getActiveSheet()->setCellValue('T'.$b,$dt['huruf']);
					}
					$objPHPExcel->getActiveSheet()->getStyle('K'.$b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('S'.$b.':T'.$b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					if($dt['n_nilai']>1){
						// mengulang warna kuning
						$objPHPExcel->getActiveSheet()->getStyle('K'.$b.':T'.$b)->applyFromArray($yellow_color);
					}
					$b++;
				}
				$n++;
			}
			if($a>$b){
				$i=$a;
			}else{
				$i=$b;
			}
			$objPHPExcel->getActiveSheet()->getStyle('A26:J'.$i)->applyFromArray($box);
			$objPHPExcel->getActiveSheet()->getStyle('K26:T'.$i)->applyFromArray($box);
			$objPHPExcel->getActiveSheet()->getStyle('A26:T'.$i)->getFont()->setName('Tahoma');
			$objPHPExcel->getActiveSheet()->getStyle('A26:T'.$i)->getFont()->setSize(9);
			$akhir=$i;
			// bawah
			// get data last TA
			$kuliahTa=new KuliahTA();
			$getLastTa=$kuliahTa->getLastKuliahTAByNim($nim);
			if(!$getLastTa){
				$jdlTa="";
				$pmb1="";
				$pmb2="";
			}else{
				foreach($getLastTa as $dtTa){
					$jdlTa=$dtTa['judul'];
					$pmb1=$dtTa['nm_dosen_pemb1'];
					$pmb2=$dtTa['nm_dosen_pemb2'];
				}		
			}
			$i=$i+2;
			// kiri
			$objPHPExcel->getActiveSheet()->getRowDimension(($i+1))->setRowHeight(5);
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'Judul Penelitian Tugas Akhir :');
			$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+2),$jdlTa);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+2))->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->mergeCells('A'.($i+2).':I'.($i+4));
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+2).':I'.($i+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+2).':I'.($i+4))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+5),'Pembimbing :');
			$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+6),'1.'.$pmb1);
			$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+7),'2.'.$pmb2);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.($i+5))->getFont()->setSize(11);
			// kanan
			if($sksTot==0){
				$ipk=0.00;
			}else{
				$ipk=number_format(floatval($kmTot/$sksTot),2,',','.');
			}
			$objPHPExcel->getActiveSheet()->setCellValue('K'.($i+2),'Tanggal Lulus');
			$objPHPExcel->getActiveSheet()->setCellValue('O'.($i+2),': '.$tglkeluar);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.($i+3),'IPK');
			$objPHPExcel->getActiveSheet()->setCellValue('O'.($i+3),': '.$ipk);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.($i+4),'Total SKS');
			$objPHPExcel->getActiveSheet()->setCellValue('O'.($i+4),': '.$sksTot);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.($i+5),'Yudisium');
			$objPHPExcel->getActiveSheet()->setCellValue('O'.($i+5),': ');
			$objPHPExcel->getActiveSheet()->getStyle('K'.$i.':T'.($i+5))->getFont()->setSize(12);

			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':T'.($i+8))->getFont()->setName('Tahoma');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':T'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($akhir+1).':J'.($i+8))->applyFromArray($box);
			$objPHPExcel->getActiveSheet()->getStyle('K'.($akhir+1).':T'.($i+8))->applyFromArray($box);
			// ttd
			$objPHPExcel->getActiveSheet()->setCellValue('O'.($i+10),'Bandung, ');
			$objPHPExcel->getActiveSheet()->setCellValue('O'.($i+11),'Ketua, ');
			$objPHPExcel->getActiveSheet()->setCellValue('O'.($i+19),'Dr. apt. Adang Firmansyah, M.Si.');
			$objPHPExcel->getActiveSheet()->getStyle('O'.($i+10).':O'.($i+19))->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('O'.($i+10).':O'.($i+19))->getFont()->setName('Tahoma');

			// protected
			$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);	
			$objPHPExcel->getActiveSheet()->protectCells('A28:T'.$i, 'PHPExcel');

			//Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Transkrip Nilai (Format).xls"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}

}
