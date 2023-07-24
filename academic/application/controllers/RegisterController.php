<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Register Controller -> Controller untuk modul halaman regstrasi
*/
class RegisterController extends Zend_Controller_Action
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
		$this->view->active_menu="register/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "register/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Periode Akademik Registrasi Akademik Mahasiswa";
			// destroy session param
			Zend_Session::namespaceUnset('param_reg');
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			$periode = new Periode();
			$this->view->listPeriode = $periode->fetchAll();
			$angkatan = new Angkatan();
			$this->view->listAngkt = $angkatan->fetchAll();
			$prodi = new Prodi();
			$this->view->listProdi= $prodi->fetchAll();
		}
	}

	function listAction(){
		$user = new Menu();
		$menu = "register/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// destroy session param
			Zend_Session::namespaceUnset('param_krs');
			// show data
			$param = new Zend_Session_Namespace('param_reg');
			$kd_prodi = $param->prd;
			$kd_periode = $param->per;
			$id_angkatan = $param->akt;
			// get data prodi
			$nm_prd="";
			$nm_jenis="";
			$prodi = new Prodi();
			$listProdi=$prodi->fetchAll();
			foreach ($listProdi as $dataPrd) {
				if($kd_prodi==$dataPrd['kd_prodi']){
					$nm_prd=$dataPrd['nm_prodi'];
				}
			}
			// Title Browser
			$this->view->title = "Daftar Registrasi Akademik ".$nm_prd." (".$id_angkatan.") - ".$kd_periode;
			$this->view->kd_periode=$kd_periode;
			$this->view->id_akt=$id_angkatan;
			$this->view->nm_prd=$nm_prd;
			// navigation
			$this->_helper->navbar('register',0,0,'register/export',0);
			// get data 
			$statReg = new StatReg();
			$this->view->listStatReg = $statReg->fetchAll();
			$register = new Register();
			$getRegister = $register->getRegisterByPeriodeAngkatanProdi($kd_periode,$id_angkatan,$kd_prodi);
			$this->view->listRegister = $getRegister;
			// session for export
			$param->listRegister=$getRegister;
			$param->nmPrd=$nm_prd;
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
			$param = new Zend_Session_Namespace('param_reg');
			$kd_prodi = $param->prd;
			$kd_periode = $param->per;
			$id_angkatan = $param->akt;
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
			$objPHPExcel->getActiveSheet()->setCellValue('A2','ANGKATAN '.$id_angkatan." PROGRAM STUDI ".$nm_prd." ".$kd_periode);
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
	
	function reportAction(){
		$user = new Menu();
		$menu = "register/report";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="register/report";
			// session chart
			$param = new Zend_Session_Namespace('param_reg_chart');
			$akt = $param->akt;
			$prd = $param->prd;
			$per = $param->per;
			$par=$param->par;
			$cht=$param->cht;
			// Title Browser
			$this->view->title = "Report Her-Registrasi ".$per;
			if($cht){
				// layout
				$this->_helper->layout()->setLayout('second');
				// navigation
				$this->_helper->navbar("register/report",0,0,0,0);
			}else {
				// layout
				$this->_helper->layout()->setLayout('main');
				// navigation
				$this->_helper->navbar(0,0,0,0,0);
			}
	
			// get data angkatan
			$angkatan = new Angkatan();
			$this->view->listAkt=$angkatan->fetchAll();
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
			// get periode
			$periode = new Periode();
			$this->view->listPeriode=$periode->fetchAll();
			if($cht){
				$rep = new Report();
				// axis
				$getTabelX=$rep->getTabel('angkatan');
				$arrTabelX=explode("||", $getTabelX);
				// where axis
				$where=array();
				$whereX[0]['key'] = $arrTabelX[1];
				$whereX[0]['param'] = $akt;
				// data axis
				$arrKolomX=array($arrTabelX[1]);
				$getX= $rep->get($arrTabelX[0], $arrKolomX, $arrKolomX, $arrKolomX, $whereX);
				
				// parameter
				$getTabelParam=$rep->getTabel($par);
				$arrTabel=explode("||", $getTabelParam);
				$tabel_param=$arrTabel[0];
				$key_param=$arrTabel[1];
				$label_param=$arrTabel[2];
				//--
				$arrKolomPar=array($key_param,$label_param);
				$wherePar=array();
				//--
				$getPar= $rep->get($tabel_param, $arrKolomPar, $arrKolomPar, $arrKolomPar,$wherePar);
				$arrPar=array();
				foreach ($getPar as $data){
					$arrPar['key'][]=$data[$key_param];
					$arrPar['label'][]=$data[$label_param];
				}
				
				// data
				$getTabelData=$rep->getTabel('mhs_reg');
				$arrTabelData=explode("||", $getTabelData);
				// where data
				$whereD[0]['key'] = $arrTabelX[1];
				$whereD[0]['param'] = $akt;
				$getTabelFil=$rep->getTabel('prodi');
				$arrTabelFil=explode("||", $getTabelFil);
				$whereD[1]['key'] = $arrTabelFil[1];
				$whereD[1]['param'] = $prd;
				$getTabelFil=$rep->getTabel('periode');
				$arrTabelFil=explode("||", $getTabelFil);
				$whereD[2]['key'] = $arrTabelFil[1];
				$whereD[2]['param'] = $per;
				//--
				$arrKolomD=array($arrTabelX[1],$key_param);
				$getReport= $rep->get($arrTabelData[0], $arrKolomD, $arrKolomD, $arrKolomD,$whereD);
				$this->view->x=$rep->query($arrTabelData[0], $arrKolomD, $arrKolomD, $arrKolomD,$whereD);
				// data
				$array=array();
				$i=0;
				foreach ($getX as $data) {
					$array[$i]['x']=$data[$arrTabelX[1]];
					foreach ($arrPar['key'] as $data2){
						$n=0;
						foreach ($getReport as $data3){
							if(($data3[$arrTabelX[1]]==$data[$arrTabelX[1]])and($data3[$key_param]==$data2)){
								$n=$data3['n'];
							}
						}
						$array[$i][$data2]=$n;
					}
					$i++;
				}
				// view
				$this->view->labels=$arrPar['label'];
				$this->view->key=$arrPar['key'];
				$this->view->data=$array;
				$this->view->chart=$cht;
			}
			// destroy session param
			Zend_Session::namespaceUnset('param_reg_chart');
		}
	}

	function logAction()
	{
		$user = new Menu();
		$menu = "register/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Log Data";
			// get nim periode
			$nim = $this->_request->get('nim');
			$kd_periode = $this->_request->get('per');
			// get register
			$register = new Register();
			$getRegister = $register->getRegisterByNimPeriode($nim,$kd_periode);
			if($getRegister){
				foreach ($getRegister as $dtReg) {
					$this->view->nim=$dtReg['nim'];
					$this->view->nm_mhs=$dtReg['nm_mhs'];
					$this->view->akt=$dtReg['id_angkatan'];
					$this->view->kd_prd=$dtReg['kd_prodi'];
					$this->view->prd=$dtReg['nm_prodi'];
					$this->view->dw=$dtReg['nm_dosen_wali'];
					$this->view->per=$dtReg['kd_periode'];
				}
				$getLog = $register->getRegisterLog($nim, $kd_periode);
				$this->view->listLog=$getLog;
				// navigation
				$this->_helper->navbar("register/list",0,0,0,0);
			}else{
				$this->view->eksis="f";
				// navigation
				$this->_helper->navbar("register",0,0,0,0);
			}
		}
	}
}