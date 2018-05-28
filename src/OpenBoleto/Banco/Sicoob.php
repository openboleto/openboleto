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
 * Classe boleto Unicred.
 *
 * @package    OpenBoleto
 * @author     Victor Feitoza <http://github.com/vfeitoza>
 * @copyright  Copyright (c) 2013 Estrada Virtual (http://www.estradavirtual.com.br)
 * @license    MIT License
 * @version    1.0
 */
class Sicoob extends BoletoAbstract
{
    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '756';

    /**
     * Localização do logotipo do banco, referente ao diretório de imagens
     * @var string
     */
    protected $logoBanco = 'sicoob.jpg';

    /**
     * Define as carteiras disponíveis para este banco
     * @var array
     */
    protected $carteiras = array('1', '2', '5');

    /**
     * Modalidades disponíveis para as carteiras
     * @var array
     */
    protected $modalidades = array('01', '02', '05');

    /**
     * Modalidade utilizada pela carteira
     * @var string
     */
    protected $modalidade = null;

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
     * Gera o Nosso Número.
     *
     * @return string
     */
    protected function gerarNossoNumero()
    {
        $numero = self::zeroFill($this->getSequencial(), 7);
        $sequencia = $this->getAgencia() . self::zeroFill($this->getConvenio(), 10) . $numero;

        $cont=0;
        $calculoDv = '';
        // Constante para cálculo  = 3197
        // c) Multiplicar cada componente da seqüência com o seu correspondente da constante e somar os resultados.
        for ($num = 0; $num <= strlen($sequencia); $num++) {
            $cont++;
            if ($cont == 1) {
                // constante fixa Sicoob » 3197
                $constante = 3;
            }
            if ($cont == 2) {
                $constante = 1;
            }
            if ($cont == 3) {
                $constante = 9;
            }
            if ($cont == 4) {
                $constante = 7;
                $cont = 0;
            }
            $calculoDv = (int)$calculoDv + ((int)substr($sequencia, $num, 1) * $constante);
        }
        // c) Multiplicar cada componente da seqüência com o seu correspondente da constante e somar os resultados.
        $resto = $calculoDv % 11;

        // e) O resto da divisão deverá ser subtraído de 11 achando assim o DV (Se o Resto for igual a 0 ou 1 então o DV é igual a 0).        
        if ( ($resto == 0) || ($resto == 1) ) {
            $dv = 0;
        } else {
            $dv = 11 - $resto;
        }

        return $numero .'-'. $dv;
        
    }

    /**
     * Método para gerar o código da posição de 20 a 44
     *
     * @return string
     * @throws \OpenBoleto\Exception
     */
    public function getCampoLivre()
    {
        return $this->getCarteira(). $this->getAgencia() . $this->getModalidade() . self::zeroFill($this->getConvenio(), 7) .
               $this->getNossoNumero(false) . $this->getNumParcelas();
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
    public function setConvenio($convenio) {
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
