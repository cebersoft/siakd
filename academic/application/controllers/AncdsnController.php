<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Home Controller -> Controller untuk modul home
*/


class AncdsnController extends Zend_Controller_Action
{
	function init()
	{
	    parent::init();
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('AnnouncementDsn');
		Zend_Loader::loadClass('AnnouncementKategori');
		Zend_Loader::loadClass('Validation');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
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
		 $this->username=$ses_ac->uname;
		// layout
		$this->_helper->layout()->setLayout('main');
		// treeview
		$this->view->active_tree="15";
		$this->view->active_menu="ancdsn/index";
	}
	 function indexAction()
    {
        // Title Browser
        $this->view->title = "Pengumuman Dosen";
        // destroy session param
        Zend_Session::namespaceUnset('par_ancdsn');
        // navigation
        $this->_helper->navbar(0,0,'ancdsn/new',0,0);
        $anc_kategori= new AnnouncementKategori();
        $this->view->list_Kat=$anc_kategori->fetchAll();
    }

    function ashowAction(){
        // makes disable layout
        $this->_helper->getHelper('layout')->disableLayout();
        $request = $this->getRequest()->getPost();
        // gets value from ajax request
        $id_announcement_kategori=$request['id_announcement_kategori'];
        $tgl1=$request['tgl1'];
        $tgl2=$request['tgl2'];
        // set session
        $param = new Zend_Session_Namespace('par_ancdsn');
        $param->id_announcement_kategori=$id_announcement_kategori;
        $param->tgl1=$tgl1;
        $param->tgl2=$tgl2;
    }

	function listAction(){
        // Title Browser
        $this->view->title = "Daftar Pengumuman Dosen";
        // Navbar
        $this->_helper->navbar('ancdsn',0,'ancdsn/new',0,0);
        // collapse
        $this->view->sidecolaps="sidebar-collapse";
        // get param
        $param = new Zend_Session_Namespace('par_ancdsn');
        $id_announcement_kategori=$param->id_announcement_kategori;
        $tgl1=$param->tgl1;
        $tgl2=$param->tgl2;
        // get data
        $ancdsn=new AnnouncementDsn();
        $getAncdsn=$ancdsn->queryAnnouncementDsn($id_announcement_kategori,$tgl1,$tgl2);
        $this->view->listAncdsn=$getAncdsn;
    }
	
	function newAction()
	{
	    // Title Browser
	    $this->view->title = "Input Data Pengumuman Dosen";
        // navigation
        $this->_helper->navbar(0,'ancdsn',0,0,0);
        $ancKategori= new AnnouncementKategori();
		$this->view->listAncKategori=$ancKategori->fetchAll();
	}
	
