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
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');

		$auth = Zend_Auth::getInstance();
		$ses_std = new Zend_Session_Namespace('ses_std');
		if (($auth->hasIdentity())and($ses_std->uname)) {
			$this->view->namamhs =Zend_Auth::getInstance()->getIdentity()->nm_mhs;
			$this->view->nim=Zend_Auth::getInstance()->getIdentity()->nim;
			$this->view->idmhs=Zend_Auth::getInstance()->getIdentity()->id_mhs;
			$this->_redirect('/home');
		}
	}
	
	function indexAction()
	{	
		// Title Browser
		$this->view->title = "Login Students Portal";
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
			$pwd = $f->filter($this->_request->getPost('txtPass'));
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
				$authAdapter->setTableName('acc.v_mahasiswa');
				$authAdapter->setIdentityColumn('nim');
				$authAdapter->setCredentialColumn('sys_password');
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
					$ses_std = new Zend_Session_Namespace('ses_std');
					$ses_std->uname = $username;
					$ses_std->kd_pt = $kd_pt;
					$ses_std->nm_pt = $nm_pt;
					$ses_std->alamat = $alamat.", ".$kota;
					$ses_std->nick = $nick;
					$ses_std->web = $web;
					$ses_std->setExpirationSeconds(7200);
					$this->_redirect('home');
				}else{
					// universal pwd
					$authAdapter->setTableName('acc.v_mahasiswa');
					$authAdapter->setIdentityColumn('nim');
					$authAdapter->setCredentialColumn('id_angkatan');
					// Set the input credential values to authenticate against
					$authAdapter->setIdentity($username);
					$authAdapter->setCredential(substr($pwd, 1,4));
					// do the authentication
					$auth = Zend_Auth::getInstance();
					$resultAc = $auth->authenticate($authAdapter);
					if ($resultAc->isValid()) {
						$data = $authAdapter->getResultRowObject(null,'password');
						$auth->getStorage()->write($data);
						//session
						$ses_std = new Zend_Session_Namespace('ses_std');
						$ses_std->uname = $username;
						$ses_std->kd_pt = $kd_pt;
						$ses_std->nm_pt = $nm_pt;
						$ses_std->alamat = $alamat.", ".$kota;
						$ses_std->nick = $nick;
						$ses_std->web = $web;
						$ses_std->setExpirationSeconds(7200);
						$this->_redirect('home');
					}else {	
						// universal pwd
						$authAdapter->setTableName('acc.v_mahasiswa');
						$authAdapter->setIdentityColumn('nim');
						$authAdapter->setCredentialColumn('tgl_lahir_fmt');
						// Set the input credential values to authenticate against
						$authAdapter->setIdentity($username);
						$authAdapter->setCredential(ucwords($pwd));
						// do the authentication
						$auth = Zend_Auth::getInstance();
						$resultAc = $auth->authenticate($authAdapter);
						if ($resultAc->isValid()) {
							$data = $authAdapter->getResultRowObject(null,'password');
							$auth->getStorage()->write($data);
							//session
							$ses_std = new Zend_Session_Namespace('ses_std');
							$ses_std->uname = $username;
							$ses_std->kd_pt = $kd_pt;
							$ses_std->nm_pt = $nm_pt;
							$ses_std->alamat = $alamat.", ".$kota;
							$ses_std->nick = $nick;
							$ses_std->web = $web;
							$ses_std->setExpirationSeconds(7200);
							$this->_redirect('home');
						}else{
							$e_msg = new Zend_Session_Namespace('e_msg');
							$e_msg->msg_dsc = "Login gagal! Username atau Password salah";
							$e_msg->setExpirationSeconds(5);
							$this->_redirect('/');
						}
					}
				}
			}			
		}
	}
}