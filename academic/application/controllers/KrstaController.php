<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: KRS TA Controller -> Controller untuk modul halaman KRS TA
*/
class KrstaController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('PaketkelasTA');
		Zend_Loader::loadClass('KuliahTA');
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
	}

	function indexAction(){
		$user = new Menu();
		$menu = "krsta/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "KRS Mata Kuliah TA";
			// treeview
			$this->view->active_tree="09";
			$this->view->active_menu="krsta/index";
			// get data angkatan
			$akt = new Angkatan();
			$this->view->listAkt=$akt->fetchAll();
			// get data prodi
			$prod = new Prodi();
			$this->view->listProdi=$prod->fetchAll();
			$periode = new Periode();
			$this->view->listPeriode=$periode->fetchAll();
			$getPerAktif=$periode->getPeriodeByStatus(0);
			foreach ($getPerAktif as $dtPerAktif) {
				$per_aktif=$dtPerAktif['kd_periode'];
			}
			$this->view->per_aktif=$per_aktif;
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
		}
	}
	
	function newAction()
	{
		$user = new Menu();
		$menu = "krsta/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get nim periode
			$nim = $this->_request->get('nim');
			$kd_periode = $this->_request->get('per');
			$back = $this->_request->get('back');
			// get register
			$register = new Register();
			$getRegister = $register->getRegisterByNimPeriode($nim,$kd_periode);
			if($getRegister){
				foreach ($getRegister as $dtReg) {
					$this->view->nim=$dtReg['nim'];
					$this->view->nm_mhs=$dtReg['nm_mhs'];
					$this->view->akt=$dtReg['id_angkatan'];
					$this->view->prd=$dtReg['nm_prodi'];
					$this->view->dw=$dtReg['nm_dosen_wali'];
					$kd_prodi=$dtReg['kd_prodi'];
					$this->view->per=$dtReg['kd_periode'];
					$krs=$dtReg['krs'];
					$nm_mhs=$dtReg['nm_mhs'];
				}
				if($krs=='f'){
					// Title Browser
					$this->view->title = "KRS TA Mahasiswa";
					$this->view->eksis="f";
				}else{
					// Title Browser
					$this->view->title = "KRS TA Mahasiswa ".$nm_mhs;
					$paketkelasta = new PaketkelasTA();
					$this->view->listPaketkelasTA= $paketkelasta->getPaketKelasTAByPeriodeProdi($kd_periode,$kd_prodi);
					$kuliahTA = new KuliahTA();
					$getKuliahTA = $kuliahTA->getKuliahTAByNimPeriode($nim,$kd_periode);
					$this->view->listKuliahTA=$getKuliahTA;
					// periode akad aktif + periode TA sebelumnya
					$periode = new Periode();
					$this->view->listPerAktif = $periode->getPeriodeByStatus(0);
					$this->view->listPeriodeTA = $kuliahTA->getKuliahTAByNim($nim);
					// navigation
					if($back=="1"){
						$this->_helper->navbar("krsta",0,0,0,0);
						// treeview
						$this->view->active_tree="09";
						$this->view->active_menu="krsta/index";
					}else{
						$this->_helper->navbar("krs/index?nim=".$nim."&per=".$kd_periode,0,0,0,0);
						// treeview
						$this->view->active_tree="05";
						$this->view->active_menu="register/index";	
					}
				}
			}else{
				// Title Browser
				$this->view->title = "KRS Mahasiswa";
				$this->view->eksis="f";
				// navigation
				if($back=="1"){
					$this->_helper->navbar("krsta",0,0,0,0);	
				}else{
					$this->_helper->navbar("krs",0,0,0,0);	
				}
			}
			
		}
	}

	function reportAction(){
		$user = new Menu();
		$menu = "krsta/report";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="krsta/report";
			// destroy session param export
			Zend_Session::namespaceUnset('param_krsta_erep');
			// session report
			$param = new Zend_Session_Namespace('param_krsta_rep');
			$prd = $param->prd;
			$per = $param->per;
			if($per){
				// layout
				$this->_helper->layout()->setLayout('second');
				// navigation
				$this->_helper->navbar("krsta/report",0,0,"krsta/ereport",0);
			}else {
				// layout
				$this->_helper->layout()->setLayout('main');
				// navigation
				$this->_helper->navbar(0,0,0,0,0);
			}
			// Title Browser
			$this->view->title = "Report KRS Tugas Akhir Mahasiswa";
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
			// get periode
			$periode = new Periode();
			$this->view->listPeriode=$periode->fetchAll();
			if($per){
				$rep = new Report();
				// data
				$getTabelData=$rep->getTabel('mhs_kul_ta');
				$arrTabelData=explode("||", $getTabelData);
				// where data
				$whereD[0]['key'] = 'kd_prodi';
				$whereD[0]['param'] = $prd;
				$whereD[1]['key'] = 'kd_periode';
				$whereD[1]['param'] = $per;
				$whereD[2]['key'] = 'approved';
				$whereD[2]['param'] = 't';
				//--
				$arrKolomD=array('nim','nm_mhs','id_angkatan','nm_prodi','nm_mk','smt_def','sks_tm','nm_dosen_pemb1','nm_dosen_pemb2','judul','no_reg');
				$arrOrder=array('nim','nm_mhs','id_angkatan','nm_mk','smt_def','sks_tm');
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
				$param2 = new Zend_Session_Namespace('param_krsta_erep');
				$param2->data=$getReport;
				$param2->per=$per;
				$param2->prd=$nm_prd;
			}
			// destroy session param
			Zend_Session::namespaceUnset('param_krsta_rep');
		}
	}
	
	function ereportAction(){
		$user = new Menu();
		$menu = "krsta/report";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// session for export
			$param = new Zend_Session_Namespace('param_krsta_erep');
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
										 ->setTitle("Rekap KRS TA")
										 ->setSubject("Sistem Informasi Akademik")
										 ->setDescription("Rekap KRS TA")
										 ->setKeywords("rekap ta")
										 ->setCategory("Data File");
										 
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Rekap KRS TA');
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
			$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:I2');
			$objPHPExcel->getActiveSheet()->mergeCells('A3:I3');
			$objPHPExcel->getActiveSheet()->getStyle('A1:I3')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('A1:I3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(16);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(17);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(60);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(24);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getStyle('A1:I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			// insert data to excel
			$objPHPExcel->getActiveSheet()->setCellValue('A1',strtoupper($nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('A2','REKAP KRS TA MAHASISWA');
			$objPHPExcel->getActiveSheet()->setCellValue('A3','PROGRAM STUDI '.$prd.' PERIODE '.$per);
			// data
			$objPHPExcel->getActiveSheet()->setCellValue('A4','NO');
			$objPHPExcel->getActiveSheet()->setCellValue('B4','NIM');
			$objPHPExcel->getActiveSheet()->setCellValue('C4','NAMA MAHASISWA');
			$objPHPExcel->getActiveSheet()->setCellValue('D4','ANGKATAN');
			$objPHPExcel->getActiveSheet()->setCellValue('E4','MATA KULIAH');
			$objPHPExcel->getActiveSheet()->setCellValue('F4','JUDUL TA');
			$objPHPExcel->getActiveSheet()->setCellValue('G4','NO.REG');
			$objPHPExcel->getActiveSheet()->setCellValue('H4','PEMBIMBING 1');
			$objPHPExcel->getActiveSheet()->setCellValue('I4','PEMBIMBING 2');
			$objPHPExcel->getActiveSheet()->getStyle('A4:I4')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A4:I4')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A4:I4')->applyFromArray($cell_color);
			$objPHPExcel->getActiveSheet()->getStyle('A4:I4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A4:I4')->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('A4:I4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$i=5;
			$n=1;
			$x=1;
			$arrNim[0]="";
		 	$rNim=$i;
			foreach ($dataRep as $data){
				$arrNim[$n]=$data['nim'];
				if($arrNim[$n]!=$arrNim[$n-1]){
					$rNim=$i;
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$x);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$data['nim']);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$data['nm_mhs']);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$data['id_angkatan']);
					$x++;	
				}else{
					$objPHPExcel->getActiveSheet()->mergeCells("A".$rNim.":A".$i);
					$objPHPExcel->getActiveSheet()->mergeCells("B".$rNim.":B".$i);
					$objPHPExcel->getActiveSheet()->mergeCells("C".$rNim.":C".$i);
					$objPHPExcel->getActiveSheet()->mergeCells("D".$rNim.":D".$i);
				}
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$data['nm_mk']);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$data['judul']);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$data['no_reg']);
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$data['nm_dosen_pemb1']);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$data['nm_dosen_pemb2']);
				$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$n++;
				$i++;
			}
			
			//Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Rekap KRS TA.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}
}