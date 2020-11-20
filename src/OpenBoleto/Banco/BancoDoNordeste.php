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
class BancoDoNordeste extends BoletoAbstract
{
    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '004';

    /**
     * Localização do logotipo do banco, referente ao diretório de imagens
     * @var string
     */
    protected $logoBanco = 'bnb.jpg';

    /**
     * Linha de local de pagamento
     * @var string
     */
    protected $localPagamento = 'Pagável preferencialmente no Banco do Nordeste até o vencimento';

    /**
     * Define as carteiras disponíveis para este banco
     * @var array
     */
    protected $carteiras = array('47','45','46','50','45','46','04','48','97','51','49','52','58','95','63','53','54','55','57','59','61');

    /**
     * Gera o Nosso Número com o dígito verificador
     *
     * @return string
     */
    protected function gerarNossoNumero()
    {
        $numero = static::zeroFill($this->sequencial, 7);
        $resto = static::modulo11($numero, 8)['resto'];
        $dv = 0;

        if ($resto > 1) {
            $dv = 11 - $resto;
        }

        return $numero . '-' . $dv;
    }

    /**
     * Método para gerar o código da posição de 20 a 44
     *
     * @return string
     * @throws \OpenBoleto\Exception
     */
    public function getCampoLivre()
    {
        return self::zeroFill($this->getAgencia(), 4) .
            self::zeroFill($this->getConta(), 7) .
            self::zeroFill($this->getContaDv(), 1) .
            self::zeroFill($this->getNossoNumero(false), 8) .
            self::zeroFill($this->getCarteira(), 2) .
            '000';
    }
}
