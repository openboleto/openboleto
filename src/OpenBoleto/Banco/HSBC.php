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

namespace OpenBoleto\Banco;

use OpenBoleto\BoletoAbstract;
use OpenBoleto\Exception;

/**
 * Classe boleto Banco Do Nordeste
 *
 * @package    OpenBoleto
 * @author     Rauye Rogiski <http://github.com/rauye>
 * @license    MIT License
 * @version    1.0
 */
class HSBC extends BoletoAbstract
{
    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '399';

    /**
     * Localização do logotipo do banco, referente ao diretório de imagens
     * @var string
     */
    protected $logoBanco = 'hsbc.jpg';

    /**
     * Linha de local de pagamento
     * @var string
     */
    protected $localPagamento = 'Pagar em qualquer banco até o vencimento ou canais eletrônicos HSBC';

    /**
     * Define as carteiras disponíveis para este banco
     * @var array
     */
    protected $carteiras = array('CNR', 'CNR-FACIL', 'CSB', 'CSBE');

    /**
     * Calculo de Modulo 11 "Invertido" (com pesos de 9 a 2 e não de 2 a 9)
     * @return int
     */
    protected function modulo11Invertido($num)
    {
        $ftini = 2;
        $ftfim = 9;
        $fator = $ftfim;
        $soma = 0;

        for($i = strlen ( $num ); $i > 0; $i --)
        {
            $soma += substr ( $num, $i - 1, 1 ) * $fator;
            if (-- $fator < $ftini)
                $fator = $ftfim;
        }

        $digito = $soma % 11;
        if ($digito > 9)
            $digito = 0;

        return $digito;
    }

    /**
     * Gera o Nosso Número
     *
     * @return string
     */
    protected function gerarNossoNumero($semDv = false)
    {
        $numero = $this->sequencial;
        if ($semDv) {
            return (int) $numero;
        }

        $venc = $this->dataVencimento->format('dmy');
        $cedente = static::zeroFill($this->conta, 7);
        $numero = $numero . $this->modulo11Invertido($numero) . 4;
        $res = $numero + $cedente + $venc;

        return $numero . $this->modulo11Invertido($res);
    }

    /**
     * Retorna a data de vencimento no formato juliano
     *
     * @return string
     */
    protected function getDataVencJuliana()
    {
        $data = $this->getDataVencimento();
        $ano = $data->format('Y');
        $dataf = strtotime($data->format('Y/m/d'));
        $datai = strtotime(($ano - 1) . '/12/31');
        $dias = (int) (($dataf - $datai) / (60 * 60 * 24));
        return str_pad($dias, 3, '0', STR_PAD_LEFT) . substr($ano, 3, 1);
    }

    /**
     * Método para gerar o código da posição de 20 a 44
     *
     * @return string
     * @throws \OpenBoleto\Exception
     */
    public function getCampoLivre()
    {
        return self::zeroFill(substr($this->getConta(), 0, 4), 4) .
            str_pad(substr($this->getConta(), 4), 7, '0', STR_PAD_RIGHT) .
            self::zeroFill($this->getContaDv(), 1) .
            self::zeroFill($this->gerarNossoNumero(true), 8) .
            self::zeroFill($this->getDataVencJuliana(), 4) .
            '2' ;
    }
}
