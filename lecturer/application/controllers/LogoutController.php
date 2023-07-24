<?php
/*
	Programmer	: Tiar Aristian
	Release		: Maret 2011
	Module		: Logout Controller -> Untuk modul logout
*/
class LogoutController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
	}
	function indexAction()
	{
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
		     Zend_Auth::getInstance()->clearIdentity();
		} 
		Zend_Session::destroy();
        $this->_redirect('/');
	}
}