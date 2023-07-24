<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2012
	Module		: Pengajuan Controller -> Controller untuk pengajuan buku
*/
class PengajuanbukuController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Paginator');	
		Zend_Loader::loadClass('Pengajuan');
		Zend_Loader::loadClass('Kk');
		Zend_Loader::loadClass('PHPExcel');
		Zend_Loader::loadClass('PHPExcel_Cell_AdvancedValueBinder');
		Zend_Loader::loadClass('PHPExcel_IOFactory');		
		Zend_Loader::loadClass('Validation');
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
		$this->view->lib_act="active";
	}
	
	function indexAction(){
		// title browser
		$this->view->title = "Daftar Pengajuan Buku";
		// get param
		$kd_dosen = $this->kd_dsn;
		$this->view->kd=$kd_dosen;
		// navigation
		$this->_helper->navbar(0,0);
		// set database
		$aju = new Pengajuan();
		$getAju = $aju->getPengajuanByDosen($kd_dosen);
		// throw to view
		$this->view->listAju = $getAju;
		// ref
		$kk=new Kk();
		$this->view->listKk=$kk->getKk();
	}

	function ainsAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$kd = $request['kd'];
		$jdl = $this->_helper->string->esc_quote($request['jdl']);
		$pgr = $this->_helper->string->esc_quote($request['pgr']);
		$kk = $request['kk'];
		$kat = $request['kat'];
		$pnb = $this->_helper->string->esc_quote($request['pnb']);
		$thn=$request['thn'];
		$ed=$this->_helper->string->esc_quote($request['ed']);
		$ex=intval($request['ex']);
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($err==0){
			// set database
			$aju = new Pengajuan();
			$setAju=$aju->setPengajuan('2019-01-01',$kd,$kk,$jdl,'',$pgr,$pnb,$thn,$ed,$kat,$ex);
			echo $setAju;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}

	function adelAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
		$id = $param[0];
		$aju = new Pengajuan();
		$delAju =$aju->delPengajuan($id);
		echo $delAju;
	}

}