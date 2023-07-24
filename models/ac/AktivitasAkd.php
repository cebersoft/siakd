<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: aktivitas akademik - Modeling untuk master aktivitas akademik
*/
class AktivitasAkd extends Zend_Db_Table
{
    protected $_name = 'acc.v_aktivitas_akd';
	protected $_primary='kd_aktivitas';
}