<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Alumni Controller -> Controller untuk modul halaman alumni
*/
class AlumniController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Alumni');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('ProdiInfo0');
		Zend_Loader::loadClass('ProdiCapaianPtMajor');
		Zend_Loader::loadClass('ProdiCapaianPtMinor');
		Zend_Loader::loadClass('ProdiCapaianPtOther');
		Zend_Loader::loadClass('ProdiSkpiLabel');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Validation');
		Zend_Loader::loadClass('PHPExcel');
		Zend_Loader::loadClass('PHPExcel_Cell_AdvancedValueBinder');
		Zend_Loader::loadClass('PHPExcel_IOFactory');
		Zend_Loader::loadClass('PHPExcel_RichText');
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
		$this->view->active_tree="10";
		$this->view->active_menu="alumni/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "alumni/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Alumni (Lulusan)";
			// navigation
			$this->_helper->navbar(0,0,'alumni/new',0,'alumni/upload');
			// destroy session param
			Zend_Session::namespaceUnset('param_alm');
			// get data angkatan
			$akt = new Angkatan();
			$this->view->listAkt=$akt->fetchAll();
			// get data prodi
			$prod = new Prodi();
			$this->view->listProdi=$prod->fetchAll();
		}
	}

	function listAction(){
		$user = new Menu();
		$menu = "alumni/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// show data
			$param = new Zend_Session_Namespace('param_alm');
			$akt = $param->akt;
			$prd = $param->prd;
			$startdate = $param->startdate;
			$enddate = $param->enddate;
			// get data angkatan
			$angkatan = new Angkatan();
			$listAkt=$angkatan->fetchAll();
			if(!$akt){
				$angk="SEMUA";
				$this->view->akt=$angk;
			}else{
				$angk="";
				foreach ($listAkt as $dataAkt) {
					foreach ($akt as $dt) {
						if($dt==$dataAkt['id_angkatan']){
							$angk=$dataAkt['id_angkatan'].", ".$angk;
						}
					}
				}
				$this->view->akt=$angk;
			}
			// get data prodi
			$prod = new Prodi();
			$listProdi=$prod->fetchAll();
			if(!$prd){
				$prod="SEMUA";
				$this->view->prd=$prod;
			}else{
				$prod="";
				foreach ($listProdi as $dataPrd) {
					foreach ($prd as $dt) {
						if($dt==$dataPrd['kd_prodi']){
							$prod=$dataPrd['nm_prodi'].", ".$prod;
						}
					}
				}
				$this->view->prd=$prod;
			}
			$this->view->startdate=$startdate;
			$this->view->enddate=$enddate;
			// Title Browser
			$this->view->title = "Daftar Alumni (Lulusan)";
			// navigation
			$this->_helper->navbar('alumni',0,'alumni/new','alumni/export','alumni/upload');
			// get data 
			$alumni = new Alumni();
			 $getAlumni=$alumni->getAlumniByAngkatanProdiTanggal($akt,$prd,$startdate,$enddate);
			 $this->view->listAlumni=$getAlumni;
			// create session for excel
			$param->data=$getAlumni;
			$param->v_prd=$prod;
			$param->v_akt=$angk;
		}
	}

	function newAction(){
		$user = new Menu();
		$menu = "alumni/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Input Alumni (Lulusan)";
			// navigation
			$this->_helper->navbar(0,'alumni',0,0,'alumni/upload');
			// get data angkatan
			$akt = new Angkatan();
			$this->view->listAkt=$akt->fetchAll();
			// get data prodi
			$prod = new Prodi();
			$this->view->listProdi=$prod->fetchAll();
		}
	}

	function editAction(){
		$user = new Menu();
		$menu = "alumni/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get id
			$nim = $this->_request->get('nim');
			// get data alumni
			$alumni = new Alumni();
			$getAlumni = $alumni->getAlumniByNim($nim);
			if($getAlumni){
				foreach ($getAlumni as $dtAlumni) {
					$nm_mhs=$dtAlumni['nm_mhs'];
					$this->view->akt=$dtAlumni['id_angkatan'];
					$this->view->prd=$dtAlumni['kd_prodi'];
					$this->view->nim=$dtAlumni['nim'];
					$this->view->nm_mhs=$dtAlumni['nm_mhs'];
					if($dtAlumni['tgl_keluar']){
						$this->view->tgllulus=$dtAlumni['tgl_keluar_fmt'];	
					}else{
						$this->view->tgllulus="";
					}
					$this->view->nosk=$dtAlumni['sk_yudisium'];
					if($dtAlumni['tgl_sk_yudisium']){
						$this->view->tglsk=$dtAlumni['tgl_sk_yudisium_fmt'];
					}else{
						$this->view->tglsk="";	
					}
					$this->view->noijz=$dtAlumni['no_ijazah'];
					$this->view->judul=$dtAlumni['judul_skripsi'];
					$this->view->ipk=$dtAlumni['ipk'];
				}
				// Title Browser
				$this->view->title = "Edit Alumni (Lulusan) ".$nm_mhs;
				// get data angkatan
				$akt = new Angkatan();
				$this->view->listAkt=$akt->fetchAll();
				// get data prodi
				$prod = new Prodi();
				$this->view->listProdi=$prod->fetchAll();
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Edit Alumni (Lulusan) ";
			}
			// navigation
			$this->_helper->navbar('alumni/list',0,0,0,'alumni/upload');
		}
	}
	
	function uploadAction(){
		$user = new Menu();
		$menu = "alumni/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Upload Data Alumni (Lulusan)";
			// navigation
			$this->_helper->navbar(0,'alumni',0,0,0);
			// get data angkatan
			$akt = new Angkatan();
			$this->view->listAkt=$akt->fetchAll();
			// get data prodi
			$prod = new Prodi();
			$this->view->listProdi=$prod->fetchAll();
		}
	}
	
	function exportAction(){
		$user = new Menu();
		$menu = "alumni/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// session
			$param = new Zend_Session_Namespace('param_alm');
			$dataAlm = $param->data;
			$prd=$param->v_prd;
			$akt=$param->v_akt;
			$startdate=$param->startdate;
			$enddate=$param->enddate;
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt=$ses_ac->nm_pt;
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Administrator")
										 ->setLastModifiedBy("Akademik")
										 ->setTitle("Export Data Alumni")
										 ->setSubject("Sistem Informasi Akademik")
										 ->setDescription("Data Alumni")
										 ->setKeywords("alumni")
										 ->setCategory("Data File");
										 
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Daftar Alumni Lulusan');
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
			$objPHPExcel->getActiveSheet()->mergeCells('A1:L1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:L2');
			$objPHPExcel->getActiveSheet()->getStyle('A1:L1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A2:L2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('A3:L3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A:B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1:L1')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('A1:L1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A2:L2')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A2:L2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A3:L3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A3:L3')->getFont()->setSize(11);
			$objPHPExcel->getActiveSheet()->getStyle('A3:L3')->applyFromArray($cell_color);
			
			// column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(8);
			// insert data to excel
			$objPHPExcel->getActiveSheet()->setCellValue('A1','DAFTAR LULUSAN '.strtoupper($nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('A2','PROGRAM STUDI : '.$prd.", ANGKATAN : ".$akt." TANGGAL : ".$startdate." s/d ".$enddate);
			$objPHPExcel->getActiveSheet()->setCellValue('A3','NO');
			$objPHPExcel->getActiveSheet()->setCellValue('B3','NIM');
			$objPHPExcel->getActiveSheet()->setCellValue('C3','NAMA MAHASISWA');
			$objPHPExcel->getActiveSheet()->setCellValue('D3','L/P');
			$objPHPExcel->getActiveSheet()->setCellValue('E3','TEMPAT, TANGGAL LAHIR');
			$objPHPExcel->getActiveSheet()->setCellValue('F3','ANGKATAN');
			$objPHPExcel->getActiveSheet()->setCellValue('G3','PRODI');
			$objPHPExcel->getActiveSheet()->setCellValue('H3','TANGGAL LULUS');
			$objPHPExcel->getActiveSheet()->setCellValue('I3','NO.IJAZAH');
			$objPHPExcel->getActiveSheet()->setCellValue('J3','NO.SK');
			$objPHPExcel->getActiveSheet()->setCellValue('K3','TANGGAL SK');
			$objPHPExcel->getActiveSheet()->setCellValue('L3','IPK');
			$i=4;
			$n=1;
			foreach ($dataAlm as $data){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
				$objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$i,$data['nim'],PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$data['nim']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$data['nm_mhs']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$data['jenis_kelamin']);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$data['tmp_lahir'].', '.$data['tgl_lahir_fmt']);	
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$data['id_angkatan']);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$data['nm_prodi']);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$data['tgl_keluar_fmt']);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$data['no_ijazah']);
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$data['sk_yudisium']);
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,$data['tgl_sk_yudisium_fmt']);
				$objPHPExcel->getActiveSheet()->setCellValue('L'.$i,$data['ipk']);
				$objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':L'.$i)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':L'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				$n++;	
				$i++;				
			}
			$objPHPExcel->getActiveSheet()->getStyle('A3:L'.($i-1))->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('A4:L'.($i-1))->getFont()->setSize(10);
			
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Daftar Lulusan.xls"');
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');	
		}
	}

	function exportskpiAction(){
		$user = new Menu();
		$menu = "alumni/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// session
			$param = new Zend_Session_Namespace('param_alm');
			$dataAlm = $param->data;
			$prd=$param->v_prd;
			$akt=$param->v_akt;
			$startdate=$param->startdate;
			$enddate=$param->enddate;
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt=$ses_ac->nm_pt;
			// image path inst
			$path = __FILE__;
			$iPath = str_replace('academic/application/controllers/AlumniController.php','public/img/logo.png',$path);
			$yPath = str_replace('academic/application/controllers/AlumniController.php','public/img/yayasan.png',$path);
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Administrator")
										 ->setLastModifiedBy("Akademik")
										 ->setTitle("Export Data SKPI")
										 ->setSubject("Sistem Informasi Akademik")
										 ->setDescription("Data SKPI")
										 ->setKeywords("alumni")
										 ->setCategory("Data File");
										 
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Daftar Alumni Lulusan');
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT)
														  ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO)
														  ->setFitToWidth('1')
														  ->setFitToHeight('Automatic')
														  ->SetHorizontalCentered(true);
			
			// margin is set in inches (0.5cm)
			$margin = 1 / 2.54;
			$leftMargin = 0.75 / 2.54;											  
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop($margin)
															->setBottom($margin)
															->setLeft($leftMargin)
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
			        'color' => array('rgb'=>'DBE5F1')
			    ),
			);
			
			// column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(3);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(36);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(3);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(3);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(53);
			$i=1;
			$n=1;
			foreach ($dataAlm as $data){
				if(file_exists($yPath)){
					$objDrawing = new PHPExcel_Worksheet_Drawing();
					$objDrawing->setName('Logo1');
					$objDrawing->setDescription('Logo1');
					$objDrawing->setPath($yPath);
					$objDrawing->setHeight(70);
					$objDrawing->setWidth(90);
					$objDrawing->setCoordinates('B'.$i);
					$objDrawing->setOffsetX(0);
					$objDrawing->setOffsetY(0);
					$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
				}
				if(file_exists($iPath)){
					$objDrawing = new PHPExcel_Worksheet_Drawing();
					$objDrawing->setName('Logo2');
					$objDrawing->setDescription('Logo2');
					$objDrawing->setPath($iPath);
					$objDrawing->setHeight(70);
					$objDrawing->setWidth(90);
					$objDrawing->setCoordinates('F'.$i);
					$objDrawing->setOffsetX(260);
					$objDrawing->setOffsetY(0);
					$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
				}
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.($i+7))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(11);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.($i+7))->getFont()->setBold(true);
				$styleArrayPT = array(
					'font'  => array(
					        'name'  => 'Arial Black'
					    ));
				$styleArrayTtl1 = array(
					'font'  => array(
					        'name'  => 'Century',
						'color' => array('rgb' => '00008B')
					    ));
				$styleArrayTtl2 = array(
					'font'  => array(
					        'name'  => 'Century'
					    ));
				// insert data to excel
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':F'.$i);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'Yayasan Hazanah');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleArrayPT);
				$i++;
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':F'.$i);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,strtoupper($nm_pt));
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i,':F'.$i)->getFont()->setSize(16);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleArrayPT);
				$i++;
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':F'.$i);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'Terakreditasi AIPT Nomor: 2711/SK/BAN-PT/Akred/PT/VIII/2017');
				// $objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'Terakreditasi AIPT Nomor: 0683/LAM-PTKes/Akr/Sar/XII/2020');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(10);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleArrayTtl2);
				$i=$i+2;
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':F'.$i);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'SURAT KETERANGAN PENDAMPING IJAZAH');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(16);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleArrayTtl1);
				$i++;
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':F'.$i);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'Diploma Supplement');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(14);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setItalic(true);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleArrayTtl2);
				$i++;
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':F'.$i);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'No.');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(14);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleArrayTtl2);
				$i=$i+2;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'Informasi Pemegang SKPI');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setSize(12);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
				$i++;
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText('Nama Lengkap /');
				$objItalic = $objRichText->createTextRun('Full name(s)');
				$objItalic->getFont()->setItalic(true)->setBold(true);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$objRichText);
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText('Nomor Pokok Mahasiswa /');
				$objItalic = $objRichText->createTextRun('Student Identification Number');
				$objItalic->getFont()->setItalic(true)->setBold(true);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$objRichText);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setBold(true);
				$i++;
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,ucwords(strtolower($data['nm_mhs'])));
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$data['nim']);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':F'.$i)->applyFromArray($cell_color);
				$i++;
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText('Tempat Tanggal Lahir /');
				$objItalic = $objRichText->createTextRun('Place and Date of Birth');
				$objItalic->getFont()->setItalic(true)->setBold(true);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$objRichText);
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText('Lama Studi /');
				$objItalic = $objRichText->createTextRun('Length of Study');
				$objItalic->getFont()->setItalic(true)->setBold(true);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$objRichText);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setBold(true);
				$i++;
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,ucwords(strtolower($data['tmp_lahir'])).', '.$data['tgl_lahir_fmt']);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$data['masa_studi']);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':F'.$i)->applyFromArray($cell_color);
				$i++;
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText('Tanggal Masuk /');
				$objItalic = $objRichText->createTextRun('Date of Entry');
				$objItalic->getFont()->setItalic(true)->setBold(true);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$objRichText);
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText('Tanggal Lulus /');
				$objItalic = $objRichText->createTextRun('Date of Graduation');
				$objItalic->getFont()->setItalic(true)->setBold(true);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$objRichText);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setBold(true);
				$i++;
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$data['tgl_masuk_fmt']);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$data['tgl_keluar_fmt']);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':F'.$i)->applyFromArray($cell_color);
				$i++;
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText('Nomor Ijazah /');
				$objItalic = $objRichText->createTextRun('Certificate Number');
				$objItalic->getFont()->setItalic(true)->setBold(true);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$objRichText);
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText('Gelar yang diperoleh /');
				$objItalic = $objRichText->createTextRun('Title Conferred');
				$objItalic->getFont()->setItalic(true)->setBold(true);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$objRichText);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setBold(true);
				$i++;
				$objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$i,$data['no_ijazah'],PHPExcel_Cell_DataType::TYPE_STRING);
				$prodiInfo=new ProdiInfo0();
				$getProdiInfo = $prodiInfo->getProdiInfo0ByKd($data['kd_prodi']);
				$gelar_id="";
				$gelar_en="";
				$lanjut_id="";
				$lanjut_en="";
				$nm_prd_en="";
				$jns_id="";
				$jns_en="";
				$req_id="";
				$req_en="";
				$bhs_id="";
				$bhs_en="";
				foreach($getProdiInfo as $dtProdiInfo){
					$gelar_id=$dtProdiInfo['gelar_id'];
					$gelar_en=$dtProdiInfo['gelar_en'];
					$lanjut_id=$dtProdiInfo['studi_lanjut_id'];
					$lanjut_en=$dtProdiInfo['studi_lanjut_en'];
					$nm_prd_en=$dtProdiInfo['nm_prodi_en'];
					$jns_id=$dtProdiInfo['jenis_pend_id'];
					$jns_en=$dtProdiInfo['jenis_pend_en'];
					$req_id=$dtProdiInfo['req_pend_id'];
					$req_en=$dtProdiInfo['req_pend_en'];
					$bhs_id=$dtProdiInfo['bahasa_id'];
					$bhs_en=$dtProdiInfo['bahasa_en'];
				}
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText($gelar_id.' /');
				$objItalic = $objRichText->createTextRun($gelar_en);
				$objItalic->getFont()->setItalic(true);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$objRichText);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':F'.$i)->applyFromArray($cell_color);
				$i++;
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText('Jenis dan Program Pendidikan Tinggi Lanjutan /');
				$objItalic = $objRichText->createTextRun('Acces to Futher Education :');
				$objItalic->getFont()->setItalic(true)->setBold(true);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$objRichText);
				$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':F'.$i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setBold(true);
				$i++;
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText($lanjut_id.' /');
				$objItalic = $objRichText->createTextRun($lanjut_en);
				$objItalic->getFont()->setItalic(true);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$objRichText);
				$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':F'.$i);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':F'.$i)->applyFromArray($cell_color);
				$i=$i+2;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'Informasi Penyelenggara Program');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setSize(12);
				$i++;
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText('Program Studi /');
				$objItalic = $objRichText->createTextRun('Study Program');
				$objItalic->getFont()->setItalic(true)->setBold(true);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$objRichText);
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText('Akreditasi Program Studi/Nomor SK Program Studi');
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$objRichText);
				$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':F'.$i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setBold(true);
				$i++;
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText(ucwords(strtolower($data['nm_prodi'])).' /');
				$objItalic = $objRichText->createTextRun(ucwords(strtolower($nm_prd_en)));
				$objItalic->getFont()->setItalic(true);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$objRichText);
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
				$objRichText = new PHPExcel_RichText();
				// $objRichText->createText('B/ No. 0158/LAM-PTKes/Akr/Sar/ IX/ 2015');
				$objRichText->createText('B/ No. 0683/LAM-PTKes/Akr/Sar/XII/2020');
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$objRichText);
				$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':F'.$i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray($cell_color);
				$i++;
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText('Jenis Pendidikan /');
				$objItalic = $objRichText->createTextRun('Type of Education');
				$objItalic->getFont()->setItalic(true)->setBold(true);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$objRichText);
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText('Tingkat KKNI / ');
				$objItalic = $objRichText->createTextRun('INQF Level');
				$objItalic->getFont()->setItalic(true)->setBold(true);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$objRichText);
				$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':F'.$i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setBold(true);
				$i++;
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText(ucwords(strtolower($jns_id)).' /');
				$objItalic = $objRichText->createTextRun(ucwords(strtolower($jns_en)));
				$objItalic->getFont()->setItalic(true);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$objRichText);
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText('6(Six)');
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$objRichText);
				$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':F'.$i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray($cell_color);
				$i++;
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText('Persyaratan Penerimaan /');
				$objItalic = $objRichText->createTextRun('Entry Requirements');
				$objItalic->getFont()->setItalic(true)->setBold(true);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$objRichText);
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText('Bahasa Pengantar / ');
				$objItalic = $objRichText->createTextRun('Language(s) of Instruction');
				$objItalic->getFont()->setItalic(true)->setBold(true);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$objRichText);
				$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':F'.$i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setBold(true);
				$i++;
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText($req_id.' /');
				$objItalic = $objRichText->createTextRun($req_en);
				$objItalic->getFont()->setItalic(true);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$objRichText);
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
				$objRichText = new PHPExcel_RichText();
				$objRichText->createText(ucwords(strtolower($bhs_id)).' /');				
				$objItalic = $objRichText->createTextRun(ucwords(strtolower($bhs_en)));
				$objItalic->getFont()->setItalic(true);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$objRichText);
				$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':F'.$i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray($cell_color);
				$i=$i+2;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'Kompetensi');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setSize(12);
				$i++;
				$label=new ProdiSkpiLabel();
				$getLabel=$label->getProdiSkpiLabelByKd($data['kd_prodi']);
				$id_label="";
				if ($getLabel){
					foreach($getLabel as $dtLabel){
						$id_label=$dtLabel['id_prodi_skpi_label'];
					}
				}
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'Kompetensi Utama');
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,'Major Competencies');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setItalic(true);
				$i++;
				$mayor = new ProdiCapaianPtMajor();
				$getMayor=$mayor->getProdiCapaianPtMajorBySkpiLabel($id_label);
				$x=1;
				foreach($getMayor as $dtMayor){
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$x);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$dtMayor['keterangan_id']);
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$x);
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$dtMayor['keterangan_en']);
					$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setItalic(true);	
					$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$i++;
					$x++;
				}
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'Kompetensi Pendukung');
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,'Minor Competencies');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setItalic(true);
				$i++;
				$minor = new ProdiCapaianPtMinor();
				$getMinor=$minor->getProdiCapaianPtMinorBySkpiLabel($id_label);
				$y=1;
				foreach($getMinor as $dtMinor){
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$y);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$dtMinor['keterangan_id']);
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$y);
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$dtMinor['keterangan_en']);
					$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setItalic(true);	
					$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$i++;
					$y++;
				}
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'Kompetensi Lainnya');
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,'Other Competencies');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setItalic(true);
				$i++;
				$other = new ProdiCapaianPtOther();
				$getOther=$other->getProdiCapaianPtOtherBySkpiLabel($id_label);
				$z=1;
				foreach($getOther as $dtOther){
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$z);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$dtOther['keterangan_id']);
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$z);
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$dtOther['keterangan_en']);
					$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setItalic(true);	
					$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$i++;
					$z++;
				}
				$n++;
				$i++;
			}
			$styleDefault = array('font'  => array('name'  => 'Calibri'));
			$objPHPExcel->getDefaultStyle()->applyFromArray($styleDefault);
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Daftar SKPI.xlsx"');
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');	
		}
	}
}