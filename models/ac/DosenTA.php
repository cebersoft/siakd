<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Dosen TA - Modeling untuk master dosen TA
*/
class DosenTA extends Zend_Db_Table
{
    protected $_name = 'acc.v_dosen_ta';
	protected $_primary='kd_dosen';
}