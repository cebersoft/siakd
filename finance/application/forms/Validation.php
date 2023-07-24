<?php 
class Validation {
	function validasiAlpha($param){
		Zend_Loader::loadClass('Zend_Validate_Alpha');
		$validator = new Zend_Validate_Alpha(array('allowWhiteSpace' => true));
		if ($validator->isValid($param)) {
		    return 'T';
		} else {
		    return 'F';
		}
	}

	function validasiAlNum($param){
		Zend_Loader::loadClass('Zend_Validate_Alnum');
		$validator = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));
		if ($validator->isValid($param)) {
		    return 'T';
		} else {
		    return 'F';
		}	
	}
	
	function validasiAlNumNoSpace($param){
		Zend_Loader::loadClass('Zend_Validate_Alnum');
		$validator = new Zend_Validate_Alnum(array('allowWhiteSpace' => false));
		if ($validator->isValid($param)) {
		    return 'T';
		} else {
		    return 'F';
		}	
	}

	function validasiLength($param,$min,$max){
		Zend_Loader::loadClass('Zend_Validate_StringLength');
		$validator = new Zend_Validate_StringLength(array('min' => $min, 'max' => $max));
		if($validator->isValid($param)){
			return 'T';
		}else{
			return 'F';
		}
	}

	function validasiEmail($param){
		Zend_Loader::loadClass('Zend_Validate_EmailAddress');
		$validator = new Zend_Validate_EmailAddress();
		if ($validator->isValid($param)) {
		    return 'T';
		} else {
		    return 'F';
		}
	}

	function validasiBetween($param,$min,$max){
		Zend_Loader::loadClass('Zend_Validate_Between');
		$validator  = new Zend_Validate_Between(array('min' => $min, 'max' => $max));
		if ($validator->isValid($param)) {
		    return 'T';
		} else {
		    return 'F';
		}
	}

	function validasiDate($param){
		Zend_Loader::loadClass('Zend_Validate_Date');
		$validator  = new Zend_Validate_Date('dd/mm/yyyy');
		if ($validator->isValid($param)) {
		    return 'T';
		} else {
		    return 'F';
		}	
	}

	function validasiTime($param){
		Zend_Loader::loadClass('Zend_Validate_Date');
		$validator  = new Zend_Validate_Date(array("format" => 'H:m'));
		if ($validator->isValid($param)) {
		    return 'T';
		} else {
		    return 'F';
		}	
	}

	function validasiRegex($param,$pattern){
		Zend_Loader::loadClass('Zend_Validate_Regex');
		$validator  = new Zend_Validate_Regex($pattern);
		if ($validator->isValid($param)) {
		    return 'T';
		} else {
		    return 'F';
		}		
	}

	function validasiRegexNama($param){
		// escape \' to '
		Zend_Loader::loadClass('Zend_Validate_Regex');
		$validator  = new Zend_Validate_Regex("/^([a-zA-Z ' . , ( ) ]*)$/");
		if ($validator->isValid($param)) {
		    return 'T';
		} else {
		    return 'F';
		}		
	}
	
	function validasiDigit($param){
		Zend_Loader::loadClass('Zend_Validate_Digits');
		$validator = new Zend_Validate_Digits();
		if($validator->isValid($param)){
			return 'T';
		}else{
			return 'F';
		}
	}
}