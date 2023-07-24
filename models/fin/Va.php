<?php
class Va extends Zend_Db_Table
{
    protected $_name = 'fin.v_no_va';
    protected $_primary='no_va';
    
    function to_pg_array($set) {
    	settype($set, 'array');
    	$result = array();
    	foreach ($set as $t) {
    		if (is_array($t)) {
    			$result[] = to_pg_array($t);
    		} else {
    			$t = str_replace('"', '\\"', $t);
    			if (! is_numeric($t))
    				$t = '"' . $t . '"';
    				$result[] = $t;
    		}
    	}
    	return '{' . implode(",", $result) . '}';
    }
    
    function queryVa($no,$available,$bank){
    	// database
    	$db=Zend_Registry::get('dbAdapter');
    	$available = $this->to_pg_array($available);
	$bank = $this->to_pg_array($bank);
    	$stmt=$db->query("select * from fin.f_no_va_query('$no','$available','$bank')");
    	$data=$stmt->fetchAll();
    	return $data;
    }
    
    function getNoVaById($no_va){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $stmt=$db->query("select * from fin.f_no_va_by_id('$no_va')");
        $data=$stmt->fetchAll();
        return $data;
    }
    function setNoVa($no_va,$bank){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from fin.f_no_va_ins('$no_va','$bank')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_no_va_ins'];
        }
        return $return;
    }
    function updNoVa($new_no_va,$bank,$old_no_va){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from fin.f_no_va_upd('$new_no_va','$bank','$old_no_va')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_no_va_upd'];
        }
        return $return;
    }
    
    function delNoVa($no_va){
        // database
        $db=Zend_Registry::get('dbAdapter');
        $query=$db->query("select * from fin.f_no_va_del('$no_va')");
        $isset=$query->fetchAll();
        foreach ($isset as $returnData) {
            $return=$returnData['f_no_va_del'];
        }
        return $return;
    }
    
}