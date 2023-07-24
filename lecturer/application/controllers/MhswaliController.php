<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Mahasiswa Wali Controller -> Controller untuk modul mahasiswa wali
*/
class MhswaliController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Prodi');
		Zend_Loader::loadClass('Periode');
		Zend_Loader::loadClass('StatMhs');
		Zend_Loader::loadClass('Wilayah');
		Zend_Loader::loadClass('Konversi');
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Register');
		Zend_Loader::loadClass('Kuliah');
		Zend_Loader::loadClass('KuliahTA');
		Zend_Loader::loadClass('Nilai');
		Zend_Loader::loadClass('Kurikulum');
		Zend_Loader::loadClass('Ekuivalensi');
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
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('PHPExcel');
		Zend_Loader::loadClass('PHPExcel_Cell_AdvancedValueBinder');
		Zend_Loader::loadClass('PHPExcel_IOFactory');
		$auth = Zend_Auth::getInstance();
		$ses_lec = new Zend_Session_Namespace('ses_lec');
		if (($auth->hasIdentity())and($ses_lec->uname)) {
			$this->view->namadsn =Zend_Auth::getInstance()->getIdentity()->nm_dosen;
			$this->view->kddsn=Zend_Auth::getInstance()->getIdentity()->kd_dosen;
			$this->view->kd_pt=$ses_lec->kd_pt;
			$this->view->nm_pt=$ses_lec->nm_pt;
			// global var
			$this->kd_dsn=Zend_Auth::getInstance()->getIdentity()->kd_dosen;
			$this->nm_dsn=Zend_Auth::getInstance()->getIdentity()->nm_dosen;
		}else{
			$this->_redirect('/');
		}
		// layout
		$this->_helper->layout()->setLayout('main');
		// nav menu
		$this->view->wali_act="active";
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
		$this->view->title = "Mahasiswa Dosen Wali ".$this->nm_dsn;
		// navigation
		$this->_helper->navbar(0,0);
		// destroy session param
		Zend_Session::namespaceUnset('param_dsn_mhs');
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
	
	function listAction(){
		// show data
		$param = new Zend_Session_Namespace('param_dsn_mhs');
		$akt = $param->akt;
		$prd = $param->prd;
		$stat=$param->stat;
		$dw = $this->kd_dsn;
		// Title Browser
		$this->view->title = "Daftar Mahasiswa";
		// navigation
		$this->_helper->navbar('mhswali','mhswali/export');
		// get data mhs
		$mahasiswa = new Mahasiswa();
		$getDataMhs = $mahasiswa->getMahasiswaByAngkatanProdiStatusDw($akt, $prd, $stat, $dw);
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
	
	function detilAction(){
		// navigation
		$this->_helper->navbar('mhswali/list',0);
		// title browser
		$this->view->title = "Profil Mahasiswa";
		$id=$this->_request->get('id');
		$wilayah = new Wilayah();
		$mhs = new Mahasiswa();
		$getMhs = $mhs->getMahasiswaById($id);
		if (!$getMhs){
			$this->view->eksis='f';
		}else{
			$this->view->mhs_pt = $getMhs;
			foreach($getMhs as $data){
				$dw=$data['kd_dosen_wali'];
				if($dw==$this->kd_dsn){
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
					if ($data['jenis_kelamin']=='L'){
						$this->view->jk='Laki-laki';
					}else{
						$this->view->jk='Perempuan';				
					}
					$this->view->jk0=$data['jenis_kelamin'];
					$this->view->tgl_masuk= $data['tgl_masuk_fmt'];
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
					// data mhs pt
					$this->view->nm_prodi = $data['nm_prodi'];;
					$this->view->akt = $data['id_angkatan'];;
					// nilai konversi
					$konversi = new Konversi();
					$this->view->listKonversi = $konversi->getKonversiByIdMhs($id);	
				}else{
					$this->view->eksis='f';		
				}
			}
		}
	}
	
	function exportAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// session
		$param = new Zend_Session_Namespace('param_dsn_mhs');
		$dataMhs = $param->data;
		$prd=$param->v_prd;
		$akt=$param->v_akt;
		$stat=$param->v_stat;
		$ses_lec = new Zend_Session_Namespace('ses_lec');
		$nm_pt=$ses_lec->nm_pt;
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
		$objPHPExcel->getActiveSheet()->mergeCells('A1:L1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:L2');
		$objPHPExcel->getActiveSheet()->getStyle('A1:L1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A2:L2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('A3:L3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A:B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:L1')->getFont()->setSize(14);
		$objPHPExcel->getActiveSheet()->getStyle('A1:L1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A2:L2')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('A2:L2')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A3:L3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A3:L3')->getFont()->setSize(11);
		$objPHPExcel->getActiveSheet()->getStyle('A3:L3')->applyFromArray($cell_color);
		
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
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
		$objPHPExcel->getActiveSheet()->setCellValue('I3','NAMA IBU');
		$objPHPExcel->getActiveSheet()->setCellValue('J3','ASAL SEKOLAH');
		$objPHPExcel->getActiveSheet()->setCellValue('K3','ALAMAT');
		$objPHPExcel->getActiveSheet()->setCellValue('L3','KONTAK');
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
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$data['nm_ibu']);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$data['nm_pt_asal']);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,$data['alamat']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('L'.$i,$data['large_kontak'],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':L'.$i)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':L'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$n++;	
			$i++;				
		}
		$objPHPExcel->getActiveSheet()->getStyle('A3:L'.($i-1))->applyFromArray($border);
		$objPHPExcel->getActiveSheet()->getStyle('A4:L'.($i-1))->getFont()->setSize(10);
		
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Daftar Mahasiswa.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	
	function registerAction(){
		// Title Browser
		$this->view->title = "Her-Registrasi Akademik";
		// navigation
		$this->_helper->navbar('mhswali/list',0);
		// get data 
		$nim=$this->_request->get('nim');
		// data mahasiswa
		$mhs = new Mahasiswa();
		$getMhs=$mhs->getMahasiswaByNim($nim);
		if($getMhs){
			foreach ($getMhs as $dtMhs){
				$kd_dw=$dtMhs['kd_dosen_wali'];
				$this->view->nim=$dtMhs['nim'];
				$this->view->nm=$dtMhs['nm_mhs'];
				$this->view->nm_prodi=$dtMhs['nm_prodi'];
				$this->view->akt=$dtMhs['id_angkatan'];
			}
			if($kd_dw!=$this->kd_dsn){
				$this->view->eksis="f";
			}else{
				// register
				$register = new Register();
				$getRegister = $register->getRegisterByNim($nim);
				$this->view->listRegister = $getRegister;	
			}
		}else{
			$this->view->eksis="f";
		}
	}
	
	function krsAction()
	{
		// navigation
		$this->_helper->navbar('mhswali/list',0);
		// get nim periode
		$nim = $this->_request->get('nim');
		$kd_periode=$this->_request->get('per');
		// get register
		$register = new Register();
		$getRegister = $register->getRegisterByNimPeriode($nim,$kd_periode);
		if($getRegister){
			foreach ($getRegister as $dtReg) {
				$this->view->nim=$dtReg['nim'];
				$this->view->nm_mhs=$dtReg['nm_mhs'];
				$this->view->akt=$dtReg['id_angkatan'];
				$this->view->kd_prd=$dtReg['kd_prodi'];
				$this->view->prd=$dtReg['nm_prodi'];
				$this->view->dw=$dtReg['nm_dosen_wali'];
				$kd_prodi=$dtReg['kd_prodi'];
				$this->view->per=$dtReg['kd_periode'];
				$krs=$dtReg['krs'];
				$nim=$dtReg['nim'];
				$nm_mhs=$dtReg['nm_mhs'];
				$akt=$dtReg['id_angkatan'];
				$prd=$dtReg['nm_prodi'];
				$per=$dtReg['kd_periode'];
				$kd_dw=$dtReg['kd_dosen_wali'];
				$dw=$dtReg['nm_dosen_wali'];
			}
			if($krs=='f'){
				// Title Browser
				$this->view->title = "KRS Mahasiswa";
				$this->view->eksis="f";	
			}else{
				if($kd_dw!=$this->kd_dsn){
					// Title Browser
					$this->view->title = "KRS Mahasiswa";
					$this->view->eksis="f";	
				}else{
					// Title Browser
					$this->view->title = "KRS Mahasiswa ".$nm_mhs;
					// get data KRS
					$kuliah = new Kuliah();
					$getKuliah = $kuliah->getKuliahByNimPeriode($nim,$kd_periode);
					$this->view->listKuliah=$getKuliah;
					$kuliahTA = new KuliahTA();
					$getKuliahTA = $kuliahTA->getKuliahTAByNimPeriode($nim,$kd_periode);
					$this->view->listKuliahTA=$getKuliahTA;
				}
			}
		}else{
			// Title Browser
			$this->view->title = "KRS Mahasiswa";
			$this->view->eksis="f";
		}
	}
	
	function khsAction(){
		// Title Browser
		$this->view->title = "Kartu Hasil Studi Mahasiswa";
		// navigation
		$this->_helper->navbar('mhswali/list',0);
		$nim = $this->_request->get('nim');
		// get data mahasiswa
		$mhs = new Mahasiswa();
		$getMhs = $mhs->getMahasiswaByNim($nim);
		if(!$getMhs){
			$this->view->eksis="f";
		}else{
			foreach ($getMhs as $dtMhs) {
				$nim=$dtMhs['nim'];
				$nm=$dtMhs['nm_mhs'];
				$akt=$dtMhs['id_angkatan'];
				$kd_prd=$dtMhs['kd_prodi'];
				$nm_prd=$dtMhs['nm_prodi'];
				$kd_dw=$dtMhs['kd_dosen_wali'];
				$dw = $dtMhs['nm_dosen_wali'];
			}
			if($kd_dw!=$this->kd_dsn){
				$this->view->eksis="f";
			}else{
				$this->view->nim=$nim;
				$this->view->nm=$nm;
				$this->view->akt=$akt;
				$this->view->kd_prd=$kd_prd;
				$this->view->nm_prd=$nm_prd;
				$this->view->dw=$dw;
				// get data nilai
				$nilai=new Nilai();
				$getNilai=$nilai->getNilaiByNimPeriode($nim,"");
				$arrPer=array();
				foreach ($getNilai as $dtNilai){
					$arrPer[]=$dtNilai['kd_periode'];
				}
				$arrPer=array_values(array_unique($arrPer));
				$this->view->listPer=$arrPer;
				$this->view->listNilai=$getNilai;
			}
		}
	}
	
	function transkripAction(){
		// Title Browser
		$this->view->title = "Transkrip Nilai Mahasiswa";
		// navigation
		$this->_helper->navbar('mhswali/list',0);
		$nim = $this->_request->get('nim');
		$kd_periode="";
		// get data mahasiswa
		$mhs = new Mahasiswa();
		$getMhs = $mhs->getMahasiswaByNim($nim);
		if(!$getMhs){
			$this->view->eksis="f";
		}else{
			foreach ($getMhs as $dtMhs) {
				$nm=$dtMhs['nm_mhs'];
				$akt=$dtMhs['id_angkatan'];
				$kd_prd=$dtMhs['kd_prodi'];
				$nm_prd=$dtMhs['nm_prodi'];
				$kd_dw=$dtMhs['kd_dosen_wali'];
			}
			if($kd_dw!=$this->kd_dsn){
				$this->view->eksis="f";	
			}else{
				$this->view->nim=$nim;
				$this->view->nm=$nm;
				$this->view->akt=$akt;
				$this->view->kd_prd=$kd_prd;
				$this->view->nm_prd=$nm_prd;
				// get kurikulum mahasiswa
				$nilai = new Nilai();
				$getKurikulumMhs=$nilai->getTranskripKurikulumByNim($nim);
				$kur_mhs="";
				if($getKurikulumMhs){
					foreach($getKurikulumMhs as $dtKurMhs){
						$kur_mhs=$dtKurMhs['id_kurikulum'];
					}
				}
				// smt kur
				$kurikulum = new Kurikulum();
				$getKur=$kurikulum->getKurById($kur_mhs);
				$nSmt=0;
				foreach ($getKur as $dtKur){
					$nSmt=$dtKur['smt_normal'];
					$this->view->smt=$dtKur['smt_normal'];
				}
				// get data transkrip
				$nilaiTrkp=$nilai->getTranskripByNimKurikulum($nim,$kur_mhs);
				$this->view->listNilai= $nilaiTrkp;		
			}
		}
	}
	
	function keuanganAction(){
		// Title Browser
		$this->view->title = "Keuangan Mahasiswa";
		// navigation
		$this->_helper->navbar('mhswali/list',0);
		$nim = $this->_request->get('nim');
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
}