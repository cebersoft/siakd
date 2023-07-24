<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Paket Kelas Controller -> Controller untuk modul halaman paket kelas
*/
class PaketkelasController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Dosen');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Kelas');
		Zend_Loader::loadClass('Paketkelas');
		Zend_Loader::loadClass('Nmkelas');
		Zend_Loader::loadClass('JnsKelas');
		Zend_Loader::loadClass('Kuliah');
		Zend_Loader::loadClass('TimTeaching');
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
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// treeview
		$this->view->active_tree="04";
		$this->view->active_menu="kelas/index";
	}
	
	function indexAction(){

	}

	function listAction()
	{
		$user = new Menu();
		$menu = "paketkelas/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$kd_kls=$this->_request->get('kd');
			// navigation
			$this->_helper->navbar('kelas/list',0,0,0,0);
			// get data kelas dan paket kelas
			$kelas = new Kelas();
			$getKelas = $kelas->getKelasByKd($kd_kls);
			if($getKelas){
				foreach ($getKelas as $data_kls) {
					$this->view->kd_kls=$data_kls['kd_kelas'];
					$this->view->kd_dsn=$data_kls['kd_dosen'];
					$this->view->nm_dsn=$data_kls['nm_dosen'];
					$this->view->kd_mk=$data_kls['kode_mk'];
					$this->view->nm_mk=$data_kls['nm_mk'];
					$this->view->kd_per=$data_kls['kd_periode'];
					$this->view->nm_prodi=$data_kls['nm_prodi_kur'];
					$this->view->jns_kelas=$data_kls['jns_kelas'];
					$this->view->sks=($data_kls['sks_tm']+$data_kls['sks_prak']+$data_kls['sks_prak_lap']+$data_kls['sks_sim']);
					$nm_mk=$data_kls['nm_mk'];
					$nm_dsn=$data_kls['nm_dosen'];
					$nm_prodi=$data_kls['nm_prodi_kur'];
					// title
					$this->view->title="Daftar Paket Kelas ".$nm_mk."(".$nm_dsn.")-".$nm_prodi;
					$paketkelas = new Paketkelas();
					$getPaket = $paketkelas->getPaketKelasByKelas($kd_kls);
					$this->view->listPaket = $getPaket;
					// data nama kelas
					$nmkls=new Nmkelas();
					$this->view->listNmKls=$nmkls->fetchAll();
					// data dosen
					$dosen = new Dosen();
					$this->view->listDsn = $dosen->getDosenByStatus('t');
					// data tim teaching
					$tt=new TimTeaching();
					$this->view->listTt=$tt->getTimTeachingByKelas($kd_kls);
				}
			}else{
				$this->view->eksis="f";
				// title
				$this->view->title="Daftar Paket Kelas";
			}
		}
	}

	function detilAction(){
		$user = new Menu();
		$menu = "paketkelas/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$kd_pkt=$this->_request->get('kd');
			// get data paket
			$paketkelas=new Paketkelas();
			$getPaket=$paketkelas->getPaketKelasByKd($kd_pkt);
			if($getPaket){
				foreach ($getPaket as $dtPaket){
					$this->view->kd_kls=$dtPaket['kd_kelas'];
					$this->view->kd_dsn=$dtPaket['kd_dosen'];
					$this->view->nm_dsn=$dtPaket['nm_dosen'];
					$this->view->kd_mk=$dtPaket['kode_mk'];
					$this->view->nm_mk=$dtPaket['nm_mk'];
					$this->view->kd_per=$dtPaket['kd_periode'];
					$this->view->nm_prodi=$dtPaket['nm_prodi_kur'];
					$this->view->jns_kelas=$dtPaket['jns_kelas'];
					$this->view->nm_kelas=$dtPaket['nm_kelas'];
					$this->view->sks=($dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim']);
					$kd_kelas=$dtPaket['kd_kelas'];
				}
				// navigation
				$this->_helper->navbar('paketkelas/list?kd='.$kd_kelas,0,0,'paketkelas/export2?kd='.$kd_pkt,0);
				// list mahasiswa
				$kuliah=new Kuliah();
				$getKuliah=$kuliah->getKuliahByPaket($kd_pkt);
				$this->view->listMhsKuliah=$getKuliah;	
			}else{
				$this->view->eksis="f";
				// navigation
				$this->_helper->navbar('kelas',0,0,0,0);
			}
			// title
			$this->view->title="Daftar Mahasiswa Pengambil Paket Kelas";
			
		}
	}

	function exportAction(){
		$user = new Menu();
		$menu = "paketkelas/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$kd_prodi=$this->_request->get('prd');
			$kd_periode=$this->_request->get('per');
			$id_jenis=$this->_request->get('jns');
			// nm pt
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt=$ses_ac->nm_pt;
			// get data prodi
			$prodi = new Prodi();
			$getProdi=$prodi->getProdiByKd($kd_prodi);
			$nmProdi="";
			foreach ($getProdi as $dtProdi){
				$nmProdi=$dtProdi['nm_prodi'];
			}
			$jnsKelas=new JnsKelas();
			$getJnsKelas=$jnsKelas->fetchAll();
			$nmJnsKls="";
			foreach ($getJnsKelas as $dtJnsKls) {
				if($dtJnsKls['id_jns_kelas']==$id_jenis){
					$nmJnsKls=$dtJnsKls['jns_kelas'];	
				}
			}
			// get data paket kelas
			$paketkelas=new Paketkelas();
			$getPaketKelas=$paketkelas->getPaketKelasByPeriodeProdi($kd_periode, $kd_prodi);
			// disabel layout
			$this->_helper->layout->disableLayout();
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Administrator")
										 ->setLastModifiedBy("Akademik")
										 ->setTitle("Export Data Paket Kelas")
										 ->setSubject("Sistem Informasi Akademik")
										 ->setDescription("Paket Kelas")
										 ->setKeywords("paket kelas")
										 ->setCategory("Data File");
										 
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Daftar Paket Kelas');
			
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
			$border = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
			$borderBottom = array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE)));
			$cell_color = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb'=>'CCCCCC')));
			// properties field excel;
			$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:H2');
			$objPHPExcel->getActiveSheet()->getStyle('A1:H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A2:H2')->applyFromArray($borderBottom);
			$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getFont()->setBold(true);
			// column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
			//-- content
			$objPHPExcel->getActiveSheet()->setCellValue('A1',strtoupper($nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('A2','DAFTAR PAKET KELAS '.strtoupper($nmJnsKls).' PERIODE '.$kd_periode.' PROGRAM STUDI '.$nmProdi);
			$objPHPExcel->getActiveSheet()->setCellValue('A4','NO');
			$objPHPExcel->getActiveSheet()->setCellValue('B4','SMT');
			$objPHPExcel->getActiveSheet()->setCellValue('C4','KODE MK');
			$objPHPExcel->getActiveSheet()->setCellValue('D4','NAMA MK');
			$objPHPExcel->getActiveSheet()->setCellValue('E4','NAMA DOSEN');
			$objPHPExcel->getActiveSheet()->setCellValue('F4','RENCANA TM');
			$objPHPExcel->getActiveSheet()->setCellValue('G4','KATEGORI');
			$objPHPExcel->getActiveSheet()->setCellValue('H4','NAMA KELAS');
			$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->applyFromArray($cell_color);
			$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setWrapText(true);
		 	$i=5;
		 	$n=1;
		 	foreach($getPaketKelas as $dtPaket){
		 		if($dtPaket['id_jns_kelas']==$id_jenis){
		 			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
		 			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$dtPaket['smt_def']);
		 			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$dtPaket['kode_mk']);
		 			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$dtPaket['nm_mk']);
		 			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$dtPaket['nm_dosen']);
		 			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$dtPaket['tatap_muka']);
		 			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$dtPaket['jns_kelas']);
		 			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$dtPaket['nm_kelas']);
		 			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($border);
		 			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setWrapText(true);
		 			$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		 			$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		 			$i++;
		 			$n++;
		 		}
		 	}
		 	
		 	// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Daftar Paket Kelas.xls"');
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}

	function export2Action(){
		$user = new Menu();
		$menu = "paketkelas/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// nm pt
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt=$ses_ac->nm_pt;
			// get param
			$kd_pkt=$this->_request->get('kd');
			// get data paket
			$paketkelas=new Paketkelas();
			$getPaket=$paketkelas->getPaketKelasByKd($kd_pkt);
			foreach ($getPaket as $dtPaket){
				$kd_kls=$dtPaket['kd_kelas'];
				$kd_dsn=$dtPaket['kd_dosen'];
				$nm_dsn=$dtPaket['nm_dosen'];
				$kd_mk=$dtPaket['kode_mk'];
				$nm_mk=$dtPaket['nm_mk'];
				$kd_per=$dtPaket['kd_periode'];
				$nm_prodi=$dtPaket['nm_prodi_kur'];
				$jns_kelas=$dtPaket['jns_kelas'];
				$nm_kelas=$dtPaket['nm_kelas'];
				$sks=($dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim']);
			}
			// list mahasiswa
			$kuliah=new Kuliah();
			$getKuliah=$kuliah->getKuliahByPaket($kd_pkt);
			// disabel layout
			$this->_helper->layout->disableLayout();
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Administrator")
										 ->setLastModifiedBy("Akademik")
										 ->setTitle("Export Data Mahasiswa Kuliah")
										 ->setSubject("Sistem Informasi Akademik")
										 ->setDescription("Mahasiswa Kuliah")
										 ->setKeywords("mahasiswa kuliah")
										 ->setCategory("Data File");
										 
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Daftar Paket Kelas');
			
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
			$border = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
			$borderBottom = array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE)));
			$cell_color = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb'=>'CCCCCC')));
			// properties field excel;
			$objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:E2');
			$objPHPExcel->getActiveSheet()->getStyle('A1:E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A9:E9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A2:E2')->applyFromArray($borderBottom);
			$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A9:E9')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A9:E9')->getFont()->setBold(true);
			// column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(45);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			//-- content
			$objPHPExcel->getActiveSheet()->setCellValue('A1',strtoupper($nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('A2','DAFTAR MAHASISWA PENGAMBIL PAKET KELAS');
			$objPHPExcel->getActiveSheet()->setCellValue('A4','PROGRAM STUDI');
			$objPHPExcel->getActiveSheet()->setCellValue('A5','PERIODE AKADEMIK');
			$objPHPExcel->getActiveSheet()->setCellValue('A6','NAMA KELAS');
			$objPHPExcel->getActiveSheet()->setCellValue('A7','DOSEN');
			$objPHPExcel->getActiveSheet()->setCellValue('A8','MATA KULIAH');
			$objPHPExcel->getActiveSheet()->setCellValue('C4',':'.$nm_prodi);
			$objPHPExcel->getActiveSheet()->setCellValue('C5',':'.$kd_per);
			$objPHPExcel->getActiveSheet()->setCellValue('C6',':'.$nm_kelas." (".$jns_kelas.")");
			$objPHPExcel->getActiveSheet()->setCellValue('C7',':'.$nm_dsn);
			$objPHPExcel->getActiveSheet()->setCellValue('C8',':'.$nm_mk);
			$objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
			$objPHPExcel->getActiveSheet()->mergeCells('A5:B5');
			$objPHPExcel->getActiveSheet()->mergeCells('A6:B6');
			$objPHPExcel->getActiveSheet()->mergeCells('A7:B7');
			$objPHPExcel->getActiveSheet()->mergeCells('A8:B8');
			$objPHPExcel->getActiveSheet()->mergeCells('C4:E4');
			$objPHPExcel->getActiveSheet()->mergeCells('C5:E5');
			$objPHPExcel->getActiveSheet()->mergeCells('C6:E6');
			$objPHPExcel->getActiveSheet()->mergeCells('C7:E7');
			$objPHPExcel->getActiveSheet()->mergeCells('C8:E8');
			$objPHPExcel->getActiveSheet()->getStyle('A4:C8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('A4:C8')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A4:A8')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->setCellValue('A9','NO');
			$objPHPExcel->getActiveSheet()->setCellValue('B9','NIM');
			$objPHPExcel->getActiveSheet()->setCellValue('C9','NAMA MAHASISWA');
			$objPHPExcel->getActiveSheet()->setCellValue('D9','ANGKATAN');
			$objPHPExcel->getActiveSheet()->setCellValue('E9','STATUS KRS');
			$objPHPExcel->getActiveSheet()->getStyle('A9:E9')->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('A9:E9')->applyFromArray($cell_color);
			$objPHPExcel->getActiveSheet()->getStyle('A9:E9')->getAlignment()->setWrapText(true);
		 	$i=10;
		 	$n=1;
		 	foreach($getKuliah as $dtKuliah){
	 			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
				$objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$i,$dtKuliah['nim'],PHPExcel_Cell_DataType::TYPE_STRING);
	 			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$dtKuliah['nm_mhs']);
	 			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$dtKuliah['id_angkatan']);
	 			if( $dtKuliah['approved']=='t'){
					$status="APPROVED";
				}else{
					$status="NOT-APPROVED";
				}
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$status);
	 			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray($border);
	 			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->getAlignment()->setWrapText(true);
	 			$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	 			$i++;
	 			$n++;
		 	}
		 	// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Daftar Mahasiswa KRS.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}
	
	function reportAction(){
		$user = new Menu();
		$menu = "paketkelas/report";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="paketkelas/report";
			// session chart
			$param = new Zend_Session_Namespace('param_pkls_chart');
			$prd = $param->prd;
			$per = $param->per;
			$cht=$param->cht;
			// Title Browser
			$this->view->title = "Report Paket Kelas";
			if($cht){
				// layout
				$this->_helper->layout()->setLayout('second');
				// navigation
				$this->_helper->navbar("paketkelas/report",0,0,0,0);
			}else {
				// layout
				$this->_helper->layout()->setLayout('main');
				// navigation
				$this->_helper->navbar(0,0,0,0,0);
			}
			
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
			// get periode
			$periode = new Periode();
			$this->view->listPeriode=$periode->fetchAll();
			if($cht){
				$rep = new Report();
				// axis
				$getTabelX=$rep->getTabel('periode');
				$arrTabelX=explode("||", $getTabelX);
				// where axis
				$where=array();
				$whereX[0]['key'] = $arrTabelX[1];
				$whereX[0]['param'] = $per;
				// data axis
				$arrKolomX=array($arrTabelX[1]);
				$getX= $rep->get($arrTabelX[0], $arrKolomX, $arrKolomX, $arrKolomX, $whereX);
				
				// parameter
				$getTabelParam=$rep->getTabel('jns_kelas');
				$arrTabel=explode("||", $getTabelParam);
				$tabel_param=$arrTabel[0];
				$key_param=$arrTabel[1];
				$label_param=$arrTabel[2];
				//--
				$arrKolomPar=array($key_param,$label_param);
				$wherePar=array();
				//--
				$getPar= $rep->get($tabel_param, $arrKolomPar, $arrKolomPar, $arrKolomPar,$wherePar);
				$arrPar=array();
				foreach ($getPar as $data){
					$arrPar['key'][]=$data[$key_param];
					$arrPar['label'][]=$data[$label_param];
				}
				
				// data 1
				$getTabelData=$rep->getTabel('p_kelas');
				$arrTabelData=explode("||", $getTabelData);
				// where data
				$whereD[0]['key'] = 'kd_prodi_kur';
				$whereD[0]['param'] = $prd;
				$getTabelFil=$rep->getTabel('periode');
				$arrTabelFil=explode("||", $getTabelFil);
				$whereD[1]['key'] = $arrTabelFil[1];
				$whereD[1]['param'] = $per;
				//--
				$arrKolomD=array($arrTabelX[1],$key_param);
				$getReport= $rep->get($arrTabelData[0], $arrKolomD, $arrKolomD, $arrKolomD,$whereD);
				$this->view->x=$rep->query($arrTabelData[0], $arrKolomD, $arrKolomD, $arrKolomD,$whereD);
				// data
				$array=array();
				$i=0;
				foreach ($getX as $data) {
					$array[$i]['x']=$data[$arrTabelX[1]];
					foreach ($arrPar['key'] as $data2){
						$n=0;
						foreach ($getReport as $data3){
							if(($data3[$arrTabelX[1]]==$data[$arrTabelX[1]])and($data3[$key_param]==$data2)){
								$n=$data3['n'];
							}
						}
						$array[$i][$data2]=$n;
					}
					$i++;
				}
				
				// data 2
				$getTabelData=$rep->getTabel('p_kelas_ta');
				$arrTabelData=explode("||", $getTabelData);
				// where data
				$whereD[0]['key'] = 'kd_prodi_kur';
				$whereD[0]['param'] = $prd;
				$getTabelFil=$rep->getTabel('periode');
				$arrTabelFil=explode("||", $getTabelFil);
				$whereD[1]['key'] = $arrTabelFil[1];
				$whereD[1]['param'] = $per;
				//--
				$arrKolomD=array($arrTabelX[1],$key_param);
				$getReport= $rep->get($arrTabelData[0], $arrKolomD, $arrKolomD, $arrKolomD,$whereD);
				$this->view->x=$rep->query($arrTabelData[0], $arrKolomD, $arrKolomD, $arrKolomD,$whereD);
				// data
				$array2=array();
				$i=0;
				foreach ($getX as $data) {
					$array2[$i]['x']=$data[$arrTabelX[1]];
					foreach ($arrPar['key'] as $data2){
						$n=0;
						foreach ($getReport as $data3){
							if(($data3[$arrTabelX[1]]==$data[$arrTabelX[1]])and($data3[$key_param]==$data2)){
								$n=$data3['n'];
							}
						}
						$array2[$i][$data2]=$n;
					}
					$i++;
				}
				// view
				$this->view->labels=$arrPar['label'];
				$this->view->key=$arrPar['key'];
				$this->view->data=$array;
				$this->view->data2=$array2;
				$this->view->chart=$cht;
			}
			// destroy session param
			Zend_Session::namespaceUnset('param_pkls_chart');
		}
	}
}