<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Nilai Controller -> Controller untuk modul halaman nilai mahasiswa
*/
class NilaiController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Kelas');
		Zend_Loader::loadClass('KelasTA');
		Zend_Loader::loadClass('Paketkelas');
		Zend_Loader::loadClass('Kuliah');
		Zend_Loader::loadClass('Nilai');
		Zend_Loader::loadClass('NilaiTA');
		Zend_Loader::loadClass('Kurikulum');
		Zend_Loader::loadClass('Ekuivalensi');
		Zend_Loader::loadClass('Register');
		Zend_Loader::loadClass('StatReg');
		Zend_Loader::loadClass('AturanNilai');
		Zend_Loader::loadClass('Konversi');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Validation');
		Zend_Loader::loadClass('PHPExcel');
		Zend_Loader::loadClass('PHPExcel_Cell_AdvancedValueBinder');
		Zend_Loader::loadClass('PHPExcel_IOFactory');
		Zend_Loader::loadClass('FPDF');
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
		$this->view->nilai_act="active";
	}
	
	function indexAction()
	{
		
	}

	function listAction()
	{
		// Title Browser
		$this->view->title = "Daftar Nilai";
		$nilai = new Nilai();
		$nim=$this->uname;
		$kd_periode="";
		$getNilai = $nilai->getNilaiByNimPeriode($nim, $kd_periode);
		$arrPer=array();
		foreach ($getNilai as $dtNilai){
			$arrPer[]=$dtNilai['kd_periode'];
		}
		$arrPer=array_values(array_unique($arrPer));
		$this->view->listPer=$arrPer;
		$this->view->listNilai=$getNilai;
		// navigation
		$this->_helper->navbar(0,0);
	}

	function detilAction(){
		// get kd kuliah
		$kd_kuliah=$this->_request->get('id');
		$nilai = new Nilai();
		$getNilai = $nilai->getNilaiByKd($kd_kuliah);
		if($getNilai){
			foreach ($getNilai as $dataNilai) {
				$kd_paket=$dataNilai['kd_paket_kelas'];
				$this->view->kd_kelas=$dataNilai['kd_kelas'];
				$kdKelas=$dataNilai['kd_kelas'];
				$this->view->kd_kuliah=$dataNilai['kd_kuliah'];
				$this->view->nm_prodi=$dataNilai['nm_prodi'];
				$kdProdi=$dataNilai['kd_prodi'];
				$this->view->kd_per=$dataNilai['kd_periode'];
				$kdPeriode=$dataNilai['kd_periode'];
				$this->view->nm_kelas=$dataNilai['nm_kelas'];
				$this->view->jns_kelas=$dataNilai['jns_kelas'];
				$this->view->kd_dsn=$dataNilai['kd_dosen'];
				$this->view->nm_dsn=$dataNilai['nm_dosen'];
				$this->view->kd_mk=$dataNilai['kode_mk'];
				$this->view->nm_mk=$dataNilai['nm_mk'];
				$this->view->sks=$dataNilai['sks_tm']+$dataNilai['sks_prak']+$dataNilai['sks_prak_lap']+$dataNilai['sks_sim'];
				$this->view->p1=$dataNilai['p1'];
				$this->view->p2=$dataNilai['p2'];
				$this->view->p3=$dataNilai['p3'];
				$this->view->p4=$dataNilai['p4'];
				$this->view->p5=$dataNilai['p5'];
				$this->view->p6=$dataNilai['p6'];
				$this->view->p7=$dataNilai['p7'];
				$this->view->p8=$dataNilai['p8'];
				$this->view->uts=$dataNilai['uts'];
				$this->view->uas=$dataNilai['uas'];
				$this->view->nilai_tot=$dataNilai['nilai_tot'];
				$this->view->index=$dataNilai['index'];
				if($dataNilai['status']=='1'){
					$this->view->status="FIX";
					$this->view->label="success";
				}elseif ($dataNilai['status']=='0'){
					$this->view->status="BELUM FIX";
					$this->view->label="danger";
				}else{
					$this->view->status="DITUNDA";
					$this->view->label="warning";
				}
			}
			// kelas
			$kelas = new Kelas();
			$getKelas = $kelas->getKelasByKd($kdKelas);
			foreach ($getKelas as $dtKls) {
				$this->view->nm_p1=$dtKls['nm_p1'];
				$this->view->nm_p2=$dtKls['nm_p2'];
				$this->view->nm_p3=$dtKls['nm_p3'];
				$this->view->nm_p4=$dtKls['nm_p4'];
				$this->view->nm_p5=$dtKls['nm_p5'];
				$this->view->nm_p6=$dtKls['nm_p6'];
				$this->view->nm_p7=$dtKls['nm_p7'];
				$this->view->nm_p8=$dtKls['nm_p8'];
				$this->view->p_p1=$dtKls['p_p1'];
				$this->view->p_p2=$dtKls['p_p2'];
				$this->view->p_p3=$dtKls['p_p3'];
				$this->view->p_p4=$dtKls['p_p4'];
				$this->view->p_uts=$dtKls['p_uts'];
				$this->view->p_p5=$dtKls['p_p5'];
				$this->view->p_p6=$dtKls['p_p6'];
				$this->view->p_p7=$dtKls['p_p7'];
				$this->view->p_p8=$dtKls['p_p8'];
				$this->view->p_uas=$dtKls['p_uas'];
				$this->view->p_tot=$dtKls['p_p1']+$dtKls['p_p2']+$dtKls['p_p3']+$dtKls['p_p4']+$dtKls['p_p5']+$dtKls['p_p6']+$dtKls['p_p7']+$dtKls['p_p8']+$dtKls['p_uts']+$dtKls['p_uas'];
				$this->view->note=$dtKls['note_dosen'];
			}
			// aturan nilai
			$aturanNilai = new AturanNilai();
			$this->view->listAturan = $aturanNilai->getAturanNilaiByProdiPeriode($kdProdi,$kdPeriode);
			// navigation
			$this->_helper->navbar('nilai/list',0);
		}else{
			$nilaiTA = new NilaiTA();
			$getNilaiTA = $nilaiTA->getNilaiTAByKd($kd_kuliah);
			if($getNilaiTA){
				foreach ($getNilaiTA as $dataNilai) {
					$kd_paket=$dataNilai['kd_paket_kelas'];
					$this->view->kd_kelas=$dataNilai['kd_kelas'];
					$kdKelas=$dataNilai['kd_kelas'];
					$this->view->kd_kuliah=$dataNilai['kd_kuliah'];
					$this->view->nm_prodi=$dataNilai['nm_prodi'];
					$kdProdi=$dataNilai['kd_prodi'];
					$this->view->kd_per=$dataNilai['kd_periode'];
					$kdPeriode=$dataNilai['kd_periode'];
					$this->view->nm_kelas=$dataNilai['nm_kelas'];
					$this->view->jns_kelas=$dataNilai['jns_kelas'];
					$this->view->kd_dsn=$dataNilai['kd_dosen'];
					$this->view->nm_dsn=$dataNilai['nm_dosen'];
					$this->view->kd_mk=$dataNilai['kode_mk'];
					$this->view->nm_mk=$dataNilai['nm_mk'];
					$this->view->sks=$dataNilai['sks_tm']+$dataNilai['sks_prak']+$dataNilai['sks_prak_lap']+$dataNilai['sks_sim'];
					$this->view->p1=$dataNilai['p1'];
					$this->view->p2=$dataNilai['p2'];
					$this->view->p3=$dataNilai['p3'];
					$this->view->p4=$dataNilai['p4'];
					$this->view->p5=$dataNilai['p5'];
					$this->view->p6=$dataNilai['p6'];
					$this->view->p7=$dataNilai['p7'];
					$this->view->p8=$dataNilai['p8'];
					$this->view->nilai_tot=$dataNilai['nilai_tot'];
					$this->view->index=$dataNilai['index'];
					if($dataNilai['status']=='1'){
						$this->view->status="FIX";
						$this->view->label="success";
					}else{
						$this->view->status="BELUM FIX";
						$this->view->label="danger";
					}
					$this->view->per_mulai=$dataNilai['kd_periode_mulai'];
					$this->view->pemb1=$dataNilai['nm_dosen_pemb1'];
					$this->view->pemb2=$dataNilai['nm_dosen_pemb2'];
					$this->view->noreg=$dataNilai['no_reg'];
					$this->view->judul=$dataNilai['judul'];
				}
				// kelas
				$kelasTA = new KelasTA();
				$getKelasTA = $kelasTA->getKelasTAByKd($kdKelas);
				foreach ($getKelasTA as $dtKls) {
					$this->view->nm_p1=$dtKls['nm_p1'];
					$this->view->nm_p2=$dtKls['nm_p2'];
					$this->view->nm_p3=$dtKls['nm_p3'];
					$this->view->nm_p4=$dtKls['nm_p4'];
					$this->view->nm_p5=$dtKls['nm_p5'];
					$this->view->nm_p6=$dtKls['nm_p6'];
					$this->view->nm_p7=$dtKls['nm_p7'];
					$this->view->nm_p8=$dtKls['nm_p8'];
					$this->view->p_p1=$dtKls['p_p1'];
					$this->view->p_p2=$dtKls['p_p2'];
					$this->view->p_p3=$dtKls['p_p3'];
					$this->view->p_p4=$dtKls['p_p4'];
					$this->view->p_p5=$dtKls['p_p5'];
					$this->view->p_p6=$dtKls['p_p6'];
					$this->view->p_p7=$dtKls['p_p7'];
					$this->view->p_p8=$dtKls['p_p8'];
					$this->view->p_tot=$dtKls['p_p1']+$dtKls['p_p2']+$dtKls['p_p3']+$dtKls['p_p4']+$dtKls['p_p5']+$dtKls['p_p6']+$dtKls['p_p7']+$dtKls['p_p8'];
					$this->view->note=$dtKls['note_dosen'];
				}
				// aturan nilai
				$aturanNilai = new AturanNilai();
				$this->view->listAturan = $aturanNilai->getAturanNilaiByProdiPeriode($kdProdi,$kdPeriode);
				// navigation
				$this->_helper->navbar('nilai/list',0);
			}else{
				$this->view->eksis="f";	
			}
		}
		$this->view->title="Data Nilai Mahasiswa";
	}
	
	function khsAction(){
		// Title Browser
		$this->view->title = "Kartu Hasil Studi Mahasiswa";
		// navigation
		$this->_helper->navbar(0,'nilai/ekhs');
		$nim = $this->uname;
		$kd_periode=$this->_request->get('id');
		$periode = new Periode();
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
		// get data nilai
		$nilai=new Nilai();
		$getNilai=$nilai->getNilaiByNimPeriode($nim,$kd_periode);
		$getNilaiAll=$nilai->getNilaiByNimPeriode($nim,"");
		$arrPer=array();
		foreach ($getNilai as $dtNilai){
			$arrPer[]=$dtNilai['kd_periode'];
		}
		$arrPer=array_values(array_unique($arrPer));
		// untuk select
		if($kd_periode){
			$arrPerSel=array();
			foreach ($getNilaiAll as $dtNilai){
				$arrPerSel[]=$dtNilai['kd_periode'];
			}
			$arrPerSel=array_values(array_unique($arrPerSel));
			$this->view->listPerSel=$arrPerSel;
		}else{
			$this->view->listPerSel=$arrPer;
		}
		$this->view->listPer=$arrPer;
		$this->view->listNilai=$getNilai;
		// create session for excel
		$param = new Zend_Session_Namespace('param_khs');
		$param->datakhs=$getNilai;
		$param->arrPer=$arrPer;
		$param->nim=$nim;
		$param->nm_mhs=$nm;
		$param->prd=$nm_prd;
		$param->akt=$akt;
		$param->dw=$dw;
	}
	
	function ekhsAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// session
		$param = new Zend_Session_Namespace('param_khs');
		$dataNilai = $param->datakhs;
		$arrPer=$param->arrPer;
		$nim=$param->nim;
		$nm=$param->nm_mhs;
		$prd=$param->prd;
		$akt=$param->akt;
		$dw=$param->dw;
		// image path logo
		$path = __FILE__;
		$imgPath = str_replace('students/application/controllers/NilaiController.php','public/img/logo.png',$path);
		// konfigurasi excel
		PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder() );
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Administrator")
									 ->setLastModifiedBy("Akademik")
									 ->setTitle("Export KHS")
									 ->setSubject("Sistem Informasi Akademik")
									 ->setDescription("Kartu Hasil Studi")
									 ->setKeywords("khs")
									 ->setCategory("Data File"); 
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('Kartu Hasil Studi');
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT)
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
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
		$objPHPExcel->getActiveSheet()->mergeCells('F1:J1');
		$objPHPExcel->getActiveSheet()->mergeCells('C2:J2');
		$objPHPExcel->getActiveSheet()->mergeCells('C3:J3');
		$objPHPExcel->getActiveSheet()->mergeCells('E4:J4');
		$objPHPExcel->getActiveSheet()->mergeCells('E5:J5');
		$objPHPExcel->getActiveSheet()->mergeCells('E6:J6');
		$objPHPExcel->getActiveSheet()->mergeCells('E7:J7');
		$objPHPExcel->getActiveSheet()->mergeCells('C4:D4');
		$objPHPExcel->getActiveSheet()->mergeCells('C5:D5');
		$objPHPExcel->getActiveSheet()->mergeCells('C6:D6');
		$objPHPExcel->getActiveSheet()->mergeCells('C7:D7');
		$objPHPExcel->getActiveSheet()->mergeCells('C8:D8');
		
		$objPHPExcel->getActiveSheet()->getStyle('C2:I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('C2:I3')->getFont()->setSize(14);
		$objPHPExcel->getActiveSheet()->getStyle('C4:I8')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('C2:I8')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('C1:I1')->getFont()->setSize(8);
		$objPHPExcel->getActiveSheet()->getStyle('C1:I1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('F1:I1')->getFont()->setItalic(true);
		$objPHPExcel->getActiveSheet()->getStyle('F1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('C3:J3')->applyFromArray(array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE))));

		// insert data to excel
		$objPHPExcel->getActiveSheet()->setCellValue('F1','Printed by '.$this->uname.': '. date("d m Y h:i:s"));
		$objPHPExcel->getActiveSheet()->setCellValue('C2',strtoupper($this->nm_pt));
		$objPHPExcel->getActiveSheet()->setCellValue('C3','KARTU HASIL STUDI');
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
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,'SKS');
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,'Taken');
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,'Nilai');						
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,'AM');	
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,'K x A');
			//konfigurasi field
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':J'.$i)->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':J'.$i)->applyFromArray($cell_color);	
			$objPHPExcel->getActiveSheet()->mergeCells('C'.($i-1).':D'.($i-1));
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':J'.$i)->getFont()->setBold(true);	
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':J'.$i)->applyFromArray($borderBottom);
			$i++;
			$rowAwal=$i;
			$n=1;
			foreach ($dataNilai as $dataExport) {
				if($dataExport['kd_periode']==$dtPer){
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$n);	
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$dataExport['kode_mk']);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$dataExport['nm_mk']);	
					$sks_def=$dataExport['sks_tm']+$dataExport['sks_prak']+$dataExport['sks_prak_lap']+$dataExport['sks_sim'];
					$sks_take=$dataExport['sks_tm']+$dataExport['sks_prak']+$dataExport['sks_prak_lap']+$dataExport['sks_sim']-$dataExport['sks_deducted'];
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$sks_def);
					$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$sks_take);
					$arrIndeks=explode('/', $dataExport['index']);
					if($dataExport['status']==1){
						$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$arrIndeks[0]);
						$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$arrIndeks[1]);
						$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$sks_take*$arrIndeks[1]);
					}else{
						$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,'-');
						$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,'-');
						$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,0);
					}
					//konfigurasi field
					$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':J'.$i)->applyFromArray($borderInside);
					$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray($borderLeft);
					$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->applyFromArray($borderLeft);
					$n++;
					$i++;
				}
			}
			if ($n==1){
				//
			}else{
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,'IPS');
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,'=J'.$i.'/G'.$i);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,'=SUM(F'.$rowAwal.':F'.($i-1).')');
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,'=SUM(G'.$rowAwal.':G'.($i-1).')');
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,'=SUM(J'.$rowAwal.':J'.($i-1).')');
				// konfigurasi field excel
				$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':J'.$i)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->applyFromArray($borderDouble);
			}
			$i=$i+2;
			
		}
		
		$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);
		
		//Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Kartu Hasil Studi.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	
	function transkripAction(){
		// Title Browser
		$this->view->title = "Transkrip Nilai Mahasiswa";
		// navigation
		$this->_helper->navbar(0,'nilai/etranskrip');
		// session
		$param = new Zend_Session_Namespace('param_trkp');
		$nim = $this->uname;
		$kd_periode="";
		// get data mahasiswa
		$mhs = new Mahasiswa();
		$getMhs = $mhs->getMahasiswaByNim($nim);
		foreach ($getMhs as $dtMhs) {
			$nm=$dtMhs['nm_mhs'];
			$akt=$dtMhs['id_angkatan'];
			$kd_prd=$dtMhs['kd_prodi'];
			$nm_prd=$dtMhs['nm_prodi'];
		}
		$this->view->nim=$nim;
		$this->view->nm=$nm;
		$this->view->akt=$akt;
		$this->view->kd_prd=$kd_prd;
		$this->view->nm_prd=$nm_prd;
		// get kurikulum mahasiswa
		$nilai = new Nilai();
		$getKurikulumMhs=$nilai->getTranskripKurikulumByNim($nim);
		$kur_mhs="";
		if($getKurikulumMhs){
			foreach($getKurikulumMhs as $dtKurMhs){
				$kur_mhs=$dtKurMhs['id_kurikulum'];
			}
		}
		// smt kur
		$kurikulum = new Kurikulum();
		$getKur=$kurikulum->getKurById($kur_mhs);
		$nSmt=0;
		foreach ($getKur as $dtKur){
			$nSmt=$dtKur['smt_normal'];
			$this->view->smt=$dtKur['smt_normal'];
		}
		// get data transkrip
		$nilaiTrkp=$nilai->getTranskripByNimKurikulum($nim,$kur_mhs);
		$this->view->listNilai= $nilaiTrkp;
		// create session for excel
		$param->data=$nilaiTrkp;
		$param->nSmt=$nSmt;
		$param->nim=$nim;
		$param->nm_mhs=$nm;
		$param->prd=$nm_prd;
		$param->akt=$akt;
	}
	
	function etranskripAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// session
		$param = new Zend_Session_Namespace('param_trkp');
		$dataNilai = $param->data;
		$nSmt=$param->nSmt;
		$nim=$param->nim;
		$nm=$param->nm_mhs;
		$prd=$param->prd;
		$akt=$param->akt;
		// image path logo
		$path = __FILE__;
		$imgPath = str_replace('students/application/controllers/NilaiController.php','public/img/logo.png',$path);
		// konfigurasi excel
		PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder() );
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Administrator")
									 ->setLastModifiedBy("Akademik")
									 ->setTitle("Export Transkrip Nilai")
									 ->setSubject("Sistem Informasi Akademik")
									 ->setDescription("Transkrip Nilai")
									 ->setKeywords("transkrip nilai")
									 ->setCategory("Data File"); 
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('Transkrip Nilai');
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT)
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(60);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
		// height
		$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(25);
		$objPHPExcel->getActiveSheet()->getRowDimension(3)->setRowHeight(25);
		// properties field excel;
		$objPHPExcel->getActiveSheet()->mergeCells('F1:I1');
		$objPHPExcel->getActiveSheet()->mergeCells('C2:I2');
		$objPHPExcel->getActiveSheet()->mergeCells('C3:I3');
		$objPHPExcel->getActiveSheet()->mergeCells('E4:I4');
		$objPHPExcel->getActiveSheet()->mergeCells('E5:I5');
		$objPHPExcel->getActiveSheet()->mergeCells('E6:I6');
		$objPHPExcel->getActiveSheet()->mergeCells('E7:I7');
		$objPHPExcel->getActiveSheet()->mergeCells('C4:D4');
		$objPHPExcel->getActiveSheet()->mergeCells('C5:D5');
		$objPHPExcel->getActiveSheet()->mergeCells('C6:D6');
		$objPHPExcel->getActiveSheet()->mergeCells('C7:D7');
		
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
		
		$objPHPExcel->getActiveSheet()->getStyle('C2:I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('C2:I3')->getFont()->setSize(14);
		$objPHPExcel->getActiveSheet()->getStyle('C4:I7')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('C2:I7')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('C1:I1')->getFont()->setSize(8);
		$objPHPExcel->getActiveSheet()->getStyle('C1:I1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('F1:I1')->getFont()->setItalic(true);
		$objPHPExcel->getActiveSheet()->getStyle('F1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('C3:I3')->applyFromArray(array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE))));

		// insert data to excel
		$objPHPExcel->getActiveSheet()->setCellValue('F1','Printed by '.$this->uname.' : '.date("d m Y h:i:s"));
		$objPHPExcel->getActiveSheet()->setCellValue('C2',strtoupper($this->nm_pt));
		$objPHPExcel->getActiveSheet()->setCellValue('C3','TRANSKRIP AKADEMIK');
		$objPHPExcel->getActiveSheet()->setCellValue('C5','Nama');
		$objPHPExcel->getActiveSheet()->setCellValue('C6','NPM');
		$objPHPExcel->getActiveSheet()->setCellValue('C7','Prodi/Angkatan');
		
		$objPHPExcel->getActiveSheet()->setCellValue('E5',': '.$nm);
		$objPHPExcel->getActiveSheet()->setCellValue('E6',': '.$nim);
		$objPHPExcel->getActiveSheet()->setCellValue('E7',': '.$prd." / ".$akt);
			
		$i=9;
		$smtr=0;
		$totSKS_all=0;
		$totKA_all=0; 
		for($x=1;$x<=$nSmt;$x++) {
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,'Semester '.$x);	
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->getFont()->setBold(true);
			$i++;
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,'No');	
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,'Kode');
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,'Mata Kuliah');	
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,'SKS');
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,'Nilai');						
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,'AM');	
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,'K x A');
			//konfigurasi field
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->applyFromArray($cell_color);	
			$objPHPExcel->getActiveSheet()->mergeCells('C'.($i-1).':D'.($i-1));
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->getFont()->setBold(true);	
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->applyFromArray($borderBottom);
			$i++;
			$rowAwal=$i;
			$n=1;
			$totSKS_part=0;
			$totKA_part=0;
			foreach ($dataNilai as $dataExport) {
				if($dataExport['smt_def']==$x){
					if((($dataExport['sks_taken'] >= $dataExport['sks_def'])and($dataExport['ori']==1))or($dataExport['ori']==0)){
					// if($dataExport['sks_taken'] >= $dataExport['sks_def']){
						$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$n);	
						$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$dataExport['kode_mk']);
						$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$dataExport['nm_mk']);	
						$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$dataExport['sks_def']);
						if($dataExport['status']==1){
							$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$dataExport['huruf']);
							$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$dataExport['bobot']);
							$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$dataExport['bobot']*$dataExport['sks_def']);
						}else{
							$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,'-');
							$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,'-');
							$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,0);;
						}
						//konfigurasi field
						$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':I'.$i)->applyFromArray($borderInside);
						$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray($borderLeft);
						$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray($borderLeft);
						if($dataExport['status']==1){
							$totKA_all=$totKA_all+$dataExport['bobot']*$dataExport['sks_def'];
							$totSKS_all=$totSKS_all+$dataExport['sks_def'];
							$totKA_part=$totKA_part+$dataExport['bobot']*$dataExport['sks_def'];
                                                        $totSKS_part=$totSKS_part+$dataExport['sks_def'];
						}
						$n++;
						$i++;
					}
				}
			}
			if ($n==1){
				//
			}else{
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,'IPS');
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,'=I'.$i.'/F'.$i);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$totSKS_part);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$totKA_part);
				// konfigurasi field excel
				$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':I'.$i)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->applyFromArray($borderDouble);
			}
			$i=$i+2;
			
		}
		
		// IPK
		$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+2),'IPK');
		$objPHPExcel->getActiveSheet()->mergeCells('C'.($i+2).':D'.($i+2));
		if($totSKS_all==0){
			$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+2),': '.number_format(0,2,',','.'));	
		}else{
			$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+2),': '.number_format(($totKA_all/$totSKS_all),2,',','.'));
		}
		$objPHPExcel->getActiveSheet()->getStyle('C'.($i+2).':E'.($i+2))->getFont()->setSize(14);
		$objPHPExcel->getActiveSheet()->getStyle('C'.($i+2).':E'.($i+2))->getFont()->setBold(true);
		
		$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);
		
		//Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Transkrip Nilai.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	
	
	function pdftranskripAction(){
		$user = new Menu();
		$menu = "nilai/transkrip";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			$pdf = new FPDF();
			$pdf->AddPage();
			$pdf->SetFont('Arial','B',16);
			for ($i=1;$i<=100;$i++){
				$pdf->Cell(0,10,'Hello World!');
			}
			$pdf->Output();
		}
	}
}
