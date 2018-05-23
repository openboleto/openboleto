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
 * Classe boleto Sicredi.
 *
 * @package    OpenBoleto
 * @author     Emmanuel Alves <http://github.com/manelpb>
 * @copyright  Copyright (c) 2016 Competiva Agencia Interativa (http://www.competiva.com.br)
 * @license    MIT License
 * @version    1.0
 */
class Sicredi extends BoletoAbstract {

    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '748';

    /**
     * Localização do logotipo do banco, referente ao diretório de imagens
     * também poderá ser usado o sicredi.jpg
     * pois algumas agências exigem o logo da UNICRED ou SICREDI
     * @var string
     */
    protected $logoBanco = 'sicredi.jpg';

    /**
     * Linha de local de pagamento
     * @var string
     */
    protected $localPagamento = 'PREFERENCIALMENTE NAS COOPERATIVAS DE CRÉDITO DO SICREDI';

    /**
     * Campo obrigatório para emissão de boletos com carteira 198 fornecido pelo Banco com 5 dígitos
     * @var int
     */
    protected $codigoCliente;

    /**
     * Define as carteiras disponíveis para este banco
     * @var array
     */
    protected $carteiras = array('1', '11', '21', '31', '41', '51');

    /**
     * Dígito verificador da carteira/nosso número para impressão no boleto
     * @var int
     */
    protected $carteiraDv;
    protected $campoLivre;

    /**
     * Define o código do cliente
     *
     * @param int $codigoCliente
     * @return $this
     */
    protected $posto;

    public function setCodigoCliente($codigoCliente) {
        $this->codigoCliente = $codigoCliente;
        return $this;
    }

    /**
     * Retorna o código do cliente
     *
     * @return int
     */
    public function getCodigoCliente() {
        return $this->codigoCliente;
    }

    /**
     * Gera o Nosso Número.
     *
     * @return string
     */
    protected function gerarNossoNumero() {
        $ano = date("y");

        $numero = self::zeroFill($this->getAgencia(), 4) .
                self::zeroFill($this->getPosto(), 2) .
                self::zeroFill($this->getConta(), 5) .
                self::zeroFill($ano, 2) .
                "2" .
                self::zeroFill($this->getSequencial(), 5);

        $dv = static::modulo11($numero);

        return self::zeroFill($ano, 2) . '/' . '2' . self::zeroFill($this->getSequencial(), 5) . '-' . $dv['digito'];
    }

    /**
     * Método para gerar o código da posição de 20 a 44
     *
     * @return string
     * @throws \OpenBoleto\Exception
     */
    public function getCampoLivre() {
       // echo 'oi';
        $numero = '3' .
                '1' .
                self::zeroFill($this->getNossoNumero(false), 9) .
                self::zeroFill($this->getAgencia(), 4) .
                self::zeroFill($this->getPosto(), 2) .
                self::zeroFill($this->getConta(), 5) .
                '1' .
                '0';

        $dv = static::modulo11($numero);

        return $numero . $dv['digito'];
    }

    /**
     * Retorna o campo Agência/Cedente do boleto
     *
     * @return string
     */
    public function getAgenciaCodigoCedente() {
        return static::zeroFill($this->getAgencia(), 4) . '.' . static::zeroFill($this->getPosto(), 2) . '.' . static::zeroFill($this->getConta(), 5);
    }

    /**
     * Retorna o código do Banco no cabeçalho do boleto seguindo o padrão estabelecido
     *
     */
    public function getCodigoBancoComDv() {
        return $this->getCodigoBanco() . '-X';
    }

    /**
     * Define o campo Posto do boleto
     *
     * @param int $cip
     * @return Bradesco
     */
    public function setPosto($posto) {
        $this->posto = $posto;
        return $this;
    }

    /**
     * Retorna o campo Posto do boleto
     *
     * @return int
     */
    public function getPosto() {
        return $this->posto;
    }

    public function getViewVars() {
        return array(
            'carteira' => $this->getCarteira(), // Campo não utilizado pelo Itaú
        );
    }

}
