<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Lms Controller -> Controller untuk modul halaman lms
*/
class LmsController extends Zend_Controller_Action
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
		Zend_Loader::loadClass('KelasLms');
		Zend_Loader::loadClass('PaketkelasLms');
		Zend_Loader::loadClass('KelompokLms');
		Zend_Loader::loadClass('Kurikulum');
		Zend_Loader::loadClass('KalenderAkd');
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
		$this->view->lms_act="active";
	}
	
	function indexAction(){
		// Title Browser
		$this->view->title = "Daftar Kelas (Online Learning)";
		// navigation
		$this->_helper->navbar(0,0);
		// destroy session param
		Zend_Session::namespaceUnset('param_dsn_kls_lms');
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
		$jnsKls=new JnsKelas();
		$this->view->listJnsKelas=$jnsKls->fetchAll();
	}
	
	function listAction(){
		// show data
		$param = new Zend_Session_Namespace('param_dsn_kls_lms');
		$kd_prodi = $param->prd;
		$kd_periode = $param->per;
		$id_jenis = $param->jns;
		$kd_dsn=$this->kd_dsn;
		// data kurikulum
		$kurikulum = new Kurikulum();
		$this->view->listKurikulum=$kurikulum->getKurByProdi($kd_prodi);
		// get data prodi dan jenis kelas
		$nm_prd="";
		$nm_jenis="";
		$prodi = new Prodi();
		$listProdi=$prodi->fetchAll();
		foreach ($listProdi as $dataPrd) {
			if($kd_prodi==$dataPrd['kd_prodi']){
				$nm_prd=$dataPrd['nm_prodi'];
			}
		}
		$jenis = new JnsKelas();
		$listJnsKelas=$jenis->fetchAll();
		foreach ($listJnsKelas as $dataJns) {
			if($id_jenis==$dataJns['id_jns_kelas']){
				$nm_jenis=$dataJns['jns_kelas'];
			}
		}
		// Title Browser
		$this->view->title = "Daftar Kelas Online ".$nm_jenis. " Prodi ".$nm_prd;
		$this->view->kd_periode=$kd_periode;
		$this->view->jns=$id_jenis;
		// navigation
		$this->_helper->navbar('lms',0);
		// get data 
		$kelas = new KelasLms();
		$getKelas = $kelas->getKelasLmsByPeriodeProdiJenis($kd_periode,$kd_prodi,$id_jenis);
		$arrKelas=array();
		$i=0;
		foreach ($getKelas as $dtKelas){
			if($dtKelas['kd_dosen']==$kd_dsn){
				$arrKelas[$i]['kd_kelas']=$dtKelas['kd_kelas'];
				$arrKelas[$i]['smt_def']=$dtKelas['smt_def'];
				$arrKelas[$i]['kode_mk']=$dtKelas['kode_mk'];
				$arrKelas[$i]['nm_mk']=$dtKelas['nm_mk'];
				$arrKelas[$i]['nm_dosen']=$dtKelas['nm_dosen'];
				$arrKelas[$i]['tatap_muka']=$dtKelas['tatap_muka'];
				$arrKelas[$i]['jns_kelas']=$dtKelas['jns_kelas'];
				$arrKelas[$i]['n_paket_kelas']=$dtKelas['n_paket_kelas'];
				$arrKelas[$i]['n_tim_teaching']=$dtKelas['n_tim_teaching'];
				$arrKelas[$i]['n_bahan_ajar']=$dtKelas['n_bahan_ajar'];
				$i++;
			}
		}
		$this->view->listKelas = $arrKelas;
		$timTeaching=new TimTeaching();
		$getKelasTt=$timTeaching->getTimTeachingByDosenPeriode($kd_dsn,$kd_periode);
		$this->view->listKelasTt = $getKelasTt;
	}
	
	function detilAction(){
		// navigation
		$this->_helper->navbar('lms/list',0);
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
				// parameter
				$this->view->p_p1=$data_kls['p_p1'];
				$this->view->nm_p1=$data_kls['nm_p1'];
				$this->view->p_p2=$data_kls['p_p2'];
				$this->view->nm_p2=$data_kls['nm_p2'];
				$this->view->p_p3=$data_kls['p_p3'];
				$this->view->nm_p3=$data_kls['nm_p3'];
				$this->view->p_p4=$data_kls['p_p4'];
				$this->view->nm_p4=$data_kls['nm_p4'];
				$this->view->p_p5=$data_kls['p_p5'];
				$this->view->nm_p5=$data_kls['nm_p5'];
				$this->view->p_p6=$data_kls['p_p6'];
				$this->view->nm_p6=$data_kls['nm_p6'];
				$this->view->p_p7=$data_kls['p_p7'];
				$this->view->nm_p7=$data_kls['nm_p7'];
				$this->view->p_p8=$data_kls['p_p8'];
				$this->view->nm_p8=$data_kls['nm_p8'];
				$this->view->p_uts=$data_kls['p_uts'];
				$this->view->p_uas=$data_kls['p_uas'];
				$this->view->note=$data_kls['note_dosen'];
				$this->view->p_tot=number_format(($data_kls['p_p1']+$data_kls['p_p2']+$data_kls['p_p3']+$data_kls['p_p4']+$data_kls['p_p5']+$data_kls['p_p6']+$data_kls['p_p7']+$data_kls['p_p8']+$data_kls['p_uts']+$data_kls['p_uas']),2,',','.');
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
				$this->view->title="Kelas Online ".$nm_mk." - ".$nm_prodi;	
				// get paket
				$paket=new PaketkelasLms();
				$this->view->listPaket=$paket->getPaketKelasLmsByKelas($kd_kelas);
				// get kelompok
				//$kelompok=new KelompokLms();
				//$this->view->listKelompok=$kelompok->getKelompokLmsByKelas($kd_kelas);
			}else{
				// title
				$this->view->title="Detil Kelas";
				$this->view->eksis ='f';
			}
		}else{
			// title
			$this->view->title="Detil Kelas";
			$this->view->eksis ='f';
		}
	}
}