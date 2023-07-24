<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Kat DOsen - Modeling untuk master kategori dosen
*/
class KatDosen extends Zend_Db_Table
{
    protected $_name = 'acc.v_kat_dosen';
	protected $_primary='id_kat_dosen';
}