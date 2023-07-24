<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Ujian Controller -> Controller untuk modul halaman ujian
*/
class UjianController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Register');
		Zend_Loader::loadClass('Kuliah');
		Zend_Loader::loadClass('Paketkelas');
		Zend_Loader::loadClass('JadwalUjian');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Zend_Barcode');
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
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "ujian/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Kartu Ujian (UTS/UAS)";
			// treeview
			$this->view->active_tree="08";
			$this->view->active_menu="ujian/index";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			$angkatan = new Angkatan();
			$this->view->listAkt = $angkatan->fetchAll();
			$prodi = new Prodi();
			$this->view->listProdi = $prodi->fetchAll();
			$periode = new Periode();
			$this->view->listPeriode = $periode->fetchAll();
		}
	}
	
	function excelAction(){
		$user = new Menu();
		$menu = "ujian/excel";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// makes disable layout
			$this->_helper->getHelper('layout')->disableLayout();
			$akt=$this->_request->get('akt');
			$prd=$this->_request->get('prd');
			$per=$this->_request->get('per');
			$jns=$this->_request->get('jns');
			$ses = new Zend_Session_Namespace('ses_ac');
			$nm_pt=$ses->nm_pt;
			$arrPeriode = explode("/",$per);
			if ($jns=='1'){
				$jenis = "UJIAN TENGAH SEMESTER";
			}else{
				$jenis = "UJIAN AKHIR SEMESTER";
			}
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Akademik")
										 ->setLastModifiedBy("Akademik")
										 ->setTitle("Kartu Ujian")
										 ->setSubject("Sistem Informasi Akademik")
										 ->setDescription("Kartu Ujian")
										 ->setKeywords("Kartu Ujian")
										 ->setCategory("Data File");
										 
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Kartu Ujian Mahasiswa');
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT)
														  ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
														  ->setFitToWidth('1')
														  ->setFitToHeight('Automatic')
														  ->SetHorizontalCentered(true)->setVerticalCentered(true);
			// margin
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft((0.5/2.54))
															->setTop((1/2.54))
															->setRight((0.5/2.54))
															->setBottom(0);
			// default height and font
			$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(13);
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Tahoma');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(9);
			// column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(3);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(8.5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(52);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(27);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(19);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(19);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(3);
			// image path
			$path = __FILE__;
			$imgPath = str_replace('academic/application/controllers/UjianController.php','public/img/logo.png',$path);
			// start write content
			$n=1;
			$i=1;
			// get data mahasiswa by angkatan dan prodi
			$register = new Register();
			$getRegister = $register->getRegisterByPeriodeAngkatanProdi($per, $akt, $prd);
			// get studi jadwal ujian by nim
			$jadwalUjian =new JadwalUjian();
			$rdm=time();
			foreach ($getRegister as $dataMhs){
				if($dataMhs['kd_status_reg']!=null){
					$rowAwal = $i;
					// drawing logo
					$objDrawing = new PHPExcel_Worksheet_Drawing();
					$objDrawing->setName('Logo1');
					$objDrawing->setDescription('Logo1');
					$objDrawing->setPath($imgPath);
					$objDrawing->setHeight(70);
					$objDrawing->setWidth(70);
					$objDrawing->setCoordinates('B'.$rowAwal);
					$objDrawing->setOffsetX(25);
					$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
					// drawing barcode
					// Only the text to draw is required
					$barcodeOptions = array('text' => $dataMhs['nim']."-".$per,'drawText'=>false);
					// No required options
					$rendererOptions = array();
					$x = $n;
					// send the headers and the image
					$imageResource = Zend_Barcode::draw('code128', 'image', $barcodeOptions, $rendererOptions);
					imagejpeg($imageResource, 'public/barcode/'.$rdm."-".$x.'.jpg', 100);
					// path barcode
					$barcodePath = str_replace('application/controllers/UjianController.php','public/barcode/'.$rdm."-".$x.'.jpg',$path);
					// Free up memory
					imagedestroy($imageResource);
					$objDrawing = new PHPExcel_Worksheet_Drawing();
					$objDrawing->setName('barcode'.$n);
					$objDrawing->setDescription('barcode'.$n);
					$objDrawing->setPath($barcodePath);
					$objDrawing->setResizeProportional(false);
					$objDrawing->setHeight(52);
					$objDrawing->setWidth(172);
					$objDrawing->setCoordinates('F'.$i);
					$objDrawing->setOffsetX(37);
					$objDrawing->setOffsetY(5);
					$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
					$i++;
					$objPHPExcel->getActiveSheet()->mergeCells('D'.($i).':E'.($i));
					$objPHPExcel->getActiveSheet()->setCellValue('D'.($i),strtoupper($nm_pt));
					$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setBold(true)->setSize(11);
					$i=$i+1;
					$objPHPExcel->getActiveSheet()->setCellValue('D'.($i),'KARTU '.$jenis);
					$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setBold(true)->setSize(11);
					$i=$i+1;
					$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':H'.$i)->applyFromArray(array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE))));
					$objPHPExcel->getActiveSheet()->mergeCells('B'.($i).':G'.($i));
					$i++;
					$objPHPExcel->getActiveSheet()->setCellValue('B'.($i),'PERIODE');
					$objPHPExcel->getActiveSheet()->mergeCells('B'.($i).':C'.($i));
					$objPHPExcel->getActiveSheet()->setCellValue('D'.($i),': '.$per);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':D'.$i)->getFont()->setBold(true)->setSize(10);
					$i++;
					$objPHPExcel->getActiveSheet()->setCellValue('B'.($i),'NPM');
					$objPHPExcel->getActiveSheet()->mergeCells('B'.($i).':C'.($i));
					$objPHPExcel->getActiveSheet()->setCellValue('D'.($i),': '.$dataMhs['nim']);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':D'.$i)->getFont()->setBold(true)->setSize(10);
					$i++;
					$objPHPExcel->getActiveSheet()->setCellValue('B'.($i),'NAMA');
					$objPHPExcel->getActiveSheet()->mergeCells('B'.($i).':C'.($i));
					$objPHPExcel->getActiveSheet()->setCellValue('D'.($i),': '.$dataMhs['nm_mhs']);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':D'.$i)->getFont()->setBold(true)->setSize(10);
					$i=$i+2;
					$objPHPExcel->getActiveSheet()->mergeCells('B'.($i).':B'.($i+1));
					$objPHPExcel->getActiveSheet()->mergeCells('C'.($i).':C'.($i+1));
					$objPHPExcel->getActiveSheet()->mergeCells('D'.($i).':D'.($i+1));
					$objPHPExcel->getActiveSheet()->mergeCells('E'.($i).':G'.$i);
					$objPHPExcel->getActiveSheet()->mergeCells('H'.($i).':H'.($i+1));
					
					$objPHPExcel->getActiveSheet()->setCellValue('B'.($i),'NO');
					$objPHPExcel->getActiveSheet()->setCellValue('C'.($i),'KODE');
					$objPHPExcel->getActiveSheet()->setCellValue('D'.($i),'MATA KULIAH');
					$objPHPExcel->getActiveSheet()->setCellValue('E'.($i),'JADWAL');
					$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+1),'HARI/TANGGAL');
					$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+1),'WAKTU');
					$objPHPExcel->getActiveSheet()->setCellValue('G'.($i+1),'RUANG');
					$objPHPExcel->getActiveSheet()->setCellValue('H'.($i),'PENGAWAS');
					$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':H'.($i+1))->getFont()->setBold(true)->setSize(10);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':H'.($i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':H'.($i+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':H'.($i+1))->applyFromArray(array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
					$i=$i+2;
					$j=$i;
					$nMk=1;
					$kuliah = new Kuliah();
					//$getKuliah = $kuliah->getKuliahByNimPeriode($dataMhs['nim'], $per);
					//$getJadwal = $jadwalUjian->getJadwalByPeriode($per, $jns);
					$getJadwalMhs = $jadwalUjian->getMhsKuliahJadwalUjianByNimPeriode($dataMhs['nim'],$per,$jns);
					foreach ($getJadwalMhs as $dtKul){
						if(($dtKul['approved']=='t')and($dtKul['a_ta']=='f')and($dtKul['sks_prak_lap']==0)and($dtKul['sks_sim']==0)){
							$objPHPExcel->getActiveSheet()->setCellValue('B'.($j),$nMk);
							$objPHPExcel->getActiveSheet()->setCellValue('C'.($j),$dtKul['kode_mk']);
							$objPHPExcel->getActiveSheet()->setCellValue('D'.($j),$dtKul['nm_mk']);
							if($dtKul['tanggal_fmt']!=''){
								$objPHPExcel->getActiveSheet()->setCellValue('E'.($j),$dtKul['hari'].', '.$dtKul['tanggal_fmt']);
							}
							$startTime=date('H:i',strtotime($dtKul['start_time']));
							$endTime=date('H:i',strtotime($dtKul['end_time']));
							if($dtKul['id_slot_ujian']!=''){
								$objPHPExcel->getActiveSheet()->setCellValue('F'.($j),$startTime." s/d ".$endTime);
							}
							$objPHPExcel->getActiveSheet()->setCellValue('G'.($j),$dtKul['nm_ruangan']);
							$objPHPExcel->getActiveSheet()->setCellValue('H'.($j),'');
							$objPHPExcel->getActiveSheet()->getStyle('B'.$j.':C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$objPHPExcel->getActiveSheet()->getStyle('E'.$j.':G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$objPHPExcel->getActiveSheet()->getStyle('B'.$j.':G'.$j)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$j++;
							$nMk++;
						}
					}
					$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':H'.($i+14))->applyFromArray(array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
					for($x=$i;$x<=($i+14);$x++){
						$objPHPExcel->getActiveSheet()->getRowDimension($x)->setRowHeight(18.25);
					}
					$i=$rowAwal+28;
					$objPHPExcel->getActiveSheet()->setCellValue('E'.($i),'Ketua/Sekretaris Program Studi');
					$objPHPExcel->getActiveSheet()->setCellValue('B'.($i),'* Tata Tertib Ujian : ');
					$objPHPExcel->getActiveSheet()->mergeCells('B'.($i).':D'.($i));
					$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+1),'- Kartu ini harap dibawa selama ujian ');
					$objPHPExcel->getActiveSheet()->mergeCells('B'.($i+1).':D'.($i+1));
					$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+2),'- Diwajibkan memakai jas almamater ');
					$objPHPExcel->getActiveSheet()->mergeCells('B'.($i+2).':D'.($i+2));
					$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+3),'- Mengenakan pakaian & sepatu sesuai aturan STFI');
					$objPHPExcel->getActiveSheet()->mergeCells('B'.($i+3).':D'.($i+3));
					$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+4),'- Handphone/alat komunikasi dimatikan & dikumpulkan di pengawas');
					$objPHPExcel->getActiveSheet()->mergeCells('B'.($i+4).':D'.($i+4));
					$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+5),'- Pindah jadwal ujian wajib melapor ke Bag. Akademik');
					$objPHPExcel->getActiveSheet()->mergeCells('B'.($i+5).':D'.($i+5));
					$objPHPExcel->getActiveSheet()->mergeCells('E'.($i).':G'.($i));
					$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+5),'(__________________________________)');
					$objPHPExcel->getActiveSheet()->mergeCells('E'.($i+5).':G'.($i+5));
					$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':G'.($i+5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':G'.($i+5))->getFont()->setBold(true)->setSize(9);
					$i=$i+6;
					// draw box
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rowAwal.':I'.($i))->applyFromArray(array('borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
					if ($n%2==0){
						$i=$i+3;
					}else{
						$i=$i+7;
					}
					$n++;
				}
			}
			
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Kartu Ujian.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			// remove temp barcode
			for ($a=1; $a<=$n ; $a++) {
				unlink('public/barcode/'.$rdm.'-'.$a.'.jpg');
			}
		}
	}
	
	function index2Action()
	{
		$user = new Menu();
		$menu = "ujian/index2";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Hadir Ujian (UTS/UAS)";
			// treeview
			$this->view->active_tree="08";
			$this->view->active_menu="ujian/index2";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			$prodi = new Prodi();
			$this->view->listProdi = $prodi->fetchAll();
			$periode = new Periode();
			$this->view->listPeriode = $periode->fetchAll();
		}
	}
	
	function excel2Action(){
		$user = new Menu();
		$menu = "ujian/excel2";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
		// get parameter
			$jns = $this->_request->get('jns');
			$pkt = $this->_request->get('pkt');
			if ($jns=='1'){
				$this->daftarHadirUTS($pkt);
			}else{
				$this->daftarHadirUAS($pkt);
			}
		}
	}
	
	protected function daftarHadirUTS($paket){
		// disable layout
		$this->_helper->layout->disableLayout();
		// get data
		$kuliah = new Kuliah();
		$getKuliah=$kuliah->getKuliahByPaket($paket);
		$paketKelas = new Paketkelas();
		$getPaketKelas = $paketKelas->getPaketKelasByKd($paket);
		foreach ($getPaketKelas as $dataPaket){			
			$arrPeriode = explode('/', $dataPaket['kd_periode']);
			$thnAjaran = $arrPeriode[0];
			$smt = $arrPeriode[1];
			$mk = $dataPaket['nm_mk'];
			$dosen = $dataPaket['nm_dosen'];
			$smtDef = $dataPaket['smt_def'];
			$nm_kls = $dataPaket['nm_kelas']."(".$dataPaket['jns_kelas'].")";
		}
		$ses = new Zend_Session_Namespace('ses_ac');
		$nm_pt=$ses->nm_pt;
		$almt = $ses->alamat;
		// image path
		$path = __FILE__;
		$imgPath = str_replace('academic/application/controllers/UjianController.php','public/img/logo.png',$path);
		// konfigurasi excel
		PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Akademik")
									 ->setLastModifiedBy("Akademik")
									 ->setTitle("Daftar Hadir Ujian")
									 ->setSubject("Sistem Informasi Akademik")
									 ->setDescription("Daftar Hadir Ujian")
									 ->setKeywords("Daftar Hadir Ujian")
									 ->setCategory("Data File");
									 
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('Daftar Hadir UTS');
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT)
													  ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL)
													  ->setFitToWidth(2)
													  ->setFitToHeight('Automatic')
													  ->SetHorizontalCentered(true);
		// margin
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft((1/2.54))
														->setTop(1/2.54)
														->setRight(0.5/2.54)
														->setBottom(0);
		// default height and font
		$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(19.50);
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Tahoma');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
		// column width
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4.43);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(26);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(32);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10.50);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(4.43);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(26);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(32);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10.50);
		$i=1;
		// drawing logo
		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Logo1');
		$objDrawing->setDescription('Logo1');
		$objDrawing->setPath($imgPath);
		$objDrawing->setHeight(75);
		$objDrawing->setWidth(75);
		$objDrawing->setCoordinates('B'.($i));
		$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Logo2');
		$objDrawing->setDescription('Logo2');
		$objDrawing->setPath($imgPath);
		$objDrawing->setHeight(75);
		$objDrawing->setWidth(75);
		$objDrawing->setCoordinates('H'.($i));
		$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		// start write content
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($i),strtoupper($nm_pt));
		$objPHPExcel->getActiveSheet()->setCellValue('G'.($i),strtoupper($nm_pt));
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i).':E'.($i));
		$objPHPExcel->getActiveSheet()->mergeCells('G'.($i).':K'.($i));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->getFont()->setBold(true)->setSize(12);
		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(15.75);
		$i++;
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($i),$almt);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.($i),$almt);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i).':E'.($i));
		$objPHPExcel->getActiveSheet()->mergeCells('G'.($i).':K'.($i));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->getFont()->setBold(true)->setSize(10);
		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(15.75);
		$i++;
		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(9.75);
		$i++;
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($i),'Daftar Hadir & Nilai UTS');
		$objPHPExcel->getActiveSheet()->setCellValue('G'.($i),'Daftar Hadir & Nilai UTS');
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i).':E'.($i));
		$objPHPExcel->getActiveSheet()->mergeCells('G'.($i).':K'.($i));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->getFont()->setBold(true)->setSize(12);
		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(15.75);
		$i++;
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($i),'Semester '.$smt.' Tahun '.$thnAjaran);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.($i),'Semester '.$smt.' Tahun '.$thnAjaran);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i).':E'.($i));
		$objPHPExcel->getActiveSheet()->mergeCells('G'.($i).':K'.($i));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->getFont()->setBold(true)->setSize(12);
		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(15.75);
		$i++;
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($i),'Mata Kuliah');
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i).':B'.($i));
		$objPHPExcel->getActiveSheet()->setCellValue('C'.($i),': '.$mk);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.($i),'Mata Kuliah');
		$objPHPExcel->getActiveSheet()->mergeCells('G'.($i).':H'.($i));
		$objPHPExcel->getActiveSheet()->setCellValue('I'.($i),': '.$mk);		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(15);
		$i++;
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($i),'Dosen');
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i).':B'.($i));
		$objPHPExcel->getActiveSheet()->setCellValue('C'.($i),': '.$dosen);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.($i),'Dosen');
		$objPHPExcel->getActiveSheet()->mergeCells('G'.($i).':H'.($i));
		$objPHPExcel->getActiveSheet()->setCellValue('I'.($i),': '.$dosen);		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(15);
		$i++;
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($i),'Semt / Kelas');
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i).':B'.($i));
		$objPHPExcel->getActiveSheet()->setCellValue('C'.($i),': '.$smtDef.' / '.$nm_kls);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.($i),'Semt / Kelas');
		$objPHPExcel->getActiveSheet()->mergeCells('G'.($i).':H'.($i));
		$objPHPExcel->getActiveSheet()->setCellValue('I'.($i),': '.$smtDef.' / '.$nm_kls);		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(15);
		$i++;
		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(9.75);
		$i++;
		$rowAwal=$i;
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($i),'No');
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i).':A'.($i+1));
		$objPHPExcel->getActiveSheet()->setCellValue('G'.($i),'No');
		$objPHPExcel->getActiveSheet()->mergeCells('G'.($i).':G'.($i+1));
		$objPHPExcel->getActiveSheet()->setCellValue('B'.($i),'NPM');
		$objPHPExcel->getActiveSheet()->mergeCells('B'.($i).':B'.($i+1));
		$objPHPExcel->getActiveSheet()->setCellValue('H'.($i),'NPM');
		$objPHPExcel->getActiveSheet()->mergeCells('H'.($i).':H'.($i+1));
		$objPHPExcel->getActiveSheet()->setCellValue('C'.($i),'Nama');
		$objPHPExcel->getActiveSheet()->mergeCells('C'.($i).':C'.($i+1));
		$objPHPExcel->getActiveSheet()->setCellValue('I'.($i),'Nama');
		$objPHPExcel->getActiveSheet()->mergeCells('I'.($i).':I'.($i+1));
		$objPHPExcel->getActiveSheet()->setCellValue('D'.($i),'Tanda Tangan');
		$objPHPExcel->getActiveSheet()->mergeCells('D'.($i).':D'.($i+1));
		$objPHPExcel->getActiveSheet()->setCellValue('J'.($i),'Tanda Tangan');
		$objPHPExcel->getActiveSheet()->mergeCells('J'.($i).':J'.($i+1));
		$objPHPExcel->getActiveSheet()->setCellValue('E'.($i),'Angka Mutu');
		$objPHPExcel->getActiveSheet()->mergeCells('E'.($i).':E'.($i+1));
		$objPHPExcel->getActiveSheet()->setCellValue('K'.($i),'Angka Mutu');
		$objPHPExcel->getActiveSheet()->mergeCells('K'.($i).':K'.($i+1));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(15);
		$objPHPExcel->getActiveSheet()->getRowDimension($i+1)->setRowHeight(15);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.($i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.($i+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.($i+1))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i+1).':E'.($i+1))->applyFromArray(array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE))));
		$objPHPExcel->getActiveSheet()->getStyle('G'.($i+1).':K'.($i+1))->applyFromArray(array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE))));
		$i=$i+2;
		$j=$i;
		$n=1;
		foreach ($getKuliah as $data){
			if($data['approved']=='t'){
				if ($n<=35){
					$objPHPExcel->getActiveSheet()->setCellValue('A'.($i),$n);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.($i),$data['nim']);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.($i),$data['nm_mhs']);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.($i),$n.'....................');
					if ($n%2==1){
						$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					}else{
						$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					}
					$i++;
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('G'.($j),$n);
					$objPHPExcel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->setCellValue('H'.($j),$data['nim']);
					$objPHPExcel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->setCellValue('I'.($j),$data['nm_mhs']);
					$objPHPExcel->getActiveSheet()->setCellValue('J'.($j),$n.'....................');
					if ($n%2==1){
						$objPHPExcel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					}else{
						$objPHPExcel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					}
					$j++;
				}
				$n++;	
			}
		}
		// draw border
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rowAwal.':E'.($rowAwal+36))->applyFromArray(array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
		$objPHPExcel->getActiveSheet()->getStyle('G'.$rowAwal.':K'.($rowAwal+36))->applyFromArray(array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
		// tanda tangan
		$objPHPExcel->getActiveSheet()->setCellValue('D'.($rowAwal+37),'Dosen Mata Kuliah');
		$objPHPExcel->getActiveSheet()->setCellValue('J'.($rowAwal+37),'Dosen Mata Kuliah');
		$objPHPExcel->getActiveSheet()->setCellValue('D'.($rowAwal+40),'(.............................)');
		$objPHPExcel->getActiveSheet()->setCellValue('J'.($rowAwal+40),'(.............................)');
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Daftar Hadir UTS.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	
	
	protected function daftarHadirUAS($paket){
		// disable layout
		$this->_helper->layout->disableLayout();
		// get data
		$kuliah = new Kuliah();
		$getKuliah=$kuliah->getKuliahByPaket($paket);
		$paketKelas = new Paketkelas();
		$getPaketKelas = $paketKelas->getPaketKelasByKd($paket);
		foreach ($getPaketKelas as $dataPaket){			
			$arrPeriode = explode('/', $dataPaket['kd_periode']);
			$thnAjaran = $arrPeriode[0];
			$smt = $arrPeriode[1];
			$mk = $dataPaket['nm_mk'];
			$dosen = $dataPaket['nm_dosen'];
			$smtDef = $dataPaket['smt_def'];
			$nm_kls = $dataPaket['nm_kelas']."(".$dataPaket['jns_kelas'].")";
		}
		$ses = new Zend_Session_Namespace('ses_ac');
		$nm_pt=$ses->nm_pt;
		$almt = $ses->alamat;
		// image path
		$path = __FILE__;
		$imgPath = str_replace('academic/application/controllers/UjianController.php','public/img/logo.png',$path);
		// konfigurasi excel
		PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Akademik")
									 ->setLastModifiedBy("Akademik")
									 ->setTitle("Daftar Hadir Ujian")
									 ->setSubject("Sistem Informasi Akademik")
									 ->setDescription("Daftar Hadir Ujian")
									 ->setKeywords("Daftar Hadir Ujian")
									 ->setCategory("Data File");
									 
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('Daftar Hadir UAS');
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
													  ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL)
													  ->setFitToWidth('Automatic')
													  ->setFitToHeight('Automatic')
													  ->SetHorizontalCentered(true);
		// margin
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft((1.8/2.54))
														->setTop(0)
														->setRight(1.8/2.54)
														->setBottom(0);
		// default height and font
		$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(14.25);
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Tahoma');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
		// column width
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4.5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(48);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(7);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(7);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(7);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(7);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(7);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(11);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(11);
		$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(8);
		// column width
		$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(4.5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(48);
		$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(7);
		$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(7);
		$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(7);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(7);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(7);
		$objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setWidth(11);
		$objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setWidth(11);
		$i=1;
		// drawing logo
		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Logo1');
		$objDrawing->setDescription('Logo1');
		$objDrawing->setPath($imgPath);
		$objDrawing->setHeight(50);
		$objDrawing->setWidth(50);
		$objDrawing->setCoordinates('A2');
		$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Logo2');
		$objDrawing->setDescription('Logo2');
		$objDrawing->setPath($imgPath);
		$objDrawing->setHeight(50);
		$objDrawing->setWidth(50);
		$objDrawing->setCoordinates('S2');
		$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		// write content
		$i++;
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,strtoupper($nm_pt));
		$objPHPExcel->getActiveSheet()->setCellValue('U'.$i,strtoupper($nm_pt));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':AI'.$i)->getFont()->setBold(true)->setSize(14);
		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(18);
		$i++;
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$almt);
		$objPHPExcel->getActiveSheet()->setCellValue('U'.$i,$almt);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':AI'.$i)->getFont()->setBold(true)->setSize(10);
		$i++;
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'DAFTAR NILAI AKHIR MAHASISWA');
		$objPHPExcel->getActiveSheet()->setCellValue('S'.$i,'DAFTAR NILAI AKHIR MAHASISWA');
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i).':Q'.($i));
		$objPHPExcel->getActiveSheet()->mergeCells('S'.($i).':AI'.($i));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('S'.$i.':AI'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':AI'.$i)->getFont()->setBold(true)->setSize(12);
		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(15);
		$i++;
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'UJIAN AKHIR SEMESTER '. strtoupper($smt).' '.$thnAjaran);
		$objPHPExcel->getActiveSheet()->setCellValue('S'.$i,'UJIAN AKHIR SEMESTER '. strtoupper($smt).' '.$thnAjaran);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i).':Q'.($i));
		$objPHPExcel->getActiveSheet()->mergeCells('S'.($i).':AI'.($i));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('S'.$i.':AI'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':AI'.$i)->getFont()->setBold(true)->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE)->setSize(12);
		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(15);
		$i++;
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,'Mata Kuliah');
		$objPHPExcel->getActiveSheet()->setCellValue('U'.$i,'Mata Kuliah');
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,': '.$mk);
		$objPHPExcel->getActiveSheet()->setCellValue('V'.$i,': '.$mk);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':AI'.$i)->getFont()->setBold(true)->setSize(12);
		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(15);
		$i++;
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,'Dosen');
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,': '.$dosen);
		$objPHPExcel->getActiveSheet()->setCellValue('U'.$i,'Dosen');
		$objPHPExcel->getActiveSheet()->setCellValue('V'.$i,': '.$dosen);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':AI'.$i)->getFont()->setBold(true)->setSize(12);
		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(15);		
		$i++;
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,'Semt/Kelas');
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,': '.$smtDef.' / '.$nm_kls);
		$objPHPExcel->getActiveSheet()->setCellValue('U'.$i,'Semt/Kelas');
		$objPHPExcel->getActiveSheet()->setCellValue('V'.$i,': '.$smtDef.' / '.$nm_kls);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':AI'.$i)->getFont()->setBold(true)->setSize(12);
		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(15);
		$i++;
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'No');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i).':A'.($i+1));
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,'NPM');
		$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->mergeCells('B'.($i).':B'.($i+1));
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,'Nama');
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->mergeCells('C'.($i).':C'.($i+1));
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,'KOMPONEN NILAI AKHIR');
		$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':H'.$i)->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->mergeCells('D'.($i).':H'.$i);
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,'NILAI AKHIR');
		$objPHPExcel->getActiveSheet()->getStyle('I'.$i.':O'.$i)->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->mergeCells('I'.($i).':O'.$i);
		$objPHPExcel->getActiveSheet()->setCellValue('P'.$i,'TANDA TANGAN');
		$objPHPExcel->getActiveSheet()->getStyle('P'.$i.':Q'.$i)->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->mergeCells('P'.($i).':Q'.($i+1));
		$objPHPExcel->getActiveSheet()->setCellValue('D'.($i+1),'Tugas');
		$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+1),'Quiz');
		$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+1),'UTS');
		$objPHPExcel->getActiveSheet()->setCellValue('G'.($i+1),'UAS');
		$objPHPExcel->getActiveSheet()->setCellValue('H'.($i+1),'NA');
		$objPHPExcel->getActiveSheet()->getStyle('D'.($i+1).':H'.($i+1))->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->setCellValue('I'.($i+1),'Huruf Mutu');
		$objPHPExcel->getActiveSheet()->getStyle('I'.($i+1))->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->mergeCells('I'.($i+1).':O'.($i+1));
		// border
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.($i+1))->applyFromArray(array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->applyFromArray(array('borders' => array('top' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE))));
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i+1).':Q'.($i+1))->applyFromArray(array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE))));
		$objPHPExcel->getActiveSheet()->getStyle('P'.$i.':P'.($i+1))->applyFromArray(array('borders' => array('left' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
		$objPHPExcel->getActiveSheet()->getStyle('P'.$i.':Q'.($i+1))->applyFromArray(array('borders' => array('right' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
		//---------------- devided 2----------------------------
		$objPHPExcel->getActiveSheet()->setCellValue('S'.$i,'No');
		$objPHPExcel->getActiveSheet()->getStyle('S'.$i)->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->mergeCells('S'.($i).':S'.($i+1));
		$objPHPExcel->getActiveSheet()->setCellValue('T'.$i,'NPM');
		$objPHPExcel->getActiveSheet()->getStyle('T'.$i)->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->mergeCells('T'.($i).':T'.($i+1));
		$objPHPExcel->getActiveSheet()->setCellValue('U'.$i,'Nama');
		$objPHPExcel->getActiveSheet()->getStyle('U'.$i)->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->mergeCells('U'.($i).':U'.($i+1));
		$objPHPExcel->getActiveSheet()->setCellValue('V'.$i,'KOMPONEN NILAI AKHIR');
		$objPHPExcel->getActiveSheet()->getStyle('V'.$i)->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->mergeCells('V'.($i).':Z'.$i);
		$objPHPExcel->getActiveSheet()->setCellValue('AA'.$i,'NILAI AKHIR');
		$objPHPExcel->getActiveSheet()->getStyle('AA'.$i)->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->mergeCells('AA'.($i).':AG'.$i);
		$objPHPExcel->getActiveSheet()->setCellValue('AH'.$i,'TANDA TANGAN');
		$objPHPExcel->getActiveSheet()->getStyle('AH'.$i)->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->mergeCells('AH'.$i.':AI'.($i+1));
		$objPHPExcel->getActiveSheet()->setCellValue('V'.($i+1),'Tugas');
		$objPHPExcel->getActiveSheet()->setCellValue('W'.($i+1),'Quiz');
		$objPHPExcel->getActiveSheet()->setCellValue('X'.($i+1),'UTS');
		$objPHPExcel->getActiveSheet()->setCellValue('Y'.($i+1),'UAS');
		$objPHPExcel->getActiveSheet()->setCellValue('Z'.($i+1),'NA');
		$objPHPExcel->getActiveSheet()->getStyle('V'.($i+1).':Z'.($i+1))->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->setCellValue('AA'.($i+1),'Huruf Mutu');
		$objPHPExcel->getActiveSheet()->getStyle('AA'.($i+1))->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->mergeCells('AA'.($i+1).':AG'.($i+1));
		// border
		$objPHPExcel->getActiveSheet()->getStyle('S'.$i.':Z'.($i+1))->applyFromArray(array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
		$objPHPExcel->getActiveSheet()->getStyle('S'.$i.':AI'.$i)->applyFromArray(array('borders' => array('top' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE))));
		$objPHPExcel->getActiveSheet()->getStyle('S'.($i+1).':AI'.($i+1))->applyFromArray(array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE))));
		$objPHPExcel->getActiveSheet()->getStyle('AH'.$i.':AI'.($i+1))->applyFromArray(array('borders' => array('left' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
		$objPHPExcel->getActiveSheet()->getStyle('AH'.$i.':AI'.($i+1))->applyFromArray(array('borders' => array('right' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
		//--- height
		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(15.75);
		$objPHPExcel->getActiveSheet()->getRowDimension($i+1)->setRowHeight(15.75);	
		// alignment
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':AI'.($i+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':AI'.($i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':AI'.($i+1))->getFont()->setBold(true);	
		// write data
		$n=1;
		$i=$i+2;
		$j=$i;
		$rowAwal=$i;
		foreach ($getKuliah as $data){
			if($data['approved']=='t'){
				if ($n<=22){
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$data['nim']);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$data['nm_mhs']);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getFont()->setSize(8);
					$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,'A');
					$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,'B');
					$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,'C');
					$objPHPExcel->getActiveSheet()->setCellValue('L'.$i,'D');
					$objPHPExcel->getActiveSheet()->setCellValue('M'.$i,'E');
					$objPHPExcel->getActiveSheet()->setCellValue('N'.$i,'T');
					$objPHPExcel->getActiveSheet()->setCellValue('O'.$i,'K');
					$objPHPExcel->getActiveSheet()->getStyle('I'.$i.':O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					if ($n%2==1){
						$objPHPExcel->getActiveSheet()->setCellValue('P'.$i,$n.'..............');
					}else{
						$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i,$n.'..............');
					}
					$i++;
				}else{
					$objPHPExcel->getActiveSheet()->setCellValue('S'.$j,$n);
					$objPHPExcel->getActiveSheet()->getStyle('S'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->setCellValue('T'.$j,$data['nim']);
					$objPHPExcel->getActiveSheet()->setCellValue('U'.$j,$data['nm_mhs']);
					$objPHPExcel->getActiveSheet()->getStyle('T'.$i.':U'.$i)->getFont()->setSize(8);
					$objPHPExcel->getActiveSheet()->setCellValue('AA'.$j,'A');
					$objPHPExcel->getActiveSheet()->setCellValue('AB'.$j,'B');
					$objPHPExcel->getActiveSheet()->setCellValue('AC'.$j,'C');
					$objPHPExcel->getActiveSheet()->setCellValue('AD'.$j,'D');
					$objPHPExcel->getActiveSheet()->setCellValue('AE'.$j,'E');
					$objPHPExcel->getActiveSheet()->setCellValue('AF'.$j,'T');
					$objPHPExcel->getActiveSheet()->setCellValue('AG'.$j,'K');
					$objPHPExcel->getActiveSheet()->getStyle('AA'.$j.':AG'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					if ($n%2==1){
						$objPHPExcel->getActiveSheet()->setCellValue('AH'.$j,$n.'..............');
					}else{
						$objPHPExcel->getActiveSheet()->setCellValue('AI'.$j,$n.'..............');
					}
					$j++;
				}
				$n++;
			}
		}
		// border data
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rowAwal.':Q'.($rowAwal+21))->applyFromArray(array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
		$objPHPExcel->getActiveSheet()->getStyle('S'.$rowAwal.':AI'.($rowAwal+21))->applyFromArray(array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
		$objPHPExcel->getActiveSheet()->getRowDimension($rowAwal+22)->setRowHeight(5.25);
		$objPHPExcel->getActiveSheet()->getRowDimension($rowAwal+23)->setRowHeight(5.25);
		$objPHPExcel->getActiveSheet()->setCellValue('M'.($rowAwal+24),'.........,.............................................');
		$objPHPExcel->getActiveSheet()->setCellValue('AE'.($rowAwal+24),'.........,.............................................');
		$objPHPExcel->getActiveSheet()->setCellValue('M'.($rowAwal+25),'Dosen Penanggungjawab MK,');
		$objPHPExcel->getActiveSheet()->setCellValue('AE'.($rowAwal+25),'Dosen Penanggungjawab MK,');
		$objPHPExcel->getActiveSheet()->setCellValue('M'.($rowAwal+29),'(.........................................................)');
		$objPHPExcel->getActiveSheet()->setCellValue('AE'.($rowAwal+29),'(.........................................................)');
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Daftar Hadir UAS.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
}
