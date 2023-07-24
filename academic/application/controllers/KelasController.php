<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Kelas Controller -> Controller untuk modul halaman kelas
*/
class KelasController extends Zend_Controller_Action
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
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Validation');
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
		$this->view->active_tree="04";
		$this->view->active_menu="kelas/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "kelas/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Kelas";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// destroy session param
			Zend_Session::namespaceUnset('param_kls');
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
	}

	function listAction(){
		$user = new Menu();
		$menu = "kelas/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// show data
			$param = new Zend_Session_Namespace('param_kls');
			$sb = $param->sb;
			$kd_prodi = $param->prd;
			$kd_periode = $param->per;
			$id_jenis = $param->jns;
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
			$this->view->title = "Daftar Kelas ".$nm_jenis. " Prodi ".$nm_prd;
			$this->view->kd_periode=$kd_periode;
			$this->view->jns=$id_jenis;
			// navigation
			$this->_helper->navbar('kelas',0,0,'paketkelas/export?prd='.$kd_prodi.'&per='.$kd_periode.'&jns='.$id_jenis,0);
			// get data 
			$kelas = new Kelas();
			$getKelas = $kelas->getKelasByPeriodeProdiJenis($kd_periode,$kd_prodi,$id_jenis);
			$this->view->listKelas = $getKelas;
		}
	}

	function detilAction(){
		$user = new Menu();
		$menu = "kelas/detil";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// navigation
			$this->_helper->navbar('kelas/list',0,0,0,0);
			// get kd
			$kd_kelas=$this->_request->get('id');
			// get data from data base
			$kelas=new Kelas();
			$getKelas=$kelas->getKelasByKd($kd_kelas);
			if($getKelas){
				foreach ($getKelas as $data_kls) {
					$this->view->kd_kls=$data_kls['kd_kelas'];
					$this->view->kd_dsn=$data_kls['kd_dosen'];
					$this->view->nm_dsn=$data_kls['nm_dosen'];
					$this->view->kd_mk=$data_kls['kode_mk'];
					$this->view->nm_mk=$data_kls['nm_mk'];
					$this->view->kd_per=$data_kls['kd_periode'];
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
				// title
				$this->view->title="Kelas ".$nm_mk."(".$nm_dsn.")-".$nm_prodi;
			}else{
				// title
				$this->view->title="Detil Kelas";
				$this->view->eksis ='f';
			}
		}
	}
}