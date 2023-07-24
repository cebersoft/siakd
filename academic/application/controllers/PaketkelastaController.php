<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Paket Kelas TA Controller -> Controller untuk modul halaman paket kelas TA
*/
class PaketkelastaController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('KelasTA');
		Zend_Loader::loadClass('PaketkelasTA');
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
		$this->view->active_menu="kelasta/index";
	}
	
	function indexAction(){

	}

	function listAction()
	{
		$user = new Menu();
		$menu = "paketkelasta/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$kd_kls=$this->_request->get('kd');
			// navigation
			$this->_helper->navbar('kelasta/list',0,0,0,0);
			// get data kelas dan paket kelas
			$KelasTA = new KelasTA();
			$getKelasTA = $KelasTA->getKelasTAByKd($kd_kls);
			if($getKelasTA){
				foreach ($getKelasTA as $data_kls) {
					$this->view->kd_mk=$data_kls['kode_mk'];
					$this->view->nm_mk=$data_kls['nm_mk'];
					$this->view->kd_per=$data_kls['kd_periode'];
					$this->view->nm_prodi=$data_kls['nm_prodi_kur'];
					$this->view->jns_kelas=$data_kls['jns_kelas'];
					$this->view->sks=($data_kls['sks_tm']+$data_kls['sks_prak']+$data_kls['sks_prak_lap']+$data_kls['sks_sim']);
					$nm_mk=$data_kls['nm_mk'];
					$nm_prodi=$data_kls['nm_prodi_kur'];
					// title
					$this->view->title="Daftar Paket Kelas TA ".$nm_mk."-".$nm_prodi;
					$paketkelasta = new PaketkelasTA();
					$getPaketTA = $paketkelasta->getPaketKelasTAByKelas($kd_kls);
					$this->view->listPaketTA = $getPaketTA;
				}
			}else{
				$this->view->eksis="f";
				// title
				$this->view->title="Daftar Paket Kelas TA";
			}
		}
	}

	function exportAction(){
		$user = new Menu();
		$menu = "paketkelasta/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$kd_prodi=$this->_request->get('prd');
			$kd_periode=$this->_request->get('per');
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
			// get data paket kelas ta
			$paketkelasTa=new PaketkelasTA();
			$getPaketKelas=$paketkelasTa->getPaketKelasTAByPeriodeProdi($kd_periode, $kd_prodi);
			// disabel layout
			$this->_helper->layout->disableLayout();
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Administrator")
										 ->setLastModifiedBy("Akademik")
										 ->setTitle("Export Data Paket Kelas TA")
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
			$objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:E2');
			$objPHPExcel->getActiveSheet()->getStyle('A1:E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A2:E2')->applyFromArray($borderBottom);
			$objPHPExcel->getActiveSheet()->getStyle('A1:E2')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('A1:E2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getFont()->setBold(true);
			// column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
			//-- content
			$objPHPExcel->getActiveSheet()->setCellValue('A1',strtoupper($nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('A2','DAFTAR PAKET KELAS TUGAS AKHIR PERIODE '.$kd_periode.' PROGRAM STUDI '.$nmProdi);
			$objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue('A4','NO');
			$objPHPExcel->getActiveSheet()->setCellValue('B4','SMT');
			$objPHPExcel->getActiveSheet()->setCellValue('C4','KODE MK');
			$objPHPExcel->getActiveSheet()->setCellValue('D4','NAMA MK');
			$objPHPExcel->getActiveSheet()->setCellValue('E4','DOSEN');
			$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->applyFromArray($cell_color);
			$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(37);
		 	$i=5;
		 	$n=1;
		 	foreach($getPaketKelas as $dtPaket){
	 			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
	 			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$dtPaket['smt_def']);
	 			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$dtPaket['kode_mk']);
	 			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$dtPaket['nm_mk']);
	 			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$dtPaket['nm_dosen']);
	 			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray($border);
	 			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->getAlignment()->setWrapText(true);
	 			$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	 			$i++;
	 			$n++;
		 	}
		 	
		 	// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Daftar Paket Kelas TA.xls"');
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}
}