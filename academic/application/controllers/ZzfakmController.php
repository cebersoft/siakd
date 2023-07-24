<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Feeder AKM Controller -> Controller untuk modul halaman akm-feeder
*/
class ZzfakmController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Soap_Client');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('FeederMhs');
		Zend_Loader::loadClass('FeederAkm');
		Zend_Loader::loadClass('FeederProdi');
		Zend_Loader::loadClass('MhsBiayaPeriode');
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
		$this->view->active_menu="zzfakm/index";
	}
	
	private function to_pg_array($set) {
		settype($set, 'array');
		$result = array();
		foreach ($set as $t) {
			if (is_array($t)) {
				$result[] = to_pg_array($t);
			} else {
				$t = str_replace('"', '\\"', $t);
				if (! is_numeric($t))
					$t = '"' . $t . '"';
					$result[] = $t;
			}
		}
		return '{' . implode(",", $result) . '}';
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "zzfakm/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			if(!$ses_feeder->token){
				$this->_redirect('zzfconfig');
			}else{
				// Title Browser
				$this->view->title = "Sinkronisasi Data Registrasi Periode (AKM) - Feeder";
				// layout
				$this->_helper->layout()->setLayout('main');
				// control navigasi back
				$this->view->disabled_back="disabled='disabled'";
				// destroy session param
				Zend_Session::namespaceUnset('param_fakm');
				// get data prodi
				$prodi = new Prodi();
				$listProdi = $prodi->fetchAll();
				$this->view->listProdi=$listProdi;
				// get data angkatan
				$akt = new Angkatan();
				$this->view->listAkt=$akt->fetchAll();
				// get data periode
				$periode = new Periode();
				$listPeriode = $periode->fetchAll();
				$this->view->listPeriode=$listPeriode;
				// navigation
				$this->_helper->navbar(0,0,0,0,0);	
			}
		}
	}
	
	function listAction() 
	{
		$user = new Menu();
		$menu = "zzfakm/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// token
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			$token=$ses_feeder->token;
			$id_sp=$ses_feeder->id_sp;
			$url=$ses_feeder->url;
			// layout
			$this->_helper->layout()->setLayout('second');
			// navigation
			$this->_helper->navbar('zzfakm',0,0,0,0);
			// show data
			$param = new Zend_Session_Namespace('param_fakm');
			$per = $param->per;
			$prd = $param->prd;
			$akt = $param->akt;
			$smt_m = $param->smt_m;
			$opt=$param->opt;
			$smt_mulai=$akt."/".($akt+1)." ".$smt_m;
			// option view
			$this->view->opt=$opt;
			// get data prodi
			$feederProdi = new FeederProdi();
			$getProdi = $feederProdi->getProdi($token, "kode_program_studi='$prd'", "", 1, 0, $url);
			$resProdi=json_decode($getProdi,true);
			if (!$resProdi['data']){
				$this->view->eksis="f";
			}else{
				// feeder side
				foreach ($resProdi['data'] as $dataProdi) {
					$id_prodi=$dataProdi['id_prodi'];
					$nm_prodi = $dataProdi['nama_program_studi'];
					$kd_prodi = $dataProdi['kode_program_studi'];
				}
				// Title Browser
				$this->view->title = "Sinkronisasi Data AKM angkatan ".$akt."(".$nm_prodi.") -".$per;
				$this->view->prd=$prd;
				// get data AKM from SIA
				$akmFeeder = new FeederAkm();
				$this->view->listAKMSia  = $akmFeeder->getRegByAktProdiPeriode($akt, $prd, $per);
				// get data finance
				$mhsBiaya=new MhsBiayaPeriode();
				$this->view->listBiaya = $mhsBiaya->getMhsBiayaPeriodeByAngkatanProdiPeriode($akt,$prd,$per);
				// cacah periode
				$arrPeriode = explode('/', $per);
				$smt=explode('-', $arrPeriode[0]);
				if($arrPeriode[1]=='GASAL'){
					$s='1';
				}elseif ($arrPeriode[1]=='GENAP'){
					$s='2';
				}else {
					$s='3';
				}
				$id_smt=$smt[0].$s;
				$this->view->id_smt=$id_smt;
				$feederMhs = new FeederMhs();
				$getMhsFeeder = $feederMhs->getListRiwayatPendidikanMahasiswa($token, "id_prodi='$id_prodi' "."and nama_periode_masuk='$smt_mulai'", "", 500, 0, $url);
				$getMhsFeeder = json_decode($getMhsFeeder,true);
				$arrReg=array();
				$i=0;
				foreach ($getMhsFeeder['data'] as $dtMhs){
					$arrReg[$i] = $dtMhs['id_registrasi_mahasiswa'];
					$i++;
				}
				$arrReg=$this->to_pg_array($arrReg);
				$feederAkm = new FeederAkm();
				$getAkmFeeder = $feederAkm->getDetailPerkuliahan($token, "id_registrasi_mahasiswa=any('$arrReg') and id_semester='$id_smt'", "", 1000, 0, $url);
				$getAkmFeeder = json_decode($getAkmFeeder,true);
				// view
				$this->view->listAkmFeeder=$getAkmFeeder['data'];
				$this->view->listMhsFeeder=$getMhsFeeder['data'];
				//$this->view->x=count($arrReg);
				$this->view->x=$smt_mulai;
			}
		}
	}
}