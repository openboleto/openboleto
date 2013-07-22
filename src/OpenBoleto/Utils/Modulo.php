<?php

/*
 * OpenBoleto - Geração de boletos bancários em PHP
 *
 * LICENSE: The MIT License (MIT)
 *
 * Copyright (C) 2013 Estrada Virtual
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this
 * software and associated documentation files (the "Software"), to deal in the Software
 * without restriction, including without limitation the rights to use, copy, modify,
 * merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies
 * or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace OpenBoleto\Utils;

/**
 * Classe Helper para cálculo do Modulo 10 e 11.
 *
 * @package    OpenBoleto
 * @author     Daniel Garajau <http://github.com/kriansa>
 * @copyright  Copyright (c) 2013 Estrada Virtual (http://www.estradavirtual.com.br)
 * @license    MIT License
 * @version    0.1
 */
class Modulo
{
    /**
     * Calcula e retorna o dígito verificador usando o algoritmo Modulo 10
     *
     * @param string $num
     * @see Documentação em http://www.febraban.org.br/Acervo1.asp?id_texto=195&id_pagina=173&palavra=
     * @return int
     */
    public static function dez($num)
    {
        $numtotal10 = 0;
        $fator = 2;

        //  Separacao dos numeros.
        for ($i = strlen($num); $i > 0; $i--) {
            //  Pega cada numero isoladamente.
            $numeros[$i] = substr($num,$i-1,1);
            //  Efetua multiplicacao do numero pelo (falor 10).
            $temp = $numeros[$i] * $fator;
            $temp0=0;
            foreach (preg_split('// ',$temp,-1,PREG_SPLIT_NO_EMPTY) as $v){ $temp0+=$v; }
            $parcial10[$i] = $temp0; // $numeros[$i] * $fator;
            //  Monta sequencia para soma dos digitos no (modulo 10).
            $numtotal10 += $parcial10[$i];
            if ($fator == 2) {
                $fator = 1;
            }
            else {
                // Intercala fator de multiplicacao (modulo 10).
                $fator = 2;
            }
        }

        $remainder  = $numtotal10 % 10;
        $digito = 10 - $remainder;

        // Make it zero if check digit is 10.
        $digito = ($digito == 10) ? 0 : $digito;

        return $digito;
    }

    /**
     * Calcula e retorna o dígito verificador usando o algoritmo Modulo 11
     *
     * @param string $num
     * @param int $base
     * @see Documentação em http://www.febraban.org.br/Acervo1.asp?id_texto=195&id_pagina=173&palavra=
     * @return array Retorna um array com as chaves 'digito' e 'resto'
     */
    public static function onze($num, $base=9)
    {
        $fator = 2;

        $soma  = 0;
        // Separacao dos numeros.
        for ($i = strlen($num); $i > 0; $i--) {
            //  Pega cada numero isoladamente.
            $numeros[$i] = substr($num,$i-1,1);
            //  Efetua multiplicacao do numero pelo falor.
            $parcial[$i] = $numeros[$i] * $fator;
            //  Soma dos digitos.
            $soma += $parcial[$i];
            if ($fator == $base) {
                //  Restaura fator de multiplicacao para 2.
                $fator = 1;
            }
            $fator++;
        }
        $result = array(
            'digito' => ($soma * 10) % 11,
            // Remainder.
            'resto'  => $soma % 11,
        );
        if ($result['digito'] == 10){
            $result['digito'] = 0;
        }
        return $result;
    }
}
