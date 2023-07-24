<?php

class KatdocController extends Zend_Controller_Action
{
    function init()
    {
        parent::init();
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
        Zend_Loader::loadClass('Validation');
        Zend_Loader::loadClass('ArsipKategori');
        Zend_Loader::loadClass('Zend_Session');
        Zend_Loader::loadClass('Zend_Layout');
        Zend_Loader::loadClass('User');
	Zend_Loader::loadClass('Menu');
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
        $this->view->active_tree="15";
	$this->view->active_menu="katdoc/index";

    }

    function indexAction()
    {
        // Title Browser
        $this->view->title = "Kategori Arsip";
        // navigation
        $this->_helper->navbar(0,0,'katdoc/new',0,0);
        $arsip_kategori= new ArsipKategori();
        $this->view->listArsipkategori=$arsip_kategori->fetchAll();
    }
    
    function newAction()
    {
      
        // Title Browser
        $this->view->title = "Input Data Kategori Arsip";
        // navigation
        $this->_helper->navbar(0,'katdoc',0,0,0);
        // 
       
    }
    
    function ainsAction(){
        // disable layout
        $this->_helper->layout->disableLayout();
        // start inserting
        $request = $this->getRequest()->getPost();
        $arsip_kategori = $this->_helper->string->esc_quote(trim($request['arsip_kategori']));
        // validation
        $err=0;
        $msg="";
        $vd = new Validation();
        if($vd->validasiLength($arsip_kategori,1,50)=='F'){
            $err++;
            $msg=$msg."<strong>- Kategori Arsip tidak boleh kosong maksimal 50 huruf</strong><br>";
        }
        if($err==0){
            $arsipKategori = new ArsipKategori();
           $setarsipKategori = $arsipKategori->setArsipKategori($arsip_kategori);
            echo $setarsipKategori;
        }else{
            echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
        }
    }
    
    function adelAction(){
        // disabel layout
        $this->_helper->layout->disableLayout();
        // set database
        $request = $this->getRequest()->getPost();
        // gets value from ajax request
        $param = $request['param'];
        $id = $param[0];
        $arsipkategori = new ArsipKategori();
        $delarsipkategori=$arsipkategori->delArsipKategori($id);
        echo $delarsipkategori;
    }
    
    function editAction()
    {
        // Title Browser
        $this->view->title = "Edit Data Kategori Arsip";
        // Navbar
        $this->_helper->navbar('katdoc',0,0,0,0);
        // get data
        $id=$this->_request->get('id');
        $arsipkategori=new ArsipKategori();
        $getarsipkategori=$arsipkategori->getArsipKategoriById($id);
        if(!$getarsipkategori){
            $this->view->eksis="f";
        }else{
            foreach ($getarsipkategori as $data){
                $this->view->id_arsip_kategori=$data['id_arsip_kategori'];
                $this->view->arsip_kategori=$data['arsip_kategori'];
            }
        }
        
    }
    
    function aupdAction(){
        // disable layout
        $this->_helper->layout->disableLayout();
        // start inserting
        $request = $this->getRequest()->getPost();
        $id_arsip_kategori= $request['id_arsip_kategori'];
        $arsip_kategori = $this->_helper->string->esc_quote(trim($request['arsip_kategori']));
        // validation
        $err=0;
        $msg="";
        $vd = new Validation();
        if($vd->validasiLength($arsip_kategori,1,50)=='F'){
            $err++;
            $msg=$msg."<strong>- Kategori Arsip tidak boleh kosong maksimal 50 huruf</strong><br>";
        }
        if($err==0){
            $arsipkategori = new ArsipKategori();
            $updarsipkategori = $arsipkategori->updArsipKategori($arsip_kategori,$id_arsip_kategori);
            echo $updarsipkategori;
        }else{
            echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
        }
    }

}