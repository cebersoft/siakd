<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Keuangan Controller -> Controller untuk modul keuangan
*/
class KeuanganController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('PaketBiaya');
		Zend_Loader::loadClass('Biaya');
		Zend_Loader::loadClass('FormulaBiaya');
		Zend_Loader::loadClass('FormulaBiayaTA');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('MhsGelombang');
		Zend_Loader::loadClass('MhsRegPeriode');
		Zend_Loader::loadClass('MhsBiayaPeriode');
		Zend_Loader::loadClass('Sumbangan');
		Zend_Loader::loadClass('Konversi');
		Zend_Loader::loadClass('Bayar');
		Zend_Loader::loadClass('Bank');
		Zend_Loader::loadClass('ViaBayar');
		Zend_Loader::loadClass('Term');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
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
		$this->view->keu_act="active";
	}


	private function intervalSemester($periode_awal,$periode_akhir){
		$arrPerAwal=explode("/", $periode_awal);
		$arrThnAwal=explode("-", $arrPerAwal[0]);
		$thnAwal=$arrThnAwal[0];
		$smtAwal=0;
		if($arrPerAwal[1]=='GASAL'){
			$smtAwal=0.5;
		}elseif ($arrPerAwal[1]=='GENAP'){
			$smtAwal=1;
		}
		$perAwal=$thnAwal+$smtAwal;
		
		$arrPerAkhir=explode("/", $periode_akhir);
		$arrThnAkhir=explode("-", $arrPerAkhir[0]);
		$thnAkhir=$arrThnAkhir[0];
		$smtAkhir=0;
		if($arrPerAkhir[1]=='GASAL'){
			$smtAkhir=0.5;
		}elseif ($arrPerAkhir[1]=='GENAP'){
			$smtAkhir=1;
		}
		$perAkhir=$thnAkhir+$smtAkhir;
		$interval=($perAkhir-$perAwal)*2;
		return $interval;
	}	

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Status Keuangan";
		$nim = $this->uname;
		// navigation
		$this->_helper->navbar(0,0);
		$mahasiswa = new Mahasiswa();
		$getMhs=$mahasiswa->getMahasiswaByNim($nim);
		if(!$getMhs){
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Daftar Biaya Periodik dan TA Mahasiswa";
		}else {
			foreach ($getMhs as $dtMhs) {
				$nm_mhs=$dtMhs['nm_mhs'];
				$this->view->nm=$nm_mhs;
				$this->view->nim=$nim;
				$this->view->akt=$dtMhs['id_angkatan'];
				$id_akt=$dtMhs['id_angkatan'];
				$kd_prd=$dtMhs['kd_prodi'];
				$this->view->nm_prd=$dtMhs['nm_prodi'];
				$this->view->stat_msk=$dtMhs['nm_stat_masuk'];
			}
			// get gelombang mahasiswa
			$mhsGel=new MhsGelombang();
			$getMhsGel=$mhsGel->getMhsGelombangByNim($nim);
			$this->view->nm_gel="";
			if($getMhsGel){
				foreach ($getMhsGel as $dtMhsGel){
					$this->view->nm_gel=$dtMhsGel['nm_gelombang'];
				}
			}
			// Title Browser
			$this->view->title = "Daftar Biaya Periodik dan TA Mahasiswa : ".$nm_mhs." (".$nim.")";
			// get data biaya periodik mahasiswa
			$mhsBiayaPer=new MhsBiayaPeriode();
			$getBiayaPeriode=$mhsBiayaPer->getMhsBiayaPeriodeByNim($nim);
			$this->view->listMhsBiayaPer=$getBiayaPeriode;
			// get data formula interval
			// get komponen formula
			$formula = new FormulaBiayaTA();
			$getFormulaTA=$formula->getFormulaBiayaTAByAktProdi($id_akt, $kd_prd);
			$arrKomp=array();
			$i=0;
			foreach ($getFormulaTA as $dtFormTA) {
				$arrKomp[$i]=$dtFormTA['id_komp'];
				$i++;
			}
			$arrKomp=array_unique($arrKomp);
			foreach ($arrKomp as $dtKomp) {
				$nFlag[$dtKomp]=0;
				$perFlag[$dtKomp]="-";
				$intvFlag[$dtKomp]=1;
			}
			// last periode registrasi
			$last_per="";
			foreach ($getBiayaPeriode as $dataPeriode){
				if($dataPeriode['kd_periode']>$last_per){
					$last_per=$dataPeriode['kd_periode'];
				}
			}
			// maping biaya
			$arrFormulaIntval=array();
			$i=0;
			foreach ($getBiayaPeriode as $dtReg){
				foreach ($arrKomp as $dtKomp) {
					$getFormulaPeriode=$formula->getFormulaBiayaTAByPeriode($id_akt, $kd_prd, $dtKomp, $last_per);
					//$getFormulaPeriode=$formula->getFormulaBiayaTAById($id_akt, $kd_prd, $dtKomp, $kd_per_berlaku);
					foreach ($getFormulaPeriode as $dtFormulaPeriode){
						if((($dtFormulaPeriode['id_param']=='003')and($dtReg['sks_ta']>$dtFormulaPeriode['min_value']))or(($dtFormulaPeriode['id_param']=='103')and($dtReg['n_ta']>$dtFormulaPeriode['min_value']))){
							if($perFlag[$dtKomp]=="-"){ // awal
								$perFlag[$dtKomp]=$dtReg['kd_periode'];
								$intvFlag[$dtKomp]=$dtFormulaPeriode['intval_perbaruan'];
								// set array biaya
								$arrFormulaIntval[$i]['kd_periode']=$dtReg['kd_periode'];
								$arrFormulaIntval[$i]['id_komp']=$dtFormulaPeriode['id_komp'];
								$arrFormulaIntval[$i]['nm_komp']=$dtFormulaPeriode['nm_komp'];
								$arrFormulaIntval[$i]['nominal']=$dtFormulaPeriode['nominal'];
								$arrFormulaIntval[$i]['nm_paket']=$dtFormulaPeriode['nm_paket'];
								$arrFormulaIntval[$i]['nm_param']=$dtFormulaPeriode['nm_param'];
								$arrFormulaIntval[$i]['min_value']=$dtFormulaPeriode['min_value'];
								$arrFormulaIntval[$i]['kd_periode_berlaku']=$dtFormulaPeriode['kd_periode_berlaku'];
								$i++;
							}else{
								$interval=$this->intervalSemester($perFlag[$dtKomp], $dtReg['kd_periode']);
								$x=($interval%$intvFlag[$dtKomp]);
								if ($interval%$intvFlag[$dtKomp]==0){
									// ganti flag
									$perFlag[$dtKomp]=$dtReg['kd_periode'];
									$intvFlag[$dtKomp]=$dtFormulaPeriode['intval_perbaruan'];
									// set array biaya
									$arrFormulaIntval[$i]['kd_periode']=$dtReg['kd_periode'];
									$arrFormulaIntval[$i]['id_komp']=$dtFormulaPeriode['id_komp'];
									$arrFormulaIntval[$i]['nm_komp']=$dtFormulaPeriode['nm_komp'];
									$arrFormulaIntval[$i]['nominal']=$dtFormulaPeriode['nominal'];
									$arrFormulaIntval[$i]['nm_paket']=$dtFormulaPeriode['nm_paket'];
									$arrFormulaIntval[$i]['nm_param']=$dtFormulaPeriode['nm_param'];
									$arrFormulaIntval[$i]['min_value']=$dtFormulaPeriode['min_value'];
									$arrFormulaIntval[$i]['kd_periode_berlaku']=$dtFormulaPeriode['kd_periode_berlaku'];
									$i++;
								}
							}
							$nFlag[$dtKomp]=$nFlag[$dtKomp]+1;
						}
					}
				}
			}
			$this->view->listBiayaInterval=$arrFormulaIntval;
			// get data sumbangan
			$sumb=new Sumbangan();
			$this->view->listSumbangan=$sumb->getSumbanganDtlByNim($nim);
			// get data pembayaran
			$bayar = new Bayar();
			$this->view->listBayar=$bayar->getBayarByNim($nim);
			$listBayar=$bayar->getBayarByNim($nim);
			$totBayarPer=0;
			foreach ($listBayar as $dtBayar){
				if(($dtBayar['id_term']=='1')or($dtBayar['id_term']=='2')){
					$totBayarPer=$totBayarPer+$dtBayar['nominal'];
				}
			}
			$this->view->totBayarPer=$totBayarPer;
			// bank
			$bank = new Bank();
			$this->view->listBank=$bank->getBankAktif();
			// via bayar
			$via = new ViaBayar();
			$this->view->listVia=$via->fetchAll();
			// term bayar
			$term = new Term();
			$this->view->listTerm=$term->fetchAll();
		}
	}
	
	function detilAction(){
		$nim = $this->uname;
		$per=$this->_request->get('per');
		$mhsReg=new MhsRegPeriode();
		$getMhsReg=$mhsReg->getMhsRegPeriodeByNimPeriode($nim, $per);
		if(!$getMhsReg){
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Rincian Biaya Per Mahasiswa";
			// navigation
			$this->_helper->navbar("keuangan",0);
		}else {
			// get biaya detil
			$mhsBiaya=new MhsBiayaPeriode();
			$getMhsBiaya=$mhsBiaya->getMhsBiayaPeriodeDetilByNimPeriode($nim, $per);
			foreach ($getMhsReg as $dtMhsReg) {
				$nm_mhs=$dtMhsReg['nm_mhs'];
				$this->view->nm=$nm_mhs;
				$this->view->nim=$nim;
				$this->view->akt=$dtMhsReg['id_angkatan'];
				$this->view->per=$per;
				$this->view->nm_prd=$dtMhsReg['nm_prodi'];
				$this->view->stat_msk=$dtMhsReg['nm_stat_masuk'];
				$this->view->nm_gel=$dtMhsReg['nm_gelombang'];
				$this->view->stat_reg=$dtMhsReg['status_reg'];
			}
			// Title Browser
			$this->view->title = "Rincian Biaya Mahasiswa : ".$nm_mhs." (".$nim.") Periode ".$per;
			$this->view->listMhsBiayaDtl=$getMhsBiaya;
			// navigation
			$this->_helper->navbar("keuangan","keuangan/edetil?nim=".$nim.'&per='.$per);
		}
	}

	function edetilAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		$nim=$this->_request->get('nim');
		$per=$this->_request->get('per');
		$nm_pt="SEKOLAH TINGGI FARMASI INDONESIA";
		$mhsReg=new MhsRegPeriode();
		$getMhsReg=$mhsReg->getMhsRegPeriodeByNimPeriode($nim, $per);
		// get biaya detil
		$mhsBiaya=new MhsBiayaPeriode();
		$getMhsBiaya=$mhsBiaya->getMhsBiayaPeriodeDetilByNimPeriode($nim, $per);
		foreach ($getMhsReg as $dtMhsReg) {
			$nm_mhs=$dtMhsReg['nm_mhs'];
			$akt=$dtMhsReg['id_angkatan'];
			$nm_prd=$dtMhsReg['nm_prodi'];
			$stat_msk=$dtMhsReg['nm_stat_masuk'];
			$nm_gel=$dtMhsReg['nm_gelombang'];
			$stat_reg=$dtMhsReg['status_reg'];
		}
		$title = "Rincian Biaya Mahasiswa Periode ".$per;
		// konfigurasi excel
		PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Administrator")
		->setLastModifiedBy("Keuangan")
		->setTitle("Export Rincian Biaya")
		->setSubject("Sistem Informasi Keuangan")
		->setDescription("Data Keuangan")
		->setKeywords("keuangan")
		->setCategory("Data File");
			
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('Rincian Biaya Mahasiswa');
			
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
		$double = array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE)));
		$cell_color = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb'=>'CCCCCC')));
		// properties field excel;
		$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:H2');
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setSize(14);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true);
		// column width
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$objPHPExcel->getActiveSheet()->mergeCells('A3:B3');
		$objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
		$objPHPExcel->getActiveSheet()->mergeCells('A5:B5');
		$objPHPExcel->getActiveSheet()->mergeCells('A6:B6');
		$objPHPExcel->getActiveSheet()->mergeCells('A7:B7');
		$objPHPExcel->getActiveSheet()->mergeCells('A8:B8');
		$objPHPExcel->getActiveSheet()->setCellValue('A1',strtoupper($nm_pt));
		$objPHPExcel->getActiveSheet()->setCellValue('A2','RINCIAN BIAYA PERIODE '.$per);
		$objPHPExcel->getActiveSheet()->getStyle('A2:H2')->applyFromArray($double);
		$objPHPExcel->getActiveSheet()->setCellValue('A4','NAMA (NIM)');
		$objPHPExcel->getActiveSheet()->setCellValue('A5','PROGRAM STUDI');
		$objPHPExcel->getActiveSheet()->setCellValue('A6','ANGKATAN');
		$objPHPExcel->getActiveSheet()->setCellValue('A7','STATUS MASUK');
		$objPHPExcel->getActiveSheet()->setCellValue('A8','STATUS REGISTRASi');
		$objPHPExcel->getActiveSheet()->getStyle('A3:C8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('A3:C8')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('A3:A8')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->mergeCells('C3:H3');
		$objPHPExcel->getActiveSheet()->mergeCells('C4:H4');
		$objPHPExcel->getActiveSheet()->mergeCells('C5:H5');
		$objPHPExcel->getActiveSheet()->mergeCells('C6:H6');
		$objPHPExcel->getActiveSheet()->mergeCells('C7:H7');
		$objPHPExcel->getActiveSheet()->mergeCells('C8:H8');
		$objPHPExcel->getActiveSheet()->setCellValue('C4',':'.$nm_mhs.' ('.$nim.')');
		$objPHPExcel->getActiveSheet()->setCellValue('C5',':'.$nm_prd);
		$objPHPExcel->getActiveSheet()->setCellValue('C6',':'.$akt);
		$objPHPExcel->getActiveSheet()->setCellValue('C7',':'.$stat_msk);
		$objPHPExcel->getActiveSheet()->setCellValue('C8',':'.$stat_reg);
		$objPHPExcel->getActiveSheet()->setCellValue('A10','No');
		$objPHPExcel->getActiveSheet()->setCellValue('B10','Komponen Biaya');
		$objPHPExcel->getActiveSheet()->setCellValue('C10','Nominal Biaya');
		$objPHPExcel->getActiveSheet()->setCellValue('D10','Formulasi Biaya');
		$objPHPExcel->getActiveSheet()->setCellValue('E10','Nominal Komponen');
		$objPHPExcel->getActiveSheet()->setCellValue('F10','Nominal Kompensasi');
		$objPHPExcel->getActiveSheet()->setCellValue('G10','Terbayar');
		$objPHPExcel->getActiveSheet()->setCellValue('H10','Tunggakan');
		$objPHPExcel->getActiveSheet()->getStyle('A10:H10')->applyFromArray($border);
		$objPHPExcel->getActiveSheet()->getStyle('A10:H10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A10:H10')->getFont()->setBold(true);
		$x=1;
		$i=11;
		$totBiaya=0;
		$totKomp=0;
		$totBayar=0;
		$n=1;
		foreach ($getMhsBiaya as $dtBiaya) {
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$dtBiaya['nm_komp']);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$dtBiaya['nominal_formula']);
			$rule="";
			if($dtBiaya['id_rule']=='1'){
				$rule=number_format($dtBiaya['hard_nominal'],2,',','.');
			}elseif ($dtBiaya['id_rule']=='2'){
				$rule=$dtBiaya['nm_param'];
			}elseif ($dtBiaya['id_rule']=='3'){
				$rule=$dtBiaya['bil_pengali'];
			}
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$dtBiaya['nm_rule']." : ".$rule);
			$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$dtBiaya['nominal_komp']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$dtBiaya['nominal_kompensasi']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$dtBiaya['nominal_alocate']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$dtBiaya['nominal_komp']-$dtBiaya['nominal_kompensasi']-$dtBiaya['nominal_alocate']);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getNumberFormat() ->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':H'.$i)->getNumberFormat() ->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.":H".$i)->applyFromArray($border);
			$i++;
			$n++;
			$totBiaya=$totBiaya+$dtBiaya['nominal_komp'];
			$totKomp=$totKomp+$dtBiaya['nominal_kompensasi'];
			$totBayar=$totBayar+$dtBiaya['nominal_alocate'];
		}
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'Total');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':D'.$i);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$totBiaya);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$totKomp);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$totBayar);
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$totBiaya-$totKomp-$totBayar);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getNumberFormat() ->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':H'.$i)->getNumberFormat() ->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.":H".$i)->applyFromArray($border);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.":H".$i)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->mergeCells('F'.($i+3).':G'.($i+3));
		$objPHPExcel->getActiveSheet()->getStyle('F'.($i+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+3),'Bag.Keuangan Mahasiswa');
		$objPHPExcel->getActiveSheet()->getStyle('F'.($i+3))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('F'.($i+7).":G".($i+7))->applyFromArray($double);
		// lock
		$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Rincian Biaya.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
}