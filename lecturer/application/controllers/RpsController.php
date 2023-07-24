<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: RPS Controller -> Controller untuk modul halaman RPS
*/
class RpsController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('JnsKelas');
		Zend_Loader::loadClass('Kelas');
		Zend_Loader::loadClass('Kurikulum');
		Zend_Loader::loadClass('KalenderAkd');
		Zend_Loader::loadClass('Rps');
		Zend_Loader::loadClass('TimTeaching');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Validation');
		Zend_Loader::loadClass('PHPExcel');
		Zend_Loader::loadClass('PHPExcel_Cell_AdvancedValueBinder');
		Zend_Loader::loadClass('PHPExcel_IOFactory');
		$auth = Zend_Auth::getInstance();
		$ses_lec = new Zend_Session_Namespace('ses_lec');
		if (($auth->hasIdentity())and($ses_lec->uname)) {
			$this->view->namadsn =Zend_Auth::getInstance()->getIdentity()->nm_dosen;
			$this->view->kddsn=Zend_Auth::getInstance()->getIdentity()->kd_dosen;
			$this->view->kd_pt=$ses_lec->kd_pt;
			$this->view->nm_pt=$ses_lec->nm_pt;
			// global var
			$this->kd_dsn=Zend_Auth::getInstance()->getIdentity()->kd_dosen;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// nav menu
		$this->view->kls_act="active";
	}

	function indexAction(){
		// navigation
		$this->_helper->navbar('kelas/list',0);
		// get kd
		$kd_kelas=$this->_request->get('id');
		// get data from data base
		$kelas=new Kelas();
		$getKelas=$kelas->getKelasByKd($kd_kelas);
		if($getKelas){
			foreach ($getKelas as $data_kls) {
				$this->view->kd_kls=$data_kls['kd_kelas'];
				$this->view->kd_dsn=$data_kls['kd_dosen'];
				$kd_dsn=$data_kls['kd_dosen'];
				$id_mk_kur=$data_kls['id_mk_kurikulum'];
				$this->view->id_mk_kur=$data_kls['id_mk_kurikulum'];
				$this->view->nm_dsn=$data_kls['nm_dosen'];
				$this->view->kd_mk=$data_kls['kode_mk'];
				$this->view->nm_mk=$data_kls['nm_mk'];
				$this->view->kd_per=$data_kls['kd_periode'];
				$kd_periode=$data_kls['kd_periode'];
				$this->view->nm_prodi=$data_kls['nm_prodi_kur'];
				$this->view->jns_kelas=$data_kls['jns_kelas'];
				$this->view->ttpmk=$data_kls['tatap_muka'];
				$this->view->sks=($data_kls['sks_tm']+$data_kls['sks_prak']+$data_kls['sks_prak_lap']+$data_kls['sks_sim']);
				$nm_mk=$data_kls['nm_mk'];
				$nm_dsn=$data_kls['nm_dosen'];
				$nm_prodi=$data_kls['nm_prodi_kur'];
			}
			$timTeaching=new TimTeaching();
			$getKelasTt=$timTeaching->getTimTeachingByKelas($kd_kelas);
			$found=0;
			if ($getKelasTt){
				foreach($getKelasTt as $tt){
					if($tt['kd_dosen']==$this->kd_dsn){
						$found=$found+1;
					}
				}
			}
			if(($kd_dsn==$this->kd_dsn)or($found>0)){
				// title
				$this->view->title="RPS Mata Kuliah ".$nm_mk." Periode ".$kd_periode;
				$rps = new Rps();
				$getRps=$rps->getRpsByMkKurPeriode($id_mk_kur,$kd_periode);
				$id_rps="";
				if($getRps){
					foreach ($getRps as $dtRps){
						$id_rps=$dtRps['id_rps'];
					}
					$getRpsDetil=$rps->getRpsDetilByRps($id_rps);
					$this->view->listRpsDetil=$getRpsDetil;
				}else{
					$this->view->listRpsDetil=array();
				}
				$this->view->listRps=$getRps;
				$this->view->id_rps=$id_rps;
			}else{
				// title
				$this->view->title="RPS Mata Kuliah";
				$this->view->eksis ='f';
			}
		}else{
			// title
			$this->view->title="RPS Mata Kuliah";
			$this->view->eksis ='f';
		}
	}

	function editAction(){
		// get data RPS
		$id=$this->_request->get('id');
		$kd_kls=$this->_request->get('kls');
		$this->view->kd_kls=$kd_kls;
		$rps =  new Rps();
		$getRps = $rps->getRpsById($id);
		if($getRps){
			foreach ($getRps as $dataRps) {
				$this->view->id_rps=$dataRps['id_rps'];
				$this->view->id_mk_kur=$dataRps['id_mk_kurikulum'];
				$id_mk_kur=$dataRps['id_mk_kurikulum'];
				$this->view->capaian=$dataRps['capaian'];
				$nmMk=$dataRps['nm_mk'];
				$kd_per=$dataRps['kd_periode'];
			}
			// Title Browser
			$this->view->title = "Edit RPS ".$nmMk." Periode ".$kd_per;
			// navigation
			$this->_helper->navbar('rps/index?id='.$kd_kls,0,0,0,0);
		}else{
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Edit RPS";
			// navigation
			$this->_helper->navbar("kurikulum/list",0,0,0,0);
		}
	}

}