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
	
	function indexAction()
	{
		// Title Browser
		$this->view->title = "Daftar Perkuliahan";
		// get kd paket kelas
		$kd_paket=$this->_request->get('id');
		$paketkelas = new Paketkelas();
		$getPaketKelas=$paketkelas->getPaketKelasByKd($kd_paket);
		if($getPaketKelas){
			foreach ($getPaketKelas as $dtPaket) {
				$this->view->kd_paket = $dtPaket['kd_paket_kelas'];
				$pkt=$dtPaket['kd_paket_kelas'];
				$kdKelas=$dtPaket['kd_kelas'];
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
			// navigation
			$this->_helper->navbar('paketkelas/index?kd='.$kdKelas,'kbm/export');
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
			$this->_helper->navbar('kelas',0);
		}
	}

	function editAction()
	{
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
			$this->_helper->navbar('kbm/index?id='.$kd_paket,0,0,0,0);
		}else{
			$this->view->eksis="f";
			// navigation
			$this->_helper->navbar('kbm/list',0,0,0,0);
		}
	}
	
	function exportAction(){
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
		$ses_lec = new Zend_Session_Namespace('ses_lec');
		$nm_pt=$ses_lec->nm_pt;
		// image path stempel
		$path = __FILE__;
		$stmPath = str_replace('lecturer/application/controllers/KbmController.php','public/img/stempel.png',$path);
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
		$ttdPath = str_replace('lecturer/application/controllers/KbmController.php','public/file/dsn/ttd/'.$ttd_kd.'.png',$path);
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