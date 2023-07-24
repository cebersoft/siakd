<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Tugas Controller -> Controller untuk modul halaman tugas
*/
class TugasController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Kelas');
		Zend_Loader::loadClass('Paketkelas');
		//Zend_Loader::loadClass('KelompokPraktikum');
		Zend_Loader::loadClass('Nmkelas');
		Zend_Loader::loadClass('Tugas');
		Zend_Loader::loadClass('TugasMhs');
		Zend_Loader::loadClass('Rps');
		Zend_Loader::loadClass('TimTeaching');
		Zend_Loader::loadClass('KalenderAkd');
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
		$this->view->lms_act="active";
	}

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Daftar Tugas";
		// get kd paket kelas
		$kd_paket=$this->_request->get('id');
		$paketkelas = new Paketkelas();
		$getPaketKelas=$paketkelas->getPaketKelasByKd($kd_paket);
		if($getPaketKelas){
			foreach ($getPaketKelas as $dtPaket) {
				$this->view->kd_paket_kelas = $dtPaket['kd_paket_kelas'];
				$this->view->id_kel = "";
				$this->view->url = $dtPaket['kd_paket_kelas'];
				$kdKelas = $dtPaket['kd_kelas'];
				$this->view->kd_kelas = $dtPaket['kd_kelas'];
				$this->view->kd_paket_kelas = $dtPaket['kd_paket_kelas'];
				$this->view->nm_prodi=$dtPaket['nm_prodi_kur'];
				$this->view->kd_per=$dtPaket['kd_periode'];
				$this->view->nm=$dtPaket['nm_kelas'];
				$this->view->jns_kelas=$dtPaket['jns_kelas'];
				$this->view->nm_dsn=$dtPaket['nm_dosen'];
				$this->view->kd_dsn=$dtPaket['kd_dosen'];
				$kd_dsn=$dtPaket['kd_dosen'];
				$this->view->nm_mk=$dtPaket['nm_mk'];
				$this->view->kd_mk=$dtPaket['kode_mk'];
				$this->view->sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
				$id_mk_kur=$dtPaket['id_mk_kurikulum'];
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
			if(($kd_dsn==$this->kd_dsn)or($found>0)){
				// ref
				$rps=new Rps();
				$getRps=$rps->getRpsByMkKur($id_mk_kur);
				$arrRps=array();
				if($getRps){
					$id_rps="";
					foreach($getRps as $dtRps){
						$id_rps=$dtRps['id_rps'];
					}
					$getDtlRps=$rps->getRpsDetilByRps($id_rps);
					$arrRps=$getDtlRps;
				}
				$this->view->listRps=$arrRps;
				// data tugas
				$tugas=new Tugas();
				$getTugas=$tugas->getTugasByPaket($kd_paket);
				$this->view->listTugas=$getTugas;
				// navigation
				$this->_helper->navbar('lms/detil?id='.$kdKelas,0);	
			}else{
				$this->view->eksis="f";
				// navigation
				$this->_helper->navbar('lms',0);
			}
		}else{
			// get kelompok
			$kelompok = new KelompokPraktikum();
			$getKelompok=$kelompok->getKelompokPraktikumByKd($kd_paket);
			if($getKelompok){
				foreach ($getKelompok as $dtPaket) {
					$this->view->kd_paket_kelas = "";
					$this->view->id_kel = $dtPaket['id_kelompok'];
					$this->view->url = $dtPaket['id_kelompok'];
					$kdKelas = $dtPaket['kd_kelas'];
					$this->view->kd_kelas = $dtPaket['kd_kelas'];
					$this->view->kd_paket_kelas = $dtPaket['kd_paket_kelas'];
					$this->view->nm_prodi=$dtPaket['nm_prodi_kur'];
					$this->view->kd_per=$dtPaket['kd_periode'];
					$this->view->nm=$dtPaket['nm_kelompok'];
					$this->view->jns_kelas=$dtPaket['jns_kelas'];
					$this->view->nm_dsn=$dtPaket['nm_dosen'];
					$this->view->kd_dsn=$dtPaket['kd_dosen'];
					$kd_dsn=$dtPaket['kd_dosen'];
					$this->view->nm_mk=$dtPaket['nm_mk'];
					$this->view->kd_mk=$dtPaket['kode_mk'];
					$this->view->sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
					$id_mk_kur=$dtPaket['id_mk_kurikulum'];
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
				if(($kd_dsn==$this->kd_dsn)or($found>0)){
					// ref
					$rps=new Rps();
					$getRps=$rps->getRpsByMkKur($id_mk_kur);
					$arrRps=array();
					if($getRps){
						$id_rps="";
						foreach($getRps as $dtRps){
							$id_rps=$dtRps['id_rps'];
						}
						$getDtlRps=$rps->getRpsDetilByRps($id_rps);
						$arrRps=$getDtlRps;
					}
					$this->view->listRps=$arrRps;
					// data tugas
					$tugas=new Tugas();
					$getTugas=$tugas->getTugasByKelompok($kd_paket);
					$this->view->listTugas=$getTugas;
					// navigation
					$this->_helper->navbar('lms/detil?id='.$kdKelas,0);	
				}else{
					$this->view->eksis="f";
					// navigation
					$this->_helper->navbar('lms',0);
				}
			}else{
				$this->view->eksis="f";
				// navigation
				$this->_helper->navbar('lms',0);
			}
		}
	}

	function detilAction(){
		// Title Browser
		$this->view->title = "Daftar Pengerjaan Tugas oleh Mahasiswa";
		// get id tugas
		$id_tugas=$this->_request->get('id');
		$tugas = new Tugas();
		$getTugas=$tugas->getTugasById($id_tugas);
		if($getTugas){
			foreach ($getTugas as $dtTugas) {
				$this->view->id_tugas = $dtTugas['id_tugas'];
				$this->view->minggu = $dtTugas['minggu'];
				$this->view->prm = $dtTugas['param_nilai'];
				$this->view->jdl = $dtTugas['judul_tugas'];
				$this->view->knt = $dtTugas['konten_tugas'];
				$this->view->tgl1 = $dtTugas['published_fmt'];
				$this->view->tgl2 = $dtTugas['expired_fmt'];
				$this->view->link = $dtTugas['link'];
				$this->view->file = $dtTugas['nm_file'];
				$this->view->nm_dosen = $dtTugas['nm_dosen'];
				$kd_paket=$dtTugas['kd_paket_kelas'];
				$id_kel=$dtTugas['id_kelompok'];
			}
			// data tugas
			$tugasMhs=new TugasMhs();
			$getTugasMhs=$tugasMhs->getTugasMhsByTugas($id_tugas);
			$this->view->listTugasMhs=$getTugasMhs;
			$paket=new Paketkelas();
			$getPaketKelas=$paket->getPaketKelasByKd($kd_paket);
			$kd_periode="";
			if($getPaketKelas){
				foreach($getPaketKelas as $dtPaket){
					$kd_periode=$dtPaket['kd_periode'];
				}
				// navigation
				$this->_helper->navbar('tugas/index?id='.$kd_paket,0);
			}else{
				$kelompok=new KelompokPraktikum();
				$getKelompok=$kelompok->getKelompokPraktikumByKd($id_kel);
				foreach($getKelompok as $dtKel){
					$kd_periode=$dtKel['kd_periode'];
				}
				// navigation
				$this->_helper->navbar('tugas/index?id='.$id_kel,0);
			}
			// cek allowance input
			$tgl = date('Y-m-d');
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
		}else{
			$this->view->eksis="f";
			// navigation
			$this->_helper->navbar('lms',0);
		}
	}

	function exportdetilAction(){
		// layout
		$this->_helper->layout->disableLayout();
		// get id tugas
		$id_tugas=$this->_request->get('id');
		$tugas = new Tugas();
		$getTugas=$tugas->getTugasById($id_tugas);
		if($getTugas){
			foreach ($getTugas as $dtTugas) {
				$id_tugas = $dtTugas['id_tugas'];
				$minggu = $dtTugas['minggu'];
				$prm = $dtTugas['param_nilai'];
				$jdl = $dtTugas['judul_tugas'];
				$knt = $dtTugas['konten_tugas'];
				$tgl1 = $dtTugas['published_fmt'];
				$tgl2 = $dtTugas['expired_fmt'];
				$link = $dtTugas['link'];
				$file = $dtTugas['nm_file'];
				$nm_dosen = $dtTugas['nm_dosen'];
				$kd_paket=$dtTugas['kd_paket_kelas'];
			}
			// data tugas
			$tugasMhs=new TugasMhs();
			$getTugasMhs=$tugasMhs->getTugasMhsByTugas($id_tugas);
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
										 ->setDescription("Daftar Nilai Tugas Mahasiswa")
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
			$objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:E2');
			$objPHPExcel->getActiveSheet()->mergeCells('A3:B3');
			$objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
			$objPHPExcel->getActiveSheet()->mergeCells('A5:B5');
			$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('A1:E6')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(1);
			$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A6:E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			// insert data to excel
			$objPHPExcel->getActiveSheet()->setCellValue('A1','DAFTAR NILAI TUGAS MAHASISWA');
			$objPHPExcel->getActiveSheet()->setCellValue('A3','Judul Tugas');
			$objPHPExcel->getActiveSheet()->setCellValue('A4','Dosen');
			$objPHPExcel->getActiveSheet()->setCellValue('A5','Minggu');
			$objPHPExcel->getActiveSheet()->setCellValue('C3',$jdl);
			$objPHPExcel->getActiveSheet()->mergeCells('C3:E3');
			$objPHPExcel->getActiveSheet()->setCellValue('C4',$nm_dosen);
			$objPHPExcel->getActiveSheet()->mergeCells('C4:E4');
			$objPHPExcel->getActiveSheet()->setCellValue('C5',$minggu);
			$objPHPExcel->getActiveSheet()->mergeCells('C5:E5');
			$objPHPExcel->getActiveSheet()->getStyle('A3:E5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->setCellValue('A6','No');
			$objPHPExcel->getActiveSheet()->setCellValue('B6','NPM');
			$objPHPExcel->getActiveSheet()->setCellValue('C6','Nama');
			$objPHPExcel->getActiveSheet()->setCellValue('D6','Angkatan');
			$objPHPExcel->getActiveSheet()->setCellValue('E6','Nilai');
			$objPHPExcel->getActiveSheet()->getStyle('A1:D6') ->getAlignment()->setWrapText(true);
			$i=7;
			$n=1;
			foreach ($getTugasMhs as $data){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$data['nim']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$data['nm_mhs']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$data['id_angkatan']);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$data['nilai']);
				$objPHPExcel->getActiveSheet()->setCellValue('R'.$i,$data['id_tugas_mhs']);
				$objPHPExcel->getActiveSheet()->getStyle('R'.$i)->applyFromArray(array('font'  => array('color'=>array('rgb'=>'FFFFFF'))));
				$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->applyFromArray($cell_color);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$i++;
				$n++;
			}
			$objPHPExcel->getActiveSheet()->getStyle('A6:E'.($i-1))->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('A6:E'.($i-1))->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('E7:E'.($i-1))->getNumberFormat()->setFormatCode('#,##0.00');
			// Redirect output to a client web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Daftar Nilai Tugas.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}else{
			
		}
	}

}