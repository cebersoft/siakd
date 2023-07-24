<?php
class Soaps
{
    /**
     * Penambahan method
     *
     * @param integer $bilpertama
     * @param integer $bilkedua
     * @return integer
     */
    function tambah($bilpertama,$bilkedua)
    {
        $return = $bilpertama + $bilkedua;
        return  $return ;
    }
    
    /**
     * Pengurangan method
     *
     * @param int $bilpertama
     * @param int $bilkedua
     * @return int
     */
    function kurang($bilpertama,$bilkedua)
    {
        return $bilpertama - $bilkedua;
    }
    
    /**
     * Hello world
     *
     * @param string $word
     * @return string
     */
    
    function hello($word) {
        return $word;
    }
}