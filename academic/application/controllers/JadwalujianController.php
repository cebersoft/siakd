<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Jadwal Ujian Controller -> Controller untuk modul halaman jadwal ujian
*/
class JadwalujianController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('JadwalUjian');
		Zend_Loader::loadClass('Ruangan');
		Zend_Loader::loadClass('SlotUjian');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Prodi');
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
		$this->view->active_tree="08";
		$this->view->active_menu="jadwalujian/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "jadwalujian/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Jadwal Ujian";
			// destroy session param
			Zend_Session::namespaceUnset('param_jdwlujian');
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// get data periode
			$periode = new Periode();
			$this->view->listPeriode=$periode->fetchAll();
		}
	}

	function listAction(){
		$user = new Menu();
		$menu = "jadwalujian/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// layout
			$this->_helper->layout()->setLayout('second');
			// get param
			$kd_periode=$this->_request->get('id');
			$jns=$this->_request->get('u');
			$t=$this->_request->get('t');
			if(($jns!='1')and($jns!='2')){
				$this->view->eksis="f";
				// navigation
				$this->_helper->navbar("jadwalujian",0,0,0,0);
				// Title Browser
				$this->view->title = "Jadwal Ujian";
			}else{
				// periode akademik
				$periode = new Periode();
				$getPeriode = $periode->getPeriodeByKd($kd_periode);
				// navigation
				$this->_helper->navbar("jadwalujian",0,0,"jadwalujian/export",0);
				if($getPeriode){
					// Title Browser
					if($jns==1){
						$this->view->title = "Jadwal UTS ".$kd_periode;
					}elseif ($jns==2){
						$this->view->title = "Jadwal UAS ".$kd_periode;
					}
					$this->view->u=$jns;
					// get data ruangan, hari, slot
					// prodi
					$prodi = new Prodi();
					$this->view->listProdi=$prodi->fetchAll();
					$this->view->per=$kd_periode;
					//------------------------------
					$slot = new SlotUjian();
					$listSlot = $slot->fetchAll();
					$this->view->listSlot=$listSlot;
					//------------------------------
					$ruangan = new Ruangan();
					$listRuangan=$ruangan->fetchAll();
					$this->view->listRuangan=$listRuangan;
					// get data paket kelas
					$jadwal=new JadwalUjian();
					$getJadwal=$jadwal->getJadwalByPeriode($kd_periode,$jns);
					$this->view->listJadwal=$getJadwal;
					// tanggal
					$tgl=array();
					$i=0;
					$eksTgl=0;
					foreach ($getJadwal as $dtJadwal){
						$tgl[$i]=$dtJadwal['hari'].", ".$dtJadwal['tanggal_fmt'];
						//-- cek tanggal dr url
						if(date('d F Y',strtotime($dtJadwal['tanggal_fmt']))==date('d F Y',strtotime($t))){
							$eksTgl++;
						}
						$i++;
					}
					if($eksTgl>0){
						$this->view->tgl=date('d F Y',strtotime($t));
					}else{
						$this->view->tgl="f";
						$this->view->dOview="disabled='disabled'";
					}
					$tgl=array_unique($tgl);
					$this->view->arrTgl=$tgl;
					// create session for excel
					$param = new Zend_Session_Namespace('param_jdwlujian');
					$param->data=$getJadwal;
					$param->per=$kd_periode;
				}else{
					$this->view->eksis="f";
					// Title Browser
					$this->view->title = "Jadwal Mata Kuliah";
				}
			}
		}
	}

	function editAction(){
		$user = new Menu();
		$menu = "jadwalujian/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$kd_periode=$this->_request->get('per');
			$tgl=$this->_request->get('tgl');
			$id_slot=$this->_request->get('sl');
			$kd_ruangan=$this->_request->get('ro');
			// layout
			$this->_helper->layout()->setLayout('main');
			$jadwal = new JadwalUjian();
			$getJadwal = $jadwal->getJadwalByKey($kd_periode, $tgl, $id_slot, $kd_ruangan);
			if($getJadwal){
				foreach ($getJadwal as $dtJadwal){
					$jns=$dtJadwal['jns_ujian'];
				}
				$this->view->u=$jns;
				// navigation
				$this->_helper->navbar("jadwalujian/list?id=".$kd_periode.'&u='.$jns,0,0,0,0);
				$this->view->listJadwal = $getJadwal;
			}else{
				$this->view->eksis="f";
				// navigation
				$this->_helper->navbar("jadwalujian",0,0,0,0);
			}
			// title browser
			$this->view->title="Ubah data Jadwal Kuliah";
		}
	}
	
	function exportAction(){
		$user = new Menu();
		$menu = "jadwal/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// session data
			$param = new Zend_Session_Namespace('param_jdwlujian');
			$dataJdwl=$param->data;
			$per=$param->per;
			// nm pt
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt=$ses_ac->nm_pt;
			// image path logo
			$path = __FILE__;
			$imgPath = str_replace('academic\application\controllers\JadwalujianController.php','public\img\logo.png',$path);
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Administrator")
										 ->setLastModifiedBy("Akademik")
										 ->setTitle("Export Data Jadwal Ujian")
										 ->setSubject("Sistem Informasi Akademik")
										 ->setDescription("Jadwal Ujian")
										 ->setKeywords("jadwal")
										 ->setCategory("Data File");
										 
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Jadwal Perkuliahan');
			
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
			$objPHPExcel->getActiveSheet()->getStyle('B2:H2')->applyFromArray($borderBottom);
			$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getFont()->setBold(true);
			// column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(28);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(17);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(32);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(25);
			$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(25);
			$objPHPExcel->getActiveSheet()->getRowDimension(3)->setRowHeight(25);
			// drawing logo
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Logo');
			$objDrawing->setDescription('Logo');
			$objDrawing->setPath($imgPath);
			$objDrawing->setHeight(55);
			$objDrawing->setWidth(75);
			$objDrawing->setCoordinates('A1');
			$objDrawing->setOffsetX(15);
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			//-- content
			$objPHPExcel->getActiveSheet()->setCellValue('A1',strtoupper($nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('A2','JADWAL PERKULIAHAN PERIODE '.$per);
			$objPHPExcel->getActiveSheet()->setCellValue('A4','HARI, TANGGAL');
			$objPHPExcel->getActiveSheet()->setCellValue('B4','SLOT');
			$objPHPExcel->getActiveSheet()->setCellValue('C4','RUANGAN');
			$objPHPExcel->getActiveSheet()->setCellValue('D4','KELAS');
			$objPHPExcel->getActiveSheet()->setCellValue('E4','DOSEN');
			$objPHPExcel->getActiveSheet()->setCellValue('F4','KODE MK');
			$objPHPExcel->getActiveSheet()->setCellValue('G4','NAMA MK');
			$objPHPExcel->getActiveSheet()->setCellValue('H4','PRODI');
			$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->applyFromArray($cell_color);
		 	$i=5;
		 	$n=1;
		 	$arrHari[0]="";
		 	$arrSlot[0]="";
		 	$arrPaket[0]="";
		 	$rHari=$i;
		 	$rSlot=$i;
		 	$rPaket=$i;
		 	foreach($dataJdwl as $dt){
		 		$arrHari[$n]=$dt['tanggal_fmt'];
			 	$arrSlot[$n]=$dt['id_slot_ujian'];
			 	$arrPaket[$n]=$dt['kd_paket_kelas'];
			 	
			 	if($arrHari[$n]!=$arrHari[$n-1]){
			 		$rHari=$i;
		 			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,strtoupper($dt['hari'].", ".$dt['tanggal_fmt']));
			 	}else{
			 		$objPHPExcel->getActiveSheet()->mergeCells("A".$rHari.":A".$i);
			 	}
			 	if($arrSlot[$n]!=$arrSlot[$n-1]){
			 		$rSlot=$i;
		 			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$dt['start_time']." s/d ".$dt['end_time']);
			 	}else{
			 		$objPHPExcel->getActiveSheet()->mergeCells("B".$rSlot.":B".$i);
			 	}
			 	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,strtoupper($dt['nm_ruangan']));
		 		$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,strtoupper($dt['nm_kelas']));
		 		$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,strtoupper($dt['nm_dosen']));
		 		$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,strtoupper($dt['kode_mk']));
		 		$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,strtoupper($dt['nm_mk']));
		 		$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,strtoupper($dt['nm_prodi_kur']));
		 		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setWrapText(true);
		 		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		 		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		 		$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		 		$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		 		$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		 		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($border);
		 		$i++;
		 		$n++;
		 	}
		 	
		 	$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
			$objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
			$objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
			$objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);
		 	
		 	// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Jadwal Ujian.xls"');
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}
}