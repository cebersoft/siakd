<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Feeder Mahasiswa Controller -> Controller untuk modul halaman mahasiswa-Feeder
*/
class ZzfmhsController extends Zend_Controller_Action
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
		Zend_Loader::loadClass('FeederMhs');
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
		$this->view->active_menu="zzfmhs/index";
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
		$menu = "zzfmhs/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			$ses_feeder = new Zend_Session_Namespace('ses_feeder');
			if(!$ses_feeder->token){
				$this->_redirect('zzfconfig');
			}else{
				// Title Browser
				$this->view->title = "Sinkronisasi Data Mahasiswa - Feeder";
				// layout
				$this->_helper->layout()->setLayout('main');
				// control navigasi back
				$this->view->disabled_back="disabled='disabled'";
				// destroy session param
				Zend_Session::namespaceUnset('param_fmhs');
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
		$menu = "zzfmhs/index";
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
				$this->_helper->navbar('zzfmhs',0,0,0,0);
				// show data
				$param = new Zend_Session_Namespace('param_fmhs');
				$akt = $param->akt;
				$prd = $param->prd;
				$opt=$param->opt;
				$smt_mulai=$akt."/".($akt+1)." Ganjil";
				$smt_mulai2=$akt."/".($akt+1)." Genap";
				// option view
				$this->view->opt=$opt;
				// get data prodi
				$feederProdi = new FeederProdi();
				$getProdi = $feederProdi->getProdi($token, "kode_program_studi='$prd'", "", 1, 0, $url);
				$getProdi=json_decode($getProdi,true);
				if (!$getProdi['data']){
					$this->view->eksis="f";
					// Title Browser
					$this->view->title = "Sinkronisasi Data Mahasiswa";
				}else{
					$dataProdi=$getProdi['data'];
					$id_prodi="";
					foreach ($dataProdi as $dtProdi){
						$id_prodi=$dtProdi['id_prodi'];
					}
					// Title Browser
					$this->view->title = "Sinkronisasi Data Mahasiswa Angkatan ".$akt." Program Studi ".$prd;
					$this->view->prd=$prd;
					// get data mahasiswa
					$mhsFeeder = new FeederMhs();
					$this->view->listMhsSIA  = $mhsFeeder->getMahasiswaByAngkatanProdi($akt,$prd);
					$getListMhsFeeder = $mhsFeeder->getListRiwayatPendidikanMahasiswa($token, "id_prodi='$id_prodi' "." and (nama_periode_masuk='$smt_mulai' or nama_periode_masuk='$smt_mulai2') ", "", 500, 0, $url);
					$resListMhsFeeder=json_decode($getListMhsFeeder,true);
					$id_pd=array();
					$i=0;
					foreach ($resListMhsFeeder['data'] as $dataFeeder){
						$id_pd[$i]=$dataFeeder['id_mahasiswa'];
						$i++;
					}
					$id_pd=$this->to_pg_array($id_pd);
					$getBioMahasiswaFeeder=$mhsFeeder->getBiodataMahasiswa($token, "id_mahasiswa=any('$id_pd')", "", 500, 0, $url);
					$resBioMhsFeeder=json_decode($getBioMahasiswaFeeder,true);
					$this->view->listProfMhsFeeder=$resBioMhsFeeder['data'];
					$this->view->listMhsFeeder=$resListMhsFeeder['data'];
				}
			}
		}
	}
	
	function detilAction() {
		$user = new Menu();
		$menu = "zzfmhs/index";
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
				$request = $this->getRequest()->getPost();
				$param=$request['param'];
			    $nim=$param[0];
			    $kode_prodi=$param[1];
			    // get prodi
			    $feederProdi=new FeederProdi();
			    $getProdi = $feederProdi->getProdi($token, "kode_program_studi='$kode_prodi'", "", 1, 0, $url);
			    $resProdi=json_decode($getProdi,true);
			    if($resProdi['error_desc']==''){
			    	$id_prodi="";
				    foreach ($resProdi['data'] as $dtSms){
				    	$id_prodi = $dtSms['id_prodi'];	
				    }
			    }
				// get data mahasiswa
			    $mhsFeeder = new FeederMhs();
			    $this->view->listMhsSIA  = $mhsFeeder->getMahasiswaByNim($nim);
				$mhsFeeder = new FeederMhs();
				$getRegMhsFeeder  = $mhsFeeder->getListRiwayatPendidikanMahasiswa($token, "nim='$nim' and id_prodi='$id_prodi'", "", 1, 0, $url);
				$resRegMhsFeeder=json_decode($getRegMhsFeeder,true);
				$id_mahasiswa="";
				foreach ($resRegMhsFeeder['data'] as $dt){
					$id_mahasiswa=$dt['id_mahasiswa'];	
				}
				$getBioMhsFeeder=$mhsFeeder->getBiodataMahasiswa($token, "id_mahasiswa='$id_mahasiswa'", "", 1, 0, $url);
				$resBioMhsFeeder=json_decode($getBioMhsFeeder,true);
				$this->view->listMhsFeeder=$resRegMhsFeeder['data'];
				$this->view->listProfMhsFeeder=$resBioMhsFeeder['data'];
			}
		}
	}
}