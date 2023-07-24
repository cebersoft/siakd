<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Home Controller -> Controller untuk modul home
*/


class AncmhsController extends Zend_Controller_Action
{
	function init()
	{
	    parent::init();
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('AnnouncementMhs');
		Zend_Loader::loadClass('AnnouncementKategori');
		Zend_Loader::loadClass('AnnouncementAngkatan');
		Zend_Loader::loadClass('AnnouncementProdi');
		Zend_Loader::loadClass('AnnouncementDw');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Dosen');
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
		$this->view->active_menu="ancmhs/index";
	}
	 function indexAction()
    {
        // Title Browser
        $this->view->title = "Pengumuman Mahasiswa";
        // destroy session param
        Zend_Session::namespaceUnset('par_ancmhs');
        // navigation
        $this->_helper->navbar(0,0,'ancmhs/new',0,0);
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
        $param = new Zend_Session_Namespace('par_ancmhs');
        $param->id_announcement_kategori=$id_announcement_kategori;
        $param->tgl1=$tgl1;
        $param->tgl2=$tgl2;
    }

	function listAction(){
        // Title Browser
        $this->view->title = "Daftar Pengumuman Mahasiswa";
        // Navbar
        $this->_helper->navbar('ancmhs',0,'ancmhs/new',0,0);
        // collapse
        $this->view->sidecolaps="sidebar-collapse";
        // get param
        $param = new Zend_Session_Namespace('par_ancmhs');
        $id_announcement_kategori=$param->id_announcement_kategori;
        $tgl1=$param->tgl1;
        $tgl2=$param->tgl2;
        // get data
        $ancmhs=new AnnouncementMhs();
        $getAncmhs=$ancmhs->queryAnnouncementMhs($id_announcement_kategori,$tgl1,$tgl2);
        $this->view->listAncmhs=$getAncmhs;
    }
	
	function newAction()
	{
	    	// Title Browser
	    	$this->view->title = "Input Data Pengumuman Mahasiswa";
        	// navigation
       		$this->_helper->navbar(0,'ancmhs',0,0,0);
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
	        $ancmhs = new AnnouncementMhs();
	       $setAnnouncementMhs = $ancmhs->setAnnouncementMhs($judul,$konten,$id_kategori,$status,$date,$username_in);
	        echo $setAnnouncementMhs;
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
	    $ancmhs = new AnnouncementMhs();
	    $delAncmhs=$ancmhs->delAnnouncementMhs($id);
	    echo $delAncmhs;
	}
	
