<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: LMS Controller -> Controller untuk modul LMS
*/
class LmsController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Nilai');
		// Zend_Loader::loadClass('Praktikum');
		Zend_Loader::loadClass('Register');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Paketkelas');
		// Zend_Loader::loadClass('KelompokPraktikum');
		Zend_Loader::loadClass('PaketkelasTA');
		Zend_Loader::loadClass('Kelas');
		Zend_Loader::loadClass('PaketkelasLms');
		Zend_Loader::loadClass('KelompokLms');
		Zend_Loader::loadClass('BahanAjar');
		Zend_Loader::loadClass('Tugas');
		Zend_Loader::loadClass('TugasMhs');
		Zend_Loader::loadClass('Diskusi');
		Zend_Loader::loadClass('DiskusiMhs');
		Zend_Loader::loadClass('Quiz');
		Zend_Loader::loadClass('QuizMhs');
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		$auth = Zend_Auth::getInstance();
		$ses_std = new Zend_Session_Namespace('ses_std');
		if (($auth->hasIdentity())and($ses_std->uname)) {
			$this->view->namamhs =Zend_Auth::getInstance()->getIdentity()->nm_mhs;
			$this->view->nim=Zend_Auth::getInstance()->getIdentity()->nim;
			$this->view->idmhs=Zend_Auth::getInstance()->getIdentity()->id_mhs;
			$this->view->kd_pt=$ses_std->kd_pt;
			$this->view->nm_pt=$ses_std->nm_pt;
			// global var
			$this->uname=Zend_Auth::getInstance()->getIdentity()->nim;
			$this->id=Zend_Auth::getInstance()->getIdentity()->id_mhs;
			$this->nm_pt=$ses_std->nm_pt;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// nav menu
		$this->view->lms_act="active";
	}

	function indexAction()
	{
		// get param
		$kd_periode=$this->_request->get('id');
		$periode = new Periode();
		if(!$kd_periode){
			// get periode aktif
			$getPeriodeAktif=$periode->getPeriodeByStatus(0);
			if($getPeriodeAktif){
				foreach ($getPeriodeAktif as $dtPeriode) {
					$kd_periode=$dtPeriode['kd_periode'];
				}
			}else{
				$kd_periode="";
			}
		}
		// periode akademik
		$getPeriode = $periode->getPeriodeByKd($kd_periode);
		// navigation
		$this->_helper->navbar(0,0);
		if($getPeriode){
			// Title Browser
			$this->view->title = "Online Learning ".$kd_periode;
			$nim=$this->uname;
			// get data krs approved/nilai
			$nilai = new Nilai();
			$getKuliahApp=$nilai->getNilaiByNimPeriode($nim, $kd_periode);
			// get periode register mhs
			$register=new Register();
			$getRegister=$register->getRegisterByNim($nim);
			$arrPer=array();
			if($getRegister){
				$i=0;
				foreach ($getRegister as $dtRegister){
					$arrPer[$i]['kd_periode']=$dtRegister['kd_periode'];
					$i++;
				}
			}
			$this->view->per=$kd_periode;
			$this->view->listPer=$arrPer;
			// lms
			$arrKuliah=array();
			$paketKelas=new PaketkelasLms();
			$i=0;
			foreach($getKuliahApp as $dtKul){
				$arrKuliah[$i]['kd_kuliah']=$dtKul['kd_kuliah'];
				$arrKuliah[$i]['kode_mk']=$dtKul['kode_mk'];
				$arrKuliah[$i]['nm_mk']=$dtKul['nm_mk'];
				$arrKuliah[$i]['nm_dosen']=$dtKul['nm_dosen'];
				$arrKuliah[$i]['nm_kelas']=$dtKul['nm_kelas'];
				$arrKuliah[$i]['a_teori']=$dtKul['a_teori'];
				$arrKuliah[$i]['a_ta']=$dtKul['a_ta'];
				$getPaketKelas=$paketKelas->getPaketKelasLmsById($dtKul['kd_paket_kelas']);
				foreach ($getPaketKelas as $dtPaket){
					$arrKuliah[$i]['n_bahan_ajar']=$dtPaket['n_bahan_ajar'];
					$arrKuliah[$i]['n_tugas']=$dtPaket['n_tugas'];
					$arrKuliah[$i]['n_diskusi']=$dtPaket['n_diskusi'];
					$arrKuliah[$i]['n_quiz']=$dtPaket['n_quiz'];
				}
				$i++;
			}
			$this->view->listKuliah=$arrKuliah;
			// praktikum
			// $praktikum = new Praktikum();
			// $getPraktikum=$praktikum->getPraktikumByNimPeriode($nim, $kd_periode);
			// -- kelompok
			// $kelompok=new KelompokLms();
			// $j=0;
			// $arrPraktikum=array();
			/*
			foreach($getPraktikum as $dtPrk){
				$arrPraktikum[$j]['kd_kuliah']=$dtPrk['kd_kuliah'];
				$arrPraktikum[$j]['kode_mk']=$dtPrk['kode_mk'];
				$arrPraktikum[$j]['nm_mk']=$dtPrk['nm_mk'];
				$arrPraktikum[$j]['nm_dosen']=$dtPrk['nm_dosen'];
				$arrPraktikum[$j]['nm_kelompok']=$dtPrk['nm_kelompok'];
				$arrPraktikum[$j]['a_teori']=$dtPrk['a_teori'];
				$arrPraktikum[$j]['a_ta']=$dtPrk['a_ta'];
				$getKelompok=$kelompok->getKelompokLmsById($dtPrk['id_kelompok']);
				foreach ($getKelompok as $dtKel){
					$arrPraktikum[$j]['n_bahan_ajar']=$dtKel['n_bahan_ajar'];
					$arrPraktikum[$j]['n_tugas']=$dtKel['n_tugas'];
					$arrPraktikum[$j]['n_diskusi']=$dtKel['n_diskusi'];
					$arrPraktikum[$j]['n_quiz']=$dtKel['n_tugas'];
				}
				$j++;
			}
			$this->view->listPraktikum=$arrPraktikum;
			*/
			$this->view->listPraktikum=array();
		}else{
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "Online Learning";	
		}
	}

	function materiAction(){
		// get param
		$kd_kuliah=$this->_request->get('id');
		$kuliah = new Nilai();
		$getKuliah=$kuliah->getNilaiByKd($kd_kuliah);
		if(!$getKuliah){
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "LMS";
			// navigation
			$this->_helper->navbar('lms',0);
		}else{
			$per="";
			$kd_kelas="";
			foreach($getKuliah as $dtKul){
				$per=$dtKul['kd_periode'];
				$kd_kelas=$dtKul['kd_kelas'];
			}
			// navigation
			$this->_helper->navbar('lms/index?id='.$per,0);
			// get data kelas dan paket kelas
			$kelas = new Kelas();
			$getKelas = $kelas->getKelasByKd($kd_kelas);
			if($getKelas){
				foreach ($getKelas as $data_kls) {
					$this->view->kd_kls=$data_kls['kd_kelas'];
					$this->view->kd_dsn=$data_kls['kd_dosen'];
					$kd_dsn=$data_kls['kd_dosen'];
					$this->view->nm_dsn=$data_kls['nm_dosen'];
					$this->view->kd_dsn=$data_kls['kd_dosen'];
					$id_mk_kur=$data_kls['id_mk_kurikulum'];
					$this->view->kd_mk=$data_kls['kode_mk'];
					$this->view->nm_mk=$data_kls['nm_mk'];
					$this->view->kd_per=$data_kls['kd_periode'];
					$this->view->nm_prodi=$data_kls['nm_prodi_kur'];
					$this->view->jns_kelas=$data_kls['jns_kelas'];
					$this->view->sks=($data_kls['sks_tm']+$data_kls['sks_prak']+$data_kls['sks_prak_lap']+$data_kls['sks_sim']);
					$nm_mk=$data_kls['nm_mk'];
					$nm_dsn=$data_kls['nm_dosen'];
					$nm_prodi=$data_kls['nm_prodi_kur'];	
					$bahan = new BahanAjar();
					$getBahan = $bahan->getBahanAjarByKelas($kd_kelas);
					$this->view->listBahan = $getBahan;
				}
				// title
				$this->view->title="Daftar Materi Ajar ".$nm_mk;
			}else{
				$this->view->eksis="f";
				// title
				$this->view->title="Daftar Materi Ajar";
			}	
		}
	}

	function tugasAction()
	{
		// get param
		$kd_kuliah=$this->_request->get('id');
		$a=$this->_request->get('a');
		$this->view->a=$a;
		$this->view->kd_kuliah=$kd_kuliah;
		$kuliah = new Nilai();
		$getKuliah=$kuliah->getNilaiByKd($kd_kuliah);
		if(!$getKuliah){
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "LMS";
			// navigation
			$this->_helper->navbar('lms',0);
		}else{
			if($a=='p'){
				$per="";
				$kd_paket="";
				foreach($getKuliah as $dtKul){
					$per=$dtKul['kd_periode'];
					$kd_paket=$dtKul['kd_paket_kelas'];
				}
				// Title Browser
				$this->view->title = "Daftar Tugas";
				// get kd paket kelas
				$paketkelas = new Paketkelas();
				$getPaketKelas=$paketkelas->getPaketKelasByKd($kd_paket);
				if($getPaketKelas){
					foreach ($getPaketKelas as $dtPaket) {
						$per=$dtPaket['kd_periode'];
						$this->view->kd_paket_kelas = $dtPaket['kd_paket_kelas'];
						$kdKelas = $dtPaket['kd_kelas'];
						$this->view->kd_kelas = $dtPaket['kd_kelas'];
						$this->view->kd_paket_kelas = $dtPaket['kd_paket_kelas'];
						$this->view->nm_prodi=$dtPaket['nm_prodi_kur'];
						$this->view->kd_per=$dtPaket['kd_periode'];
						$this->view->nm=$dtPaket['nm_kelas'];
						$this->view->jns_kelas=$dtPaket['jns_kelas'];
						$this->view->nm_dsn=$dtPaket['nm_dosen'];
						$this->view->kd_dsn=$dtPaket['kd_dosen'];
						$kd_dsn=$dtPaket['kd_dosen'];
						$this->view->nm_mk=$dtPaket['nm_mk'];
						$this->view->kd_mk=$dtPaket['kode_mk'];
						$this->view->sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
						$id_mk_kur=$dtPaket['id_mk_kurikulum'];
					}
					// data tugas
					$tugas=new Tugas();
					$getTugas=$tugas->getTugasByPaket($kd_paket);
					$tugasMhs=new TugasMhs();
					$arrTugas=array();
					$i=0;
					foreach($getTugas as $dtTugas){
						$arrTugas[$i]['id_tugas']=$dtTugas['id_tugas'];
						$arrTugas[$i]['kd_paket_kelas']=$dtTugas['kd_paket_kelas'];
						$arrTugas[$i]['judul_tugas']=$dtTugas['judul_tugas'];
						$arrTugas[$i]['konten_tugas']=$dtTugas['konten_tugas'];
						$arrTugas[$i]['created']=$dtTugas['created'];
						$arrTugas[$i]['created_fmt']=$dtTugas['created_fmt'];
						$arrTugas[$i]['published_fmt']=$dtTugas['published_fmt'];
						$arrTugas[$i]['expired_fmt']=$dtTugas['expired_fmt'];
						$arrTugas[$i]['kd_dosen_created']=$dtTugas['kd_dosen_created'];
						$arrTugas[$i]['param_nilai']=$dtTugas['param_nilai'];
						$arrTugas[$i]['nm_dosen']=$dtTugas['nm_dosen'];
						$arrTugas[$i]['id_rps_detil']=$dtTugas['id_rps_detil'];
						$arrTugas[$i]['nm_file']=$dtTugas['nm_file'];
						$arrTugas[$i]['link']=$dtTugas['link'];
						$arrTugas[$i]['id_rps']=$dtTugas['id_rps'];
						$arrTugas[$i]['minggu']=$dtTugas['minggu'];
						$arrTugas[$i]['status_tugas']=$dtTugas['status_tugas'];
						// tugas mhs
						$getTugasMhs=$tugasMhs->getTugasMhsByTugasKuliah($dtTugas['id_tugas'],$kd_kuliah);
						if(!$getTugasMhs){
							$arrTugas[$i]['done']="f";
							$arrTugas[$i]['status_done']="Belum Dikerjakan";
							$arrTugas[$i]['warna']="danger";
							$arrTugas[$i]['nilai']="-";
						}else{
							$arrTugas[$i]['done']="t";
							$arrTugas[$i]['status_done']="Sudah Dikerjakan";
							$arrTugas[$i]['warna']="success";
							foreach ($getTugasMhs as $dtTgsMhs){
								$arrTugas[$i]['nilai']=$dtTgsMhs['nilai'];	
							}
						}
						$i++;
					}
					$this->view->listTugas=$arrTugas;
					// navigation
					$this->_helper->navbar('lms/index?id='.$per,0);
				}else{
					$this->view->eksis="f";
					// navigation
					$this->_helper->navbar('lms',0);
				}
			}else{
				$praktikum = new Praktikum();
				$getPraktikum=$praktikum->getPraktikumByKd($kd_kuliah);
				$per="";
				$id_kel="";
				foreach($getPraktikum as $dtKul){
					$per=$dtKul['kd_periode'];
					$id_kel=$dtKul['id_kelompok'];
				}
				// Title Browser
				$this->view->title = "Daftar Tugas";
				// get kelompok
				$kelompok = new KelompokPraktikum();
				$getKelompok=$kelompok->getKelompokPraktikumByKd($id_kel);
				if($getKelompok){
					foreach ($getKelompok as $dtPaket) {
						$per=$dtPaket['kd_periode'];
						$this->view->id_kelompok = $dtPaket['id_kelompok'];
						$kdKelas = $dtPaket['kd_kelas'];
						$this->view->kd_kelas = $dtPaket['kd_kelas'];
						$this->view->nm_prodi=$dtPaket['nm_prodi_kur'];
						$this->view->kd_per=$dtPaket['kd_periode'];
						$this->view->nm=$dtPaket['nm_kelompok'];
						$this->view->jns_kelas=$dtPaket['jns_kelas'];
						$this->view->nm_dsn=$dtPaket['nm_dosen'];
						$this->view->kd_dsn=$dtPaket['kd_dosen'];
						$kd_dsn=$dtPaket['kd_dosen'];
						$this->view->nm_mk=$dtPaket['nm_mk'];
						$this->view->kd_mk=$dtPaket['kode_mk'];
						$this->view->sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
						$id_mk_kur=$dtPaket['id_mk_kurikulum'];
					}
					// data tugas
					$tugas=new Tugas();
					$getTugas=$tugas->getTugasByKelompok($id_kel);
					$tugasMhs=new TugasMhs();
					$arrTugas=array();
					$i=0;
					foreach($getTugas as $dtTugas){
						$arrTugas[$i]['id_tugas']=$dtTugas['id_tugas'];
						$arrTugas[$i]['id_kelompok']=$dtTugas['id_kelompok'];
						$arrTugas[$i]['judul_tugas']=$dtTugas['judul_tugas'];
						$arrTugas[$i]['konten_tugas']=$dtTugas['konten_tugas'];
						$arrTugas[$i]['created']=$dtTugas['created'];
						$arrTugas[$i]['created_fmt']=$dtTugas['created_fmt'];
						$arrTugas[$i]['published_fmt']=$dtTugas['published_fmt'];
						$arrTugas[$i]['expired_fmt']=$dtTugas['expired_fmt'];
						$arrTugas[$i]['kd_dosen_created']=$dtTugas['kd_dosen_created'];
						$arrTugas[$i]['param_nilai']=$dtTugas['param_nilai'];
						$arrTugas[$i]['nm_dosen']=$dtTugas['nm_dosen'];
						$arrTugas[$i]['id_rps_detil']=$dtTugas['id_rps_detil'];
						$arrTugas[$i]['nm_file']=$dtTugas['nm_file'];
						$arrTugas[$i]['link']=$dtTugas['link'];
						$arrTugas[$i]['id_rps']=$dtTugas['id_rps'];
						$arrTugas[$i]['minggu']=$dtTugas['minggu'];
						$arrTugas[$i]['status_tugas']=$dtTugas['status_tugas'];
						// tugas mhs
						$getTugasMhs=$tugasMhs->getTugasMhsByTugasKuliah($dtTugas['id_tugas'],$kd_kuliah);
						if(!$getTugasMhs){
							$arrTugas[$i]['done']="f";
							$arrTugas[$i]['status_done']="Belum Dikerjakan";
							$arrTugas[$i]['warna']="danger";
							$arrTugas[$i]['nilai']="-";
						}else{
							$arrTugas[$i]['done']="t";
							$arrTugas[$i]['status_done']="Sudah Dikerjakan";
							$arrTugas[$i]['warna']="success";
							foreach ($getTugasMhs as $dtTgsMhs){
								$arrTugas[$i]['nilai']=$dtTgsMhs['nilai'];	
							}
						}
						$i++;
					}
					$this->view->listTugas=$arrTugas;
					// navigation
					$this->_helper->navbar('lms/index?id='.$per,0);
				}else{
					$this->view->eksis="f";
					// navigation
					$this->_helper->navbar('lms',0);
				}
			}
		}
	}

	function tugasdetilAction()
	{
		// Title Browser
		$this->view->title = "Daftar Pengerjaan Tugas";
		// get id tugas
		$id_tugas=$this->_request->get('id');
		$kd_kuliah=$this->_request->get('kul');
		$a=$this->_request->get('a');
		$this->view->kd_kuliah=$kd_kuliah;
		$this->view->a=$a;
		$tugas = new Tugas();
		$getTugas=$tugas->getTugasById($id_tugas);
		if($getTugas){
			foreach ($getTugas as $dtTugas) {
				$this->view->id_tugas = $dtTugas['id_tugas'];
				$this->view->minggu = $dtTugas['minggu'];
				$this->view->prm = $dtTugas['param_nilai'];
				$this->view->jdl = $dtTugas['judul_tugas'];
				$this->view->knt = $dtTugas['konten_tugas'];
				$this->view->tgl1 = $dtTugas['published_fmt'];
				$this->view->tgl2 = $dtTugas['expired_fmt'];
				$this->view->link = $dtTugas['link'];
				$this->view->file = $dtTugas['nm_file'];
				$this->view->nm_dosen = $dtTugas['nm_dosen'];
				$kd_paket=$dtTugas['kd_paket_kelas'];
				$status=$dtTugas['status_tugas'];
				$this->view->status_tugas = $dtTugas['status_tugas'];
			}
			// data tugas
			$tugasMhs=new TugasMhs();
			$getTugasMhs=$tugasMhs->getTugasMhsByTugasKuliah($id_tugas,$kd_kuliah);
			$this->view->listTugasMhs=$getTugasMhs;
			if(($status!='RUNNING')or(count($getTugasMhs)>0)){
				$this->view->allow="f";
			}else{
				$this->view->allow="t";
			}
			// navigation
			$this->_helper->navbar('lms/tugas?id='.$kd_kuliah.'&a='.$a,0);
		}else{
			$this->view->eksis="f";
			// navigation
			$this->_helper->navbar('lms',0);
		}
	}

	function diskusiAction()
	{
		// get param
		$kd_kuliah=$this->_request->get('id');
		$this->view->kd_kuliah=$kd_kuliah;
		$a=$this->_request->get('a');
		$this->view->a=$a;
		$kuliah = new Nilai();
		$getKuliah=$kuliah->getNilaiByKd($kd_kuliah);
		if(!$getKuliah){
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "LMS";
			// navigation
			$this->_helper->navbar('lms',0);
		}else{
			if($a=='p'){
				$per="";
				$kd_paket="";
				foreach($getKuliah as $dtKul){
					$per=$dtKul['kd_periode'];
					$kd_paket=$dtKul['kd_paket_kelas'];
				}
				// Title Browser
				$this->view->title = "Daftar Diskusi";
				// get kd paket kelas
				$paketkelas = new Paketkelas();
				$getPaketKelas=$paketkelas->getPaketKelasByKd($kd_paket);
				if($getPaketKelas){
					foreach ($getPaketKelas as $dtPaket) {
						$per=$dtPaket['kd_periode'];
						$this->view->kd_paket_kelas = $dtPaket['kd_paket_kelas'];
						$kdKelas = $dtPaket['kd_kelas'];
						$this->view->kd_kelas = $dtPaket['kd_kelas'];
						$this->view->kd_paket_kelas = $dtPaket['kd_paket_kelas'];
						$this->view->nm_prodi=$dtPaket['nm_prodi_kur'];
						$this->view->kd_per=$dtPaket['kd_periode'];
						$this->view->nm_kelas=$dtPaket['nm_kelas'];
						$this->view->jns_kelas=$dtPaket['jns_kelas'];
						$this->view->nm_dsn=$dtPaket['nm_dosen'];
						$kd_dsn=$dtPaket['kd_dosen'];
						$this->view->nm_mk=$dtPaket['nm_mk'];
						$this->view->kd_mk=$dtPaket['kode_mk'];
						$this->view->sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
						$id_mk_kur=$dtPaket['id_mk_kurikulum'];
					}
					// data diskusi
					$diskusi=new Diskusi();
					$getDiskusi=$diskusi->getDiskusiByPaket($kd_paket);
					$diskMhs=new DiskusiMhs();
					$arrDisk=array();
					$i=0;
					foreach($getDiskusi as $dtDisk){
						$arrDisk[$i]['id_diskusi']=$dtDisk['id_diskusi'];
						$arrDisk[$i]['kd_paket_kelas']=$dtDisk['kd_paket_kelas'];
						$arrDisk[$i]['judul_diskusi']=$dtDisk['judul_diskusi'];
						$arrDisk[$i]['konten_diskusi']=$dtDisk['konten_diskusi'];
						$arrDisk[$i]['created']=$dtDisk['created'];
						$arrDisk[$i]['created_fmt']=$dtDisk['created_fmt'];
						$arrDisk[$i]['published_fmt']=$dtDisk['published_fmt'];
						$arrDisk[$i]['expired_fmt']=$dtDisk['expired_fmt'];
						$arrDisk[$i]['kd_dosen_created']=$dtDisk['kd_dosen_created'];
						$arrDisk[$i]['param_nilai']=$dtDisk['param_nilai'];
						$arrDisk[$i]['nm_dosen']=$dtDisk['nm_dosen'];
						$arrDisk[$i]['id_rps_detil']=$dtDisk['id_rps_detil'];
						$arrDisk[$i]['id_rps']=$dtDisk['id_rps'];
						$arrDisk[$i]['minggu']=$dtDisk['minggu'];
						$arrDisk[$i]['status_diskusi']=$dtDisk['status_diskusi'];
						// disk  mhs
						$getDiskMhs=$diskMhs->getDiskusiMhsByDiskusiKuliah($dtDisk['id_diskusi'],$kd_kuliah);
						if(!$getDiskMhs){
							$arrDisk[$i]['done']="f";
							$arrDisk[$i]['status_done']="Belum Posting";
							$arrDisk[$i]['warna']="danger";
							$arrDisk[$i]['nilai']="-";
						}else{
							$arrDisk[$i]['done']="t";
							$arrDisk[$i]['status_done']="Sudah Posting";
							$arrDisk[$i]['warna']="success";
							foreach ($getDiskMhs as $dtDiskMhs){
								$arrDisk[$i]['nilai']=$dtDiskMhs['nilai'];	
							}
						}
						$i++;
					}
					$this->view->listDiskusi=$arrDisk;
					// navigation
					$this->_helper->navbar('lms/index?id='.$per,0);
				}else{
					$this->view->eksis="f";
					// navigation
					$this->_helper->navbar('lms',0);
				}
			}else{
				$praktikum = new Praktikum();
				$getPraktikum=$praktikum->getPraktikumByKd($kd_kuliah);
				$per="";
				$id_kel="";
				foreach($getPraktikum as $dtKul){
					$per=$dtKul['kd_periode'];
					$id_kel=$dtKul['id_kelompok'];
				}
				// Title Browser
				$this->view->title = "Daftar Diskusi";
				// get kelompok
				$kelompok = new KelompokPraktikum();
				$getKelompok=$kelompok->getKelompokPraktikumByKd($id_kel);
				if($getKelompok){
					foreach ($getKelompok as $dtPaket) {
						$per=$dtPaket['kd_periode'];
						$this->view->id_kelompok = $dtPaket['id_kelompok'];
						$kdKelas = $dtPaket['kd_kelas'];
						$this->view->kd_kelas = $dtPaket['kd_kelas'];
						$this->view->nm_prodi=$dtPaket['nm_prodi_kur'];
						$this->view->kd_per=$dtPaket['kd_periode'];
						$this->view->nm_kelas=$dtPaket['nm_kelas'];
						$this->view->jns_kelas=$dtPaket['jns_kelas'];
						$this->view->nm_dsn=$dtPaket['nm_dosen'];
						$kd_dsn=$dtPaket['kd_dosen'];
						$this->view->nm_mk=$dtPaket['nm_mk'];
						$this->view->kd_mk=$dtPaket['kode_mk'];
						$this->view->sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
						$id_mk_kur=$dtPaket['id_mk_kurikulum'];
					}
					// data diskusi
					$diskusi=new Diskusi();
					$getDiskusi=$diskusi->getDiskusiByKelompok($id_kel);
					$diskMhs=new DiskusiMhs();
					$arrDisk=array();
					$i=0;
					foreach($getDiskusi as $dtDisk){
						$arrDisk[$i]['id_diskusi']=$dtDisk['id_diskusi'];
						$arrDisk[$i]['kd_paket_kelas']=$dtDisk['kd_paket_kelas'];
						$arrDisk[$i]['judul_diskusi']=$dtDisk['judul_diskusi'];
						$arrDisk[$i]['konten_diskusi']=$dtDisk['konten_diskusi'];
						$arrDisk[$i]['created']=$dtDisk['created'];
						$arrDisk[$i]['created_fmt']=$dtDisk['created_fmt'];
						$arrDisk[$i]['published_fmt']=$dtDisk['published_fmt'];
						$arrDisk[$i]['expired_fmt']=$dtDisk['expired_fmt'];
						$arrDisk[$i]['kd_dosen_created']=$dtDisk['kd_dosen_created'];
						$arrDisk[$i]['param_nilai']=$dtDisk['param_nilai'];
						$arrDisk[$i]['nm_dosen']=$dtDisk['nm_dosen'];
						$arrDisk[$i]['id_rps_detil']=$dtDisk['id_rps_detil'];
						$arrDisk[$i]['id_rps']=$dtDisk['id_rps'];
						$arrDisk[$i]['minggu']=$dtDisk['minggu'];
						$arrDisk[$i]['status_diskusi']=$dtDisk['status_diskusi'];
						// disk  mhs
						$getDiskMhs=$diskMhs->getDiskusiMhsByDiskusiKuliah($dtDisk['id_diskusi'],$kd_kuliah);
						if(!$getDiskMhs){
							$arrDisk[$i]['done']="f";
							$arrDisk[$i]['status_done']="Belum Posting";
							$arrDisk[$i]['warna']="danger";
							$arrDisk[$i]['nilai']="-";
						}else{
							$arrDisk[$i]['done']="t";
							$arrDisk[$i]['status_done']="Sudah Posting";
							$arrDisk[$i]['warna']="success";
							foreach ($getDiskMhs as $dtDiskMhs){
								$arrDisk[$i]['nilai']=$dtDiskMhs['nilai'];	
							}
						}
						$i++;
					}
					$this->view->listDiskusi=$arrDisk;
					// navigation
					$this->_helper->navbar('lms/index?id='.$per,0);
				}else{
					$this->view->eksis="f";
					// navigation
					$this->_helper->navbar('lms',0);
				}
			}
		}
	}

	function diskusidetilAction()
	{
		// Title Browser
		$this->view->title = "Daftar Posting Diskusi";
		// get id tugas
		$id_disk=$this->_request->get('id');
		$kd_kuliah=$this->_request->get('kul');
		$a=$this->_request->get('a');
		$this->view->a=$a;
		$this->view->kd_kuliah=$kd_kuliah;
		$diskusi = new Diskusi();
		$getDiskusi=$diskusi->getDiskusiById($id_disk);
		if($getDiskusi){
			foreach ($getDiskusi as $dtDisk) {
				$this->view->id_diskusi = $dtDisk['id_diskusi'];
				$this->view->minggu = $dtDisk['minggu'];
				$this->view->prm = $dtDisk['param_nilai'];
				$this->view->jdl = $dtDisk['judul_diskusi'];
				$this->view->knt = $dtDisk['konten_diskusi'];
				$this->view->tgl1 = $dtDisk['published_fmt'];
				$this->view->tgl2 = $dtDisk['expired_fmt'];
				$this->view->nm_dosen = $dtDisk['nm_dosen'];
				$kd_paket=$dtDisk['kd_paket_kelas'];
				$status=$dtDisk['status_diskusi'];
				$this->view->status_diskusi = $dtDisk['status_diskusi'];
			}
			// data tugas
			$diskMhs=new DiskusiMhs();
			$getDiskMhs=$diskMhs->getDiskusiMhsByDiskusi($id_disk);
			$this->view->listDiskMhs=$getDiskMhs;
			if($status!='RUNNING'){
				$this->view->allow="f";
			}else{
				$this->view->allow="t";
			}
			// navigation
			$this->_helper->navbar('lms/diskusi?id='.$kd_kuliah.'&a='.$a,0);
		}else{
			$this->view->eksis="f";
			// navigation
			$this->_helper->navbar('lms',0);
		}
	}

	function quizAction()
	{
		// get param
		$kd_kuliah=$this->_request->get('id');
		$this->view->kd_kuliah=$kd_kuliah;
		$a=$this->_request->get('a');
		$this->view->a=$a;
		$kuliah = new Nilai();
		$getKuliah=$kuliah->getNilaiByKd($kd_kuliah);
		$this->view->kd_kuliah=$kd_kuliah;
		if(!$getKuliah){
			$this->view->eksis="f";
			// Title Browser
			$this->view->title = "LMS";
			// navigation
			$this->_helper->navbar('lms',0);
		}else{
			if($a=='p'){
				$per="";
				$kd_paket="";
				foreach($getKuliah as $dtKul){
					$per=$dtKul['kd_periode'];
					$kd_paket=$dtKul['kd_paket_kelas'];
				}
				// Title Browser
				$this->view->title = "Daftar Quiz";
				// get kd paket kelas
				$paketkelas = new Paketkelas();
				$getPaketKelas=$paketkelas->getPaketKelasByKd($kd_paket);
				if($getPaketKelas){
					foreach ($getPaketKelas as $dtPaket) {
						$per=$dtPaket['kd_periode'];
						$this->view->kd_paket_kelas = $dtPaket['kd_paket_kelas'];
						$kdKelas = $dtPaket['kd_kelas'];
						$this->view->kd_kelas = $dtPaket['kd_kelas'];
						$this->view->kd_paket_kelas = $dtPaket['kd_paket_kelas'];
						$this->view->nm_prodi=$dtPaket['nm_prodi_kur'];
						$this->view->kd_per=$dtPaket['kd_periode'];
						$this->view->nm_kelas=$dtPaket['nm_kelas'];
						$this->view->jns_kelas=$dtPaket['jns_kelas'];
						$this->view->nm_dsn=$dtPaket['nm_dosen'];
						$kd_dsn=$dtPaket['kd_dosen'];
						$this->view->nm_mk=$dtPaket['nm_mk'];
						$this->view->kd_mk=$dtPaket['kode_mk'];
						$this->view->sks=$dtPaket['sks_tm']+$dtPaket['sks_prak']+$dtPaket['sks_prak_lap']+$dtPaket['sks_sim'];
						$id_mk_kur=$dtPaket['id_mk_kurikulum'];
					}
					// data quiz
					$quiz=new Quiz();
					$getQuiz=$quiz->getQuiz0ByPaket($kd_paket);
					$quizMhs=new QuizMhs();
					$arrQuiz=array();
					$i=0;
					foreach($getQuiz as $dtQuiz){
						$arrQuiz[$i]['id_quiz0']=$dtQuiz['id_quiz0'];
						$arrQuiz[$i]['kd_paket_kelas']=$dtQuiz['kd_paket_kelas'];
						$arrQuiz[$i]['nama_quiz']=$dtQuiz['nama_quiz'];
						$arrQuiz[$i]['published_fmt']=$dtQuiz['published_fmt'];
						$arrQuiz[$i]['start_time']=$dtQuiz['start_time'];
						$arrQuiz[$i]['end_time']=$dtQuiz['end_time'];
						$arrQuiz[$i]['kd_dosen_created']=$dtQuiz['kd_dosen_created'];
						$arrQuiz[$i]['param_nilai']=$dtQuiz['param_nilai'];
						$arrQuiz[$i]['nm_dosen']=$dtQuiz['nm_dosen'];
						$arrQuiz[$i]['id_rps_detil']=$dtQuiz['id_rps_detil'];
						$arrQuiz[$i]['minggu']=$dtQuiz['minggu'];
						$arrQuiz[$i]['status']=$dtQuiz['status'];
						// quiz mhs
						$getQuizMhs=$quizMhs->getQuzMhs0ByQuiz0Kuliah($dtQuiz['id_quiz0'],$kd_kuliah);
						if(!$getQuizMhs){
							$arrQuiz[$i]['id_quiz_mhs0']="";
							$arrQuiz[$i]['n_quiz_mhs1']=0;
							$arrQuiz[$i]['n_quiz_mhs1_answered']=0;
							$arrQuiz[$i]['n_quiz_mhs1_correct']=0;
							$arrQuiz[$i]['nilai']=0;
						}else{
							foreach ($getQuizMhs as $dtQuizMhs){
								$arrQuiz[$i]['id_quiz_mhs0']=$dtQuizMhs['id_quiz_mhs0'];
								$arrQuiz[$i]['n_quiz_mhs1']=$dtQuizMhs['n_quiz_mhs1'];
								$arrQuiz[$i]['n_quiz_mhs1_answered']=$dtQuizMhs['n_quiz_mhs1_answered'];
								$arrQuiz[$i]['n_quiz_mhs1_correct']=$dtQuizMhs['n_quiz_mhs1_correct'];
								$arrQuiz[$i]['nilai']=$dtQuizMhs['nilai'];	
							}
						}
						$i++;
					}
					$this->view->listQuiz=$arrQuiz;
					// navigation
					$this->_helper->navbar('lms/index?id='.$per,0);
				}else{
					$this->view->eksis="f";
					// navigation
					$this->_helper->navbar('lms',0);
				}
			}else{
				$this->view->eksis="f";
				// navigation
				$this->_helper->navbar('lms',0);			
			}
		}
	}


}