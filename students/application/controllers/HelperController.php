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
		Zend_Loader::loadClass('Jadwal');
		Zend_Loader::loadClass('Kuliah');
		Zend_Loader::loadClass('Ruangan');
		Zend_Loader::loadClass('Slot');
		Zend_Loader::loadClass('Hari');
		Zend_Loader::loadClass('Periode');
		// layout
		$this->_helper->layout()->setLayout('source');
		// global var
		$this->uname=Zend_Auth::getInstance()->getIdentity()->nim;
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
	
	function jadwalAction(){		
		//gets value from ajax request
		$request = $this->getRequest()->getPost();
	    $caller=$request['caller'];
	    $param =$request['param'];
	    $key = $param[0];
	    $value = $param[1];
	    $kd_periode = $param[2];
		//------------------------------
		$slot = new Slot();
		$listSlot = $slot->fetchAll();
		$this->view->listSlot=$listSlot;
		//------------------------------
		$ruangan = new Ruangan();
		$listRuangan=$ruangan->fetchAll();
		$this->view->listRuangan=$listRuangan;
		//------------------------------
		$hari = new Hari();
		$listHari=$hari->fetchAll();
		$this->view->listHari=$listHari;
		// tab hari
		$tab=$this->_request->get('tab');
		if($tab){
			$this->view->tab=$tab;
		}else{
			$this->view->tab='1';
		}
	    // get data
	    $jadwal = new Jadwal();
	    $getJadwal = $jadwal->getJadwalByPeriode($kd_periode);
	    if($key=='pkt'){
	    	// by paket
	    	$kd_paket=$value;
	    	$i=0;
	    	$arrJadwal=array();
	    	foreach ($getJadwal as $dtJadwal){
	    		if($dtJadwal['kd_paket_kelas']==$kd_paket){
	    			$arrJadwal[$i]['id_hari']=$dtJadwal['id_hari'];
					$arrJadwal[$i]['nm_hari']=$dtJadwal['nm_hari'];
					$arrJadwal[$i]['id_slot']=$dtJadwal['id_slot'];
					$arrJadwal[$i]['start_time']=$dtJadwal['start_time'];
					$arrJadwal[$i]['end_time']=$dtJadwal['end_time'];
					$arrJadwal[$i]['kd_ruangan']=$dtJadwal['kd_ruangan'];
					$arrJadwal[$i]['nm_ruangan']=$dtJadwal['nm_ruangan'];
					$arrJadwal[$i]['kd_paket_kelas']=$dtJadwal['kd_paket_kelas'];
					$arrJadwal[$i]['id_nm_kelas']=$dtJadwal['id_nm_kelas'];
					$arrJadwal[$i]['nm_kelas']=$dtJadwal['nm_kelas'];
					$arrJadwal[$i]['kd_dosen']=$dtJadwal['kd_dosen'];
					$arrJadwal[$i]['nm_dosen']=$dtJadwal['nm_dosen'];
					$arrJadwal[$i]['kode_mk']=$dtJadwal['kode_mk'];
					$arrJadwal[$i]['nm_mk']=$dtJadwal['nm_mk'];
					$arrJadwal[$i]['nm_prodi_kur']=$dtJadwal['nm_prodi_kur'];
					$i++;
	    		}
	    	}
	    	$this->view->listJadwal=$arrJadwal;
	    }else{
	    	// by nim
	    	$nim = $this->uname;
	    	// get data krs
			$kuliah = new Kuliah();
			$getKuliah=$kuliah->getKuliahByNimPeriode($nim, $kd_periode);
			$arrJadwal=array();
			$i=0;
			foreach ($getJadwal as $dtJadwal){
				foreach ($getKuliah as $dtKuliah) {
					if($dtJadwal['kd_paket_kelas']==$dtKuliah['kd_paket_kelas']){
						$arrJadwal[$i]['id_hari']=$dtJadwal['id_hari'];
						$arrJadwal[$i]['nm_hari']=$dtJadwal['nm_hari'];
						$arrJadwal[$i]['id_slot']=$dtJadwal['id_slot'];
						$arrJadwal[$i]['start_time']=$dtJadwal['start_time'];
						$arrJadwal[$i]['end_time']=$dtJadwal['end_time'];
						$arrJadwal[$i]['kd_ruangan']=$dtJadwal['kd_ruangan'];
						$arrJadwal[$i]['nm_ruangan']=$dtJadwal['nm_ruangan'];
						$arrJadwal[$i]['kd_paket_kelas']=$dtJadwal['kd_paket_kelas'];
						$arrJadwal[$i]['id_nm_kelas']=$dtJadwal['id_nm_kelas'];
						$arrJadwal[$i]['nm_kelas']=$dtJadwal['nm_kelas'];
						$arrJadwal[$i]['kd_dosen']=$dtJadwal['kd_dosen'];
						$arrJadwal[$i]['nm_dosen']=$dtJadwal['nm_dosen'];
						$arrJadwal[$i]['kode_mk']=$dtJadwal['kode_mk'];
						$arrJadwal[$i]['nm_mk']=$dtJadwal['nm_mk'];
						$arrJadwal[$i]['nm_prodi_kur']=$dtJadwal['nm_prodi_kur'];
						$i++;
					}
				}
			}
			$this->view->listJadwal=$arrJadwal;
	    }
	    // title
		$this->view->title="Jadwal Kelas";
	}
}