	function editAction()
	{
	    // Title Browser
	    $this->view->title = "Edit Data Pengumuman Mahasiswa";
	    // Navbar
	    $this->_helper->navbar('ancmhs/list',0,0,0,0);
	    // get data
	    $id=$this->_request->get('id');
	    $ancmhs=new AnnouncementMhs();
	    $getAncmhs=$ancmhs->getAnnouncementMhsById($id);
	    if(!$getAncmhs){
	        $this->view->eksis="f";
	    }else{
	    	 $ancmhs_Kategori= new AnnouncementKategori();
			$this->view->listAnc_Kategori=$ancmhs_Kategori->fetchAll();
	        foreach ($getAncmhs as $data){
	            $this->view->id=$data['id_announcement_mhs'];
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
	    $id_ancmhs= $request['id'];
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
	        $ancmhs = new AnnouncementMhs();
	        $updAncmhs = $ancmhs->updAnnouncementMhs($judul,$konten,$id_kategori,$status,$date,$username_ed,$id_ancmhs);
	        echo $updAncmhs;
	    }else{
	        echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
	    }
	}
	
	function detilAction()
	{
	    // Title Browser
	    $this->view->title = "Detil Data Pengumuman Mahasiswa";
	    // Navbar
	    $this->_helper->navbar('ancmhs/list',0,0,0,0);
	    // get data
	    $id=$this->_request->get('id');
	    $bb=$this->_request->get('bb');
	    $ancmhs=new AnnouncementMhs();
	    $getAncmhs=$ancmhs->getAnnouncementMhsById($id);
	    if(!$getAncmhs){
	        $this->view->eksis="f";
	    }else{
	        $ancmhs_Kategori= new AnnouncementKategori();
	        $this->view->listAnc_Kategori=$ancmhs_Kategori->fetchAll();
	        foreach ($getAncmhs as $data){
	            $this->view->id=$data['id_announcement_mhs'];
	            $this->view->judul=$data['judul'];
	            $this->view->nm_kategori=$data['announcement_kategori'];
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
		// ref
		$angkatan=new Angkatan();
		$this->view->listAkt=$angkatan->fetchAll();
		$prodi=new Prodi();
		$this->view->listProdi=$prodi->fetchAll();
		$dosen=new Dosen();
		$this->view->listDw=$dosen->getDosenWali();
		// data
		$ancAkt=new AnnouncementAngkatan();
		$getAngkatan=$ancAkt->getAnnouncementAngkatanByAnn($id);
		$this->view->listAncAkt=$getAngkatan;
		$ancPrd=new AnnouncementProdi();
		$getProdi=$ancPrd->getAnnouncementProdiByAnn($id);
		$this->view->listAncPrd=$getProdi;
		$ancDw=new AnnouncementDw();
		$getDw=$ancDw->getAnnouncementDwByAnn($id);
		$this->view->listAncDw=$getDw;
	    }
	}

	function ains2Action(){
	    // disable layout
	    $this->_helper->layout->disableLayout();
	    // start inserting
	    $request = $this->getRequest()->getPost();
	    $id = $this->_helper->string->esc_quote(trim($request['id']));
	    $akt = $request['akt'];
	    $prd = $request['prd'];
	    $dw = $request['dw'];
	    $username_in = $this->username;
	    // validation
	    $err=0;
	    $msg="";
	    $vd = new Validation();
	    if(($akt[0]=='') and ($prd[0]=='') and ($dw[0]=='')){
		$err++;
	        $msg=$msg."<strong>- Angkatan, prodi dan dosen wali tidak boleh kosong semua</strong><br>";
	    }
	    if($err==0){
	        $ancakt = new AnnouncementAngkatan();
		$msg_a="";
		foreach($akt as $inAkt){
		       	$setAnnouncementAkt = $ancakt->setAnnouncementAngkatan($id,$inAkt);
			$arrSetA=explode("|",$setAnnouncementAkt);
			$msg_a=$msg_a."<br>Data angkatan ".$inAkt." : ".$arrSetA[1];
		}
		$ancprd = new AnnouncementProdi();
		$msg_b="";
		foreach($prd as $inPrd){
		       	$setAnnouncementPrd = $ancprd->setAnnouncementProdi($id,$inPrd);
			$arrSetB=explode("|",$setAnnouncementPrd);
			$msg_b=$msg_b."<br>Data Prodi ".$inPrd." : ".$arrSetB[1];
		}
		$ancdw = new AnnouncementDw();
		$msg_c="";
		foreach($dw as $inDw){
		       	$setAnnouncementDw = $ancdw->setAnnouncementDw($id,$inDw);
			$arrSetC=explode("|",$setAnnouncementDw);
			$msg_c=$msg_c."<br>Data Dosen Wali ".$inDw." : ".$arrSetC[1];
		}
	        echo "T|".$msg_a."<br>".$msg_b."<br>".$msg_c;
	    }else{
	        echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
	    }
	}

	function adel21Action(){
	    // disabel layout
	    $this->_helper->layout->disableLayout();
	    // set database
	    $request = $this->getRequest()->getPost();
	    // gets value from ajax request
	    $param = $request['param'];
	    $id = $param[0];
	    $ancakt = new AnnouncementAngkatan();
	    $delAncAkt=$ancakt->delAnnouncementAngkatan($id);
	    echo $delAncAkt;
	}

	function adel22Action(){
	    // disabel layout
	    $this->_helper->layout->disableLayout();
	    // set database
	    $request = $this->getRequest()->getPost();
	    // gets value from ajax request
	    $param = $request['param'];
	    $id = $param[0];
	    $ancprd = new AnnouncementProdi();
	    $delAncPrd=$ancprd->delAnnouncementProdi($id);
	    echo $delAncPrd;
	}

	function adel23Action(){
	    // disabel layout
	    $this->_helper->layout->disableLayout();
	    // set database
	    $request = $this->getRequest()->getPost();
	    // gets value from ajax request
	    $param = $request['param'];
	    $id = $param[0];
	    $ancdw = new AnnouncementDw();
	    $delAncDw=$ancdw->delAnnouncementDw($id);
	    echo $delAncDw;
	}


}