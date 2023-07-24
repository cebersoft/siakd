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
		$this->view->active_menu="jadwal/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "jadwal/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Jadwal Mata Kuliah";
			// destroy session param
			Zend_Session::namespaceUnset('param_jdwl');
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// get data periode
			$periode = new Periode();
			$this->view->listPeriode=$periode->fetchAll();
		}
	}

	function listAction(){
		$user = new Menu();
		$menu = "jadwal/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// layout
			$this->_helper->layout()->setLayout('second');
			// get param
			$kd_periode=$this->_request->get('id');
			// periode akademik
			$periode = new Periode();
			$getPeriode = $periode->getPeriodeByKd($kd_periode);
			// navigation
			$this->_helper->navbar("jadwal",0,0,"jadwal/export",0);
			if($getPeriode){
				// Title Browser
				$this->view->title = "Jadwal Mata Kuliah ".$kd_periode;
				// get data ruangan, hari, slot
				// prodi
				$prodi = new Prodi();
				$this->view->listProdi=$prodi->fetchAll();
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
				// get data paket kelas
				$jadwal=new Jadwal();
				$getJadwal=$jadwal->getJadwalByPeriode($kd_periode);
				$this->view->listJadwal=$getJadwal;
				// tab hari
				$tab=$this->_request->get('tab');
				if($tab){
					$this->view->tab=$tab;
				}else{
					$this->view->tab='1';
				}
				// create session for excel
				$param = new Zend_Session_Namespace('param_jdwl');
				$param->data=$getJadwal;
				$param->per=$kd_periode;
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Jadwal Mata Kuliah";
			}
		}
	}

	function editAction(){
		$user = new Menu();
		$menu = "jadwal/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$kd_periode=$this->_request->get('per');
			$id_hari=$this->_request->get('hr');
			$id_slot=$this->_request->get('sl');
			$kd_ruangan=$this->_request->get('ro');
			// layout
			$this->_helper->layout()->setLayout('main');
			$jadwal = new Jadwal();
			$getJadwal = $jadwal->getJadwalByKey($kd_periode,$id_hari,$id_slot,$kd_ruangan);
			if($getJadwal){
				// navigation
				$this->_helper->navbar("jadwal/list?id=".$kd_periode,0,0,0,0);
				$this->view->listJadwal = $getJadwal;
				// data
				$hari = new Hari();
				$listHari=$hari->fetchAll();
				$this->view->listHari=$listHari;
			}else{
				$this->view->eksis="f";
				// navigation
				$this->_helper->navbar("jadwal",0,0,0,0);
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
			$param = new Zend_Session_Namespace('param_jdwl');
			$dataJdwl=$param->data;
			$per=$param->per;
			// nm pt
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt=$ses_ac->nm_pt;
			// image path logo
			$path = __FILE__;
			$imgPath = str_replace('academic/application/controllers/JadwalController.php','public/img/logo.png',$path);
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
	
	function reportAction(){
		$user = new Menu();
		$menu = "jadwal/report";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="jadwal/report";
			// session chart
			$param = new Zend_Session_Namespace('param_jdw_chart');
			$per = $param->per;
			$cht=$param->cht;
			// Title Browser
			$this->view->title = "Report Jadwal ".$per;
			if($cht){
				// layout
				$this->_helper->layout()->setLayout('second');
				// navigation
				$this->_helper->navbar("jadwal/report",0,0,0,0);
			}else {
				// layout
				$this->_helper->layout()->setLayout('main');
				// navigation
				$this->_helper->navbar(0,0,0,0,0);
			}
			
			// get periode
			$periode = new Periode();
			$this->view->listPeriode=$periode->fetchAll();
			if($cht){
				$rep = new Report();
				// axis
				$getTabelX=$rep->getTabel('hari');
				$arrTabelX=explode("||", $getTabelX);
				// where axis
				$whereX=array();
				// data axis
				$arrKolomX=array($arrTabelX[1],$arrTabelX[2]);
				$getX= $rep->get($arrTabelX[0], $arrKolomX, $arrKolomX, $arrKolomX, $whereX);
				
				// parameter
				$getTabelParam=$rep->getTabel('slot');
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
				
				// data
				$getTabelData=$rep->getTabel('jadwal');
				$arrTabelData=explode("||", $getTabelData);
				// where data
				$getTabelFil=$rep->getTabel('periode');
				$arrTabelFil=explode("||", $getTabelFil);
				$whereD[0]['key'] = $arrTabelFil[1];
				$whereD[0]['param'] = $per;
				//--
				$arrKolomD=array($arrTabelX[1],$key_param);
				$getReport= $rep->get($arrTabelData[0], $arrKolomD, $arrKolomD, $arrKolomD,$whereD);
				$this->view->x=$rep->query($arrTabelData[0], $arrKolomD, $arrKolomD, $arrKolomD,$whereD);
				// data
				$array=array();
				$i=0;
				foreach ($getX as $data) {
					$array[$i]['x']=$data[$arrTabelX[2]];
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
				// view
				$this->view->labels=$arrPar['label'];
				$this->view->key=$arrPar['key'];
				$this->view->data=$array;
				$this->view->chart=$cht;
			}
			// destroy session param
			Zend_Session::namespaceUnset('param_jdw_chart');
		}
	}
}