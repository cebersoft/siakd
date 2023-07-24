<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Ajax Controller -> Controller untuk submit via ajax
*/
class Ajax2Controller extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Mahasiswa');
		Zend_Loader::loadClass('Register');
		Zend_Loader::loadClass('Kuliah');
		Zend_Loader::loadClass('KuliahTA');
		Zend_Loader::loadClass('Perwalian');
		Zend_Loader::loadClass('Kuisioner');
		Zend_Loader::loadClass('Survey');
		Zend_Loader::loadClass('Pkrs');
		Zend_Loader::loadClass('TugasMhs');
		Zend_Loader::loadClass('DiskusiMhs');
		Zend_Loader::loadClass('PrpUjianTa');
		Zend_Loader::loadClass('Zend_Layout');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Validation');
		$auth = Zend_Auth::getInstance();
		$ses_std = new Zend_Session_Namespace('ses_std');
		if (($auth->hasIdentity())and($ses_std->uname)) {
			// global var
			$this->uname=Zend_Auth::getInstance()->getIdentity()->nim;
			$this->id=Zend_Auth::getInstance()->getIdentity()->id_mhs;
		}else{
			echo "F|Sesi anda sudah habis. Silakan login ulang!|";
		}
		// disabel layout
		$this->_helper->layout->disableLayout();
	}
	
	function inskuis3Action(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$id0=$request['id0'];
		$nimhsmsmh = $this->uname;
		$telpomsmh = $this->_helper->string->esc_quote($request['telpomsmh']);
		$emailmsmh = $this->_helper->string->esc_quote($request['emailmsmh']);
		$tahun_lulus = $this->_helper->string->esc_quote($request['tahun_lulus']);
		$nik = $this->_helper->string->esc_quote($request['nik']);
		$npwp = $this->_helper->string->esc_quote($request['npwp']);
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($nimhsmsmh,1,200)=='F'){
			$err++;
			$msg=$msg."<strong>- NIM tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength(trim($telpomsmh),1,200)=='F'){
			$err++;
			$msg=$msg."<strong>- No Telepon tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength(trim($emailmsmh),1,200)=='F'){
			$err++;
			$msg=$msg."<strong>- Email tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength(trim($tahun_lulus),1,200)=='F'){
			$err++;
			$msg=$msg."<strong>- Tahun lulus tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength(trim($nik),1,200)=='F'){
			$err++;
			$msg=$msg."<strong>- NIK tidak boleh kosong</strong><br>";
		}
		$kuis = new Kuisioner();
		$getChoiceCode=$kuis->getChoiceCodeByQuestion0($id0);
		$arrCode=array();
		foreach($getChoiceCode as $dtCode){
			$c=$dtCode['choice_code'];
			$arrCode=array_merge($arrCode,array($c=>$request['y_'.$c]));
		}
		if($err==0){
			// set database			
			$setKuis=$kuis->setKuis3($nimhsmsmh, $telpomsmh, $emailmsmh, $nik, $npwp, $tahun_lulus, json_encode($arrCode),$id0);
			echo $setKuis;
			// echo "F|".json_encode($arrCode);
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}

	function insprpujiantaAction(){
		// disabel layout
		$this->_helper->layout->disableLayout();
		// start inserting
		$request = $this->getRequest()->getPost();
		$kd_kuliah=$request['kd_kuliah'];
		$tgl=$request['tgl'];
		$kk=$request['kk'];
		// validation
		$err=0;
		$msg="";
		$vd = new Validation();
		if($vd->validasiLength($tgl,1,100)=='F'){
			$err++;
			$msg=$msg."<strong>- Tanggal tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength(trim($kd_kuliah),1,200)=='F'){
			$err++;
			$msg=$msg."<strong>- Mata Kuliah TA tidak boleh kosong</strong><br>";
		}
		if($vd->validasiLength(trim($kk),1,200)=='F'){
			$err++;
			$msg=$msg."<strong>- Kelompok keilmuan tidak boleh kosong</strong><br>";
		}
		$prp = new PrpUjianTa();
		if($err==0){
			// start uploading
                	if ((0<$_FILES["filez1"]["error"])) {
                    		$msg= "F|Error: ". $_FILES["filez1"]["error"];
                   		echo "F|".$msg;
                	}else {
				$doc_name=$this->_helper->string->esc_quote($_FILES["filez1"]["name"]);
		    		$setPrp=$prp->setPrp($tgl,$kd_kuliah,$kk,$doc_name);
                    		$arrSetPrp=explode("|", $setPrp);
                    		if($arrSetPrp[0]!='F'){
                        		$arrName = explode(".", $_FILES["filez1"]["name"]);
                       			$newfilename=$arrSetPrp[0].".".end($arrName);
                        		$file_url=Zend_Registry::get('FILE_URL');
                        		$target_dir=$file_url.'/ta';
                        		$target_file = str_replace("'", "", $target_dir ."/". $newfilename);
                        		$uploadOk = 1;
                        		$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
                        		$mimes = array('application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-powerpoint','application/vnd.openxmlformats-officedocument.presentationml.presentation');
                        		$msg="";
                        		if(!in_array($_FILES['filez1']['type'],$mimes)){
                           			$msg=$msg."File Harus PDF, Ms.Word, Excel! <br> nama:".$_FILES["filez1"]["name"];
                            			$uploadOk = 0;
                        		}
                        		if ($_FILES["filez1"]["size"] > (1024*20000)) {
                            			$msg= $msg."File  maksimal 5 MB<br>";
                            			$uploadOk = 0;
                        		}
                        		// Check if $uploadOk is set to 0 by an error
                        		if ($uploadOk != 0) {
                            			if (move_uploaded_file($_FILES["filez1"]["tmp_name"], $target_file)){
                                			$msg=$arrSetPrp[0]."|Data berhasil disimpan . <br>File berhasil diupload : ".$_FILES["filez1"]["name"];
                                			echo $msg;
                            			}else{
                                			$delPrp=$prp->delPrp($arrSetPrp[0]);
                                			$msg= "F|Maaf terjadi error saat upload, silakan coba lagi.". $_FILES["filez1"]["error"];
                                			echo $msg;
                           			 }
                        		}else{
                            			$delPrp=$prp->delPrp($arrSetPrp[0]);
                            			$msg= "F|Maaf terjadi error saat upload : <br>".$msg;
                            			echo $msg;
                        		}
                    		}else{
                        		echo $setPrp;
                    		}
                	}
		}else{
			echo "F|Terjadi ".$err." kesalahan data input :<br>".$msg;
		}
	}

	function delprpujiantaAction(){
		// set database
		$request = $this->getRequest()->getPost();
		// gets value from ajax request
		$param = $request['param'];
    		$id = $param[0];
    		$prp = new PrpUjianTa();
		$delPrp = $prp->delPrp($id);
		
		echo $delPrp;
	}
}