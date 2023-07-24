<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: PKRS Controller -> Controller untuk modul halaman PKRS
*/
class PkrsController extends Zend_Controller_Action
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
		Zend_Loader::loadClass('Pkrs');
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
		// navigation
		$this->_helper->navbar(0,'pkrs/export');
		// get periode from tgl now
		$tgl = date('Y-m-d');
		$kd_periode="";
		$periode = new Periode();
		$getPeriode=$periode->getPeriodeByTgl($tgl);
		if($getPeriode){
			foreach ($getPeriode as $dtPeriode){
				$kd_periode=$dtPeriode['kd_periode'];
			}
		}else{
			$getPeriodeAktif=$periode->getPeriodeByStatus(0);
			foreach ($getPeriodeAktif as $dtPeriode) {
				$kd_periode=$dtPeriode['kd_periode'];;
			}
		}
		$this->view->per=$kd_periode;
		$kd_aktivitas='102'; // pkrs
		// jadwal pkrs
		$kalender=new KalenderAkd();
		$getKalender=$kalender->getKalenderAkdByPeriodeAktivitas($kd_periode, $kd_aktivitas);
		$this->view->allowReg="";
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
				$this->view->title = "Form PKRS Mahasiswa";
				$this->view->eksis="f";
			}else{
				// Title Browser
				$this->view->title = "Form PKRS MAHASISWA ".$nm_mhs;
				// navigation
				$this->_helper->navbar(0,"pkrs/export");
				$kuliah = new Kuliah();
				$getKuliah = $kuliah->getKuliahByNimPeriode($nim,$kd_periode);
				$this->view->listKuliah=$getKuliah;
				$kuliahTA = new KuliahTA();
				$getKuliahTA = $kuliahTA->getKuliahTAByNimPeriode($nim,$kd_periode);
				$this->view->listKuliahTA=$getKuliahTA;
				$this->view->per=$kd_periode;
				// show paket kelas
				$paketkelas = new Paketkelas();
				$this->view->listPaketkelas= $paketkelas->getPaketKelasByPeriodeProdi($kd_periode,$kd_prodi);
				// list pkrs
				$pkrs = new Pkrs();
				$getPkrs=$pkrs->getPkrsByNimPeriode($nim, $kd_periode);
				$getPkrsTA=$pkrs->getPkrsTAByNimPeriode($nim, $kd_periode);
				$this->view->listPkrs=$getPkrs;
				$this->view->listPkrsTA=$getPkrsTA;
			}
		}else{
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Form PKRS Mahasiswa";
		}
		// create session for export
		$param = new Zend_Session_Namespace('param_pkrs');
		$param->data=array_merge($getPkrs,$getPkrsTA);
		$param->per=$per;
		$param->nim=$nim;
		$param->nm=$nm_mhs;
		$param->akt=$akt;
		$param->prd=$prd;
		$param->dw=$dw;
		if ($getKalender){
			// cek tanggal
			foreach ($getKalender as $dtKalender) {
				$startDate=$dtKalender['start_date'];
				$endDate=$dtKalender['end_date'];
			}
			if(($tgl>=$startDate)and($tgl<=$endDate)){
				$this->view->allowReg=1;
			}else{
				$this->view->allowReg=-1;
			}
		}else{
			$this->view->allowReg=0;
		}
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
			$this->_helper->navbar("pkrs",0);
		}else{
			$this->view->eksis="f";
			$this->_helper->navbar("register",0);
		}
	}
	
	function editsksAction()
	{
		// layout
		$this->_helper->layout()->setLayout('main');
		// title Browser
		$this->view->title="Edit SKS pada Penambahan Mata Kuliah PKRS";
		// get nim , paket kelas
		$nim=$this->uname;
		$kd_paket=$this->_request->get('pkt');
		$pkrs = new Pkrs();
		$getPkrs = $pkrs->getPkrsByNimPaket($nim, $kd_paket);
		if($getPkrs){
			foreach ($getPkrs as $dataKuliah) {
				$this->view->kd_paket=$dataKuliah['kd_paket_kelas'];
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
			$this->_helper->navbar("pkrs",0);
		}else{
			$this->view->eksis="f";
			$this->_helper->navbar("pkrs",0);
		}
	}
	
	function exportAction(){
		$param = new Zend_Session_Namespace('param_pkrs');
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
		$imgPath = str_replace('students/application/controllers/PkrsController.php','public/img/logo.png',$path);
		// konfigurasi excel
		PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Akademik")
									 ->setLastModifiedBy("Akademik")
									 ->setTitle("Export Data PKRS Mahasiswa")
									 ->setSubject("Sistem Informasi Akademik")
									 ->setDescription("Data PKRS Mahasiswa")
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(13);
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
		$objPHPExcel->getActiveSheet()->mergeCells('A1:K1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:K2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:K3');
		$objPHPExcel->getActiveSheet()->mergeCells('A8:K8');
		$objPHPExcel->getActiveSheet()->getStyle('A1:K1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A2:K2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:K2')->getFont()->setSize(14);
		$objPHPExcel->getActiveSheet()->getStyle('A1:K2')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(25);
		$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(25);
		// text
		$objPHPExcel->getActiveSheet()->setCellValue('A1',strtoupper($nm_pt));
		$objPHPExcel->getActiveSheet()->setCellValue('A2','FORM PKRS');
		$objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
		$objPHPExcel->getActiveSheet()->mergeCells('A5:B5');
		$objPHPExcel->getActiveSheet()->mergeCells('A6:B6');
		$objPHPExcel->getActiveSheet()->mergeCells('A7:B7');
		$objPHPExcel->getActiveSheet()->setCellValue('A4','  PERIODE');
		$objPHPExcel->getActiveSheet()->setCellValue('A5','  NPM');
		$objPHPExcel->getActiveSheet()->setCellValue('A6','  NAMA MAHASISWA');
		$objPHPExcel->getActiveSheet()->setCellValue('A7','  DOSEN WALI');
		$objPHPExcel->getActiveSheet()->mergeCells('C4:K4');
		$objPHPExcel->getActiveSheet()->mergeCells('C5:K5');
		$objPHPExcel->getActiveSheet()->mergeCells('C6:K6');
		$objPHPExcel->getActiveSheet()->mergeCells('C7:J7');
		$objPHPExcel->getActiveSheet()->setCellValue('C4',' :  '.$per);
		$objPHPExcel->getActiveSheet()->setCellValue('C5',' :  '.$nim);
		$objPHPExcel->getActiveSheet()->setCellValue('C6',' :  '.$nm_mhs);
		$objPHPExcel->getActiveSheet()->setCellValue('C7',' :  '.$dw);
		$objPHPExcel->getActiveSheet()->getStyle('A1:K8')->applyFromArray($noborder);
		$objPHPExcel->getActiveSheet()->setCellValue('A8','* Warna abu untuk PKRS yang belum/tidak diapprove dosen wali');
		$objPHPExcel->getActiveSheet()->getStyle('A8')->getFont()->setSize(8);
		// tabel krs
		$objPHPExcel->getActiveSheet()->mergeCells('A9:A11');
		$objPHPExcel->getActiveSheet()->mergeCells('B9:E9');
		$objPHPExcel->getActiveSheet()->mergeCells('F9:G9');
		$objPHPExcel->getActiveSheet()->mergeCells('H9:J11');
		$objPHPExcel->getActiveSheet()->mergeCells('K9:K11');
		$objPHPExcel->getActiveSheet()->mergeCells('B10:B11');
		$objPHPExcel->getActiveSheet()->mergeCells('C10:C11');
		$objPHPExcel->getActiveSheet()->mergeCells('D10:E10');
		$objPHPExcel->getActiveSheet()->mergeCells('F10:F11');
		$objPHPExcel->getActiveSheet()->mergeCells('G10:G11');
		$objPHPExcel->getActiveSheet()->setCellValue('A9',' NO');
		$objPHPExcel->getActiveSheet()->setCellValue('B9',' MATA KULIAH');
		$objPHPExcel->getActiveSheet()->setCellValue('F9',' DOSEN');
		$objPHPExcel->getActiveSheet()->setCellValue('H9',' NAMA KELAS');
		$objPHPExcel->getActiveSheet()->setCellValue('K9',' PERUBAHAN');
		$objPHPExcel->getActiveSheet()->setCellValue('B10',' KODE');
		$objPHPExcel->getActiveSheet()->setCellValue('C10',' NAMA MATA KULIAH');
		$objPHPExcel->getActiveSheet()->setCellValue('D10',' SKS');
		$objPHPExcel->getActiveSheet()->setCellValue('D11',' DEF');
		$objPHPExcel->getActiveSheet()->setCellValue('E11',' TAKE');
		$objPHPExcel->getActiveSheet()->setCellValue('F10',' KODE');
		$objPHPExcel->getActiveSheet()->setCellValue('G10',' NAMA DOSEN ');
		$objPHPExcel->getActiveSheet()->getStyle('A9:K11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A9:K11')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A9:K11')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('A9:K11')->getFont()->setBold(true);
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
				if($dataBelajar['mode']=='i'){
					$mode="TAMBAH";
				}elseif ($dataBelajar['mode']=='d'){
					$mode="DROP";
				}elseif ($dataBelajar['mode']=='u'){
					$mode="UBAH SKS";
				}else{
					$mode="-";
				}
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$n,' '.$mode);
				$objPHPExcel->getActiveSheet()->getStyle('K'.$n)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				if($dataBelajar['executed']==0){
					$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':K'.$n)->applyFromArray($cell_color);
				}
				$i++;
				$n++;
			}
		}
		$objPHPExcel->getActiveSheet()->getStyle('A3:K3')->applyFromArray(array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE))));
		$objPHPExcel->getActiveSheet()->getStyle('A9:K'.$n)->applyFromArray($border);
		$n=$n+2;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$n.':C'.$n);
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$n.':F'.$n);
		$objPHPExcel->getActiveSheet()->mergeCells('G'.$n.':K'.$n);
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$n,'MAHASISWA');
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$n,'BAG.AKADEMIK');
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$n,'DOSEN WALI');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':G'.$n)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':G'.$n)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':K'.$n)->applyFromArray($border);
		$n++;
		$objPHPExcel->getActiveSheet()->getRowDimension($n)->setRowHeight(70);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$n.':C'.$n);
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$n.':F'.$n);
		$objPHPExcel->getActiveSheet()->mergeCells('G'.$n.':K'.$n);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':K'.$n)->applyFromArray($border);
		$n++;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$n.':C'.$n);
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$n,'('.$nm_mhs.')');
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$n.':F'.$n);
		$objPHPExcel->getActiveSheet()->mergeCells('G'.$n.':K'.$n);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$n,'('.$dw.')');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':G'.$n)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':G'.$n)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':K'.$n)->applyFromArray($border);
		
		// protect sheet
		$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);
		
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="PKRS.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
}