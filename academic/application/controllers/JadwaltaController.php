<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Jadwal TA Controller -> Controller untuk modul halaman jadwal penguji TA
*/
class JadwaltaController extends Zend_Controller_Action
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
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('KelasTA');
		Zend_Loader::loadClass('PaketkelasTA');
		Zend_Loader::loadClass('KuliahTA');
		Zend_Loader::loadClass('NilaiTA');
		Zend_Loader::loadClass('Dosbim');
		Zend_Loader::loadClass('Dosji');
		Zend_Loader::loadClass('Register');
		Zend_Loader::loadClass('StatReg');
		Zend_Loader::loadClass('AturanNilai');
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
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// treeview
		$this->view->active_tree="09";
		$this->view->active_menu="jadwalta/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "jadwalta/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Plotting Jadwal Penguji";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// destroy session param
			Zend_Session::namespaceUnset('param_jadwalta');
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
			// get data periode
			$periode = new Periode();
			$this->view->listPeriode=$periode->fetchAll();
			$getPerAktif=$periode->getPeriodeByStatus(0);
			foreach ($getPerAktif as $dtPerAktif) {
				$per_aktif=$dtPerAktif['kd_periode'];
			}
			$this->view->per_aktif=$per_aktif;
		}
	}

	function listAction()
	{
		$user = new Menu();
		$menu = "jadwalta/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// show data
			$param = new Zend_Session_Namespace('param_jadwalta');
			$kd_prodi = $param->prd;
			$kd_periode = $param->per;
			$prodi = new Prodi();
			$getProdi = $prodi->getProdiByKd($kd_prodi);
			$periode = new Periode();
			$getPeriode = $periode->getPeriodeByKd($kd_periode);
			if(($getPeriode)and($getProdi)){
				foreach ($getProdi as $dtProdi) {
					$nm_prd=$dtProdi['nm_prodi'];
				}
				// Title Browser
				$this->view->title = "Daftar Mata Kuliah TA Periode ".$kd_periode." Prodi ".$nm_prd;
				// paket kelas
				$paketkelasta=new PaketkelasTA();
				$this->view->listPaketTA=$paketkelasta->getPaketKelasTAByPeriodeProdi($kd_periode,$kd_prodi);
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Daftar Mata Kuliah TA";	
			}
			// navigation
			$this->_helper->navbar('jadwalta',0,0,0,0);
		}
	}

	function detilAction(){
		$user = new Menu();
		$menu = "jadwalta/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// layout
			$this->_helper->layout()->setLayout('second');
			// Title Browser
			$this->view->title = "Daftar Plotting Jadwal Penguji";
			// get kd paket kelas
			$kd_paket=$this->_request->get('id');
			$this->view->kd_paket=$kd_paket;
			$paketkelasta = new PaketkelasTA();
			$getPaketKelasTA=$paketkelasta->getPaketKelasTAByKd($kd_paket);
			if($getPaketKelasTA){
				foreach ($getPaketKelasTA as $dtPaket) {
					$kd_periode=$dtPaket['kd_periode'];
					$kd_prodi=$dtPaket['kd_prodi_kur'];
					$kdKelas = $dtPaket['kd_kelas'];
					$this->view->kd_kelas = $dtPaket['kd_kelas'];
					$this->view->nm_prodi=$dtPaket['nm_prodi_kur'];
					$this->view->kd_per=$dtPaket['kd_periode'];
					$this->view->nm_kelas=$dtPaket['nm_kelas'];
					$this->view->jns_kelas=$dtPaket['jns_kelas'];
					$this->view->nm_dsn=$dtPaket['nm_dosen'];
					$this->view->nm_mk=$dtPaket['nm_mk'];
					$this->view->kd_mk=$dtPaket['kode_mk'];
					$this->view->sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
				}
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
				}
				$nilaiTA = new NilaiTA();
				$getNilaiTA = $nilaiTA->getNilaiTAByPaket($kd_paket);
				$this->view->listNilaiTA=$getNilaiTA;
				// dosji
				$dosji=new Dosji();
				$this->view->listDosji=$dosji->getDosjiByPeriode($kd_periode,$kd_prodi);
				// navigation
				$this->_helper->navbar('jadwalta/list',0,0,'jadwalta/export?id='.$kd_paket,0);
			}else{
				$this->view->eksis="f";
				// navigation
				$this->_helper->navbar('jadwalta',0,0,0,0);
			}
		}
	}

	function exportAction(){
		$user = new Menu();
		$menu = "jadwalta/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// layout
			$this->_helper->layout->disableLayout();
			// get kd paket kelas
			$kd_paket=$this->_request->get('id');
			$paketkelas = new PaketkelasTA();
			$getPaketKelas=$paketkelas->getPaketKelasTAByKd($kd_paket);
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
				$nilai = new NilaiTA();
				$getNilai = $nilai->getNilaiTAByPaket($kd_paket);
				// export excel
				// konfigurasi excel
				PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
				$objPHPExcel = new PHPExcel();
				$ses_ac = new Zend_Session_Namespace('ses_ac');
				$nm_pt = $ses_ac->nm_pt;
				$objPHPExcel->getProperties()->setCreator($nm_pt)
				->setLastModifiedBy("Akademik")
				->setTitle("Nilai Mahasiswa")
				->setSubject("Sistem Informasi Akademik")
				->setDescription("Daftar Nilai Mahasiswa")
				->setKeywords("daftar nilai")
				->setCategory("Data File");
	
				// Rename sheet
				$objPHPExcel->getActiveSheet()->setTitle('Export Daftar Ploting Jadwal');
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
				$objPHPExcel->getActiveSheet()->mergeCells('A1:K1');
				$objPHPExcel->getActiveSheet()->mergeCells('A2:K2');
				$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setSize(14);
				$objPHPExcel->getActiveSheet()->getStyle('A1:K9')->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(9);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(18);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(50);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
				$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(35);
				$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(35);
				$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(35);
				$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A9:K9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A9:K9')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				// insert data to excel
				$objPHPExcel->getActiveSheet()->setCellValue('A1','DAFTAR PLOTTING JADWAL');
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
				$objPHPExcel->getActiveSheet()->setCellValue('A9','No');
				$objPHPExcel->getActiveSheet()->setCellValue('B9','NPM');
				$objPHPExcel->getActiveSheet()->setCellValue('C9','Nama');
				$objPHPExcel->getActiveSheet()->setCellValue('D9','Angkatan');
				$objPHPExcel->getActiveSheet()->setCellValue('E9','No.Registrasi');
				$objPHPExcel->getActiveSheet()->setCellValue('F9','Judul');
				$objPHPExcel->getActiveSheet()->setCellValue('G9','Pembimbing 1');
				$objPHPExcel->getActiveSheet()->setCellValue('H9','Pembimbing 2');
				$objPHPExcel->getActiveSheet()->setCellValue('I9','Penguji 1');
				$objPHPExcel->getActiveSheet()->setCellValue('J9','Penguji 2');
				$objPHPExcel->getActiveSheet()->setCellValue('K9','Tanggal');
				$i=10;
				$n=1;
				foreach ($getNilai as $data){
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$data['nim']);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$data['nm_mhs']);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$data['id_angkatan']);
					$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$data['no_reg']);
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$data['judul']);
					$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$data['nm_dosen_pemb1']);
					$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$data['nm_dosen_pemb2']);
					$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$data['nm_dosen_penguji1']);
					$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$data['nm_dosen_penguji2']);
					$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,$data['tgl_ujian_fmt']);
					$i++;
					$n++;
				}
				$objPHPExcel->getActiveSheet()->getStyle('A9:K'.($i-1))->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('A9:K'.($i-1))->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle('A9:K'.($i-1))->getFont()->setSize(12);
				// Redirect output to a client web browser (Excel5)
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="Daftar Ploting Jadwal TA.xls"');
				header('Cache-Control: max-age=0');
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$objWriter->save('php://output');
			}else{
	
			}
		}
	}
}