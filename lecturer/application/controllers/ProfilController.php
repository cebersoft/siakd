<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Profil Controller -> Controller untuk modul profil
*/
class ProfilController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('Dosen');
		Zend_Loader::loadClass('PendDosen');
		Zend_Loader::loadClass('JenjangPendidikan');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
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
		$this->view->prof_act="active";
		// navigation
		$this->_helper->navbar(0,0);
	}

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Profil Dosen";
		$kd=$this->kd_dsn;
		// get profile dosen
		$dosen = new Dosen();
		$getDosen = $dosen->getDosenByKd($kd);
		if($getDosen){
			foreach ($getDosen as $data) {
				$this->view->kd_dosen=$data['kd_dosen'];
				$this->view->nm_dosen=$data['nm_dosen'];
				$this->view->g_dpn=$data['gelar_depan'];
				$this->view->g_blk=$data['gelar_belakang'];
				$this->view->nidn=$data['nidn'];
				if($data['a_dosen_homebase']=='f'){
					$a_hb="TIDAK";
				}else{
					$a_hb="YA";
				}
				$this->view->a_hb=$a_hb;
				$this->view->kategori_dosen=$data['kategori_dosen'];
				$this->view->tempat_lahir=$data['tmp_lahir'];
				$this->view->tanggal_lahir=$data['tgl_lahir_fmt'];
				if ($data['jenis_kelamin']=='L'){
					$this->view->jk='Laki-laki';
				}else{
					$this->view->jk='Perempuan';				
				}
				$this->view->jk0=$data['jenis_kelamin'];
				$this->view->agm=$data['nm_agama'];
				$this->view->kwn=$data['nm_kwn'];
				if($data['aktif']=='f'){
					$aktif="TIDAK AKTIF";
				}else{
					$aktif="AKTIF";
				}
				$this->view->aktif=$aktif;
				$this->view->alamat=$data['alamat'];
				$this->view->kota_tinggal=$data['kota'];
				$this->view->nik=$data['nik'];
				$this->view->kontak=$data['kontak'];
				$this->view->email_k=$data['email_kampus'];
				$this->view->email_l=$data['email_lain'];
				$this->view->jab=$data['nm_jab'];
				$this->view->pang=$data['nm_pangkat'];
				if($data['a_dosen_wali']=='f'){
					$a_dw="TIDAK";
				}else{
					$a_dw="YA";
				}
				$this->view->a_dw=$a_dw;
				$this->view->uname=$data['kd_dosen'];
				$this->view->pwd=$data['sys_password'];
				$this->view->sys_a=$data['sys_aktif'];
			}
			// pendidikan dosen
			$pendDosen = new PendDosen();
			$this->view->listPendDosen = $pendDosen->getPendByKdDosen($kd);
			// jenjang pendidikan
			$jenjang = new JenjangPendidikan();
			$this->view->listJenjang=$jenjang->fetchAll();
		}else{
			$this->view->eksis ='f';
		}
	}

	function passwordAction()
	{
		// Title Browser
		$this->view->title = "Ganti Password";
		
	}
}