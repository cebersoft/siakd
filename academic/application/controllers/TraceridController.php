<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Tracerid  Controller -> Controller untuk modul halaman tracerid
*/
class TraceridController extends Zend_Controller_Action
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
		$this->view->active_menu="tracerid/index";
	}
	
	function indexAction()
	{
		$user = new Menu();
		$menu = "tracerid/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// Title Browser
			$this->view->title = "Rekap Tracer Study";
			// navigation
			$this->_helper->navbar(0,0,0,0,0);
			// destroy session param
			Zend_Session::namespaceUnset('param_trc');
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
		$menu = "tracerid/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// show data
			$param = new Zend_Session_Namespace('param_trc');
			$thn = $param->thn;
			$prd = $param->prd;
			// Title Browser
			$this->view->title = "Daftar Tracer Study";
			// navigation
			$this->_helper->navbar('tracerid/index',0,0,'tracerid/export',0);
			// get data 
			$kuis = new Kuisioner();
			$getKuis=$kuis->queryKuisioner($prd,$thn);
			$this->view->listKuis=$getKuis;
			// create session for excel
			$param->data=$getKuis;
		}
	}
		
	function exportAction(){
		$user = new Menu();
		$menu = "tracerid/index";
		$getMenu = $user->cekUserMenu($menu);
		if ($getMenu=="F"){
			$this->_redirect('home/akses');
		} else {
			// disabel layout
			$this->_helper->layout->disableLayout();
			// session
			$param = new Zend_Session_Namespace('param_trc');
			$dataKuis = $param->data;
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
			
			// properties field excel;
			$objPHPExcel->getActiveSheet()->getStyle('A1:EA1')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A1:EA1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A1:EA1')->applyFromArray($cell_color);
			
			// column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);

			// insert data to excel
			$objPHPExcel->getActiveSheet()->setCellValue('A1','kdptimsmh');
			$objPHPExcel->getActiveSheet()->setCellValue('B1','kdpstmsmh');
			$objPHPExcel->getActiveSheet()->setCellValue('C1','nimhsmsmh');
			$objPHPExcel->getActiveSheet()->setCellValue('D1','nmmhsmsmh');
			$objPHPExcel->getActiveSheet()->setCellValue('E1','telpomsmh');
			$objPHPExcel->getActiveSheet()->setCellValue('F1','emailmsmh');
			$objPHPExcel->getActiveSheet()->setCellValue('G1','tahun_lulus');
			$objPHPExcel->getActiveSheet()->setCellValue('H1','f21');
$objPHPExcel->getActiveSheet()->setCellValue('I1','f22');
$objPHPExcel->getActiveSheet()->setCellValue('J1','f23');
$objPHPExcel->getActiveSheet()->setCellValue('K1','f24');
$objPHPExcel->getActiveSheet()->setCellValue('L1','f25');
$objPHPExcel->getActiveSheet()->setCellValue('M1','f26');
$objPHPExcel->getActiveSheet()->setCellValue('N1','f27');
$objPHPExcel->getActiveSheet()->setCellValue('O1','f301');
$objPHPExcel->getActiveSheet()->setCellValue('P1','f302 ');
$objPHPExcel->getActiveSheet()->setCellValue('Q1','f303 ');
$objPHPExcel->getActiveSheet()->setCellValue('R1','f401');
$objPHPExcel->getActiveSheet()->setCellValue('S1','f402');
$objPHPExcel->getActiveSheet()->setCellValue('T1','f403');
$objPHPExcel->getActiveSheet()->setCellValue('U1','f404');
$objPHPExcel->getActiveSheet()->setCellValue('V1','f405');
$objPHPExcel->getActiveSheet()->setCellValue('W1','f406');
$objPHPExcel->getActiveSheet()->setCellValue('X1','f407');
$objPHPExcel->getActiveSheet()->setCellValue('Y1','f408');
$objPHPExcel->getActiveSheet()->setCellValue('Z1','f409');
$objPHPExcel->getActiveSheet()->setCellValue('AA1','f410');
$objPHPExcel->getActiveSheet()->setCellValue('AB1','f411');
$objPHPExcel->getActiveSheet()->setCellValue('AC1','f412');
$objPHPExcel->getActiveSheet()->setCellValue('AD1','f413');
$objPHPExcel->getActiveSheet()->setCellValue('AE1','f414');
$objPHPExcel->getActiveSheet()->setCellValue('AF1','f415');
$objPHPExcel->getActiveSheet()->setCellValue('AG1','f416');
$objPHPExcel->getActiveSheet()->setCellValue('AH1','f6 ');
$objPHPExcel->getActiveSheet()->setCellValue('AI1','f501');
$objPHPExcel->getActiveSheet()->setCellValue('AJ1','f502 ');
$objPHPExcel->getActiveSheet()->setCellValue('AK1','f503 ');
$objPHPExcel->getActiveSheet()->setCellValue('AL1','f7 ');
$objPHPExcel->getActiveSheet()->setCellValue('AM1','f7a');
$objPHPExcel->getActiveSheet()->setCellValue('AN1','f8');
$objPHPExcel->getActiveSheet()->setCellValue('AO1','f901');
$objPHPExcel->getActiveSheet()->setCellValue('AP1','f902');
$objPHPExcel->getActiveSheet()->setCellValue('AQ1','f903');
$objPHPExcel->getActiveSheet()->setCellValue('AR1','f904');
$objPHPExcel->getActiveSheet()->setCellValue('AS1','f905');
$objPHPExcel->getActiveSheet()->setCellValue('AT1','f906');
$objPHPExcel->getActiveSheet()->setCellValue('AU1','f1001');
$objPHPExcel->getActiveSheet()->setCellValue('AV1','f1002');
$objPHPExcel->getActiveSheet()->setCellValue('AW1','f1101');
$objPHPExcel->getActiveSheet()->setCellValue('AX1','f1102');
$objPHPExcel->getActiveSheet()->setCellValue('AY1','f1201');
$objPHPExcel->getActiveSheet()->setCellValue('AZ1','f1202');
$objPHPExcel->getActiveSheet()->setCellValue('BA1','f1301');
$objPHPExcel->getActiveSheet()->setCellValue('BB1','f1302');
$objPHPExcel->getActiveSheet()->setCellValue('BC1','f1303');
$objPHPExcel->getActiveSheet()->setCellValue('BD1','f14');
$objPHPExcel->getActiveSheet()->setCellValue('BE1','f15');
$objPHPExcel->getActiveSheet()->setCellValue('BF1','f1601');
$objPHPExcel->getActiveSheet()->setCellValue('BG1','f1602');
$objPHPExcel->getActiveSheet()->setCellValue('BH1','f1603');
$objPHPExcel->getActiveSheet()->setCellValue('BI1','f1604');
$objPHPExcel->getActiveSheet()->setCellValue('BJ1','f1605');
$objPHPExcel->getActiveSheet()->setCellValue('BK1','f1606');
$objPHPExcel->getActiveSheet()->setCellValue('BL1','f1607');
$objPHPExcel->getActiveSheet()->setCellValue('BM1','f1608');
$objPHPExcel->getActiveSheet()->setCellValue('BN1','f1609');
$objPHPExcel->getActiveSheet()->setCellValue('BO1','f1610');
$objPHPExcel->getActiveSheet()->setCellValue('BP1','f1611');
$objPHPExcel->getActiveSheet()->setCellValue('BQ1','f1612');
$objPHPExcel->getActiveSheet()->setCellValue('BR1','f1613');
$objPHPExcel->getActiveSheet()->setCellValue('BS1','f1614');
$objPHPExcel->getActiveSheet()->setCellValue('BT1','f1701');
$objPHPExcel->getActiveSheet()->setCellValue('BU1','f1702b');
$objPHPExcel->getActiveSheet()->setCellValue('BV1','f1703');
$objPHPExcel->getActiveSheet()->setCellValue('BW1','f1704b');
$objPHPExcel->getActiveSheet()->setCellValue('BX1','f1705');
$objPHPExcel->getActiveSheet()->setCellValue('BY1','f1705a');
$objPHPExcel->getActiveSheet()->setCellValue('BZ1','f1706');
$objPHPExcel->getActiveSheet()->setCellValue('CA1','f1706ba');
$objPHPExcel->getActiveSheet()->setCellValue('CB1','f1707');
$objPHPExcel->getActiveSheet()->setCellValue('CC1','f1708b');
$objPHPExcel->getActiveSheet()->setCellValue('CD1','f1709');
$objPHPExcel->getActiveSheet()->setCellValue('CE1','f1710b');
$objPHPExcel->getActiveSheet()->setCellValue('CF1','f1711');
$objPHPExcel->getActiveSheet()->setCellValue('CG1','f1711a');
$objPHPExcel->getActiveSheet()->setCellValue('CH1','f1712b');
$objPHPExcel->getActiveSheet()->setCellValue('CI1','f1712a');
$objPHPExcel->getActiveSheet()->setCellValue('CJ1','f1713');
$objPHPExcel->getActiveSheet()->setCellValue('CK1','f1714b');
$objPHPExcel->getActiveSheet()->setCellValue('CL1','f1715');
$objPHPExcel->getActiveSheet()->setCellValue('CM1','f1716b');
$objPHPExcel->getActiveSheet()->setCellValue('CN1','f1717');
$objPHPExcel->getActiveSheet()->setCellValue('CO1','f1718b');
$objPHPExcel->getActiveSheet()->setCellValue('CP1','f1719');
$objPHPExcel->getActiveSheet()->setCellValue('CQ1','f1720b');
$objPHPExcel->getActiveSheet()->setCellValue('CR1','f1721');
$objPHPExcel->getActiveSheet()->setCellValue('CS1','f1722b');
$objPHPExcel->getActiveSheet()->setCellValue('CT1','f1723');
$objPHPExcel->getActiveSheet()->setCellValue('CU1','f1724b');
$objPHPExcel->getActiveSheet()->setCellValue('CV1','f1725');
$objPHPExcel->getActiveSheet()->setCellValue('CW1','f1726b');
$objPHPExcel->getActiveSheet()->setCellValue('CX1','f1727');
$objPHPExcel->getActiveSheet()->setCellValue('CY1','f1728b');
$objPHPExcel->getActiveSheet()->setCellValue('CZ1','f1729');
$objPHPExcel->getActiveSheet()->setCellValue('DA1','f1730b');
$objPHPExcel->getActiveSheet()->setCellValue('DB1','f1731');
$objPHPExcel->getActiveSheet()->setCellValue('DC1','f1732b');
$objPHPExcel->getActiveSheet()->setCellValue('DD1','f1733');
$objPHPExcel->getActiveSheet()->setCellValue('DE1','f1734b');
$objPHPExcel->getActiveSheet()->setCellValue('DF1','f1735');
$objPHPExcel->getActiveSheet()->setCellValue('DG1','f1736b');
$objPHPExcel->getActiveSheet()->setCellValue('DH1','f1737');
$objPHPExcel->getActiveSheet()->setCellValue('DI1','f1737a');
$objPHPExcel->getActiveSheet()->setCellValue('DJ1','f1738');
$objPHPExcel->getActiveSheet()->setCellValue('DK1','f1738ba');
$objPHPExcel->getActiveSheet()->setCellValue('DL1','f1739');
$objPHPExcel->getActiveSheet()->setCellValue('DM1','f1740b');
$objPHPExcel->getActiveSheet()->setCellValue('DN1','f1741');
$objPHPExcel->getActiveSheet()->setCellValue('DO1','f1742b');
$objPHPExcel->getActiveSheet()->setCellValue('DP1','f1743');
$objPHPExcel->getActiveSheet()->setCellValue('DQ1','f1744b');
$objPHPExcel->getActiveSheet()->setCellValue('DR1','f1745');
$objPHPExcel->getActiveSheet()->setCellValue('DS1','f1746b');
$objPHPExcel->getActiveSheet()->setCellValue('DT1','f1747');
$objPHPExcel->getActiveSheet()->setCellValue('DU1','f1748b');
$objPHPExcel->getActiveSheet()->setCellValue('DV1','f1749');
$objPHPExcel->getActiveSheet()->setCellValue('DW1','f1750b');
$objPHPExcel->getActiveSheet()->setCellValue('DX1','f1751');
$objPHPExcel->getActiveSheet()->setCellValue('DY1','f1752b');
$objPHPExcel->getActiveSheet()->setCellValue('DZ1','f1753');
$objPHPExcel->getActiveSheet()->setCellValue('EA1','f1754b');
			$i=2;
			$n=1;
			foreach ($dataKuis as $data){
				$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$i,$data['kdptimsmh'],PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$i,$data['kdpstmsmh'],PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$data['nimhsmsmh']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$data['nmmhsmsmh']);
				$objPHPExcel->getActiveSheet()->setCellValueExplicit('E'.$i,$data['telpomsmh'],PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$data['emailmsmh']);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$data['tahun_lulus']);
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$data['f21']);
$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$data['f22']);
$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$data['f23']);
$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,$data['f24']);
$objPHPExcel->getActiveSheet()->setCellValue('L'.$i,$data['f25']);
$objPHPExcel->getActiveSheet()->setCellValue('M'.$i,$data['f26']);
$objPHPExcel->getActiveSheet()->setCellValue('N'.$i,$data['f27']);
$objPHPExcel->getActiveSheet()->setCellValue('O'.$i,$data['f301']);
$objPHPExcel->getActiveSheet()->setCellValue('P'.$i,$data['f302']);
$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i,$data['f303']);
$objPHPExcel->getActiveSheet()->setCellValue('R'.$i,$data['f401']);
$objPHPExcel->getActiveSheet()->setCellValue('S'.$i,$data['f402']);
$objPHPExcel->getActiveSheet()->setCellValue('T'.$i,$data['f403']);
$objPHPExcel->getActiveSheet()->setCellValue('U'.$i,$data['f404']);
$objPHPExcel->getActiveSheet()->setCellValue('V'.$i,$data['f405']);
$objPHPExcel->getActiveSheet()->setCellValue('W'.$i,$data['f406']);
$objPHPExcel->getActiveSheet()->setCellValue('X'.$i,$data['f407']);
$objPHPExcel->getActiveSheet()->setCellValue('Y'.$i,$data['f408']);
$objPHPExcel->getActiveSheet()->setCellValue('Z'.$i,$data['f409']);
$objPHPExcel->getActiveSheet()->setCellValue('AA'.$i,$data['f410']);
$objPHPExcel->getActiveSheet()->setCellValue('AB'.$i,$data['f411']);
$objPHPExcel->getActiveSheet()->setCellValue('AC'.$i,$data['f412']);
$objPHPExcel->getActiveSheet()->setCellValue('AD'.$i,$data['f413']);
$objPHPExcel->getActiveSheet()->setCellValue('AE'.$i,$data['f414']);
$objPHPExcel->getActiveSheet()->setCellValue('AF'.$i,$data['f415']);
$objPHPExcel->getActiveSheet()->setCellValue('AG'.$i,$data['f416']);
$objPHPExcel->getActiveSheet()->setCellValue('AH'.$i,$data['f6']);
$objPHPExcel->getActiveSheet()->setCellValue('AI'.$i,$data['f501']);
$objPHPExcel->getActiveSheet()->setCellValue('AJ'.$i,$data['f502']);
$objPHPExcel->getActiveSheet()->setCellValue('AK'.$i,$data['f503']);
$objPHPExcel->getActiveSheet()->setCellValue('AL'.$i,$data['f7']);
$objPHPExcel->getActiveSheet()->setCellValue('AM'.$i,$data['f7a']);
$objPHPExcel->getActiveSheet()->setCellValue('AN'.$i,$data['f8']);
$objPHPExcel->getActiveSheet()->setCellValue('AO'.$i,$data['f901']);
$objPHPExcel->getActiveSheet()->setCellValue('AP'.$i,$data['f902']);
$objPHPExcel->getActiveSheet()->setCellValue('AQ'.$i,$data['f903']);
$objPHPExcel->getActiveSheet()->setCellValue('AR'.$i,$data['f904']);
$objPHPExcel->getActiveSheet()->setCellValue('AS'.$i,$data['f905']);
$objPHPExcel->getActiveSheet()->setCellValue('AT'.$i,$data['f906']);
$objPHPExcel->getActiveSheet()->setCellValue('AU'.$i,$data['f1001']);
$objPHPExcel->getActiveSheet()->setCellValue('AV'.$i,$data['f1002']);
$objPHPExcel->getActiveSheet()->setCellValue('AW'.$i,$data['f1101']);
$objPHPExcel->getActiveSheet()->setCellValue('AX'.$i,$data['f1102']);
$objPHPExcel->getActiveSheet()->setCellValue('AY'.$i,$data['f1201']);
$objPHPExcel->getActiveSheet()->setCellValue('AZ'.$i,$data['f1202']);
$objPHPExcel->getActiveSheet()->setCellValue('BA'.$i,$data['f1301']);
$objPHPExcel->getActiveSheet()->setCellValue('BB'.$i,$data['f1302']);
$objPHPExcel->getActiveSheet()->setCellValue('BC'.$i,$data['f1303']);
$objPHPExcel->getActiveSheet()->setCellValue('BD'.$i,$data['f14']);
$objPHPExcel->getActiveSheet()->setCellValue('BE'.$i,$data['f15']);
$objPHPExcel->getActiveSheet()->setCellValue('BF'.$i,$data['f1601']);
$objPHPExcel->getActiveSheet()->setCellValue('BG'.$i,$data['f1602']);
$objPHPExcel->getActiveSheet()->setCellValue('BH'.$i,$data['f1603']);
$objPHPExcel->getActiveSheet()->setCellValue('BI'.$i,$data['f1604']);
$objPHPExcel->getActiveSheet()->setCellValue('BJ'.$i,$data['f1605']);
$objPHPExcel->getActiveSheet()->setCellValue('BK'.$i,$data['f1606']);
$objPHPExcel->getActiveSheet()->setCellValue('BL'.$i,$data['f1607']);
$objPHPExcel->getActiveSheet()->setCellValue('BM'.$i,$data['f1608']);
$objPHPExcel->getActiveSheet()->setCellValue('BN'.$i,$data['f1609']);
$objPHPExcel->getActiveSheet()->setCellValue('BO'.$i,$data['f1610']);
$objPHPExcel->getActiveSheet()->setCellValue('BP'.$i,$data['f1611']);
$objPHPExcel->getActiveSheet()->setCellValue('BQ'.$i,$data['f1612']);
$objPHPExcel->getActiveSheet()->setCellValue('BR'.$i,$data['f1613']);
$objPHPExcel->getActiveSheet()->setCellValue('BS'.$i,$data['f1614']);
$objPHPExcel->getActiveSheet()->setCellValue('BT'.$i,$data['f1701']);
$objPHPExcel->getActiveSheet()->setCellValue('BU'.$i,$data['f1702b']);
$objPHPExcel->getActiveSheet()->setCellValue('BV'.$i,$data['f1703']);
$objPHPExcel->getActiveSheet()->setCellValue('BW'.$i,$data['f1704b']);
$objPHPExcel->getActiveSheet()->setCellValue('BX'.$i,$data['f1705']);
$objPHPExcel->getActiveSheet()->setCellValue('BY'.$i,$data['f1705a']);
$objPHPExcel->getActiveSheet()->setCellValue('BZ'.$i,$data['f1706']);
$objPHPExcel->getActiveSheet()->setCellValue('CA'.$i,$data['f1706ba']);
$objPHPExcel->getActiveSheet()->setCellValue('CB'.$i,$data['f1707']);
$objPHPExcel->getActiveSheet()->setCellValue('CC'.$i,$data['f1708b']);
$objPHPExcel->getActiveSheet()->setCellValue('CD'.$i,$data['f1709']);
$objPHPExcel->getActiveSheet()->setCellValue('CE'.$i,$data['f1710b']);
$objPHPExcel->getActiveSheet()->setCellValue('CF'.$i,$data['f1711']);
$objPHPExcel->getActiveSheet()->setCellValue('CG'.$i,$data['f1711a']);
$objPHPExcel->getActiveSheet()->setCellValue('CH'.$i,$data['f1712b']);
$objPHPExcel->getActiveSheet()->setCellValue('CI'.$i,$data['f1712a']);
$objPHPExcel->getActiveSheet()->setCellValue('CJ'.$i,$data['f1713']);
$objPHPExcel->getActiveSheet()->setCellValue('CK'.$i,$data['f1714b']);
$objPHPExcel->getActiveSheet()->setCellValue('CL'.$i,$data['f1715']);
$objPHPExcel->getActiveSheet()->setCellValue('CM'.$i,$data['f1716b']);
$objPHPExcel->getActiveSheet()->setCellValue('CN'.$i,$data['f1717']);
$objPHPExcel->getActiveSheet()->setCellValue('CO'.$i,$data['f1718b']);
$objPHPExcel->getActiveSheet()->setCellValue('CP'.$i,$data['f1719']);
$objPHPExcel->getActiveSheet()->setCellValue('CQ'.$i,$data['f1720b']);
$objPHPExcel->getActiveSheet()->setCellValue('CR'.$i,$data['f1721']);
$objPHPExcel->getActiveSheet()->setCellValue('CS'.$i,$data['f1722b']);
$objPHPExcel->getActiveSheet()->setCellValue('CT'.$i,$data['f1723']);
$objPHPExcel->getActiveSheet()->setCellValue('CU'.$i,$data['f1724b']);
$objPHPExcel->getActiveSheet()->setCellValue('CV'.$i,$data['f1725']);
$objPHPExcel->getActiveSheet()->setCellValue('CW'.$i,$data['f1726b']);
$objPHPExcel->getActiveSheet()->setCellValue('CX'.$i,$data['f1727']);
$objPHPExcel->getActiveSheet()->setCellValue('CY'.$i,$data['f1728b']);
$objPHPExcel->getActiveSheet()->setCellValue('CZ'.$i,$data['f1729']);
$objPHPExcel->getActiveSheet()->setCellValue('DA'.$i,$data['f1730b']);
$objPHPExcel->getActiveSheet()->setCellValue('DB'.$i,$data['f1731']);
$objPHPExcel->getActiveSheet()->setCellValue('DC'.$i,$data['f1732b']);
$objPHPExcel->getActiveSheet()->setCellValue('DD'.$i,$data['f1733']);
$objPHPExcel->getActiveSheet()->setCellValue('DE'.$i,$data['f1734b']);
$objPHPExcel->getActiveSheet()->setCellValue('DF'.$i,$data['f1735']);
$objPHPExcel->getActiveSheet()->setCellValue('DG'.$i,$data['f1736b']);
$objPHPExcel->getActiveSheet()->setCellValue('DH'.$i,$data['f1737']);
$objPHPExcel->getActiveSheet()->setCellValue('DI'.$i,$data['f1737a']);
$objPHPExcel->getActiveSheet()->setCellValue('DJ'.$i,$data['f1738']);
$objPHPExcel->getActiveSheet()->setCellValue('DK'.$i,$data['f1738ba']);
$objPHPExcel->getActiveSheet()->setCellValue('DL'.$i,$data['f1739']);
$objPHPExcel->getActiveSheet()->setCellValue('DM'.$i,$data['f1740b']);
$objPHPExcel->getActiveSheet()->setCellValue('DN'.$i,$data['f1741']);
$objPHPExcel->getActiveSheet()->setCellValue('DO'.$i,$data['f1742b']);
$objPHPExcel->getActiveSheet()->setCellValue('DP'.$i,$data['f1743']);
$objPHPExcel->getActiveSheet()->setCellValue('DQ'.$i,$data['f1744b']);
$objPHPExcel->getActiveSheet()->setCellValue('DR'.$i,$data['f1745']);
$objPHPExcel->getActiveSheet()->setCellValue('DS'.$i,$data['f1746b']);
$objPHPExcel->getActiveSheet()->setCellValue('DT'.$i,$data['f1747']);
$objPHPExcel->getActiveSheet()->setCellValue('DU'.$i,$data['f1748b']);
$objPHPExcel->getActiveSheet()->setCellValue('DV'.$i,$data['f1749']);
$objPHPExcel->getActiveSheet()->setCellValue('DW'.$i,$data['f1750b']);
$objPHPExcel->getActiveSheet()->setCellValue('DX'.$i,$data['f1751']);
$objPHPExcel->getActiveSheet()->setCellValue('DY'.$i,$data['f1752b']);
$objPHPExcel->getActiveSheet()->setCellValue('DZ'.$i,$data['f1753']);
$objPHPExcel->getActiveSheet()->setCellValue('EA'.$i,$data['f1754b']);
				$n++;	
				$i++;				
			}
			
			$objPHPExcel->getActiveSheet()->getStyle('A1:EA'.($i-1))->applyFromArray($border);
			$objPHPExcel->getActiveSheet()->getStyle('A2:EA'.($i-1))->getFont()->setSize(10);
			
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
	    $menu = "tracerid/index2";
	    $getMenu = $user->cekUserMenu($menu);
	    if ($getMenu=="F"){
	        $this->_redirect('home/akses');
	    } else {
	        // treeview
	        $this->view->active_tree="14";
	        $this->view->active_menu="tracerid/index2";
	        // Title Browser
	        $this->view->title = "Rekap Mahasiswa Belum Mengisi";
	        // navigation
	        $this->_helper->navbar(0,0,0,0,0);
	        // destroy session param
	        Zend_Session::namespaceUnset('param_ntrc');
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
	    $menu = "tracerid/index2";
	    $getMenu = $user->cekUserMenu($menu);
	    if ($getMenu=="F"){
	        $this->_redirect('home/akses');
	    } else {
	        // treeview
	        $this->view->active_tree="14";
	        $this->view->active_menu="tracerid/index2";
	        // show data
	        $param = new Zend_Session_Namespace('param_ntrc');
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
	        $this->_helper->navbar('tracerid/index2',0,0,'tracerid/export2',0);
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
	        $getKuis=$kuis->queryKuisioner($prd,$arrThn);
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
			$arrMhs[$i]['email_lain']=$dtAlumni['email_lain'].",".$dtAlumni['email_kampus'];
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
	    $menu = "tracerid/index2";
	    $getMenu = $user->cekUserMenu($menu);
	    if ($getMenu=="F"){
	        $this->_redirect('home/akses');
	    } else {
	        // disabel layout
	        $this->_helper->layout->disableLayout();
	        // session
	        $param = new Zend_Session_Namespace('param_ntrc');
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
	        $objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
	        $objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
	        $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	        $objPHPExcel->getActiveSheet()->getStyle('A2:G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	        $objPHPExcel->getActiveSheet()->getStyle('A3:G3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	        $objPHPExcel->getActiveSheet()->getStyle('A:C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	        $objPHPExcel->getActiveSheet()->getStyle('D:F')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	        $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getFont()->setSize(14);
	        $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);
	        $objPHPExcel->getActiveSheet()->getStyle('A2:G2')->getFont()->setSize(12);
	        $objPHPExcel->getActiveSheet()->getStyle('A2:G2')->getFont()->setBold(true);
	        $objPHPExcel->getActiveSheet()->getStyle('A3:G3')->getFont()->setBold(true);
	        $objPHPExcel->getActiveSheet()->getStyle('A3:G3')->getFont()->setSize(11);
	        $objPHPExcel->getActiveSheet()->getStyle('A3:G3')->applyFromArray($cell_color);
	        
	        // column width
	        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(45);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
	        // insert data to excel
	        $objPHPExcel->getActiveSheet()->setCellValue('A1','DAFTAR LULUSAN BELUM MENGISI TRACER STUDY '.strtoupper($nm_pt));
	        $objPHPExcel->getActiveSheet()->setCellValue('A2','PROGRAM STUDI : '.$prd.", ANGKATAN : ".$akt);
	        $objPHPExcel->getActiveSheet()->setCellValue('A3','NO');
	        $objPHPExcel->getActiveSheet()->setCellValue('B3','NIM');
	        $objPHPExcel->getActiveSheet()->setCellValue('C3','NAMA MAHASISWA');
	        $objPHPExcel->getActiveSheet()->setCellValue('D3','TANGGAL LULUS');
	        $objPHPExcel->getActiveSheet()->setCellValue('E3','ANGKATAN');
	        $objPHPExcel->getActiveSheet()->setCellValue('F3','PRODI');
		$objPHPExcel->getActiveSheet()->setCellValue('G3','EMAIL');
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
		    $objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$data['email_lain']);
	            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->getAlignment()->setWrapText(true);
	            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
	            $n++;
	            $i++;
	        }
	        $objPHPExcel->getActiveSheet()->getStyle('A3:G'.($i-1))->applyFromArray($border);
	        $objPHPExcel->getActiveSheet()->getStyle('A4:G'.($i-1))->getFont()->setSize(10);
	        
	        // Redirect output to a clientâ€™s web browser (Excel5)
	        header('Content-Type: application/vnd.ms-excel');
	        header('Content-Disposition: attachment;filename="Daftar Lulusan.xls"');
	        header('Cache-Control: max-age=0');
	        
	        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	        $objWriter->save('php://output');
	    }
	}
}
