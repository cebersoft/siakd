<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Jadwal TA Controller -> Controller untuk modul halaman jadwal penguji TA
*/
class VerifytaController extends Zend_Controller_Action
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
		Zend_Loader::loadClass('PrpUjianTa');
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
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "verifyta/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// treeview
			$this->view->active_menu="verifyta/index";
			// Title Browser
			$this->view->title = "Verifikasi TA (Akademik)";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// destroy session param
			Zend_Session::namespaceUnset('param_verifyta');
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

	function index2Action()
	{
		$user = new Menu();
		$menu = "verifyta/index2";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// treeview
			$this->view->active_menu="verifyta/index2";
			// Title Browser
			$this->view->title = "Verifikasi TA (Prodi)";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// destroy session param
			Zend_Session::namespaceUnset('param_verifyta2');
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

	function index3Action()
	{
		$user = new Menu();
		$menu = "verifyta/index3";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// treeview
			$this->view->active_menu="verifyta/index3";
			// Title Browser
			$this->view->title = "Verifikasi TA (Portofolio)";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// destroy session param
			Zend_Session::namespaceUnset('param_verifyta3');
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
		$menu = "verifyta/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// treeview
			$this->view->active_menu="verifyta/index";
			// show data
			$param = new Zend_Session_Namespace('param_verifyta');
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
				$this->view->title = "Verifikasi TA (Akademik) Periode ".$kd_periode." Prodi ".$nm_prd;
				// paket kelas
				$paketkelasta=new PaketkelasTA();
				$this->view->listPaketTA=$paketkelasta->getPaketKelasTAByPeriodeProdi($kd_periode,$kd_prodi);
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Verifikasi TA (Akademik)";	
			}
			// navigation
			$this->_helper->navbar('verifyta',0,0,0,0);
		}
	}

	function list2Action()
	{
		$user = new Menu();
		$menu = "verifyta/index2";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// treeview
			$this->view->active_menu="verifyta/index2";
			// show data
			$param = new Zend_Session_Namespace('param_verifyta2');
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
				$this->view->title = "Verifikasi TA (Prodi) Periode ".$kd_periode." Prodi ".$nm_prd;
				// paket kelas
				$paketkelasta=new PaketkelasTA();
				$this->view->listPaketTA=$paketkelasta->getPaketKelasTAByPeriodeProdi($kd_periode,$kd_prodi);
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Verifikasi TA (Prodi)";	
			}
			// navigation
			$this->_helper->navbar('verifyta/index2',0,0,0,0);
		}
	}

	function list3Action()
	{
		$user = new Menu();
		$menu = "verifyta/index3";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// treeview
			$this->view->active_menu="verifyta/index3";
			// show data
			$param = new Zend_Session_Namespace('param_verifyta3');
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
				$this->view->title = "Verifikasi TA (Portofolio) Periode ".$kd_periode." Prodi ".$nm_prd;
				// paket kelas
				$paketkelasta=new PaketkelasTA();
				$this->view->listPaketTA=$paketkelasta->getPaketKelasTAByPeriodeProdi($kd_periode,$kd_prodi);
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Verifikasi TA (Portofolio)";	
			}
			// navigation
			$this->_helper->navbar('verifyta/index3',0,0,0,0);
		}
	}

	function detilAction(){
		$user = new Menu();
		$menu = "verifyta/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// treeview
			$this->view->active_menu="verifyta/index";
			// layout
			$this->_helper->layout()->setLayout('second');
			// Title Browser
			$this->view->title = "Verifikasi TA (Akademik) - Daftar Pengajuan";
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
				$prp = new PrpUjianTa();
				$getPrp = $prp->getPrpApproverByPaketKelas($kd_paket);
				$this->view->listPrp=$getPrp;
				// navigation
				$this->_helper->navbar('verifyta/list',0,0,0,0);
			}else{
				$this->view->eksis="f";
				// navigation
				$this->_helper->navbar('verifyta',0,0,0,0);
			}
		}
	}

	function detil2Action(){
		$user = new Menu();
		$menu = "verifyta/index2";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// treeview
			$this->view->active_menu="verifyta/index2";
			// layout
			$this->_helper->layout()->setLayout('second');
			// Title Browser
			$this->view->title = "Verifikasi TA (Prodi) - Daftar Pengajuan";
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
				$prp = new PrpUjianTa();
				$getPrp = $prp->getPrpApproverByPaketKelas($kd_paket);
				$this->view->listPrp=$getPrp;
				// navigation
				$this->_helper->navbar('verifyta/list2',0,0,0,0);
			}else{
				$this->view->eksis="f";
				// navigation
				$this->_helper->navbar('verifyta/index2',0,0,0,0);
			}
		}
	}

	function detil3Action(){
		$user = new Menu();
		$menu = "verifyta/index3";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// treeview
			$this->view->active_menu="verifyta/index3";
			// layout
			$this->_helper->layout()->setLayout('second');
			// Title Browser
			$this->view->title = "Verifikasi TA (Portofolio) - Daftar Pengajuan";
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
				$prp = new PrpUjianTa();
				$getPrp = $prp->getPrpApproverByPaketKelas($kd_paket);
				$this->view->listPrp=$getPrp;
				// navigation
				$this->_helper->navbar('verifyta/list3',0,0,0,0);
			}else{
				$this->view->eksis="f";
				// navigation
				$this->_helper->navbar('verifyta/index3',0,0,0,0);
			}
		}
	}

}