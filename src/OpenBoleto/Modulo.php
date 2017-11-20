<?php

namespace OpenBoleto;

/**
 *   Autor:
 *           Pablo Costa <pablo@users.sourceforge.net>
 *
 *   Função:
 *    Calculo do Modulo 11 para geracao do digito verificador
 *    de boletos bancarios conforme documentos obtidos
 *    da Febraban - www.febraban.org.br
 *
 *   Entrada:
 *     $num: string numérica para a qual se deseja calcularo digito verificador;
 *     $base: valor maximo de multiplicacao [2-$base]
 *     $r: quando especificado um devolve somente o resto
 *
 *   Saída:
 *     Retorna o Digito verificador.
 *
 *   Observações:
 *     - Script desenvolvido sem nenhum reaproveitamento de código pré existente.
 *     - Assume-se que a verificação do formato das variáveis de entrada é feita antes da execução deste script.
 */
class Modulo
{
    public static function modulo11($num, $base = 9, $r = 0)
    {


        $soma = 0;
        $fator = 2;

        /* Separacao dos numeros */
        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num, $i - 1, 1);
            // Efetua multiplicacao do numero pelo falor
            $parcial[$i] = $numeros[$i] * $fator;
            // Soma dos digitos
            $soma += $parcial[$i];
            if ($fator == $base) {
                // restaura fator de multiplicacao para 2
                $fator = 1;
            }
            $fator++;
        }

        /* Calculo do modulo 11 */
        if ($r == 0) {
            $soma *= 10;
            $digito = $soma % 11;
            if ($digito == 10) {
                $digito = 0;
            }
            return $digito;
        } elseif ($r == 1) {
            $resto = $soma % 11;
            return $resto;
        }
    }

    public static function modulo10($num)
    {
        $numtotal10 = 0;
        $fator = 2;

        // Separacao dos numeros
        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num, $i - 1, 1);
            // Efetua multiplicacao do numero pelo (falor 10)
            // 2002-07-07 01:33:34 Macete para adequar ao Mod10 do Itaú
            $temp = $numeros[$i] * $fator;
            $temp0 = 0;
            foreach (preg_split('//', $temp, -1, PREG_SPLIT_NO_EMPTY) as $k => $v) {
                $temp0 += $v;
            }
            $parcial10[$i] = $temp0; //$numeros[$i] * $fator;
            // monta sequencia para soma dos digitos no (modulo 10)
            $numtotal10 += $parcial10[$i];
            if ($fator == 2) {
                $fator = 1;
            } else {
                $fator = 2; // intercala fator de multiplicacao (modulo 10)
            }
        }

        // várias linhas removidas, vide função original
        // Calculo do modulo 10
        $resto = $numtotal10 % 10;
        $digito = 10 - $resto;
        if ($resto == 0) {
            $digito = 0;
        }

        return $digito;

    }
} 