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
 * Classe boleto Cecred
 *
 * @package    OpenBoleto
 * @author     Frederico Wuerges Becker <fred@vestigium.com.br>
 * @copyright  Copyright (c) 2013 Estrada Virtual (http://www.estradavirtual.com.br)
 * @license    MIT License
 * @version    1.0
 */
class Cecred extends BoletoAbstract
{
    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '085';

    /**
     * @var array Nome espécie das moedas
     */
    protected static $especie = array(
        self::MOEDA_REAL => 'R$'
    );

    /**
     * Localização do logotipo do banco, referente ao diretório de imagens
     * @var string
     */
    protected $logoBanco = 'cecred.jpg';

    /**
     * Linha de local de pagamento
     * @var string
     */
    protected $localPagamento = 'PAGAVEL PREFERENCIALMENTE NAS COOPERATIVAS DO SISTEMA CECRED.<br/>APOS VENCIMENTO PAGAR SOMENTE NA COOPERATIVA VIACREDI.';

    /**
     * Define as carteiras disponíveis para este banco
     * @var array
     */
    protected $carteiras = array('1', '2', '3', '4', '5');

    /**
     * Define o número do convênio (4, 6 ou 7 caracteres)
     * @var string
     */
    protected $convenio;

    /**
     * Nome do arquivo de template a ser usado
     *
     * @var string
     */
    protected $layout = 'cecred.phtml';

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
     * Retorna o código do banco com o dígito verificador
     *
     * @return string
     */
    public function getCodigoBancoComDv()
    {
        return $this->getCodigoBanco() . '-1';
    }

    /**
     * Gera o Nosso Número.
     *
     * @throws Exception
     * @return string
     */
    protected function gerarNossoNumero()
    {
        // Conta + Div da conta + Sequencial
        return self::zeroFill($this->getConta() . $this->getContaDv(), 8) . self::zeroFill($this->getSequencial(), 9);
    }

    /**
     * Método para gerar o código da posição de 20 a 44
     *
     * @return string
     * @throws \OpenBoleto\Exception
     */
    public function getCampoLivre()
    {
        $codigo = '';
        $codigo .= self::zeroFill($this->getConvenio(), 6);
        $codigo .= self::zeroFill($this->getConta() . $this->getContaDv(), 8);
        $codigo .= self::zeroFill($this->getSequencial(), 9);
        $codigo .= self::zeroFill($this->getCarteira(), 2);
        return $codigo;
    }

    /**
     * Retorna o dígito verificador para o campo 1 conforme os padrões da CECRED
     *
     * @return int
     */
    protected function getDigitoVerificadorCampo1($campo)
    {
        for ($i = strlen($campo); $i > 0; $i--) {

            // Pega cada numero isoladamente.
            $numeros[$i] = substr($campo, $i - 1, 1);

            // Quando par, multiplica o numero por 1
            // Quando impar, multiplica o numero por 2
            $numeros[$i] = $i % 2 != 0 ? $numeros[$i] * 2 : $numeros[$i] * 1;

            // Se o numero mupltiplicado tiver 2 digitos
            // Soma o numero da primeira posição com o da segunda
            if (strlen($numeros[$i]) == 2) {
                $numeros[$i] = substr($numeros[$i], 0, 1) + substr($numeros[$i], 1, 1);
            }
        }

        // Resto da divisão por 10, menos 10
        return (10 - (array_sum($numeros) % 10));
    }

    /**
     * Retorna a linha digitável do boleto
     *
     * @return string
     */
    public function getLinhaDigitavel()
    {
        $convenio = self::zeroFill($this->getConvenio(), 6);
        $sequencial = self::zeroFill($this->getSequencial(), 9);

        // Banco (3) + Moeda (1) + Convenio (5 primeiros digitos) + DV (1)
        $part1 = $this->getCodigoBanco(). $this->getMoeda() . substr($convenio, 0, 5);
        $part1 .= $this->getDigitoVerificadorCampo1($part1);
        $part1 = substr_replace($part1, '.', 5, 0);

        // Convenio (1, último digito) + Número da conta corrente (8) + Número do boleto (1, primeiro digito) + DIV (1)
        $part2 = substr($convenio, 5, 1). self::zeroFill($this->getConta() . $this->getContaDv(), 8) . substr($sequencial, 0, 1);
        $part2 .= static::modulo10($part2);
        $part2 = substr_replace($part2, '.', 5, 0);

        // Número do Boleto (8, ultimos 8 digitos) + Número da carteira (2) + DIV (1)
        $part3 = substr($sequencial, 1, 8) . self::zeroFill($this->getCarteira(), 2);
        $part3 .= static::modulo10($part3);
        $part3 = substr_replace($part3, '.', 5, 0);

        // Digito verificador do codigo de barras
        $cd = $this->getDigitoVerificador();

        // Fator Vencimento (4) + Valor (10)
        $part4 = $this->getFatorVencimento() . $this->getValorZeroFill();

        return "$part1 $part2 $part3 $cd $part4";
    }
}
