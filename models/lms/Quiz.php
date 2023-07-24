<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Quiz - Modeling untuk quiz
*/
class Quiz extends Zend_Db_Table
{

	function getQuiz0ByPaket($kd_paket){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_quiz0_fby_paket_kelas('$kd_paket')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getQuiz0ByKelompok($kel){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_quiz0_fby_kelompok('$kel')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getQuiz0ById($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_quiz0_fby_id('$id')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setQuiz0($kd_paket,$kel,$nm,$tgl,$time1,$time2,$kd_dsn,$rps,$param){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from lms.f_quiz0_ins('$kd_paket','$kel','$nm','$tgl','$time1','$time2','$kd_dsn','$rps','$param')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_quiz0_ins'];
		}
		return $return;
	}

	function delQuiz0($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from lms.f_quiz0_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_quiz0_del'];
		}
		return $return;
	}

	function getQuiz1ByQuiz0($id_quiz0){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_quiz1_fby_quiz0('$id_quiz0')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getQuiz1ById($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_quiz1_fby_id('$id')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setQuiz1($id_quiz0,$quest,$img,$ord){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from lms.f_quiz1_ins('$id_quiz0','$quest','$img',$ord)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_quiz1_ins'];
		}
		return $return;
	}

	function delQuiz1($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from lms.f_quiz1_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_quiz1_del'];
		}
		return $return;
	}

	function updJawabanQuiz1($id,$jawaban){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from lms.f_quiz1_jawaban_upd('$id','$jawaban')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_quiz1_jawaban_upd'];
		}
		return $return;
	}

	function getQuiz2ByQuiz1($id_quiz1){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_quiz2_fby_quiz1('$id_quiz1')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getQuiz2ById($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_quiz2_fby_id('$id')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function setQuiz2($id_quiz1,$choice,$img,$ord){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from lms.f_quiz2_ins('$id_quiz1','$choice','$img',$ord)");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_quiz2_ins'];
		}
		return $return;
	}

	function delQuiz2($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from lms.f_quiz2_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_quiz2_del'];
		}
		return $return;
	}


}