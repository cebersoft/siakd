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
		Zend_Loader::loadClass('Paketkelas');
		Zend_Loader::loadClass('Kuliah');
		Zend_Loader::loadClass('Nilai');
		Zend_Loader::loadClass('Kurikulum');
		Zend_Loader::loadClass('Ekuivalensi');
		Zend_Loader::loadClass('Register');
		Zend_Loader::loadClass('StatReg');
		Zend_Loader::loadClass('AturanNilai');
		Zend_Loader::loadClass('Konversi');
		Zend_Loader::loadClass('KalenderAkd');
		Zend_Loader::loadClass('TimTeaching');
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
		$this->view->kls_act="active";
	}
	
	function indexAction(){
		// Title Browser
		$this->view->title = "Daftar Nilai Mahasiswa";
		// get kd paket kelas
		$kd_paket=$this->_request->get('kd');
		$paketkelas = new Paketkelas();
		$getPaketKelas=$paketkelas->getPaketKelasByKd($kd_paket);
		if($getPaketKelas){
			foreach ($getPaketKelas as $dtPaket) {
				$this->view->kd_paket_kelas = $dtPaket['kd_paket_kelas'];
				$kdKelas = $dtPaket['kd_kelas'];
				$this->view->kd_kelas = $dtPaket['kd_kelas'];
				$this->view->kd_paket_kelas = $dtPaket['kd_paket_kelas'];
				$this->view->nm_prodi=$dtPaket['nm_prodi_kur'];
				$this->view->kd_per=$dtPaket['kd_periode'];
				$this->view->nm_kelas=$dtPaket['nm_kelas'];
				$this->view->jns_kelas=$dtPaket['jns_kelas'];
				$this->view->nm_dsn=$dtPaket['nm_dosen'];
				$kd_dsn=$dtPaket['kd_dosen'];
				$this->view->nm_mk=$dtPaket['nm_mk'];
				$this->view->kd_mk=$dtPaket['kode_mk'];
				$this->view->sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
			}
			$timTeaching=new TimTeaching();
			$getKelasTt=$timTeaching->getTimTeachingByKelas($kdKelas);
			$found=0;
			if ($getKelasTt){
				foreach($getKelasTt as $tt){
					if($tt['kd_dosen']==$this->kd_dsn){
						$found=$found+1;
					}
				}
			}
			$agttim="f";
			if(($kd_dsn!=$this->kd_dsn)and($found>0)){
				// tim teaching aja -- ga bisa fix/unfix
				$agttim="t";
			}
			$this->view->agttim=$agttim;
			if(($kd_dsn==$this->kd_dsn)or($found>0)){
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
				}
				$nilai = new Nilai();
				$getNilai = $nilai->getNilaiByPaket($kd_paket);
				$this->view->listNilai=$getNilai;
				// navigation
				$this->_helper->navbar('paketkelas/index?kd='.$kdKelas,'nilai/export?id='.$kd_paket);	
			}else{
				$this->view->eksis="f";
				// navigation
				$this->_helper->navbar('kelas/list',0);
			}
		}else{
			$this->view->eksis="f";
			// navigation
			$this->_helper->navbar('kelas/list',0);
		}
	}
	
	
	function newAction(){
		// get kd paket kelas
		$kd_kuliah=$this->_request->get('id');
		$nilai = new Nilai();
		$getNilai = $nilai->getNilaiByKd($kd_kuliah);
		if($getNilai){
			foreach ($getNilai as $dataNilai) {
				$this->view->nim=$dataNilai['nim'];
				$this->view->nm=$dataNilai['nm_mhs'];
				$kd_paket=$dataNilai['kd_paket_kelas'];
				$this->view->kd_kelas=$dataNilai['kd_kelas'];
				$kdKelas=$dataNilai['kd_kelas'];
				$this->view->kd_kuliah=$dataNilai['kd_kuliah'];
				$this->view->nm_prodi=$dataNilai['nm_prodi'];
				$kdProdi=$dataNilai['kd_prodi'];
				$this->view->kd_per=$dataNilai['kd_periode'];
				$kd_periode=$dataNilai['kd_periode'];
				$kdPeriode=$dataNilai['kd_periode'];
				$this->view->nm_kelas=$dataNilai['nm_kelas'];
				$this->view->jns_kelas=$dataNilai['jns_kelas'];
				$this->view->kd_dsn=$dataNilai['kd_dosen'];
				$kd_dsn=$dataNilai['kd_dosen'];
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
				// cek tim teaching
				$timTeaching=new TimTeaching();
				$getKelasTt=$timTeaching->getTimTeachingByKelas($kdKelas);
				$found=0;
				if ($getKelasTt){
					foreach($getKelasTt as $tt){
						if($tt['kd_dosen']==$this->kd_dsn){
							$found=$found+1;
						}
					}
				}
				$agttim="f";
				if(($kd_dsn!=$this->kd_dsn)and($found>0)){
					// tim teaching aja -- ga bisa fix/unfix
					$agttim="t";
				}
				$this->view->agttim=$agttim;
				if(($kd_dsn!=$this->kd_dsn)and($found==0)){
					$this->view->eksis="f";
				}else{
					$tgl = date('Y-m-d');
					/* versi 1 (ceking tanggal aktual->periode akademik)
					// cek periode dan tanggal di kalender
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
						$this->view->allowInput=-1;	
					}else{
						// cek kalender
						$kd_aktivitas='201';
						// jadwal entri nilai
						$kalender=new KalenderAkd();
						$getKalender=$kalender->getKalenderAkdByPeriodeAktivitas($kd_periode, $kd_aktivitas);
						$this->view->allowInput="";
						if ($getKalender){
							// cek tanggal
							foreach ($getKalender as $dtKalender) {
								$startDate=$dtKalender['start_date'];
								$endDate=$dtKalender['end_date'];
							}
							if(($tgl>=$startDate)and($tgl<=$endDate)){
								$this->view->allowInput=1;
							}else{
								$this->view->allowInput=-1;
							}
						}else{
							$this->view->allowInput=0;
						}
					}
					*/
					// versi 2 (tanpa ceking tanggal aktual
					// cek kalender
					$kd_aktivitas='201';
					// jadwal entri nilai
					$kalender=new KalenderAkd();
					$getKalender=$kalender->getKalenderAkdByPeriodeAktivitas($kd_periode, $kd_aktivitas);
					$this->view->allowInput="";
					if ($getKalender){
						// cek tanggal
						foreach ($getKalender as $dtKalender) {
							$startDate=$dtKalender['start_date'];
							$endDate=$dtKalender['end_date'];
						}
						if(($tgl>=$startDate)and($tgl<=$endDate)){
							$this->view->allowInput=1;
						}else{
							$this->view->allowInput=-1;
						}
					}else{
						$this->view->allowInput=0;
					}
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
			$this->_helper->navbar('nilai/index?kd='.$kd_paket,0);
		}else{
			$this->view->eksis="f";
		}
		$this->view->title="Data Nilai Mahasiswa";
	}
	
	function uploadAction(){
		// message
		$message = new Zend_Session_Namespace('message');
		$this->view->msg = $message->message;
		$kd_paket = $this->_request->get('id');
		// get kd paket kelas
		$kd_paket=$this->_request->get('id');
		$paketkelas = new Paketkelas();
		$getPaketKelas=$paketkelas->getPaketKelasByKd($kd_paket);
		if($getPaketKelas){
			foreach ($getPaketKelas as $dtPaket) {
				$kdKelas = $dtPaket['kd_kelas'];
				$this->view->kd_paket_kelas = $dtPaket['kd_paket_kelas'];
				$this->view->kd_kelas = $dtPaket['kd_kelas'];
				$this->view->nm_prodi=$dtPaket['nm_prodi_kur'];
				$this->view->kd_per=$dtPaket['kd_periode'];
				$kd_periode=$dtPaket['kd_periode'];
				$this->view->nm_kelas=$dtPaket['nm_kelas'];
				$this->view->jns_kelas=$dtPaket['jns_kelas'];
				$kd_dsn=$dtPaket['kd_dosen'];
				$this->view->nm_dsn=$dtPaket['nm_dosen'];
				$this->view->nm_mk=$dtPaket['nm_mk'];
				$this->view->kd_mk=$dtPaket['kode_mk'];
				$this->view->sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
				// cek tim teaching
				$timTeaching=new TimTeaching();
				$getKelasTt=$timTeaching->getTimTeachingByKelas($kdKelas);
				$found=0;
				if ($getKelasTt){
					foreach($getKelasTt as $tt){
						if($tt['kd_dosen']==$this->kd_dsn){
							$found=$found+1;
						}
					}
				}
				$agttim="f";
				if(($kd_dsn!=$this->kd_dsn)and($found>0)){
					// tim teaching aja -- ga bisa fix/unfix
					$agttim="t";
				}
				$this->view->agttim=$agttim;
				if(($kd_dsn!=$this->kd_dsn)and($found==0)){
					$this->view->eksis="f";
				}else{
					$tgl = date('Y-m-d');
					/* versi 1 (ceking tanggal aktual->periode akademik)
					// cek periode dan tanggal di kalender
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
						$this->view->allowInput=-1;	
					}else{
						// cek kalender
						$kd_aktivitas='201';
						// jadwal entri nilai
						$kalender=new KalenderAkd();
						$getKalender=$kalender->getKalenderAkdByPeriodeAktivitas($kd_periode, $kd_aktivitas);
						$this->view->allowInput="";
						if ($getKalender){
							// cek tanggal
							foreach ($getKalender as $dtKalender) {
								$startDate=$dtKalender['start_date'];
								$endDate=$dtKalender['end_date'];
							}
							if(($tgl>=$startDate)and($tgl<=$endDate)){
								$this->view->allowInput=1;
							}else{
								$this->view->allowInput=-1;
							}
						}else{
							$this->view->allowInput=0;
						}
					}
					*/
					// versi 2 (tanpa ceking tanggal aktual
					// cek kalender
					$kd_aktivitas='201';
					// jadwal entri nilai
					$kalender=new KalenderAkd();
					$getKalender=$kalender->getKalenderAkdByPeriodeAktivitas($kd_periode, $kd_aktivitas);
					$this->view->allowInput="";
					if ($getKalender){
						// cek tanggal
						foreach ($getKalender as $dtKalender) {
							$startDate=$dtKalender['start_date'];
							$endDate=$dtKalender['end_date'];
						}
						if(($tgl>=$startDate)and($tgl<=$endDate)){
							$this->view->allowInput=1;
						}else{
							$this->view->allowInput=-1;
						}
					}else{
						$this->view->allowInput=0;
					}
				}
			}
		}else{
			$this->view->eksis="f";
		}
		$this->view->title="Upload Data Nilai Mahasiswa";
		// navigation
		$this->_helper->navbar('nilai/index?kd='.$kd_paket,0);
	}

	function exportAction(){
		// layout
		$this->_helper->layout->disableLayout();
		// get kd paket kelas
		$kd_paket=$this->_request->get('id');
		$paketkelas = new Paketkelas();
		$getPaketKelas=$paketkelas->getPaketKelasByKd($kd_paket);
		if($getPaketKelas){
			foreach ($getPaketKelas as $dtPaket) {
				$kdKelas = $dtPaket['kd_kelas'];
				$kd_kelas = $dtPaket['kd_kelas'];
				$nm_prodi=$dtPaket['nm_prodi_kur'];
				$kd_per=$dtPaket['kd_periode'];
				$nm_kelas=$dtPaket['nm_kelas'];
				$jns_kelas=$dtPaket['jns_kelas'];
				$nm_dsn=$dtPaket['nm_dosen'];
				$nm_mk=$dtPaket['nm_mk'];
				$kd_mk=$dtPaket['kode_mk'];
				$sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
			}
			$kelas = new Kelas();
			$getKelas = $kelas->getKelasByKd($kdKelas);
			foreach ($getKelas as $dtKls) {
				$nm_p1=$dtKls['nm_p1'];
				$nm_p2=$dtKls['nm_p2'];
				$nm_p3=$dtKls['nm_p3'];
				$nm_p4=$dtKls['nm_p4'];
				$nm_p5=$dtKls['nm_p5'];
				$nm_p6=$dtKls['nm_p6'];
				$nm_p7=$dtKls['nm_p7'];
				$nm_p8=$dtKls['nm_p8'];
				$p_p1=$dtKls['p_p1'];
				$p_p2=$dtKls['p_p2'];
				$p_p3=$dtKls['p_p3'];
				$p_p4=$dtKls['p_p4'];
				$p_uts=$dtKls['p_uts'];
				$p_p5=$dtKls['p_p5'];
				$p_p6=$dtKls['p_p6'];
				$p_p7=$dtKls['p_p7'];
				$p_p8=$dtKls['p_p8'];
				$p_uas=$dtKls['p_uas'];
				$p_tot=$dtKls['p_p1']+$dtKls['p_p2']+$dtKls['p_p3']+$dtKls['p_p4']+$dtKls['p_p5']+$dtKls['p_p6']+$dtKls['p_p7']+$dtKls['p_p8']+$dtKls['p_uts']+$dtKls['p_uas'];
			}
			$nilai = new Nilai();
			$getNilai = $nilai->getNilaiByPaket($kd_paket);
			// export excel
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$ses_lec = new Zend_Session_Namespace('ses_lec');
			$nm_pt = $ses_lec->nm_pt;
			$objPHPExcel->getProperties()->setCreator($nm_pt)
										 ->setLastModifiedBy("Akademik")
										 ->setTitle("Nilai Mahasiswa")
										 ->setSubject("Sistem Informasi Akademik")
										 ->setDescription("Daftar Nilai Mahasiswa")
										 ->setKeywords("daftar nilai")
										 ->setCategory("Data File");
										 
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Export Daftar Nilai');
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
			$objPHPExcel->getActiveSheet()->mergeCells('A1:Q1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:Q2');
			$objPHPExcel->getActiveSheet()->mergeCells('A3:B3');
			$objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
			$objPHPExcel->getActiveSheet()->mergeCells('A5:B5');
			$objPHPExcel->getActiveSheet()->mergeCells('A6:B6');
			$objPHPExcel->getActiveSheet()->mergeCells('A7:B7');
			$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('A1:Q9')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(0);
			$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A8:Q9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A8:Q9')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			// insert data to excel
			$objPHPExcel->getActiveSheet()->setCellValue('A1','DAFTAR NILAI MAHASISWA');
			$objPHPExcel->getActiveSheet()->setCellValue('A2',strtoupper($nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('A3','PROGRAM STUDI');
			$objPHPExcel->getActiveSheet()->setCellValue('A4','PERIODE');
			$objPHPExcel->getActiveSheet()->setCellValue('A5','KELAS');
			$objPHPExcel->getActiveSheet()->setCellValue('A6','DOSEN');
			$objPHPExcel->getActiveSheet()->setCellValue('A7','MATA KULIAH');
			$objPHPExcel->getActiveSheet()->setCellValue('C3',$nm_prodi);
			$objPHPExcel->getActiveSheet()->setCellValue('C4',$kd_per);
			$objPHPExcel->getActiveSheet()->setCellValue('C5',$nm_kelas."(".$jns_kelas.")");
			$objPHPExcel->getActiveSheet()->setCellValue('C6',$nm_dsn);
			$objPHPExcel->getActiveSheet()->setCellValue('C7',$nm_mk."(".$kd_mk.")-".$sks." SKS");
			$objPHPExcel->getActiveSheet()->mergeCells('A9:A10');
			$objPHPExcel->getActiveSheet()->setCellValue('A9','No');
			$objPHPExcel->getActiveSheet()->mergeCells('B9:B10');
			$objPHPExcel->getActiveSheet()->setCellValue('B9','NPM');
			$objPHPExcel->getActiveSheet()->mergeCells('C9:C10');
			$objPHPExcel->getActiveSheet()->setCellValue('C9','Nama');
			$objPHPExcel->getActiveSheet()->mergeCells('D9:D10');
			$objPHPExcel->getActiveSheet()->setCellValue('D9','Angkatan');
			$objPHPExcel->getActiveSheet()->setCellValue('E9',$nm_p1);
			$objPHPExcel->getActiveSheet()->setCellValue('F9',$nm_p2);
			$objPHPExcel->getActiveSheet()->setCellValue('G9',$nm_p3);
			$objPHPExcel->getActiveSheet()->setCellValue('H9',$nm_p4);
			$objPHPExcel->getActiveSheet()->setCellValue('I9',"UTS");
			$objPHPExcel->getActiveSheet()->setCellValue('J9',$nm_p5);
			$objPHPExcel->getActiveSheet()->setCellValue('K9',$nm_p6);
			$objPHPExcel->getActiveSheet()->setCellValue('L9',$nm_p7);
			$objPHPExcel->getActiveSheet()->setCellValue('M9',$nm_p8);
			$objPHPExcel->getActiveSheet()->setCellValue('N9',"UAS");
			$objPHPExcel->getActiveSheet()->setCellValue('O9',"Total");
			$objPHPExcel->getActiveSheet()->getStyle('E9:O9') ->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->setCellValue('E10',$p_p1."%");
			$objPHPExcel->getActiveSheet()->setCellValue('F10',$p_p2."%");
			$objPHPExcel->getActiveSheet()->setCellValue('G10',$p_p3."%");
			$objPHPExcel->getActiveSheet()->setCellValue('H10',$p_p4."%");
			$objPHPExcel->getActiveSheet()->setCellValue('I10',$p_uts."%");
			$objPHPExcel->getActiveSheet()->setCellValue('J10',$p_p5."%");
			$objPHPExcel->getActiveSheet()->setCellValue('K10',$p_p6."%");
			$objPHPExcel->getActiveSheet()->setCellValue('L10',$p_p7."%");
			$objPHPExcel->getActiveSheet()->setCellValue('M10',$p_p8."%");
			$objPHPExcel->getActiveSheet()->setCellValue('N10',$p_uas."%");
			$objPHPExcel->getActiveSheet()->setCellValue('O10',$p_tot."%");
			$objPHPExcel->getActiveSheet()->mergeCells('P9:P10');
			$objPHPExcel->getActiveSheet()->setCellValue('P9','Indeks');
			$objPHPExcel->getActiveSheet()->mergeCells('Q9:Q10');
			$objPHPExcel->getActiveSheet()->setCellValue('Q9','Bobot');
			$i=11;
			$n=1;
			foreach ($getNilai as $data){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$data['nim']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$data['nm_mhs']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$data['id_angkatan']);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$data['p1']);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$data['p2']);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$data['p3']);
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$data['p4']);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$data['uts']);
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$data['p5']);
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,$data['p6']);
				$objPHPExcel->getActiveSheet()->setCellValue('L'.$i,$data['p7']);
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$i,$data['p8']);
				$objPHPExcel->getActiveSheet()->setCellValue('N'.$i,$data['uas']);
				$objPHPExcel->getActiveSheet()->setCellValue('O'.$i,$data['nilai_tot']);
				$arrIndex = explode('/', $data['index']);
				$objPHPExcel->getActiveSheet()->setCellValue('P'.$i,$arrIndex[0]);
				$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i,$arrIndex[1]);
				$objPHPExcel->getActiveSheet()->setCellValue('R'.$i,$data['kd_kuliah']);
				$objPHPExcel->getActiveSheet()->getStyle('R'.$i)->applyFromArray(array('font'  => array('color'=>array('rgb'=>'FFFFFF'))));
				$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':N'.$i)->applyFromArray($cell_color);
				$i++;
				$n++;
			}
			$objPHPExcel->getActiveSheet()->getStyle('A9:Q'.($i-1))->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('A9:Q'.($i-1))->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('E11:O'.($i-1))->getNumberFormat()->setFormatCode('#,##0.00');
			// Redirect output to a client web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Daftar Nilai Mahasiswa.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}else{
			
		}
	}
}