<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Matkul kurikulum Controller -> Controller untuk modul halaman mata kuliah - kurikulum
*/
class MatkulkurikulumController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('MatkulKurikulum');
		Zend_Loader::loadClass('Kurikulum');
		Zend_Loader::loadClass('KatMatkul');
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
		$this->view->active_tree="02";
		$this->view->active_menu="kurikulum/index";
	}
	
	function indexAction()
	{
		
	}

	function editAction(){
		$user = new Menu();
		$menu = "matkulkurikulum/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			echo "F|Anda tidak memiliki akses";
		} else {
			// get data matkul
			$id=$this->_request->get('id');
			$mkKur =  new MatkulKurikulum();
			$getMkKur = $mkKur->getMatkulKurikulumById($id);
			if($getMkKur){
				foreach ($getMkKur as $dataMkKur) {
					$this->view->id_mk_kur=$dataMkKur['id_mk_kurikulum'];
					$nmMk=$dataMkKur['nm_mk'];
					$nmKur=$dataMkKur['nm_kurikulum'];
					$idKur=$dataMkKur['id_kurikulum'];
					$this->view->id_kurikulum=$dataMkKur['id_kurikulum'];
					$this->view->kd_mk=$dataMkKur['kode_mk'];
					$this->view->id_mk=$dataMkKur['id_mk'];
					$this->view->nm_mk=$dataMkKur['nm_mk'];
					$this->view->sks_tm=$dataMkKur['sks_tm'];
					$this->view->sks_prak=$dataMkKur['sks_prak'];
					$this->view->sks_prak_lap=$dataMkKur['sks_prak_lap'];
					$this->view->sks_sim=$dataMkKur['sks_sim'];
					$this->view->smt_def=$dataMkKur['smt_def'];
					$this->view->id_kat=$dataMkKur['id_kat_mk'];
					if (($dataMkKur['a_teori']=='t')&&($dataMkKur['a_wajib']=='t')&&($dataMkKur['a_ta']=='f')){
						$this->view->jenis_mk0="selected";
					}elseif (($dataMkKur['a_teori']=='t')&&($dataMkKur['a_wajib']=='f')&&($dataMkKur['a_ta']=='f')){
						$this->view->jenis_mk1="selected";
					}elseif (($dataMkKur['a_teori']=='f')&&($dataMkKur['a_wajib']=='t')&&($dataMkKur['a_ta']=='f')){
						$this->view->jenis_mk2="selected";
					}elseif (($dataMkKur['a_teori']=='t')&&($dataMkKur['a_wajib']=='t')&&($dataMkKur['a_ta']=='t')){
						$this->view->jenis_mk3="selected";
					}
				}
				// kat matkul
				$katMatkul = new KatMatkul();
				$this->view->listKatMatkul=$katMatkul->fetchAll();
				$kur = new Kurikulum();
				$getKur = $kur->getKurById($idKur);
				foreach ($getKur as $dataKur) {
					$this->view->smt_normal=$dataKur['smt_normal'];
				}
				// Title Browser
				$this->view->title = "Edit Mata Kuliah ".$nmMk." di Kurikulum : ".$nmKur;
				// navigation
				$this->_helper->navbar('kurikulum/detil?id='.$idKur,0,0,0,0);
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Edit Mata Kuliah";
				// navigation
				$this->_helper->navbar("kurikulum/list",0,0,0,0);
			}
		}
	}
}