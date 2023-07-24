<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Atribut TA Controller -> Controller untuk modul halaman nilai dan atribut TA
*/
class AtrtaController extends Zend_Controller_Action
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
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('KelasTA');
		Zend_Loader::loadClass('PaketkelasTA');
		Zend_Loader::loadClass('KuliahTA');
		Zend_Loader::loadClass('NilaiTA');
		Zend_Loader::loadClass('Dosbim');
		Zend_Loader::loadClass('Dosji');
		Zend_Loader::loadClass('Register');
		Zend_Loader::loadClass('StatReg');
		Zend_Loader::loadClass('AturanNilai');
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
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// treeview
		$this->view->active_tree="09";
		$this->view->active_menu="atrta/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "atrta/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Nilai dan Kelengkapan Data TA";
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
		$menu = "atrta/index";
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
				$this->view->title = "Daftar Paket Kelas TA Periode ".$kd_periode." Prodi ".$nm_prd;
				// paket kelas
				$paketkelasta=new PaketkelasTA();
				$this->view->listPaketTA=$paketkelasta->getPaketKelasTAByPeriodeProdi($kd_periode,$kd_prodi);
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Daftar Paket Kelas TA";	
			}
			// navigation
			$this->_helper->navbar('atrta',0,0,0,0);
		}
	}

	function detilAction(){
		$user = new Menu();
		$menu = "atrta/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// layout
			$this->_helper->layout()->setLayout('second');
			// Title Browser
			$this->view->title = "Daftar Mahasiswa Mengambil TA";
			// get kd paket kelas
			$kd_paket=$this->_request->get('id');
			$paketkelasta = new PaketkelasTA();
			$getPaketKelasTA=$paketkelasta->getPaketKelasTAByKd($kd_paket);
			if($getPaketKelasTA){
				foreach ($getPaketKelasTA as $dtPaket) {
					$kdKelas = $dtPaket['kd_kelas'];
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
				$kelasTA = new KelasTA();
				$getKelasTA = $kelasTA->getKelasTAByKd($kdKelas);
				foreach ($getKelasTA as $dtKls) {
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
					$this->view->p_p5=$dtKls['p_p5'];
					$this->view->p_p6=$dtKls['p_p6'];
					$this->view->p_p7=$dtKls['p_p7'];
					$this->view->p_p8=$dtKls['p_p8'];
					$this->view->p_tot=$dtKls['p_p1']+$dtKls['p_p2']+$dtKls['p_p3']+$dtKls['p_p4']+$dtKls['p_p5']+$dtKls['p_p6']+$dtKls['p_p7']+$dtKls['p_p8'];
				}
				$nilaiTA = new NilaiTA();
				$getNilaiTA = $nilaiTA->getNilaiTAByPaket($kd_paket);
				$this->view->listNilaiTA=$getNilaiTA;
				// navigation
				$this->_helper->navbar('atrta/list',0,0,'atrta/export?id='.$kd_paket,0);
			}else{
				$this->view->eksis="f";
				// navigation
				$this->_helper->navbar('atrta',0,0,0,0);
			}
		}
	}

	function newAction(){
		$user = new Menu();
		$menu = "atrta/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get kd paket kelas
			$kd_kuliah=$this->_request->get('id');
			$nilaiTA = new NilaiTA();
			$getNilaiTA = $nilaiTA->getNilaiTAByKd($kd_kuliah);
			if($getNilaiTA){
				foreach ($getNilaiTA as $dataNilai) {
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
					$this->view->pemb1=$dataNilai['kd_dosen_pemb1'];
					$this->view->pemb2=$dataNilai['kd_dosen_pemb2'];
					$this->view->pemb3=$dataNilai['kd_dosen_pemb3'];
					$this->view->noreg=$dataNilai['no_reg'];
					$this->view->judul=$dataNilai['judul'];
					$this->view->penj1=$dataNilai['kd_dosen_penguji1'];
					$this->view->penj2=$dataNilai['kd_dosen_penguji2'];
					$this->view->penj3=$dataNilai['kd_dosen_penguji3'];
					$this->view->penj4=$dataNilai['kd_dosen_penguji4'];
				}
				// kelas
				$kelasTA = new KelasTA();
				$getKelasTA = $kelasTA->getKelasTAByKd($kdKelas);
				foreach ($getKelasTA as $dtKls) {
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
					$this->view->p_p5=$dtKls['p_p5'];
					$this->view->p_p6=$dtKls['p_p6'];
					$this->view->p_p7=$dtKls['p_p7'];
					$this->view->p_p8=$dtKls['p_p8'];
					$this->view->p_tot=$dtKls['p_p1']+$dtKls['p_p2']+$dtKls['p_p3']+$dtKls['p_p4']+$dtKls['p_p5']+$dtKls['p_p6']+$dtKls['p_p7']+$dtKls['p_p8'];
					$this->view->note=$dtKls['note_dosen'];
				}
				// aturan nilai
				$aturanNilai = new AturanNilai();
				$this->view->listAturan = $aturanNilai->getAturanNilaiByProdiPeriode($kdProdi,$kdPeriode);
				// dosbim available
				$dosbim = new Dosbim();
				$this->view->listDosbim = $dosbim->getDosbimByPeriode($kdPeriode,$kdProdi);
				$dosji = new Dosji();
				$this->view->listDosji = $dosji->getDosjiByPeriode($kdPeriode,$kdProdi);
				// navigation
				$this->_helper->navbar('atrta/detil?id='.$kd_paket,0,0,0,0);
			}else{
				$this->view->eksis="f";
			}
			$this->view->title="Data Nilai TA Mahasiswa";
		}
	}

	function exportAction(){
		$user = new Menu();
		$menu = "atrta/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// layout
			$this->_helper->layout->disableLayout();
			// get kd paket kelas
			$kd_paket=$this->_request->get('id');
			$paketkelas = new PaketkelasTA();
			$getPaketKelas=$paketkelas->getPaketKelasTAByKd($kd_paket);
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
				$kelas = new KelasTA();
				$getKelas = $kelas->getKelasTAByKd($kd_kelas);
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
					$p_p5=$dtKls['p_p5'];
					$p_p6=$dtKls['p_p6'];
					$p_p7=$dtKls['p_p7'];
					$p_p8=$dtKls['p_p8'];
					$p_tot=$dtKls['p_p1']+$dtKls['p_p2']+$dtKls['p_p3']+$dtKls['p_p4']+$dtKls['p_p5']+$dtKls['p_p6']+$dtKls['p_p7']+$dtKls['p_p8'];
				}
				$nilai = new NilaiTA();
				$getNilai = $nilai->getNilaiTAByPaket($kd_paket);
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
				$objPHPExcel->getActiveSheet()->setTitle('Export Daftar Nilai TA');
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
				$objPHPExcel->getActiveSheet()->mergeCells('A1:O1');
				$objPHPExcel->getActiveSheet()->mergeCells('A2:O2');
				$objPHPExcel->getActiveSheet()->mergeCells('A3:B3');
				$objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
				$objPHPExcel->getActiveSheet()->mergeCells('A5:B5');
				$objPHPExcel->getActiveSheet()->mergeCells('A6:B6');
				$objPHPExcel->getActiveSheet()->mergeCells('A7:B7');
				$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setSize(14);
				$objPHPExcel->getActiveSheet()->getStyle('A1:O9')->getFont()->setBold(true);
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
				$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(0);
				$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A8:O9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A8:O9')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				// insert data to excel
				$objPHPExcel->getActiveSheet()->setCellValue('A1','DAFTAR NILAI TA MAHASISWA');
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
				$objPHPExcel->getActiveSheet()->setCellValue('I9',$nm_p5);
				$objPHPExcel->getActiveSheet()->setCellValue('J9',$nm_p6);
				$objPHPExcel->getActiveSheet()->setCellValue('K9',$nm_p7);
				$objPHPExcel->getActiveSheet()->setCellValue('L9',$nm_p8);
				$objPHPExcel->getActiveSheet()->setCellValue('M9',"Total");
				$objPHPExcel->getActiveSheet()->getStyle('E9:M9') ->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->setCellValue('E10',$p_p1."%");
				$objPHPExcel->getActiveSheet()->setCellValue('F10',$p_p2."%");
				$objPHPExcel->getActiveSheet()->setCellValue('G10',$p_p3."%");
				$objPHPExcel->getActiveSheet()->setCellValue('H10',$p_p4."%");
				$objPHPExcel->getActiveSheet()->setCellValue('I10',$p_p5."%");
				$objPHPExcel->getActiveSheet()->setCellValue('J10',$p_p6."%");
				$objPHPExcel->getActiveSheet()->setCellValue('K10',$p_p7."%");
				$objPHPExcel->getActiveSheet()->setCellValue('L10',$p_p8."%");
				$objPHPExcel->getActiveSheet()->setCellValue('M10',$p_tot."%");
				$objPHPExcel->getActiveSheet()->mergeCells('N9:N10');
				$objPHPExcel->getActiveSheet()->setCellValue('N9','Indeks');
				$objPHPExcel->getActiveSheet()->mergeCells('O9:O10');
				$objPHPExcel->getActiveSheet()->setCellValue('O9','Bobot');
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
					$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$data['p5']);
					$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$data['p6']);
					$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,$data['p7']);
					$objPHPExcel->getActiveSheet()->setCellValue('L'.$i,$data['p8']);
					$objPHPExcel->getActiveSheet()->setCellValue('M'.$i,$data['nilai_tot']);
					$arrIndex = explode('/', $data['index']);
					$objPHPExcel->getActiveSheet()->setCellValue('N'.$i,$arrIndex[0]);
					$objPHPExcel->getActiveSheet()->setCellValue('O'.$i,$arrIndex[1]);
					$objPHPExcel->getActiveSheet()->setCellValue('R'.$i,$data['kd_kuliah']);
					$objPHPExcel->getActiveSheet()->getStyle('R'.$i)->applyFromArray(array('font'  => array('color'=>array('rgb'=>'FFFFFF'))));
					$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':L'.$i)->applyFromArray($cell_color);
					$i++;
					$n++;
				}
				$objPHPExcel->getActiveSheet()->getStyle('A9:O'.($i-1))->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('A9:O'.($i-1))->getFont()->setSize(12);
				$objPHPExcel->getActiveSheet()->getStyle('E11:O'.($i-1))->getNumberFormat()->setFormatCode('#,##0.00');
				// Redirect output to a client web browser (Excel5)
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="Daftar Nilai TA Mahasiswa.xls"');
				header('Cache-Control: max-age=0');
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$objWriter->save('php://output');
			}else{
	
			}
		}
	}
}