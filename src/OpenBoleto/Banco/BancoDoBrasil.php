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
 * Classe boleto Banco do Brasil S/A.
 *
 * @package    OpenBoleto
 * @author     Daniel Garajau <http://github.com/kriansa>
 * @copyright  Copyright (c) 2013 Estrada Virtual (http://www.estradavirtual.com.br)
 * @license    MIT License
 * @version    1.0
 */
class BancoDoBrasil extends BoletoAbstract
{
    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '001';

    /**
     * Localização do logotipo do banco, referente ao diretório de imagens
     * @var string
     */
    protected $logoBanco = 'bb.jpg';

    /**
     * Linha de local de pagamento
     * @var string
     */
    protected $localPagamento = 'Pagável em qualquer Banco até o vencimento';

    /**
     * Define as carteiras disponíveis para este banco
     * @var array
     */
    protected $carteiras = array('31', '11', '16', '17', '18', '12', '51', '21');

    /**
     * Define o número do convênio (4, 6 ou 7 caracteres)
     * @var string
     */
    protected $convenio;

    /**
     * Define o número do convênio. Sempre use string pois a quantidade de caracteres é validada.
     *
     * @param string $convenio
     * @return BancoDoBrasil
     */
    public function setConvenio($convenio)
    {
        $this->convenio = $convenio;
        return $this;
    }

    /**
     * Retorna o número do convênio
     *
     * @return string
     */
    public function getConvenio()
    {
        return $this->convenio;
    }

    /**
     * Gera o Nosso Número.
     *
     * @throws Exception
     * @return string
     */
    protected function gerarNossoNumero()
    {
        $convenio = $this->getConvenio();
        $sequencial = $this->getSequencial();
        $numero = null;

        switch (strlen($convenio)) {
            // Convênio de 4 dígitos, são 11 dígitos no nosso número
            case 4:
                $numero = self::zeroFill($convenio, 4) . self::zeroFill($sequencial, 7);
                break;

            // Convênio de 6 dígitos, são 11 dígitos no nosso número
            case 6:
                // Exceto no caso de ter a carteira 21, onde são 17 dígitos
                if ($this->getCarteira() == 21) {
                    $numero = self::zeroFill($sequencial, 17);
                } else {
                    $numero = self::zeroFill($convenio, 6) . self::zeroFill($sequencial, 5);
                }
                break;

            // Convênio de 7 dígitos, são 17 dígitos no nosso número
            case 7:
                $numero = self::zeroFill($convenio, 7) . self::zeroFill($sequencial, 10);
                break;

            // Não é com 4, 6 ou 7 dígitos? Não existe.
            default:
                throw new Exception('O código do convênio precisa ter 4, 6 ou 7 dígitos!');
        }

        // Quando o nosso número tiver menos de 17 dígitos, colocar o dígito
        if (strlen($numero) < 17) {
            $modulo = static::modulo11($numero);
            $numero .= '-' . $modulo['digito'];
        }

        return $numero;
    }

    /**
     * Método para gerar o código da posição de 20 a 44
     *
     * @return string
     * @throws \OpenBoleto\Exception
     */
    public function getCampoLivre()
    {
        $length = strlen($this->getConvenio());
        $nossoNumero = $this->gerarNossoNumero();
        // Nosso número sem o DV - repare que ele só vem com DV quando o mesmo é menor que 17 caracteres
        // Então removemos o dígito (e o traço) apenas quando seu tamanho for menor que 17 caracteres
        strlen($this->getNossoNumero()) < 17 and $nossoNumero = substr($nossoNumero, 0, -2);

        // Sequencial do cliente com 17 dígitos
        // Apenas para convênio com 6 dígitos, modalidade sem registro - carteira 16 e 18 (definida para 21)
        if (strlen($this->getSequencial()) > 10) {
            if ($length == 6 and $this->getCarteira() == 21) {
                // Convênio (6) + Nosso número (17) + Carteira (2)
                return self::zeroFill($this->getConvenio(), 6) . $nossoNumero . '21';
            } else {
                throw new Exception('Só é possível criar um boleto com mais de 10 dígitos no nosso número quando a carteira é 21 e o convênio possuir 6 dígitos.');
            }
        }

        switch ($length) {
            case 4:
            case 6:
                // Nosso número (11) + Agencia (4) + Conta (8) + Carteira (2)
                return $nossoNumero . self::zeroFill($this->getAgencia(), 4) . self::zeroFill($this->getConta(), 8) . self::zeroFill($this->getCarteira(), 2);
            case 7:
                // Zeros (6) + Nosso número (17) + Carteira (2)
                return '000000' . $nossoNumero . self::zeroFill($this->getCarteira(), 2);
        }

        throw new Exception('O código do convênio precisa ter 4, 6 ou 7 dígitos!');
    }
}
