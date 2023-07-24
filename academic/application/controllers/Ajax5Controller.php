<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Ajax Controller -> Controller untuk submit via ajax
*/
class Ajax5Controller extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Konversi');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Validation');
		$auth = Zend_Auth::getInstance();
		$ses_ac = new Zend_Session_Namespace('ses_ac');
		if (($auth->hasIdentity())and($ses_ac->uname)) {

		}else{
			echo "F|Sesi anda sudah habis. Silakan login ulang!|";
		}
		// disabel layout
		$this->_helper->layout->disableLayout();
	}
		
	function showmhschtAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$akt = $request['akt'];
		$prd = $request['prd'];
		$par = $request['par'];
		$cht = $request['cht'];
		// set session
		$param = new Zend_Session_Namespace('param_mhs_chart');
		$param->akt=$akt;
		$param->prd=$prd;
		$param->par=$par;
		$param->cht=$cht;
	}
	
	function showdsnchtAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$stat = $request['stat'];
		$par = $request['par'];
		$cht = $request['cht'];
		// set session
		$param = new Zend_Session_Namespace('param_dsn_chart');
		$param->stat=$stat;
		$param->par=$par;
		$param->cht=$cht;
	}
	
	function showmhsregchtAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$akt = $request['akt'];
		$prd = $request['prd'];
		$per = $request['per'];
		$par = $request['par'];
		$cht = $request['cht'];
		// set session
		$param = new Zend_Session_Namespace('param_reg_chart');
		$param->akt=$akt;
		$param->prd=$prd;
		$param->per=$per;
		$param->par=$par;
		$param->cht=$cht;
	}
	
	function showjdwchtAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$per = $request['per'];
		$cht = $request['cht'];
		// set session
		$param = new Zend_Session_Namespace('param_jdw_chart');
		$param->per=$per;
		$param->cht=$cht;
	}
	
	function showpklschtAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$prd = $request['prd'];
		$per = $request['per'];
		$cht = $request['cht'];
		// set session
		$param = new Zend_Session_Namespace('param_pkls_chart');
		$param->prd=$prd;
		$param->per=$per;
		$param->cht=$cht;
	}
	
	function showkbmrepAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$prd = $request['prd'];
		$per = $request['per'];
		// set session
		$param = new Zend_Session_Namespace('param_kbm_rep');
		$param->prd=$prd;
		$param->per=$per;
	}
	
	function showkrschtAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$akt = $request['akt'];
		$prd = $request['prd'];
		$per = $request['per'];
		$par = $request['par'];
		$cht = $request['cht'];
		// set session
		$param = new Zend_Session_Namespace('param_krs_chart');
		$param->akt=$akt;
		$param->prd=$prd;
		$param->per=$per;
		$param->par=$par;
		$param->cht=$cht;
	}
	
	function showabschtAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$akt = $request['akt'];
		$prd = $request['prd'];
		$per = $request['per'];
		$par = $request['par'];
		// set session
		$param = new Zend_Session_Namespace('param_abs_chart');
		$param->akt=$akt;
		$param->prd=$prd;
		$param->per=$per;
		$param->par=$par;
	}
	
	function showhschtAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$akt = $request['akt'];
		$prd = $request['prd'];
		$per = $request['per'];
		$cht = $request['cht'];
		// set session
		$param = new Zend_Session_Namespace('param_hs_chart');
		$param->akt=$akt;
		$param->prd=$prd;
		$param->per=$per;
		$param->cht=$cht;
	}

	function showipkrepAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$akt = $request['akt'];
		$prd = $request['prd'];
		$nim1 = $request['nim1'];
		$nim2 = $request['nim2'];
		// set session
		$param = new Zend_Session_Namespace('param_ipk_rep');
		$param->akt=$akt;
		$param->prd=$prd;
		$param->nim1=$nim1;
		$param->nim2=$nim2;
	}

	function showmatrixrepAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$akt = $request['akt'];
		$prd = $request['prd'];
		$per = $request['per'];
		// set session
		$param = new Zend_Session_Namespace('param_mtx_erep');
		$param->akt=$akt;
		$param->prd=$prd;
		$param->per=$per;
	}

	function showrekapnilaiAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$prd = $request['prd'];
		$per = $request['per'];
		// set session
		$param = new Zend_Session_Namespace('param_rekap_erep');
		$param->prd=$prd;
		$param->per=$per;
	}

	function showkrstarepAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$prd = $request['prd'];
		$per = $request['per'];
		// set session
		$param = new Zend_Session_Namespace('param_krsta_rep');
		$param->prd=$prd;
		$param->per=$per;
	}

	function showkbmrekapdosenAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$prd = $request['prd'];
		$tgl1 = $request['tgl1'];
		$tgl2 = $request['tgl2'];
		$tp = $request['tp'];
		// set session
		$param = new Zend_Session_Namespace('param_kbmrekapdosen_erep');
		$param->prd=$prd;
		$param->tgl1=$tgl1;
		$param->tgl2=$tgl2;
		$param->tp=$tp;
	}

	function showrekapkonvAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$akt = $request['akt'];
		$prd = $request['prd'];
		// set session
		$param = new Zend_Session_Namespace('param_rekap_konv');
		$param->akt=$akt;
		$param->prd=$prd;
	}

	function showsumujianAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$prd = $request['prd'];
		$per = $request['per'];
		// set session
		$param = new Zend_Session_Namespace('param_sumujian_erep');
		$param->prd=$prd;
		$param->per=$per;
	}

	function showrekapkrsAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$akt = $request['akt'];
		$prd = $request['prd'];
		$per = $request['per'];
		// set session
		$param = new Zend_Session_Namespace('param_rekap_krs');
		$param->akt=$akt;
		$param->prd=$prd;
		$param->per=$per;
	}

	function showsumabsensiAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$prd = $request['prd'];
		$per = $request['per'];
		// set session
		$param = new Zend_Session_Namespace('param_sumabsensi_erep');
		$param->prd=$prd;
		$param->per=$per;
	}


}