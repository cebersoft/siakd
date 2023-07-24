<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Stat Periode - Modeling untuk master status periode akademik
*/
class StatPeriode extends Zend_Db_Table
{
    protected $_name = 'acc.v_status_periode';
	protected $_primary='id_status_periode';
}