<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Quiz Mahasiswa - Modeling untuk quiz mhs
*/
class QuizMhs extends Zend_Db_Table
{
	function getQuizMhs0ByQuiz0($id_quiz0){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_tugas_mhs_fby_tugas('$id_tugas')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function getQuzMhs0ByQuiz0Kuliah($id_quiz0,$kd_kuliah){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from lms.f_quiz_mhs0_fby_quiz0_kuliah('$id_quiz0','$kd_kuliah')");
		$data=$stmt->fetchAll();
		return $data;
	}

	function genQuiz0Mhs($kd_kuliah,$id_quiz0){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from lms.f_quiz_mhs0_gen('$kd_kuliah','$id_quiz0')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_quiz_mhs0_gen'];
		}
		return $return;
	}

	function delQuiz0Mhs($id){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from lms.f_quiz_mhs0_del('$id')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_quiz_mhs0_del'];
		}
		return $return;
	}

}