	function ainsAction(){
	    // disable layout
	    $this->_helper->layout->disableLayout();
	    // start inserting
	    $request = $this->getRequest()->getPost();
	    $judul = $this->_helper->string->esc_quote(trim($request['judul']));
	    $konten = $this->_helper->string->esc_quote(trim($request['konten']));
	    $id_kategori = $this->_helper->string->esc_quote(trim($request['id_kategori']));
	    $status = $this->_helper->string->esc_quote($request['status']);
	    $date = $this->_helper->string->esc_quote($request['date']);
	    $username_in = $this->username;
	    // validation
	    $err=0;
	    $msg="";
	    $vd = new Validation();
	    if($vd->validasiLength($judul,1,200)=='F'){
	        $err++;
	        $msg=$msg."<strong>- Judul tidak boleh kosong maksimal 200 huruf</strong><br>";
	    }
	     if($vd->validasiLength($id_kategori,1,200)=='F'){
	        $err++;
	        $msg=$msg."<strong>- Kategori tidak boleh kosong maksimal 200 huruf</strong><br>";
	    }
	   
	    if($vd->validasiLength($status,1,200)=='F'){
	        $err++;
	        $msg=$msg."<strong>- Status tidak boleh kosong maksimal 200 huruf</strong><br>";
	    }
	    if($vd->validasiLength($date,1,200)=='F'){
	        $err++;
	        $msg=$msg."<strong>- Tanggal tidak boleh kosong maksimal 200 huruf</strong><br>";
	    }
	    if($err==0){
	        $ancdsn = new AnnouncementDsn();
	       $setAnnouncementDsn = $ancdsn->setAnnouncementDsn($judul,$konten,$id_kategori,$status,$date,$username_in);
	        echo $setAnnouncementDsn;
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
	    $ancdsn = new AnnouncementDsn();
	    $delAncdsn=$ancdsn->delAnnouncementDsn($id);
	    echo $delAncdsn;
	}
	
	function editAction()
	{
	    // Title Browser
	    $this->view->title = "Edit Data Pengumuman Dosen";
	    // Navbar
	    $this->_helper->navbar('ancdsn',0,0,0,0);
	    // get data
	    $id=$this->_request->get('id');
	    $ancdsn=new AnnouncementDsn();
	    $getAncdsn=$ancdsn->getAnnouncementDsnById($id);
	    if(!$getAncdsn){
	        $this->view->eksis="f";
	    }else{
	    	 $ancdsn_Kategori= new AnnouncementKategori();
			$this->view->listAnc_Kategori=$ancdsn_Kategori->fetchAll();
	        foreach ($getAncdsn as $data){
	            $this->view->id=$data['id_announcement_dsn'];
	            $this->view->judul=$data['judul'];
	            $this->view->id_kategori=$data['id_announcement_kategori'];
	     		$this->view->konten=$data['konten'];
	            $this->view->status=$data['status'];
	            $this->view->date=$data['date_expired_fmt'];
	        }
	    }
	}
	
	function aupdAction(){
	    // disable layout
	    $this->_helper->layout->disableLayout();
	    // start inserting
	    $request = $this->getRequest()->getPost();
	    $id_ancdsn= $request['id'];
	    $judul = $this->_helper->string->esc_quote(trim($request['judul']));
	    $konten = $this->_helper->string->esc_quote(trim($request['konten']));
	    $id_kategori = $this->_helper->string->esc_quote(trim($request['id_kategori']));
	    $status = $this->_helper->string->esc_quote($request['status']);
	    $date = $this->_helper->string->esc_quote($request['date']);
	    $username_ed = $this->username;
	    // validation
	    $err=0;
	    $msg="";
	    $vd = new Validation();
	    if($vd->validasiLength($judul,1,200)=='F'){
	        $err++;
	        $msg=$msg."<strong>- Judul  tidak boleh kosong maksimal 200 huruf</strong><br>";
	    }
	     if($vd->validasiLength($id_kategori,1,200)=='F'){
	        $err++;
	        $msg=$msg."<strong>- Kategori  tidak boleh kosong maksimal 200 huruf</strong><br>";
	    }
	    
	    if($vd->validasiLength($status,1,200)=='F'){
	        $err++;
	        $msg=$msg."<strong>- Status  tidak boleh kosong maksimal 200 huruf</strong><br>";
	    }
	    if($vd->validasiLength($date,1,200)=='F'){
	        $err++;
	        $msg=$msg."<strong>- Tanggal tidak boleh kosong maksimal 200 huruf</strong><br>";
	    }
	    if($err==0){
	        $ancdsn = new AnnouncementDsn();
	        $updAncdsn = $ancdsn->updAnnouncementDsn($judul,$konten,$id_kategori,$status,$date,$username_ed,$id_ancdsn);
	        echo $updAncdsn;
	    }else{
	        echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
	    }
	}
	
	function detilAction()
	{
	    // Title Browser
	    $this->view->title = "Edit Data Pengumuman Dosen";
	    // Navbar
	    $this->_helper->navbar('ancdsn/list',0,0,0,0);
	    // get data
	    $id=$this->_request->get('id');
	    $ancdsn=new AnnouncementDsn();
	    $getAncdsn=$ancdsn->getAnnouncementDsnById($id);
	    if(!$getAncdsn){
	        $this->view->eksis="f";
	    }else{
	        $ancdsn_Kategori= new AnnouncementKategori();
	        $this->view->listAnc_Kategori=$ancdsn_Kategori->fetchAll();
	        foreach ($getAncdsn as $data){
	            $this->view->id=$data['id_announcement_dsn'];
	            $this->view->judul=$data['judul'];
	            $this->view->id_kategori=$data['id_announcement_kategori'];
	            $this->view->konten=$data['konten'];
	            $this->view->status=$data['status'];
	            $this->view->usr_crt=$data['nm_user_created'];
	            $this->view->usr_edt=$data['nm_user_edited'];
	            $this->view->date_crt=$data['date_created_fmt'];
	            $this->view->date_edt=$data['date_edited_fmt'];
	            $this->view->time_crt=date('H:i',strtotime($data['time_created']));
	            $this->view->time_edt=date('H:i',strtotime($data['time_edited']));
	            $this->view->date_exp=$data['date_expired_fmt'];
	        }
	    }
	}
}