<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Absensi Controller -> Controller untuk modul halaman absensi
*/
class AbsensiController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Report');
		Zend_Loader::loadClass('Kbm');
		Zend_Loader::loadClass('Absensi');
		Zend_Loader::loadClass('StatHadir');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Kuliah');
		Zend_Loader::loadClass('Paketkelas');
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
		$this->view->active_tree="06";
		$this->view->active_menu="kbm/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "absensi/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get id perkuliahan
			$id_perkuliahan=$this->_request->get('id');
			// Title Browser
			$this->view->title = "Daftar Presensi Mahasiswa";
			// get perkuliahan
			$kbm = new Kbm();
			$getKbm = $kbm->getKbmById($id_perkuliahan);
			if($getKbm){
				// data kbm
				foreach ($getKbm as $dataKbm) {
					$kd_paket=$dataKbm['kd_paket_kelas'];
					$this->view->nm_prodi=$dataKbm['nm_prodi_kur'];
					$this->view->kd_per=$dataKbm['kd_periode'];
					$this->view->nm_kelas=$dataKbm['nm_kelas'];
					$this->view->jns_kelas=$dataKbm['jns_kelas'];
					$this->view->nm_dsn=$dataKbm['nm_dosen'];
					$this->view->nm_mk=$dataKbm['nm_mk'];
					$this->view->kd_mk=$dataKbm['kode_mk'];
					$this->view->hari=$dataKbm['hari'];
					$this->view->tgl=$dataKbm['tgl_kuliah_fmt'];
					$this->view->start=$dataKbm['start_time_fmt'];
					$this->view->end=$dataKbm['end_time_fmt'];
					$this->view->tempat=$dataKbm['tempat'];
				}
				// stat kehadiran
				$statHadir = new StatHadir();
				$this->view->listStatHdr=$statHadir->fetchAll();
				// get data absensi
				$absensi = new Absensi();
				$this->view->listAbsensi=$absensi->getAbsensiByPerkuliahan($id_perkuliahan);
				$this->view->id_perkuliahan=$id_perkuliahan;
				// get data kbm
				$getListKbm=$kbm->getKbmByPaket($kd_paket);
				$this->view->listKbm=$getListKbm;
				// ref krs
				$kuliah=new Kuliah();
				$getKuliah=$kuliah->getKuliahByPaket($kd_paket);
				$this->view->listKuliah=$getKuliah;
				// navigation
				$this->_helper->navbar('kbm/detil?id='.$kd_paket,0,0,0,0);
			}else{
				$this->view->eksis="f";
				$this->_helper->navbar('kbm',0,0,0,0);
			}
		}
	}

	function lpmAction(){
		$user = new Menu();
		$menu = "absensi/lpm";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Log Presensi Mahasiswa";
			// treeview
			$this->view->active_tree="06";
			$this->view->active_menu="absensi/lpm";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// destroy session param
			Zend_Session::namespaceUnset('param_lpm');
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
	
	function plpmAction(){
		$user = new Menu();
		$menu = "absensi/lpm";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Log Presensi Mahasiswa";
			// layout
			$this->_helper->layout()->setLayout('second');
			// navigation
			$this->_helper->navbar('absensi/lpm',0,0,'absensi/elpm',0);
			// treeview
			$this->view->active_tree="06";
			$this->view->active_menu="absensi/lpm";
			// session
			$param = new Zend_Session_Namespace('param_lpm');
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
			// get data absensi
			$absensi=new Absensi();
			$getAbsensi=$absensi->getAbsensiByNimPeriode($nim, $kd_periode);
			$arrPer=array();
			foreach ($getAbsensi as $dtAbsensi){
				$arrPer[]=$dtAbsensi['kd_periode'];
			}
			$arrPer=array_values(array_unique($arrPer));
			$this->view->listPer=$arrPer;
			$this->view->listAbsensi=$getAbsensi;
			$this->view->x=count($getAbsensi);
			// create session for excel
			$param->dataabs=$getAbsensi;
			$param->arrPer=$arrPer;
			$param->nim=$nim;
			$param->nm_mhs=$nm;
			$param->prd=$nm_prd;
			$param->akt=$akt;
			$param->dw=$dw;
		}
	}

	function elpmAction(){
		$user = new Menu();
		$menu = "absensi/lpm";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// session
			$param = new Zend_Session_Namespace('param_lpm');
			$dataAbs = $param->dataabs;
			$arrPer=$param->arrPer;
			$nim=$param->nim;
			$nm=$param->nm_mhs;
			$prd=$param->prd;
			$akt=$param->akt;
			$dw=$param->dw;
			// image path logo
			$path = __FILE__;
			$imgPath = str_replace('academic/application/controllers/AbsensiController.php','public/img/logo.png',$path);
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Administrator")
			->setLastModifiedBy("Akademik")
			->setTitle("Export LPM")
			->setSubject("Sistem Informasi Akademik")
			->setDescription("Log Presensi Mahasiswa")
			->setKeywords("khs")
			->setCategory("Data File");
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Log Presensi Mahasiswa');
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
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
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(50);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(12);
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
			$objPHPExcel->getActiveSheet()->mergeCells('F1:O1');
			$objPHPExcel->getActiveSheet()->mergeCells('C2:O2');
			$objPHPExcel->getActiveSheet()->mergeCells('C3:O3');
			$objPHPExcel->getActiveSheet()->mergeCells('E4:O4');
			$objPHPExcel->getActiveSheet()->mergeCells('E5:O5');
			$objPHPExcel->getActiveSheet()->mergeCells('E6:O6');
			$objPHPExcel->getActiveSheet()->mergeCells('E7:O7');
			$objPHPExcel->getActiveSheet()->mergeCells('E8:O8');
			$objPHPExcel->getActiveSheet()->mergeCells('C4:D4');
			$objPHPExcel->getActiveSheet()->mergeCells('C5:D5');
			$objPHPExcel->getActiveSheet()->mergeCells('C6:D6');
			$objPHPExcel->getActiveSheet()->mergeCells('C7:D7');
			$objPHPExcel->getActiveSheet()->mergeCells('C8:D8');
				
			$objPHPExcel->getActiveSheet()->getStyle('C2:O3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C2:O3')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('C4:O8')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('C2:O8')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C1:O1')->getFont()->setSize(8);
			$objPHPExcel->getActiveSheet()->getStyle('C1:O1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('F1:O1')->getFont()->setItalic(true);
			$objPHPExcel->getActiveSheet()->getStyle('F1:O1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('C3:O3')->applyFromArray(array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE))));
	
			// insert data to excel
			$objPHPExcel->getActiveSheet()->setCellValue('F1','Printed by Administrator : '. date("d m Y h:i:s"));
			$objPHPExcel->getActiveSheet()->setCellValue('C2',strtoupper($this->nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('C3','LOG PRESENSI MAHASISWA');
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
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,'Kelas');
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,'Dosen');
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,'SKS');
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,'Taken');
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,'Pertemuan');
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,'Hadir');
				$objPHPExcel->getActiveSheet()->setCellValue('L'.$i,'Alpha');
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$i,'Sakit');
				$objPHPExcel->getActiveSheet()->setCellValue('N'.$i,'Izin');
				$objPHPExcel->getActiveSheet()->setCellValue('O'.$i,'Persentase');
				//konfigurasi field
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':O'.$i)->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':O'.$i)->applyFromArray($cell_color);
				$objPHPExcel->getActiveSheet()->mergeCells('C'.($i-1).':D'.($i-1));
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':O'.$i)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':O'.$i)->applyFromArray($borderBottom);
				$i++;
				$rowAwal=$i;
				$n=1;
				foreach ($dataAbs as $dataExport) {
					if($dataExport['kd_periode']==$dtPer){
						$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$n);
						$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$dataExport['kode_mk']);
						$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$dataExport['nm_mk']);
						$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$dataExport['nm_kelas']);
						$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$dataExport['nm_dosen']);
						$sks_def=$dataExport['sks_tm']+$dataExport['sks_prak']+$dataExport['sks_prak_lap']+$dataExport['sks_sim'];
						$sks_take=$dataExport['sks_tm']+$dataExport['sks_prak']+$dataExport['sks_prak_lap']+$dataExport['sks_sim']-$dataExport['sks_deducted'];
						$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$sks_def);
						$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$sks_take);
						$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$dataExport['n_perkuliahan']);
						$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,$dataExport['n_hadir']);
						$objPHPExcel->getActiveSheet()->setCellValue('L'.$i,$dataExport['n_alpha']);
						$objPHPExcel->getActiveSheet()->setCellValue('M'.$i,$dataExport['n_sakit']);
						$objPHPExcel->getActiveSheet()->setCellValue('N'.$i,$dataExport['n_izin']);
						if($dataExport['n_perkuliahan']==0){
							$persen=0;
						}else{
							$persen=number_format(($dataExport['n_hadir']/$dataExport['n_perkuliahan']*100),2,',','.');
						}
						$objPHPExcel->getActiveSheet()->setCellValue('O'.$i,$persen." %");
						//konfigurasi field
						$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':O'.$i)->applyFromArray($borderInside);
						$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray($borderLeft);
						$objPHPExcel->getActiveSheet()->getStyle('P'.$i)->applyFromArray($borderLeft);
						$n++;
						$i++;
					}
				}
				$i=$i+2;
			}
				
			$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
			$objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
			$objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
			$objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);
				
			//Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Log Presensi Mahasiswa.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}
	
	function reportAction()
	{
		$user = new Menu();
		$menu = "absensi/report";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="absensi/report";
			// session report
			$param = new Zend_Session_Namespace('param_abs_chart');
			$akt = $param->akt;
			$prd = $param->prd;
			$per = $param->per;
			$par = $param->par;
			if($per){
				// layout
				$this->_helper->layout()->setLayout('second');
				// navigation
				$this->_helper->navbar("absensi/report",0,0,0,0);
			}else {
				// layout
				$this->_helper->layout()->setLayout('main');
				// navigation
				$this->_helper->navbar(0,0,0,0,0);
			}
			// Title Browser
			$this->view->title = "Report Absensi";
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
			// get periode
			$periode = new Periode();
			$this->view->listPeriode=$periode->fetchAll();
			// get data angkatan
			$angkatan = new Angkatan();
			$this->view->listAkt=$angkatan->fetchAll();
			if($per){
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
				$getTabelParam=$rep->getTabel($par);
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
				$getTabelData=$rep->getTabel('absensi');
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
				$arrKolomD=array($arrTabelX[1],$key_param);
				//$this->view->x=$rep->query($arrTabelData[0], $arrKolomD, $arrKolomD, $arrKolomD,$whereD);
				$getReport= $rep->get($arrTabelData[0], $arrKolomD, $arrKolomD, $arrKolomD,$whereD);
				// data
				$j=0;
				foreach ($getX as $dtX) {
					$keyX=$dtX[$arrTabelX[1]];
					$array[$dtX[$arrTabelX[1]]]=array();
					$i=0;
					foreach ($arrPar['key'] as $data1){
						$array[$dtX[$arrTabelX[1]]][$i]['label']=$arrPar['label'][$i];
						$n=0;
						foreach ($getReport as $data2){
							if(($data2[$key_param]==$data1)and($keyX==$data2[$arrTabelX[1]])){
								$n=$data2['n'];
							}
						}
						$array[$keyX][$i]['value']=$n;
						$i++;
					}
					// view
					$data[$keyX]=$array[$keyX];
					$j++;
				}
				$this->view->data=$data;
				$this->view->per=$per;
				$this->view->listX=$getX;
			}
			// destroy session param
			Zend_Session::namespaceUnset('param_abs_chart');
		}
	}

	function repsumAction(){
		$user = new Menu();
		$menu = "absensi/repsum";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// destroy session param export
			Zend_Session::namespaceUnset('param_sumabsensi_erep');
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="absensi/repsum";
			// title
			$this->view->title = "Rekap Kehadiran (Summary)";
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
	
	function prepsumAction(){
		$user = new Menu();
		$menu = "absensi/repsum";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// navigation
			$this->_helper->navbar('absensi/repsum',0,0,'absensi/erepsum',0);
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="absensi/repsum";
			// layout
			$this->_helper->layout()->setLayout('second');
			// session
			$param = new Zend_Session_Namespace('param_sumabsensi_erep');
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
			$this->view->title = "Rekap Kehadiran (Summary) ".$kd_periode.' Prodi '.$nm_prd." (Summary)";
			// get data paket kelas
			$paket=new Paketkelas();
			$getPaket=$paket->getPaketKelasByPeriodeProdi($kd_periode,$kd_prodi);
			$arrPaket=array();
			// get perkuliahan
			$kbm=new Kbm();
			$absensi=new Absensi();
			$i=0;
			foreach ($getPaket as $dataPaket){
				$arrPaket[$i]['kode_mk']=$dataPaket['kode_mk'];
				$arrPaket[$i]['nm_mk']=$dataPaket['nm_mk'];
				$arrPaket[$i]['kd_dosen']=$dataPaket['kd_dosen'];
				$arrPaket[$i]['nm_dosen']=$dataPaket['nm_dosen'];
				$arrPaket[$i]['smt_def']=$dataPaket['smt_def'];
				$arrPaket[$i]['sks_tot']=$dataPaket['sks_tm']+$dataPaket['sks_prak']+$dataPaket['sks_prak_lap']+$dataPaket['sks_sim'];
				$arrPaket[$i]['nm_kelas']=$dataPaket['nm_kelas'];
				$arrPaket[$i]['jns_kelas']=$dataPaket['jns_kelas'];
				$getKbm=$kbm->getKbmByPaket($dataPaket['kd_paket_kelas']);
				$nKbm=count($getKbm);
				$tm=$dataPaket['tatap_muka'];
				if ($tm==0){
					$tm=14;
				}
				$arrPaket[$i]['tatap_muka']=$tm;
				$arrPaket[$i]['persen_dosen']=number_format(($nKbm*100/$tm),2,',','.');
				$arrPaket[$i]['n_kbm']=$nKbm;
				$getAbsensi=$absensi->getAbsensiByPaketKelas($dataPaket['kd_paket_kelas']);
				if(!$getAbsensi){
					$arrPaket[$i]['persen_hadir']=0;
					$arrPaket[$i]['persen_alpa']=0;
					$arrPaket[$i]['persen_sakit']=0;
					$arrPaket[$i]['persen_izin']=0;
					$arrPaket[$i]['persen_not']=0;
				}else{
					$n_all=0;
					$n_not=0;
					$n_hdr=0;
					$n_alpa=0;
					$n_sakit=0;
					$n_izin=0;
					foreach($getAbsensi as $dtAbsensi){
						if($dtAbsensi['id_hadir']==0){
							$n_not++;
						}elseif($dtAbsensi['id_hadir']==1){
							$n_hdr++;
						}elseif($dtAbsensi['id_hadir']==2){
							$n_alpa++;		
						}elseif($dtAbsensi['id_hadir']==3){
							$n_sakit++;		
						}elseif($dtAbsensi['id_hadir']==4){
							$n_izin++;
						}
						$n_all++;
					}
					$arrPaket[$i]['persen_hadir']=number_format(($n_hdr*100/$n_all),2,',','.');
					$arrPaket[$i]['persen_alpa']=number_format(($n_alpa*100/$n_all),2,',','.');
					$arrPaket[$i]['persen_sakit']=number_format(($n_sakit*100/$n_all),2,',','.');
					$arrPaket[$i]['persen_izin']=number_format(($n_izin*100/$n_all),2,',','.');
					$arrPaket[$i]['persen_not']=number_format(($n_not*100/$n_all),2,',','.');
				}
				$i++;
			}
			$this->view->listPaket=$arrPaket;
			// session for excel
			$param->dataKehadiran=$arrPaket;
			$param->nmPrd=$nm_prd;
		}
	}
	
	function erepsumAction(){
		$user = new Menu();
		$menu = "absensi/repsum";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// session
			$param = new Zend_Session_Namespace('param_sumabsensi_erep');
			$dataKehadiran = $param->dataKehadiran;
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
			->setTitle("Rekap Kehadiran (Summary)")
			->setSubject("Sistem Informasi Akademik")
			->setDescription("Rekap Kehadiran")
			->setKeywords("rekap kehadiran")
			->setCategory("Data File");
				
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Rekap Kehadiran');
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
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(17);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getStyle('A1:O3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1:O3')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A1:O3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:O1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:O2');
			$objPHPExcel->getActiveSheet()->mergeCells('A3:O3');
			// insert data to excel
			$objPHPExcel->getActiveSheet()->setCellValue('A1',strtoupper($nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('A2','REKAP KEHADIRAN (SUMMARY)');
			$objPHPExcel->getActiveSheet()->setCellValue('A3','PROGRAM STUDI '.$nmPrd.' PERIODE AKADEMIK '.$kdPeriode);
			// data
			$objPHPExcel->getActiveSheet()->setCellValue('A4','NO');
			$objPHPExcel->getActiveSheet()->setCellValue('B4','MATA KULIAH');
			$objPHPExcel->getActiveSheet()->setCellValue('C4','KODE');
			$objPHPExcel->getActiveSheet()->setCellValue('D4','SKS');
			$objPHPExcel->getActiveSheet()->setCellValue('E4','SEMESTER');
			$objPHPExcel->getActiveSheet()->setCellValue('F4','DOSEN');
			$objPHPExcel->getActiveSheet()->setCellValue('G4','KELAS');
			$objPHPExcel->getActiveSheet()->setCellValue('H4','RENCANA');
			$objPHPExcel->getActiveSheet()->setCellValue('I4','REALISASI');
			$objPHPExcel->getActiveSheet()->setCellValue('J4','%DOSEN');
			$objPHPExcel->getActiveSheet()->setCellValue('K4','%MAHASISWA');
			$objPHPExcel->getActiveSheet()->setCellValue('K5','H');
			$objPHPExcel->getActiveSheet()->setCellValue('L5','A');
			$objPHPExcel->getActiveSheet()->setCellValue('M5','S');
			$objPHPExcel->getActiveSheet()->setCellValue('N5','I');
			$objPHPExcel->getActiveSheet()->setCellValue('O5','N/A');
			$objPHPExcel->getActiveSheet()->mergeCells('A4:A5');
			$objPHPExcel->getActiveSheet()->mergeCells('B4:B5');
			$objPHPExcel->getActiveSheet()->mergeCells('C4:C5');
			$objPHPExcel->getActiveSheet()->mergeCells('D4:D5');
			$objPHPExcel->getActiveSheet()->mergeCells('E4:E5');
			$objPHPExcel->getActiveSheet()->mergeCells('F4:F5');
			$objPHPExcel->getActiveSheet()->mergeCells('G4:G5');
			$objPHPExcel->getActiveSheet()->mergeCells('H4:H5');
			$objPHPExcel->getActiveSheet()->mergeCells('I4:I5');
			$objPHPExcel->getActiveSheet()->mergeCells('J4:J5');
			$objPHPExcel->getActiveSheet()->mergeCells('K4:O4');
			$i=6;
			$n=1;
			$objPHPExcel->getActiveSheet()->getStyle('A4:O5')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A4:O5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A4:O5')->applyFromArray($cell_color);
			$objPHPExcel->getActiveSheet()->getStyle('A4:O5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A4:O5')->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('A4:O5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			foreach ($dataKehadiran as $dtAbs){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$dtAbs['nm_mk']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$dtAbs['kode_mk']);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$dtAbs['sks_tot']);
				$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$dtAbs['smt_def']);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$dtAbs['nm_dosen']);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$dtAbs['nm_kelas']." ".$dtAbs['jns_kelas']);
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$dtAbs['tatap_muka']);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$dtAbs['n_kbm']);
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$dtAbs['persen_dosen']);
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,$dtAbs['persen_hadir']);
				$objPHPExcel->getActiveSheet()->setCellValue('L'.$i,$dtAbs['persen_alpa']);
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$i,$dtAbs['persen_sakit']);
				$objPHPExcel->getActiveSheet()->setCellValue('N'.$i,$dtAbs['persen_izin']);
				$objPHPExcel->getActiveSheet()->setCellValue('O'.$i,$dtAbs['persen_not']);
				$objPHPExcel->getActiveSheet()->getStyle('G'.$i.':O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i) ->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle('J'.$i.':O'.$i)->getNumberFormat()->setFormatCode('#,##0.00');
				$n++;
				$i++;
			}
			//Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Rekap Kehadiran Summary.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}

}