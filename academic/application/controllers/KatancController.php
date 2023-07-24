<?php

class KatancController extends Zend_Controller_Action
{
    function init()
    {
        parent::init();
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
        Zend_Loader::loadClass('Validation');
        Zend_Loader::loadClass('AnnouncementKategori');
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
	$this->view->active_menu="katanc/index";
    }

    function indexAction()
    {
        // Title Browser
        $this->view->title = "Kategori pengumuman";
        // navigation
        $this->_helper->navbar(0,0,'katanc/new',0,0);
        $announcement_kategori= new AnnouncementKategori();
        $this->view->listAnnouncementkategori=$announcement_kategori->fetchAll();
    }
    
    function newAction()
    {
        // Title Browser
        $this->view->title = "Input Data Kategori pengumuman";
        // navigation
        $this->_helper->navbar(0,'katanc',0,0,0);
    }
    
    function ainsAction(){
        // disable layout
        $this->_helper->layout->disableLayout();
        // start inserting
        $request = $this->getRequest()->getPost();
        $announcement_kategori = $this->_helper->string->esc_quote(trim($request['announcement_kategori']));
        // validation
        $err=0;
        $msg="";
        $vd = new Validation();
        if($vd->validasiLength($announcement_kategori,1,50)=='F'){
            $err++;
            $msg=$msg."<strong>- Kategori pengumuman tidak boleh kosong maksimal 50 huruf</strong><br>";
        }
        if($err==0){
            $announKategori = new AnnouncementKategori();
           $setannounKategori = $announKategori->setAnnouncementKategori($announcement_kategori);
            echo $setannounKategori;
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
        $announkategori = new AnnouncementKategori();
        $delannounkategori=$announkategori->delAnnouncementKategori($id);
        echo $delannounkategori;
    }
    
    function editAction()
    {
        // Title Browser
        $this->view->title = "Edit Data Kategori pengumuman";
        // Navbar
        $this->_helper->navbar('katanc',0,0,0,0);
        // get data
        $id=$this->_request->get('id');
        $announkategori=new AnnouncementKategori();
        $getannounkategori=$announkategori->getAnnouncementKategoriById($id);
        if(!$getannounkategori){
            $this->view->eksis="f";
        }else{
            foreach ($getannounkategori as $data){
                $this->view->id_announcement_kategori=$data['id_announcement_kategori'];
                $this->view->announcement_kategori=$data['announcement_kategori'];
            }
        }
    }
    
    function aupdAction(){
        // disable layout
        $this->_helper->layout->disableLayout();
        // start inserting
        $request = $this->getRequest()->getPost();
        $id_announcement_kategori= $request['id_announcement_kategori'];
        $announcement_kategori = $this->_helper->string->esc_quote(trim($request['announcement_kategori']));
        // validation
        $err=0;
        $msg="";
        $vd = new Validation();
        if($vd->validasiLength($announcement_kategori,1,50)=='F'){
            $err++;
            $msg=$msg."<strong>- Kategori pengumuman tidak boleh kosong maksimal 50 huruf</strong><br>";
        }
        if($err==0){
            $announcementkategori = new AnnouncementKategori();
            $updannouncementkategori = $announcementkategori->updAnnouncementKategori($announcement_kategori,$id_announcement_kategori);
            echo $updannouncementkategori;
        }else{
            echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
        }
    }

}
