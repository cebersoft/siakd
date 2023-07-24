<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Image Controller -> Controller untuk modul image public
*/
class ImageController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		// disabel layout
		$this->_helper->layout->disableLayout();
	}
	
	function pngAction()
	{
		$url=$this->_request->get('url');
		$path=Zend_Registry::get('pathOutside');
		$logo = file_get_contents($path.$url);
	    $type = 'image/png'; 
	    $response = $this->getFrontController()->getResponse(); 
	    $response->setHeader('Content-Type', $type, true); 
		//$response->setHeader('Content-Length', count($logo), true); 
	    $response->setHeader('Content-Transfer-Encoding', 'binary', true); 
	    $response->setHeader('Cache-Control', 'max-age=3600, must-revalidate', true); 
	    $response->setBody($logo);
	    $response->sendResponse(); 
	    exit; 
	}
	
	function jpgAction()
	{
		$url=$this->_request->get('url');
		$path=Zend_Registry::get('pathOutside');
		$logo = file_get_contents($path.$url);
	    $type = 'image/jpeg'; 
	    $response = $this->getFrontController()->getResponse(); 
	    $response->setHeader('Content-Type', $type, true); 
		//$response->setHeader('Content-Length', count($logo), true); 
	    $response->setHeader('Content-Transfer-Encoding', 'binary', true); 
	    $response->setHeader('Cache-Control', 'max-age=3600, must-revalidate', true); 
	    $response->setBody($logo);
	    $response->sendResponse(); 
	    exit; 
	}
}