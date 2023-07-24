<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Tracerid  Controller -> Controller untuk modul halaman tracerid
*/
class Tracerid2Controller extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		Zend_Loader::loadClass('Profile');
		Zend_Loader::loadClass('User');
		Zend_Loader::loadClass('Menu');
		Zend_Loader::loadClass('Kuisioner');
		Zend_Loader::loadClass('Alumni');
		Zend_Loader::loadClass('Angkatan');
		Zend_Loader::loadClass('Prodi');
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
		$this->view->active_tree="14";
		$this->view->active_menu="tracerid2/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "tracerid2/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Rekap Tracer Study";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// destroy session param
			Zend_Session::namespaceUnset('param_tracer2');
			// get data tahun lulus
			$year=date("Y");
			$arrThn=array();
			for($i=2010;$i<=$year;$i++){
				$arrThn[]=$i;
			}
			$this->view->listTahun=$arrThn;
			// get data prodi
			$prod = new Prodi();
			$this->view->listProdi=$prod->fetchAll();
		}
	}

	function listAction(){
		$user = new Menu();
		$menu = "tracerid2/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// show data
			$param = new Zend_Session_Namespace('param_tracer2');
			$thn = $param->thn;
			$prd = $param->prd;
			// Title Browser
			$this->view->title = "Daftar Tracer Study";
			// navigation
			$this->_helper->navbar('tracerid2/index',0,0,'tracerid2/export',0);
			// get data 
			$kuis = new Kuisioner();
			$getKuis=$kuis->queryNewKuisioner($prd,$thn);
			$this->view->listKuis=$getKuis;
			$getQuestion0=$kuis->queryQuestion0NewKuisioner($prd,$thn);
			// create session for excel
			$param->data=$getKuis;
			$param->q0=$getQuestion0;
		}
	}
		
	function exportAction(){
		$user = new Menu();
		$menu = "tracerid2/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// session
			$param = new Zend_Session_Namespace('param_tracer2');
			$dataKuis = $param->data;
			$q0=$param->q0;
			// konfigurasi excel
			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Administrator")
										 ->setLastModifiedBy("Akademik")
										 ->setTitle("Export Data Tracerid")
										 ->setSubject("Sistem Informasi Akademik")
										 ->setDescription("Data Tracerid")
										 ->setKeywords("tracerid")
										 ->setCategory("Data File");
										 
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Daftar Tracerid');
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
			$arrHuruf=array("J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
					"AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ",
					"BA","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP","BQ","BR","BS","BT","BU","BV","BW","BX","BY","BZ",
					"CA","CB","CC","CD","CE","CF","CG","CH","CI","CJ","CK","CL","CM","CN","CO","CP","CQ","CR","CS","CT","CU","CV","CW","CX","CY","CZ"
					);
			$i=1;
			$kuis=new Kuisioner();
			foreach ($q0 as $dtQ0){
				$start=$i;
				// column width
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
				// insert data to excel
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'kdptimsmh');
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,'kdpstmsmh');
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,'nimhsmsmh');
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,'nmmhsmsmh');
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,'telpomsmh');
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,'emailmsmh');
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,'tahun_lulus');
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,'nik');
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,'npwp');
				$getCode=$kuis->getChoiceCodeByQuestion0($dtQ0['question0_id']);
				$j=0;
				foreach($getCode as $dtCode){
					$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$j].'1',$dtCode['choice_code']);
					$j++;
				}
				// properties field excel;
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.$arrHuruf[($j-1)].'1')->getFont()->setSize(12);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.$arrHuruf[($j-1)].'1')->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.$arrHuruf[($j-1)].'1')->applyFromArray($cell_color);
				$i=$i+1;
				$n=1;
				foreach ($dataKuis as $data){
					$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$i,$data['kdptimsmh'],PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$i,$data['kdpstmsmh'],PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$data['nimhsmsmh']);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$data['nmmhsmsmh']);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit('E'.$i,$data['telpomsmh'],PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$data['emailmsmh']);
					$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$data['tahun_lulus']);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit('H'.$i,$data['nik'],PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit('I'.$i,$data['npwp'],PHPExcel_Cell_DataType::TYPE_STRING);
					$a=json_decode($data['answer'],true);
					$k=0;
					foreach($getCode as $dtCode){
						foreach($a as $key=>$value){
							if($dtCode['choice_code']==$key){
								$objPHPExcel->getActiveSheet()->setCellValue($arrHuruf[$k].$i,$value);
							}
						}
						$k++;	
					}
					$n++;	
					$i++;				
				}
				$objPHPExcel->getActiveSheet()->getStyle('A'.$start.':'.$arrHuruf[($j-1)].($i-1))->applyFromArray($border);
				$objPHPExcel->getActiveSheet()->getStyle('A'.($start+1).':'.$arrHuruf[($j-1)].($i-1))->getFont()->setSize(10);
			}
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Rekap Tracerid.xls"');
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');	
		}
	}

	function index2Action()
	{
	    $user = new Menu();
	    $menu = "tracerid2/index2";
	    $getMenu = $user->cekUserMenu($menu);
	    if ($getMenu=="F"){
	        $this->_redirect('home/akses');
	    } else {
	        // treeview
	        $this->view->active_tree="14";
	        $this->view->active_menu="tracerid2/index2";
	        // Title Browser
	        $this->view->title = "Rekap Mahasiswa Belum Mengisi";
	        // navigation
	        $this->_helper->navbar(0,0,0,0,0);
	        // destroy session param
	        Zend_Session::namespaceUnset('param_ntrc2');
	        // get data angkatan
	        $akt = new Angkatan();
	        $this->view->listAkt=$akt->fetchAll();
	        // get data prodi
	        $prod = new Prodi();
	        $this->view->listProdi=$prod->fetchAll();
	        // get data tahun lulus
	        $year=date("Y");
	        $arrThn=array();
	        for($i=2010;$i<=$year;$i++){
	            $arrThn[]=$i;
	        }
	        $this->view->listTahun=$arrThn;
	    }
	}
	
	function list2Action(){
	    $user = new Menu();
	    $menu = "tracerid2/index2";
	    $getMenu = $user->cekUserMenu($menu);
	    if ($getMenu=="F"){
	        $this->_redirect('home/akses');
	    } else {
	        // treeview
	        $this->view->active_tree="14";
	        $this->view->active_menu="tracerid2/index2";
	        // show data
	        $param = new Zend_Session_Namespace('param_ntrc2');
	        $akt = $param->akt;
	        $prd = $param->prd;
	        // get data angkatan
	        $angkatan = new Angkatan();
	        $listAkt=$angkatan->fetchAll();
	        if(!$akt){
	            $angk="SEMUA";
	            $this->view->akt=$angk;
	        }else{
	            $angk="";
	            foreach ($listAkt as $dataAkt) {
	                foreach ($akt as $dt) {
	                    if($dt==$dataAkt['id_angkatan']){
	                        $angk=$dataAkt['id_angkatan'].", ".$angk;
	                    }
	                }
	            }
	            $this->view->akt=$angk;
	        }
	        // get data prodi
	        $prod = new Prodi();
	        $listProdi=$prod->fetchAll();
	        if(!$prd){
	            $prod="SEMUA";
	            $this->view->prd=$prod;
	        }else{
	            $prod="";
	            foreach ($listProdi as $dataPrd) {
	                foreach ($prd as $dt) {
	                    if($dt==$dataPrd['kd_prodi']){
	                        $prod=$dataPrd['nm_prodi'].", ".$prod;
	                    }
	                }
	            }
	            $this->view->prd=$prod;
	        }
	        // Title Browser
	        $this->view->title = "Daftar Lulusan Belum Mengisi Tracer Study";
	        // navigation
	        $this->_helper->navbar('tracerid2/index2',0,0,'tracerid2/export2',0);
	        // get data
	        $alumni = new Alumni();
	        $getAlumni=$alumni->getAlumniByAngkatanProdiTanggal($akt,$prd,"","");
	        $arrThn=array();
	        $i=0;
	        foreach ($getAlumni as $dtAlumni){
	            $dtThn=explode("-", $dtAlumni['tgl_keluar']);
	            $arrThn[$i]=$dtThn[0];
	            $i++;
	        }
	        $arrThn=array_unique($arrThn);
	        $kuis = new Kuisioner();
	        $getKuis=$kuis->queryNewKuisioner($prd,$arrThn);
	        $arrMhs=array();
	        $i=0;
	        foreach ($getAlumni as $dtAlumni){
	            $is_trc="f";
	            foreach ($getKuis as $dtKuis){
	                if($dtAlumni['nim']==$dtKuis['nimhsmsmh']){
	                    $is_trc="t";
	                }
	            }
	            if($is_trc=='f'){
	                $arrMhs[$i]['nim']=$dtAlumni['nim'];
	                $arrMhs[$i]['nm_mhs']=$dtAlumni['nm_mhs'];
	                $arrMhs[$i]['tgl_keluar_fmt']=$dtAlumni['tgl_keluar_fmt'];
	                $arrMhs[$i]['id_angkatan']=$dtAlumni['id_angkatan'];
	                $arrMhs[$i]['nm_prodi']=$dtAlumni['nm_prodi'];
	                $i++;
	            }
	        }
	        $this->view->listMhs=$arrMhs;
	        // create session for excel
	        $param->data=$arrMhs;
	        $param->v_prd=$prod;
	        $param->v_akt=$angk;
	    }
	}
	
	function export2Action(){
	    $user = new Menu();
	    $menu = "tracerid2/index2";
	    $getMenu = $user->cekUserMenu($menu);
	    if ($getMenu=="F"){
	        $this->_redirect('home/akses');
	    } else {
	        // disabel layout
	        $this->_helper->layout->disableLayout();
	        // session
	        $param = new Zend_Session_Namespace('param_ntrc2');
	        $dataAlm = $param->data;
	        $prd=$param->v_prd;
	        $akt=$param->v_akt;
	        $ses_ac = new Zend_Session_Namespace('ses_ac');
	        $nm_pt=$ses_ac->nm_pt;
	        // konfigurasi excel
	        PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
	        $objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setCreator("Administrator")
	        ->setLastModifiedBy("Akademik")
	        ->setTitle("Export Data Tracer")
	        ->setSubject("Sistem Informasi Akademik")
	        ->setDescription("Data Tracer")
	        ->setKeywords("alumni")
	        ->setCategory("Data File");
	        
	        // Rename sheet
	        $objPHPExcel->getActiveSheet()->setTitle('Daftar Alumni Lulusan');
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
	        $objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
	        $objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
	        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	        $objPHPExcel->getActiveSheet()->getStyle('A2:F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	        $objPHPExcel->getActiveSheet()->getStyle('A3:F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	        $objPHPExcel->getActiveSheet()->getStyle('A:C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	        $objPHPExcel->getActiveSheet()->getStyle('D:F')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(14);
	        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
	        $objPHPExcel->getActiveSheet()->getStyle('A2:F2')->getFont()->setSize(12);
	        $objPHPExcel->getActiveSheet()->getStyle('A2:F2')->getFont()->setBold(true);
	        $objPHPExcel->getActiveSheet()->getStyle('A3:F3')->getFont()->setBold(true);
	        $objPHPExcel->getActiveSheet()->getStyle('A3:F3')->getFont()->setSize(11);
	        $objPHPExcel->getActiveSheet()->getStyle('A3:F3')->applyFromArray($cell_color);
	        
	        // column width
	        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(45);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
	        // insert data to excel
	        $objPHPExcel->getActiveSheet()->setCellValue('A1','DAFTAR LULUSAN BELUM MENGISI TRACER STUDY '.strtoupper($nm_pt));
	        $objPHPExcel->getActiveSheet()->setCellValue('A2','PROGRAM STUDI : '.$prd.", ANGKATAN : ".$akt);
	        $objPHPExcel->getActiveSheet()->setCellValue('A3','NO');
	        $objPHPExcel->getActiveSheet()->setCellValue('B3','NIM');
	        $objPHPExcel->getActiveSheet()->setCellValue('C3','NAMA MAHASISWA');
	        $objPHPExcel->getActiveSheet()->setCellValue('D3','TANGGAL LULUS');
	        $objPHPExcel->getActiveSheet()->setCellValue('E3','ANGKATAN');
	        $objPHPExcel->getActiveSheet()->setCellValue('F3','PRODI');
	        $i=4;
	        $n=1;
	        foreach ($dataAlm as $data){
	            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$n);
	            $objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$i,$data['nim'],PHPExcel_Cell_DataType::TYPE_STRING);
	            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$data['nim']);
	            $objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$data['nm_mhs']);
	            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$data['tgl_keluar_fmt']);
	            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$data['id_angkatan']);
	            $objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$data['nm_prodi']);
	            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setWrapText(true);
	            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
	            $n++;
	            $i++;
	        }
	        $objPHPExcel->getActiveSheet()->getStyle('A3:F'.($i-1))->applyFromArray($border);
	        $objPHPExcel->getActiveSheet()->getStyle('A4:F'.($i-1))->getFont()->setSize(10);
	        
	        // Redirect output to a clientâ€™s web browser (Excel5)
	        header('Content-Type: application/vnd.ms-excel');
	        header('Content-Disposition: attachment;filename="Daftar Lulusan.xls"');
	        header('Cache-Control: max-age=0');
	        
	        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	        $objWriter->save('php://output');
	    }
	}
}
