<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Helper Controller -> Controller untuk modul halaman helper
*/
class HelperController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		// load class
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Mahasiswa');
		// layout
		$this->_helper->layout()->setLayout('source');
	}
	
	public function convertCaller($caller) {
		$i=1;
		$readyCaller='';
		foreach ($caller as $dataCaller) {
			if($i==1){
				$readyCaller='{'.$dataCaller;
			}elseif ($i==count($caller)) {
				$readyCaller=$readyCaller.','.$dataCaller.'}';
			}else{
				$readyCaller=$readyCaller.','.$dataCaller;
			}
			$i++;
		}
		return $readyCaller;
	}

	function indexAction(){
		// title
		$this->view->title="Halaman Data";
	}

	function mahasiswaAction(){
		// get param
		$request = $this->getRequest()->getPost();
		// if use status?
		$stat = $this->_request->get('st');
	    //gets value from ajax request
	    $caller=$request['caller'];
	    $param=$request['param'];
	    $angkatan=$param[0];
	    $prodi=$param[1];
	    if(!$stat){
	    	// get data
			$mhs = new Mahasiswa();
			$this->view->listMhs=$mhs->getMahasiswaByAngkatanProdi($angkatan,$prodi);	
	    }else{
	    	$mhs = new Mahasiswa();
			$this->view->listMhs=$mhs->getMahasiswaByAngkatanProdiStatus($angkatan,$prodi,$stat);	
	    }
		// title
		$this->view->title="Daftar Mahasiswa";
		// convert variabel caller
		$readyCaller=$this->convertCaller($caller);
		// throw variabel caller to view
		$this->view->readyCaller=$readyCaller;
	}
}