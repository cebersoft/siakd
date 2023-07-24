<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Index Controller -> Controller untuk modul index, login atau redirect ke home
*/
class IndexController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('UserFin');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');

		$auth = Zend_Auth::getInstance();
		$ses_fin = new Zend_Session_Namespace('ses_fin');
		if (($auth->hasIdentity())and($ses_fin->uname)) {
			$this->view->namauser =Zend_Auth::getInstance()->getIdentity()->nama;
			$this->view->username=Zend_Auth::getInstance()->getIdentity()->username;
			$this->_redirect('/home');
		}
	}
	
	function indexAction()
	{	
		// Title Browser
		$this->view->title = "Login Sistem Informasi Keuangan Mahasiswa";
		// disabel layout
		$this->_helper->layout->disableLayout();
		// get data profil PT
		$kd_pt="";
		$nm_pt="";
		$alamat="";
		$profil_pt = new Profile();
		$getProfil = $profil_pt->fetchAll();
		if($getProfil){
			foreach ($getProfil as $dataProfil) {
				$kd_pt=$dataProfil['kode_pt'];
				$nm_pt=$dataProfil['nama_pt'];
				$alamat=$dataProfil['alamat'];
				$kota=$dataProfil['kota'];
				$nick=$dataProfil['nickname'];
				$web=$dataProfil['web'];
			}
		}
		$this->view->nickname=$nick;
		$this->view->nm_pt=$nm_pt;
		// message
		$e_msg = new Zend_Session_Namespace('e_msg');
		$this->view->message = $e_msg->msg_dsc;
		if ($this->_request->isPost()) {
			// collect the data from the user
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$f = new Zend_Filter_StripTags();
			$username = $f->filter($this->_request->getPost('txtUsername'));
			$password = $f->filter($this->_request->getPost('txtPass'));
			$password = md5($password);
			if (empty($username)) {
				$e_msg = new Zend_Session_Namespace('e_msg');
				$e_msg->msg_dsc = "Username tidak boleh kosong!";
				$e_msg->setExpirationSeconds(5);
				$this->_redirect('/');
			} else {
				// setup Zend_Auth adapter for a database table
				Zend_Loader::loadClass('Zend_Auth_Adapter_DbTable');
				$dbAdapter = Zend_Registry::get('dbAdapter');
				$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
				$authAdapter->setTableName('sys.user_fin');
				$authAdapter->setIdentityColumn('username');
				$authAdapter->setCredentialColumn('pwd');
				// Set the input credential values to authenticate against
				$authAdapter->setIdentity($username);
				$authAdapter->setCredential($password);
				// do the authentication
				$auth = Zend_Auth::getInstance();
				$resultAc = $auth->authenticate($authAdapter);
				if ($resultAc->isValid()) {
					$data = $authAdapter->getResultRowObject(null,'password');
					$auth->getStorage()->write($data);
					//session
					$ses_fin = new Zend_Session_Namespace('ses_fin');
					$ses_fin->uname = $username;
					$ses_fin->kd_pt = $kd_pt;
					$ses_fin->nm_pt = $nm_pt;
					$ses_fin->alamat = $alamat.", ".$kota;
					$ses_fin->nick = $nick;
					$ses_fin->web = $web;
					$userFin = new UserFin();
					$getUser=$userFin->getUserFinByUname($username);
					$superadmin="f";
					foreach ($getUser as $dtUser){
						$superadmin=$dtUser['superadmin'];
					}
					$ses_fin->super = $superadmin;
					$ses_fin->setExpirationSeconds(7200);
					$this->_redirect('home');
				}else{
					$e_msg = new Zend_Session_Namespace('e_msg');
					$e_msg->msg_dsc = "Login gagal!<br>Username atau Password salah";
					$e_msg->setExpirationSeconds(5);
					$this->_redirect('/');
				}
			}
		}
	}
}