<?php
error_reporting(E_ALL|E_STRICT);
date_default_timezone_set('Asia/Jakarta');
set_include_path('.' . PATH_SEPARATOR . '../library/'
	  . PATH_SEPARATOR . '../models/ac'
	  . PATH_SEPARATOR . '../models/sys'
	  . PATH_SEPARATOR . '../models/pt'
	  . PATH_SEPARATOR . '../models/ref'
	  . PATH_SEPARATOR . '../models/fin'
	  . PATH_SEPARATOR . './application/forms/'
      . PATH_SEPARATOR . get_include_path());
include "Zend/Loader.php";
Zend_Loader::loadClass('Zend_Controller_Front');
Zend_Loader::loadClass('Zend_Config_Ini');
Zend_Loader::loadClass('Zend_Registry');
Zend_Loader::loadClass('Zend_Db');
Zend_Loader::loadClass('Zend_Db_Table');
Zend_Loader::loadClass('Zend_Layout');
Zend_Loader::loadClass('Zend_View');
Zend_Loader::loadClass('Zend_Form');
Zend_Loader::loadClass('Zend_Controller_Action_Helper_ViewRenderer');

// session
Zend_Loader::loadClass('Zend_Auth');

// load configuration
$config = new Zend_Config_Ini('../config.ini', 'general');
$registry = Zend_Registry::getInstance();
$registry->set('config', $config);

// Zend Layout
$options = array(
    //'layout'=> 'main',
    'layoutPath'=>'./application/layouts/scripts',
	'contentKey'=> 'content'
);
Zend_Layout::startMvc($options);

// setup database
$db = Zend_Db::factory($config->db->adapter, $config->db->config->toArray());
Zend_Db_Table::setDefaultAdapter($db);
Zend_Registry::set('dbAdapter', $db);

// path outside root
$path = __FILE__;
$filePath = str_replace('finance\index.php','\\',$path);
Zend_Registry::set('pathOutside', $filePath);

Zend_Registry::set('TEMP_URL', $filePath.'public/temp');

// ZendX
$view = new Zend_View();
$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
$view->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');
$viewRenderer->setView($view);
Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
// helpers
Zend_Controller_Action_HelperBroker::addPath('./application/helpers');

// setup controller
$baseUrl = substr($_SERVER['PHP_SELF'], 0,strpos($_SERVER['PHP_SELF'], '/index.php'));
$frontController = Zend_Controller_Front::getInstance();
$frontController->setControllerDirectory('./application/controllers/');
// run!
$frontController->dispatch();