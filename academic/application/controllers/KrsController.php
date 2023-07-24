<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: KRS Controller -> Controller untuk modul halaman KRS
*/
class KrsController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Paketkelas');
		Zend_Loader::loadClass('Kuliah');
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
		$this->_helper->layout()->setLayout('second');
		// treeview
		$this->view->active_tree="05";
		$this->view->active_menu="register/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "krs/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
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
					$kd_prodi=$dtReg['kd_prodi'];
					$this->view->per=$dtReg['kd_periode'];
					$krs=$dtReg['krs'];
					$nim=$dtReg['nim'];
					$nm_mhs=$dtReg['nm_mhs'];
					$akt=$dtReg['id_angkatan'];
					$prd=$dtReg['nm_prodi'];
					$per=$dtReg['kd_periode'];
					$dw=$dtReg['nm_dosen_wali'];
				}
				if($krs=='f'){
					// Title Browser
					$this->view->title = "KRS Mahasiswa";
					$this->view->eksis="f";	
				}else{
					// Title Browser
					$this->view->title = "KRS Mahasiswa ".$nm_mhs;
					$paketkelas = new Paketkelas();
					$this->view->listPaketkelas= $paketkelas->getPaketKelasByPeriodeProdi($kd_periode,$kd_prodi);
					$kuliah = new Kuliah();
					$getKuliah = $kuliah->getKuliahByNimPeriode($nim,$kd_periode);
					$this->view->listKuliah=$getKuliah;
					$kuliahTA = new KuliahTA();
					$getKuliahTA = $kuliahTA->getKuliahTAByNimPeriode($nim,$kd_periode);
					$this->view->listKuliahTA=$getKuliahTA;
				}
				// navigation
				$this->_helper->navbar("register/list",0,0,'krs/export',0);
			}else{
				// Title Browser
				$this->view->title = "KRS Mahasiswa";
				$this->view->eksis="f";
				// navigation
				$this->_helper->navbar("register/list",0,0,0,0);
			}
			// create session for export
			$param = new Zend_Session_Namespace('param_krs');
			$param->data=array_merge($getKuliah,$getKuliahTA);
			$param->per=$per;
			$param->nim=$nim;
			$param->nm=$nm_mhs;
			$param->akt=$akt;
			$param->prd=$prd;
			$param->dw=$dw;
		}
	}

	function editAction()
	{
		$user = new Menu();
		$menu = "krs/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// layout
			$this->_helper->layout()->setLayout('main');
			// title Browser
			$this->view->title="Edit SKS diambil pada KRS";
			// get kd kuliah
			$kd_kuliah = $this->_request->get('id');
			$kuliah = new Kuliah();
			$getKuliah = $kuliah->getKuliahByKd($kd_kuliah);
			if($getKuliah){
				foreach ($getKuliah as $dataKuliah) {
					$this->view->kd=$dataKuliah['kd_kuliah'];
					$this->view->nim=$dataKuliah['nim'];
					$nim=$dataKuliah['nim'];
					$this->view->nm_mhs=$dataKuliah['nm_mhs'];
					$this->view->akt=$dataKuliah['id_angkatan'];
					$this->view->prd=$dataKuliah['nm_prodi'];
					$this->view->kd_mk=$dataKuliah['kode_mk'];
					$this->view->nm_mk=$dataKuliah['nm_mk'];
					$this->view->per=$dataKuliah['kd_periode'];
					$kd_periode=$dataKuliah['kd_periode'];
					$this->view->nm_dsn=$dataKuliah['nm_dosen'];
					$this->view->sks_def=$dataKuliah['sks_tm']+$dataKuliah['sks_prak']+$dataKuliah['sks_prak_lap']+$dataKuliah['sks_sim'];
					$this->view->sks_taken=$dataKuliah['sks_tm']+$dataKuliah['sks_prak']+$dataKuliah['sks_prak_lap']+$dataKuliah['sks_sim']-$dataKuliah['sks_deducted'];
				}
				// navigation
				$this->_helper->navbar("krs/index?nim=".$nim."&per=".$kd_periode,0,0,0,0);
			}else{
				$this->view->eksis="f";
				$this->_helper->navbar("register/list".$kd_periode,0,0,0,0);
			}
		}
	}
	
	function exportAction(){
		$user = new Menu();
		$menu = "krs/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			$param = new Zend_Session_Namespace('param_krs');
			$dataKrs=$param->data;
			$per=$param->per;
			$nim=$param->nim;
			$nm_mhs=$param->nm;
			$akt=$param->akt;
			$prd=$param->prd;
			$dw=$param->dw;
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt=$ses_ac->nm_pt;
			// disabel layout
			$this->_helper->layout->disableLayout();
			// image path logo
			$path = __FILE__;
			$imgPath = str_replace('academic/application/controllers/KrsController.php','public/img/logo.png',$path);
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Akademik")
										 ->setLastModifiedBy("Akademik")
										 ->setTitle("Export Data KRS Mahasiswa")
										 ->setSubject("Sistem Informasi Akademik")
										 ->setDescription("Data KRS Mahasiswa")
										 ->setKeywords("krs")
										 ->setCategory("Data File");
										 
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Form KRS');
										 
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
			$noborder = array(
	 		   'borders' => array(
			        'allborders' => array(
			        	'style' => PHPExcel_Style_Border::BORDER_NONE
			    	)
			    )
			);
			// column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(32);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(22);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(32);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
			// drawing logo
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Logo');
			$objDrawing->setDescription('Logo');
			$objDrawing->setPath($imgPath);
			$objDrawing->setHeight(55);
			$objDrawing->setWidth(75);
			$objDrawing->setCoordinates('A1');
			$objDrawing->setOffsetX(15);
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			// properties field excel;
			$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:J2');
			$objPHPExcel->getActiveSheet()->mergeCells('A3:J3');
			$objPHPExcel->getActiveSheet()->mergeCells('A8:J8');
			$objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A2:J2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1:J2')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('A1:J2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(25);
			$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(25);
			// text
			$objPHPExcel->getActiveSheet()->setCellValue('A1',strtoupper($nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('A2','KARTU RENCANA STUDI');
			$objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
			$objPHPExcel->getActiveSheet()->mergeCells('A5:B5');
			$objPHPExcel->getActiveSheet()->mergeCells('A6:B6');
			$objPHPExcel->getActiveSheet()->mergeCells('A7:B7');
			$objPHPExcel->getActiveSheet()->setCellValue('A4','  PERIODE');
			$objPHPExcel->getActiveSheet()->setCellValue('A5','  NPM');
			$objPHPExcel->getActiveSheet()->setCellValue('A6','  NAMA MAHASISWA');
			$objPHPExcel->getActiveSheet()->setCellValue('A7','  DOSEN WALI');
			$objPHPExcel->getActiveSheet()->mergeCells('C4:J4');
			$objPHPExcel->getActiveSheet()->mergeCells('C5:J5');
			$objPHPExcel->getActiveSheet()->mergeCells('C6:J6');
			$objPHPExcel->getActiveSheet()->mergeCells('C7:J7');
			$objPHPExcel->getActiveSheet()->setCellValue('C4',' :  '.$per);
			$objPHPExcel->getActiveSheet()->setCellValue('C5',' :  '.$nim);
			$objPHPExcel->getActiveSheet()->setCellValue('C6',' :  '.$nm_mhs);
			$objPHPExcel->getActiveSheet()->setCellValue('C7',' :  '.$dw);
			$objPHPExcel->getActiveSheet()->getStyle('A1:J8')->applyFromArray($noborder);
			$objPHPExcel->getActiveSheet()->setCellValue('A8','* Warna abu untuk paket kelas yang belum/tidak diapprove dosen wali');
			$objPHPExcel->getActiveSheet()->getStyle('A8')->getFont()->setSize(8);
			// tabel krs
			$objPHPExcel->getActiveSheet()->mergeCells('A9:A11');
			$objPHPExcel->getActiveSheet()->mergeCells('B9:E9');
			$objPHPExcel->getActiveSheet()->mergeCells('F9:G9');
			$objPHPExcel->getActiveSheet()->mergeCells('H9:J11');
			$objPHPExcel->getActiveSheet()->mergeCells('B10:B11');
			$objPHPExcel->getActiveSheet()->mergeCells('C10:C11');
			$objPHPExcel->getActiveSheet()->mergeCells('D10:E10');
			$objPHPExcel->getActiveSheet()->mergeCells('F10:F11');
			$objPHPExcel->getActiveSheet()->mergeCells('G10:G11');
			$objPHPExcel->getActiveSheet()->setCellValue('A9',' NO');
			$objPHPExcel->getActiveSheet()->setCellValue('B9',' MATA KULIAH');
			$objPHPExcel->getActiveSheet()->setCellValue('F9',' DOSEN');
			$objPHPExcel->getActiveSheet()->setCellValue('H9',' NAMA KELAS');
			$objPHPExcel->getActiveSheet()->setCellValue('B10',' KODE');
			$objPHPExcel->getActiveSheet()->setCellValue('C10',' NAMA MATA KULIAH');
			$objPHPExcel->getActiveSheet()->setCellValue('D10',' SKS');
			$objPHPExcel->getActiveSheet()->setCellValue('D11',' DEF');
			$objPHPExcel->getActiveSheet()->setCellValue('E11',' TAKE');
			$objPHPExcel->getActiveSheet()->setCellValue('F10',' KODE');
			$objPHPExcel->getActiveSheet()->setCellValue('G10',' NAMA DOSEN ');
			$objPHPExcel->getActiveSheet()->getStyle('A9:J11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A9:J11')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A9:J11')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A9:J11')->getFont()->setBold(true);
			// sort data
			$sortmk=array();
			foreach ($dataKrs as $key => $row)
			{
			    $sortmk[$key] = $row['kode_mk'];
			}
			array_multisort($sortmk, SORT_ASC, $dataKrs);
			$i=1;
			$n=12;
			foreach ($dataKrs as $dataBelajar) {
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$n,$i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$n)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$n,$dataBelajar['kode_mk']);
				$objPHPExcel->getActiveSheet()->getStyle('B'.$n)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$n,'  '.$dataBelajar['nm_mk']);
				$sks=$dataBelajar['sks_tm']+$dataBelajar['sks_prak']+$dataBelajar['sks_prak_lap']+$dataBelajar['sks_sim'];
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$n,$sks);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$n,$sks-$dataBelajar['sks_deducted']);
				$objPHPExcel->getActiveSheet()->getStyle('D'.$n.':E'.$n)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$n,$dataBelajar['kd_dosen']);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$n)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$n,'  '.$dataBelajar['nm_dosen']);
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$n,' '.$dataBelajar['nm_kelas']);
				$objPHPExcel->getActiveSheet()->getStyle('H'.$n)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->mergeCells('H'.$n.':J'.$n);
				if($dataBelajar['approved']=='f'){
					$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':J'.$n)->applyFromArray($cell_color);
				}
				$i++;
				$n++;
			}
			$objPHPExcel->getActiveSheet()->getStyle('A3:J3')->applyFromArray(array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE))));
			$objPHPExcel->getActiveSheet()->getStyle('A9:J'.$n)->applyFromArray($border);
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$n,'JUMLAH SKS DIAMBIL');
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$n.':D'.$n);
			$objPHPExcel->getActiveSheet()->mergeCells('F'.$n.':J'.$n);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$n,'=SUM(E12:E'.($n-1).')');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':E'.$n)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':E'.$n)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells('A'.($n+1).':J'.($n+1));
			$n=$n+2;
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$n.':C'.$n);
			$objPHPExcel->getActiveSheet()->mergeCells('D'.$n.':F'.$n);
			$objPHPExcel->getActiveSheet()->mergeCells('G'.$n.':J'.$n);
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$n,'MAHASISWA');
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$n,'BAG.AKADEMIK');
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$n,'DOSEN WALI');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':G'.$n)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':G'.$n)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':J'.$n)->applyFromArray($border);
			$n++;
			$objPHPExcel->getActiveSheet()->getRowDimension($n)->setRowHeight(70);
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$n.':C'.$n);
			$objPHPExcel->getActiveSheet()->mergeCells('D'.$n.':F'.$n);
			$objPHPExcel->getActiveSheet()->mergeCells('G'.$n.':J'.$n);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':J'.$n)->applyFromArray($border);
			$n++;
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$n.':C'.$n);
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$n,'('.$nm_mhs.')');
			$objPHPExcel->getActiveSheet()->mergeCells('D'.$n.':F'.$n);
			$objPHPExcel->getActiveSheet()->mergeCells('G'.$n.':J'.$n);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$n,'('.$dw.')');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':G'.$n)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':G'.$n)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$n.':J'.$n)->applyFromArray($border);
			
			// protect sheet
			$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
			$objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
			$objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
			$objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);
			
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Kartu Rencana Studi.xls"');
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}
	
	function reportAction(){
		$user = new Menu();
		$menu = "krs/report";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="krs/report";
			// session chart
			$param = new Zend_Session_Namespace('param_krs_chart');
			$akt = $param->akt;
			$prd = $param->prd;
			$per = $param->per;
			$par=$param->par;
			$cht=$param->cht;
			// Title Browser
			$this->view->title = "Report Jumlah SKS KRS ".$per;
			if($cht){
				// layout
				$this->_helper->layout()->setLayout('second');
				// navigation
				$this->_helper->navbar("krs/report",0,0,0,0);
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
				$wherePar[0]['key']=$key_param;
				$wherePar[0]['param']=$prd;
				//--
				$getPar= $rep->get($tabel_param, $arrKolomPar, $arrKolomPar, $arrKolomPar,$wherePar);
				$arrPar=array();
				foreach ($getPar as $data){
					$arrPar['key'][]=$data[$key_param];
					$arrPar['label'][]=$data[$label_param];
				}
				
				// data
				$getTabelData=$rep->getTabel('mhs_kul');
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
				$whereD[3]['key'] = "approved";
				$whereD[3]['param'] = "t";
				//--
				$arrKolomD=array($arrTabelX[1],$key_param, 'sum(sks_tm+sks_prak+sks_prak_lap+sks_sim-sks_deducted) as sum_sks');
				$orderD=array($arrTabelX[1],$key_param);
				$groupD=array($arrTabelX[1],$key_param);
				$getReport= $rep->get($arrTabelData[0], $arrKolomD, $groupD, $orderD,$whereD);
				$this->view->x=$rep->query($arrTabelData[0], $arrKolomD, $groupD, $orderD,$whereD);
				// data
				$array=array();
				$i=0;
				foreach ($getX as $data) {
					$array[$i]['x']=$data[$arrTabelX[1]];
					foreach ($arrPar['key'] as $data2){
						$n=0;
						foreach ($getReport as $data3){
							if(($data3[$arrTabelX[1]]==$data[$arrTabelX[1]])and($data3[$key_param]==$data2)){
								$n=$data3['sum_sks'];
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
			Zend_Session::namespaceUnset('param_krs_chart');
		}
	}

	function logAction()
	{
		$user = new Menu();
		$menu = "krs/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// layout
			$this->_helper->layout()->setLayout('main');
			// title Browser
			$this->view->title="Log Data";
			// get kd kuliah
			$kd_kuliah = $this->_request->get('id');
			$ta=$this->_request->get('ta');
			if($ta==0){
				$kuliah = new Kuliah();
				$getKuliah = $kuliah->getKuliahByKd($kd_kuliah);
			}else{
				$kuliahTA = new KuliahTA();
				$getKuliah = $kuliahTA->getKuliahTAByKd($kd_kuliah);
			}
			if($getKuliah){
				foreach ($getKuliah as $dataKuliah) {
					$this->view->kd=$dataKuliah['kd_kuliah'];
					$this->view->nim=$dataKuliah['nim'];
					$nim=$dataKuliah['nim'];
					$this->view->nm_mhs=$dataKuliah['nm_mhs'];
					$this->view->akt=$dataKuliah['id_angkatan'];
					$this->view->prd=$dataKuliah['nm_prodi'];
					$this->view->kd_mk=$dataKuliah['kode_mk'];
					$this->view->nm_mk=$dataKuliah['nm_mk'];
					$this->view->per=$dataKuliah['kd_periode'];
					$kd_periode=$dataKuliah['kd_periode'];
					$this->view->nm_dsn=$dataKuliah['nm_dosen'];
					$this->view->sks_def=$dataKuliah['sks_tm']+$dataKuliah['sks_prak']+$dataKuliah['sks_prak_lap']+$dataKuliah['sks_sim'];
					$this->view->sks_taken=$dataKuliah['sks_tm']+$dataKuliah['sks_prak']+$dataKuliah['sks_prak_lap']+$dataKuliah['sks_sim']-$dataKuliah['sks_deducted'];
				}
				// log
				if($ta==0){
					$this->view->listLog = $kuliah->getKuliahLog($kd_kuliah);
				}else{
					$this->view->listLog = $kuliahTA->getKuliahTALog($kd_kuliah);
				}
				// navigation
				$this->_helper->navbar("krs/index?nim=".$nim."&per=".$kd_periode,0,0,0,0);
			}else{
				$this->view->eksis="f";
				$this->_helper->navbar("register",0,0,0,0);
			}
		}
	}

	function rekapAction(){
		$user = new Menu();
		$menu = "krs/rekap";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// destroy session param export
			Zend_Session::namespaceUnset('param_rekap_krs');
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="krs/rekap";
			// title
			$this->view->title = "Rekap KRS";
			// layout
			$this->_helper->layout()->setLayout('main');
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
			// get data angkatan
			$angkatan= new Angkatan();
			$this->view->listAkt=$angkatan->fetchAll();
			// get data periode
			$periode = new Periode();
			$this->view->listPeriode=$periode->fetchAll();
		}
	}
	
	function prekapAction(){
		$user = new Menu();
		$menu = "krs/rekap";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// navigation
			$this->_helper->navbar('krs/rekap',0,0,'krs/erekap',0);
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="krs/rekap";
			// layout
			$this->_helper->layout()->setLayout('second');
			// session
			$param = new Zend_Session_Namespace('param_rekap_krs');
			$kd_prodi = $param->prd;
			$akt = $param->akt;
			$per = $param->per;
			// get prodi
			$prodi = new Prodi();
			$getProdi=$prodi->getProdiByKd($kd_prodi);
			$nm_prd="";
			foreach ($getProdi as $dtProdi){
				$nm_prd=$dtProdi['nm_prodi'];
			}
			// title
			$this->view->title = "Rekap KRS Prodi ".$nm_prd." Angkatan ".$akt." Periode ".$per;
			// get data mahasiswa
			$mhs=new Register();
			$kuliah=new Kuliah();
			$getMhs=$mhs->getRegisterByPeriodeAngkatanProdi($per,$akt, $kd_prodi);
			$arrMhs=array();
			$arrKuliah=array();
			$n=0;
			$m=0;
			foreach ($getMhs as $dtMhs){
				$arrMhs[$n]['nim']=$dtMhs['nim'];
				$arrMhs[$n]['nm_mhs']=$dtMhs['nm_mhs'];
				$arrMhs[$n]['id_mhs']=$dtMhs['id_mhs'];
				$arrMhs[$n]['status_mhs_periode']=$dtMhs['status_mhs_periode'];
				$getKuliah=$kuliah->getKuliahByNimPeriode($dtMhs['nim'],$per);
				foreach ($getKuliah as $dtKuliah){
					if($dtKuliah['approved']=='t'){
						$arrKuliah[$m]['nim']=$dtKuliah['nim'];
						$arrKuliah[$m]['kode_mk']=$dtKuliah['kode_mk'];
						$arrKuliah[$m]['nm_mk']=$dtKuliah['nm_mk'];
						$arrKuliah[$m]['sks_tot']=$dtKuliah['sks_tm']+$dtKuliah['sks_prak']+$dtKuliah['sks_prak_lap']+$dtKuliah['sks_sim'];
						$arrKuliah[$m]['sks_deducted']=$dtKuliah['sks_deducted'];
						$arrKuliah[$m]['kd_dosen']=$dtKuliah['kd_dosen'];
						$arrKuliah[$m]['nm_dosen']=$dtKuliah['nm_dosen'];
						$arrKuliah[$m]['nm_kelas']=$dtKuliah['nm_kelas'];
						$m++;
					}
				}
				$n++;
			}
			$this->view->prd=$nm_prd;
			$this->view->akt=$akt;
			$this->view->per=$per;
			$this->view->listKuliah=$arrKuliah;
			$this->view->listMhs=$arrMhs;
			// session for excel
			$param->dataKuliah=$arrKuliah;
			$param->dataMhs=$arrMhs;
			$param->nmPrd=$nm_prd;
			$param->akt=$akt;
			$param->per=$per;
		}
	}
	
	function erekapAction(){
		$user = new Menu();
		$menu = "krs/rekap";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// session
			$param = new Zend_Session_Namespace('param_rekap_krs');
			$dataKuliah = $param->dataKuliah;
			$dataMhs=$param->dataMhs;
			$nmPrd=$param->nmPrd;
			$akt=$param->akt;
			$per=$param->per;
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt=$ses_ac->nm_pt;
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt = $ses_ac->nm_pt;
			$objPHPExcel->getProperties()->setCreator($nm_pt)
			->setLastModifiedBy("Akademik")
			->setTitle("Rekap Nilai Konversi")
			->setSubject("Sistem Informasi Akademik")
			->setDescription("Rekap KRS")
			->setKeywords("rekap nilai")
			->setCategory("Data File");
				
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Rekap KRS');
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
			$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:H2');
			$objPHPExcel->getActiveSheet()->mergeCells('A3:H3');
			$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A2:H2')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A2:H2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A3:H3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A3:H3')->getFont()->setSize(11);
			$objPHPExcel->getActiveSheet()->getStyle('A1:H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(43);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
			// insert data to excel
			$objPHPExcel->getActiveSheet()->setCellValue('A1',strtoupper($nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('A2','REKAP KRS MAHASISWA');
			$objPHPExcel->getActiveSheet()->setCellValue('A3','PROGRAM STUDI '.$nmPrd.' ANGKATAN '.$akt.' PERIODE '.$per);
			// data
			$i=5;
			foreach ($dataMhs as $dtMhs){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'NIM');
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$dtMhs['nim']);
				$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':J'.$i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getFont()->setBold(true);
				$i++;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'NAMA');
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$dtMhs['nm_mhs']);
				$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':J'.$i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getFont()->setBold(true);
				$i++;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'STATUS REGISTRASI');
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$dtMhs['status_mhs_periode']);
				$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':J'.$i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getFont()->setBold(true);
				$i++;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'NO');
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,'KODE');
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,'MATA KULIAH');
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,'SKS DEFAULT');
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,'SKS DIAMBIL');
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,'KODE DOSEN');
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,'NAMA DOSEN');
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,'KELAS');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($cell_color);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getFont()->setBold(true);
				$i++;
				$n=1;
				$totSksDef=0;
				$totSksDiambil=0;
				foreach ($dataKuliah as $dtNilai){
					if($dtNilai['nim']==$dtMhs['nim']){
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
						$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$dtNilai['kode_mk']);
						$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$dtNilai['nm_mk']);
						$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$dtNilai['sks_tot']);
						$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$dtNilai['sks_tot']-$dtNilai['sks_deducted']);
						$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$dtNilai['kd_dosen']);
						$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$dtNilai['nm_dosen']);
						$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$dtNilai['nm_kelas']);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($border);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setWrapText(true);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$i++;
						$n++;
						$totSksDef=$totSksDef+$dtNilai['sks_tot'];
						$totSksDiambil=$totSksDiambil+($dtNilai['sks_tot']-$dtNilai['sks_deducted']);
					}
				}
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'Jumlah');
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$totSksDef);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$totSksDiambil);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$i=$i+2;
			}
			//Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Rekap KRS Mahasiswa.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}

}