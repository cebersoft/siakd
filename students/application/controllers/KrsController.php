<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: KRS Controller -> Controller untuk modul halaman KRS
*/
class KrsController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Paketkelas');
		Zend_Loader::loadClass('Kuliah');
		Zend_Loader::loadClass('KuliahTA');
		Zend_Loader::loadClass('Register');
		Zend_Loader::loadClass('StatReg');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('KalenderAkd');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Validation');
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
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// nav menu
		$this->view->krs_act="active";
	}
	
	function indexAction()
	{
		// get nim periode
		$nim = $this->uname;
		$kd_periode = $this->_request->get('per');
		// get register
		$register = new Register();
		$getRegister = $register->getRegisterByNimPeriode($nim,$kd_periode);
		if($getRegister){
			foreach ($getRegister as $dtReg) {
				$this->view->nim=$dtReg['nim'];
				$this->view->nm_mhs=$dtReg['nm_mhs'];
				$this->view->akt=$dtReg['id_angkatan'];
				$this->view->kd_prd=$dtReg['kd_prodi'];
				$this->view->prd=$dtReg['nm_prodi'];
				$this->view->dw=$dtReg['nm_dosen_wali'];
				$kd_prodi=$dtReg['kd_prodi'];
				$this->view->per=$dtReg['kd_periode'];
				$krs=$dtReg['krs'];
				$nim=$dtReg['nim'];
				$nm_mhs=$dtReg['nm_mhs'];
				$akt=$dtReg['id_angkatan'];
				$prd=$dtReg['nm_prodi'];
				$per=$dtReg['kd_periode'];
				$dw=$dtReg['nm_dosen_wali'];
			}
			if($krs=='f'){
				// Title Browser
				$this->view->title = "KRS Mahasiswa";
				$this->view->eksis="f";	
			}else{
				// Title Browser
				$this->view->title = "KRS MAHASISWA ".$nm_mhs;
				// navigation
				$this->_helper->navbar("register","krs/export");
				$kuliah = new Kuliah();
				$getKuliah = $kuliah->getKuliahByNimPeriode($nim,$kd_periode);
				$this->view->listKuliah=$getKuliah;
				$kuliahTA = new KuliahTA();
				$getKuliahTA = $kuliahTA->getKuliahTAByNimPeriode($nim,$kd_periode);
				$this->view->listKuliahTA=$getKuliahTA;
				$this->view->per=$kd_periode;
				// cek periode dan tanggal di kalender
				$tgl = date('Y-m-d');
				$periode=new Periode();
				$getPeriode=$periode->getPeriodeByTgl($tgl);
				$kd_periode_now="";
				if($getPeriode){
					foreach ($getPeriode as $dtPeriode){
						$kd_periode_now=$dtPeriode['kd_periode'];
					}
				}else{
					$getPeriodeAktif=$periode->getPeriodeByStatus(0);
					foreach ($getPeriodeAktif as $dtPeriode) {
						$kd_periode_now=$dtPeriode['kd_periode'];;
					}
				}
				if($kd_periode_now!=$kd_periode){
					$this->view->allowReg=-1;
				}else{
					// cek kalender
					$kd_aktivitas='101';
					// jadwal krs/her registrasi
					$kalender=new KalenderAkd();
					$getKalender=$kalender->getKalenderAkdByPeriodeAktivitas($kd_periode, $kd_aktivitas);
					$this->view->allowReg="";
					if ($getKalender){
						// cek tanggal
						foreach ($getKalender as $dtKalender) {
							$startDate=$dtKalender['start_date'];
							$endDate=$dtKalender['end_date'];
						}
						if(($tgl>=$startDate)and($tgl<=$endDate)){
							$this->view->allowReg=1;
							// show paket kelas
							$paketkelas = new Paketkelas();
							$this->view->listPaketkelas= $paketkelas->getPaketKelasByPeriodeProdi($kd_periode,$kd_prodi);
						}else{
							$this->view->allowReg=-1;
						}
					}else{
						$this->view->allowReg=0;
					}
				}
			}
		}else{
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "KRS Mahasiswa";
			// navigation
			$this->_helper->navbar("register",0);
		}
		// create session for export
		$param = new Zend_Session_Namespace('param_krs');
		$param->data=array_merge($getKuliah,$getKuliahTA);
		$param->per=$per;
		$param->nim=$nim;
		$param->nm=$nm_mhs;
		$param->akt=$akt;
		$param->prd=$prd;
		$param->dw=$dw;
	}
	
	function editAction()
	{
		// layout
		$this->_helper->layout()->setLayout('main');
		// title Browser
		$this->view->title="Edit SKS diambil pada KRS";
		// get kd kuliah
		$kd_kuliah = $this->_request->get('id');
		$kuliah = new Kuliah();
		$getKuliah = $kuliah->getKuliahByKd($kd_kuliah);
		if($getKuliah){
			foreach ($getKuliah as $dataKuliah) {
				$this->view->kd=$dataKuliah['kd_kuliah'];
				$this->view->nim=$dataKuliah['nim'];
				$nim=$dataKuliah['nim'];
				$this->view->nm_mhs=$dataKuliah['nm_mhs'];
				$this->view->akt=$dataKuliah['id_angkatan'];
				$this->view->prd=$dataKuliah['nm_prodi'];
				$this->view->kd_mk=$dataKuliah['kode_mk'];
				$this->view->nm_mk=$dataKuliah['nm_mk'];
				$this->view->per=$dataKuliah['kd_periode'];
				$kd_periode=$dataKuliah['kd_periode'];
				$this->view->nm_dsn=$dataKuliah['nm_dosen'];
				$this->view->sks_def=$dataKuliah['sks_tm']+$dataKuliah['sks_prak']+$dataKuliah['sks_prak_lap']+$dataKuliah['sks_sim'];
				$this->view->sks_taken=$dataKuliah['sks_tm']+$dataKuliah['sks_prak']+$dataKuliah['sks_prak_lap']+$dataKuliah['sks_sim']-$dataKuliah['sks_deducted'];
			}
			// navigation
			$this->_helper->navbar("krs/index?per=".$kd_periode,0);
		}else{
			$this->view->eksis="f";
			$this->_helper->navbar("register",0);
		}
	}
	
	function exportAction(){
		$param = new Zend_Session_Namespace('param_krs');
		$dataKrs=$param->data;
		$per=$param->per;
		$nim=$param->nim;
		$nm_mhs=$param->nm;
		$akt=$param->akt;
		$prd=$param->prd;
		$dw=$param->dw;
		$ses_std = new Zend_Session_Namespace('ses_std');
		$nm_pt=$ses_std->nm_pt;
		// disabel layout
		$this->_helper->layout->disableLayout();
		// image path logo
		$path = __FILE__;
		$imgPath = str_replace('students/application/controllers/KrsController.php','public/img/logo.png',$path);
		// konfigurasi excel
		PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Akademik")
									 ->setLastModifiedBy("Akademik")
									 ->setTitle("Export Data KRS Mahasiswa")
									 ->setSubject("Sistem Informasi Akademik")
									 ->setDescription("Data KRS Mahasiswa")
									 ->setKeywords("krs")
									 ->setCategory("Data File");
									 
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('Form KRS');
									 
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
		$noborder = array(
 		   'borders' => array(
		        'allborders' => array(
		        	'style' => PHPExcel_Style_Border::BORDER_NONE
		    	)
		    )
		);
		// column width
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(22);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(32);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(9);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(9);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(22);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(32);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
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
		// properties field excel;
		$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:J2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:J3');
		$objPHPExcel->getActiveSheet()->mergeCells('A8:J8');
		$objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A2:J2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:J2')->getFont()->setSize(14);
		$objPHPExcel->getActiveSheet()->getStyle('A1:J2')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(25);
		$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(25);
		// text
		$objPHPExcel->getActiveSheet()->setCellValue('A1',strtoupper($nm_pt));
		$objPHPExcel->getActiveSheet()->setCellValue('A2','KARTU RENCANA STUDI');
		$objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
		$objPHPExcel->getActiveSheet()->mergeCells('A5:B5');
		$objPHPExcel->getActiveSheet()->mergeCells('A6:B6');
		$objPHPExcel->getActiveSheet()->mergeCells('A7:B7');
		$objPHPExcel->getActiveSheet()->setCellValue('A4','  PERIODE');
		$objPHPExcel->getActiveSheet()->setCellValue('A5','  NPM');
		$objPHPExcel->getActiveSheet()->setCellValue('A6','  NAMA MAHASISWA');
		$objPHPExcel->getActiveSheet()->setCellValue('A7','  DOSEN WALI');
		$objPHPExcel->getActiveSheet()->mergeCells('C4:J4');
		$objPHPExcel->getActiveSheet()->mergeCells('C5:J5');
		$objPHPExcel->getActiveSheet()->mergeCells('C6:J6');
		$objPHPExcel->getActiveSheet()->mergeCells('C7:J7');
		$objPHPExcel->getActiveSheet()->setCellValue('C4',' :  '.$per);
		$objPHPExcel->getActiveSheet()->setCellValue('C5',' :  '.$nim);
		$objPHPExcel->getActiveSheet()->setCellValue('C6',' :  '.$nm_mhs);
		$objPHPExcel->getActiveSheet()->setCellValue('C7',' :  '.$dw);
		$objPHPExcel->getActiveSheet()->getStyle('A1:J8')->applyFromArray($noborder);
		$objPHPExcel->getActiveSheet()->setCellValue('A8','* Warna abu untuk paket kelas yang belum/tidak diapprove dosen wali');
		$objPHPExcel->getActiveSheet()->getStyle('A8')->getFont()->setSize(8);
		// tabel krs
		$objPHPExcel->getActiveSheet()->mergeCells('A9:A11');
		$objPHPExcel->getActiveSheet()->mergeCells('B9:E9');
		$objPHPExcel->getActiveSheet()->mergeCells('F9:G9');
		$objPHPExcel->getActiveSheet()->mergeCells('H9:J11');
		$objPHPExcel->getActiveSheet()->mergeCells('B10:B11');
		$objPHPExcel->getActiveSheet()->mergeCells('C10:C11');
		$objPHPExcel->getActiveSheet()->mergeCells('D10:E10');
		$objPHPExcel->getActiveSheet()->mergeCells('F10:F11');
		$objPHPExcel->getActiveSheet()->mergeCells('G10:G11');
		$objPHPExcel->getActiveSheet()->setCellValue('A9',' NO');
		$objPHPExcel->getActiveSheet()->setCellValue('B9',' MATA KULIAH');
		$objPHPExcel->getActiveSheet()->setCellValue('F9',' DOSEN');
		$objPHPExcel->getActiveSheet()->setCellValue('H9',' NAMA KELAS');
		$objPHPExcel->getActiveSheet()->setCellValue('B10',' KODE');
		$objPHPExcel->getActiveSheet()->setCellValue('C10',' NAMA MATA KULIAH');
		$objPHPExcel->getActiveSheet()->setCellValue('D10',' SKS');
		$objPHPExcel->getActiveSheet()->setCellValue('D11',' DEF');
		$objPHPExcel->getActiveSheet()->setCellValue('E11',' TAKE');
		$objPHPExcel->getActiveSheet()->setCellValue('F10',' KODE');
		$objPHPExcel->getActiveSheet()->setCellValue('G10',' NAMA DOSEN ');
		$objPHPExcel->getActiveSheet()->getStyle('A9:J11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A9:J11')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A9:J11')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('A9:J11')->getFont()->setBold(true);
		$i=1;
		$n=12;
		if(count($dataKrs)>0){
			// sort data
			$sortmk=array();
			foreach ($dataKrs as $key => $row)
			{
			    $sortmk[$key] = $row['kode_mk'];
			}
			array_multisort($sortmk, SORT_ASC, $dataKrs);
			foreach ($dataKrs as $dataBelajar) {
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$n,$i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$n)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$n,$dataBelajar['kode_mk']);
				$objPHPExcel->getActiveSheet()->getStyle('B'.$n)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$n,'  '.$dataBelajar['nm_mk']);
				$sks=$dataBelajar['sks_tm']+$dataBelajar['sks_prak']+$dataBelajar['sks_prak_lap']+$dataBelajar['sks_sim'];
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$n,$sks);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$n,$sks-$dataBelajar['sks_deducted']);
				$objPHPExcel->getActiveSheet()->getStyle('D'.$n.':E'.$n)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$n,$dataBelajar['kd_dosen']);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$n)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$n,'  '.$dataBelajar['nm_dosen']);
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$n,' '.$dataBelajar['nm_kelas']);
				$objPHPExcel->getActiveSheet()->getStyle('H'.$n)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->mergeCells('H'.$n.':J'.$n);
				if($dataBelajar['approved']=='f'){
					$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':J'.$n)->applyFromArray($cell_color);
				}
				$i++;
				$n++;
			}
		}
		$objPHPExcel->getActiveSheet()->getStyle('A3:J3')->applyFromArray(array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE))));
		$objPHPExcel->getActiveSheet()->getStyle('A9:J'.$n)->applyFromArray($border);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$n,'JUMLAH SKS DIAMBIL');
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$n.':D'.$n);
		$objPHPExcel->getActiveSheet()->mergeCells('F'.$n.':J'.$n);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$n,'=SUM(E12:E'.($n-1).')');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':E'.$n)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':E'.$n)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($n+1).':J'.($n+1));
		$n=$n+2;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$n.':C'.$n);
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$n.':F'.$n);
		$objPHPExcel->getActiveSheet()->mergeCells('G'.$n.':J'.$n);
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$n,'MAHASISWA');
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$n,'BAG.AKADEMIK');
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$n,'DOSEN WALI');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':G'.$n)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':G'.$n)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':J'.$n)->applyFromArray($border);
		$n++;
		$objPHPExcel->getActiveSheet()->getRowDimension($n)->setRowHeight(70);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$n.':C'.$n);
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$n.':F'.$n);
		$objPHPExcel->getActiveSheet()->mergeCells('G'.$n.':J'.$n);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':J'.$n)->applyFromArray($border);
		$n++;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$n.':C'.$n);
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$n,'('.$nm_mhs.')');
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$n.':F'.$n);
		$objPHPExcel->getActiveSheet()->mergeCells('G'.$n.':J'.$n);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$n,'('.$dw.')');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':G'.$n)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':G'.$n)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':J'.$n)->applyFromArray($border);
		
		// protect sheet
		$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);
		
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Kartu Rencana Studi.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
}