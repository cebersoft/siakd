<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Quiz Controller -> Controller untuk modul halaman quiz
*/
class QuizController extends Zend_Controller_Action
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
		Zend_Loader::loadClass('Quiz');
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
		$this->view->title = "Daftar Quiz";
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
				// data Quiz
				$quiz=new Quiz();
				$getQuiz=$quiz->getQuiz0ByPaket($kd_paket);
				$this->view->listQuiz0=$getQuiz;
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
					// data Quiz
					$quiz=new Quiz();
					$getQuiz=$quiz->getQuiz0ByKelompok($kd_paket);
					$this->view->listQuiz0=$getQuiz;
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

	function index2Action(){
		// Title Browser
		$this->view->title = "Daftar Soal Quiz";
		// get id quiz
		$id_quiz=$this->_request->get('id');
		$quiz = new Quiz();
		$getQuiz=$quiz->getQuiz0ById($id_quiz);
		if($getQuiz){
			foreach ($getQuiz as $dtQuiz) {
				$this->view->id_quiz0 = $dtQuiz['id_quiz0'];
				$id_quiz0=$dtQuiz['id_quiz0'];
				$this->view->minggu = $dtQuiz['minggu'];
				$this->view->prm = $dtQuiz['param_nilai'];
				$this->view->nm = $dtQuiz['nama_quiz'];
				$this->view->tgl = $dtQuiz['published_fmt'];
				$this->view->time1 = $dtQuiz['start_time'];
				$this->view->time2 = $dtQuiz['end_time'];
				$this->view->nm_dosen = $dtQuiz['nm_dosen'];
				$kd_paket=$dtQuiz['kd_paket_kelas'];
				$id_kel=$dtQuiz['id_kelompok'];
			}
			// navigation
			$this->_helper->navbar('quiz/index?id='.$kd_paket,0);
			// data Soal
			$getSoal=$quiz->getQuiz1ByQuiz0($id_quiz);
			$this->view->listSoal=$getSoal;
		}else{
			$this->view->eksis="f";
			// navigation
			$this->_helper->navbar('lms',0);
		}
	}

	function index3Action(){
		// Title Browser
		$this->view->title = "Daftar Pilihan Jawaban";
		// get id quiz
		$id_quiz=$this->_request->get('id');
		$quiz = new Quiz();
		$getQuiz=$quiz->getQuiz1ById($id_quiz);
		if($getQuiz){
			foreach ($getQuiz as $dtQuiz) {
				$id_quiz0=$dtQuiz['id_quiz0'];
				$this->view->id_quiz1 = $dtQuiz['id_quiz1'];
				$this->view->question = $dtQuiz['question'];
				$this->view->jawaban = $dtQuiz['id_jawaban'];
				$this->view->ord = $dtQuiz['urutan'];
				$id_kel=$dtQuiz['id_kelompok'];
			}
			// navigation
			$this->_helper->navbar('quiz/index2?id='.$id_quiz0,0);
			// data Jawaban
			$getPilihan=$quiz->getQuiz2ByQuiz1($id_quiz);
			$this->view->listPilihan=$getPilihan;
		}else{
			$this->view->eksis="f";
			// navigation
			$this->_helper->navbar('lms',0);
		}
	}

}