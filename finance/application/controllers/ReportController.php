<?php 
	// bismillah
	/*
	 Programmer	: Tiar Aristian
	 Release	: September 2016
	 Module		: Report Controller -> Controller untuk modul report
	 */
class ReportController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('PaketBiaya');
		Zend_Loader::loadClass('Biaya');
		Zend_Loader::loadClass('FormulaBiaya');
		Zend_Loader::loadClass('FormulaBiayaTA');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('MhsGelombang');
		Zend_Loader::loadClass('MhsRegPeriode');
		Zend_Loader::loadClass('MhsBiayaPeriode');
		Zend_Loader::loadClass('Sumbangan');
		Zend_Loader::loadClass('Bayar');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('PHPExcel');
		Zend_Loader::loadClass('PHPExcel_Cell_AdvancedValueBinder');
		Zend_Loader::loadClass('PHPExcel_IOFactory');
		$auth = Zend_Auth::getInstance();
		$ses_fin = new Zend_Session_Namespace('ses_fin');
		if (($auth->hasIdentity())and($ses_fin->uname)) {
			$this->view->namauser =Zend_Auth::getInstance()->getIdentity()->nama;
			$this->view->kd_pt=$ses_fin->kd_pt;
			$this->view->nm_pt=$ses_fin->nm_pt;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// menu nav
		$this->view->act_rep="active open";
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
	
	function indexAction(){
		// Title Browser
		$this->view->title = "Laporan Biaya Periodik Mahasiswa";
		// navigation
		$this->_helper->navbar(0,0,0,0,0);
		// angkatan
		$akt=new Angkatan();
		$this->view->listAkt=$akt->fetchAll();
		// prodi
		$prodi = new Prodi();
		$this->view->listProdi = $prodi->fetchAll();
		// periode akademik
		$periode = new Periode();
		$this->view->listPeriode=$periode->fetchAll();
		// menu nav
		$this->view->act_rep1="active";
	}
	
	function showAction(){
		// menu nav
		$this->view->act_rep1="active";
		// get param
		$param = new Zend_Session_Namespace('param_report1');
		$prd=$param->prd;
		$akt=$param->akt;
		$per=$param->per;
		if(!$per){
			$this->view->eksis="f";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
		}else{
			// navigation
			$this->_helper->navbar('report',0,0,'report/excel',0);
			// title
			$this->view->title="Laporan Kewajiban Periodik Mahasiswa ".$per;
			// get data mhs reg
			$mhsReg=new MhsRegPeriode();
			$getMhsReg=$mhsReg->getMhsRegPeriodeByAktProdiPeriode($akt, $prd, $per);
			$this->view->listMhsReg=$getMhsReg;
			// get biaya mahasiswa
			$mhsBiaya=new MhsBiayaPeriode();
			$getMhsBiaya=$mhsBiaya->getMhsBiayaPeriodeDetilByAktProdiPeriode($akt, $prd, $per);
			$this->view->listMhsBiayaPer=$getMhsBiaya;
			// komponen biaya
			$arrKomp=array();
			$i=0;
			foreach ($getMhsBiaya as $dtMhsBiaya){
				if($dtMhsBiaya['id_komp']!=''){
					$arrKomp[$i]=$dtMhsBiaya['id_komp']."||".$dtMhsBiaya['nm_komp'];
					$i++;
				}
			}
			$this->view->listKompBiaya=array_values(array_unique($arrKomp));
			// sumbangan
			$sumbangan=new Sumbangan();
			$getSumbangan=$sumbangan->getSumbanganDtlByAktProdi($akt, $prd);
			$this->view->listSumb=$getSumbangan;
			$arrKompSumb=array();
			$i=0;
			foreach ($getSumbangan as $dtSumb){
				if($dtSumb['kd_periode']==$per){
					if($dtSumb['id_komp']!=''){
						$arrKompSumb[$i]=$dtSumb['id_komp']."||".$dtSumb['nm_komp'];
						$i++;
					}
				}
			}
			$this->view->listKompSumb=array_values(array_unique($arrKompSumb));
			$this->view->nSumb=count(array_values(array_unique($arrKompSumb)));
			// create session for export
			$param = new Zend_Session_Namespace('param_erep1');
			$param->dtMhsReg=$getMhsReg;
			$param->dtBiayaPer=$getMhsBiaya;
			$param->dtKomp=array_values(array_unique($arrKomp));
			$param->dtSumb=$getSumbangan;
			$param->dtKompSumb=array_values(array_unique($arrKompSumb));
			$param->per=$per;
		}
	}
	
	function excelAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// session data
		$param = new Zend_Session_Namespace('param_erep1');
		$mhsReg=$param->dtMhsReg;
		$biayaPer=$param->dtBiayaPer;
		$komp=$param->dtKomp;
		$sumb=$param->dtSumb;
		$kompSumb=$param->dtKompSumb;
		$per=$param->per;
		$ses_fin = new Zend_Session_Namespace('ses_fin');
		$nm_pt=$ses_fin->nm_pt;
		// konfigurasi excel
		PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Administrator")
		->setLastModifiedBy("Keuangan")
		->setTitle("Export Laporan Periodik")
		->setSubject("Sistem Informasi Keuangan")
		->setDescription("Data Keuangan")
		->setKeywords("keuangan")
		->setCategory("Data File");
			
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('Laporan Biaya Periodik');
			
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
		$arrHuruf=array("","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE");
		// properties field excel;
		$objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:D2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:D3');
		$objPHPExcel->getActiveSheet()->getStyle('A1:D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('A1:D3')->getFont()->setSize(14);
		$objPHPExcel->getActiveSheet()->getStyle('A1:D3')->getFont()->setBold(true);
		// column width
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(28);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(28);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(9);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(9);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(9);
		$objPHPExcel->getActiveSheet()->mergeCells('A4:A6');
		$objPHPExcel->getActiveSheet()->mergeCells('B4:B6');
		$objPHPExcel->getActiveSheet()->mergeCells('C4:C6');
		$objPHPExcel->getActiveSheet()->mergeCells('D4:D6');
		$objPHPExcel->getActiveSheet()->mergeCells('E4:E6');
		$objPHPExcel->getActiveSheet()->mergeCells('F4:H4');
		$objPHPExcel->getActiveSheet()->mergeCells('F5:F6');
		$objPHPExcel->getActiveSheet()->mergeCells('G5:G6');
		$objPHPExcel->getActiveSheet()->mergeCells('H5:H6');
		$objPHPExcel->getActiveSheet()->setCellValue('A1',strtoupper($nm_pt));
		$objPHPExcel->getActiveSheet()->setCellValue('A2','LAPORAN KEWAJIBAN PERIODIK MAHASISWA');
		$objPHPExcel->getActiveSheet()->setCellValue('A3','PERIODE '.$per);
		$objPHPExcel->getActiveSheet()->setCellValue('A4','NO');
		$objPHPExcel->getActiveSheet()->setCellValue('B4','NIM');
		$objPHPExcel->getActiveSheet()->setCellValue('C4','NAMA');
		$objPHPExcel->getActiveSheet()->setCellValue('D4','ANGKATAN/PRODI');
		$objPHPExcel->getActiveSheet()->setCellValue('E4','STATUS REGISTRASI');
		$objPHPExcel->getActiveSheet()->setCellValue('F4','SKS/JUMLAH MK');
		$objPHPExcel->getActiveSheet()->setCellValue('F5','TEORI');
		$objPHPExcel->getActiveSheet()->setCellValue('G5','PRAK');
		$objPHPExcel->getActiveSheet()->setCellValue('H5','TA');
		$objPHPExcel->getActiveSheet()->getStyle('A4:H6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H6')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H6')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H6')->applyFromArray($border);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H6')->getAlignment()->setWrapText(true);
		// komponen
		$colH=1;
		$nCol=count($komp)*2;
		$objPHPExcel->getActiveSheet()->setCellValue('I4',"Komponen Biaya Semester ini");
		if(count($komp)>0){
			$objPHPExcel->getActiveSheet()->getStyle('I4:'.$arrHuruf[$nCol]."4")->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('I4:'.$arrHuruf[$nCol]."4")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->mergeCells('I4:'.$arrHuruf[$nCol]."4");
			foreach ($komp as $dtKomp){
				$arrKomp=explode("||", $dtKomp);
				$id_komp=$arrKomp[0];
				$nm_komp=$arrKomp[1];
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colH]."5",$nm_komp);
				$objPHPExcel->getActiveSheet()->mergeCells($arrHuruf[$colH]."5:".$arrHuruf[$colH+1]."5");
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colH]."6","Kewajiban");
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colH+1]."6","Bayar");
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."5:".$arrHuruf[$colH+1]."6")->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."5:".$arrHuruf[$colH+1]."6")->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$colH])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$colH+1])->setWidth(20);
				$colH=$colH+2;
			}
		}
		$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colH]."4","Total Biaya Semester Ini");
		$objPHPExcel->getActiveSheet()->mergeCells($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6");
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->applyFromArray($border);
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$colH])->setWidth(20);
		$colH++;
		$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colH]."4","Kompensasi");
		$objPHPExcel->getActiveSheet()->mergeCells($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6");
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->applyFromArray($border);
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$colH])->setWidth(20);
		$colH++;
		$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colH]."4","Pembayaran Semester Ini");
		$objPHPExcel->getActiveSheet()->mergeCells($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6");
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->applyFromArray($border);
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$colH])->setWidth(20);
		$colH++;
		$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colH]."4","Tunggakan Semester Ini");
		$objPHPExcel->getActiveSheet()->mergeCells($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6");
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->applyFromArray($border);
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$colH])->setWidth(20);
		$colH++;
		$nCol=count($kompSumb)*2;
		if(count($kompSumb)>0){
			$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colH]."4","Komponen Biaya Sumbangan");
			$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$colH])->setWidth(20);
			$objPHPExcel->getActiveSheet()->mergeCells($arrHuruf[$colH]."4:".$arrHuruf[$colH-1+$nCol]."4");
			$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH-1+$nCol]."4")->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH-1+$nCol]."4")->getAlignment()->setWrapText(true);
			foreach ($kompSumb as $dtKompSumb){
				$arrKompSumb=explode("||", $dtKompSumb);
				$id_komp_sumb=$arrKompSumb[0];
				$nm_komp_sumb=$arrKompSumb[1];
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colH]."5",$nm_komp_sumb);
				$objPHPExcel->getActiveSheet()->mergeCells($arrHuruf[$colH]."5:".$arrHuruf[$colH+1]."5");
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colH]."6","Kewajiban");
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colH+1]."6","Bayar");
				$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$colH])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$colH+1])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."5:".$arrHuruf[$colH+1]."6")->applyFromArray($border);
				$colH=$colH+2;
			}
		}
		$objPHPExcel->getActiveSheet()->getStyle('I4:'.$arrHuruf[$colH]."6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('I4:'.$arrHuruf[$colH]."6")->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('I4:'.$arrHuruf[$colH]."6")->getFont()->setBold(true);
		// data isi
		$x=1;
		$i=7;
		foreach ($mhsReg as $dtMhsReg){
			$objPHPExcel->getActiveSheet()->setCellValue("A".$i,$x);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$i,$dtMhsReg['nim']);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$i,$dtMhsReg['nm_mhs']);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$i,$dtMhsReg['nm_prodi']."/".$dtMhsReg['id_angkatan']);
			$objPHPExcel->getActiveSheet()->setCellValue("E".$i,$dtMhsReg['status_reg']."(".$dtMhsReg['status_mhs_periode'].")");
			$objPHPExcel->getActiveSheet()->setCellValue("F".$i,$dtMhsReg['sks_teori']."/".$dtMhsReg['n_teori']);
			$objPHPExcel->getActiveSheet()->setCellValue("G".$i,$dtMhsReg['sks_prak']."/".$dtMhsReg['n_prak']);
			$objPHPExcel->getActiveSheet()->setCellValue("H".$i,$dtMhsReg['sks_ta']."/".$dtMhsReg['n_ta']);
			$objPHPExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("D".$i.":H".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("A".$i.":H".$i)->applyFromArray($border);
			$totNomKomp=0;
			$totNomAlocate=0;
			$totNomKompen=0;
			$colD=1;
			if(count($komp)>0){
				foreach ($komp as $dtKomp){
					$nomKomp=0;
					$nomKompen=0;
					$nomAlocate=0;
					$arrKomp=explode("||", $dtKomp);
					$id_komp=$arrKomp[0];
					foreach ($biayaPer as $dtMhsBiaya){
						if(($dtMhsBiaya['nim']==$dtMhsReg['nim'])and($dtMhsBiaya['id_komp']==$id_komp)){
							$nomKomp=$dtMhsBiaya['nominal_komp'];
							$nomAlocate=$dtMhsBiaya['nominal_alocate'];
							$totNomKomp=$totNomKomp+$dtMhsBiaya['nominal_komp'];
							$totNomAlocate=$totNomAlocate+$dtMhsBiaya['nominal_alocate'];
							$totNomKompen=$totNomKompen+$dtMhsBiaya['nominal_kompensasi'];
						}
					}
					$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colD].$i,$nomKomp);
					$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colD+1].$i,$nomAlocate);
					$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i.":".$arrHuruf[$colD+1].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i.":".$arrHuruf[$colD+1].$i)->getNumberFormat() ->setFormatCode('#,##0.00');
					$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i.":".$arrHuruf[$colD+1].$i)->applyFromArray($border);
					$colD=$colD+2;
				}
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colD].$i,$totNomKomp);
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->getNumberFormat() ->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->applyFromArray($border);
				$colD++;
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colD].$i,$totNomKompen);
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->getNumberFormat() ->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->applyFromArray($border);
				$colD++;
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colD].$i,$totNomAlocate);
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->getNumberFormat() ->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->applyFromArray($border);
				$colD++;
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colD].$i,($totNomKomp-$totNomKompen-$totNomAlocate));
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->getNumberFormat() ->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->applyFromArray($border);
				$colD++;
			}
			// sumbangan
			if(count($kompSumb)>0){
				foreach ($kompSumb as $dtKompSumb){
					$arrKompSumb=explode("||", $dtKompSumb);
					$id_komp_sumb=$arrKompSumb[0];
					$nomKompSumb=0;
					$nomAlocateSumb=0;
					foreach ($sumb as $dtMhsSumb){
						if(($dtMhsSumb['nim']==$dtMhsReg['nim'])and($dtMhsSumb['id_komp']==$id_komp_sumb)){
							$nomKompSumb=$dtMhsSumb['nominal'];
							$nomAlocateSumb=$dtMhsSumb['tot_bayar'];
						}
					}
					$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colD].$i,$nomKompSumb);
					$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colD+1].$i,$nomAlocateSumb);
					$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i.":".$arrHuruf[$colD+1].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i.":".$arrHuruf[$colD+1].$i)->getNumberFormat() ->setFormatCode('#,##0.00');
					$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i.":".$arrHuruf[$colD+1].$i)->applyFromArray($border);
					$colD=$colD+2;
				}
			}
			$i++;
			$x++;
		}
		// sum
		$objPHPExcel->getActiveSheet()->setCellValue("A".$i,"JUMLAH");
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.":H".$i)->applyFromArray($border);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.":H".$i);
		for($y=1;$y<$colD;$y++){
			$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$y].$i,"=SUM(".$arrHuruf[$y]."7:".$arrHuruf[$y].($i-1).")");
			$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$y].$i)->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$y].$i)->getNumberFormat() ->setFormatCode('#,##0.00');
		}
				
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Laporan Kewajiban Periodik.xls"');
		header('Cache-Control: max-age=0');
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	
	function index2Action(){
		// Title Browser
		$this->view->title = "Laporan Biaya TA";
		// navigation
		$this->_helper->navbar(0,0,0,0,0);
		// angkatan
		$akt=new Angkatan();
		$this->view->listAkt=$akt->fetchAll();
		// prodi
		$prodi = new Prodi();
		$this->view->listProdi = $prodi->fetchAll();
		// periode akademik
		$periode = new Periode();
		$this->view->listPeriode=$periode->fetchAll();
		// menu nav
		$this->view->act_rep2="active";
	}
	
	function show2Action(){
		// menu nav
		$this->view->act_rep2="active";
		// get param
		$param = new Zend_Session_Namespace('param_report2');
		$prd=$param->prd;
		$akt=$param->akt;
		$per=$param->per;
		if(!$per){
			$this->view->eksis="f";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
		}else{
			// navigation
			$this->_helper->navbar('report',0,0,'report/excel2',0);
			// title
			$this->view->title="Laporan Kewajiban TA Mahasiswa ".$per;
			// get data mhs reg
			$mhsReg=new MhsRegPeriode();
			$getMhsReg=$mhsReg->getMhsRegPeriodeByAktProdiPeriode($akt, $prd, $per);
			$arrKomp=array();
			$arrFormulaIntval=array();
			$arrMhsReg=array();
			$arrKompIntv=array();
			$n=0;
			$formula=new FormulaBiayaTA();
			foreach ($getMhsReg as $dtMhsReg){
				$nim=$dtMhsReg['nim'];
				// get data formula interval
				$getFormulaTA=$formula->getFormulaBiayaTAByAktProdi($dtMhsReg['id_angkatan'], $dtMhsReg['kd_prodi']);
				// get periode per mhs
				$getMhsRegNim=$mhsReg->getMhsRegPeriodeByNim($nim);
				$i=0;
				foreach ($getFormulaTA as $dtFormTA) {
					$arrKomp[$nim][$i]=$dtFormTA['id_komp'];
					$i++;
				}
				$arrKomp[$nim]=array_unique($arrKomp[$nim]);
				foreach ($arrKomp[$nim] as $dtKomp) {
					$nFlag[$nim][$dtKomp]=0;
					$perFlag[$nim][$dtKomp]="-";
					$intvFlag[$nim][$dtKomp]=1;
				}
				// last periode registrasi
				$last_per[$nim]="";
				foreach ($getMhsRegNim as $dataPeriode){
					if($dataPeriode['kd_periode']>$last_per[$nim]){
						$last_per[$nim]=$dataPeriode['kd_periode'];
					}
				}
				// maping biaya
				$i=0;
				foreach ($getMhsRegNim as $dtReg){
					if($dtReg['nim']==$nim){
						foreach ($arrKomp[$nim] as $dtKomp) {
							$getFormulaPeriode=$formula->getFormulaBiayaTAByPeriode($dtMhsReg['id_angkatan'], $dtMhsReg['kd_prodi'], $dtKomp, $last_per[$nim]);
							foreach ($getFormulaPeriode as $dtFormulaPeriode){
								if((($dtFormulaPeriode['id_param']=='003')and($dtReg['sks_ta']>$dtFormulaPeriode['min_value']))or(($dtFormulaPeriode['id_param']=='103')and($dtReg['n_ta']>$dtFormulaPeriode['min_value']))){
									if($perFlag[$nim][$dtKomp]=="-"){ // awal
										$perFlag[$nim][$dtKomp]=$dtReg['kd_periode'];
										$intvFlag[$nim][$dtKomp]=$dtFormulaPeriode['intval_perbaruan'];
										// set array biaya
										$arrKompIntv[$n]=$dtFormulaPeriode['id_komp']."||".$dtFormulaPeriode['nm_komp'];
										$arrMhsReg[$n]=$dtReg['nim']."||".$dtReg['nm_mhs']."||".$dtReg['id_angkatan']."||".$dtReg['nm_prodi'];
										$arrFormulaIntval[$n]['nim']=$dtReg['nim'];
										$arrFormulaIntval[$n]['kd_periode']=$dtReg['kd_periode'];
										$arrFormulaIntval[$n]['id_komp']=$dtFormulaPeriode['id_komp'];
										$arrFormulaIntval[$n]['nm_komp']=$dtFormulaPeriode['nm_komp'];
										$arrFormulaIntval[$n]['nominal']=$dtFormulaPeriode['nominal'];
										$arrFormulaIntval[$n]['nm_paket']=$dtFormulaPeriode['nm_paket'];
										$arrFormulaIntval[$n]['nm_param']=$dtFormulaPeriode['nm_param'];
										$arrFormulaIntval[$n]['min_value']=$dtFormulaPeriode['min_value'];
										$arrFormulaIntval[$n]['kd_periode_berlaku']=$dtFormulaPeriode['kd_periode_berlaku'];
										$n++;
									}else{
										$interval=$this->intervalSemester($perFlag[$nim][$dtKomp], $dtReg['kd_periode']);
										$x=($interval%$intvFlag[$nim][$dtKomp]);
										if ($interval%$intvFlag[$nim][$dtKomp]==0){
											// ganti flag
											$perFlag[$nim][$dtKomp]=$dtReg['kd_periode'];
											$intvFlag[$nim][$dtKomp]=$dtFormulaPeriode['intval_perbaruan'];
											// set array biaya
											$arrKompIntv[$n]=$dtFormulaPeriode['id_komp']."||".$dtFormulaPeriode['nm_komp'];
											$arrMhsReg[$n]=$dtReg['nim']."||".$dtReg['nm_mhs']."||".$dtReg['id_angkatan']."||".$dtReg['nm_prodi'];
											$arrFormulaIntval[$n]['nim']=$dtReg['nim'];
											$arrFormulaIntval[$n]['kd_periode']=$dtReg['kd_periode'];
											$arrFormulaIntval[$n]['id_komp']=$dtFormulaPeriode['id_komp'];
											$arrFormulaIntval[$n]['nm_komp']=$dtFormulaPeriode['nm_komp'];
											$arrFormulaIntval[$n]['nominal']=$dtFormulaPeriode['nominal'];
											$arrFormulaIntval[$n]['nm_paket']=$dtFormulaPeriode['nm_paket'];
											$arrFormulaIntval[$n]['nm_param']=$dtFormulaPeriode['nm_param'];
											$arrFormulaIntval[$n]['min_value']=$dtFormulaPeriode['min_value'];
											$arrFormulaIntval[$n]['kd_periode_berlaku']=$dtFormulaPeriode['kd_periode_berlaku'];
											$n++;
										}
									}
									$nFlag[$nim][$dtKomp]=$nFlag[$nim][$dtKomp]+1;
								}
							}
						}
					}
				}
			}
			$this->view->listBiayaInterval=$arrFormulaIntval;
			$this->view->listMhsReg=array_values(array_unique($arrMhsReg));
			$this->view->listKompInterval=array_values(array_unique($arrKompIntv));
			// get bayar
			$bayar=new Bayar();
			$getBayar=$bayar->getBayarDtlByPeriodeTerm($per, "3");
			$this->view->listBayar=$getBayar;
			// create session for export
			$param = new Zend_Session_Namespace('param_erep2');
			$param->dtMhsReg=array_values(array_unique($arrMhsReg));
			$param->dtBiayaInt=$arrFormulaIntval;
			$param->dtKomp=array_values(array_unique($arrKompIntv));
			$param->bayar=$getBayar;
			$param->per=$per;
		}
	}
	
	function excel2Action(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// session data
		$param = new Zend_Session_Namespace('param_erep2');
		$mhsReg=$param->dtMhsReg;
		$biayaInt=$param->dtBiayaInt;
		$komp=$param->dtKomp;
		$bayar=$param->bayar;
		$per=$param->per;
		$ses_fin = new Zend_Session_Namespace('ses_fin');
		$nm_pt=$ses_fin->nm_pt;
		// konfigurasi excel
		PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Administrator")
		->setLastModifiedBy("Keuangan")
		->setTitle("Export Laporan Periodik")
		->setSubject("Sistem Informasi Keuangan")
		->setDescription("Data Keuangan")
		->setKeywords("keuangan")
		->setCategory("Data File");
			
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('Laporan Biaya TA');
			
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
		$arrHuruf=array("","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X");
		// properties field excel;
		$objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:D2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:D3');
		$objPHPExcel->getActiveSheet()->getStyle('A1:D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('A1:D3')->getFont()->setSize(14);
		$objPHPExcel->getActiveSheet()->getStyle('A1:D3')->getFont()->setBold(true);
		// column width
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(28);
		$objPHPExcel->getActiveSheet()->mergeCells('A4:A6');
		$objPHPExcel->getActiveSheet()->mergeCells('B4:B6');
		$objPHPExcel->getActiveSheet()->mergeCells('C4:C6');
		$objPHPExcel->getActiveSheet()->mergeCells('D4:D6');
		$objPHPExcel->getActiveSheet()->setCellValue('A1',strtoupper($nm_pt));
		$objPHPExcel->getActiveSheet()->setCellValue('A2','LAPORAN KEWAJIBAN TA MAHASISWA');
		$objPHPExcel->getActiveSheet()->setCellValue('A3','PERIODE '.$per);
		$objPHPExcel->getActiveSheet()->setCellValue('A4','NO');
		$objPHPExcel->getActiveSheet()->setCellValue('B4','NIM');
		$objPHPExcel->getActiveSheet()->setCellValue('C4','NAMA');
		$objPHPExcel->getActiveSheet()->setCellValue('D4','ANGKATAN/PRODI');
		$objPHPExcel->getActiveSheet()->getStyle('A4:D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:D6')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('A4:D6')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A4:D6')->applyFromArray($border);
		$objPHPExcel->getActiveSheet()->getStyle('A4:D6')->getAlignment()->setWrapText(true);
		// komponen
		$colH=1;
		$nCol=count($komp)*2;
		$objPHPExcel->getActiveSheet()->setCellValue('E4',"Komponen Biaya");
		$objPHPExcel->getActiveSheet()->getStyle('E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
		if(count($komp)>0){
			$objPHPExcel->getActiveSheet()->getStyle('E4:'.$arrHuruf[$nCol]."4")->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('E4:'.$arrHuruf[$nCol]."4")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->mergeCells('E4:'.$arrHuruf[$nCol]."4");
			$objPHPExcel->getActiveSheet()->getStyle('E4:'.$arrHuruf[$nCol]."4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E4:'.$arrHuruf[$nCol]."4")->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('E4:'.$arrHuruf[$nCol]."4")->getFont()->setBold(true);
			foreach ($komp as $dtKomp){
				$arrKomp=explode("||", $dtKomp);
				$id_komp=$arrKomp[0];
				$nm_komp=$arrKomp[1];
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colH]."5",$nm_komp);
				$objPHPExcel->getActiveSheet()->mergeCells($arrHuruf[$colH]."5:".$arrHuruf[$colH+1]."5");
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colH]."6","Kewajiban");
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colH+1]."6","Bayar");
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."5:".$arrHuruf[$colH+1]."6")->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."5:".$arrHuruf[$colH+1]."6")->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."5:".$arrHuruf[$colH+1]."6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."5:".$arrHuruf[$colH+1]."6")->getFont()->setSize(12);
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."5:".$arrHuruf[$colH+1]."6")->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$colH])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$colH+1])->setWidth(20);
				$colH=$colH+2;
			}
		}
		$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colH]."4","Total Biaya");
		$objPHPExcel->getActiveSheet()->mergeCells($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6");
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->applyFromArray($border);
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$colH])->setWidth(20);
		$colH++;
		$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colH]."4","Pembayaran");
		$objPHPExcel->getActiveSheet()->mergeCells($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6");
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->applyFromArray($border);
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$colH])->setWidth(20);
		$colH++;
		$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colH]."4","Tunggakan");
		$objPHPExcel->getActiveSheet()->mergeCells($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6");
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->applyFromArray($border);
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."4:".$arrHuruf[$colH]."6")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$colH])->setWidth(20);
		$colH++;
		// data isi
		$x=1;
		$i=7;
		foreach ($mhsReg as $dtMhsReg){
			$arrDtMhsReg=explode("||", $dtMhsReg);
			$nim=$arrDtMhsReg[0];
			$nm=$arrDtMhsReg[1];
			$akt=$arrDtMhsReg[2];
			$prd=$arrDtMhsReg[3];
			$objPHPExcel->getActiveSheet()->setCellValue("A".$i,$x);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$i,$nim);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$i,$nm);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$i,$prd."/".$akt);
			$objPHPExcel->getActiveSheet()->getStyle("A".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("D".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle("A".$i.":D".$i)->applyFromArray($border);
			$nomKomp=0;
			$totNomKomp=0;
			$totNomAlocate=0;
			$colD=1;
			if(count($komp)>0){
				foreach ($komp as $dtKomp){
					$arrKomp=explode("||", $dtKomp);
					$id_komp=$arrKomp[0];
					$nm_komp=$arrKomp[1];
					foreach ($biayaInt as $dtMhsBiaya){
						if(($dtMhsBiaya['nim']==$nim)and($dtMhsBiaya['id_komp']==$id_komp)){
							$nomKomp=$dtMhsBiaya['nominal'];
							$totNomKomp=$totNomKomp+$dtMhsBiaya['nominal'];
						}
					}
					$nomAlocate[$id_komp]=0;
					foreach ($bayar as $dtBayar) {
						if(($dtBayar['nim']==$nim)and($dtBayar['id_komp_alocate']==$id_komp)){;
							$nomAlocate[$id_komp]=$nomAlocate[$id_komp]+$dtBayar['nominal_alocate'];
							$totNomAlocate=$totNomAlocate+$nomAlocate[$id_komp];
						}
					}
					$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colD].$i,$nomKomp);
					$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colD+1].$i,$nomAlocate[$id_komp]);
					$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i.":".$arrHuruf[$colD+1].($i+1))->applyFromArray($border);
					$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i.":".$arrHuruf[$colD+1].$i)->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i.":".$arrHuruf[$colD+1].$i)->getNumberFormat() ->setFormatCode('#,##0.00');
					$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$colD])->setWidth(20);
					$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$colD+1])->setWidth(20);
					$colD=$colD+2;
				}
			}	
			$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colD].$i,$totNomKomp);
			$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->getNumberFormat() ->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->applyFromArray($border);
			$colD++;
			$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colD].$i,$totNomAlocate);
			$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->getNumberFormat() ->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->applyFromArray($border);
			$colD++;
			$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colD].$i,$totNomKomp-$totNomAlocate);
			$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->getNumberFormat() ->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->applyFromArray($border);
			$colD++;
			$i++;
			$x++;
		}
		// sum
		$objPHPExcel->getActiveSheet()->setCellValue("A".$i,"JUMLAH");
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.":D".$i)->applyFromArray($border);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.":D".$i);
		for($y=1;$y<$colD;$y++){
			$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$y].$i,"=SUM(".$arrHuruf[$y]."7:".$arrHuruf[$y].($i-1).")");
			$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$y].$i)->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$y].$i)->getNumberFormat() ->setFormatCode('#,##0.00');
		}
		
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Laporan Kewajiban TA.xls"');
		header('Cache-Control: max-age=0');
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	
	function index5Action(){
		// Title Browser
		$this->view->title = "Laporan Pembayaran Mahasiswa";
		// navigation
		$this->_helper->navbar(0,0,0,0,0);
		// angkatan
		$akt=new Angkatan();
		$this->view->listAkt=$akt->fetchAll();
		// prodi
		$prodi = new Prodi();
		$this->view->listProdi = $prodi->fetchAll();
		// menu nav
		$this->view->act_rep5="active";
	}
	
	function show5Action(){
		// menu nav
		$this->view->act_rep5="active";
		// Title Browser
		$this->view->title = "Laporan Pembayaran Mahasiswa ";
		// navigation
		$this->_helper->navbar('report/index5',0,0,'report/excel5',0);
		// get param
		$param = new Zend_Session_Namespace('param_report5');
		$prd=$param->prd;
		$akt=$param->akt;
		$tgl1=$param->tgl1;
		$tgl2=$param->tgl2;
		// get bayar
		$bayar=new Bayar();
		$getBayar=$bayar->getBayarDtlByAktProdiTgl($akt, $prd, $tgl1, $tgl2);
		$this->view->listBayar=$getBayar;
		// hdr
		$arrBayarHdr=array();
		$arrKomp=array();
		$i=0;
		foreach ($getBayar as $dtBayar){
			$arrBayarHdr[$i]=$dtBayar['no_trans']."||".$dtBayar['tgl_bayar_fmt']."||".$dtBayar['nim']."||".$dtBayar['nm_mhs']."||".$dtBayar['id_angkatan']."||".$dtBayar['nm_prodi']."||".$dtBayar['via']." ".$dtBayar['nm_bank']."/".$dtBayar['no_bukti'];
			if($dtBayar['id_komp_alocate']!=''){
				$arrKomp[$i]=$dtBayar['id_komp_alocate']."||".$dtBayar['nm_komp_alocate'];
			}
			$i++;
		}
		$this->view->listByrHdr=array_values(array_unique($arrBayarHdr));
		$this->view->listKomp=array_values(array_unique($arrKomp));
		// create session for export
		$param = new Zend_Session_Namespace('param_erep5');
		$param->dtByrHdr=array_values(array_unique($arrBayarHdr));
		$param->dtKomp=array_values(array_unique($arrKomp));
		$param->dtBayar=$getBayar;
		$param->tgl1=$tgl1;
		$param->tgl2=$tgl2;
	}
	
	function excel5Action(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// session data
		$param = new Zend_Session_Namespace('param_erep5');
		$byrHdr=$param->dtByrHdr;
		$komp=$param->dtKomp;
		$byrDtl=$param->dtBayar;
		$tgl1=$param->tgl1;
		$tgl2=$param->tgl2;
		$ses_fin = new Zend_Session_Namespace('ses_fin');
		$nm_pt=$ses_fin->nm_pt;
		// konfigurasi excel
		PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Administrator")
		->setLastModifiedBy("Keuangan")
		->setTitle("Export Laporan Pembayaran")
		->setSubject("Sistem Informasi Keuangan")
		->setDescription("Data Keuangan")
		->setKeywords("keuangan")
		->setCategory("Data File");
			
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('Laporan Pembayaran Mahasiswa');
			
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
		$arrHuruf=array("","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE");
		// properties field excel;
		$objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:D2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:D3');
		$objPHPExcel->getActiveSheet()->getStyle('A1:D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('A1:D3')->getFont()->setSize(14);
		$objPHPExcel->getActiveSheet()->getStyle('A1:D3')->getFont()->setBold(true);
		// column width
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(28);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(28);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
		$objPHPExcel->getActiveSheet()->mergeCells('A4:A6');
		$objPHPExcel->getActiveSheet()->mergeCells('B4:B6');
		$objPHPExcel->getActiveSheet()->mergeCells('C4:C6');
		$objPHPExcel->getActiveSheet()->mergeCells('D4:D6');
		$objPHPExcel->getActiveSheet()->mergeCells('E4:E6');
		$objPHPExcel->getActiveSheet()->mergeCells('F4:F6');
		$objPHPExcel->getActiveSheet()->setCellValue('A1',strtoupper($nm_pt));
		$objPHPExcel->getActiveSheet()->setCellValue('A2','LAPORAN PEMBAYARAN MAHASISWA');
		$objPHPExcel->getActiveSheet()->setCellValue('A3','PERIODE '.$tgl1." s/d ".$tgl2);
		$objPHPExcel->getActiveSheet()->setCellValue('A4','NO');
		$objPHPExcel->getActiveSheet()->setCellValue('B4','NIM');
		$objPHPExcel->getActiveSheet()->setCellValue('C4','NAMA');
		$objPHPExcel->getActiveSheet()->setCellValue('D4','ANGKATAN/PRODI');
		$objPHPExcel->getActiveSheet()->setCellValue('E4','TANGGAL BAYAR');
		$objPHPExcel->getActiveSheet()->setCellValue('F4','PEMBAYARAN');
		$objPHPExcel->getActiveSheet()->getStyle('A4:F6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:F6')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('A4:F6')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A4:F6')->applyFromArray($border);
		$objPHPExcel->getActiveSheet()->getStyle('A4:F6')->getAlignment()->setWrapText(true);
		// komponen
		$colH=1;
		$nCol=count($komp);
		$objPHPExcel->getActiveSheet()->setCellValue('G4',"Komponen Biaya");
		if(count($komp)>0){
			$objPHPExcel->getActiveSheet()->getStyle('G4:'.$arrHuruf[$nCol]."4")->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('G4:'.$arrHuruf[$nCol]."4")->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->mergeCells('G4:'.$arrHuruf[$nCol]."4");
			$objPHPExcel->getActiveSheet()->getStyle('G4:'.$arrHuruf[$nCol]."4")->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('G4:'.$arrHuruf[$nCol]."4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			foreach ($komp as $dtKomp){
				$arrKomp=explode("||", $dtKomp);
				$id_komp=$arrKomp[0];
				$nm_komp=$arrKomp[1];
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colH]."5",$nm_komp);
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colH]."6","Kewajiban");
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."5:".$arrHuruf[$colH]."6")->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."5:".$arrHuruf[$colH]."6")->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."5:".$arrHuruf[$colH]."6")->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colH]."5:".$arrHuruf[$colH]."6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$colH])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$colH+1])->setWidth(20);
				$colH=$colH+1;
			}
		}
		// data isi
		$x=1;
		$i=7;
		foreach ($byrHdr as $dtBayarHdr){
			$arrByrHdr=explode("||", $dtBayarHdr);
			$notrans=$arrByrHdr[0];
			$tgl=$arrByrHdr[1];
			$nim=$arrByrHdr[2];
			$nm=$arrByrHdr[3];
			$akt=$arrByrHdr[4];
			$prd=$arrByrHdr[5];
			$pembayaran=$arrByrHdr[6];
			$objPHPExcel->getActiveSheet()->setCellValue("A".$i,$x);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$i,$nim);
			$objPHPExcel->getActiveSheet()->setCellValue("C".$i,$nm);
			$objPHPExcel->getActiveSheet()->setCellValue("D".$i,$prd."/".$akt);
			$objPHPExcel->getActiveSheet()->setCellValue("E".$i,$tgl);
			$objPHPExcel->getActiveSheet()->setCellValue("F".$i,$pembayaran);
			$objPHPExcel->getActiveSheet()->getStyle("A".$i.":F".$i)->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle("D".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$nomAlocate=0;
			$colD=1;
			$nCol=count($komp)*2;
			if(count($komp)>0){
				foreach ($komp as $dtKomp){
					$arrKomp=explode("||", $dtKomp);
					$id_komp=$arrKomp[0];
					$nomAlocate=0;
					foreach ($byrDtl as $dtBayarDtl){
						if(($dtBayarDtl['id_komp_alocate']==$id_komp)and($notrans==$dtBayarDtl['no_trans'])){
							$nomAlocate=$dtBayarDtl['nominal_alocate'];
						}
					}
					$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i)->applyFromArray($border);
					$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$colD].$i,$nomAlocate);
					$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$colD].$i.":".$arrHuruf[$colD+1].$i)->getNumberFormat() ->setFormatCode('#,##0.00');
					$colD=$colD+1;
				}
			}
			$x++;
			$i++;
		}
		// sum
		$objPHPExcel->getActiveSheet()->setCellValue("A".$i,"JUMLAH");
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.":F".$i)->applyFromArray($border);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.":F".$i);
		for($y=1;$y<$colD;$y++){
			$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$y].$i,"=SUM(".$arrHuruf[$y]."7:".$arrHuruf[$y].($i-1).")");
			$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$y].$i)->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$y].$i)->getNumberFormat() ->setFormatCode('#,##0.00');
		}
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Laporan Pembayaran Mahasiswa.xls"');
		header('Cache-Control: max-age=0');
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
}