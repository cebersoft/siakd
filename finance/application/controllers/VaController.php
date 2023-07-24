<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: VA Controller -> Controller untuk modul halaman VA
*/

class VaController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('Va');
		Zend_Loader::loadClass('Bank');
		Zend_Loader::loadClass('Validation');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('PHPExcel');
		Zend_Loader::loadClass('PHPExcel_Cell_AdvancedValueBinder');
		Zend_Loader::loadClass('PHPExcel_IOFactory');
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
		$this->view->act_va="active";
		$this->view->act_mst="active open";	
	}
	
	function indexAction()
	{
		// Title Browser
		$this->view->title = "Daftar Virtual Akun";
		// destroy session param
		Zend_Session::namespaceUnset('param_va');
		// navigation
		$this->_helper->navbar(0,0,'va/new',0,'va/upload');
		// ref
		$bank = new Bank();
		$this->view->listBank=$bank->fetchAll();
	}
	
	function ashowAction(){
		// gets value from ajax request
		$request = $this->getRequest()->getPost();
		$no = $request['no'];
		$available = $request['available'];
		$bank = $request['bank'];
		// set session
		$param = new Zend_Session_Namespace('param_va');
		$param->no=$no;
		$param->available=$available;
		$param->bank=$bank;
	}
	
	function listAction()
	{
		// Title Browser
		$this->view->title = "Daftar Virtual Akun";
		// show data
		// get param
		$a=$this->_request->get('a');
		$va=new Va();
		if(!$a){
			$param = new Zend_Session_Namespace('param_va');
			$no = $param->no;
			$available = $param->available;
			$bank = $param->bank;
			$this->view->listVa=$va->queryVa($no, $available, $bank);
		}else{
			$this->view->listVa=$va->queryVa("", $a, "");
		}
		// navigation
		$this->_helper->navbar('va',0,'va/new',0,'va/upload');
	}

	function newAction(){
		// Title Browser
		$this->view->title = "Input Virtual Account Baru";
		// navigation
		$this->_helper->navbar(0,'va',0,0,'va/upload');
		// ref
		$bank = new Bank();
		$this->view->listBank=$bank->fetchAll();
	}
	
	function ainsAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$no = $this->_helper->string->esc_quote(trim($request['no']));
		$bank = $request['bank'];
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($no,1,100)=='F'){
			$err++;
			$msg=$msg."<strong>- No virtual account tidak boleh kosong maskimal 100 angka</strong><br>";
		}
		if($err==0){
			// set database
			$va = new Va();
			$setVa = $va->setNoVa($no,$bank);
			echo $setVa;
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}

	function editAction(){
		// get param
		$id=$this->_request->get('id');
		// Title Browser
		$this->view->title = "Edit Data Virtual Account";
		// ref
		$bank = new Bank();
		$this->view->listBank=$bank->fetchAll();
		// get data Va
		$va = new Va();
		$getVa=$va->getNoVaById($id);
		if($getVa){
			foreach ($getVa as $dtVa) {
				$this->view->no = $dtVa['no_va'];
				$this->view->bank=$dtVa['id_bank'];
				$available=$dtVa['available'];
			}
			if($available=='f'){
				$this->view->eksis="f";
			}
		}else{
			$this->view->eksis="f";
		}
		// navigation
		$this->_helper->navbar('va/list',0,0,0,0);
	}
	
	function aupdAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$id = $this->_helper->string->esc_quote(trim($request['id']));
		$no = $this->_helper->string->esc_quote(trim($request['no']));
		$bank = $request['bank'];
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($no,1,100)=='F'){
			$err++;
			$msg=$msg."<strong>- No virtual account tidak boleh kosong maskimal 100 angka</strong><br>";
		}
		if($err==0){
			// set database
			$va = new Va();
			$updVa = $va->updNoVa($no,$bank, $id);
			echo $updVa;
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
		$va = new Va();
		$delVa = $va->delNoVa($id);
		echo $delVa;
	}
	
	function exportAction(){
		// get param
		$bank=$this->_request->get('bank');
		// layout
		$this->_helper->layout->disableLayout();
		// export excel
		// konfigurasi excel
		PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Admin")
		->setLastModifiedBy("Admin")
		->setTitle("Va")
		->setSubject("Sistem Informasi")
		->setDescription("Daftar Va")
		->setKeywords("daftar va")
		->setCategory("Data File");

		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('Daftar VA');
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT)
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
		$objPHPExcel->getActiveSheet()->setCellValue('A1','NO.VA');
		$objPHPExcel->getActiveSheet()->getStyle('A1:A500')->applyFromArray($border);
		// Redirect output to a client web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Daftar VA.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	
	function uploadAction(){
		// Title Browser
		$this->view->title = "Upload Virtual Account";
		// navigation
		$this->_helper->navbar(0,'va',0,0,0);
		// ref
		$bank = new Bank();
		$this->view->listBank=$bank->fetchAll();
	}
	
	function auplAction(){
		// get param
		$bank=$this->_request->get('bank');
		// disabel layout
		$this->_helper->layout->disableLayout();
		if (0<$_FILES["file"]["error"] ) {
			echo "Error: ". $_FILES["file"]["error"] . "<br>";
		}
		else {
			$temp = explode(".", $_FILES["file"]["name"]);
			$newfilename = md5(round(microtime(true))) . '.' . end($temp);
			$x=rand(100000,999999);
			$temp_url=Zend_Registry::get('TEMP_URL');
			$target_dir=$temp_url.'/';
			$target_file = str_replace("'", "", $target_dir ."/". $newfilename);
			$uploadOk = 1;
			$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
			$mimes = array('application/vnd.ms-excel');
			$msg="";
			if(!in_array($_FILES['file']['type'],$mimes)){
				$msg=$msg."File harus excel! <br>";
				$uploadOk = 0;
			}
			// Check if file already exists
			if (file_exists($target_file)) {
				$msg= $msg."File sudah ada, silakan coba lagi<br>";
				$uploadOk = 0;
			}
			// Check file size
			if ($_FILES["file"]["size"] > 1000000) {
				$msg= $msg."File terlalu besar<br>";
				$uploadOk = 0;
			}
			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 0) {
				echo $msg;
			} else {
				if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
					$uploadOk = 1;
					// to database
					$objPHPExcel = PHPExcel_IOFactory::load($target_file);
					$objPHPExcel->setActiveSheetIndex(0);
					$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
					$jml_row = $objPHPExcel->getActiveSheet()->getHighestRow()-1;
					$arr_data=array();
					foreach ($cell_collection as $cell) {
						$column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
						$row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
						$data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
						if ($row < 2) {
							$header[$row][$column] = $data_value;
						} else {
							$arr_data[$row][$column] = $data_value;
						}
					}
					if (count($arr_data)>0) {
						$va = new Va();
						$n=1;
						foreach ($arr_data as $key => $value) {
							$no = $value['A'];
							if(($no!='')){
								$setNoVa =$va->setNoVa($no,$bank);
								$arrResult=explode('|', $setNoVa);
								$msg=$msg."Baris ke-".$n." : ".$arrResult[1]."<br>";
							}
							$n++;
						}
						echo $msg;
					}else{
						$msg= "Maaf tidak ada baris yang dapat diproses, silakan cek kembali";
						echo $msg;
					}
				} else {
					$msg= "Maaf terjadi error saat upload, silakan coba lagi.";
					echo $msg;
				}
				unlink($target_file);
			}
		}
	}
}