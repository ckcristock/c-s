<?php
if (!function_exists('roundInHalf')) {
    function roundInHalf($n)
    {
        $ent = floor($n); // Parte entera
        $dec = $n - $ent; // Parte decimal

        $r = $dec >= .5 ? .5 : 0;
        return ($ent + $r);
    }
}
