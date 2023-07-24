<?php

class Zend_Controller_Action_Helper_String extends Zend_Controller_Action_Helper_Abstract
{
	function direct(){
		return;
	}

	public function esc_quote($param){
		$param = addslashes($param);
		$param = str_replace("\'","''",$param);
		$param = str_replace('\"','"',$param);
		return $param;
	}
}
?>
