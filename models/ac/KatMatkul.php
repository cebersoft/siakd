<?php
/*
	Programmer	: Tiar Aristian
	Release		: Januari 2016
	Module		: Kat Matkul - Modeling untuk master kategori mata kuliah
*/
class KatMatkul extends Zend_Db_Table
{
    protected $_name = 'acc.v_kat_matkul';
	protected $_primary='id_kat_mk';
}