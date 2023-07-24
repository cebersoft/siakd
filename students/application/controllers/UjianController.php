<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Ujian Controller -> Controller untuk modul ujian
*/
class UjianController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Register');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('KalenderAkd');
		Zend_Loader::loadClass('Kuliah');
		Zend_Loader::loadClass('Paketkelas');
		Zend_Loader::loadClass('JadwalUjian');
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Zend_Barcode');
		Zend_Loader::loadClass('PHPExcel');
		Zend_Loader::loadClass('PHPExcel_Cell_AdvancedValueBinder');
		Zend_Loader::loadClass('PHPExcel_IOFactory');
		$auth = Zend_Auth::getInstance();
		$ses_std = new Zend_Session_Namespace('ses_std');
		if (($auth->hasIdentity())and($ses_std->uname)) {
			$this->view->namamhs =Zend_Auth::getInstance()->getIdentity()->nm_mhs;
			$this->view->nim=Zend_Auth::getInstance()->getIdentity()->nim;
			$this->view->idmhs=Zend_Auth::getInstance()->getIdentity()->id_mhs;
			$this->view->kd_pt=$ses_std->kd_pt;
			$this->view->nm_pt=$ses_std->nm_pt;
			// global var
			$this->uname=Zend_Auth::getInstance()->getIdentity()->nim;
			$this->id=Zend_Auth::getInstance()->getIdentity()->id_mhs;
			$this->nm_pt=$ses_std->nm_pt;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// nav menu
		$this->view->ujian_act="active";
	}

	function indexAction()
	{
		// get param
		$periode = new Periode();
		// get periode aktif
		$getPeriodeAktif=$periode->getPeriodeByStatus(0);
		if($getPeriodeAktif){
			foreach ($getPeriodeAktif as $dtPeriode) {
				$kd_periode=$dtPeriode['kd_periode'];
			}
		}else{
			$kd_periode="";
		}
		// navigation
		$this->_helper->navbar(0,0);
		$tgl = date('Y-m-d');
		if($getPeriodeAktif){
			// Title Browser
			$this->view->title = "Ujian Periode Akademik ".$kd_periode;
			// cek masa uts dan uas
			$kd_uts='105';
			$kd_uas='106';
			// jadwal
			$kalender=new KalenderAkd();
			$getKalenderUts=$kalender->getKalenderAkdByPeriodeAktivitas($kd_periode, $kd_uts);
			$this->view->allowUts="0";
			if ($getKalenderUts){
				// cek tanggal
				foreach ($getKalenderUts as $dtKalender) {
					$startDate=$dtKalender['start_date'];
					$endDate=$dtKalender['end_date'];
				}
				if(($tgl>=$startDate)and($tgl<=$endDate)){
					$this->view->allowUts=1;
				}else{
					$this->view->allowUts=0;
				}
			}else{
				$this->view->allowUts=0;
			}
			$getKalenderUas=$kalender->getKalenderAkdByPeriodeAktivitas($kd_periode, $kd_uas);
			$this->view->allowUas="0";
			if ($getKalenderUas){
				// cek tanggal
				foreach ($getKalenderUas as $dtKalender) {
					$startDate=$dtKalender['start_date'];
					$endDate=$dtKalender['end_date'];
				}
				if(($tgl>=$startDate)and($tgl<=$endDate)){
					$this->view->allowUas=1;
				}else{
					$this->view->allowUas=0;
				}
			}else{
				$this->view->allowUas=0;
			}
			$nim=$this->uname;
		}else{
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Ujian";	
		}
	}

	function kartuAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$nim=$this->uname;
		$nm_pt=$this->nm_pt;
		$periode = new Periode();
		// get periode aktif
		$getPeriodeAktif=$periode->getPeriodeByStatus(0);
		if($getPeriodeAktif){
			foreach ($getPeriodeAktif as $dtPeriode) {
				$kd_periode=$dtPeriode['kd_periode'];
			}
		}else{
			$kd_periode="";
		}
		$tp=$this->_request->get('tp');
		if ($tp=='uts'){
			$jenis = "UJIAN TENGAH SEMESTER";
			$jns=1;
		}else{
			$jenis = "UJIAN AKHIR SEMESTER";
			$jns=2;
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
			$imgPath = str_replace('students/application/controllers/UjianController.php','public/img/logo.png',$path);
			// start write content
			$n=1;
			$i=1;
			// get data mahasiswa by nim
			$register = new Register();
			$getRegister = $register->getRegisterByNimPeriode($nim,$kd_periode);
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
					$barcodeOptions = array('text' => $dataMhs['nim']."-".$kd_periode,'drawText'=>false);
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
					$objPHPExcel->getActiveSheet()->setCellValue('D'.($i),': '.$kd_periode);
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
					$getJadwalMhs = $jadwalUjian->getMhsKuliahJadwalUjianByNimPeriode($dataMhs['nim'],$kd_periode,$jns);
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