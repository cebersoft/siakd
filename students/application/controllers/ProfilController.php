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
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Konversi');
		Zend_Loader::loadClass('Wilayah');
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
		$this->view->title = "Profil Mahasiswa";
		// get profile mahasiswa
		$mhs = new Mahasiswa();
		$wilayah=new Wilayah();
		$nim=$this->uname;
		$getMhs = $mhs->getMahasiswaByNim($nim);
		if (!$getMhs){
			$this->view->eksis='f';
		}else{
			foreach($getMhs as $data){
				$this->view->nim_mhs=$data['nim'];
				$this->view->id_mhs=$data['id_mhs'];
				$this->view->nama_mhs=$data['nm_mhs'];
				$this->view->dosen_wali=$data['nm_dosen_wali'];
				$this->view->alamat=$data['alamat'];
				$this->view->kontak=$data['large_kontak'];
				$this->view->email_k=$data['email_kampus'];
				$this->view->email_l=$data['email_lain'];
				$this->view->kota_tinggal=$data['kota'];
				$this->view->asal_sekolah=$data['nm_pt_asal'];
				$this->view->asal_prodi=$data['nm_prodi_asal'];
				$this->view->tempat_lahir=$data['tmp_lahir'];
				$this->view->tanggal_lahir=$data['tgl_lahir_fmt'];
				$this->view->nama_ayah=$data['nm_ayah'];
				$this->view->nama_ibu=$data['nm_ibu'];
				$this->view->job_ayah=$data['nm_job_ayah'];
				$this->view->job_ibu=$data['nm_job_ibu'];
				$this->view->username=$data['nim'];
				$this->view->status_mhs=$data['status_mhs'];
				if ($data['id_jns_keluar']==null){
					$this->view->status_keluar="BELUM LULUS";
				}else{
					$this->view->status_keluar=$data['ket_keluar'];
				}
				$this->view->tgl_keluar=$data['tgl_keluar_fmt'];
				$this->view->no_ijazah=$data['no_ijazah'];
				$this->view->sk_yudisium=$data['sk_yudisium'];
				$this->view->tgl_sk_yudisium=$data['tgl_sk_yudisium_fmt'];
				$this->view->ipk=$data['ipk'];
				$this->view->judul_ta=$data['judul_skripsi'];
				$this->view->agama=$data['nm_agama'];
				$this->view->kwn=$data['nm_kwn'];
				if ($data['jenis_kelamin']=='L'){
					$this->view->jk='LAKI-LAKI';
				}else{
					$this->view->jk='PEREMPUAN';				
				}
				$this->view->jk0=$data['jenis_kelamin'];
				$this->view->tgl_masuk= $data['tgl_masuk_fmt'];
				$this->view->status_masuk= $data['nm_stat_masuk'];
				$this->view->jns_masuk= $data['jns_masuk'];
				$this->view->ket_masuk= $data['ket_masuk'];
				$nm_wil="";
				$id_wil=$data['id_wil'];
				$getWilayah = $wilayah->getWilayahById($id_wil);
				foreach ($getWilayah as $dtWil){
					$nm_wil=$dtWil['nm_wil'];
					$kota=$dtWil['kota'];
					$prop=$dtWil['propinsi'];
					$negara=$dtWil['negara'];
				}
				$this->view->nm_wil = $nm_wil;
				$this->view->kota_wil = $kota;
				$this->view->prop_wil = $prop;
				$this->view->neg_wil = $negara;
				// data mhs pt
				$this->view->nm_prodi = $data['nm_prodi'];;
				$this->view->akt = $data['id_angkatan'];;
				// nilai konversi
				$konversi = new Konversi();
				$this->view->listKonversi = $konversi->getKonversiByNim($nim);
			}
		}
	}

	function passwordAction()
	{
		// Title Browser
		$this->view->title = "Ganti Password";
		
	}
}