<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Bayar Controller -> Controller untuk modul pembayaran
*/
class BayarController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('FormulaBiaya');
		Zend_Loader::loadClass('FormulaBiayaTA');
		Zend_Loader::loadClass('MhsBiayaPeriode');
		Zend_Loader::loadClass('Sumbangan');
		Zend_Loader::loadClass('Bayar');
		Zend_Loader::loadClass('Bank');
		Zend_Loader::loadClass('ViaBayar');
		Zend_Loader::loadClass('Term');
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
		
	}
	
	function editAction(){
		$notrans=$this->_request->get('kd');
		$bayar=new Bayar();
		$getBayar=$bayar->getBayarByNoTrans($notrans);
		if(!$getBayar){
			$this->view->eksis="f";
			$this->_helper->navbar('mhsbiaya'.$nim,0,0,0,0);
			$this->view->title="Edit Data Pembayaran";
		}else{
			$this->view->title="Edit Data Pembayaran ".$notrans;
			foreach ($getBayar as $dtBayar) {
				$nim=$dtBayar['nim'];
				$this->view->notrans=$dtBayar['no_trans'];
				$this->view->nim=$dtBayar['nim'];
				$this->view->tgl=$dtBayar['tgl_bayar_fmt'];
				$this->view->nom=$dtBayar['nominal'];
				$this->view->via=$dtBayar['id_via'];
				$this->view->bank=$dtBayar['id_bank'];
				$this->view->bukti=$dtBayar['no_bukti'];
				$this->view->term=$dtBayar['id_term'];
				$term=$dtBayar['id_term'];
				$this->view->per=$dtBayar['kd_periode'];
				$this->view->komp=$dtBayar['id_komp'];
				$this->view->stat=$dtBayar['status_bayar'];
				$statBayar=$dtBayar['status_bayar'];
				//--
				$id_akt=$dtBayar['id_angkatan'];
				$kd_prd=$dtBayar['kd_prodi'];
			}
			if($statBayar==2){
				$this->view->eksis="f";
				$this->_helper->navbar('mhsbiaya'.$nim,0,0,0,0);
				$this->view->title="Edit Data Pembayaran";
			}else{
				if($term=='2'){
					$this->view->hid_kom="hidden";
					$this->view->hid_sumb="hidden";
				}elseif ($term=='3'){
					$this->view->hid_per="hidden";
					$this->view->hid_sumb="hidden";
				}elseif ($term=='4'){
					$this->view->hid_per="hidden";
					$this->view->hid_kom="hidden";
				}else{
					$this->view->hid_kom="hidden";
					$this->view->hid_per="hidden";
					$this->view->hid_sumb="hidden";
				}
				// bank
				$bank = new Bank();
				$this->view->listBank=$bank->getBankAktif();
				// via bayar
				$via = new ViaBayar();
				$this->view->listVia=$via->fetchAll();
				// term bayar
				$term = new Term();
				$this->view->listTerm=$term->fetchAll();
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
				// maping biaya
				$arrFormulaIntval=array();
				$i=0;
				foreach ($getBiayaPeriode as $dtReg){
					foreach ($arrKomp as $dtKomp) {
						$getFormulaPeriode=$formula->getFormulaBiayaTAByPeriode($id_akt, $kd_prd, $dtKomp, $dtReg['kd_periode']);
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
			}
			// navigation
			$this->_helper->navbar('mhsbiaya/list?nim='.$nim,0,0,0,0);
		}
	}
}