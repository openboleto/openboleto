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
 * Classe boleto C6 Bank.
 *
 * @author     Warquia Pereira
 * @version    1.0
 * @copyright  Copyright (c) 2021
 * @license    MIT License
 * @package    OpenBoleto
 */
class C6Bank extends BoletoAbstract
{
    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '336';

    /**
     * Localização do logotipo do banco, referente ao diretório de imagens
     * @var string
     */
    protected $logoBanco = 'c6bank.png';

    /**
     * Define as carteiras disponíveis para este banco
     * @var array
     */
    protected $carteiras = array('10', '20');

    /**
     * Convênio utilizado pelo Sacado
     * @var integer
     */
    protected $convenio = '12345';

    /**
     * Número de parcelas usadas no boleto ou carnê
     * @var string
     */
    protected $numParcelas = '001';

    /**
     * Linha de local de pagamento
     * @var string
     */
    protected $localPagamento = 'PAGAVEL EM CANAIS ELETRONICOS, AGENCIAS OU CORRESPONDENTES';

    /**
     * Opções de modalidade aceitas pelo banco
     * @var array
     */
    protected $modalidades = array('01', '02', '05');

    /**
     * Gera o Nosso Número.
     *
     * @return string
     */
    protected function gerarNossoNumero()
    {
        $numero = self::zeroFill($this->getSequencial(), 10);
        $sequencia = $this->getAgencia() . self::zeroFill($this->getConvenio(), 10) . $numero;
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
        /*Identificador de layout:
         * 3 para cobrança registrada com emissão pelo banco
         * 4 para cobrança direta com emissão pelo cedente
         */
        $layout = '4';
        return self::zeroFill($this->getConvenio(), 12) . self::zeroFill($this->getNossoNumero(false), 10) .
               self::zeroFill($this->getCarteira(), 2) . $layout;
    }

    /**
     * Retorna o campo Agência/Cedente do boleto
     *
     * @return string
     */
    public function getAgenciaCodigoCedente()
    {
        return static::zeroFill($this->getAgencia(), 4) . ' / ' . $this->getConvenio();
    }

    /**
     * Define a modalidade da carteira
     *
     * @param type $modalidade
     * @return \OpenBoleto\Banco\Sicoob
     * @throws Exception
     */
    public function setModalidade($modalidade)
    {
        if (!in_array($modalidade, $this->getModalidades())) {
            throw new Exception("Modalidade não disponível!");
        }

        $this->modalidade = $modalidade;

        return $this;
    }

    /**
     * seta o convênio a ser utilizado pelo Sacado
     *
     * @param integer $convenio Convẽnio do sacado
     * @return \OpenBoleto\Banco\Sicoob
     */
    public function setConvenio($convenio)
    {
        $this->convenio = $convenio;

        return $this;
    }

    /**
     * Retorna a modalidade da carteira
     *
     * @return string
     */
    public function getModalidade()
    {
        return $this->modalidade;
    }

    /**
     * Retorna todas as modalidades disponíveis
     *
     * @return array
     */
    public function getModalidades()
    {
        return $this->modalidades;
    }

    /**
     * Retorna o número de parcelas
     *
     * @return string
     */
    public function getNumParcelas()
    {
        return $this->numParcelas;
    }

    /**
     * Retorna o convênio do Sacado
     *
     * @return integer
     */
    public function getConvenio()
    {
        return $this->convenio;
    }
}
