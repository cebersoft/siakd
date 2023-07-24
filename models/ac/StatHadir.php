<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: StatHadir - Modeling untuk master status kehadiran
*/
class StatHadir extends Zend_Db_Table
{
    protected $_name = 'acc.stat_kehadiran';
	protected $_primary='id_hadir';
}