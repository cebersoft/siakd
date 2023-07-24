<?php
/*
	Programmer	: Tiar Aristian
	Release		: Agustus 2016
	Module		: Term pembayaran - Modeling untuk term pembayaran
*/
class Term extends Zend_Db_Table
{
    protected $_name = 'fin.v_term';
	protected $_primary='id_term';
}