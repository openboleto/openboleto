<?php

namespace OpenBoleto;


class UnidadeMedida
{

    /**
     * Formula para converter milimetros em px (n = 25.4 * x / 96) n = milimetros, x  = px
     *
     */
    public static function px2milimetros($valor)
    {
        return ((25.4 * $valor) / 96);
    }
} 