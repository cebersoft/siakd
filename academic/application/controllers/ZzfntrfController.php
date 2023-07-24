<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Feeder Nilai transfer
*/
class ZzfntrfController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Soap_Client');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('FeederNilaiTf');
		Zend_Loader::loadClass('FeederProdi');
		Zend_Loader::loadClass('Menu');
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
		$ses_feeder = new Zend_Session_Namespace('ses_feeder');
		if(!$ses_feeder->token){
			$this->_redirect('zzfconfig');
		}
		// treeview
		$this->view->active_tree="13";
		$this->view->active_menu="zzfntrf/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "zzfntrf/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			if(!$ses_feeder->token){
				$this->_redirect('zzfconfig');
			}else{
				// Title Browser
				$this->view->title = "Sinkronisasi Data Nilai Transfer - Feeder";
				// layout
				$this->_helper->layout()->setLayout('main');
				// destroy session param
				Zend_Session::namespaceUnset('param_ftrf');
				// get data prodi
				$prodi = new Prodi();
				$listProdi = $prodi->fetchAll();
				$this->view->listProdi=$listProdi;
				// get data angkatan
				$akt = new Angkatan();
				$this->view->listAkt=$akt->fetchAll();
				// navigation
				$this->_helper->navbar(0,0,0,0,0);
			}
		}
	}

	function listAction(){
		$user = new Menu();
		$menu = "zzfntrf/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			if(!$ses_feeder->token){
				$this->_redirect('zzfconfig');
			}else{
				// token
				$token=$ses_feeder->token;
				$id_sp=$ses_feeder->id_sp;
				$url=$ses_feeder->url;
				// layout
				$this->_helper->layout()->setLayout('second');
				// navigation
				$this->_helper->navbar('zzfntrf',0,0,0,0);
				// show data
				$param = new Zend_Session_Namespace('param_fntrf');
				$akt = $param->akt;
				$prd = $param->prd;
				// get data prodi
				$feederProdi = new FeederProdi();
				$getProdi = $feederProdi->getProdi($token, "kode_program_studi='$prd'", "", 1, 0, $url);
				$getProdi=json_decode($getProdi,true);
				if (!$getProdi['data']){
					$this->view->eksis="f";
					// Title Browser
					$this->view->title = "Sinkronisasi Data Nilai Transfer";
				}else{
					$dataProdi=$getProdi['data'];
					$id_prodi="";
					foreach ($dataProdi as $dtProdi){
						$id_prodi=$dtProdi['id_prodi'];
					}
					// Title Browser
					$this->view->title = "Sinkronisasi Data Nilai Transfer Mahasiswa Konversi ".$akt." Program Studi ".$prd;
					$this->view->prd=$kd_prodi;
					$nilaiTfFeeder=new FeederNilaiTf();
					// get data mahasiswa
					// sia
					$dtMhsSia=$nilaiTfFeeder->getDataNilaiTfByAngkatanProdi($akt, $prd);
					$this->view->listMhsSia=$dtMhsSia;
					// Feeder
					$smt_masuk1=$akt."1";
					$smt_masuk2=$akt."2";
					$dtMhsFeeder = $nilaiTfFeeder->getNilaiTransferPendidikanMahasiswa($token,"id_prodi='$id_prodi' and (id_periode_masuk='$smt_masuk1' or id_periode_masuk='$smt_masuk2')","","5000","0",$url);
					$dtMhsFeeder=json_decode($dtMhsFeeder,true);
					$this->view->listMhsFeeder=$dtMhsFeeder['data'];
				}
			}
		}
	}
	
	function detilAction() {
		$user = new Menu();
		$menu = "zzfntrf/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "Anda tidak memiliki akses atau sesi anda sudah habis, silakan login ulang";
		} else {
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			if(!$ses_feeder->token){
				$this->_redirect('zzfconfig/akses');
			}else{
				// layout
				$this->_helper->layout()->setLayout('source');
				// session id sp
				$id_sp=$ses_feeder->id_sp;
				$token=$ses_feeder->token;
				$url=$ses_feeder->url;
				$nim=$this->_request->get('nim');
			    // get data nilai tf
			    // feeder
			    $feederNTrf=new FeederNilaiTf();
			    $getNtf=$feederNTrf->getDataNilaiTfByNipd($url, $token, $id_sp, $nim);
			    $this->view->nilaiTfFeeder=$getNtf;
			    // sia
			    $getNtfSia=$feederNTrf->getDataNilaiTfByNim($nim);
			    $this->view->nilaiTfSia=$getNtfSia;
			}
		}
	}
}