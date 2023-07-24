<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Ajax Controller -> Controller untuk submit via ajax
*/
class AjaxController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('KalenderFin');
		Zend_Loader::loadClass('Bank');
		Zend_Loader::loadClass('KompBiaya');
		Zend_Loader::loadClass('PaketBiaya');
		Zend_Loader::loadClass('Biaya');
		Zend_Loader::loadClass('FormulaBiaya');
		Zend_Loader::loadClass('FormulaBiayaTA');
		Zend_Loader::loadClass('GelombangMsk');
		Zend_Loader::loadClass('MhsGelombang');
		Zend_Loader::loadClass('Sumbangan');
		Zend_Loader::loadClass('Bayar');
		Zend_Loader::loadClass('Kompensasi');
		Zend_Loader::loadClass('PrpUjianTa');
		Zend_Loader::loadClass('UserFin');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Validation');
		$auth = Zend_Auth::getInstance();
		$ses_fin = new Zend_Session_Namespace('ses_fin');
		if (($auth->hasIdentity())and($ses_fin->uname)) {
			$this->namauser =Zend_Auth::getInstance()->getIdentity()->nama;
			$this->view->namauser =Zend_Auth::getInstance()->getIdentity()->nama;
			$this->view->kd_pt=$ses_fin->kd_pt;
			$this->view->nm_pt=$ses_fin->nm_pt;
		}else{
			echo "F|Sesi anda sudah habis. Silakan login ulang!|";
		}
		// disabel layout
		$this->_helper->layout->disableLayout();
	}
	
	function insbankAction(){
		// start inserting
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$nm = $this->_helper->string->esc_quote(trim($request['nm']));
		$rek = $this->_helper->string->esc_quote(trim($request['rek']));
		$akun = $this->_helper->string->esc_quote(trim($request['akun']));
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($nm,1,50)=='F'){
			$err++;
			$msg=$msg."<strong>- Nama Bank tidak boleh kosong dan maksimal 50 karakter</strong><br>";
		}
		if($vd->validasiLength($rek,1,50)=='F'){
			$err++;
			$msg=$msg."<strong>- Rekening Bank tidak boleh kosong dan maksimal 50 karakter</strong><br>";
		}
		if($vd->validasiLength($akun,1,50)=='F'){
			$err++;
			$msg=$msg."<strong>- Pemilik akun tidak boleh kosong dan maksimal 50 karakter</strong><br>";
		}
		if($err==0){
			// set database
			$bank=new Bank();
			$setBank=$bank->setBank($nm, $rek, $akun);
			echo $setBank;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function delbankAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
		$id = $param[0];
		$bank = new Bank();
		$delBank=$bank->delBank($id);
		echo $delBank;
	}
	
	function updbankAction(){
		// start updating
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$id = $request['id'];
		$nm = $this->_helper->string->esc_quote(trim($request['nm']));
		$rek = $this->_helper->string->esc_quote(trim($request['rek']));
		$akun = $this->_helper->string->esc_quote(trim($request['akun']));
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($nm,1,50)=='F'){
			$err++;
			$msg=$msg."<strong>- Nama Bank tidak boleh kosong dan maksimal 50 karakter</strong><br>";
		}
		if($vd->validasiLength($rek,1,50)=='F'){
			$err++;
			$msg=$msg."<strong>- Rekening Bank tidak boleh kosong dan maksimal 50 karakter</strong><br>";
		}
		if($vd->validasiLength($akun,1,50)=='F'){
			$err++;
			$msg=$msg."<strong>- Pemilik akun tidak boleh kosong dan maksimal 50 karakter</strong><br>";
		}
		if($err==0){
			// set database
			$bank=new Bank();
			$updBank=$bank->updBank($nm, $rek, $akun, $id);
			echo $updBank;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function updstatbankAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
		$id = $param[0];
		$stat = $param[1];
		$bank = new Bank();
		$updBank=$bank->updStatBank($stat, $id);
		echo $updBank;
	}
	
	function inskompbiayaAction(){
		// start updating
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$idKomp = $request['idKomp'];
		$nmKomp = $this->_helper->string->esc_quote(trim($request['nmKomp']));
		$ta=$request['ta'];
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();		
		if(($vd->validasiLength($idKomp,1,10)=='F')or($vd->validasiAlNumNoSpace($idKomp)=='F')){
			$err++;
			$msg=$msg."<strong>- Id Komponen Biaya tidak boleh kosong dan maksimal 10 karakter tanpa spasi dan karakter khusus</strong><br>";
		}
		if($vd->validasiLength($nmKomp,1,30)=='F'){
			$err++;
			$msg=$msg."<strong>- Nama Komponen Biaya tidak boleh kosong dan maksimal 30 karakter tanpa spasi dan karakter khusus</strong><br>";
		}
		if($err==0){
			// set database
			$kompBiaya=new KompBiaya();
			$setKomp=$kompBiaya->setKompBiaya($idKomp, $nmKomp, $ta);			
			echo $setKomp;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function delkompbiayaAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    	$id = $param[0];
		$kompBiaya = new KompBiaya();
		$delKomp = $kompBiaya->delKompBiaya($id);
		echo $delKomp;
	}
	
	function updkompbiayaAction(){
		// start updating
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$idKomp = $request['idKomp'];
		$oldIdKomp = $request['oldIdKomp'];
		$nmKomp = $this->_helper->string->esc_quote(trim($request['nmKomp']));
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();		
		if(($vd->validasiLength($idKomp,1,10)=='F')or($vd->validasiAlNumNoSpace($idKomp)=='F')){
			$err++;
			$msg=$msg."<strong>- Id Komponen Biaya tidak boleh kosong dan maksimal 10 karakter tanpa spasi dan karakter khusus</strong><br>";
		}
		if($vd->validasiLength($nmKomp,1,30)=='F'){
			$err++;
			$msg=$msg."<strong>- Nama Komponen Biaya tidak boleh kosong dan maksimal 30 karakter tanpa spasi dan karakter khusus</strong><br>";
		}
		if($err==0){
			// set database
			$kompBiaya=new KompBiaya();
			$updKompBiaya=$kompBiaya->updKompBiaya($idKomp, $nmKomp, $oldIdKomp);			
			echo $updKompBiaya;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function updstatkompbiayaAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    	$stat = $param[0];
    	$id = $param[1];
		$kompBiaya = new KompBiaya();
		$updKomp = $kompBiaya->updStatKompBiaya($stat, $id);
		echo $updKomp;
	}
	
	function inspktbiayaAction(){
		// start updating
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$idPkt = $request['idPkt'];
		$nmPkt = $this->_helper->string->esc_quote(trim($request['nmPkt']));
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();		
		if(($vd->validasiLength($idPkt,1,10)=='F')or($vd->validasiAlNumNoSpace($idPkt)=='F')){
			$err++;
			$msg=$msg."<strong>- Id Paket Biaya tidak boleh kosong dan maksimal 10 karakter tanpa spasi dan karakter khusus</strong><br>";
		}
		if($vd->validasiLength($nmPkt,1,30)=='F'){
			$err++;
			$msg=$msg."<strong>- Nama Paket Biaya tidak boleh kosong dan maksimal 30 karakter</strong><br>";
		}
		if($err==0){
			// set database
			$pktBiaya=new PaketBiaya();
			$setPkt=$pktBiaya->setPaketBiaya($idPkt, $nmPkt);			
			echo $setPkt;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function delpktbiayaAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    	$id = $param[0];
		$pktBiaya = new PaketBiaya();
		$delPkt = $pktBiaya->delPaketBiaya($id);
		echo $delPkt;
	}
	
	function updpktbiayaAction(){
		// start updating
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$idPkt = $request['idPkt'];
		$oldIdPkt = $request['oldIdPkt'];
		$nmPkt = $this->_helper->string->esc_quote(trim($request['nmPkt']));
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();		
		if(($vd->validasiLength($idPkt,1,10)=='F')or($vd->validasiAlNumNoSpace($idPkt)=='F')){
			$err++;
			$msg=$msg."<strong>- Id Paket Biaya tidak boleh kosong dan maksimal 10 karakter tanpa spasi dan karakter khusus</strong><br>";
		}
		if($vd->validasiLength($nmPkt,1,30)=='F'){
			$err++;
			$msg=$msg."<strong>- Nama Paket Biaya tidak boleh kosong dan maksimal 30 karakter tanpa spasi dan karakter khusus</strong><br>";
		}
		if($err==0){
			// set database
			$pktBiaya=new PaketBiaya();
			$updPkt=$pktBiaya->updPaketBiaya($idPkt, $nmPkt, $oldIdPkt);			
			echo $updPkt;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function updstatpktbiayaAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    	$stat = $param[0];
    	$id = $param[1];
		$pktBiaya = new PaketBiaya();
		$updPkt = $pktBiaya->updStatPaketBiaya($stat, $id);
		echo $updPkt;
	}
	
	function showbiayaAction(){
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$frm=$this->_request->get('frm');
		$prd = $request['prd'.$frm];
		$akt = $request['akt'.$frm];
		// set session
		$param = new Zend_Session_Namespace('param_biaya');
		$param->prd=$prd;
		$param->akt=$akt;
	}
	
	function showfbiayaAction(){
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$frm=$this->_request->get('frm');
		$prd = $request['prd'.$frm];
		$akt = $request['akt'.$frm];
		// set session
		$param = new Zend_Session_Namespace('param_fbiaya');
		$param->prd=$prd;
		$param->akt=$akt;
	}
	
	function updfbiayaAction(){
		// start inserting
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$n = $request['n'];
		$akt = $request['akt'];
		$prd = $request['prd'];
		$sm = $request['sm'];
		$gel = $request['gel'];
		$thn = $request['thn'];
		$smt = $request['smt'];
		$reg = $request['reg'];
		$komp=array();
		$urutan=array();
		for($i=1;$i<$n;$i++){
			$komp[$i]=$request['komp_'.$i];
			$urutan[$i]=intval($request['order_'.$i]);
		}
		// validation
		if(count($komp)==0){
			echo "F|Tidak ada data|";
		}else{
			$msg="";
			$i=1;
			$formula = new FormulaBiaya();
			foreach ($komp as $dtKomp){
				$updUrutan=$formula->updUrutanFormulaBiaya($akt, $prd, $sm, $gel, $dtKomp, $thn, $smt, $reg, $urutan[$i]);
				$arrMsg=explode('|', $updUrutan);
				$msg=$msg."<br>Baris ke : ".$i.":".$arrMsg[1];
				$i++;
			}
			echo "T|".$msg;
		}
	}
	
	function showbiayataAction(){
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$frm=$this->_request->get('frm');
		$prd = $request['prd'.$frm];
		$akt = $request['akt'.$frm];
		// set session
		$param = new Zend_Session_Namespace('param_biayata');
		$param->prd=$prd;
		$param->akt=$akt;
	}
	
	function insbiayaAction(){
		// start inserting
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$frm=$this->_request->get('frm');
		$akt = $request['akt_'.$frm];
		$prd = $request['prd_'.$frm];
		$sm = $request['sm_'.$frm];
		$gel = $request['gel_'.$frm];
		$kom = $request['komp_'.$frm];
		$pkt = $request['pkt_'.$frm];
		$nom = $request['nom_'.$frm];
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();		
		if($vd->validasiLength($akt,1,4)=='F'){
			$err++;
			$msg=$msg."<strong>- Angkatan tidak boleh kosong dan maksimal 4 karakter</strong><br>";
		}
		if($vd->validasiLength($prd,1,5)=='F'){
			$err++;
			$msg=$msg."<strong>- Program studi tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($sm,1,5)=='F'){
			$err++;
			$msg=$msg."<strong>- Status masuk tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($gel,1,10)=='F'){
			$err++;
			$msg=$msg."<strong>- Gelombang masuk tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($kom,1,10)=='F'){
			$err++;
			$msg=$msg."<strong>- Komponen biaya tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($pkt,1,10)=='F'){
			$err++;
			$msg=$msg."<strong>- Paket biaya tidak boleh kosong</strong><br>";
		}
		if($vd->validasiBetween($nom, 1, 100000000)=='F'){
			$err++;
			$msg=$msg."<strong>- Nominal tidak boleh 0</strong><br>";
		}
		if($err==0){
			// set database
			$biaya=new Biaya();
			$setBiaya=$biaya->setBiaya($akt, $prd, $sm, $gel, $kom, $nom, $pkt);			
			echo $setBiaya;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function delbiayaAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    	$akt = $param[0];
    	$prd = $param[1];
    	$sm = $param[2];
    	$gel = $param[3];
    	$kom = $param[4];
		$biaya = new Biaya();
		$delBiaya = $biaya->delBiaya($akt,$prd,$sm,$gel,$kom);
		echo $delBiaya;
	}
	
	function updbiayaAction(){
		// start inserting
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$akt = $request['akt'];
		$prd = $request['prd'];
		$sm = $request['sm'];
		$gel = $request['gel'];
		$kom = $request['komp'];
		$pkt = $request['pkt'];
		$nom = $request['nom'];
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();		
		if($vd->validasiLength($akt,1,4)=='F'){
			$err++;
			$msg=$msg."<strong>- Angkatan tidak boleh kosong dan maksimal 4 karakter</strong><br>";
		}
		if($vd->validasiLength($prd,1,5)=='F'){
			$err++;
			$msg=$msg."<strong>- Program studi tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($sm,1,5)=='F'){
			$err++;
			$msg=$msg."<strong>- Status masuk tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($gel,1,10)=='F'){
			$err++;
			$msg=$msg."<strong>- Gelombang masuk tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($kom,1,10)=='F'){
			$err++;
			$msg=$msg."<strong>- Komponen biaya tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($pkt,1,10)=='F'){
			$err++;
			$msg=$msg."<strong>- Paket biaya tidak boleh kosong</strong><br>";
		}
		if($vd->validasiBetween($nom, 1, 100000000)=='F'){
			$err++;
			$msg=$msg."<strong>- Nominal tidak boleh 0</strong><br>";
		}
		if($err==0){
			// set database
			$biaya=new Biaya();
			$updBiaya=$biaya->updBiaya($akt, $prd, $sm, $gel, $kom, $nom, $pkt);			
			echo $updBiaya;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function insformulabiayaAction(){
		// start inserting
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$akt = $request['akt'];
		$prd = $request['prd'];
		$sm = $request['sm'];
		$gel = $request['gel'];
		$kom = $request['komp'];
		$per = $request['per'];
		$statreg = $request['statreg'];
		$rule = $request['rule'];
		$hardnom=$request['nomfix'];
		$par=$request['par'];
		$multiply=$request['multiply'];
		$nomform=floatval($request['nomform']);
		$thn=0;
		$smt="";
		$arrPer=explode("-", $per);
		if(count($arrPer)==2){
			$thn=$arrPer[0];
			$smt=$arrPer[1];	
		}
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();		
		if($vd->validasiLength($akt,1,4)=='F'){
			$err++;
			$msg=$msg."<strong>- Angkatan tidak boleh kosong dan maksimal 4 karakter</strong><br>";
		}
		if($vd->validasiLength($prd,1,5)=='F'){
			$err++;
			$msg=$msg."<strong>- Program studi tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($sm,1,5)=='F'){
			$err++;
			$msg=$msg."<strong>- Status masuk tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($gel,1,10)=='F'){
			$err++;
			$msg=$msg."<strong>- Gelombang masuk tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($kom,1,10)=='F'){
			$err++;
			$msg=$msg."<strong>- Komponen biaya tidak boleh kosong</strong><br>";
		}
		if(($vd->validasiBetween($thn, 0, 9)=='F')or($vd->validasiLength($smt, 1, 8)=='F')){
			$err++;
			$msg=$msg."<strong>- Periode akademik tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($statreg, 1, 5)=='F'){
			$err++;
			$msg=$msg."<strong>- Status Her registrasi tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($rule, 1, 5)=='F'){
			$err++;
			$msg=$msg."<strong>- Formula biaya tidak boleh kosong</strong><br>";
		}else{
			if($rule=='1'){
				$multiply=0;
				if($vd->validasiBetween($hardnom, 1, 1000000000)=='F'){
					$err++;
					$msg=$msg."<strong>- Nominal fix tidak boleh kosong</strong><br>";
				}
			}elseif ($rule=='2'){
				$hardnom=0;
				$multiply=0;
				if($vd->validasiLength($par, 1, 10)=='F'){
					$err++;
					$msg=$msg."<strong>- Parameter biaya tidak boleh kosong</strong><br>";
				}		
			}elseif ($rule=='3'){
				$hardnom=0;
				if($vd->validasiBetween($multiply, 0.01, 1000000000)=='F'){
					$err++;
					$msg=$msg."<strong>- Bilangan pengali tidak boleh kosong</strong><br>";
				}
			}	
		}
		if($err==0){
			// set database
			$formulaBiaya=new FormulaBiaya();
			$setFormBiaya=$formulaBiaya->setFormulaBiaya($akt, $prd, $sm, $gel, $kom, $thn, $smt, $statreg, $rule, $hardnom, $par, $multiply,$nomform);			
			echo $setFormBiaya;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function delformulabiayaAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    	$akt = $param[0];
    	$prd = $param[1];
    	$sm = $param[2];
    	$gel = $param[3];
    	$kom = $param[4];
    	$thn = $param[5];
    	$smt = $param[6];
    	$statreg = $param[7];
		$formBiaya = new FormulaBiaya();
		$delFormBiaya = $formBiaya->delFormulaBiaya($akt, $prd, $sm, $gel, $kom, $thn, $smt, $statreg);
		echo $delFormBiaya;
	}
	
	function showmhsbiayaAction(){
		// makes disable layout
		$this->_helper->getHelper('layout')->disableLayout();
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$frm=$this->_request->get('frm');
		$nim = $request['nim'];
	}
	
	function insgelmasterAction(){
		// start inserting
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$id = $request['id'];
		$nm = $this->_helper->string->esc_quote(trim($request['nm']));
		$urutan = intval($request['urutan']);
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();		
		if(($vd->validasiLength($id,1,5)=='F')or($vd->validasiAlNumNoSpace($id)=='F')){
			$err++;
			$msg=$msg."<strong>- Id gelombang tidak boleh kosong dan maksimal 5 karakter tanpa spasi dan karakter khusus</strong><br>";
		}
		if($vd->validasiLength($nm,1,20)=='F'){
			$err++;
			$msg=$msg."<strong>- Keterangan gelombang masuk tidak boleh kosong dan maksimal 20 karakter</strong><br>";
		}
		if($vd->validasiBetween($urutan, 1, 10)=='F'){
			$err++;
			$msg=$msg."<strong>- Urutan gelombang tidak boleh kosong dan lebih dari 0</strong><br>";
		}
		if($err==0){
			// set database
			$gel=new GelombangMsk();
			$setGel=$gel->setGelombang($id, $nm, $urutan);			
			echo $setGel;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function delgelmasterAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    	$id = $param[0];
		$gel = new GelombangMsk();
		$delGel = $gel->delGelombang($id);
		echo $delGel;
	}
	
	function updgelmasterAction(){
		// start updating
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$oldid = $request['oldid'];
		$id = $request['id'];
		$nm = $this->_helper->string->esc_quote(trim($request['nm']));
		$urutan = intval($request['urutan']);
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();	
		if(($vd->validasiLength($id,1,5)=='F')or($vd->validasiAlNumNoSpace($id)=='F')){
			$err++;
			$msg=$msg."<strong>- Id gelombang tidak boleh kosong dan maksimal 5 karakter tanpa spasi dan karakter khusus</strong><br>";
		}
		if($vd->validasiLength($nm,1,20)=='F'){
			$err++;
			$msg=$msg."<strong>- Keterangan gelombang masuk tidak boleh kosong dan maksimal 20 karakter</strong><br>";
		}
		if($vd->validasiBetween($urutan, 1, 10)=='F'){
			$err++;
			$msg=$msg."<strong>- Urutan gelombang tidak boleh kosong dan lebih dari 0</strong><br>";
		}
		if($err==0){
			// set database
			$gel=new GelombangMsk();
			$updGel=$gel->updGelombang($id, $nm, $urutan, $oldid);			
			echo $updGel;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function showmhsgelAction(){
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$prd = $request['prd'];
		$akt = $request['akt'];
		// set session
		$param = new Zend_Session_Namespace('param_mhsgel');
		$param->prd=$prd;
		$param->akt=$akt;
	}
	
	function insgelmhsAction(){
		// start updating
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$nim = $request['nim'];
		$id_gel = $request['gel'];
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();		
		if(($vd->validasiLength($nim,1,20)=='F')){
			$err++;
			$msg=$msg."<strong>- NIM tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($id_gel,1,5)=='F'){
			$err++;
			$msg=$msg."<strong>- Gelombang masuk tidak boleh kosong</strong><br>";
		}
		if($err==0){
			// set database
			$mhsGel=new MhsGelombang();
			$setMhsGel=$mhsGel->setMhsGel($nim, $id_gel);			
			echo $setMhsGel;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function insformulabiayataAction(){
		// start inserting
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$akt = $request['akt'];
		$prd = $request['prd'];
		$kom = $request['komp'];
		$par = $request['par'];
		$minval = intval($request['minval']);
		$per = $request['per'];
		$nom = $request['nom'];
		//$intval = intval($request['intval']);
		$intval=1000;
		$pkt = $request['pkt'];
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();		
		if($vd->validasiLength($akt,1,4)=='F'){
			$err++;
			$msg=$msg."<strong>- Angkatan tidak boleh kosong dan maksimal 4 karakter</strong><br>";
		}
		if($vd->validasiLength($prd,1,5)=='F'){
			$err++;
			$msg=$msg."<strong>- Program studi tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($kom,1,10)=='F'){
			$err++;
			$msg=$msg."<strong>- Komponen biaya tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($par,1,10)=='F'){
			$err++;
			$msg=$msg."<strong>- Parameter biaya tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($per,1,20)=='F'){
			$err++;
			$msg=$msg."<strong>- Periode berlaku tidak boleh kosong</strong><br>";
		}
		if($vd->validasiBetween($nom, 1, 10000000000)=='F'){
			$err++;
			$msg=$msg."<strong>- Nominal harus lebih dari 0</strong><br>";
		}
		/*if($vd->validasiBetween($intval, 1, 9)=='F'){
			$err++;
			$msg=$msg."<strong>- Interval perbaruan harus lebih dari 0</strong><br>";
		}*/
		if($vd->validasiLength($pkt, 1, 10)=='F'){
			$err++;
			$msg=$msg."<strong>- Paket biaya tidak boleh kosong</strong><br>";
		}
		if($err==0){
			// set database
			$formulaBiayaTA=new FormulaBiayaTA();
			$setFormulaBiayaTA=$formulaBiayaTA->setFormulaBiayaTA($akt, $prd, $kom, $per, $intval, $nom, $pkt, $par, $minval);			
			echo $setFormulaBiayaTA;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function delformulabiayataAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    	$akt = $param[0];
    	$prd = $param[1];
    	$kom = $param[2];
    	$per = $param[3];
		$formBiaya = new FormulaBiayaTA();
		$delFormBiayaTA = $formBiaya->delFormulaBiayaTA($akt, $prd, $kom, $per);
		echo $delFormBiayaTA;
	}
	
	function showsumbanganAction(){
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$prd = $request['prd'];
		$akt = $request['akt'];
		// set session
		$param = new Zend_Session_Namespace('param_sumb');
		$param->prd=$prd;
		$param->akt=$akt;
	}
	
	function inssumbanganAction(){
		// start updating
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$nim = $request['nim'];
		$id_komp = $request['komp'];
		$kd_periode = $request['per'];
		$nominal = $request['nominal'];
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();		
		if(($vd->validasiLength($nim,1,20)=='F')){
			$err++;
			$msg=$msg."<strong>- NIM tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($id_komp,1,10)=='F'){
			$err++;
			$msg=$msg."<strong>- Komponen Biaya tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($kd_periode,1,20)=='F'){
			$err++;
			$msg=$msg."<strong>- Periode tidak boleh kosong</strong><br>";
		}
		if($vd->validasiBetween($nominal,1,100000000000)=='F'){
			$err++;
			$msg=$msg."<strong>- Nominal tidak boleh kosong</strong><br>";
		}
		if($err==0){
			// set database
			$sumb=new Sumbangan();
			$setSumbangan=$sumb->setSumbangan($nim, $id_komp, $kd_periode, $nominal);		
			echo $setSumbangan;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function delsumbanganAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    	$nim = $param[0];
    	$id_komp = $param[1];
    	$kd_periode = $param[2];
		$sumb = new Sumbangan();
		$delSumb = $sumb->delSumbangan($nim, $id_komp, $kd_periode);
		echo $delSumb;
	}
	
	function insbayarAction(){
		// start inserting
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$nim = $request['nim'];
		$tgl = $request['tgl'];
		$nom = $request['nominal'];
		$via = $request['via'];
		$bank = $request['bank'];
		$nobukti = $request['nobukti'];
		$term = $request['term'];
		$per=$request['per'];
		$komp=$request['komp'];
		$sumb=$request['sumb'];
		$status=1;
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();		
		if($vd->validasiLength($nim,1,50)=='F'){
			$err++;
			$msg=$msg."<strong>- NIM tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($tgl,1,100)=='F'){
			$err++;
			$msg=$msg."<strong>- Tanggal bayar tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($via,1,3)=='F'){
			$err++;
			$msg=$msg."<strong>- Via bayar tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($bank,1,10)=='F'){
			$err++;
			$msg=$msg."<strong>- Bank tujuan tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($nobukti,1,20)=='F'){
			$err++;
			$msg=$msg."<strong>- Nomor bukti tidak boleh kosong</strong><br>";
		}
		if($vd->validasiBetween($nom, 1, 1000000000)=='F'){
			$err++;
			$msg=$msg."<strong>- Jumlah bayar harus lebih dari 0 dan kurang dari 1,000,000,000</strong><br>";
		}
		if($vd->validasiLength($term, 1, 10)=='F'){
			$err++;
			$msg=$msg."<strong>- Term pembayaran tidak boleh kosong</strong><br>";
		}else{
			if($term=='2'){
				$komp="";
				if($vd->validasiLength($per,1,20)=='F'){
					$err++;
					$msg=$msg."<strong>- Periode akademik tidak boleh kosong</strong><br>";
				}
			}elseif ($term=='3'){
				if($vd->validasiLength($komp, 1, 50)=='F'){
					$err++;
					$msg=$msg."<strong>- Komponen biaya interval tidak boleh kosong</strong><br>";
				}else{
					$arrKompIntv=explode("||", $komp);
					$per=$arrKompIntv[0];
					$komp=$arrKompIntv[1];
				}		
			}elseif ($term=='4'){
				if($vd->validasiLength($sumb, 1, 50)=='F'){
					$err++;
					$msg=$msg."<strong>- Komponen biaya sumbangan tidak boleh kosong</strong><br>";
				}else{
					$arrKompSumb=explode("||", $sumb);
					$per=$arrKompSumb[0];
					$komp=$arrKompSumb[1];
				}	
			}else{
				$per="";
				$komp="";
			}
		}
		if($err==0){
			// set database
			$bayar=new Bayar();
			$setBayar=$bayar->setBayar($nim, $tgl, $nom, $via, $bank, $nobukti, $term, $per, $komp, $status);			
			echo $setBayar;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function delbayarAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    	$no_trans = $param[0];
		$bayar = new Bayar();
		$delBayar = $bayar->delBayar($no_trans);
		echo $delBayar;
	}
	
	function updbayarAction(){
		// start inserting
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$notrans = $request['notrans'];
		$nim = $request['nim'];
		$tgl = $request['tgl'];
		$nom = $request['nominal'];
		$via = $request['via'];
		$bank = $request['bank'];
		$nobukti = $request['nobukti'];
		$term = $request['term'];
		$per=$request['per'];
		$komp=$request['komp'];
		$sumb=$request['sumb'];
		$status=1;
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($notrans,1,100)=='F'){
			$err++;
			$msg=$msg."<strong>- Nomor transaski tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($nim,1,50)=='F'){
			$err++;
			$msg=$msg."<strong>- NIM tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($tgl,1,100)=='F'){
			$err++;
			$msg=$msg."<strong>- Tanggal bayar tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($via,1,3)=='F'){
			$err++;
			$msg=$msg."<strong>- Via bayar tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($bank,1,10)=='F'){
			$err++;
			$msg=$msg."<strong>- Bank tujuan tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($nobukti,1,20)=='F'){
			$err++;
			$msg=$msg."<strong>- Nomor bukti tidak boleh kosong</strong><br>";
		}
		if($vd->validasiBetween($nom, 1, 1000000000)=='F'){
			$err++;
			$msg=$msg."<strong>- Jumlah bayar harus lebih dari 0 dan kurang dari 1,000,000,000</strong><br>";
		}
		if($vd->validasiLength($term, 1, 10)=='F'){
			$err++;
			$msg=$msg."<strong>- Term pembayaran tidak boleh kosong</strong><br>";
		}else{
			if($term=='2'){
				$komp="";
				if($vd->validasiLength($per,1,20)=='F'){
					$err++;
					$msg=$msg."<strong>- Periode akademik tidak boleh kosong</strong><br>";
				}
			}elseif ($term=='3'){
				if($vd->validasiLength($komp, 1, 50)=='F'){
					$err++;
					$msg=$msg."<strong>- Komponen biaya interval tidak boleh kosong</strong><br>";
				}else{
					$arrKompIntv=explode("||", $komp);
					$per=$arrKompIntv[0];
					$komp=$arrKompIntv[1];
				}		
			}elseif ($term=='4'){
				if($vd->validasiLength($sumb, 1, 50)=='F'){
					$err++;
					$msg=$msg."<strong>- Komponen biaya sumbangan tidak boleh kosong</strong><br>";
				}else{
					$arrKompSumb=explode("||", $sumb);
					$per=$arrKompSumb[0];
					$komp=$arrKompSumb[1];
				}	
			}else{
				$per="";
				$komp="";
			}
		}
		if($err==0){
			// set database
			$bayar=new Bayar();
			$updBayar=$bayar->updBayar($nim, $tgl, $nom, $via, $bank, $nobukti, $term, $per, $komp, $status, $notrans);			
			echo $updBayar;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function realocateAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    	$nim = $param[0];
		$bayar = new Bayar();
		$realocateBayar = $bayar->realocateBayar($nim);
		if($realocateBayar=='T'){
			$realocateBayar="T|Data pembayaran NIM ".$nim." berhasil direalokasi";
		}else{
			$realocateBayar="F|Data pembayaran NIM ".$nim." gagal direalokasi";
		}
		echo $realocateBayar;
	}
	
	function inskompensasiAction(){
		// start inserting
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$nim = $request['nim'];
		$per = $request['per'];
		$komp = $request['komp'];
		$rule = $request['rule'];
		$hardnom=$request['nomfix'];
		$par=$request['par'];
		$dedpar=$request['dedpar'];
		$multiply=$request['multiply'];
		$ket=$this->_helper->string->esc_quote($request['ket']);
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();		
		if($vd->validasiLength($nim,1,30)=='F'){
			$err++;
			$msg=$msg."<strong>- NIM tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($per,1,20)=='F'){
			$err++;
			$msg=$msg."<strong>- Periode akademik tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($komp,1,10)=='F'){
			$err++;
			$msg=$msg."<strong>- Komponen Biaya tidak boleh kosong</strong><br>";
		}
		
		if($vd->validasiLength($ket,1,100)=='F'){
			$err++;
			$msg=$msg."<strong>- Keterangan tidak boleh kosong max 100 karakter</strong><br>";
		}
		
		if($vd->validasiLength($rule, 1, 5)=='F'){
			$err++;
			$msg=$msg."<strong>- Formula pengurangan biaya tidak boleh kosong</strong><br>";
		}else{
			if($rule=='1'){
				$multiply=0;
				$dedpar=0;
				if($vd->validasiBetween($hardnom, -1000000000, 1000000000)=='F'){
					$err++;
					$msg=$msg."<strong>- Nominal fix tidak boleh kosong</strong><br>";
				}
			}elseif ($rule=='2'){
				$hardnom=0;
				$multiply=0;
				if($vd->validasiLength($par, 1, 10)=='F'){
					$err++;
					$msg=$msg."<strong>- Parameter biaya tidak boleh kosong</strong><br>";
				}
				if($vd->validasiBetween($dedpar, 1, 10)=='F'){
					$err++;
					$msg=$msg."<strong>- Pengurangan parameter harus lebih dari 0 dan kurang dari 10</strong><br>";
				}
			}elseif ($rule=='3'){
				$hardnom=0;
				$dedpar=0;
				if($vd->validasiBetween($multiply, 0.01, 1000000000)=='F'){
					$err++;
					$msg=$msg."<strong>- Bilangan pengali tidak boleh kosong</strong><br>";
				}
			}	
		}
		if($err==0){
			// set database
			$kompensasi=new Kompensasi();
			$setkompensasi=$kompensasi->setKompensasiBiaya($nim, $per, $komp, $rule, $hardnom, $par, $dedpar, $multiply, $ket);			
			echo $setkompensasi;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function delkompensasiAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    		$nim = $param[0];
    		$kd_periode = $param[1];
    		$id_komp = $param[2];
		$kompensasi = new Kompensasi();
		$delKompensasi = $kompensasi->delKompensasiBiaya($nim, $kd_periode, $id_komp);
		echo $delKompensasi;
	}
	
	function showreportAction(){
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$type = $this->getRequest()->get('type');
		$prd = $request['prd'];
		$akt = $request['akt'];
		$per = $request['per'];
		// set session
		$param = new Zend_Session_Namespace('param_report1');
		$param->prd=$prd;
		$param->akt=$akt;
		$param->per=$per;
	}
	
	function showreport2Action(){
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$prd = $request['prd'];
		$akt = $request['akt'];
		$per = $request['per'];
		// set session
		$param = new Zend_Session_Namespace('param_report2');
		$param->prd=$prd;
		$param->akt=$akt;
		$param->per=$per;
	}
	
	function showreport5Action(){
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$prd = $request['prd'];
		$akt = $request['akt'];
		$tgl1 = $request['tgl1'];
		$tgl2 = $request['tgl2'];
		// set session
		$param = new Zend_Session_Namespace('param_report5');
		$param->prd=$prd;
		$param->akt=$akt;
		$param->tgl1=$tgl1;
		$param->tgl2=$tgl2;
	}
	
	// User
	function insuserAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$uname = trim($request['uname']);
		$nm = trim($request['nm']);
		$pwd1 = trim($request['pwd1']);
		$pwd2 = trim($request['pwd2']);
		$superadm = trim($request['superadm']);
		$email = trim($request['email']);
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($uname,1,25)=='F'){
			$err++;
			$msg=$msg."<strong>- Username tidak boleh kosong</strong><br>";
		}
		if($vd->validasiAlNumNoSpace($uname)=='F'){
			$err++;
			$msg=$msg."<strong>- Username hanya boleh mengandung huruf dan angka tanpa spasi</strong><br>";
		}
		if($vd->validasiLength($nm,1,40)=='F'){
			$err++;
			$msg=$msg."<strong>- Nama tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($pwd1,1,30)=='F'){
			$err++;
			$msg=$msg."<strong>- Password tidak boleh kosong</strong><br>";
		}
		if(md5($pwd1)!=md5($pwd2)){
			$err++;
			$msg=$msg."<strong>- Password tidak sama</strong><br>";
		}
		if($err==0){
			// set database
			$user_fin = new UserFin();
			$setUserFin = $user_fin->setUserFin($uname, $pwd1, $nm, $superadm, $email);
			echo $setUserFin;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function deluserAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
		$username = $param[0];
		$user_fin = new UserFin();
		$delUserFin = $user_fin->delUserFin($username);
		echo $delUserFin;
	}
	
	function upduserAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$uname = trim($request['uname']);
		$nm = trim($request['nm']);
		$email = trim($request['email']);
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($uname,1,25)=='F'){
			$err++;
			$msg=$msg."<strong>- Username tidak boleh kosong</strong><br>";
		}
		if($vd->validasiAlNum($uname)=='F'){
			$err++;
			$msg=$msg."<strong>- Username hanya boleh mengandung huruf dan angka</strong><br>";
		}
		if($vd->validasiLength($nm,1,40)=='F'){
			$err++;
			$msg=$msg."<strong>- Nama tidak boleh kosong</strong><br>";
		}
		if($err==0){
			// set database
			$user_fin = new UserFin();
			$updUserFin = $user_fin->updUserFin($uname, $nm, $email);
			echo $updUserFin;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function upduserpwdAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$uname = trim($request['uname']);
		$pwd1 = trim($request['pwd1']);
		$pwd2 = trim($request['pwd2']);
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($uname,1,25)=='F'){
			$err++;
			$msg=$msg."<strong>- Username tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($pwd1,1,30)=='F'){
			$err++;
			$msg=$msg."<strong>- Password tidak boleh kosong</strong><br>";
		}
		if(md5($pwd1)!=md5($pwd2)){
			$err++;
			$msg=$msg."<strong>- Password tidak sama</strong><br>";
		}
		if($err==0){
			// set database
			$user_fin = new UserFin();
			$updPasswordUserFin = $user_fin->updPasswordUserFin($uname, $pwd1);
			echo $updPasswordUserFin;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
		
	function updprofilAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$uname = trim($request['uname']);
		$nm = trim($request['nm']);
		$email = trim($request['email']);
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($uname,1,25)=='F'){
			$err++;
			$msg=$msg."<strong>- Username tidak boleh kosong</strong><br>";
		}
		if($vd->validasiAlNum($uname)=='F'){
			$err++;
			$msg=$msg."<strong>- Username hanya boleh mengandung huruf dan angka</strong><br>";
		}
		if($vd->validasiLength($nm,1,40)=='F'){
			$err++;
			$msg=$msg."<strong>- Nama tidak boleh kosong</strong><br>";
		}
		if($err==0){
			// set database
			$user_fin = new UserFin();
			$updUserFin = $user_fin->updUserFin($uname, $nm, $email);
			echo $updUserFin;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}
	
	function updprofilpwdAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$uname = trim($request['uname']);
		$oldpwd = trim($request['oldpwd']);
		$pwd0 = trim($request['pwd0']);
		$pwd1 = trim($request['pwd1']);
		$pwd2 = trim($request['pwd2']);
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($uname,1,25)=='F'){
			$err++;
			$msg=$msg."<strong>- Username tidak boleh kosong</strong><br>";
		}
		if(md5($pwd0)!=$oldpwd){
			$err++;
			$msg=$msg."<strong>- Password lama tidak benar</strong><br>";
		}
		if($vd->validasiLength($pwd1,1,30)=='F'){
			$err++;
			$msg=$msg."<strong>- Password tidak boleh kosong</strong><br>";
		}
		if(md5($pwd1)!=md5($pwd2)){
			$err++;
			$msg=$msg."<strong>- Password tidak sama</strong><br>";
		}
		if($err==0){
			// set database
			$user_fin = new UserFin();
			$updPasswordUserFin = $user_fin->updPasswordUserFin($uname, $pwd1);
			echo $updPasswordUserFin;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}

	// kalender
	function inskalAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$kd_periode = $request['per'];
		$startdate = $request['tgl1'];
		$enddate = trim($request['tgl2']);
		$aktFin = trim($request['aktFin']);
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($kd_periode,1,30)=='F'){
			$err++;
			$msg=$msg."<strong>- Periode akademik tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($aktFin,1,20)=='F'){
			$err++;
			$msg=$msg."<strong>- Agenda keuangan tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($startdate,1,20)=='F'){
			$err++;
			$msg=$msg."<strong>- Tanggal mulai tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength($enddate,1,20)=='F'){
			$err++;
			$msg=$msg."<strong>- Tanggal selesai tidak boleh kosong</strong><br>";
		}
		if($err==0){
			// set database
			$kalender = new KalenderFin();
			$setKalenderFin = $kalender->setKalenderFin($kd_periode, $aktFin, $startdate, $enddate);
			echo $setKalenderFin;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}

	function delkalAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
	    	$kd_periode = $param[0];
	    	$kd_aktivitas = $param[1];
		$kalender = new KalenderFin();
		$delKalenderFin = $kalender->delKalenderFin($kd_periode, $kd_aktivitas);
		echo $delKalenderFin;
	}

	function showpraregisterAction(){
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$prd = $request['prd'];
		$akt = $request['akt'];
		$per = $request['per'];
		// set session
		$param = new Zend_Session_Namespace('param_praregister');
		$param->prd=$prd;
		$param->akt=$akt;
		$param->per=$per;
	}
	
	function showverifytaAction(){
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$frm=$this->_request->get('frm');
		$prd = $request['prd'.$frm];
		$per = $request['per'.$frm];
		// set session
		$param = new Zend_Session_Namespace('param_verifyta');
		$param->prd=$prd;
		$param->per=$per;
	}

	function verifytaAction(){
		// start inserting
		$request = $this->getRequest()->getPost();
		$id_prp1 = trim($request['id_prp_o']);
		$stat = $request['stat_o'];
		$note = $request['ket_o'];
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($id_prp1,1,200)=='F'){
			$err++;
			$msg=$msg."<strong>- ID tidak boleh kosong</strong><br>";
		}
		if($err==0){
			// set database
			$prp = new PrpUjianTa();
			$updPrp =$prp->updStatusPrpApproverPemb($id_prp1,$stat,$this->namauser,$note);
			echo $updPrp;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}

	function verifytacancelAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
		$id = $param[0];
		$prp = new PrpUjianTa();
		$updPrp = $prp->updStatusPrpApproverPemb($id,0,$this->namauser,'');
		echo $updPrp;
	}
}
