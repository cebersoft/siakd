<?php

class Zend_Controller_Action_Helper_Navbar extends Zend_Controller_Action_Helper_Abstract
{
	function direct($b,$e){
		$view = $this->getActionController()->view;
		if($b=='0'){$view->bdis='disabled="disabled"';}else{$view->lback=$b;}
		if($e=='0'){$view->edis='disabled="disabled"';}else{$view->lexport=$e;}
		return;
	}
}
?>