<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Diskusi Controller -> Controller untuk modul halaman diskusi
*/
class DiskusiController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Kelas');
		Zend_Loader::loadClass('Paketkelas');
		//Zend_Loader::loadClass('KelompokPraktikum');
		Zend_Loader::loadClass('Nmkelas');
		Zend_Loader::loadClass('Diskusi');
		Zend_Loader::loadClass('DiskusiMhs');
		Zend_Loader::loadClass('Rps');
		Zend_Loader::loadClass('TimTeaching');
		Zend_Loader::loadClass('KalenderAkd');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
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
		$this->view->lms_act="active";
	}

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Daftar Diskusi";
		// get kd paket kelas
		$kd_paket=$this->_request->get('id');
		$paketkelas = new Paketkelas();
		$getPaketKelas=$paketkelas->getPaketKelasByKd($kd_paket);
		if($getPaketKelas){
			foreach ($getPaketKelas as $dtPaket) {
				$this->view->kd_paket_kelas = $dtPaket['kd_paket_kelas'];
				$this->view->id_kel = "";
				$this->view->url = $dtPaket['kd_paket_kelas'];
				$kdKelas = $dtPaket['kd_kelas'];
				$this->view->kd_kelas = $dtPaket['kd_kelas'];
				$this->view->kd_paket_kelas = $dtPaket['kd_paket_kelas'];
				$this->view->nm_prodi=$dtPaket['nm_prodi_kur'];
				$this->view->kd_per=$dtPaket['kd_periode'];
				$this->view->nm=$dtPaket['nm_kelas'];
				$this->view->jns_kelas=$dtPaket['jns_kelas'];
				$this->view->nm_dsn=$dtPaket['nm_dosen'];
				$this->view->kd_dsn=$dtPaket['kd_dosen'];
				$kd_dsn=$dtPaket['kd_dosen'];
				$this->view->nm_mk=$dtPaket['nm_mk'];
				$this->view->kd_mk=$dtPaket['kode_mk'];
				$this->view->sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
				$id_mk_kur=$dtPaket['id_mk_kurikulum'];
			}
			$timTeaching=new TimTeaching();
			$getKelasTt=$timTeaching->getTimTeachingByKelas($kdKelas);
			$found=0;
			if ($getKelasTt){
				foreach($getKelasTt as $tt){
					if($tt['kd_dosen']==$this->kd_dsn){
						$found=$found+1;
					}
				}
			}
			if(($kd_dsn==$this->kd_dsn)or($found>0)){
				// ref
				$rps=new Rps();
				$getRps=$rps->getRpsByMkKur($id_mk_kur);
				$arrRps=array();
				if($getRps){
					$id_rps="";
					foreach($getRps as $dtRps){
						$id_rps=$dtRps['id_rps'];
					}
					$getDtlRps=$rps->getRpsDetilByRps($id_rps);
					$arrRps=$getDtlRps;
				}
				$this->view->listRps=$arrRps;
				// data Diskusi
				$diskusi=new Diskusi();
				$getDiskusi=$diskusi->getDiskusiByPaket($kd_paket);
				$this->view->listDiskusi=$getDiskusi;
				// navigation
				$this->_helper->navbar('lms/detil?id='.$kdKelas,0);	
			}else{
				$this->view->eksis="f";
				// navigation
				$this->_helper->navbar('lms',0);
			}
		}else{
			// get kelompok
			$kelompok = new KelompokPraktikum();
			$getKelompok=$kelompok->getKelompokPraktikumByKd($kd_paket);
			if($getKelompok){
				foreach ($getKelompok as $dtPaket) {
					$this->view->kd_paket_kelas = "";
					$this->view->id_kel = $dtPaket['id_kelompok'];
					$this->view->url = $dtPaket['id_kelompok'];
					$kdKelas = $dtPaket['kd_kelas'];
					$this->view->kd_kelas = $dtPaket['kd_kelas'];
					$this->view->kd_paket_kelas = $dtPaket['kd_paket_kelas'];
					$this->view->nm_prodi=$dtPaket['nm_prodi_kur'];
					$this->view->kd_per=$dtPaket['kd_periode'];
					$this->view->nm=$dtPaket['nm_kelompok'];
					$this->view->jns_kelas=$dtPaket['jns_kelas'];
					$this->view->nm_dsn=$dtPaket['nm_dosen'];
					$this->view->kd_dsn=$dtPaket['kd_dosen'];
					$kd_dsn=$dtPaket['kd_dosen'];
					$this->view->nm_mk=$dtPaket['nm_mk'];
					$this->view->kd_mk=$dtPaket['kode_mk'];
					$this->view->sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
					$id_mk_kur=$dtPaket['id_mk_kurikulum'];
				}
				$timTeaching=new TimTeaching();
				$getKelasTt=$timTeaching->getTimTeachingByKelas($kdKelas);
				$found=0;
				if ($getKelasTt){
					foreach($getKelasTt as $tt){
						if($tt['kd_dosen']==$this->kd_dsn){
							$found=$found+1;
						}
					}
				}
				if(($kd_dsn==$this->kd_dsn)or($found>0)){
					// ref
					$rps=new Rps();
					$getRps=$rps->getRpsByMkKur($id_mk_kur);
					$arrRps=array();
					if($getRps){
						$id_rps="";
						foreach($getRps as $dtRps){
							$id_rps=$dtRps['id_rps'];
						}
						$getDtlRps=$rps->getRpsDetilByRps($id_rps);
						$arrRps=$getDtlRps;
					}
					$this->view->listRps=$arrRps;
					// data Diskusi
					$diskusi=new Diskusi();
					$getDiskusi=$diskusi->getDiskusiByKelompok($kd_paket);
					$this->view->listDiskusi=$getDiskusi;
					// navigation
					$this->_helper->navbar('lms/detil?id='.$kdKelas,0);
				}else{
					$this->view->eksis="f";
					// navigation
					$this->_helper->navbar('lms',0);
				}
			}else{
				$this->view->eksis="f";
				// navigation
				$this->_helper->navbar('lms',0);
			}
		}
	}

	function detilAction(){
		// Title Browser
		$this->view->title = "Daftar Respon Diskusi oleh Mahasiswa";
		// get id tugas
		$id_disk=$this->_request->get('id');
		$diskusi = new Diskusi();
		$getDisk=$diskusi->getDiskusiById($id_disk);
		if($getDisk){
			foreach ($getDisk as $dtDisk) {
				$this->view->id_diskusi = $dtDisk['id_diskusi'];
				$this->view->minggu = $dtDisk['minggu'];
				$this->view->prm = $dtDisk['param_nilai'];
				$this->view->jdl = $dtDisk['judul_diskusi'];
				$this->view->knt = $dtDisk['konten_diskusi'];
				$this->view->tgl1 = $dtDisk['published_fmt'];
				$this->view->tgl2 = $dtDisk['expired_fmt'];
				$this->view->nm_dosen = $dtDisk['nm_dosen'];
				$kd_paket=$dtDisk['kd_paket_kelas'];
				$id_kel=$dtDisk['id_kelompok'];
			}
			// data diskusi
			$diskMhs=new DiskusiMhs();
			$getDiskusiMhs=$diskMhs->getDiskusiMhsByDiskusi($id_disk);
			$this->view->listDiskusiMhs=$getDiskusiMhs;
			$paket=new Paketkelas();
			$getPaketKelas=$paket->getPaketKelasByKd($kd_paket);
			$kd_periode="";
			if($getPaketKelas){
				foreach($getPaketKelas as $dtPaket){
					$kd_periode=$dtPaket['kd_periode'];
				}
				// navigation
				$this->_helper->navbar('diskusi/index?id='.$kd_paket,0);
			}else{
				$kelompok=new KelompokPraktikum();
				$getKelompok=$kelompok->getKelompokPraktikumByKd($id_kel);
				foreach($getKelompok as $dtKel){
					$kd_periode=$dtKel['kd_periode'];
				}
				// navigation
				$this->_helper->navbar('diskusi/index?id='.$id_kel,0);
			}
			// cek allowance input
			$tgl = date('Y-m-d');
			// versi 2 (tanpa ceking tanggal aktual
			// cek kalender
			$kd_aktivitas='201';
			// jadwal entri nilai
			$kalender=new KalenderAkd();
			$getKalender=$kalender->getKalenderAkdByPeriodeAktivitas($kd_periode, $kd_aktivitas);
			$this->view->allowInput="";
			if ($getKalender){
				// cek tanggal
				foreach ($getKalender as $dtKalender) {
					$startDate=$dtKalender['start_date'];
					$endDate=$dtKalender['end_date'];
				}
				if(($tgl>=$startDate)and($tgl<=$endDate)){
					$this->view->allowInput=1;
				}else{
					$this->view->allowInput=-1;
				}
			}else{
				$this->view->allowInput=0;
			}
		}else{
			$this->view->eksis="f";
			// navigation
			$this->_helper->navbar('lms',0);
		}
	}
}