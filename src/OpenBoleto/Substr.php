<?php
namespace OpenBoleto;

class Substr
{
    public static function esquerda($entra, $comp)
    {
        return substr($entra, 0, $comp);
    }

    public static function direita($entra, $comp)
    {
        return substr($entra, strlen($entra) - $comp, $comp);
    }
}