<?php 
class SoapController extends Zend_Controller_Action
{
    private $_WSDL_URI="https://academic.stfi.ac.id/soap?wsdl";
 
    
    function init()
    {
        $this->initView();
        Zend_Loader::loadClass('Zend_Session');
        Zend_Loader::loadClass('Zend_Soap_Server');
        Zend_Loader::loadClass('Zend_Soap_Client');
        Zend_Loader::loadClass('Zend_Soap_AutoDiscover');
        Zend_Loader::loadClass('Soaps');
        $this->view->baseUrl = $this->_request->getBaseUrl();
        // layout
        $this->_helper->layout->disableLayout();
    }
    
    public function indexAction()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        if(isset($_GET['wsdl'])) {
            //mengembalikan wsdl...
            $this->hadlereturnWSDL();
        } else {
            //menghandle SOAP request...
            $this->handleActualSOAP();
        }
        die;
    }
 
    private function hadlereturnWSDL() {
        $autodiscover = new Zend_Soap_AutoDiscover();
        $autodiscover->setClass('Soaps');
        $autodiscover->handle();
    }
 
    private function handleActualSOAP()
    {
        $soap = new Zend_Soap_Server($this->_WSDL_URI);
        $soap->setClass('Soaps');
        $soap->handle();
    }
 
    public function clientAction() {
        
        //isi variable...
        try {
            $client = new Zend_Soap_Client($this->_WSDL_URI);
            $tambah =  $client->tambah(10,2);
        } catch (Exception $e) {
            echo $e;
            die();
        }
        $this->view->r=$tambah;
    }
}