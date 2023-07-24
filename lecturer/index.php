<?php
error_reporting(E_ALL|E_STRICT);
date_default_timezone_set('Asia/Jakarta');
set_include_path('.' . PATH_SEPARATOR . '../library/'
	  . PATH_SEPARATOR . '../models/ac'
	  . PATH_SEPARATOR . '../models/sys'
	  . PATH_SEPARATOR . '../models/pt'
	  . PATH_SEPARATOR . '../models/ref'
	  . PATH_SEPARATOR . '../models/feeder'
	  . PATH_SEPARATOR . '../models/fin'
          . PATH_SEPARATOR . '../models/lib'
	  . PATH_SEPARATOR . '../models/lms'
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

// db library
$config2 = new Zend_Config_Ini('../lib.ini', 'general');
$registry2 = Zend_Registry::getInstance();
$registry2->set('config', $config2);
$db2 = Zend_Db::factory($config2->db->adapter, $config2->db->config->toArray());
Zend_Registry::set('dbAdapter2', $db2);

// path outside root
$path = __FILE__;
$filePath = str_replace('lecturer\index.php','\\',$path);
Zend_Registry::set('pathOutside', $filePath);

// ZendX
$view = new Zend_View();
$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
$view->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');
$viewRenderer->setView($view);
Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
// helpers
Zend_Controller_Action_HelperBroker::addPath('./application/helpers');

Zend_Registry::set('FILE_URL', $filePath.'public/file');

// setup controller
$baseUrl = substr($_SERVER['PHP_SELF'], 0,strpos($_SERVER['PHP_SELF'], '/index.php'));
$frontController = Zend_Controller_Front::getInstance();
$frontController->setControllerDirectory('./application/controllers/');
// run!
$frontController->dispatch();