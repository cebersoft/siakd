<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Mahasiswa Controller -> Controller untuk modul halaman mahasiswa
*/
class MahasiswaController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('Dosen');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Konversi');
		Zend_Loader::loadClass('Indeks');
		Zend_Loader::loadClass('Agama');
		Zend_Loader::loadClass('Kwn');
		Zend_Loader::loadClass('JenisKelamin');
		Zend_Loader::loadClass('AlatTransport');
		Zend_Loader::loadClass('JenisTinggal');
		Zend_Loader::loadClass('Wilayah');
		Zend_Loader::loadClass('Job');
		Zend_Loader::loadClass('StatMasuk');
		Zend_Loader::loadClass('StatMhs');
		Zend_Loader::loadClass('Register');
		Zend_Loader::loadClass('Kuliah');
		Zend_Loader::loadClass('KuliahTA');
		Zend_Loader::loadClass('Nilai');
		Zend_Loader::loadClass('Report');
		Zend_Loader::loadClass('RepMhs');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Validation');
		Zend_Loader::loadClass('PHPExcel');
		Zend_Loader::loadClass('PHPExcel_Cell_AdvancedValueBinder');
		Zend_Loader::loadClass('PHPExcel_IOFactory');
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
		$this->view->active_tree="01";
		$this->view->active_menu="mahasiswa/index";
	}

	function indexAction()
	{
		$user = new Menu();
		$menu = "mahasiswa/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Daftar Mahasiswa";
			// navigation
			$this->_helper->navbar(0,0,'mahasiswa/new',0,'mahasiswa/upload');
			// destroy session param
			Zend_Session::namespaceUnset('param_mhs');
			// get data angkatan
			$akt = new Angkatan();
			$this->view->listAkt=$akt->fetchAll();
			// get data prodi
			$prod = new Prodi();
			$this->view->listProdi=$prod->fetchAll();
			// get data status mahasiswa
			$stat = new StatMhs();
			$this->view->listStat=$stat->fetchAll();
		}
	}
	
	function listAction(){
		$user = new Menu();
		$menu = "mahasiswa/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');	
		} else {		
			// show data
			$param = new Zend_Session_Namespace('param_mhs');
			$akt = $param->akt;
			$prd = $param->prd;
			$stat=$param->stat;
			// Title Browser
			$this->view->title = "Daftar Mahasiswa";
			// navigation
			$this->_helper->navbar('mahasiswa',0,'mahasiswa/new','mahasiswa/export','mahasiswa/upload');
			// get data mhs
			$mahasiswa = new Mahasiswa();
			$getDataMhs = $mahasiswa->getMahasiswaByAngkatanProdiStatus($akt,$prd,$stat);
			$this->view->listMhs = $getDataMhs;
			// get data angkatan
			$angkatan = new Angkatan();
			$listAkt=$angkatan->fetchAll();
			if(!$akt){
				$v_akt="SEMUA";
				$this->view->akt=$v_akt;
			}else{
				$v_akt="";
				foreach ($listAkt as $dataAkt) {
					foreach ($akt as $dt) {
						if($dt==$dataAkt['id_angkatan']){
							$v_akt=$dataAkt['id_angkatan'].", ".$v_akt;
						}
					}
				}
				$this->view->akt=$v_akt;
			}
			// get data prodi
			$prod = new Prodi();
			$listProdi=$prod->fetchAll();
			if(!$prd){
				$v_prd="SEMUA";
				$this->view->prd=$v_prd;
			}else{
				$v_prd="";
				foreach ($listProdi as $dataPrd) {
					foreach ($prd as $dt) {
						if($dt==$dataPrd['kd_prodi']){
							$v_prd=$dataPrd['nm_prodi'].", ".$v_prd;
						}
					}
				}
				$this->view->prd=$v_prd;
			}
			// get data status mahasiswa
			$status = new StatMhs();
			$listStat=$status->fetchAll();
			if(!$stat){
				$v_stat="SEMUA";
				$this->view->stat=$v_stat;
			}else{
				$v_stat="";
				foreach ($listStat as $dataStat) {
					foreach ($stat as $dt) {
						if($dt==$dataStat['id_stat_mhs']){
							$v_stat=$dataStat['status_mhs'].", ".$v_stat;
						}
					}
				}
				$this->view->stat=$v_stat;
			}
			// session data for export
			$param->data=$getDataMhs;
			$param->v_akt=$v_akt;
			$param->v_prd=$v_prd;
			$param->v_stat=$v_stat;
		}
	}
	
	function detilAction(){
		$user = new Menu();
		$menu = "mahasiswa/detil";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');				
		} else {
			// navigation
			$this->_helper->navbar('mahasiswa/list',0,'mahasiswa/new',0,'mahasiswa/upload');
			// title browser
			$this->view->title = "Profil Mahasiswa";
			$id=$this->_request->get('id');
			$nkonv=$this->_request->get('nkonv');
			$this->view->tab_hist="active";
			if($nkonv){
				$this->view->tab_konv="active";
				$this->view->tab_hist="";
			}
			$wilayah = new Wilayah();
			$mhs = new Mahasiswa();
			$getMhs = $mhs->getMahasiswaById($id);
			if (!$getMhs){
				$this->view->eksis='f';
			}else{
				$this->view->mhs_pt = $getMhs;
				foreach($getMhs as $data){
					$this->view->nim_mhs=$data['nim'];
					$this->view->id_mhs=$data['id_mhs'];
					$this->view->nama_mhs=$data['nm_mhs'];
					$this->view->dosen_wali=$data['nm_dosen_wali'];
					$this->view->alamat=$data['alamat'];
					$this->view->kontak=$data['large_kontak'];
					$this->view->email_k=$data['email_kampus'];
					$this->view->email_l=$data['email_lain'];
					$this->view->kota_tinggal=$data['kota'];
					$this->view->asal_sekolah=$data['nm_pt_asal'];
					$this->view->asal_prodi=$data['nm_prodi_asal'];
					$this->view->tempat_lahir=$data['tmp_lahir'];
					$this->view->tanggal_lahir=$data['tgl_lahir_fmt'];
					$this->view->nama_ayah=$data['nm_ayah'];
					$this->view->nama_ibu=$data['nm_ibu'];
					$this->view->job_ayah=$data['nm_job_ayah'];
					$this->view->job_ibu=$data['nm_job_ibu'];
					$this->view->username=$data['nim'];
					$this->view->status_mhs=$data['status_mhs'];
					if ($data['id_jns_keluar']==null){
						$this->view->status_keluar="Belum Lulus";
					}else{
						$this->view->status_keluar=$data['ket_keluar'];
					}
					$this->view->tgl_keluar=$data['tgl_keluar_fmt'];
					$this->view->no_ijazah=$data['no_ijazah'];
					$this->view->sk_yudisium=$data['sk_yudisium'];
					$this->view->tgl_sk_yudisium=$data['tgl_sk_yudisium_fmt'];
					$this->view->agama=$data['nm_agama'];
					$this->view->kwn=$data['nm_kwn'];
					$this->view->nik=$data['nik'];
					if ($data['jenis_kelamin']=='L'){
						$this->view->jk='Laki-laki';
					}else{
						$this->view->jk='Perempuan';				
					}
					$this->view->jk0=$data['jenis_kelamin'];
					$this->view->status_masuk= $data['nm_stat_masuk'];
					$this->view->jns_masuk= $data['jns_masuk'];
					$nm_wil="";
					$id_wil=$data['id_wil'];
					$getWilayah = $wilayah->getWilayahById($id_wil);
					foreach ($getWilayah as $dtWil){
						$nm_wil=$dtWil['nm_wil'];
						$kota=$dtWil['kota'];
						$prop=$dtWil['propinsi'];
						$negara=$dtWil['negara'];
					}
					$this->view->nm_wil = $nm_wil;
					$this->view->kota_wil = $kota;
					$this->view->prop_wil = $prop;
					$this->view->neg_wil = $negara;
					// alamat
					$this->view->nisn=$data['nisn'];
					$this->view->npwp=$data['npwp'];
					$this->view->zip=$data['zip'];
					if ($data['kps']=='t'){
						$this->view->kps='YA';
					}else{
						$this->view->kps='TIDAK';				
					}
					$this->view->trans=$data['nm_alat_transport'];
					$this->view->tinggal=$data['nm_jenis_tinggal'];
					// nilai konversi
					$konversi = new Konversi();
					$this->view->listKonversi = $konversi->getKonversiByIdMhs($id);
					// indeks
					$indeks = new Indeks();
					$this->view->listIndeks = $indeks->fetchAll();
				}
			}
		}
	}

	function detilptAction(){
		$user = new Menu();
		$menu = "mahasiswa/detil";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');				
		} else {
			// navigation
			$this->_helper->navbar('mahasiswa/list',0,'mahasiswa/new',0,'mahasiswa/upload');
			// title browser
			$this->view->title = "Histori Kuliah Mahasiswa";
			$nim=$this->_request->get('nim');
			// get mhs
			$mhs = new Mahasiswa();
			$getMhs = $mhs->getMahasiswaByNim($nim);
			if(!$getMhs){
				// title browser
				$this->view->title = "Histori Kuliah Mahasiswa";
				$this->view->eksis = "f";
			}else {
				foreach ($getMhs as $dtMhs){
					$nm_mhs=$dtMhs['nm_mhs'];
					$dw=$dtMhs['nm_dosen_wali'];
					$akt=$dtMhs['id_angkatan'];
					$kd_prd=$dtMhs['kd_prodi'];
					$nm_prd=$dtMhs['nm_prodi'];
					$id_jns_keluar=$dtMhs['id_jns_keluar'];
					$jns_keluar=$dtMhs['ket_keluar'];
					$tgl_keluar=$dtMhs['tgl_keluar'];
				}
				// title browser
				$this->view->title = "Histori Kuliah ".$nm_mhs." (".$nim.")";
				$this->view->nim=$nim;
				$this->view->nm_mhs=$nm_mhs;
				$this->view->dw=$dw;
				$this->view->akt=$akt;
				$this->view->kd_prd=$kd_prd;
				$this->view->nm_prd=$nm_prd;
				// register
				$register = new Register();
				$getRegister = $register->getRegisterByNim($nim);
				$this->view->listRegister = $getRegister;
				// keluar/lulus
				if($id_jns_keluar!=''){
					$this->view->keluar="t";
					$periode = new Periode();
					$getPeriodeKeluar=$periode->getPeriodeByTgl($tgl_keluar);
					$periode_keluar="";
					foreach($getPeriodeKeluar as $dtPerKeluar){
						$periode_keluar=$dtPerKeluar['kd_periode'];
					}
					$this->view->periode_keluar=$periode_keluar;
					$this->view->jns_keluar=$jns_keluar;
				}else{
					$this->view->keluar="f";
				}
				// krs
				$kuliah = new Kuliah();
				$getKuliah=$kuliah->getKuliahByNim($nim);
				$kuliahTA = new KuliahTA();
				$getKuliahTA=$kuliahTA->getKuliahTAByNim($nim);
				$getKuliahAll=array_merge($getKuliah,$getKuliahTA);
				$this->view->listKuliah=$getKuliahAll;
				$arrPerKul=array();
				foreach ($getKuliahAll as $dtKul){
					$arrPerKul[]=$dtKul['kd_periode'];
				}
				$arrPerKul=array_values(array_unique($arrPerKul));
				array_multisort($arrPerKul, SORT_ASC, $arrPerKul);
				$this->view->listPerKul=$arrPerKul;
				
				// khs
				$nilai = new Nilai();
				$kd_periode="";
				$getNilai = $nilai->getNilaiByNimPeriode($nim, $kd_periode);
				$this->view->listNilai=$getNilai;
				$arrPerNilai=array();
				foreach ($getNilai as $dtNilai){
					$arrPerNilai[]=$dtNilai['kd_periode'];
				}
				$arrPerNilai=array_values(array_unique($arrPerNilai));
				array_multisort($arrPerNilai, SORT_ASC, $arrPerNilai);
				$this->view->listPerNilai=$arrPerNilai;
			}
		}
	}
	
	function newAction(){
		$user = new Menu();
		$menu = "mahasiswa/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');			
		} else {		
			// navigation
			$this->_helper->navbar(0,'mahasiswa',0,0,'mahasiswa/upload');		
			// Title Browser
			$this->view->title = "Input Mahasiswa Baru";
			// ref
			$agama = new Agama();
			$get_agama=$agama->fetchAll();
			$this->view->listAgama=$get_agama;
			$kwn = new Kwn();
			$get_kwn=$kwn->fetchAll();
			$this->view->listKwn=$get_kwn;
			$job = new Job();
			$get_job=$job->fetchAll();
			$this->view->listJob=$get_job;
			$trans=new AlatTransport();
			$this->view->listTransport=$trans->fetchAll();
			$tinggal=new JenisTinggal();
			$this->view->listTinggal=$tinggal->fetchAll();
			$akt = new Angkatan();
			$this->view->listAkt = $akt->fetchAll();
			$prod = new Prodi();
			$this->view->listProdi=$prod->fetchAll();
			$dosen = new Dosen();
			$this->view->listDosenAktif = $dosen->getDosenWali();
			$statmasuk = new StatMasuk();
			$this->view->listStatMasuk = $statmasuk->fetchAll();
		}
	}
	
	function editAction(){
		$user = new Menu();
		$menu = "mahasiswa/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {		
			// get nim
			$nim=$this->_request->get('nim');
			// get data from data base
			$mhs=new Mahasiswa();
			$data_mhs=$mhs->getMahasiswaByNim($nim);
			// ref
			$agama = new Agama();
			$get_agama=$agama->fetchAll();
			$this->view->listAgama=$get_agama;
			$kwn = new Kwn();
			$get_kwn=$kwn->fetchAll();
			$this->view->listKwn=$get_kwn;
			$job = new Job();
			$get_job=$job->fetchAll();
			$this->view->listJob=$get_job;
			$trans=new AlatTransport();
			$this->view->listTransport=$trans->fetchAll();
			$tinggal=new JenisTinggal();
			$this->view->listTinggal=$tinggal->fetchAll();
			$akt = new Angkatan();
			$this->view->listAkt = $akt->fetchAll();
			$prod = new Prodi();
			$this->view->listProdi=$prod->fetchAll();
			$dosen = new Dosen();
			$this->view->listDosenAktif = $dosen->getDosenWali();
			$statmasuk = new StatMasuk();
			$this->view->listStatMasuk = $statmasuk->fetchAll();
			$wilayah = new Wilayah();
			$i=0;
			foreach($data_mhs as $data){
				$this->view->nm=$data['nm_mhs'];
				$nm=$data['nm_mhs'];
				$this->view->tmplhr=$data['tmp_lahir'];
				$this->view->tgllhr=$data['tgl_lahir_fmt'];
				$jk=$data['jenis_kelamin'];
				if($jk=='L'){
					$this->view->jkL="selected";
				}elseif ($jk=='P') {
					$this->view->jkP="selected";
				}
				$this->view->agm=$data['id_agama'];
				$this->view->kwn=$data['id_kwn'];
				$this->view->nik=$data['nik'];
				$this->view->almt=$data['alamat'];
				$this->view->kota=$data['kota'];
				$this->view->ayah=$data['nm_ayah'];
				$this->view->ibu=$data['nm_ibu'];
				$this->view->job_a=$data['job_ayah'];
				$this->view->job_i=$data['job_ibu'];
				$this->view->nim=$data['nim'];
				$this->view->akt=$data['id_angkatan'];
				$this->view->dw=$data['kd_dosen_wali'];
				$this->view->prd=$data['kd_prodi'];
				$this->view->tglmsk=$data['tgl_masuk_fmt'];
				$this->view->statmasuk=$data['id_stat_masuk'];
				$this->view->as_sk=$data['nm_pt_asal'];
				$this->view->as_prd=$data['nm_prodi_asal'];
				$this->view->sks=$data['sks_diakui'];
				$this->view->hp=$data['large_kontak'];
				$this->view->email_k=$data['email_kampus'];
				$this->view->email_l=$data['email_lain'];
				$this->view->idmhs = $data['id_mhs'];
				// wilayah
				$nm_wil="";
				$id_wil=$data['id_wil'];
				$getWilayah = $wilayah->getWilayahById($id_wil);
				foreach ($getWilayah as $dtWil){
					$nm_wil=$dtWil['nm_wil'];
				}
				$this->view->id_wil = $id_wil;
				$this->view->nm_wil = $nm_wil;
				// alamat
				$this->view->nisn=$data['nisn'];
				$this->view->npwp=$data['npwp'];
				$this->view->jln=$data['jalan'];
				$this->view->dsn=$data['dusun'];
				$this->view->rt=$data['rt'];
				$this->view->rw=$data['rw'];
				$this->view->kel=$data['kelurahan'];
				$this->view->zip=$data['zip'];
				$this->view->kps=$data['kps'];
				$this->view->trans=$data['id_alat_transport'];
				$this->view->tinggal=$data['id_jenis_tinggal'];
				$i++;
			}
			// navigation
			$this->_helper->navbar('mahasiswa/list','0','0','0','0');
			// Title Browser
			if ($i>0){
				$this->view->title = "Edit Data Mahasiswa Nama : ".$nm. " (".$nim.")";
			}else{
				$this->view->title = "Edit Data Mahasiswa";
				$this->view->eksis ='f';
			}
		}
	}

	function editmktransferAction(){
		$user = new Menu();
		$menu = "mahasiswa/edit";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// get nim
			$nim=$this->_request->get('nim');
			$id_mk_kur=$this->_request->get('idmkkur');
			// get mahasiswa
			$mhs=new Mahasiswa();
			$getMhs=$mhs->getMahasiswaByNim($nim);
			$id_mhs="";
			foreach ($getMhs as $dtMhs){
				$id_mhs=$dtMhs['id_mhs'];
			}
			// navigation
			$this->_helper->navbar('mahasiswa/detil?id='.$id_mhs.'&nkonv=t','0','0','0','0');
			// get data from data base
			$konv=new Konversi();
			$dataKonv=$konv->getKonversiByNimMkKur($nim, $id_mk_kur);
			$i=0;
			$this->view->id_mhs=$id_mhs;
			foreach($dataKonv as $data){
				$this->view->nim=$data['nim'];
				$this->view->id_kur=$data['id_kurikulum'];
				$this->view->nm_kur=$data['nm_kurikulum'];
				$this->view->id_mk_kur=$data['id_mk_kurikulum'];
				$this->view->kd_mk=$data['kode_mk'];
				$this->view->nm_mk=$data['nm_mk'];
				$this->view->sks_def=$data['sks_tot'];
				$this->view->sks_diakui=$data['sks_tot']-$data['sks_deducted'];
				$this->view->id_indeks=$data['id_indeks'];
				$this->view->kd_mk_asal=$data['kode_mk_asal'];
				$this->view->nm_mk_asal=$data['nm_mk_asal'];
				$this->view->sks_asal=$data['sks_asal'];
				$this->view->id_indeks_asal=$data['index_asal'];
			}
			// indeks
			$indeks = new Indeks();
			$this->view->listIndeks = $indeks->fetchAll();
		}
	}
	
	function exportAction(){
		$user = new Menu();
		$menu = "mahasiswa/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');			
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// session
			$param = new Zend_Session_Namespace('param_mhs');
			$dataMhs = $param->data;
			$prd=$param->v_prd;
			$akt=$param->v_akt;
			$stat=$param->v_stat;
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt=$ses_ac->nm_pt;
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Administrator")
										 ->setLastModifiedBy("Akademik")
										 ->setTitle("Export Data Mahasiswa")
										 ->setSubject("Sistem Informasi Akademik")
										 ->setDescription("Data Mahasiswa")
										 ->setKeywords("mahasiswa")
										 ->setCategory("Data File");
										 
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Daftar Mahasiswa');
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
														  ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
														  ->setFitToWidth('1')
														  ->setFitToHeight('Automatic')
														  ->SetHorizontalCentered(true);
			
			// margin is set in inches (0.5cm)
			$margin = 0.5 / 2.54;											  
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop($margin)
															->setBottom($margin)
															->setLeft($margin)
															->setRight($margin);	
	
			//set Layout
			$border = array(
			  'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$cell_color = array(
	 		   'fill' => array(
			        'type' => PHPExcel_Style_Fill::FILL_SOLID,
			        'color' => array('rgb'=>'CCCCCC')
			    ),
			);
			
			// properties field excel;
			$objPHPExcel->getActiveSheet()->mergeCells('A1:R1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:R2');
			$objPHPExcel->getActiveSheet()->getStyle('A1:R1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A2:R2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('A3:R3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A:B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A1:R1')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('A1:R1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A2:R2')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A2:R2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A3:R3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A3:R3')->getFont()->setSize(11);
			$objPHPExcel->getActiveSheet()->getStyle('A3:R3')->applyFromArray($cell_color);
			
			// column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(5);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(35);
			$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
			// insert data to excel
			$objPHPExcel->getActiveSheet()->setCellValue('A1','DAFTAR MAHASISWA '.strtoupper($nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('A2','STATUS : '.$stat.", ANGKATAN : ".$akt." PRODI : ".$prd);
			$objPHPExcel->getActiveSheet()->setCellValue('A3','NO');
			$objPHPExcel->getActiveSheet()->setCellValue('B3','NIM');
			$objPHPExcel->getActiveSheet()->setCellValue('C3','NAMA MAHASISWA');
			$objPHPExcel->getActiveSheet()->setCellValue('D3','L/P');
			$objPHPExcel->getActiveSheet()->setCellValue('E3','TEMPAT, TANGGAL LAHIR');
			$objPHPExcel->getActiveSheet()->setCellValue('F3','AGAMA');
			$objPHPExcel->getActiveSheet()->setCellValue('G3','STATUS');
			$objPHPExcel->getActiveSheet()->setCellValue('H3','NAMA AYAH');
			$objPHPExcel->getActiveSheet()->setCellValue('I3','PEKERJAAN AYAH');
			$objPHPExcel->getActiveSheet()->setCellValue('J3','NAMA IBU');
			$objPHPExcel->getActiveSheet()->setCellValue('K3','PEKERJAAN IBU');
			$objPHPExcel->getActiveSheet()->setCellValue('L3','ASAL SEKOLAH');
			$objPHPExcel->getActiveSheet()->setCellValue('M3','ALAMAT');
			$objPHPExcel->getActiveSheet()->setCellValue('N3','KONTAK');
			$objPHPExcel->getActiveSheet()->setCellValue('O3','EMAIL KAMPUS');
			$objPHPExcel->getActiveSheet()->setCellValue('P3','EMAIL LAIN');
			$objPHPExcel->getActiveSheet()->setCellValue('Q3','DOSEN WALI');
			$objPHPExcel->getActiveSheet()->setCellValue('R3','NIK');
			$i=4;
			$n=1;
			foreach ($dataMhs as $data){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
				$objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$i,$data['nim'],PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$data['nim']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$data['nm_mhs']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$data['jenis_kelamin']);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$data['tmp_lahir'].', '.$data['tgl_lahir_fmt']);	
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$data['nm_agama']);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$data['status_mhs']);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$data['nm_ayah']);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$data['nm_job_ayah']);
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$data['nm_ibu']);
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,$data['nm_job_ibu']);
				$objPHPExcel->getActiveSheet()->setCellValue('L'.$i,$data['nm_pt_asal']);
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$i,$data['alamat']);
				$objPHPExcel->getActiveSheet()->setCellValueExplicit('N'.$i,$data['large_kontak'],PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->setCellValue('O'.$i,$data['email_kampus']);
				$objPHPExcel->getActiveSheet()->setCellValue('P'.$i,$data['email_lain']);
				$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i,$data['nm_dosen_wali']);
				$objPHPExcel->getActiveSheet()->setCellValueExplicit('R'.$i,$data['nik'],PHPExcel_Cell_DataType::TYPE_STRING);;
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				$n++;	
				$i++;
			}
			$objPHPExcel->getActiveSheet()->getStyle('A3:R'.($i-1))->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('A4:R'.($i-1))->getFont()->setSize(10);
			
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Daftar Mahasiswa.xls"');
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}
	
	function reportAction(){
		$user = new Menu();
		$menu = "mahasiswa/report";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="mahasiswa/report";
			// session chart
			$param = new Zend_Session_Namespace('param_mhs_chart');
			$akt = $param->akt;
			$prd = $param->prd;
			$par=$param->par;
			$cht=$param->cht;
			// Title Browser
			$this->view->title = "Report Mahasiswa";
			if($cht){
				// layout
				$this->_helper->layout()->setLayout('second');
				// navigation
				$this->_helper->navbar("mahasiswa/report",0,0,0,0);
			}else {
				// layout
				$this->_helper->layout()->setLayout('main');
				// navigation
				$this->_helper->navbar(0,0,0,0,0);
			}
	
			// get data angkatan
			$angkatan = new Angkatan();
			$this->view->listAkt=$angkatan->fetchAll();
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
			if($cht){
				$rep = new Report();
				// axis
				$getTabelX=$rep->getTabel('angkatan');
				$arrTabelX=explode("||", $getTabelX);
				// where axis
				$where=array();
				$whereX[0]['key'] = $arrTabelX[1];
				$whereX[0]['param'] = $akt;
				// data axis
				$arrKolomX=array($arrTabelX[1]);
				$getX= $rep->get($arrTabelX[0], $arrKolomX, $arrKolomX, $arrKolomX, $whereX);
				
				// parameter
				$getTabelParam=$rep->getTabel($par);
				$arrTabel=explode("||", $getTabelParam);
				$tabel_param=$arrTabel[0];
				$key_param=$arrTabel[1];
				$label_param=$arrTabel[2];
				//--
				$arrKolomPar=array($key_param,$label_param);
				$wherePar=array();
				//--
				$getPar= $rep->get($tabel_param, $arrKolomPar, $arrKolomPar, $arrKolomPar,$wherePar);
				$arrPar=array();
				foreach ($getPar as $data){
					$arrPar['key'][]=$data[$key_param];
					$arrPar['label'][]=$data[$label_param];
				}
				
				// data
				$getTabelData=$rep->getTabel('mahasiswa');
				$arrTabelData=explode("||", $getTabelData);
				// where data
				$whereD[0]['key'] = $arrTabelX[1];
				$whereD[0]['param'] = $akt;
				$getTabelFil=$rep->getTabel('prodi');
				$arrTabelFil=explode("||", $getTabelFil);
				$whereD[1]['key'] = $arrTabelFil[1];
				$whereD[1]['param'] = $prd;
				//--
				$arrKolomD=array($arrTabelX[1],$key_param);
				$getReport= $rep->get($arrTabelData[0], $arrKolomD, $arrKolomD, $arrKolomD,$whereD);
				$this->view->x=$rep->query($arrTabelData[0], $arrKolomD, $arrKolomD, $arrKolomD,$whereD);
				// data
				$array=array();
				$i=0;
				foreach ($getX as $data) {
					$array[$i]['x']=$data[$arrTabelX[1]];
					foreach ($arrPar['key'] as $data2){
						$n=0;
						foreach ($getReport as $data3){
							if(($data3[$arrTabelX[1]]==$data[$arrTabelX[1]])and($data3[$key_param]==$data2)){
								$n=$data3['n'];
							}
						}
						$array[$i][$data2]=$n;
					}
					$i++;
				}
				// view
				$this->view->labels=$arrPar['label'];
				$this->view->key=$arrPar['key'];
				$this->view->data=$array;
				$this->view->chart=$cht;
			}
			// destroy session param
			Zend_Session::namespaceUnset('param_mhs_chart');
		}
	}

	function rekapkonversiAction(){
		$user = new Menu();
		$menu = "mahasiswa/rekapkonversi";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// destroy session param export
			Zend_Session::namespaceUnset('param_rekap_konv');
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="mahasiswa/rekapkonversi";
			// title
			$this->view->title = "Rekap Nilai Konversi";
			// layout
			$this->_helper->layout()->setLayout('main');
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
			// get data angkatan
			$angkatan= new Angkatan();
			$this->view->listAkt=$angkatan->fetchAll();
		}
	}
	
	function prekapkonversiAction(){
		$user = new Menu();
		$menu = "mahasiswa/rekapkonversi";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// navigation
			$this->_helper->navbar('mahasiswa/rekapkonversi',0,0,'mahasiswa/erekapkonversi',0);
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="mahasiswa/rekapkonversi";
			// layout
			$this->_helper->layout()->setLayout('second');
			// session
			$param = new Zend_Session_Namespace('param_rekap_konv');
			$kd_prodi = $param->prd;
			$akt = $param->akt;
			// get prodi
			$prodi = new Prodi();
			$getProdi=$prodi->getProdiByKd($kd_prodi);
			$nm_prd="";
			foreach ($getProdi as $dtProdi){
				$nm_prd=$dtProdi['nm_prodi'];
			}
			// title
			$this->view->title = "Rekap Nilai Konversi Prodi ".$nm_prd." Angkatan ".$akt;
			// get data mahasiswa
			$mhs=new Mahasiswa();
			$nilai=new Konversi();
			$getMhs=$mhs->getMahasiswaByAngkatanProdi($akt, $kd_prodi);
			$arrMhs=array();
			$arrNilai=array();
			$n=0;
			$m=0;
			foreach ($getMhs as $dtMhs){
				if($dtMhs['jns_masuk']=='2'){
					$arrMhs[$n]['nim']=$dtMhs['nim'];
					$arrMhs[$n]['nm_mhs']=$dtMhs['nm_mhs'];
					$arrMhs[$n]['id_mhs']=$dtMhs['id_mhs'];
					$arrMhs[$n]['nm_pt_asal']=$dtMhs['nm_pt_asal'];
					$arrMhs[$n]['nm_prodi_asal']=$dtMhs['nm_prodi_asal'];
					$getNilai=$nilai->getKonversiByNim($dtMhs['nim']);
					foreach ($getNilai as $dtNilai){
						$arrNilai[$m]['nim']=$dtNilai['nim'];
						$arrNilai[$m]['kode_mk_asal']=$dtNilai['kode_mk_asal'];
						$arrNilai[$m]['nm_mk_asal']=$dtNilai['nm_mk_asal'];
						$arrNilai[$m]['sks_asal']=$dtNilai['sks_asal'];
						$arrNilai[$m]['indeks_asal']=$dtNilai['indeks_asal'];
						$arrNilai[$m]['bobot_asal']=$dtNilai['bobot_asal'];
						$arrNilai[$m]['kode_mk']=$dtNilai['kode_mk'];
						$arrNilai[$m]['nm_mk']=$dtNilai['nm_mk'];
						$arrNilai[$m]['sks_tot']=$dtNilai['sks_tot'];
						$arrNilai[$m]['sks_diakui']=$dtNilai['sks_tot']-$dtNilai['sks_deducted'];
						$arrNilai[$m]['indeks']=$dtNilai['indeks'];
						$arrNilai[$m]['bobot']=$dtNilai['bobot'];
						$m++;
					}
					$n++;
				}
			}
			$this->view->prd=$nm_prd;
			$this->view->akt=$akt;
			$this->view->listNilai=$arrNilai;
			$this->view->listMhs=$arrMhs;
			// session for excel
			$param->dataNilai=$arrNilai;
			$param->dataMhs=$arrMhs;
			$param->nmPrd=$nm_prd;
			$param->akt=$akt;
		}
	}
	
	function erekapkonversiAction(){
		$user = new Menu();
		$menu = "mahasiswa/rekapkonversi";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// session
			$param = new Zend_Session_Namespace('param_rekap_konv');
			$dataNilai = $param->dataNilai;
			$dataMhs=$param->dataMhs;
			$nmPrd=$param->nmPrd;
			$akt=$param->akt;
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt=$ses_ac->nm_pt;
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$ses_ac = new Zend_Session_Namespace('ses_ac');
			$nm_pt = $ses_ac->nm_pt;
			$objPHPExcel->getProperties()->setCreator($nm_pt)
			->setLastModifiedBy("Akademik")
			->setTitle("Rekap Nilai Konversi")
			->setSubject("Sistem Informasi Akademik")
			->setDescription("Rekap Nilai")
			->setKeywords("rekap nilai")
			->setCategory("Data File");
				
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Rekap Nilai Konversi');
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
			->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
			->setFitToWidth('1')
			->setFitToHeight('Automatic')
			->SetHorizontalCentered(true);
			// margin is set in inches (0.5cm)
			$margin = 0.5 / 2.54;
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop($margin)
			->setBottom($margin)
			->setLeft($margin)
			->setRight($margin);
			//set Layout
			$border = array(
					'borders' => array(
							'allborders' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
							)
					)
			);
			$cell_color = array(
					'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb'=>'CCCCCC')
					),
			);
			// properties field excel;
			$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:J2');
			$objPHPExcel->getActiveSheet()->mergeCells('A3:J3');
			$objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A2:J2')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A2:J2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A3:J3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A3:J3')->getFont()->setSize(11);
			$objPHPExcel->getActiveSheet()->getStyle('A1:J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(17);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
			// insert data to excel
			$objPHPExcel->getActiveSheet()->setCellValue('A1',strtoupper($nm_pt));
			$objPHPExcel->getActiveSheet()->setCellValue('A2','REKAP NILAI KONVERSI');
			$objPHPExcel->getActiveSheet()->setCellValue('A3','PROGRAM STUDI '.$nmPrd.' ANGKATAN '.$akt);
			// data
			$i=5;
			foreach ($dataMhs as $dtMhs){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'NIM');
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$dtMhs['nim']);
				$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':J'.$i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getFont()->setBold(true);
				$i++;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'NAMA');
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$dtMhs['nm_mhs']);
				$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':J'.$i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getFont()->setBold(true);
				$i++;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'DIAKUI DI '.$nm_pt);
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':F'.$i);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,'ASAL : '.$dtMhs['nm_pt_asal']." (".$dtMhs['nm_prodi_asal'].")");
				$objPHPExcel->getActiveSheet()->mergeCells('G'.$i.':J'.$i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray($cell_color);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getFont()->setBold(true);
				$i++;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'NO');
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,'KODE MK');
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,'NAMA MK');
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,'SKS DEF');
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,'SKS DIAKUI');
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,'NILAI');
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,'KODE MK');
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,'NAMA MK');
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,'SKS MK');
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,'NILAI');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray($cell_color);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getFont()->setBold(true);
				$i++;
				$n=1;
				$totSksDef=0;
				$totSksDiakui=0;
				foreach ($dataNilai as $dtNilai){
					if($dtNilai['nim']==$dtMhs['nim']){
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
						$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$dtNilai['kode_mk']);
						$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$dtNilai['nm_mk']);
						$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$dtNilai['sks_tot']);
						$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$dtNilai['sks_diakui']);
						$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$dtNilai['indeks']);
						$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$dtNilai['kode_mk_asal']);
						$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$dtNilai['nm_mk_asal']);
						$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$dtNilai['sks_asal']);
						$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$dtNilai['indeks_asal']);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray($border);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getAlignment()->setWrapText(true);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$i++;
						$n++;
						$totSksDef=$totSksDef+$dtNilai['sks_tot'];
						$totSksDiakui=$totSksDiakui+$dtNilai['sks_diakui'];
					}
				}
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'Jumlah');
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$totSksDef);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$totSksDiakui);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$i=$i+2;
			}
			//Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Rekap Nilai Konversi.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}

	function uploadAction(){
	    $user = new Menu();
		$menu = "mahasiswa/new";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');			
		} else {
		    	// navigation
		    	$this->_helper->navbar(0,'mahasiswa',0,0,0);
	    		// Title Browser
	    		$this->view->title = "Upload Data Mahasiswa Baru";
	    		// ref
	    		$agama = new Agama();
	    		$get_agama=$agama->fetchAll();
	    		$this->view->listAgama=$get_agama;
	    		$kwn = new Kwn();
	    		$get_kwn=$kwn->fetchAll();
	   		$this->view->listKwn=$get_kwn;
	    		$job = new Job();
	   		$get_job=$job->fetchAll();
	    		$this->view->listJob=$get_job;
	    		$akt = new Angkatan();
	    		$this->view->listAkt = $akt->fetchAll();
	    		$prod = new Prodi();
	    		$this->view->listProdi=$prod->fetchAll();
	    		$dosen = new Dosen();
	    		$this->view->listDosenAktif = $dosen->getDosenWali();
	    		$statmasuk = new StatMasuk();
	    		$this->view->listStatMasuk = $statmasuk->fetchAll();
		}
	}
	
	function templateAction(){
	    $user = new Menu();
	    $menu = "mahasiswa/index";
	    $getMenu = $user->cekUserMenu($menu);
	    if ($getMenu=="F"){
	        $this->_redirect('home/akses');
	    } else {
	        // layout
	        $this->_helper->layout->disableLayout();
	        // get param
	        $akt=$this->_request->get('akt');
	        $prd=$this->_request->get('prd');
	        // export excel
	        // konfigurasi excel
	        PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
	        $objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setCreator("Admin")
	        ->setLastModifiedBy("Admin")
	        ->setTitle("Template Mhs")
	        ->setSubject("Sistem Informasi")
	        ->setDescription("Daftar Mhs")
	        ->setKeywords("daftar mahasiswa")
	        ->setCategory("Data File");
	        // Rename sheet
	        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
	        ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
	        ->setFitToWidth('1')
	        ->setFitToHeight('Automatic')
	        ->SetHorizontalCentered(true);
	        // margin is set in inches (0.5cm)
	        $margin = 0.5 / 2.54;
	        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop($margin)
	        ->setBottom($margin)
	        ->setLeft($margin)
	        ->setRight($margin);
	        //set Layout
	        $border = array(
	            'borders' => array(
	                'allborders' => array(
	                    'style' => PHPExcel_Style_Border::BORDER_THIN
	                )
	            )
	        );
	        $cell_color = array(
	            'fill' => array(
	                'type' => PHPExcel_Style_Fill::FILL_SOLID,
	                'color' => array('rgb'=>'CCCCCC')
	            ),
	        );
	        // properties field excel;
	        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(9);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(18);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(40);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(35);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(35);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(35);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(40);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(15);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(30);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(25);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(38);
	        $objPHPExcel->getActiveSheet()->getStyle('A1:X1')->getFont()->setBold(true);
	        $objPHPExcel->getActiveSheet()->getStyle('A1:X1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	        // insert data to excel
	        $objPHPExcel->getActiveSheet()->setCellValue('A1','NO');
	        $objPHPExcel->getActiveSheet()->setCellValue('B1','NIM');
	        $objPHPExcel->getActiveSheet()->setCellValue('C1','NAMA MAHASISWA');
	        $objPHPExcel->getActiveSheet()->setCellValue('D1','L/P');
	        $objPHPExcel->getActiveSheet()->setCellValue('E1','TEMPAT LAHIR');
	        $objPHPExcel->getActiveSheet()->setCellValue('F1','TANGGAL LAHIR (DD/MM/YYYY)');
	        $objPHPExcel->getActiveSheet()->setCellValue('G1','AGAMA');
	        $objPHPExcel->getActiveSheet()->setCellValue('H1','KEWARGANEGARAAN');
	        $objPHPExcel->getActiveSheet()->setCellValue('I1','ALAMAT');
	        $objPHPExcel->getActiveSheet()->setCellValue('J1','KOTA');
	        $objPHPExcel->getActiveSheet()->setCellValue('K1','NAMA AYAH');
	        $objPHPExcel->getActiveSheet()->setCellValue('L1','NAMA IBU');
	        $objPHPExcel->getActiveSheet()->setCellValue('M1','PEKERJAAN AYAH');
	        $objPHPExcel->getActiveSheet()->setCellValue('N1','PEKERJAAN IBU');
	        $objPHPExcel->getActiveSheet()->setCellValue('O1','NIK');
	        $objPHPExcel->getActiveSheet()->setCellValue('P1','EMAIL KAMPUS');
	        $objPHPExcel->getActiveSheet()->setCellValue('Q1','EMAIL LAIN');
	        $objPHPExcel->getActiveSheet()->setCellValue('R1','KONTAK');
	        $objPHPExcel->getActiveSheet()->setCellValue('S1','TANGGAL MASUK (DD/MM/YYYY)');
	        $objPHPExcel->getActiveSheet()->setCellValue('T1','STATUS MASUK-PRODI-ANGKATAN');
	        $objPHPExcel->getActiveSheet()->setCellValue('U1','SKS DIAKUI');
	        $objPHPExcel->getActiveSheet()->setCellValue('V1','ASAL PT/SEKOLAH');
	        $objPHPExcel->getActiveSheet()->setCellValue('W1','ASAL PRODI/JURUSAN');
	        $objPHPExcel->getActiveSheet()->setCellValue('X1','DOSEN WALI');
	        $objWorkseet2=$objPHPExcel->createSheet();
	        $objWorkseet2->setTitle('sheet2');
	        $n=202;
	        $agama=new Agama();
	        $getAgama=$agama->fetchAll();
	        $arrAgm=array();
	        foreach ($getAgama as $dtAgm){
	            $arrAgm[]=$dtAgm['nm_agama']."#:".$dtAgm['id_agama'];
	        }
	        $a=1;
	        foreach ($arrAgm as $agm){
	            $objPHPExcel->getSheetByName('sheet2')->setCellValue('B'.$a,$agm);
	            $a++;
	        }
	        //--
	        $kwn=new Kwn();
	        $getKwn=$kwn->fetchAll();
	        $arrKwn=array();
	        foreach ($getKwn as $dtKwn){
	            $arrKwn[]=$dtKwn['nm_kwn']."#:".$dtKwn['id_kwn'];
	        }
	        $b=1;
	        foreach ($arrKwn as $kn){
	            $objPHPExcel->getSheetByName('sheet2')->setCellValue('C'.$b,$kn);
	            $b++;
	        }
	        //--
	        $job=new Job();
	        $getJob=$job->fetchAll();
	        $arrJob=array();
	        foreach ($getJob as $dtJob){
	            $arrJob[]=$dtJob['nm_job']."#:".$dtJob['id_job'];
	        }
	        $c=1;
	        foreach ($arrJob as $jb){
	            $objPHPExcel->getSheetByName('sheet2')->setCellValue('D'.$c,$jb);
	            $c++;
	        }
	        //--
	        $sm=new StatMasuk();
	        $getSm=$sm->fetchAll();
	        $arrSm=array();
	        foreach ($getSm as $dtSm){
	            $arrSm[]=$dtSm['nm_stat_masuk']."#:".$dtSm['id_stat_masuk']."#:".$prd."#:".$akt;
	        }
	        $d=1;
	        foreach ($arrSm as $stm){
	            $objPHPExcel->getSheetByName('sheet2')->setCellValue('E'.$d,$stm);
	            $d++;
	        }
	        //--
	        $dosen=new Dosen();
	        $getDsn=$dosen->getDosenWali();
	        $arrDsn=array();
	        foreach ($getDsn as $dtDsn){
	            $arrDsn[]=substr($dtDsn['nm_dosen'],0,20)."#:".$dtDsn['kd_dosen'];
	        }
	        $i=1;
	        foreach ($arrDsn as $dsn){
	            $objPHPExcel->getSheetByName('sheet2')->setCellValue('A'.$i,$dsn);
	            $i++;
	        }
	        for ($x=2;$x<=$n;$x++){
	            $objValidation0 = $objPHPExcel->getActiveSheet()->getCell('D'.$x)->getDataValidation();
	            $objValidation0->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
	            $objValidation0->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
	            $objValidation0->setAllowBlank(false);
	            $objValidation0->setShowInputMessage(true);
	            $objValidation0->setShowErrorMessage(true);
	            $objValidation0->setShowDropDown(true);
	            $objValidation0->setErrorTitle('Input error');
	            $objValidation0->setError('Data tidak ada dalam listing');
	            $objValidation0->setPromptTitle('Ambil dari daftar yang tersedia');
	            $objValidation0->setPrompt('Silakan ambil data dari daftar');
	            $objValidation0->setFormula1('"L,P"');
	            //--
	            $objValidation1 = $objPHPExcel->getActiveSheet()->getCell('G'.$x)->getDataValidation();
	            $objValidation1->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
	            $objValidation1->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
	            $objValidation1->setAllowBlank(false);
	            $objValidation1->setShowInputMessage(true);
	            $objValidation1->setShowErrorMessage(true);
	            $objValidation1->setShowDropDown(true);
	            $objValidation1->setErrorTitle('Input error');
	            $objValidation1->setError('Data tidak ada dalam listing');
	            $objValidation1->setPromptTitle('Ambil dari daftar yang tersedia');
	            $objValidation1->setPrompt('Silakan ambil data dari daftar');
	            $objValidation1->setFormula1('sheet2!$B$1:$B$'.($a-1));
	            //--
	            $objValidation2 = $objPHPExcel->getActiveSheet()->getCell('H'.$x)->getDataValidation();
	            $objValidation2->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
	            $objValidation2->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
	            $objValidation2->setAllowBlank(false);
	            $objValidation2->setShowInputMessage(true);
	            $objValidation2->setShowErrorMessage(true);
	            $objValidation2->setShowDropDown(true);
	            $objValidation2->setErrorTitle('Input error');
	            $objValidation2->setError('Data tidak ada dalam listing');
	            $objValidation2->setPromptTitle('Ambil dari daftar yang tersedia');
	            $objValidation2->setPrompt('Silakan ambil data dari daftar');
	            $objValidation2->setFormula1('sheet2!$C$1:$C$'.($b-1));
	            //--
	            $objValidation3 = $objPHPExcel->getActiveSheet()->getCell('M'.$x)->getDataValidation();
	            $objValidation3->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
	            $objValidation3->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
	            $objValidation3->setAllowBlank(false);
	            $objValidation3->setShowInputMessage(true);
	            $objValidation3->setShowErrorMessage(true);
	            $objValidation3->setShowDropDown(true);
	            $objValidation3->setErrorTitle('Input error');
	            $objValidation3->setError('Data tidak ada dalam listing');
	            $objValidation3->setPromptTitle('Ambil dari daftar yang tersedia');
	            $objValidation3->setPrompt('Silakan ambil data dari daftar');
	            $objValidation3->setFormula1('sheet2!$D$1:$D$'.($c-1));
	            //--
	            $objValidation4 = $objPHPExcel->getActiveSheet()->getCell('N'.$x)->getDataValidation();
	            $objValidation4->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
	            $objValidation4->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
	            $objValidation4->setAllowBlank(false);
	            $objValidation4->setShowInputMessage(true);
	            $objValidation4->setShowErrorMessage(true);
	            $objValidation4->setShowDropDown(true);
	            $objValidation4->setErrorTitle('Input error');
	            $objValidation4->setError('Data tidak ada dalam listing');
	            $objValidation4->setPromptTitle('Ambil dari daftar yang tersedia');
	            $objValidation4->setPrompt('Silakan ambil data dari daftar');
	            $objValidation4->setFormula1('sheet2!$D$1:$D$'.($c-1));
	            //--
	            $objValidation5 = $objPHPExcel->getActiveSheet()->getCell('T'.$x)->getDataValidation();
	            $objValidation5->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
	            $objValidation5->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
	            $objValidation5->setAllowBlank(false);
	            $objValidation5->setShowInputMessage(true);
	            $objValidation5->setShowErrorMessage(true);
	            $objValidation5->setShowDropDown(true);
	            $objValidation5->setErrorTitle('Input error');
	            $objValidation5->setError('Data tidak ada dalam listing');
	            $objValidation5->setPromptTitle('Ambil dari daftar yang tersedia');
	            $objValidation5->setPrompt('Silakan ambil data dari daftar');
	            $objValidation5->setFormula1('sheet2!$E$1:$E$'.($d-1));
	            //--
	            $objValidation6 = $objPHPExcel->getActiveSheet()->getCell('X'.$x)->getDataValidation();
	            $objValidation6->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
	            $objValidation6->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
	            $objValidation6->setAllowBlank(false);
	            $objValidation6->setShowInputMessage(true);
	            $objValidation6->setShowErrorMessage(true);
	            $objValidation6->setShowDropDown(true);
	            $objValidation6->setErrorTitle('Input error');
	            $objValidation6->setError('Data tidak ada dalam listing');
	            $objValidation6->setPromptTitle('Ambil dari daftar yang tersedia');
	            $objValidation6->setPrompt('Silakan ambil data dari daftar');
	            $objValidation6->setFormula1('sheet2!$A$1:$A$'.($i-1));
	        }
	        $objPHPExcel->getActiveSheet()->getStyle('A1:X'.$n)->applyFromArray($border);
	        $objPHPExcel->getActiveSheet()->getStyle('A1:X1')->applyFromArray($cell_color);
	        // Redirect output to a client web browser (Excel5)
	        header('Content-Type: application/vnd.ms-excel');
	        header('Content-Disposition: attachment;filename="Template Mahasiswa.xls"');
	        header('Cache-Control: max-age=0');
	        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	        $objWriter->save('php://output');
	    }
	 }

	function rekapAction(){
		$user = new Menu();
		$menu = "mahasiswa/rekap";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// destroy session param export
			Zend_Session::namespaceUnset('param_rekap_mhs');
			// treeview
			$this->view->active_tree="11";
			$this->view->active_menu="mahasiswa/rekap";
			// title
			$this->view->title = "Rekap Mahasiswa";
			// layout
			$this->_helper->layout()->setLayout('main');
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// get data prodi
			$prodi = new Prodi();
			$this->view->listProdi=$prodi->fetchAll();
			// get data angkatan
			$angkatan= new Angkatan();
			$this->view->listAngkatan=$angkatan->fetchAll();
		}
	}

	function prekapAction(){
		// Title Browser
		$this->view->title = "Rekap Data Mahasiswa";
		// treeview
		$this->view->active_tree="11";
		$this->view->active_menu="mahasiswa/rekap";
		// navigation
		$this->_helper->navbar('mahasiswa/rekap',0,0,0,0);
		$param = new Zend_Session_Namespace('param_rekap_mhs');
    		$kd_prodi=$param->prodi;
    		$id_angkatan=$param->angkatan;
		$jns=$param->jns;
        	$prodi=new Prodi();
        	$this->view->listProdi=$prodi->getProdiQuery($kd_prodi);
       		$angkatan=new Angkatan();
        	$this->view->listAngkatan=$angkatan->getAngkatanQuery($id_angkatan);
        	$jeniskelamin=new JenisKelamin();
        	$this->view->listJenisKelamin=$jeniskelamin->getAll();
        	$agama=new Agama();
        	$this->view->listAgama=$agama->fetchAll();
       		$statmhs=new StatMhs();
        	$this->view->listStatMhs=$statmhs->fetchAll();
        	$statmasuk=new StatMasuk();
        	$this->view->listStatMasuk=$statmasuk->fetchAll();
		$rep=new RepMhs();
		$this->view->header=$jns; 
        	$this->view->listRepJk=$rep->getRepJk($id_angkatan,$kd_prodi); 
        	$this->view->listRepAgama=$rep->getRepAgama($id_angkatan,$kd_prodi);
        	$this->view->listRepStatMhs=$rep->getRepStatMhs($id_angkatan,$kd_prodi);
        	$this->view->listRepStatMasuk=$rep->getRepStatMasuk($id_angkatan,$kd_prodi);
        	$getRepKota=$rep->getRepKota($id_angkatan,$kd_prodi);
        	$this->view->listRepKota=$getRepKota;
        	$arrKota=array();
       		$i=0;
        	foreach ($getRepKota as $dtKota){
            		if($dtKota['id_kota']!=''){
                		$arrKota[$i]=$dtKota['id_kota']."&&&&".$dtKota['kota']."&&&&".$dtKota['propinsi'];
            		}else{
                		$arrKota[$i]="&&&&-&&&&-";
            		}
            		$i++;
        	}
        	$arrKota=array_unique($arrKota);
        	$this->view->listKota=$arrKota;
       		$getRepProv=$rep->getRepProv($id_angkatan,$kd_prodi);
        	$this->view->listRepProv=$getRepProv;
       		$arrProv=array();
        	$i=0;
        	foreach ($getRepProv as $dtProv){
            		if($dtProv['id_propinsi']!=''){
                		$arrProv[$i]=$dtProv['id_propinsi']."&&&&".$dtProv['propinsi'];
            		}else{
                		$arrProv[$i]="&&&&-&&&&-";
            		}
            		$i++;
        	}
        	$arrProv=array_unique($arrProv);
        	$this->view->listProv=$arrProv;
	}
}