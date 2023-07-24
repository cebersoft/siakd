<?php
/*
	Programmer	: Tiar Aristian
	Release		: Maret 2016
	Module		: Feeder Config
*/
class ZzfconfigController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Zend_Soap_Client');
		Zend_Loader::loadClass('WsConfig');
		Zend_Loader::loadClass('ConfigKurikulum');
		Zend_Loader::loadClass('FeederKurikulum');
		Zend_Loader::loadClass('FeederProdi');
		Zend_Loader::loadClass('FeederGen');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Periode');
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
		// layout
		$this->_helper->layout()->setLayout('main');
		// navigation
		$this->_helper->navbar(0,0,0,0,0);
		// treeview
		$this->view->active_tree="13";
		$this->view->active_menu="zzfconfig/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "zzfconfig/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Konfigurasi WS Feeder";
			$config = new WsConfig();
			$this->view->listConfig=$config->fetchAll();
			// session
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			if($ses_feeder->token){
				$this->view->ses_feeder='1';
				// token
				$token=$ses_feeder->token;
				$id_sp=$ses_feeder->id_sp;
				$url=$ses_feeder->url;
				$ckur = new ConfigKurikulum();
				$this->view->listConfkur=$ckur->getDataKurikulumProdi();
				// kur feeder
				$kurFeeder=new FeederKurikulum();
				$getKurFeeder=$kurFeeder->getListKurikulum($url, $token);
				$resultKur = json_decode($getKurFeeder, true);
				$this->view->listKurFeeder=$resultKur['data'];
				// get data prodi
				$prodi = new Prodi();
				$listProdi = $prodi->fetchAll();
				$this->view->listProdi=$listProdi;
				// get data periode
				$per = new Periode();
				$this->view->listPeriode=$per->fetchAll();
				// get data wilayah
				$fg=new FeederGen();
				$listWil=$fg->getWilayah($token, '', '', '100000', 0, $url);
				$this->view->listWilayah=json_decode($listWil,true);
				// get data kebutuhan
				$listKK=$fg->getKebutuhanKhusus($token, '', '', '10000', 0, $url);
				$this->view->listKK=json_decode($listKK,true);
				// get agama
				$listAgama=$fg->getAgama($token, '', '', '10000', 0, $url);
				$this->view->listAgama=json_decode($listAgama,true);
				// get dic
				$listDic=$fg->getDictionary($token, '', '', '1', 0, $url);
				$this->view->listDic=json_decode($listDic,true);
			}
		}
	}
	
	function aksesAction()
	{
		// Title Browser
		$this->view->title = "Akses WS Feeder";
		$this->_helper->layout()->setLayout('source');
	}
}