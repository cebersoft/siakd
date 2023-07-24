<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: KBM Controller -> Controller untuk modul halaman perkuliahan
*/
class KbmController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Paketkelas');
		Zend_Loader::loadClass('Kbm');
		Zend_Loader::loadClass('Absensi');
		Zend_Loader::loadClass('Kuliah');
		Zend_Loader::loadClass('Report');
		Zend_Loader::loadClass('Kaprodi');
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
		$this->view->active_tree="06";
		$this->view->active_menu="kbm/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "kbm/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Perkuliahan Mahasiswa";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// destroy session param
			Zend_Session::namespaceUnset('param_pkls');
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
		$menu = "kbm/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// destroy session param
			Zend_Session::namespaceUnset('param_kbm');
			// show data
			$param = new Zend_Session_Namespace('param_pkls');
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
				$this->view->title = "Daftar Paket Kelas Periode ".$kd_periode." Prodi ".$nm_prd;
				// paket kelas
				$paketkelas=new Paketkelas();
				$this->view->listPaket=$paketkelas->getPaketKelasByPeriodeProdi($kd_periode,$kd_prodi);
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Daftar Paket Kelas";	
			}
			// navigation
			$this->_helper->navbar('kbm',0,0,0,0);
		}
	}

	function detilAction()
	{
		$user = new Menu();
		$menu = "kbm/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Perkuliahan";
			// navigation
			$this->_helper->navbar('kbm/list',0,0,'kbm/export',0);
			// get kd paket kelas
			$kd_paket=$this->_request->get('id');
			$paketkelas = new Paketkelas();
			$getPaketKelas=$paketkelas->getPaketKelasByKd($kd_paket);
			if($getPaketKelas){
				foreach ($getPaketKelas as $dtPaket) {
					$this->view->kd_paket = $dtPaket['kd_paket_kelas'];
					$pkt=$dtPaket['kd_paket_kelas'];
					$this->view->nm_prodi=$dtPaket['nm_prodi_kur'];
					$prd=$dtPaket['nm_prodi_kur'];
					$kd_prd=$dtPaket['kd_prodi_kur'];
					$this->view->kd_per=$dtPaket['kd_periode'];
					$per=$dtPaket['kd_periode'];
					$this->view->nm_kelas=$dtPaket['nm_kelas'];
					$nmkls=$dtPaket['nm_kelas'];
					$this->view->jns_kelas=$dtPaket['jns_kelas'];
					$jnskls=$dtPaket['jns_kelas'];
					$this->view->nm_dsn=$dtPaket['nm_dosen'];
					$dsn=$dtPaket['nm_dosen'];
					$this->view->nm_mk=$dtPaket['nm_mk'];
					$nm_mk=$dtPaket['nm_mk'];
					$this->view->kd_mk=$dtPaket['kode_mk'];
					$kd_mk=$dtPaket['kode_mk'];
					$this->view->sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
					$sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
				}
				// get data perkuliahan
				$kbm = new Kbm();
				$getKbm=$kbm->getKbmByPaket($kd_paket);
				$this->view->listKbm=$getKbm;
				// create session for export
				$param = new Zend_Session_Namespace('param_kbm');
				$param->data=$getKbm;
				$param->prd=$prd;
				$param->kd_prd=$kd_prd;
				$param->per=$per;
				$param->nmkls=$nmkls;
				$param->dsn=$dsn;
				$param->nm_mk=$nm_mk;
				$param->kd_mk=$kd_mk;
				$param->sks=$sks;
				$param->pkt=$pkt;
			}else{
				$this->view->eksis="f";
			}
			
		}
	}

	function editAction()
	{
		$user = new Menu();
		$menu = "kbm/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get id perkuliahan
			$id_perkuliahan=$this->_request->get('id');
			// Title Browser
			$this->view->title = "Edit Perkuliahan";
			// get perkuliahan
			$kbm = new Kbm();
			$getKbm = $kbm->getKbmById($id_perkuliahan);
			if($getKbm){
				// data kbm
				foreach ($getKbm as $dataKbm) {
					$this->view->id_perkuliahan=$dataKbm['id_perkuliahan'];
					$kd_paket=$dataKbm['kd_paket_kelas'];
					$this->view->kd_paket=$dataKbm['kd_paket_kelas'];
					$this->view->nm_prodi=$dataKbm['nm_prodi_kur'];
					$this->view->kd_per=$dataKbm['kd_periode'];
					$this->view->nm_kelas=$dataKbm['nm_kelas'];
					$this->view->jns_kelas=$dataKbm['jns_kelas'];
					$this->view->nm_dsn=$dataKbm['nm_dosen'];
					$this->view->nm_mk=$dataKbm['nm_mk'];
					$this->view->kd_mk=$dataKbm['kode_mk'];
					$this->view->hari=$dataKbm['hari'];
					$this->view->tgl=$dataKbm['tgl_kuliah_fmt'];
					$this->view->start=$dataKbm['start_time_fmt'];
					$this->view->end=$dataKbm['end_time_fmt'];
					$this->view->tempat=$dataKbm['tempat'];
					$this->view->materi=$dataKbm['materi'];
					$this->view->media=$dataKbm['media'];
					$this->view->kejadian=$dataKbm['kejadian'];
				}
				// navigation
				$this->_helper->navbar('kbm/detil?id='.$kd_paket,0,0,0,0);
			}else{
				$this->view->eksis="f";
				// navigation
				$this->_helper->navbar('kbm/list',0,0,0,0);
			}
			
		}
	}
	
	function exportAction(){
		$user = new Menu();
		$menu = "kbm/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// session data
			$param = new Zend_Session_Namespace('param_kbm');
			$dataKbm=$param->data;
			$prd=$param->prd;
			$kd_prd=$param->kd_prd;
			$per=$param->per;
			$nmksl=$param->nmkls;
			$dsn=$param->dsn;
			$nm_mk=$param->nm_mk;
			$kd_mk=$param->kd_mk;
			$sks=$param->sks;
			$pkt=$param->pkt;
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt=$ses_ac->nm_pt;
			// image path stempel
			$path = __FILE__;
			$stmPath = str_replace('academic/application/controllers/KbmController.php','public/img/stempel.png',$path);
			// image path ttd
			$kaprodi = new Kaprodi();
			$getKaprodi=$kaprodi->getKaprodiByKdProdiPeriode($kd_prd,$per);
			$ttd_nm="-";
			$ttd_kd="-";
			if($getKaprodi){
				foreach($getKaprodi as $dtKaprodi){
					$ttd_kd=$dtKaprodi['kd_dosen'];
					$ttd_nm=$dtKaprodi['nm_dosen'];
				}
			}
			$ttdPath = str_replace('academic/application/controllers/KbmController.php','public/file/dsn/ttd/'.$ttd_kd.'.png',$path);
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Administrator")
										 ->setLastModifiedBy("Akademik")
										 ->setTitle("Export Data Perkuliahan")
										 ->setSubject("Sistem Informasi Akademik")
										 ->setDescription("Data Perkuliahan")
										 ->setKeywords("perkuliahan")
										 ->setCategory("Data File");
										 
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Data Presensi');
			
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
			$arrHuruf=array("","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U");
			$nPlan=14;
			// properties field excel;
			$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:I2');
			$objPHPExcel->getActiveSheet()->mergeCells('A3:B3');
			$objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
			$objPHPExcel->getActiveSheet()->mergeCells('A5:B5');
			$objPHPExcel->getActiveSheet()->mergeCells('A6:B6');
			$objPHPExcel->getActiveSheet()->mergeCells('A7:B7');
			$objPHPExcel->getActiveSheet()->mergeCells('A8:B8');
			$objPHPExcel->getActiveSheet()->getStyle('A1:A8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A3:C8')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A3:C8')->getFont()->setBold(true);
			
			// column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
			for($z=1;$z<=$nPlan;$z++){
				$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$z])->setWidth(13);	
			}
			$objPHPExcel->getActiveSheet()->mergeCells('E9:'.$arrHuruf[$nPlan].'9');
			$objPHPExcel->getActiveSheet()->mergeCells('A9:A11');
			$objPHPExcel->getActiveSheet()->mergeCells('B9:B11');
			$objPHPExcel->getActiveSheet()->mergeCells('C9:C11');
			$objPHPExcel->getActiveSheet()->mergeCells('D9:D11');
			$objPHPExcel->getActiveSheet()->setCellValue('A1',strtoupper($nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('A2','REKAP PRESENSI PERKULIAHAN MAHASISWA');
			$objPHPExcel->getActiveSheet()->setCellValue('A4','PROGRAM STUDI');
			$objPHPExcel->getActiveSheet()->setCellValue('A5','PERIODE AKADEMIK');
			$objPHPExcel->getActiveSheet()->setCellValue('A6','NAMA DOSEN');
			$objPHPExcel->getActiveSheet()->setCellValue('A7','MATA KULIAH');
			$objPHPExcel->getActiveSheet()->setCellValue('A8','KELAS');
			$objPHPExcel->getActiveSheet()->setCellValue('C4'," : ".strtoupper($prd));
			$objPHPExcel->getActiveSheet()->setCellValue('C5'," : ".$per);
			$objPHPExcel->getActiveSheet()->setCellValue('C6'," : ".strtoupper($dsn));
			$objPHPExcel->getActiveSheet()->setCellValue('C7'," : ".strtoupper($nm_mk)." (".$kd_mk.") ".$sks." SKS");
			$objPHPExcel->getActiveSheet()->setCellValue('C8'," : ".strtoupper($nmksl));
			$objPHPExcel->getActiveSheet()->setCellValue('A9','NO');
			$objPHPExcel->getActiveSheet()->setCellValue('B9','NIM');
			$objPHPExcel->getActiveSheet()->setCellValue('C9','NAMA MAHASISWA');
			$objPHPExcel->getActiveSheet()->setCellValue('D9','ANGKATAN');
			$objPHPExcel->getActiveSheet()->setCellValue('E9','PERTEMUAN');
			$objPHPExcel->getActiveSheet()->getStyle('A9:'.$arrHuruf[$nPlan+2].'11')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A9:'.$arrHuruf[$nPlan+2].'11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			for($z=1;$z<=$nPlan;$z++){
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$z].'10',$z);	
			}
			$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$nPlan+1].'9','TOTAL KEHADIRAN');
			$objPHPExcel->getActiveSheet()->mergeCells($arrHuruf[$nPlan+1].'9:'.$arrHuruf[$nPlan+1].'11');
			$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$nPlan+2].'9','PERSENTASE KEHADIRAN (%)');
			$objPHPExcel->getActiveSheet()->mergeCells($arrHuruf[$nPlan+2].'9:'.$arrHuruf[$nPlan+2].'11');
			$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$nPlan+1])->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$nPlan+2])->setWidth(15);
			$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$nPlan+1].'9:'.$arrHuruf[$nPlan+2].'11')->getAlignment()->setWrapText(true);
			$i=12;
			$n=1;
			// get data mahasiswa
			$absensi=new Absensi();
			$getAbsensiByPaket=$absensi->getAbsensiByPaketKelas($pkt);
			$arrMhs=array();
			$nMhs=0;
			foreach ($getAbsensiByPaket as $dtMhs) {
				$arrMhs[$nMhs]['nim']=$dtMhs['nim'];
				$arrMhs[$nMhs]['nm_mhs']=$dtMhs['nm_mhs'];
				$arrMhs[$nMhs]['id_angkatan']=$dtMhs['id_angkatan'];
				$nMhs++;
			}
			// unique value
			// $arrMhs=array_values(array_unique($arrMhs)); // <--- di php 5.3 ke atas tidak perlu
			$arrMhs = array_values(array_map("unserialize", array_unique(array_map("serialize", $arrMhs))));
			if($dataKbm){
				// loop data mahasiswa
				$sortnim = array();
				foreach ($arrMhs as $key => $row)
				{
				    $sortnim[$key] = $row['nim'];
				}
				array_multisort($sortnim, SORT_ASC, $arrMhs);
				foreach ($arrMhs as $dtMhs) {
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$i,$dtMhs['nim'],PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$dtMhs['nm_mhs']);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$dtMhs['id_angkatan']);
					$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->getFont()->setSize(14);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(50);
					$i++;
					$n++;
				}
				// loop data kbm
				$x=1;
				$tgl = array();
				foreach ($dataKbm as $key => $row)
				{
				    $tgl[$key] = $row['tgl_kuliah'];
				}
				array_multisort($tgl, SORT_ASC, $dataKbm);
				foreach ($dataKbm as $dtkbm){
					$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$x].'11',$dtkbm['tgl_kuliah']);
					$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$x].'11')->getNumberFormat()->setFormatCode('d-mmm-yy');
					// get data absensi
					$getAbsen=$absensi->getAbsensiByPerkuliahan($dtkbm['id_perkuliahan']);
					$start=12;
					foreach ($arrMhs as $dtMhs) {
						foreach ($getAbsen as $dtAbsen) {
							if($dtMhs['nim']==$dtAbsen['nim']){
								$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$x].$start,$dtAbsen['ket']);
								$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$x].$start)->getFont()->setSize(14);
								
							}
						}
						$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$x].$start)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$x].$start)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$nPlan+1].$start,'=COUNTIF(E'.$start.':'.$arrHuruf[$nPlan].$start.',"HADIR")');
						$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$nPlan+2].$start,'='.$arrHuruf[$nPlan+1].$start.'/COUNTA(E11:'.$arrHuruf[$nPlan].'11)*100');
						$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$nPlan+1].$start.':'.$arrHuruf[$nPlan+2].$start)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$start++;
					}
					$x++;
				}
				$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$nPlan+2].'12:'.$arrHuruf[$nPlan+2].($start-1))->getNumberFormat() ->setFormatCode('#,###');
				$objPHPExcel->getActiveSheet()->getStyle('A9:'.$arrHuruf[$nPlan+2].($start-1))->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$nPlan].($start+1),'BANDUNG');
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$nPlan].($start+2),'MENGETAHUI,');
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$nPlan].($start+3),'KETUA PROGRAM STUDI');
				// sheet 2 BAK
				$objWorksheet = new PHPExcel_Worksheet($objPHPExcel);
				$objPHPExcel->addSheet($objWorksheet);
				$objWorksheet->setTitle('BERITA ACARA PERKULIAHAN');
				$objPHPExcel->setActiveSheetIndex(1);
				
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
				
				// properties field excel;
				$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
				$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
				$objPHPExcel->getActiveSheet()->mergeCells('A3:B3');
				$objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
				$objPHPExcel->getActiveSheet()->mergeCells('A5:B5');
				$objPHPExcel->getActiveSheet()->mergeCells('A6:B6');
				$objPHPExcel->getActiveSheet()->mergeCells('A7:B7');
				$objPHPExcel->getActiveSheet()->mergeCells('A8:B8');
				$objPHPExcel->getActiveSheet()->getStyle('A1:A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setSize(14);
				$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('A3:C8')->getFont()->setSize(12);
				$objPHPExcel->getActiveSheet()->getStyle('A3:C8')->getFont()->setBold(true);
				
				// column width
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(45);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
				$objPHPExcel->getActiveSheet()->setCellValue('A1',strtoupper($nm_pt));
				$objPHPExcel->getActiveSheet()->setCellValue('A2','BERITA ACARA PERKULIAHAN');
				$objPHPExcel->getActiveSheet()->setCellValue('A4','PROGRAM STUDI');
				$objPHPExcel->getActiveSheet()->setCellValue('A5','PERIODE AKADEMIK');
				$objPHPExcel->getActiveSheet()->setCellValue('A6','NAMA DOSEN');
				$objPHPExcel->getActiveSheet()->setCellValue('A7','MATA KULIAH');
				$objPHPExcel->getActiveSheet()->setCellValue('A8','KELAS');
				$objPHPExcel->getActiveSheet()->setCellValue('C4'," : ".strtoupper($prd));
				$objPHPExcel->getActiveSheet()->setCellValue('C5'," : ".$per);
				$objPHPExcel->getActiveSheet()->setCellValue('C6'," : ".strtoupper($dsn));
				$objPHPExcel->getActiveSheet()->setCellValue('C7'," : ".strtoupper($nm_mk)." (".$kd_mk.") ".$sks." SKS");
				$objPHPExcel->getActiveSheet()->setCellValue('C8'," : ".strtoupper($nmksl));
				
				$objPHPExcel->getActiveSheet()->setCellValue('A9','NO');
				$objPHPExcel->getActiveSheet()->setCellValue('B9','HARI/TANGGAL');
				$objPHPExcel->getActiveSheet()->setCellValue('C9','MATERI');
				$objPHPExcel->getActiveSheet()->setCellValue('D9','TEMPAT');
				$objPHPExcel->getActiveSheet()->setCellValue('E9','WAKTU');
				$objPHPExcel->getActiveSheet()->setCellValue('F9','MEDIA YANG DIGUNAKAN');
				$objPHPExcel->getActiveSheet()->setCellValue('G9','KEJADIAN SELAMA PERKULIAHAN');
				$objPHPExcel->getActiveSheet()->getStyle('A9:G9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A9:G9')->getFont()->setBold(true);
				$i=10;
				$n=1;
				foreach($dataKbm as $dtkbm){
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$dtkbm['hari'].", ".$dtkbm['tgl_kuliah_fmt']);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$dtkbm['materi']);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$dtkbm['tempat']);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$dtkbm['start_time_fmt']." s/d ".$dtkbm['end_time_fmt']);
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$dtkbm['media']);
					$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$dtkbm['kejadian']);
					$i++;
					$n++;
				}
				$objPHPExcel->getActiveSheet()->getStyle('A9:G'.($i-1))->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('A9:G'.($i-1))->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+1),'BANDUNG');
				$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+2),'MENGETAHUI,');
				$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+3),'KETUA PROGRAM STUDI');
				// drawing stempel
				if(file_exists($stmPath)){
					$objDrawing = new PHPExcel_Worksheet_Drawing();
					$objDrawing->setName('Stempel');
					$objDrawing->setDescription('Stempel');
					$objDrawing->setPath($stmPath);
					$objDrawing->setHeight(90);
					$objDrawing->setWidth(120);
					$objDrawing->setCoordinates('E'.($i+4));
					$objDrawing->setOffsetX(60);
					$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
				}
				// drawing ttd
				if(file_exists($ttdPath)){
					$objDrawing2 = new PHPExcel_Worksheet_Drawing();
					$objDrawing2->setName('Ttd');
					$objDrawing2->setDescription('Ttd');
					$objDrawing2->setPath($ttdPath);
					$objDrawing2->setHeight(125);
					$objDrawing2->setWidth(155);
					$objDrawing2->setCoordinates('F'.($i+4));
					$objDrawing2->setOffsetX(5);
					$objDrawing2->setWorksheet($objPHPExcel->getActiveSheet());	
				}
				$objPHPExcel->getActiveSheet()->mergeCells('F'.($i+9).':G'.($i+9));
				$objPHPExcel->getActiveSheet()->getStyle('F'.($i+9))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+9),$ttd_nm);
			}
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Data Perkuliahan.xls"');
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}
	
	function reportAction()
	{
		$user = new Menu();
		$menu = "kbm/report";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="kbm/report";
			// destroy session param export
			Zend_Session::namespaceUnset('param_kbm_erep');
			// session report
			$param = new Zend_Session_Namespace('param_kbm_rep');
			$prd = $param->prd;
			$per = $param->per;
			if($per){
				// layout
				$this->_helper->layout()->setLayout('second');
				// navigation
				$this->_helper->navbar("kbm/report",0,0,"kbm/ereport",0);
			}else {
				// layout
				$this->_helper->layout()->setLayout('main');
				// navigation
				$this->_helper->navbar(0,0,0,0,0);
			}
			// Title Browser
			$this->view->title = "Report Perkuliahan";
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
			// get periode
			$periode = new Periode();
			$this->view->listPeriode=$periode->fetchAll();
			if($per){
				$rep = new Report();
				// data
				$getTabelData=$rep->getTabel('kuliah_abs');
				$arrTabelData=explode("||", $getTabelData);
				// where data
				$whereD[0]['key'] = 'kd_prodi_kur';
				$whereD[0]['param'] = $prd;
				$whereD[1]['key'] = 'kd_periode';
				$whereD[1]['param'] = $per;
				//--
				$arrKolomD=array('smt_def','kd_paket_kelas','kd_dosen','nm_dosen','kode_mk','nm_mk','nm_kelas','id_nm_kelas','jns_kelas','tgl_kuliah','tgl_kuliah_fmt','materi','n_hadir','n_alpha','n_sakit','n_izin');
				$arrOrder=array('smt_def','kd_dosen','kode_mk','kd_paket_kelas','nm_kelas','tgl_kuliah');
				$getReport= $rep->get($arrTabelData[0], $arrKolomD, $arrKolomD, $arrOrder,$whereD);
				$this->view->x=$rep->query($arrTabelData[0], $arrKolomD, $arrKolomD, $arrOrder,$whereD);
				$this->view->per=$per;
				$prodi = new Prodi();
				$getProdi=$prodi->getProdiByKd($prd);
				if($getProdi){
					foreach ($getProdi as $dtPrd){
						$nm_prd=$dtPrd['nm_prodi'];
						$this->view->nm_prd=$dtPrd['nm_prodi'];		
					}	
				}
				$this->view->listReport=$getReport;
				// session for excel
				$param2 = new Zend_Session_Namespace('param_kbm_erep');
				$param2->data=$getReport;
				$param2->per=$per;
				$param2->prd=$nm_prd;
			}
			// destroy session param
			Zend_Session::namespaceUnset('param_kbm_rep');
		}
	}
	
	function ereportAction(){
		$user = new Menu();
		$menu = "kbm/report";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// session for export
			$param = new Zend_Session_Namespace('param_kbm_erep');
			$per=$param->per;
			$prd=$param->prd;
			$dataRep=$param->data;
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt=$ses_ac->nm_pt;
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt = $ses_ac->nm_pt;
			$objPHPExcel->getProperties()->setCreator($nm_pt)
										 ->setLastModifiedBy("Akademik")
										 ->setTitle("Rekap Perkuliahan")
										 ->setSubject("Sistem Informasi Akademik")
										 ->setDescription("Rekap Perkuliahan")
										 ->setKeywords("rekap perkuliahan")
										 ->setCategory("Data File");
										 
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Rekap Perkuliahan');
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
			$objPHPExcel->getActiveSheet()->mergeCells('A3:K3');
			$objPHPExcel->getActiveSheet()->getStyle('A1:K3')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('A1:K3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(17);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getStyle('A1:K3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			// insert data to excel
			$objPHPExcel->getActiveSheet()->setCellValue('A1',strtoupper($nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('A2','REKAP PERKULIAHAN');
			$objPHPExcel->getActiveSheet()->setCellValue('A3','PROGRAM STUDI '.$prd.' PERIODE '.$per);
			// data
			$objPHPExcel->getActiveSheet()->setCellValue('A4','NO');
			$objPHPExcel->getActiveSheet()->mergeCells('A4:A6');
			$objPHPExcel->getActiveSheet()->setCellValue('B4','NAMA DOSEN');
			$objPHPExcel->getActiveSheet()->mergeCells('B4:B6');
			$objPHPExcel->getActiveSheet()->setCellValue('C4','MATA KULIAH');
			$objPHPExcel->getActiveSheet()->mergeCells('C4:C6');
			$objPHPExcel->getActiveSheet()->setCellValue('D4','KELAS');
			$objPHPExcel->getActiveSheet()->mergeCells('D4:D6');
			$objPHPExcel->getActiveSheet()->setCellValue('E4','TANGGAL');
			$objPHPExcel->getActiveSheet()->mergeCells('E4:E6');
			$objPHPExcel->getActiveSheet()->setCellValue('F4','MATERI');
			$objPHPExcel->getActiveSheet()->mergeCells('F4:F6');
			$objPHPExcel->getActiveSheet()->setCellValue('G4','PERTEMUAN');
			$objPHPExcel->getActiveSheet()->mergeCells('G4:K4');
			$objPHPExcel->getActiveSheet()->setCellValue('G5','DOSEN');
			$objPHPExcel->getActiveSheet()->mergeCells('G5:G6');
			$objPHPExcel->getActiveSheet()->setCellValue('H5','MHS');
			$objPHPExcel->getActiveSheet()->mergeCells('H5:K5');
			$objPHPExcel->getActiveSheet()->setCellValue('H6','H');
			$objPHPExcel->getActiveSheet()->setCellValue('I6','A');
			$objPHPExcel->getActiveSheet()->setCellValue('J6','S');
			$objPHPExcel->getActiveSheet()->setCellValue('K6','I');
			$objPHPExcel->getActiveSheet()->getStyle('A4:K6')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A4:K6')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A4:K6')->applyFromArray($cell_color);
			$objPHPExcel->getActiveSheet()->getStyle('A4:K6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A4:K6')->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('A4:K6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$i=7;
			$n=1;
			$x=1;
			$arrDosen[0]="";
		 	$arrMk[0]="";
		 	$arrKelas[0]="";
		 	$rDosen=$i;
		 	$rMk=$i;
		 	$rKelas=$i;
			foreach ($dataRep as $data){
				$arrDosen[$n]=$data['kd_dosen'];
				$arrMk[$n]=$data['kode_mk'];
				$arrKelas[$n]=$data['kd_paket_kelas'];
				if($arrDosen[$n]!=$arrDosen[$n-1]){
					$rDosen=$i;
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$x);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$data['nm_dosen']);
					$x++;	
				}else{
					$objPHPExcel->getActiveSheet()->mergeCells("A".$rDosen.":A".$i);
					$objPHPExcel->getActiveSheet()->mergeCells("B".$rDosen.":B".$i);
				}
				if(($arrMk[$n]!=$arrMk[$n-1])or($arrDosen[$n]!=$arrDosen[$n-1])){
					$rMk=$i;
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$data['nm_mk']);
				}else{
					$objPHPExcel->getActiveSheet()->mergeCells("C".$rMk.":C".$i);
				}
				if(($arrKelas[$n]!=$arrKelas[$n-1])or($arrDosen[$n]!=$arrDosen[$n-1])or($arrMk[$n]!=$arrMk[$n-1])){
					$rKelas=$i;
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$data['nm_kelas'].'-'.$data['jns_kelas']);
				}else{
					$objPHPExcel->getActiveSheet()->mergeCells("D".$rKelas.":D".$i);
				}
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$data['tgl_kuliah_fmt']);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$data['materi']);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,1);
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$data['n_hadir']);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$data['n_alpha']);
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$data['n_sakit']);
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,$data['n_izin']);
				$objPHPExcel->getActiveSheet()->getStyle('G'.$i.':K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$n++;
				$i++;
			}
			
			//Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Rekap Perkuliahan.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}

	function rekapdosenAction()
	{
		$user = new Menu();
		$menu = "kbm/rekapdosen";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="kbm/rekapdosen";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// Title Browser
			$this->view->title = "Rekap Absensi Dosen";
			// destroy session param export
			Zend_Session::namespaceUnset('param_kbmrekapdosen_erep');
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
			
			$begin = new DateTime('5 February 2017');
			$end = new DateTime(date("Y-m-d",strtotime("+1 day", strtotime('10 February 2017'))));
			$period=array();
			while($begin < $end) {
				$period[] = $begin->format('Y-m-d');
				$begin->modify('+1 day');
			}
			$this->view->listTgl=$period;
		}
	}
	
	function erekapdosenAction(){
		$user = new Menu();
		$menu = "kbm/rekapdosen";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt=$ses_ac->nm_pt;
			// session for export
			$param = new Zend_Session_Namespace('param_kbmrekapdosen_erep');
			$prd=$param->prd;
			$tgl1=$param->tgl1;
			$tgl2=$param->tgl2;
			$tp=$param->tp;
			if($tp==0){
				// get periode akademik
				$periode=new Periode();
				$getPeriode=$periode->getPeriodeByTgl($tgl1);
				$kd_periode="";
				foreach ($getPeriode as $dtPeriode){
					$kd_periode=$dtPeriode['kd_periode'];
				}
				// array tanggal
				$begin = new DateTime($tgl1);
				$end = new DateTime(date("Y-m-d",strtotime("+1 day", strtotime($tgl2))));
				$period=array();
				$month=array();
				while($begin < $end) {
					$period[] = $begin->format('Y-m-d');
					$month[] = $begin->format('M y')."||".$begin->format('Y-m');
					$begin->modify('+1 day');
				}
				$month=array_unique($month);
				$bulan=array();
				$j=0;
				foreach ($month as $dtMonth){
					$arrMonth=explode("||", $dtMonth);
					$mM=$arrMonth[1];
					$nD=0;
					foreach ($period as $dtPeriod){
						$m=date("Y-m",strtotime($dtPeriod));
						if($mM==$m){
							$nD++;
						}
					}
					$bulan[$j]=$arrMonth[0]."||".$nD;
					$j++;
				}
				// destroy session param export
				Zend_Session::namespaceUnset('param_kbmrekapdosen_erep');
				$rep = new Report();
				// data
				$getTabelData=$rep->getTabel('kuliah');
				$arrTabelData=explode("||", $getTabelData);
				// where data
				$whereD[0]['key'] = 'kd_prodi_kur';
				$whereD[0]['param'] = $prd;
				$whereD[1]['key'] = 'tgl_kuliah';
				$whereD[1]['param'] = $period;
				//--
				$arrKolomD=array('kd_paket_kelas','nm_dosen','kode_mk','nm_mk','id_nm_kelas','nm_kelas','jns_kelas','tgl_kuliah');
				$arrOrderD=array('nm_dosen','kode_mk','nm_kelas','tgl_kuliah');
				$arrKolomH=array('kd_paket_kelas','nm_dosen','id_mk_kurikulum','kode_mk','nm_mk','smt_def','id_nm_kelas','nm_kelas','jns_kelas');
				$arrOrderH=array('smt_def','kode_mk','nm_kelas','nm_dosen','jns_kelas');
				$getReportD= $rep->get($arrTabelData[0], $arrKolomD, $arrKolomD, $arrOrderD,$whereD);
				$getReportH= $rep->get($arrTabelData[0], $arrKolomH, $arrKolomH, $arrOrderH,$whereD);
				$this->view->x=$rep->query($arrTabelData[0], $arrKolomH, $arrKolomH, $arrOrderH,$whereD);
				$this->view->y=$getReportH;
					
				// writing excel
				// konfigurasi excel
				PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
				$objPHPExcel = new PHPExcel();
				$objPHPExcel->getProperties()->setCreator("Administrator")
				->setLastModifiedBy("Akademik")
				->setTitle("Export Data Absensi Dosen")
				->setSubject("Sistem Informasi Akademik")
				->setDescription("Data Absensi")
				->setKeywords("absensi")
				->setCategory("Data File");
				
				// Rename sheet
				$objPHPExcel->getActiveSheet()->setTitle('Daftar Absensi Detil');
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
					
				$red_color = array(
						'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb'=>'FF0000')
						),
				);
				
				$arrHuruf=array("","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ");
					
				// properties field excel;
				$objPHPExcel->getActiveSheet()->mergeCells('A1:C1');
				$objPHPExcel->getActiveSheet()->mergeCells('A2:C2');
				$objPHPExcel->getActiveSheet()->mergeCells('A4:C4');
				$objPHPExcel->getActiveSheet()->getStyle('A1:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objPHPExcel->getActiveSheet()->getStyle('A:C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A1:D5')->getFont()->setSize(14);
				$objPHPExcel->getActiveSheet()->getStyle('A1:D5')->getFont()->setBold(true);
				
				// column width
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4.5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
					
				// insert data to excel
				$objPHPExcel->getActiveSheet()->setCellValue('A1','REKAPITULASI KEHADIRAN DOSEN');
				$objPHPExcel->getActiveSheet()->setCellValue('A2','SEMESTER '.$kd_periode);
				$objPHPExcel->getActiveSheet()->setCellValue('A4','PERIODE '.$tgl1.' s/d '.$tgl2);
				$objPHPExcel->getActiveSheet()->setCellValue('A5','NO');
				$objPHPExcel->getActiveSheet()->setCellValue('B5','DOSEN');
				$objPHPExcel->getActiveSheet()->setCellValue('C5','MATA KULIAH');
				$objPHPExcel->getActiveSheet()->setCellValue('D5','KELAS');
				$objPHPExcel->getActiveSheet()->setCellValue('E5','BULAN');
				$objPHPExcel->getActiveSheet()->getStyle('A5:E5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				// bulan
				$iMonth=1;
				foreach ($bulan as $dtBulan){
					$arrMonth=explode("||", $dtBulan);
					$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$iMonth].'6',$arrMonth[0]);
					$objPHPExcel->getActiveSheet()->mergeCells($arrHuruf[$iMonth].'6:'.$arrHuruf[$iMonth+($arrMonth[1]-1)].'6');
					$iMonth=$iMonth+$arrMonth[1];
				}
				$nHari=count($period);
				$objPHPExcel->getActiveSheet()->mergeCells('E5:'.$arrHuruf[$nHari].'5');
				$objPHPExcel->getActiveSheet()->mergeCells('A5:A7');
				$objPHPExcel->getActiveSheet()->mergeCells('B5:B7');
				$objPHPExcel->getActiveSheet()->mergeCells('C5:C7');
				$objPHPExcel->getActiveSheet()->mergeCells('D5:D7');
				$h=1;
				foreach ($period as $dtPeriod){
					$arrPeriod=explode("-", $dtPeriod);
					$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$h].'7',$arrPeriod[2]);
					$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$h])->setWidth(3.5);
					$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$h].'7')->getFont()->setBold(true);
					$day=date("D",strtotime($dtPeriod));
					if($day=='Sun'){
						$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$h].'7')->applyFromArray(array('font'=>array('color'=>array('rgb'=>'FF0000'))));
					}
					$h++;
				}
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$nHari+1].'5','JUMLAH KEHADIRAN');
				$objPHPExcel->getActiveSheet()->mergeCells($arrHuruf[$nHari+1].'5:'.$arrHuruf[$nHari+1].'7');
				$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$nHari+1])->setWidth(12);
				$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$nHari+2].'5','KET');
				$objPHPExcel->getActiveSheet()->mergeCells($arrHuruf[$nHari+2].'5:'.$arrHuruf[$nHari+2].'7');
				$objPHPExcel->getActiveSheet()->getColumnDimension($arrHuruf[$nHari+2])->setWidth(9);
				$i=8;
				$n=1;
				$arrSmt=array();
				$arrMk=array();
				$arrSmt[0]="-";
				$arrMk[0]="-";
				$nMk=0;
				foreach ($getReportH as $data){
					$arrMk[$n]=$data['id_mk_kurikulum'];
					if($arrMk[$n]!=$arrMk[$n-1]){
						$nMk++;
					}else {
						$objPHPExcel->getActiveSheet()->mergeCells('A'.($i-1).':A'.$i);
						$objPHPExcel->getActiveSheet()->getStyle('A'.($i-1).':A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('A'.($i-1).':A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					}
					$arrSmt[$n]=$data['smt_def'];
					if($arrSmt[$n]!=$arrSmt[$n-1]){
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,"SEMESTER ".$data['smt_def']);
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':'.$arrHuruf[$nHari+2].$i);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
						$i++;
					}
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$nMk);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$data['nm_dosen']);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$data['nm_mk']);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$data['nm_kelas']);
					$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$nHari+1].$i,$data['n']);
					$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$nHari+1].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$h_isi=1;
					foreach ($period as $dtPeriod){
						$x=0;
						foreach ($getReportD as $dataDtl){
							if(($data['kd_paket_kelas']==$dataDtl['kd_paket_kelas'])and($dataDtl['tgl_kuliah']==$dtPeriod)){
								$x=$x+$dataDtl['n'];
							}
						}
						if($x>0){
							$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$h_isi].$i,$x);
							$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$h_isi].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						}
						// red for sunday
						$day=date("D",strtotime($dtPeriod));
						if($day=='Sun'){
							$objPHPExcel->getActiveSheet()->getStyle($arrHuruf[$h_isi].$i)->applyFromArray($red_color);
						}
						$h_isi++;
					}
					$n++;
					$i++;
				}
					
				$objPHPExcel->getActiveSheet()->getStyle('A5:'.$arrHuruf[$nHari+2].($i-1))->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('A5:'.$arrHuruf[$nHari+2].($i-1))->getFont()->setSize(10);
				$objPHPExcel->getActiveSheet()->getStyle('A5:'.$arrHuruf[$nHari+2].'7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A5:'.$arrHuruf[$nHari+2].'7')->applyFromArray($cell_color);
				$objPHPExcel->getActiveSheet()->getStyle('A5:'.$arrHuruf[$nHari+2].$i)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle('E5:'.$arrHuruf[$nHari+2].'7')->getFont()->setBold(true);
				// Redirect output to a client’s web browser (Excel5)
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="Rekap Absensi Dosen.xls"');
				header('Cache-Control: max-age=0');
					
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$objWriter->save('php://output');
			}else{
				// get periode akademik
				$periode=new Periode();
				$getPeriode=$periode->getPeriodeByTgl($tgl1);
				$kd_periode="";
				foreach ($getPeriode as $dtPeriode){
					$kd_periode=$dtPeriode['kd_periode'];
				}
				// array tanggal
				$begin = new DateTime($tgl1);
				$end = new DateTime(date("Y-m-d",strtotime("+1 day", strtotime($tgl2))));
				$period=array();
				while($begin < $end) {
					$period[] = $begin->format('Y-m-d');
					$month[] = $begin->format('M y')."||".$begin->format('Y-m');
					$begin->modify('+1 day');
				}
				// destroy session param export
				Zend_Session::namespaceUnset('param_kbmrekapdosen_erep');
				$rep = new Report();
				// data
				$getTabelData=$rep->getTabel('kuliah');
				$arrTabelData=explode("||", $getTabelData);
				// where data
				$whereD[0]['key'] = 'kd_prodi_kur';
				$whereD[0]['param'] = $prd;
				$whereD[1]['key'] = 'tgl_kuliah';
				$whereD[1]['param'] = $period;
				//--
				$arrKolomD=array('kd_paket_kelas','kd_dosen','nm_dosen','kode_mk','nm_mk','sks_tm','sks_prak','sks_prak_lap','sks_sim','id_nm_kelas','nm_kelas','jns_kelas');
				$arrOrderD=array('kd_dosen','nm_dosen','kode_mk','nm_kelas');
				$getReportD= $rep->get($arrTabelData[0], $arrKolomD, $arrKolomD, $arrOrderD,$whereD);
				// writing excel
				// konfigurasi excel
				PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
				$objPHPExcel = new PHPExcel();
				$objPHPExcel->getProperties()->setCreator("Administrator")
				->setLastModifiedBy("Akademik")
				->setTitle("Export Data Absensi Dosen")
				->setSubject("Sistem Informasi Akademik")
				->setDescription("Data Absensi")
				->setKeywords("absensi")
				->setCategory("Data File");
				
				// Rename sheet
				$objPHPExcel->getActiveSheet()->setTitle('Daftar Absensi Kumulatif');
				$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT)
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
				$objPHPExcel->getActiveSheet()->mergeCells('A1:C1');
				$objPHPExcel->getActiveSheet()->mergeCells('A2:C2');
				$objPHPExcel->getActiveSheet()->mergeCells('A4:C4');
				$objPHPExcel->getActiveSheet()->getStyle('A1:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objPHPExcel->getActiveSheet()->getStyle('B:C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('D:G')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A1:G5')->getFont()->setSize(12);
				$objPHPExcel->getActiveSheet()->getStyle('A1:G5')->getFont()->setBold(true);
				
				// column width
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4.5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
				// insert data to excel
				$objPHPExcel->getActiveSheet()->setCellValue('A1','REKAPITULASI KEHADIRAN DOSEN');
				$objPHPExcel->getActiveSheet()->setCellValue('A2','SEMESTER '.$kd_periode);
				$objPHPExcel->getActiveSheet()->setCellValue('A4','PERIODE '.$tgl1.' s/d '.$tgl2);
				$objPHPExcel->getActiveSheet()->setCellValue('A5','NO');
				$objPHPExcel->getActiveSheet()->setCellValue('B5','DOSEN');
				$objPHPExcel->getActiveSheet()->setCellValue('C5','MATA KULIAH');
				$objPHPExcel->getActiveSheet()->setCellValue('D5','KELAS');
				$objPHPExcel->getActiveSheet()->setCellValue('E5','SKS');
				$objPHPExcel->getActiveSheet()->setCellValue('F5','JUMLAH SKS');
				$objPHPExcel->getActiveSheet()->setCellValue('G5','KEHADIRAN');
				$objPHPExcel->getActiveSheet()->getStyle('A5:G5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$i=6;
				$n=1;
				$arrDsn=array();
				$arrDsn[0]="-";
				$totSks=array();
				$rowAwalDsn=array();
				$rowAhirDsn=array();
				$nDsn=0;
				foreach ($getReportD as $data){
					$arrDsn[$n]=$data['kd_dosen'];
					if($arrDsn[$n]!=$arrDsn[$n-1]){
						$nDsn++;
						$rowAwalDsn[$nDsn]=$i;
						$rowAhirDsn[$nDsn]=$rowAwalDsn;
						$totSks[$nDsn]=($data['sks_tm']+$data['sks_prak']+$data['sks_prak_lap']+$data['sks_sim']);
					}else {
						$objPHPExcel->getActiveSheet()->mergeCells('A'.($i-1).':A'.$i);
						$objPHPExcel->getActiveSheet()->getStyle('A'.($i-1).':A'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('A'.($i-1).':A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->mergeCells('F'.($i-1).':F'.$i);
						$objPHPExcel->getActiveSheet()->getStyle('F'.($i-1).':F'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('F'.($i-1).':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$totSks[$nDsn]=$totSks[$nDsn]+($data['sks_tm']+$data['sks_prak']+$data['sks_prak_lap']+$data['sks_sim']);
						$rowAhirDsn[$nDsn]++;
					}
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$nDsn);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$data['nm_dosen']);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$data['nm_mk']);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$data['nm_kelas']);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,($data['sks_tm']+$data['sks_prak']+$data['sks_prak_lap']+$data['sks_sim']));
					$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$data['n']);
					$n++;
					$i++;
				}
				
				for($nDs=1;$nDs<=$nDsn;$nDs++){
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$rowAwalDsn[$nDs],$totSks[$nDs]);
				}
				$objPHPExcel->getActiveSheet()->getStyle('A5:G'.$i)->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('A5:G'.$i)->getAlignment()->setWrapText(true);
				// Redirect output to a client’s web browser (Excel5)
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="Rekap Absensi Dosen.xls"');
				header('Cache-Control: max-age=0');
					
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$objWriter->save('php://output');
			}
		}
	}
}