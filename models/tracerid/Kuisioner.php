<?php

class Kuisioner
{    
	
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

	function setKuis($nim,$telp,$email,$thn,$val){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from trc.f_kuisioner01_insert('$nim','$telp','$email','$thn','$val')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kuisioner01_insert'];
		}
		return $return;
	}

	function setKuis3($nim,$telp,$email,$nik,$npwp,$thn,$val,$id0){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from trc.f_kuisioner03_insert('$nim','$telp','$email','$nik','$npwp','$thn','$val','$id0')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kuisioner03_insert'];
		}
		return $return;
	}

	function getKuisionerByNim($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from trc.f_kuisioner01_by_nim('$nim')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function queryKuisioner($prd,$thn){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$prd = $this->to_pg_array($prd);
		$thn = $this->to_pg_array($thn);
		$stmt=$db->query("select * from trc.f_kuisioner01_query('$prd','$thn')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function delKuis($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from trc.f_kuisioner01_delete('$nim')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kuisioner01_delete'];
		}
		return $return;
	}
	
	function getQuestion(){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from 
					(select * from trc.question0 where now() >= question0_startdate 
 						order by question0_startdate limit 1) a
					left join trc.question1 b on a.question0_id=b.question0_id
				order by b.question1_ord");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getChoiceByQuestion1($q1){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from trc.choice where question1_id='$q1' order by choice_ord, choice_code");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getChoiceCodeByQuestion0($q0){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("
			(select a.choice_code,b.question1_ord 
				from trc.choice a 
				left join trc.question1 b on a.question1_id = b.question1_id
				where b.question0_id='$q0'
				group by choice_code,b.question1_ord
			)
			union
			(select a.choice_ext_code as choice_code, b.question1_ord 
				from trc.choice a 
				left join trc.question1 b on a.question1_id = b.question1_id
				where b.question0_id='$q0' and a.choice_ext_code NOTNULL
				group by choice_ext_code,b.question1_ord
			)
			order by question1_ord, choice_code
		");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getKuisionerNewByNim($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from trc.f_kuisioner_new_by_nim('$nim')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function queryNewKuisioner($prd,$thn){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$prd = $this->to_pg_array($prd);
		$thn = $this->to_pg_array($thn);
		$stmt=$db->query("select * from trc.f_kuisioner_new_query('$prd','$thn')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function queryQuestion0NewKuisioner($prd,$thn){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$prd = $this->to_pg_array($prd);
		$thn = $this->to_pg_array($thn);
		$stmt=$db->query("select distinct question0_id from trc.f_kuisioner_new_query('$prd','$thn')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function delNewKuis($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from trc.f_kuisioner03_delete('$nim')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_kuisioner03_delete'];
		}
		return $return;
	}
}