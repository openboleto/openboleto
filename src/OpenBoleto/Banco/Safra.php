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
 * Classe boleto Safra.
 *
 * @package    OpenBoleto
 * @author     GBBS 
 * @copyright  Copyright (c) 2021 GBBS (http://www.gbbs.com.br)
 * @license    MIT License
 * @version    1.0
 */
class Safra extends BoletoAbstract {

    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '422';

    /**
     * Localização do logotipo do banco, referente ao diretório de imagens
     * também poderá ser usado o sicredi.jpg
     * pois algumas agências exigem o logo da UNICRED ou SICREDI
     * @var string
     */
    protected $logoBanco = 'safra.jpg';

    /**
     * Linha de local de pagamento
     * @var string
     */
    protected $localPagamento = 'Pagável em qualquer Banco até o vencimento';

    /**
     * Altera layout para o exigido pelo banco
     * @var string
     */
    protected $layout = 'safra.phtml';

    /**
     * Campo obrigatório para emissão de boletos com carteira 198 fornecido pelo Banco com 5 dígitos
     * @var int
     */
    protected $codigoCliente;

    /**
     * Define as carteiras disponíveis para este banco
     * @var array
     */
    protected $carteiras = array('1');

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

        $numero = self::zeroFill($this->getSequencial(), 8);

        return $numero;
    }

    protected function gerarDigitoVerificadorNossoNumero() {
        $sequencial = self::zeroFill($this->getSequencial(), 8);
        $digitoVerificador = static::modulo11($sequencial);

        return $digitoVerificador['digito'];
    }

    /**
     * Método para gerar o código da posição de 20 a 44
     *
     * @return string
     * @throws \OpenBoleto\Exception
     */
    public function getCampoLivre() {

        $numero =   "7".self::zeroFill($this->getAgencia(), 4).
                    ''.self::zeroFill($this->getAgenciaDv(), 1).''.self::zeroFill($this->getConta(), 8).''.self::zeroFill($this->getContaDV(), 1).
                     self::zeroFill($this->getSequencial(), 9).''.'2';


        return $numero;
    }

    /**
     * Retorna o código do Banco no cabeçalho do boleto seguindo o padrão estabelecido
     *
     */
    public function getCodigoBancoComDv() {
        return $this->getCodigoBanco().'-7';
    }


    public function getViewVars() {
        return array(
            'carteira' => $this->getCarteira(),
        );
    }

}
