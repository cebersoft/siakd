<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Mahasiswa biaya Controller -> Controller untuk modul rincian biaya per mahasiswa
*/
class MhsbiayaController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('PaketBiaya');
		Zend_Loader::loadClass('Biaya');
		Zend_Loader::loadClass('FormulaBiaya');
		Zend_Loader::loadClass('FormulaBiayaTA');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('MhsGelombang');
		Zend_Loader::loadClass('MhsRegPeriode');
		Zend_Loader::loadClass('MhsBiayaPeriode');
		Zend_Loader::loadClass('Sumbangan');
		Zend_Loader::loadClass('Konversi');
		Zend_Loader::loadClass('Bayar');
		Zend_Loader::loadClass('Bank');
		Zend_Loader::loadClass('ViaBayar');
		Zend_Loader::loadClass('Term');
		// academic
		Zend_Loader::loadClass('Kuliah');
		Zend_Loader::loadClass('KuliahTA');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		$auth = Zend_Auth::getInstance();
		$ses_fin = new Zend_Session_Namespace('ses_fin');
		if (($auth->hasIdentity())and($ses_fin->uname)) {
			$this->view->namauser =Zend_Auth::getInstance()->getIdentity()->nama;
			$this->view->kd_pt=$ses_fin->kd_pt;
			$this->view->nm_pt=$ses_fin->nm_pt;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// menu nav
		$this->view->act_detbiaya="active";
		$this->view->act_mhsbiaya="active open";
	}
	
	private function intervalSemester($periode_awal,$periode_akhir){
		$arrPerAwal=explode("/", $periode_awal);
		$arrThnAwal=explode("-", $arrPerAwal[0]);
		$thnAwal=$arrThnAwal[0];
		$smtAwal=0;
		if($arrPerAwal[1]=='GASAL'){
			$smtAwal=0.5;
		}elseif ($arrPerAwal[1]=='GENAP'){
			$smtAwal=1;
		}
		$perAwal=$thnAwal+$smtAwal;
		
		$arrPerAkhir=explode("/", $periode_akhir);
		$arrThnAkhir=explode("-", $arrPerAkhir[0]);
		$thnAkhir=$arrThnAkhir[0];
		$smtAkhir=0;
		if($arrPerAkhir[1]=='GASAL'){
			$smtAkhir=0.5;
		}elseif ($arrPerAkhir[1]=='GENAP'){
			$smtAkhir=1;
		}
		$perAkhir=$thnAkhir+$smtAkhir;
		$interval=($perAkhir-$perAwal)*2;
		return $interval;
	}

	function indexAction()
	{
		// Title Browser
		$this->view->title = "Rincian Biaya Per Mahasiswa";
		// navigation
		$this->_helper->navbar(0,0,0,0,0);
		// angkatan
		$akt=new Angkatan();
		$this->view->listAkt=$akt->fetchAll();
		// prodi
		$prodi = new Prodi();
		$this->view->listProdi = $prodi->fetchAll();
	}
	
	function listAction(){
		$nim=$this->_request->get('nim');
		// navigation
		$this->_helper->navbar('mhsbiaya',0,0,0,0);
		$mahasiswa = new Mahasiswa();
		$getMhs=$mahasiswa->getMahasiswaByNim($nim);
		if(!$getMhs){
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Daftar Biaya Periodik dan TA Mahasiswa";
		}else {
			foreach ($getMhs as $dtMhs) {
				$nm_mhs=$dtMhs['nm_mhs'];
				$this->view->nm=$nm_mhs;
				$this->view->nim=$nim;
				$this->view->akt=$dtMhs['id_angkatan'];
				$id_akt=$dtMhs['id_angkatan'];
				$kd_prd=$dtMhs['kd_prodi'];
				$this->view->nm_prd=$dtMhs['nm_prodi'];
				$this->view->stat_msk=$dtMhs['nm_stat_masuk'];
			}
			// get gelombang mahasiswa
			$mhsGel=new MhsGelombang();
			$getMhsGel=$mhsGel->getMhsGelombangByNim($nim);
			$this->view->nm_gel="";
			if($getMhsGel){
				foreach ($getMhsGel as $dtMhsGel){
					$this->view->nm_gel=$dtMhsGel['nm_gelombang'];
				}
			}
			// Title Browser
			$this->view->title = "Daftar Biaya Periodik dan TA Mahasiswa : ".$nm_mhs." (".$nim.")";
			// get data biaya periodik mahasiswa
			$mhsBiayaPer=new MhsBiayaPeriode();
			$getBiayaPeriode=$mhsBiayaPer->getMhsBiayaPeriodeByNim($nim);
			$this->view->listMhsBiayaPer=$getBiayaPeriode;
			// get data formula interval
			// get komponen formula
			$formula = new FormulaBiayaTA();
			$getFormulaTA=$formula->getFormulaBiayaTAByAktProdi($id_akt, $kd_prd);
			$arrKomp=array();
			$i=0;
			foreach ($getFormulaTA as $dtFormTA) {
				$arrKomp[$i]=$dtFormTA['id_komp'];
				$i++;
			}
			$arrKomp=array_unique($arrKomp);
			foreach ($arrKomp as $dtKomp) {
				$nFlag[$dtKomp]=0;
				$perFlag[$dtKomp]="-";
				$intvFlag[$dtKomp]=1;
			}
			// last periode registrasi
			$last_per="";
			foreach ($getBiayaPeriode as $dataPeriode){
				if($dataPeriode['kd_periode']>$last_per){
					$last_per=$dataPeriode['kd_periode'];
				}
			}
			// maping biaya
			$arrFormulaIntval=array();
			$i=0;
			foreach ($getBiayaPeriode as $dtReg){
				foreach ($arrKomp as $dtKomp) {
					$getFormulaPeriode=$formula->getFormulaBiayaTAByPeriode($id_akt, $kd_prd, $dtKomp, $last_per);					
					//$getFormulaPeriode=$formula->getFormulaBiayaTAById($id_akt, $kd_prd, $dtKomp, $kd_per_berlaku);
					foreach ($getFormulaPeriode as $dtFormulaPeriode){
						if((($dtFormulaPeriode['id_param']=='003')and($dtReg['sks_ta']>$dtFormulaPeriode['min_value']))or(($dtFormulaPeriode['id_param']=='103')and($dtReg['n_ta']>$dtFormulaPeriode['min_value']))){
							if($perFlag[$dtKomp]=="-"){ // awal
								$perFlag[$dtKomp]=$dtReg['kd_periode'];
								$intvFlag[$dtKomp]=$dtFormulaPeriode['intval_perbaruan'];
								// set array biaya
								$arrFormulaIntval[$i]['kd_periode']=$dtReg['kd_periode'];
								$arrFormulaIntval[$i]['id_komp']=$dtFormulaPeriode['id_komp'];
								$arrFormulaIntval[$i]['nm_komp']=$dtFormulaPeriode['nm_komp'];
								$arrFormulaIntval[$i]['nominal']=$dtFormulaPeriode['nominal'];
								$arrFormulaIntval[$i]['nm_paket']=$dtFormulaPeriode['nm_paket'];
								$arrFormulaIntval[$i]['nm_param']=$dtFormulaPeriode['nm_param'];
								$arrFormulaIntval[$i]['min_value']=$dtFormulaPeriode['min_value'];
								$arrFormulaIntval[$i]['kd_periode_berlaku']=$dtFormulaPeriode['kd_periode_berlaku'];
								$i++;
							}else{
								$interval=$this->intervalSemester($perFlag[$dtKomp], $dtReg['kd_periode']);
								$x=($interval%$intvFlag[$dtKomp]);
								if ($interval%$intvFlag[$dtKomp]==0){
									// ganti flag
									$perFlag[$dtKomp]=$dtReg['kd_periode'];
									$intvFlag[$dtKomp]=$dtFormulaPeriode['intval_perbaruan'];
									// set array biaya
									$arrFormulaIntval[$i]['kd_periode']=$dtReg['kd_periode'];
									$arrFormulaIntval[$i]['id_komp']=$dtFormulaPeriode['id_komp'];
									$arrFormulaIntval[$i]['nm_komp']=$dtFormulaPeriode['nm_komp'];
									$arrFormulaIntval[$i]['nominal']=$dtFormulaPeriode['nominal'];
									$arrFormulaIntval[$i]['nm_paket']=$dtFormulaPeriode['nm_paket'];
									$arrFormulaIntval[$i]['nm_param']=$dtFormulaPeriode['nm_param'];
									$arrFormulaIntval[$i]['min_value']=$dtFormulaPeriode['min_value'];
									$arrFormulaIntval[$i]['kd_periode_berlaku']=$dtFormulaPeriode['kd_periode_berlaku'];
									$i++;
								}
							}
							$nFlag[$dtKomp]=$nFlag[$dtKomp]+1;
						}
					}
				}
			}			
			$this->view->listBiayaInterval=$arrFormulaIntval;
			// get data sumbangan
			$sumb=new Sumbangan();
			$this->view->listSumbangan=$sumb->getSumbanganDtlByNim($nim);
			// get data pembayaran
			$bayar = new Bayar();
			$this->view->listBayar=$bayar->getBayarByNim($nim);
			$listBayar=$bayar->getBayarByNim($nim);
			$totBayarPer=0;
			foreach ($listBayar as $dtBayar){
				if(($dtBayar['id_term']=='1')or($dtBayar['id_term']=='2')){
					$totBayarPer=$totBayarPer+$dtBayar['nominal'];
				}
			}
			$this->view->totBayarPer=$totBayarPer;
			// bank
			$bank = new Bank();
			$this->view->listBank=$bank->getBankAktif();
			// via bayar
			$via = new ViaBayar();
			$this->view->listVia=$via->fetchAll();
			// term bayar
			$term = new Term();
			$this->view->listTerm=$term->fetchAll();
		}
	}
	
	function detilAction(){
		$nim=$this->_request->get('nim');
		$per=$this->_request->get('per');
		$mhsReg=new MhsRegPeriode();
		$getMhsReg=$mhsReg->getMhsRegPeriodeByNimPeriode($nim, $per);
		if(!$getMhsReg){
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Rincian Biaya Per Mahasiswa";
			// navigation
			$this->_helper->navbar('mhsbiaya',0,0,0,0);
		}else {
			// get biaya detil
			$mhsBiaya=new MhsBiayaPeriode();
			$getMhsBiaya=$mhsBiaya->getMhsBiayaPeriodeDetilByNimPeriode($nim, $per);
			// navigation
			$this->_helper->navbar('mhsbiaya/list?nim='.$nim,0,0,0,0);
			foreach ($getMhsReg as $dtMhsReg) {
				$nm_mhs=$dtMhsReg['nm_mhs'];
				$this->view->nm=$nm_mhs;
				$this->view->nim=$nim;
				$this->view->akt=$dtMhsReg['id_angkatan'];
				$this->view->per=$per;
				$this->view->nm_prd=$dtMhsReg['nm_prodi'];
				$this->view->stat_msk=$dtMhsReg['nm_stat_masuk'];
				$this->view->nm_gel=$dtMhsReg['nm_gelombang'];
				$this->view->stat_reg=$dtMhsReg['status_reg'];
			}
			// Title Browser
			$this->view->title = "Rincian Biaya Mahasiswa : ".$nm_mhs." (".$nim.") Periode ".$per;
			$this->view->listMhsBiayaDtl=$getMhsBiaya;
			// get data KRS
			$kuliah = new Kuliah();
			$getKuliah = $kuliah->getKuliahByNimPeriode($nim,$per);
			$this->view->listKuliah=$getKuliah;
			$kuliahTA = new KuliahTA();
			$getKuliahTA = $kuliahTA->getKuliahTAByNimPeriode($nim,$per);
			$this->view->listKuliahTA=$getKuliahTA;
		}
	}

	function ebayarAction(){
		// layout
		$this->_helper->layout()->setLayout('printout');
		$id=$this->_request->get('id');
		$bayar=new Bayar();
		$getBayar=$bayar->getBayarByNoTrans($id);
		if(!$getBayar){
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Bukti Pembayaran";
		}else {
			foreach ($getBayar as $dtBayar) {
				$this->view->id=$id;
				$nm_mhs=$dtBayar['nm_mhs'];
				$this->view->nm=$nm_mhs;
				$this->view->nim=$dtBayar['nim'];
				$this->view->akt=$dtBayar['id_angkatan'];
				$this->view->nm_prd=$dtBayar['nm_prodi'];
				$this->view->tgl=$dtBayar['tgl_bayar_fmt'];
				$this->view->nom=number_format($dtBayar['nominal'],2,',','.');
				$this->view->via=$dtBayar['via'];
				$this->view->term=$dtBayar['nm_term'];
				$this->view->terbilang=$bayar->terbilang(intval($dtBayar['nominal']));
				$now=date("d F Y");
				$this->view->time_cetak=$now;
			}
			// Title Browser
			$this->view->title = "Bukti Pembayaran Mahasiswa";
		}
	}
}