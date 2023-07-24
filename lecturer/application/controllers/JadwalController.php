<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Jadwal Controller -> Controller untuk modul halaman jadwal
*/
class JadwalController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Jadwal');
		Zend_Loader::loadClass('Ruangan');
		Zend_Loader::loadClass('Slot');
		Zend_Loader::loadClass('Hari');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Paketkelas');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Validation');
		Zend_Loader::loadClass('PHPExcel');
		Zend_Loader::loadClass('PHPExcel_Cell_AdvancedValueBinder');
		Zend_Loader::loadClass('PHPExcel_IOFactory');
		$auth = Zend_Auth::getInstance();
		$ses_lec = new Zend_Session_Namespace('ses_lec');
		if (($auth->hasIdentity())and($ses_lec->uname)) {
			$this->view->namadsn =Zend_Auth::getInstance()->getIdentity()->nm_dosen;
			$this->view->kddsn=Zend_Auth::getInstance()->getIdentity()->kd_dosen;
			$this->view->kd_pt=$ses_lec->kd_pt;
			$this->view->nm_pt=$ses_lec->nm_pt;
			// global var
			$this->kd_dsn=Zend_Auth::getInstance()->getIdentity()->kd_dosen;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// nav menu
		$this->view->sch_act="active";
	}
	
	function indexAction(){
		// layout
		$this->_helper->layout()->setLayout('main');
		// get param
		$kd_periode=$this->_request->get('id');
		$periode = new Periode();
		if(!$kd_periode){
			// get periode aktif
			$getPeriodeAktif=$periode->getPeriodeByStatus(0);
			if($getPeriodeAktif){
				foreach ($getPeriodeAktif as $dtPeriode) {
					$kd_periode=$dtPeriode['kd_periode'];
				}
			}else{
				$kd_periode="";
			}
		}
		// periode akademik
		$getPeriode = $periode->getPeriodeByKd($kd_periode);
		// navigation
		$this->_helper->navbar(0,"jadwal/export");
		if($getPeriode){
			// Title Browser
			$this->view->title = "Jadwal Mengajar ".$kd_periode;
			$kd_dosen=$this->kd_dsn;
			// get data ruangan, hari, slot
			$this->view->per=$kd_periode;
			//------------------------------
			$slot = new Slot();
			$listSlot = $slot->fetchAll();
			$this->view->listSlot=$listSlot;
			//------------------------------
			$ruangan = new Ruangan();
			$listRuangan=$ruangan->fetchAll();
			$this->view->listRuangan=$listRuangan;
			//------------------------------
			$hari = new Hari();
			$listHari=$hari->fetchAll();
			$this->view->listHari=$listHari;
			// get ajar dosen
			$pKelas=new Paketkelas();
			$getPaketKelas=$pKelas->getPaketKelasByPeriode($kd_periode);
			$paketKelasDsn=array();
			if($getPaketKelas){
				$i=0;
				foreach ($getPaketKelas as $dtPKelas){
					if($dtPKelas['kd_dosen']==$kd_dosen){
						$paketKelasDsn[$i]=$dtPKelas['kd_paket_kelas'];
						$i++;
					}
				}
			}
			// get periode
			$this->view->listPer=$periode->fetchAll();
			// get data jadwal
			$jadwal=new Jadwal();
			$getJadwal=$jadwal->getJadwalByPeriode($kd_periode);
			// get data jadwal mengajar
			$arrJadwal=array();
			$i=0;
			foreach ($getJadwal as $dtJadwal){
				foreach ($paketKelasDsn as $dtPaketKelasDsn) {
					if($dtJadwal['kd_paket_kelas']==$dtPaketKelasDsn){
						$arrJadwal[$i]['id_hari']=$dtJadwal['id_hari'];
						$arrJadwal[$i]['nm_hari']=$dtJadwal['nm_hari'];
						$arrJadwal[$i]['id_slot']=$dtJadwal['id_slot'];
						$arrJadwal[$i]['start_time']=$dtJadwal['start_time'];
						$arrJadwal[$i]['end_time']=$dtJadwal['end_time'];
						$arrJadwal[$i]['kd_ruangan']=$dtJadwal['kd_ruangan'];
						$arrJadwal[$i]['nm_ruangan']=$dtJadwal['nm_ruangan'];
						$arrJadwal[$i]['kd_paket_kelas']=$dtJadwal['kd_paket_kelas'];
						$arrJadwal[$i]['id_nm_kelas']=$dtJadwal['id_nm_kelas'];
						$arrJadwal[$i]['nm_kelas']=$dtJadwal['nm_kelas'];
						$arrJadwal[$i]['kd_dosen']=$dtJadwal['kd_dosen'];
						$arrJadwal[$i]['nm_dosen']=$dtJadwal['nm_dosen'];
						$arrJadwal[$i]['kode_mk']=$dtJadwal['kode_mk'];
						$arrJadwal[$i]['nm_mk']=$dtJadwal['nm_mk'];
						$arrJadwal[$i]['nm_prodi_kur']=$dtJadwal['nm_prodi_kur'];
						$i++;
					}
				}
			}
			$this->view->listJadwal=$arrJadwal;
			// tab hari
			$tab=$this->_request->get('tab');
			if($tab){
				$this->view->tab=$tab;
			}else{
				$this->view->tab='1';
			}
			// create session for excel
			$param = new Zend_Session_Namespace('param_jdwl');
			$param->data=$arrJadwal;
			$param->per=$kd_periode;
		}else{
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Jadwal Mata Kuliah";
		}
	}
	
	function exportAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// session data
		$param = new Zend_Session_Namespace('param_jdwl');
		$dataJdwl=$param->data;
		$per=$param->per;
		// nm pt
		$ses_lec = new Zend_Session_Namespace('ses_lec');
		$nm_pt=$ses_lec->nm_pt;
		// image path logo
		$path = __FILE__;
		$imgPath = str_replace('lecturer\application\controllers\JadwalController.php','public\img\logo.png',$path);
		// konfigurasi excel
		PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Administrator")
									 ->setLastModifiedBy("Akademik")
									 ->setTitle("Export Data Jadwal Perkuliahan")
									 ->setSubject("Sistem Informasi Akademik")
									 ->setDescription("Jadwal Perkuliahan")
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
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
		$objPHPExcel->getActiveSheet()->setCellValue('A4','HARI');
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
	 	$arrRoom[0]="";
	 	$arrPaket[0]="";
	 	$rHari=$i;
	 	$rSlot=$i;
	 	$rRoom=$i;
	 	$rPaket=$i;
	 	foreach($dataJdwl as $dt){
	 		$arrHari[$n]=$dt['id_hari'];
		 	$arrSlot[$n]=$dt['id_slot'];
		 	$arrRoom[$n]=$dt['kd_ruangan'];
		 	$arrPaket[$n]=$dt['kd_paket_kelas'];
		 	
		 	if($arrHari[$n]!=$arrHari[$n-1]){
		 		$rHari=$i;
	 			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,strtoupper($dt['nm_hari']));
		 	}else{
		 		$objPHPExcel->getActiveSheet()->mergeCells("A".$rHari.":A".$i);
		 		$objPHPExcel->getActiveSheet()->mergeCells("A".$rHari.":A".$i);
		 	}
		 	if($arrSlot[$n]!=$arrSlot[$n-1]){
		 		$rSlot=$i;
	 			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$dt['start_time']." s/d ".$dt['end_time']);
		 	}else{
		 		$objPHPExcel->getActiveSheet()->mergeCells("B".$rSlot.":B".$i);
		 	}
		 	if($arrRoom[$n]!=$arrRoom[$n-1]){
		 		$rRoom=$i;
	 			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,strtoupper($dt['nm_ruangan']));
		 	}else{
		 		$objPHPExcel->getActiveSheet()->mergeCells("C".$rRoom.":C".$i);
		 	}
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
		header('Content-Disposition: attachment;filename="Jadwal Perkuliahan.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
}