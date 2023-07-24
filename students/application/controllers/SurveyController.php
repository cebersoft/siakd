<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Survey
*/
class SurveyController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Kuliah');
		Zend_Loader::loadClass('KuliahTA');
		Zend_Loader::loadClass('Survey');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Validation');
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
		// nav menu
		$this->view->srv_act="active";
	}

	function indexAction(){
		// Title Browser
		$this->view->title = "Survey";
		// navigation
		$this->_helper->navbar(0,0);
		// get data 
		$nim=$this->uname;
		$kd_periode="";
		$periode = new Periode();
		$getPeriodeAktif=$periode->getPeriodeByStatus(0);
		foreach ($getPeriodeAktif as $dtPeriode) {
			$kd_periode=$dtPeriode['kd_periode'];;
		}
		// get data mhs
		$mhs=new Mahasiswa();
		$getMhs=$mhs->getMahasiswaByNim($nim);
		$kd_prodi="";
		foreach ($getMhs as $dtMhs){
			$kd_prodi=$dtMhs['kd_prodi'];
		}
		// get survey
		$survey=new Survey();
		$getSurveyGen=$survey->getQuiGen0ByPeriodeProdi($kd_periode, $kd_prodi);
		$this->view->listSurveyGen=$getSurveyGen;
		$getAnsweredKuisioner=$survey->getKuisionerGenByNimGroupByQui0($nim);
		$this->view->listKuisionerAns=$getAnsweredKuisioner;
		//--
		$getSurveyKul=$survey->getQuiKul0ByPeriodeProdi($kd_periode, $kd_prodi);
		$this->view->listSurveyKul=$getSurveyKul;
		// krs
		$kuliah = new Kuliah();
		$getKuliah = $kuliah->getKuliahByNimPeriode($nim,$kd_periode);
		$this->view->listKuliah=$getKuliah;
		$arrAns=array();
		$i=1;
		foreach ($getKuliah as $dtKuliah){
			$arrAns[$i]['kd_kuliah']=$dtKuliah['kd_kuliah'];
			$arrAns[$i]['id_qui0']="";
			$arrAns[$i]['n_qui1_ans']=0;
			$getAnsweredKuisionerKul=$survey->getKuisionerKulByKdKulGroupByQui0($dtKuliah['kd_kuliah']);
			foreach ($getAnsweredKuisionerKul as $dtAns){
				$arrAns[$i]['id_qui0']=$dtAns['id_qui0'];
				$arrAns[$i]['n_qui1_ans']=$dtAns['n_qui1_ans'];
			}
			$i++;
		}
		$this->view->listKuisionerAnsKul=$arrAns;
	}
	
	function detil1Action(){
		// Title Browser
		$this->view->title = "Form Kuisioner";
		// navigation
		$this->_helper->navbar('survey',0);
		// get data
		$id_qui0=$this->_request->get('id');
		$survey=new Survey();
		$getQui1=$survey->getQuiGen1ByQuiGen0($id_qui0);
		$this->view->listQui1=$getQui1;
		$this->view->id_qui0=$id_qui0;
	}
	
	function detil2Action(){
		// Title Browser
		$this->view->title = "Form Kuisioner";
		// navigation
		$this->_helper->navbar('survey',0);
		// get data
		$id_qui0=$this->_request->get('id');
		$kd_kuliah=$this->_request->get('kul');
		$kuliah=new Kuliah();
		$getKuliah=$kuliah->getKuliahByKd($kd_kuliah);
		foreach ($getKuliah as $dtKuliah){
			$this->view->nm_dosen=$dtKuliah['nm_dosen'];
			$this->view->nm_mk=$dtKuliah['nm_mk'];
			$this->view->per=$dtKuliah['kd_periode'];
			$this->view->prd=$dtKuliah['nm_prodi'];
			$this->view->kls=$dtKuliah['nm_kelas'];
		}
		$survey=new Survey();
		$getQui1=$survey->getQuiKul1ByQuiKul0($id_qui0);
		$this->view->listQui1=$getQui1;
		$this->view->id_qui0=$id_qui0;
		$this->view->id_kul=$kd_kuliah;
	}
}