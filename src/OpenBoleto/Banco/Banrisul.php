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
 * Classe boleto Banrisul
 *
 * @package    OpenBoleto
 * @author     Rauye Rogiski <http://github.com/rauye>
 * @license    MIT License
 * @version    1.0
 */
class Banrisul extends BoletoAbstract
{
    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '041';

    /**
     * Localização do logotipo do banco, referente ao diretório de imagens
     * @var string
     */
    protected $logoBanco = 'banrisul.jpg';

    /**
     * Linha de local de pagamento
     * @var string
     */
    protected $localPagamento = 'Pagável preferencialmente no Banrisul até o vencimento';

    /**
     * Define o tipo da cobrança: 1 Cobrança Normal, Fichário emitido pelo BANRISUL; 2 Cobrança Direta, Fichário emitido pelo CLIENTE
     * @var int
     */
    protected $tipoCobranca = 2;

    /**
     * @return int
     */
    public function getTipoCobranca(): int
    {
        return $this->tipoCobranca;
    }

    /**
     * @param int $tipoCobranca
     */
    public function setTipoCobranca(int $tipoCobranca)
    {
        $this->tipoCobranca = $tipoCobranca;
    }

    /**
     * Gera o Nosso Número.
     *
     * @return string
     */
    protected function gerarNossoNumero()
    {
        return self::zeroFill($this->getSequencial(), 8);
    }

    /**
     * Gera o dígito verificador duplo
     *
     * @return string
     */
    protected function gerarDigitoVerificadorDuplo()
    {
        $sequencial = self::zeroFill($this->getSequencial(), 8);

        $dv1 = static::modulo10($sequencial);
        $resto2 = static::modulo11($sequencial . $dv1, 7)['resto'];

        if ($resto2 == 1) {
            $dv1++;
            if ($dv1 == 10) {
                $dv1 = '0';
            }
            $resto2 = static::modulo11($sequencial . $dv1,7)['resto'];
        }

        $digito = 11 - $resto2;

        if ($digito > 9) {
            $dv2 = 0;
        } else {
            $dv2 = $digito;
        }

        return $dv1 . $dv2;
    }

    /**
     * Método para gerar o código da posição de 20 a 44
     *
     * @return string
     * @throws \OpenBoleto\Exception
     */
    public function getCampoLivre()
    {
        return self::zeroFill($this->getTipoCobranca(), 1) .
            1 .
            self::zeroFill($this->getAgencia(), 4) .
            self::zeroFill($this->getConta(), 7) .
            self::zeroFill($this->getNossoNumero(), 8) .
            40 .
            self::zeroFill($this->gerarDigitoVerificadorDuplo(), 2);
    }
}
