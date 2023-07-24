<?php

class Zend_Controller_Action_Helper_Navbar extends Zend_Controller_Action_Helper_Abstract
{
	function direct($b,$l,$n,$e,$i){
		$view = $this->getActionController()->view;
		if($b=='0'){$view->bdis='disabled="disabled"';}else{$view->lback=$b;}
		if($l=='0'){$view->ldis='disabled="disabled"';}else{$view->llist=$l;}
		if($n=='0'){$view->ndis='disabled="disabled"';}else{$view->lnew=$n;}
		if($e=='0'){$view->edis='disabled="disabled"';}else{$view->lexport=$e;}
		if($i=='0'){$view->idis='disabled="disabled"';}else{$view->limport=$i;}
		return;
	}
}
?>