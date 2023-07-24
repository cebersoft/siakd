<?php
/*
 Programmer	: Tiar Aristian
Release		: Januari 2016
Module		: Feeder Dosen Controller -> Controller untuk modul halaman dosen-feeder
*/
class ZZfdsnController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Soap_Client');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('FeederMk');
		Zend_Loader::loadClass('FeederKls');
		Zend_Loader::loadClass('FeederDsn');
		Zend_Loader::loadClass('FeederProdi');
		Zend_Loader::loadClass('ConfigKurikulum');
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
		$this->view->active_menu="zzfdsn/index";
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
		// Title Browser
		$this->view->title = "Sinkronisasi Data Ajar Dosen - Feeder";
		// layout
		$this->_helper->layout()->setLayout('main');
		// control navigasi back
		$this->view->disabled_back="disabled='disabled'";
		// destroy session param
		Zend_Session::namespaceUnset('param_fdsn');
		// get data prodi
		$prodi = new Prodi();
		$listProdi = $prodi->fetchAll();
		$this->view->listProdi=$listProdi;
		// get data periode
		$per = new Periode();
		$this->view->listPeriode=$per->fetchAll();
		// navigation
		$this->_helper->navbar(0,0,0,0,0);
	}

	function listAction(){
		// token
		$ses_feeder = new Zend_Session_Namespace('ses_feeder');
		$token=$ses_feeder->token;
		$id_sp=$ses_feeder->id_sp;
		$url=$ses_feeder->url;
		// layout
		$this->_helper->layout()->setLayout('second');
		// navigation
		$this->_helper->navbar('zzfdsn',0,0,0,0);
		// show data
		$param = new Zend_Session_Namespace('param_fdsn');
		$per = $param->per;
		$prd = $param->prd;
		$opt=$param->opt;
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
			$this->view->title = "Sinkronisasi Data Ajar Dosen Periode ".$per." Program Studi ".$nm_prodi;
			$this->view->prd=$kd_prodi;
			// get data ajar dosen from SIA
			$feederDsn = new FeederDsn();
			$this->view->listDsnSIA = $feederDsn->getKelasDosenByPeriodeProdi($per, $prd);
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
			$klsFeeder = new FeederKls();
			$dtKlsFeeder = $klsFeeder->getDetailKelasKuliah($token, "id_prodi='$id_prodi' and id_semester='$id_smt'", "", 1000, 0, $url);
			$dtKlsFeeder = json_decode($dtKlsFeeder,true);
			$id_kls=array();
			if($dtKlsFeeder['data']){
				$i=0;
				foreach ($dtKlsFeeder['data'] as $dtKls){
					$id_kls[$i]=$dtKls['id_kelas_kuliah'];
					$i++;
				}
			}
			// ajar dosen Feeder
			$id_kls=$this->to_pg_array($id_kls);
			$getKlsDsn = $feederDsn->GetDosenKelas($token, "id_kelas_kuliah=any('$id_kls')", "", 1000, 0, $url);
			$getKlsDsn = json_decode($getKlsDsn,true);
			$i=0;
			//$getPenugasan = $feederDsn->GetDetilPenugasanDosen($token, "trim(nidn)=trim('$nidn') and id_perguruan_tinggi='$id_sp' and id_tahun_ajaran='2016'", "", 1, 0, $url);
			//$getPenugasan=json_decode($getPenugasan,true);
			//$this->view->listPenugasan=$getPenugasan['data'];
			$j=0;
			foreach ($getKlsDsn['data'] as $dataKlsDsn){
				$idKlsKul=$dataKlsDsn['id_kelas_kuliah'];
				$getKlsKuliah = $klsFeeder->getDetailKelasKuliah($token, "id_kelas_kuliah='$idKlsKul'", "", 1, 0, $url);
				$getKlsKuliah = json_decode($getKlsKuliah,true);
				foreach ($getKlsKuliah['data'] as $dtKlsKuliah){
					$getKlsDsn['data'][$j]['kode_mata_kuliah']=$dtKlsKuliah['kode_mata_kuliah'];
					$getKlsDsn['data'][$j]['nama_mata_kuliah']=$dtKlsKuliah['nama_mata_kuliah'];
				}
				$j++;
			}
			$this->view->listKlsDsnFeeder=$getKlsDsn;
			// kurikulum feeder
			$ckur = new ConfigKurikulum();
			$getKurikulum=$ckur->getDataKurikulumByProdiPeriode($prd, $per);
			$id_kur="";
			if($getKurikulum){
				foreach ($getKurikulum as $dtKur) {
					$id_kur=$dtKur['id_kurikulum_sp'];
				}
			}
			$this->view->id_kur=$id_kur;
		}
	}
}