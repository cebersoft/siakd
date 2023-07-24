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
		Zend_Loader::loadClass('Matkul');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Dosen');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Kurikulum');
		Zend_Loader::loadClass('MatkulKurikulum');
		Zend_Loader::loadClass('Paketkelas');
		Zend_Loader::loadClass('Ajar');
		Zend_Loader::loadClass('AjarTA');
		Zend_Loader::loadClass('Ruangan');
		Zend_Loader::loadClass('Slot');
		Zend_Loader::loadClass('SlotUjian');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Wilayah');
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

	function matkulAction(){
		// title
		$this->view->title="Daftar Mata Kuliah";
		// get data
		$matkul = new Matkul();
		$this->view->listMk=$matkul->fetchAll();
		$request = $this->getRequest()->getPost();
	    //gets value from ajax request
	    $caller=$request['caller'];
		// convert variabel caller
		$readyCaller=$this->convertCaller($caller);
		// throw variabel caller to view
		$this->view->readyCaller=$readyCaller;
	}

	function dosenAction(){
		// get param
		$request = $this->getRequest()->getPost();
	    //gets value from ajax request
	    $caller=$request['caller'];
	    $param=$request['param'];
	    $stat=$param[0];
		// get data
		$dosen = new Dosen();
		$this->view->listDsn=$dosen->getDosenByStatus($stat);
		// title
		$this->view->title="Daftar Dosen";
		// convert variabel caller
		$readyCaller=$this->convertCaller($caller);
		// throw variabel caller to view
		$this->view->readyCaller=$readyCaller;
	}

	function kurikulumAction(){
		// get param
		$request = $this->getRequest()->getPost();
	    //gets value from ajax request
	    $caller=$request['caller'];
	    $param=$request['param'];
	    $kd_prodi=$param[0];
		// get data
		$kurikulum = new Kurikulum();
		$this->view->listKur=$kurikulum->getKurByProdi($kd_prodi);
		$prodi = new Prodi();
		$getProdi = $prodi->getProdiByKd($kd_prodi);
		if($getProdi){
			foreach ($getProdi as $dtProdi) {
				$nmProdi = $dtProdi['nm_prodi'];
			}
			// title
			$this->view->title="Daftar Kurikulum Prodi : ".$nmProdi;
		}else{
			// title
			$this->view->title="Daftar Kurikulum";
		}
		// convert variabel caller
		$readyCaller=$this->convertCaller($caller);
		// throw variabel caller to view
		$this->view->readyCaller=$readyCaller;
	}

	function matkulkurikulumAction(){
		//gets value from ajax request
		$request = $this->getRequest()->getPost();
		// if TA?
		$ta = $this->_request->get('ta');
	    $caller=$request['caller'];
	    $param =$request['param'];
	    $id_kurikulum = $param[0];
		// get data
		$mkKur = new MatkulKurikulum();
		if($ta=='1'){
			$this->view->listMkKur=$mkKur->getMatkulTAByKurikulum($id_kurikulum);	
		}else{
			$this->view->listMkKur=$mkKur->getMatkulByKurikulum($id_kurikulum);
		}
		$kurikulum = new Kurikulum();
		$getKurikulum = $kurikulum->getKurById($id_kurikulum);
		if($getKurikulum){
			foreach ($getKurikulum as $dataKur) {
				$nmKur=$dataKur['nm_kurikulum'];
			}
			// title
			$this->view->title="Daftar Mata Kuliah Kurikulum ".$nmKur;
		}else{
			$this->view->title="Daftar Mata Kuliah";
		}
		// convert variabel caller
		$readyCaller=$this->convertCaller($caller);
		// throw variabel caller to view
		$this->view->readyCaller=$readyCaller;
	}

	function ajarAction(){
		//gets value from ajax request
		$request = $this->getRequest()->getPost();
		// if TA?
		$ta = $this->_request->get('ta');
	    $caller=$request['caller'];
	    $param =$request['param'];
	    $id_kurikulum = $param[0];
	    // get data
	    if($ta=='1'){
	    	$ajarTA = new AjarTA();
	    	$getAjar = $ajarTA->getAjarTAByKurikulum($id_kurikulum);
	    }else{
	    	$ajar = new Ajar();
	    	$getAjar = $ajar->getAjarByKurikulum($id_kurikulum);
	    }
	    $this->view->listAjar=$getAjar;
	    $kurikulum = new Kurikulum();
		$getKurikulum = $kurikulum->getKurById($id_kurikulum);
	    if($getKurikulum){
			foreach ($getKurikulum as $dataKur) {
				$nmKur=$dataKur['nm_kurikulum'];
			}
			// title
			$this->view->title="Daftar Mata Kuliah dan Dosen Kurikulum ".$nmKur;
		}else{
			$this->view->title="Daftar Mata Kuliah dan Dosen";
		}
		// convert variabel caller
		$readyCaller=$this->convertCaller($caller);
		// throw variabel caller to view
		$this->view->readyCaller=$readyCaller;
	}

	function paketkelasAction(){
		//gets value from ajax request
		$request = $this->getRequest()->getPost();
	    $caller=$request['caller'];
	    $param =$request['param'];
	    $kd_periode = $param[0];
	    $kd_prodi = $param[1];
	    // get data
	    $paketkelas = new Paketkelas();
	    $getPaketKelas = $paketkelas->getPaketKelasByPeriodeProdi($kd_periode,$kd_prodi);
	    $this->view->listPaketkelas=$getPaketKelas;
	    $prodi = new Prodi();
		$getProdi = $prodi->getProdiByKd($kd_prodi);
	    if($getProdi){
			foreach ($getProdi as $dataProdi) {
				$nmPrd=$dataProdi['nm_prodi'];
			}
			// title
			$this->view->title="Daftar Paket Kelas Prodi ".$nmPrd." Periode ".$kd_periode;
		}else{
			$this->view->title="Daftar Paket Kelas";
		}
		// convert variabel caller
		$readyCaller=$this->convertCaller($caller);
		// throw variabel caller to view
		$this->view->readyCaller=$readyCaller;
	}

	function ruanganAction(){
		// title
		$this->view->title="Daftar Ruangan";
		// get data
		$ruangan = new Ruangan();
		$this->view->listRuangan=$ruangan->fetchAll();
		$request = $this->getRequest()->getPost();
	    //gets value from ajax request
	    $caller=$request['caller'];
		// convert variabel caller
		$readyCaller=$this->convertCaller($caller);
		// throw variabel caller to view
		$this->view->readyCaller=$readyCaller;
	}

	function slotAction(){
		// title
		$this->view->title="Daftar Slot";
		// get data
		$slot = new Slot();
		$this->view->listSlot=$slot->fetchAll();
		$request = $this->getRequest()->getPost();
	    //gets value from ajax request
	    $caller=$request['caller'];
		// convert variabel caller
		$readyCaller=$this->convertCaller($caller);
		// throw variabel caller to view
		$this->view->readyCaller=$readyCaller;
	}

	function slotujianAction(){
		// title
		$this->view->title="Daftar Slot Ujian";
		// get data
		$slot = new SlotUjian();
		$this->view->listSlot=$slot->fetchAll();
		$request = $this->getRequest()->getPost();
		//gets value from ajax request
		$caller=$request['caller'];
		// convert variabel caller
		$readyCaller=$this->convertCaller($caller);
		// throw variabel caller to view
		$this->view->readyCaller=$readyCaller;
	}
	
	function wilayahAction(){
		// title
		$this->view->title="Daftar Wilayah";
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
	    $caller=$request['caller'];
	    $param =$request['param'];
	    $id_level = intval($param[0]);
	    // get data
		$wil = new Wilayah();
		$this->view->listWilayah=$wil->getWilayahByLevel($id_level);
		// convert variabel caller
		$readyCaller=$this->convertCaller($caller);
		// throw variabel caller to view
		$this->view->readyCaller=$readyCaller;
	}
	
	function menuAction(){
		// title
		$this->view->title="Daftar Menu Akademik";
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
	    $caller=$request['caller'];
		$filter = $this->_request->get('f');
		// get data
		if(!$filter){
			// all
			$menu = new Menu();
			$this->view->listMenu=$menu->getMenuDtlAcc();	
		}else{
			$param =$request['param'];
			$username = $param[0];
			$true=$param[1];
			if($true=='t'){
				$user = new User();
				$this->view->listMenu=$user->getMenuAcByUname($username);
			}else{
				$user = new User();
				$this->view->listMenu=$user->getMenuAcByUnameNot($username);
			}
		}
		// convert variabel caller
		$readyCaller=$this->convertCaller($caller);
		// throw variabel caller to view
		$this->view->readyCaller=$readyCaller;
	}
}
?>