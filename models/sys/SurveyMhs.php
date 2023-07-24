<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Survey mahasiswa tentang sistem
*/
class SurveyMhs extends Zend_Db_Table
{
	
	function getSurveyById($id_survey){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from surv.survey_mhs0 where id_survey='$id_survey'");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function getSurveyDtlByNim($nim){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$stmt=$db->query("select * from surv.survey_mhs1 where nim='$nim'");
		$data=$stmt->fetchAll();
		return $data;
	}
	
	function setSurveyDtl($id_survey,$nim,$nilai,$komen){
		// database
		$db=Zend_Registry::get('dbAdapter');
		$query=$db->query("select * from surv.f_survey_mhs1_ins('$id_survey','$nim',$nilai,'$komen')");
		$isset=$query->fetchAll();
		foreach ($isset as $returnData) {
			$return=$returnData['f_survey_mhs1_ins'];
		}
		return $return;
	}
}