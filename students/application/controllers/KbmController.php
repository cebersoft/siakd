<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: KBM Controller -> Controller untuk modul KBM
*/
class KbmController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Nilai');
		Zend_Loader::loadClass('Register');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Paketkelas');
		Zend_Loader::loadClass('PaketkelasTA');
		Zend_Loader::loadClass('Absensi');
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
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
			$this->nm_pt=$ses_std->nm_pt;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// nav menu
		$this->view->kbm_act="active";
	}

	function indexAction()
	{
		// get param
		$kd_periode=$this->_request->get('id');
		$periode = new Periode();
		if(!$kd_periode){
			// get periode aktif
			$getPeriodeAktif=$periode->getPeriodeByStatus(0);
			if($getPeriodeAktif){
				foreach ($getPeriodeAktif as $dtPeriode) {
					$kd_periode=$dtPeriode['kd_periode'];
				}
			}else{
				$kd_periode="";
			}
		}
		// periode akademik
		$getPeriode = $periode->getPeriodeByKd($kd_periode);
		// navigation
		$this->_helper->navbar(0,0);
		if($getPeriode){
			// Title Browser
			$this->view->title = "Perkuliahan ".$kd_periode;
			$nim=$this->uname;
			// get data krs approved/nilai
			$nilai = new Nilai();
			$getKuliahApp=$nilai->getNilaiByNimPeriode($nim, $kd_periode);
			// get periode register mhs
			$register=new Register();
			$getRegister=$register->getRegisterByNim($nim);
			$arrPer=array();
			if($getRegister){
				$i=0;
				foreach ($getRegister as $dtRegister){
					$arrPer[$i]['kd_periode']=$dtRegister['kd_periode'];
					$i++;
				}
			}
			$this->view->per=$kd_periode;
			$this->view->listPer=$arrPer;
			$this->view->listKuliah=$getKuliahApp;
		}else{
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Perkuliahan";	
		}
	}
	
	function absensiAction()
	{
		// title
		$this->view->title="Kehadiran";
		// get param
		$kd_paket=$this->_request->get('kd');
		$nim=$this->uname;
		$absensi = new Absensi();
		$paketKelas=new Paketkelas();
		$getPaketKelas=$paketKelas->getPaketKelasByKd($kd_paket);
		if(!$getPaketKelas){
			$paketKelasTa=new PaketkelasTA();
			$getPaketKelas=$paketKelasTa->getPaketKelasTAByKd($kd_paket);
			if(!$getPaketKelas){
				$this->view->eksis="f";
				// navigation
				$this->_helper->navbar("kbm/index",0);	
			}else{
				// get paket
				foreach ($getPaketKelas as $dtPaket){
					$this->view->nm_prodi=$dtPaket['nm_prodi_kur'];
					$this->view->kd_per=$dtPaket['kd_periode'];
					$kd_periode=$dtPaket['kd_periode'];
					$this->view->nm_kelas=$dtPaket['nm_kelas'];
					$this->view->jns_kelas=$dtPaket['jns_kelas'];
					$this->view->nm_dsn=$dtPaket['nm_dosen'];
					$this->view->nm_mk=$dtPaket['nm_mk'];
					$this->view->kd_mk=$dtPaket['kode_mk'];
					$this->view->sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
				}
				$getAbsensi=$absensi->getAbsensiByPaketKelas($kd_paket);
				$arrAbsensi=array();
				$i=0;
				foreach ($getAbsensi as $dtAbsensi){
					if($dtAbsensi['nim']==$nim){
						$arrAbsensi[$i]['tgl_kuliah']=$dtAbsensi['tgl_kuliah'];
						$arrAbsensi[$i]['tgl_kuliah_fmt']=$dtAbsensi['tgl_kuliah_fmt'];
						$arrAbsensi[$i]['hari']=$dtAbsensi['hari'];
						$arrAbsensi[$i]['materi']=$dtAbsensi['materi'];
						$arrAbsensi[$i]['start_time']=$dtAbsensi['start_time'];
						$arrAbsensi[$i]['end_time']=$dtAbsensi['end_time'];
						$arrAbsensi[$i]['tempat']=$dtAbsensi['tempat'];
						$arrAbsensi[$i]['id_hadir']=$dtAbsensi['id_hadir'];
						$arrAbsensi[$i]['ket']=$dtAbsensi['ket'];
						$i++;
					}
				}
				$this->view->listAbsensi=$arrAbsensi;	
				// navigation
				$this->_helper->navbar("kbm/index?id=".$kd_periode,0);
			}
		}else{
			// get paket
			foreach ($getPaketKelas as $dtPaket){
				$this->view->nm_prodi=$dtPaket['nm_prodi_kur'];
				$this->view->kd_per=$dtPaket['kd_periode'];
				$kd_periode=$dtPaket['kd_periode'];
				$this->view->nm_kelas=$dtPaket['nm_kelas'];
				$this->view->jns_kelas=$dtPaket['jns_kelas'];
				$this->view->nm_dsn=$dtPaket['nm_dosen'];
				$this->view->nm_mk=$dtPaket['nm_mk'];
				$this->view->kd_mk=$dtPaket['kode_mk'];
				$this->view->sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
			}
			$getAbsensi=$absensi->getAbsensiByPaketKelas($kd_paket);
			$arrAbsensi=array();
			$i=0;
			foreach ($getAbsensi as $dtAbsensi){
				if($dtAbsensi['nim']==$nim){
					$arrAbsensi[$i]['tgl_kuliah']=$dtAbsensi['tgl_kuliah'];
					$arrAbsensi[$i]['tgl_kuliah_fmt']=$dtAbsensi['tgl_kuliah_fmt'];
					$arrAbsensi[$i]['hari']=$dtAbsensi['hari'];
					$arrAbsensi[$i]['materi']=$dtAbsensi['materi'];
					$arrAbsensi[$i]['start_time']=$dtAbsensi['start_time'];
					$arrAbsensi[$i]['end_time']=$dtAbsensi['end_time'];
					$arrAbsensi[$i]['tempat']=$dtAbsensi['tempat'];
					$arrAbsensi[$i]['id_hadir']=$dtAbsensi['id_hadir'];
					$arrAbsensi[$i]['ket']=$dtAbsensi['ket'];
					$i++;
				}
			}
			$this->view->listAbsensi=$arrAbsensi;	
			// navigation
			$this->_helper->navbar("kbm/index?id=".$kd_periode,0);
		}
	}
	
	function materiAction()
	{
		// title
		$this->view->title="Materi Perkuliahan";
		// navigation
			$this->_helper->navbar("kbm/index",0);
	}
}