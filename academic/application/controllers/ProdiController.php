<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Prodi Controller -> Controller untuk modul halaman prodi
*/
class ProdiController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('ProdiInfo0');
		Zend_Loader::loadClass('ProdiSkpiLabel');
		Zend_Loader::loadClass('ProdiCapaianKkni');
		Zend_Loader::loadClass('ProdiCapaianAsosiasi');
		Zend_Loader::loadClass('ProdiCapaianPtMajor');
		Zend_Loader::loadClass('ProdiCapaianPtMinor');
		Zend_Loader::loadClass('ProdiCapaianPtOther');
		Zend_Loader::loadClass('Kaprodi');
		Zend_Loader::loadClass('Dosen');
		Zend_Loader::loadClass('JenjangProdi');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Validation');
		$auth = Zend_Auth::getInstance();
		$ses_ac = new Zend_Session_Namespace('ses_ac');
		$ses_menu = new Zend_Session_Namespace('ses_menu');
		if (($auth->hasIdentity())and($ses_ac->uname)) {
			$this->view->namauser =Zend_Auth::getInstance()->getIdentity()->nama;
			$this->view->username=Zend_Auth::getInstance()->getIdentity()->username;
			$this->view->kd_pt=$ses_ac->kd_pt;
			$this->view->nm_pt=$ses_ac->nm_pt;
			$this->view->menu=$ses_menu->menu;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// treeview
		$this->view->active_tree="00";
		$this->view->active_menu="prodi/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "prodi/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Program Studi";
			// navigation
			$this->_helper->navbar(0,0,'prodi/new',0,0);
			// prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
		}
	}

	function newAction(){
		$user = new Menu();
		$menu = "prodi/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Input Program Studi Baru";
			// navigation
			$this->_helper->navbar(0,'prodi',0,0,0);
			// get jenjang prodi
			$jenjang=new JenjangProdi();
			$this->view->listJenjang = $jenjang->fetchAll();
		}
	}

	function editAction(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$kd=$this->_request->get('kd');
			// get data Prodi
			$prodi = new Prodi();
			$getProdi=$prodi->getProdiByKd($kd);
			if($getProdi){
				foreach ($getProdi as $dtProdi) {
					$this->view->kd = $dtProdi['kd_prodi'];
					$this->view->nm = $dtProdi['nm_prodi'];
					$nm_prodi=$dtProdi['nm_prodi'];
					$this->view->jenjang = $dtProdi['id_jenjang_prodi'];
				}
				// Title Browser
				$this->view->title = "Edit Data Program Studi ".$nm_prodi;
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Edit Data Program Studi";
			}
			
			// navigation
			$this->_helper->navbar('prodi',0,0,0,0);
			// get jenjang prodi
			$jenjang=new JenjangProdi();
			$this->view->listJenjang = $jenjang->fetchAll();
		}
	}

	function edit2Action(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$kd=$this->_request->get('kd');
			// get data Prodi
			$prodiinfo0 = new ProdiInfo0();
			$getProdiInfo=$prodiinfo0->getProdiInfo0ByKd($kd);
			if($getProdiInfo){
				foreach ($getProdiInfo as $dtProdi) {
					$this->view->kd = $dtProdi['kd_prodi'];
					$this->view->gelar_id = $dtProdi['gelar_id'];
					$this->view->gelar_en = $dtProdi['gelar_en'];
					$this->view->jenis_pend_id = $dtProdi['jenis_pend_id'];
					$this->view->jenis_pend_en = $dtProdi['jenis_pend_en'];
					$this->view->req_pend_id = $dtProdi['req_pend_id'];
					$this->view->req_pend_en = $dtProdi['req_pend_en'];
					$this->view->bahasa_id = $dtProdi['bahasa_id'];
					$this->view->bahasa_en = $dtProdi['bahasa_en'];
					$this->view->lanjut_id = $dtProdi['studi_lanjut_id'];
					$this->view->lanjut_en = $dtProdi['studi_lanjut_en'];
					$nm_prodi=$dtProdi['nm_prodi'];
				}
				// Title Browser
				$this->view->title = "Edit Data Info Program Studi ".$nm_prodi;
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Edit Data Info Program Studi";
			}
			
			// navigation
			$this->_helper->navbar('prodi',0,0,0,0);
		}
	}

	function detilAction(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$kd=$this->_request->get('kd');
			// get data Prodi
			$prodi = new Prodi();
			$getProdi=$prodi->getProdiByKd($kd);
			if($getProdi){
				foreach ($getProdi as $dtProdi) {
					$this->view->kd = $dtProdi['kd_prodi'];
					$this->view->nm = $dtProdi['nm_prodi'];
					$nm_prodi=$dtProdi['nm_prodi'];
				}
				// Title Browser
				$this->view->title = "Detil Data Program Studi ".$nm_prodi;
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Detil Data Program Studi";
			}
			
			// navigation
			$this->_helper->navbar('prodi',0,0,0,0);
			// get jenjang prodi
			
			$prodiinfo0=new ProdiInfo0();
			$this->view->listProdiInfo = $prodiinfo0->getProdiInfo0ByKd($kd);
			$this->view->listcount = count($prodiinfo0->getProdiInfo0ByKd($kd));
		}
	}

	function detil2Action(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$kd=$this->_request->get('kd');
			// get data Prodi
			$prodi = new Prodi();
			$getProdi=$prodi->getProdiByKd($kd);
			if($getProdi){
				foreach ($getProdi as $dtProdi) {
					$this->view->kd = $dtProdi['kd_prodi'];
					$this->view->nm = $dtProdi['nm_prodi'];
					$nm_prodi=$dtProdi['nm_prodi'];
				}
				// Title Browser
				$this->view->title = "Detil Data Program Studi ".$nm_prodi;
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Detil Data Program Studi";
			}
			
			// navigation
			$this->_helper->navbar('prodi',0,0,0,0);
			// get jenjang prodi
			
			$labelprodi=new ProdiSkpiLabel();
			$this->view->listProdiSkpiLabel = $labelprodi->getProdiSkpiLabelByKd($kd);
			$this->view->listcount = count($labelprodi->getProdiSkpiLabelByKd($kd));
		}
	}

	function edit3Action(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$id=$this->_request->get('id');
			// get data Prodi
			$prodiskpilabel = new ProdiSkpiLabel();
			$getProdiSkpiLabel=$prodiskpilabel->getProdiSkpiLabelByid($id);
			if($getProdiSkpiLabel){
				foreach ($getProdiSkpiLabel as $dtProdi) {
					$this->view->id = $dtProdi['id_prodi_skpi_label'];
					$this->view->kd = $dtProdi['kd_prodi'];
					$this->view->capaian_kkni_id = $dtProdi['capaian_kkni_id'];
					$this->view->capaian_kkni_en = $dtProdi['capaian_kkni_en'];
					$this->view->capaian_asosiasi_id = $dtProdi['capaian_asosiasi_id'];
					$this->view->capaian_asosiasi_en = $dtProdi['capaian_asosiasi_en'];
					$this->view->capaian_pt_major_id = $dtProdi['capaian_pt_major_id'];
					$this->view->capaian_pt_major_en = $dtProdi['capaian_pt_major_en'];
					$this->view->capaian_pt_minor_id = $dtProdi['capaian_pt_minor_id'];
					$this->view->capaian_pt_minor_en = $dtProdi['capaian_pt_minor_en'];
					$this->view->capaian_pt_other_id = $dtProdi['capaian_pt_other_id'];
					$this->view->capaian_pt_other_en = $dtProdi['capaian_pt_other_en'];
					$nm_prodi=$dtProdi['nm_prodi'];
				}
				// Title Browser
				$this->view->title = "Edit Data Info Program Studi ".$nm_prodi;
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Edit Data Info Program Studi";
			}
			
			// navigation
			$this->_helper->navbar('prodi',0,0,0,0);
		}
	}

	// KKNI
	function capaian1Action(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$id=$this->_request->get('id');
			// get data Prodi
			$labelprodi=new ProdiSkpiLabel();
			$getProdiSpkiLabel = $labelprodi->getProdiSkpiLabelById($id);

			if($getProdiSpkiLabel){
				foreach ($getProdiSpkiLabel as $dtProdiSpkiLabel) {
					$this->view->id = $dtProdiSpkiLabel['id_prodi_skpi_label'];
					$this->view->nm = $dtProdiSpkiLabel['nm_prodi'];
					$this->view->kkni_in = $dtProdiSpkiLabel['capaian_kkni_id'];
					$this->view->kkni_en = $dtProdiSpkiLabel['capaian_kkni_en'];
					$nm_prodi=$dtProdiSpkiLabel['nm_prodi'];
					$kd_prodi= $dtProdiSpkiLabel['kd_prodi'];
				}
				// Title Browser
				$this->view->title = "Detil Data Prodi ".$nm_prodi." Capaian KKNI";
				$this->_helper->navbar('prodi/detil2?kd='.$kd_prodi,0,0,0,0);
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Detil Data Prodi ".$nm_prodi." Capaian KKNI";
				$this->_helper->navbar('prodi',0,0,0,0);
			}
			
			$kkni=new ProdiCapaianKkni();
			$this->view->listProdiCapaianKkni = $kkni->getProdiCapaianKkniBySkpiLabel($id);
		}
	}
	function editprdcapaian1Action(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$id=$this->_request->get('id');
			// get data Prodi
			$prodicapaiankkni = new ProdiCapaianKkni();
			$getProdiCapaianKkni=$prodicapaiankkni->getProdiCapaianKkniByid($id);
			if($getProdiCapaianKkni){
				foreach ($getProdiCapaianKkni as $dtKkni) {
					$this->view->id = $dtKkni['id_prodi_capaian_kkni'];
					$this->view->id_prodi_skpi_label = $dtKkni['id_prodi_skpi_label'];
					$this->view->nm = $dtKkni['nm_prodi'];
					$this->view->kkni_in = $dtKkni['capaian_kkni_id'];
					$this->view->kkni_en = $dtKkni['capaian_kkni_en'];
					$this->view->urutan = $dtKkni['urutan'];
					$this->view->is_numbered = $dtKkni['is_numbered'];
					$this->view->keterangan_id = $dtKkni['keterangan_id'];
					$this->view->keterangan_en = $dtKkni['keterangan_en'];
					$nm_prodi=$dtKkni['nm_prodi'];
					$id_skpi_label = $dtKkni['id_prodi_skpi_label'];
				}
				// Title Browser
				$this->view->title = "Edit Data Prodi ".$nm_prodi." Capaian KKNI";
				$this->_helper->navbar('prodi/capaian1?id='.$id_skpi_label,0,0,0,0);
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title ="Edit Data Prodi ".$nm_prodi." Capaian KKNI";
				$this->_helper->navbar('prodi',0,0,0,0);
			}
		}
	}

	//Asosiasi
	function capaian2Action(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$id=$this->_request->get('id');
			// get data Prodi
			$labelprodi=new ProdiSkpiLabel();
			$getProdiSpkiLabel = $labelprodi->getProdiSkpiLabelById($id);

			if($getProdiSpkiLabel){
				foreach ($getProdiSpkiLabel as $dtProdiSpkiLabel) {
					$this->view->id = $dtProdiSpkiLabel['id_prodi_skpi_label'];
					$this->view->nm = $dtProdiSpkiLabel['nm_prodi'];
					$this->view->asosiasi_in = $dtProdiSpkiLabel['capaian_asosiasi_id'];
					$this->view->asosiasi_en = $dtProdiSpkiLabel['capaian_asosiasi_en'];
					$nm_prodi=$dtProdiSpkiLabel['nm_prodi'];
					$kd_prodi=$dtProdiSpkiLabel['kd_prodi'];
				}
				// Title Browser
				$this->view->title = "Detil Data Prodi ".$nm_prodi." Capaian Asosiasi";
				$this->_helper->navbar('prodi/detil2?kd='.$kd_prodi,0,0,0,0);
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Detil Data Prodi ".$nm_prodi." Capaian Asosiasi";
				$this->_helper->navbar('prodi',0,0,0,0);
			}
			$asosiasi=new ProdiCapaianAsosiasi();
			$this->view->listProdiCapaianAsosiasi = $asosiasi->getProdiCapaianAsosiasiBySkpiLabel($id);
		}
	}
	function editprdcapaian2Action(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$id=$this->_request->get('id');
			// get data Prodi
			$prodicapaianasosiasi = new ProdiCapaianAsosiasi();
			$getProdiCapaianAsosiasi=$prodicapaianasosiasi->getProdiCapaianAsosiasiByid($id);
			if($getProdiCapaianAsosiasi){
				foreach ($getProdiCapaianAsosiasi as $dtAsosiasi) {
					$this->view->id = $dtAsosiasi['id_prodi_capaian_asosiasi'];
					$this->view->id_prodi_skpi_label = $dtAsosiasi['id_prodi_skpi_label'];
					$this->view->nm = $dtAsosiasi['nm_prodi'];
					$this->view->asosiasi_in = $dtAsosiasi['capaian_asosiasi_id'];
					$this->view->asosiasi_en = $dtAsosiasi['capaian_asosiasi_en'];
					$this->view->urutan = $dtAsosiasi['urutan'];
					$this->view->is_numbered = $dtAsosiasi['is_numbered'];
					$this->view->keterangan_id = $dtAsosiasi['keterangan_id'];
					$this->view->keterangan_en = $dtAsosiasi['keterangan_en'];
					$nm_prodi=$dtAsosiasi['nm_prodi'];
					$id_skpi_label = $dtAsosiasi['id_prodi_skpi_label'];
				}
				// Title Browser
				$this->view->title = "Edit Data Prodi ".$nm_prodi." Capaian Asosiasi";
				$this->_helper->navbar('prodi/capaian2?id='.$id_skpi_label,0,0,0,0);
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title ="Edit Data Prodi ".$nm_prodi." Capaian Asosiasi";
				$this->_helper->navbar('prodi',0,0,0,0);
			}
		}
	}

	// Pt Major
	function capaian3Action(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$id=$this->_request->get('id');
			// get data Prodi
			$labelprodi=new ProdiSkpiLabel();
			$getProdiSpkiLabel = $labelprodi->getProdiSkpiLabelById($id);

			if($getProdiSpkiLabel){
				foreach ($getProdiSpkiLabel as $dtProdiSpkiLabel) {
					$this->view->id = $dtProdiSpkiLabel['id_prodi_skpi_label'];
					$this->view->nm = $dtProdiSpkiLabel['nm_prodi'];
					$this->view->ptmajor_in = $dtProdiSpkiLabel['capaian_pt_major_id'];
					$this->view->ptmajor_en = $dtProdiSpkiLabel['capaian_pt_major_en'];
					$nm_prodi=$dtProdiSpkiLabel['nm_prodi'];
					$kd_prodi=$dtProdiSpkiLabel['kd_prodi'];
				}
				// Title Browser
				$this->view->title = "Detil Data Prodi ".$nm_prodi." Capaian Perguruan Tinggi";
				$this->_helper->navbar('prodi/detil2?kd='.$kd_prodi,0,0,0,0);
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Detil Data Prodi ".$nm_prodi." Capaian Perguruan Tinggi";
				$this->_helper->navbar('prodi',0,0,0,0);
			}
			$ptmajor=new ProdiCapaianPtMajor();
			$this->view->listProdiCapaianPtMajor = $ptmajor->getProdiCapaianPtMajorBySkpiLabel($id);
		}
	}
	function editprdcapaian3Action(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$id=$this->_request->get('id');
			// get data Prodi
			$prodicapaianptmajor = new ProdiCapaianPtMajor();
			$getProdiCapaianPtMajor=$prodicapaianptmajor->getProdiCapaianPtMajorByid($id);
			if($getProdiCapaianPtMajor){
				foreach ($getProdiCapaianPtMajor as $dtptmajor) {
					$this->view->id = $dtptmajor['id_prodi_capaian_pt_major'];
					$this->view->id_prodi_skpi_label = $dtptmajor['id_prodi_skpi_label'];
					$this->view->nm = $dtptmajor['nm_prodi'];
					$this->view->ptmajor_in = $dtptmajor['capaian_pt_major_id'];
					$this->view->ptmajor_en = $dtptmajor['capaian_pt_major_en'];
					$this->view->urutan = $dtptmajor['urutan'];
					$this->view->is_numbered = $dtptmajor['is_numbered'];
					$this->view->keterangan_id = $dtptmajor['keterangan_id'];
					$this->view->keterangan_en = $dtptmajor['keterangan_en'];
					$nm_prodi=$dtptmajor['nm_prodi'];
					$id_skpi_label = $dtptmajor['id_prodi_skpi_label'];
				}
				// Title Browser
				$this->view->title = "Edit Data Prodi ".$nm_prodi." Capaian Perguruan Tinggi";
				$this->_helper->navbar('prodi/capaian3?id='.$id_skpi_label,0,0,0,0);
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title ="Edit Data Prodi ".$nm_prodi." Capaian Perguruan Tinggi";
				$this->_helper->navbar('prodi',0,0,0,0);
			}
		}
	}

	// Pt Minor
	function capaian4Action(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$id=$this->_request->get('id');
			// get data Prodi
			$labelprodi=new ProdiSkpiLabel();
			$getProdiSpkiLabel = $labelprodi->getProdiSkpiLabelById($id);

			if($getProdiSpkiLabel){
				foreach ($getProdiSpkiLabel as $dtProdiSpkiLabel) {
					$this->view->id = $dtProdiSpkiLabel['id_prodi_skpi_label'];
					$this->view->nm = $dtProdiSpkiLabel['nm_prodi'];
					$this->view->ptminor_in = $dtProdiSpkiLabel['capaian_pt_minor_id'];
					$this->view->ptminor_en = $dtProdiSpkiLabel['capaian_pt_minor_en'];
					$nm_prodi=$dtProdiSpkiLabel['nm_prodi'];
					$kd_prodi=$dtProdiSpkiLabel['kd_prodi'];
				}
				// Title Browser
				$this->view->title = "Detil Data Prodi ".$nm_prodi." Capaian Perguruan Tinggi (Pendukung)";
				$this->_helper->navbar('prodi/detil2?kd='.$kd_prodi,0,0,0,0);
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Detil Data Prodi ".$nm_prodi." Capaian Perguruan Tinggi (Pendukung)";
				$this->_helper->navbar('prodi'.$kd_prodi,0,0,0,0);
			}
			$pt_minor=new ProdiCapaianPtMinor();
			$this->view->listProdiCapaianPtMinor = $pt_minor->getProdiCapaianPtMinorBySkpiLabel($id);
		}
	}
	function editprdcapaian4Action(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$id=$this->_request->get('id');
			// get data Prodi
			$prodicapaianptminor = new ProdiCapaianPtMinor();
			$getProdiCapaianPtMinor=$prodicapaianptminor->getProdiCapaianPtMinorByid($id);
			if($getProdiCapaianPtMinor){
				foreach ($getProdiCapaianPtMinor as $dtptminor) {
					$this->view->id = $dtptminor['id_prodi_capaian_pt_minor'];
					$this->view->id_prodi_skpi_label = $dtptminor['id_prodi_skpi_label'];
					$this->view->nm = $dtptminor['nm_prodi'];
					$this->view->ptminor_in = $dtptminor['capaian_pt_minor_id'];
					$this->view->ptminor_en = $dtptminor['capaian_pt_minor_en'];
					$this->view->urutan = $dtptminor['urutan'];
					$this->view->is_numbered = $dtptminor['is_numbered'];
					$this->view->keterangan_id = $dtptminor['keterangan_id'];
					$this->view->keterangan_en = $dtptminor['keterangan_en'];
					$nm_prodi=$dtptminor['nm_prodi'];
					$id_skpi_label = $dtptminor['id_prodi_skpi_label'];
				}
				// Title Browser
				$this->view->title = "Edit Data Prodi ".$nm_prodi." Capaian Perguruan Tinggi (Pendukung)";
				$this->_helper->navbar('prodi/capaian4?id='.$id_skpi_label,0,0,0,0);
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title ="Edit Data Prodi ".$nm_prodi." Capaian Perguruan Tinggi (Pendukung)";
				$this->_helper->navbar('prodi',0,0,0,0);
			}
		}
	}

	// Pt Other
	function capaian5Action(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$id=$this->_request->get('id');
			// get data Prodi
			$labelprodi=new ProdiSkpiLabel();
			$getProdiSpkiLabel = $labelprodi->getProdiSkpiLabelById($id);
			
			if($getProdiSpkiLabel){
				foreach ($getProdiSpkiLabel as $dtProdiSpkiLabel) {
					$this->view->id = $dtProdiSpkiLabel['id_prodi_skpi_label'];
					$this->view->nm = $dtProdiSpkiLabel['nm_prodi'];
					$this->view->ptother_in = $dtProdiSpkiLabel['capaian_pt_other_id'];
					$this->view->ptother_en = $dtProdiSpkiLabel['capaian_pt_other_en'];
					$nm_prodi=$dtProdiSpkiLabel['nm_prodi'];
					$kd_prodi=$dtProdiSpkiLabel['kd_prodi'];
				}
				// Title Browser
				$this->view->title = "Detil Data Prodi ".$nm_prodi." Capaian Lain";
				$this->_helper->navbar('prodi/detil2?kd='.$kd_prodi,0,0,0,0);
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Detil Data Prodi ".$nm_prodi." Capaian Lain";
				$this->_helper->navbar('prodi',0,0,0,0);
			}
			$pt_other=new ProdiCapaianPtOther();
			$this->view->listProdiCapaianPtOther = $pt_other->getProdiCapaianPtOtherBySkpiLabel($id);
			
		}
	}
	function editprdcapaian5Action(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$id=$this->_request->get('id');
			// get data Prodi
			$prodicapaianptother = new ProdiCapaianPtOther();
			$getProdiCapaianPtOther=$prodicapaianptother->getProdiCapaianPtOtherByid($id);
			if($getProdiCapaianPtOther){
				foreach ($getProdiCapaianPtOther as $dtptother) {
					$this->view->id = $dtptother['id_prodi_capaian_pt_other'];
					$this->view->id_prodi_skpi_label = $dtptother['id_prodi_skpi_label'];
					$this->view->nm = $dtptother['nm_prodi'];
					$this->view->ptother_in = $dtptother['capaian_pt_other_id'];
					$this->view->ptother_en = $dtptother['capaian_pt_other_en'];
					$this->view->urutan = $dtptother['urutan'];
					$this->view->is_numbered = $dtptother['is_numbered'];
					$this->view->keterangan_id = $dtptother['keterangan_id'];
					$this->view->keterangan_en = $dtptother['keterangan_en'];
					$nm_prodi=$dtptother['nm_prodi'];
					$id_skpi_label = $dtptother['id_prodi_skpi_label'];
				}
				// Title Browser
				$this->view->title = "Edit Data Prodi ".$nm_prodi." Capaian Lain";
				$this->_helper->navbar('prodi/capaian5?id='.$id_skpi_label,0,0,0,0);
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title ="Edit Data Prodi ".$nm_prodi." Capaian Lain";
				$this->_helper->navbar('prodi',0,0,0,0);
			}
		}
	}

	function detil3Action(){
		$user = new Menu();
		$menu = "prodi/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get param
			$kd=$this->_request->get('kd');
			// get data Prodi
			$prodi = new Prodi();
			$getProdi=$prodi->getProdiByKd($kd);
			if($getProdi){
				foreach ($getProdi as $dtProdi) {
					$this->view->kd = $dtProdi['kd_prodi'];
					$this->view->nm = $dtProdi['nm_prodi'];
					$nm_prodi=$dtProdi['nm_prodi'];
				}
				// Title Browser
				$this->view->title = "Data Kepala Program Studi ".$nm_prodi;
			}else{
				$this->view->eksis="f";
				// Title Browser
				$this->view->title = "Data Kepala Program Studi";
			}
			// data dosen
			$dosen = new Dosen();
			$this->view->listDosenAktif = $dosen->getDosenWali();
			// navigation
			$this->_helper->navbar('prodi',0,0,0,0);
			$kaprodi=new Kaprodi();
			$this->view->listKaprodi = $kaprodi->getKaprodiByProdi($kd);
		}
	}

}