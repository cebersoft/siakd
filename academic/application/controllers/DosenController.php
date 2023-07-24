<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Dosen Controller -> Controller untuk modul halaman dosen
*/
class DosenController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Dosen');
		Zend_Loader::loadClass('Agama');
		Zend_Loader::loadClass('Kwn');
		Zend_Loader::loadClass('JenjangPendidikan');
		Zend_Loader::loadClass('KatDosen');
		Zend_Loader::loadClass('JabDosen');
		Zend_Loader::loadClass('PendDosen');
		Zend_Loader::loadClass('PangkatDosen');
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
		$this->view->active_tree="01";
		$this->view->active_menu="dosen/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "dosen/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Dosen";
			// navigation
			$this->_helper->navbar(0,0,'dosen/new',0,0);
			// destroy session param
			Zend_Session::namespaceUnset('param_dsn');
			// get data kat dosen
			$katDosen = new KatDosen();
			$this->view->listKatDosen=$katDosen->fetchAll();
		}
	}

	function listAction(){
		$user = new Menu();
		$menu = "dosen/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// show data
			$param = new Zend_Session_Namespace('param_dsn');
			$kat = $param->kat;
			$stat = $param->statdsn;
			$a_hb=$param->a_hb;
			// Title Browser
			$this->view->title = "Daftar Dosen";
			// navigation
			$this->_helper->navbar('dosen',0,'dosen/new','dosen/export',0);
			// get data 
			$dosen = new Dosen();
			$getDosen = $dosen->getDosenByKatStatusHb($kat,$stat,$a_hb);
			$this->view->listDosen = $getDosen;
			// get data kategori
			$katDosen = new KatDosen();
			$listKatDosen=$katDosen->fetchAll();
			if(!$kat){
				$v_kat="SEMUA";
				$this->view->kat=$v_kat;
			}else{
				$v_kat="";
				foreach ($listKatDosen as $dataKat) {
					foreach ($kat as $dt) {
						if($dt==$dataKat['id_kat_dosen']){
							$v_kat=$dataKat['kategori_dosen'].", ".$v_kat;
						}
					}
				}
				$this->view->kat=$v_kat;
			}
			// get data status
			$listStatDsn=array(array('id_stat'=>'t','stat_dsn'=>'AKTIF'),array('id_stat'=>'f','stat_dsn'=>'TIDAK AKTIF'));
			if(!$stat){
				$v_stat="SEMUA";
				$this->view->stat=$v_stat;
			}else{
				$v_stat="";
				foreach ($listStatDsn as $dataStat) {
					foreach ($stat as $dt) {
						if($dt==$dataStat['id_stat']){
							$v_stat=$dataStat['stat_dsn'].", ".$v_stat;
						}
					}
				}
				$this->view->stat=$v_stat;
			}
			// get homebase
			$listHbDsn=array(array('id_ahb'=>'t','ahb'=>'HOMEBASE'),array('id_ahb'=>'f','ahb'=>'BUKAN HOMEBASE'));
			if(!$a_hb){
				$v_ahb="SEMUA";
				$this->view->a_hb=$v_ahb;
			}else{
				$v_ahb="";
				foreach ($listHbDsn as $dataHb) {
					foreach ($a_hb as $dt) {
						if($dt==$dataHb['id_ahb']){
							$v_ahb=$dataHb['ahb'].", ".$v_ahb;
						}
					}
				}
				$this->view->a_hb=$v_ahb;
			}
			// session for export
			$param->data=$getDosen;
			$param->v_kat=$v_kat;
			$param->v_stat=$v_stat;
			$param->v_ahb=$v_ahb;
		}
	}

	function detilAction(){
		$user = new Menu();
		$menu = "dosen/detil";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// navigation
			$this->_helper->navbar('dosen/list',0,'dosen/new',0,0);
			// title browser
			$this->view->title = "Profil Dosen";
			$kd=$this->_request->get('id');
			// tab
			$pd=$this->_request->get('pd');
			$this->view->tab_akd="active";
			if($pd){
				$this->view->tab_pd="active";
				$this->view->tab_akd="";
			}
			$dosen = new Dosen();
			$getDosen = $dosen->getDosenByKd($kd);
			if($getDosen){
				foreach ($getDosen as $data) {
					$this->view->kd_dosen=$data['kd_dosen'];
					$this->view->nm_dosen=$data['nm_dosen'];
					$this->view->g_dpn=$data['gelar_depan'];
					$this->view->g_blk=$data['gelar_belakang'];
					$this->view->nidn=$data['nidn'];
					if($data['a_dosen_homebase']=='f'){
						$a_hb="TIDAK";
					}else{
						$a_hb="YA";
					}
					$this->view->a_hb=$a_hb;
					$this->view->kategori_dosen=$data['kategori_dosen'];
					$this->view->tempat_lahir=$data['tmp_lahir'];
					$this->view->tanggal_lahir=$data['tgl_lahir_fmt'];
					if ($data['jenis_kelamin']=='L'){
						$this->view->jk='Laki-laki';
					}else{
						$this->view->jk='Perempuan';				
					}
					$this->view->jk0=$data['jenis_kelamin'];
					$this->view->agm=$data['nm_agama'];
					$this->view->kwn=$data['nm_kwn'];
					if($data['aktif']=='f'){
						$aktif="TIDAK AKTIF";
					}else{
						$aktif="AKTIF";
					}
					$this->view->aktif=$aktif;
					$this->view->alamat=$data['alamat'];
					$this->view->kota_tinggal=$data['kota'];
					$this->view->nik=$data['nik'];
					$this->view->kontak=$data['kontak'];
					$this->view->email_k=$data['email_kampus'];
					$this->view->email_l=$data['email_lain'];
					$this->view->jab=$data['nm_jab'];
					$this->view->pang=$data['nm_pangkat'];
					if($data['a_dosen_wali']=='f'){
						$a_dw="TIDAK";
					}else{
						$a_dw="YA";
					}
					$this->view->a_dw=$a_dw;
					$this->view->uname=$data['kd_dosen'];
					$this->view->pwd=$data['sys_password'];
					$this->view->sys_a=$data['sys_aktif'];
				}
				// pendidikan dosen
				$menupend = "pendidikandosen/index";
				$getMenuPend = $user->cekUserMenu($menupend);
				if ($getMenuPend=="F"){
					$this->view->menuPend="f";
				}else{
					$this->view->menuPend="t";
					$pendDosen = new PendDosen();
					$this->view->listPendDosen = $pendDosen->getPendByKdDosen($kd);
					// jenjang pendidikan
					$jenjang = new JenjangPendidikan();
					$this->view->listJenjang=$jenjang->fetchAll();
				}
			}else{
				$this->view->eksis ='f';
			}
		}
	}

	function newAction(){
		$user = new Menu();
		$menu = "dosen/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// navigation
			$this->_helper->navbar(0,'dosen',0,0,0);	
			// Title Browser
			$this->view->title = "Input Dosen Baru";
			// ref
			$agama = new Agama();
			$get_agama=$agama->fetchAll();
			$this->view->listAgama=$get_agama;
			$kwn = new Kwn();
			$get_kwn=$kwn->fetchAll();
			$this->view->listKwn=$get_kwn;
			// get data kat dosen
			$katDosen = new KatDosen();
			$this->view->listKatDosen=$katDosen->fetchAll();
			// get data jabatan
			$jabDosen = new JabDosen();
			$this->view->listJabDosen=$jabDosen->fetchAll();
			// get data pangkat
			$pangDosen = new PangkatDosen();
			$this->view->listPangDosen=$pangDosen->fetchAll();
		}
	}

	function editAction(){
		$user = new Menu();
		$menu = "dosen/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get kd
			$kd=$this->_request->get('kd');
			// get data from data base
			$dsn=new Dosen();
			$data_dsn=$dsn->getDosenByKd($kd);
			// ref
			$agama = new Agama();
			$get_agama=$agama->fetchAll();
			$this->view->listAgama=$get_agama;
			$kwn = new Kwn();
			$get_kwn=$kwn->fetchAll();
			$this->view->listKwn=$get_kwn;
			// get data kat dosen
			$katDosen = new KatDosen();
			$this->view->listKatDosen=$katDosen->fetchAll();
			// get data jabatan
			$jabDosen = new JabDosen();
			$this->view->listJabDosen=$jabDosen->fetchAll();
			// get data pangkat
			$pangDosen = new PangkatDosen();
			$this->view->listPangDosen=$pangDosen->fetchAll();
			$i=0;
			foreach($data_dsn as $data){
				$kd_dosen = $data['kd_dosen'];
				$this->view->kd_dosen = $data['kd_dosen'];
				$this->view->nm_dosen=$data['nm_dosen'];
				$nm_dosen = $data['nm_dosen'];
				$this->view->g_dpn=$data['gelar_depan'];
				$this->view->g_blk=$data['gelar_belakang'];
				$this->view->nidn=$data['nidn'];
				if($data['a_dosen_homebase']=='f'){
					$this->view->ahb_t="selected";
				}else{
					$this->view->ahb_y="selected";
				}
				$this->view->kategori_dosen=$data['id_kat_dosen'];
				$this->view->tempat_lahir=$data['tmp_lahir'];
				$this->view->tanggal_lahir=$data['tgl_lahir_fmt'];
				$jk=$data['jenis_kelamin'];
				if($jk=='L'){
					$this->view->jkL="selected";
				}elseif ($jk=='P') {
					$this->view->jkP="selected";
				}
				$this->view->agm=$data['id_agama'];
				$this->view->kwn=$data['id_kwn'];
				$this->view->alamat=$data['alamat'];
				$this->view->kota_tinggal=$data['kota'];
				$this->view->nik=$data['nik'];
				$this->view->kontak=$data['kontak'];
				$this->view->email_k=$data['email_kampus'];
				$this->view->email_l=$data['email_lain'];
				$this->view->jab=$data['id_jab'];
				$this->view->pang=$data['id_pangkat'];
				if($data['a_dosen_wali']=='f'){
					$this->view->adw_t="selected";
				}else{
					$this->view->adw_y="selected";
				}
				$this->view->uname=$data['kd_dosen'];
				$this->view->pwd=$data['sys_password'];
				$this->view->sys_a=$data['sys_aktif'];
				$i++;
			}
			// navigation
			$this->_helper->navbar('dosen/list',0,0,0,0);
			// Title Browser
			if ($i>0){
				$this->view->title = "Edit Data Dosen Nama : ".$nm_dosen. " (".$kd_dosen.")";
			}else{
				$this->view->title = "Edit Data Dosen";
				$this->view->eksis ='f';
			}
		}
	}
	
	function exportAction(){
		$user = new Menu();
		$menu = "dosen/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// session
			$param = new Zend_Session_Namespace('param_dsn');
			$dataDsn = $param->data;
			$kat=$param->v_kat;
			$stat=$param->v_stat;
			$ahb=$param->v_ahb;
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt=$ses_ac->nm_pt;
			
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
					
			$objPHPExcel->getProperties()->setCreator("Administrator")
										 ->setLastModifiedBy("Akademik")
										 ->setTitle("Export Data Dosen")
										 ->setSubject("Sistem Informasi Akademik")
										 ->setDescription("Data Dosen")
										 ->setKeywords("dosen")
										 ->setCategory("Data File");
										 
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Export Daftar Dosen');
										 
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
			$objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:N2');
			$objPHPExcel->getActiveSheet()->getStyle('A1:N1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A2:N2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('A3:N3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1:N1')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('A1:N1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A2:N2')->getFont()->setSize(12);	
			$objPHPExcel->getActiveSheet()->getStyle('A2:N2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A3:N3')->getFont()->setSize(11);
			$objPHPExcel->getActiveSheet()->getStyle('A3:N3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A3:N3')->applyFromArray($cell_color);
			
			// column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
			
			// insert data to excel
			$objPHPExcel->getActiveSheet()->setCellValue('A1','DAFTAR DOSEN '.strtoupper($nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('A2','STATUS : '.$stat.", DOSEN HOMEBASE : ".$ahb." KATEGORI : ".$kat);
			$objPHPExcel->getActiveSheet()->setCellValue('A3','NO');
			$objPHPExcel->getActiveSheet()->setCellValue('B3','NAMA DOSEN');
			$objPHPExcel->getActiveSheet()->setCellValue('C3','KODE DOSEN');
			$objPHPExcel->getActiveSheet()->setCellValue('D3','STATUS');
			$objPHPExcel->getActiveSheet()->setCellValue('E3','NIDN');
			$objPHPExcel->getActiveSheet()->setCellValue('F3','TTL');
			$objPHPExcel->getActiveSheet()->setCellValue('G3','L/P');
			$objPHPExcel->getActiveSheet()->setCellValue('H3','DOSEN WALI');
			$objPHPExcel->getActiveSheet()->setCellValue('I3','KATEGORI');
			$objPHPExcel->getActiveSheet()->setCellValue('J3','JABATAN');
			$objPHPExcel->getActiveSheet()->setCellValue('K3','PANGKAT');
			$objPHPExcel->getActiveSheet()->setCellValue('L3','AGAMA');
			$objPHPExcel->getActiveSheet()->setCellValue('M3','ALAMAT');
			$objPHPExcel->getActiveSheet()->setCellValue('N3','KONTAK');			
			$n=1;
			$i=4;
			foreach ($dataDsn as $data){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$data['gelar_depan']."".$data['nm_dosen']."".$data['gelar_belakang']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$data['kd_dosen']);
				if($data['aktif']=='t'){
					$status="AKTIF";
				}else{
					$status="TIDAK AKTIF";
				}
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$status);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$data['nidn']);
				$objPHPExcel->getActiveSheet()->setCellValueExplicit('E'.$i,$data['nidn'],PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$data['tmp_lahir'].", ".$data['tgl_lahir_fmt']);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$data['jenis_kelamin']);
				if ($data['a_dosen_wali']=='t'){
					$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,'DOSEN WALI');	
				} else {
					$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,'TIDAK DOSEN WALI');	
				}
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$data['kategori_dosen']);
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$data['nm_jab']);
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,$data['nm_pangkat']." (".$data['golongan'].")");
				$objPHPExcel->getActiveSheet()->setCellValue('L'.$i,$data['nm_agama']);
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$i,$data['alamat']);
				$objPHPExcel->getActiveSheet()->setCellValueExplicit('N'.$i,$data['kontak'],PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':N'.$i)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':N'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$n++;
				$i++;				
			}
			$objPHPExcel->getActiveSheet()->getStyle('A3:N'.($i-1))->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('A4:N'.($i-1))->getFont()->setSize(10);
			
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Daftar Dosen.xls"');
			header('Cache-Control: max-age=0');
	
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}
	
	function reportAction(){
		$user = new Menu();
		$menu = "dosen/report";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="dosen/report";
			// session chart
			//Zend_Session::namespaceUnset('param_dsn_chart');
			$param = new Zend_Session_Namespace('param_dsn_chart');
			$stat = $param->stat;
			$par=$param->par;
			$cht=$param->cht;
			// Title Browser
			$this->view->title = "Report Dosen";
			if($cht){
				// layout
				$this->_helper->layout()->setLayout('second');
				// navigation
				$this->_helper->navbar("dosen/report",0,0,0,0);
			}else {
				// layout
				$this->_helper->layout()->setLayout('main');
				// navigation
				$this->_helper->navbar(0,0,0,0,0);
			}
			
			if($cht){
				$rep = new Report();
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
				$getTabelData=$rep->getTabel('dosen');
				$arrTabelData=explode("||", $getTabelData);
				// where data
				$getTabelFil=$rep->getTabel('stat_aktif');
				$arrTabelFil=explode("||", $getTabelFil);
				$whereD[0]['key'] = $arrTabelFil[1];
				$whereD[0]['param'] = $stat;
				//--
				$arrKolomD=array($key_param);
				$this->view->x=$rep->query($arrTabelData[0], $arrKolomD, $arrKolomD, $arrKolomD,$whereD);
				$getReport= $rep->get($arrTabelData[0], $arrKolomD, $arrKolomD, $arrKolomD,$whereD);
				// data
				$array=array();
				$i=0;
				foreach ($arrPar['key'] as $data1){
					$array[$i]['label']=$arrPar['label'][$i];
					$n=0;
					foreach ($getReport as $data2){
						if($data2[$key_param]==$data1){
							$n=$data2['n'];
						}
					}
					$array[$i]['value']=$n;
					$i++;
				}
				// view
				$this->view->data=$array;
				$this->view->chart=$cht;
			}
			// destroy session param
			Zend_Session::namespaceUnset('param_dsn_chart');
		}
	}
}