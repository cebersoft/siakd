<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Home Controller -> Controller untuk modul home
*/
class TraceridController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Kuisioner');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		$auth = Zend_Auth::getInstance();
		$ses_std = new Zend_Session_Namespace('ses_std');
		if (($auth->hasIdentity())and($ses_std->uname)) {
			$this->view->namamhs =Zend_Auth::getInstance()->getIdentity()->nm_mhs;
			$this->view->nim=Zend_Auth::getInstance()->getIdentity()->nim;
			$this->view->idmhs=Zend_Auth::getInstance()->getIdentity()->id_mhs;
			$this->view->kd_pt=$ses_std->kd_pt;
			$this->view->nm_pt=$ses_std->nm_pt;
			// global var
			$this->uname=Zend_Auth::getInstance()->getIdentity()->nim;
			$this->id=Zend_Auth::getInstance()->getIdentity()->id_mhs;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// navigation
		$this->_helper->navbar(0,0);
		// nav menu
		$this->view->trc_act="active";
	}

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Tracer ID Alumni";
		// get profile pt
		$profil = new Profile();
		$getProfil = $profil->fetchAll();
		foreach ($getProfil as $dtProf){
			$this->view->visi=$dtProf['visi'];
			$this->view->misi=$dtProf['misi'];		
		}
		// nav menu
		$this->view->home_act="active";
		$nim=$this->uname;
		$mhs = new Mahasiswa();
		$getMhs = $mhs->getMahasiswaByNim($nim);
		if (!$getMhs){
			$this->view->eksis='f';
		}else{
			foreach($getMhs as $data){
				$this->view->nim_mhs=$data['nim'];
				$this->view->nama_mhs=$data['nm_mhs'];
				$this->view->status_keluar=$data['id_jns_keluar'];
				$jnsKel=$data['id_jns_keluar'];
				$arrTgl_keluar=explode("-", $data['tgl_keluar']);
				$this->view->thn_keluar=$arrTgl_keluar[0];
				$this->view->kd_prodi = $data['kd_prodi'];;
			}
			if($jnsKel=='1'){
				$kuis = new Kuisioner();
				$getKuis=$kuis->getKuisionerByNim($nim);
				if($getKuis){
					$this->_redirect('/tracerid/success');	
				}
			}else{
				$this->_redirect('/tracerid/noaccess');
			}
		}
	}

	function index_1Action()
	{
		// Title Browser
		$this->view->title = "Tracer ID Alumni";
		$nim=$this->uname;
		$mhs = new Mahasiswa();
		$getMhs = $mhs->getMahasiswaByNim($nim);
		if (!$getMhs){
			$this->view->eksis='f';
		}else{
			foreach($getMhs as $data){
				$this->view->nim_mhs=$data['nim'];
				$this->view->nama_mhs=$data['nm_mhs'];
				$this->view->status_keluar=$data['id_jns_keluar'];
				$jnsKel=$data['id_jns_keluar'];
				$arrTgl_keluar=explode("-", $data['tgl_keluar']);
				$this->view->thn_keluar=$arrTgl_keluar[0];
				$this->view->kd_prodi = $data['kd_prodi'];;
			}
			if($jnsKel=='1'){
				$kuis = new Kuisioner();
				$getKuis=$kuis->getKuisionerByNim($nim);
				if($getKuis){
					$this->_redirect('/tracerid/success');	
				}
			}else{
				$this->_redirect('/tracerid/noaccess');
			}
		}
	}

	function index4Action()
	{
		// Title Browser
		$this->view->title = "Tracer ID Alumni";
		// get profile pt
		$profil = new Profile();
		$getProfil = $profil->fetchAll();
		foreach ($getProfil as $dtProf){
			$this->view->visi=$dtProf['visi'];
			$this->view->misi=$dtProf['misi'];		
		}
		// nav menu
		$this->view->home_act="active";
		$nim=$this->uname;
		$mhs = new Mahasiswa();
		$getMhs = $mhs->getMahasiswaByNim($nim);
		if (!$getMhs){
			$this->view->eksis='f';
		}else{
			foreach($getMhs as $data){
				$this->view->nim_mhs=$data['nim'];
				$this->view->nama_mhs=$data['nm_mhs'];
				$this->view->status_keluar=$data['id_jns_keluar'];
				$jnsKel=$data['id_jns_keluar'];
				$arrTgl_keluar=explode("-", $data['tgl_keluar']);
				$this->view->thn_keluar=$arrTgl_keluar[0];
				$this->view->kd_prodi = $data['kd_prodi'];
				$this->view->nik=$data['nik'];
				$this->view->large_kontak=$data['large_kontak'];
				$this->view->email=$data['email_lain'];
			}
			$kuis = new Kuisioner();
			$getQuestion=$kuis->getQuestion();
			$dtKuis=array();
			$dtChoice=array();
			$i=0;
			$j=0;
			foreach($getQuestion as $dtQuestion){
				$question0_id=$dtQuestion['question0_id'];
				$dtKuis[$i]['question1_id']=$dtQuestion['question1_id'];
				$dtKuis[$i]['question1_text']=$dtQuestion['question1_text'];
				$dtKuis[$i]['question1_type']=$dtQuestion['question1_type'];
				$dtKuis[$i]['question1_code']=$dtQuestion['question1_code'];
				$q1=$dtQuestion['question1_id'];
				$getChoice=$kuis->getChoiceByQuestion1($q1);
				foreach($getChoice as $dataChoice){
					$dtChoice[$j]['question1_id']=$dataChoice['question1_id'];
					$dtChoice[$j]['choice_label']=$dataChoice['choice_label'];
					$dtChoice[$j]['choice_code']=$dataChoice['choice_code'];
					$dtChoice[$j]['choice_value']=$dataChoice['choice_value'];
					$dtChoice[$j]['choice_ext']=$dataChoice['choice_ext'];
					$dtChoice[$j]['choice_ext_code']=$dataChoice['choice_ext_code'];
					$j++;
				}
				$i++;
			}
			$this->view->id0=$question0_id;
			$this->view->listKuis=$dtKuis;
			$this->view->listChoice=$dtChoice;
			if($jnsKel=='1'){
				$getKuis=$kuis->getKuisionerNewByNim($nim);
				if($getKuis){
					$this->_redirect('/tracerid/success');	
				}
			}else{
				$this->_redirect('/tracerid/noaccess');
			}
		}
	}


	function successAction(){
		// Title Browser
		$this->view->title = "Tracer ID Alumni";		
	}

	function noaccessAction(){
		// Title Browser
		$this->view->title = "Tracer ID Alumni";
	}
}