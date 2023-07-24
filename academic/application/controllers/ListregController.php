<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Listreg Controller -> Controller untuk modul halaman list regstrasi
*/
class ListregController extends Zend_Controller_Action
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
		Zend_Loader::loadClass('Register');
		Zend_Loader::loadClass('StatReg');
		Zend_Loader::loadClass('Report');
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
		$this->view->active_tree="05";
		$this->view->active_menu="listreg/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "listreg/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Registrasi Akademik Mahasiswa";
			// destroy session param
			Zend_Session::namespaceUnset('param_listreg');
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			$periode = new Periode();
			$this->view->listPeriode = $periode->fetchAll();
			$angkatan = new Angkatan();
			$this->view->listAngkt = $angkatan->fetchAll();
			$prodi = new Prodi();
			$this->view->listProdi= $prodi->fetchAll();
			$statReg = new StatReg();
			$this->view->listStatReg= $statReg ->fetchAll();
		}
	}

	function listAction(){
		$user = new Menu();
		$menu = "listreg/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// show data
			$param = new Zend_Session_Namespace('param_listreg');
			$kd_prodi = $param->prd;
			$kd_periode = $param->per;
			$id_angkatan = $param->akt;
			$stat_reg = $param->stat;
			// get data prodi
			$nm_prd="";
			// Title Browser
			$nm_angkatan=implode(", ",$id_angkatan);
			$nm_prd=implode(", ",$kd_prodi);
			$this->view->title = "Daftar Registrasi Akademik ".$kd_periode." Angkatan ".$nm_angkatan." Prodi ".$nm_prd;
			$this->view->kd_periode=$kd_periode;
			$this->view->id_akt=$id_angkatan;
			$this->view->nm_prd=$nm_prd;
			// navigation
			$this->_helper->navbar('listreg',0,0,'listreg/export',0);
			// get data 
			$statReg = new StatReg();
			$this->view->listStatReg = $statReg->fetchAll();
			$register = new Register();
			$getRegister = $register->getRegisterByPeriodeAngkatanProdiStatus($kd_periode,$id_angkatan,$kd_prodi,$stat_reg);
			$this->view->listRegister = $getRegister;
			// session for export
			$param->listRegister=$getRegister;
			$param->nmPrd=$nm_prd;
			$param->nm_akt=$nm_angkatan;
		}
	}

	function exportAction(){
		$user = new Menu();
		$menu = "register/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// session
			$param = new Zend_Session_Namespace('param_listreg');
			$kd_prodi = $param->prd;
			$kd_periode = $param->per;
			$id_angkatan = $param->akt;
			$nm_angkatan = $param->nm_akt;
			$nm_prd = $param->nmPrd;
			$listReg=$param->listRegister;
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt=$ses_ac->nm_pt;
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Administrator")
										 ->setLastModifiedBy("Akademik")
										 ->setTitle("Export Data Her Registrasi")
										 ->setSubject("Sistem Informasi Akademik")
										 ->setDescription("Data Her Registrasi")
										 ->setKeywords("her registrasi")
										 ->setCategory("Data File");
										 
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Daftar Her Registrasi');
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
			$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
			$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A3:G3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A:B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('D:F')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A3:G3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A3:G3')->getFont()->setSize(11);
			$objPHPExcel->getActiveSheet()->getStyle('A3:G3')->applyFromArray($cell_color);
			
			// column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
			// insert data to excel
			$objPHPExcel->getActiveSheet()->setCellValue('A1','DAFTAR REGISTRASI MAHASISWA '.strtoupper($nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('A2','ANGKATAN '.$nm_angkatan." PROGRAM STUDI ".$nm_prd." ".$kd_periode);
			$objPHPExcel->getActiveSheet()->setCellValue('A3','NO');
			$objPHPExcel->getActiveSheet()->setCellValue('B3','NIM');
			$objPHPExcel->getActiveSheet()->setCellValue('C3','NAMA MAHASISWA');
			$objPHPExcel->getActiveSheet()->setCellValue('D3','STATUS MAHASISWA');
			$objPHPExcel->getActiveSheet()->setCellValue('E3','STATUS REGISTRASI');
			$objPHPExcel->getActiveSheet()->setCellValue('F3','SKS APPROVED');
			$objPHPExcel->getActiveSheet()->setCellValue('G3','DOSEN WALI');
			$i=4;
			$n=1;
			foreach ($listReg as $data){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
				$objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$i,$data['nim'],PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$data['nim']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$data['nm_mhs']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$data['status_mhs']);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$data['status_reg']);
				if($data['kd_status_reg']==null){
					if($data['id_jns_keluar']==''){
						$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,"UNREGISTERED");
						$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->applyFromArray($cell_color);
					}else{
						$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,"-");
					}
				}
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$data['sks_app']."/".$data['sks_krs']);
				if($data['sks_app']!=$data['sks_krs']){
					$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($cell_color);
				}
				if(($data['krs']=='t')and($data['sks_krs']==0)){
					$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($cell_color);
				}
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$data['nm_dosen_wali']);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				$n++;
				$i++;				
			}
			$objPHPExcel->getActiveSheet()->getStyle('A3:G'.($i-1))->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('A4:G'.($i-1))->getFont()->setSize(10);
			
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Daftar Registrasi Mahasiswa.xls"');
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}
}