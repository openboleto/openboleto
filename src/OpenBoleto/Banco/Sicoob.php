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
 * Classe boleto Sicoob.
 *
 *
 * @package    OpenBoleto
 * @author     Fernando Dutra Neres <http://github.com/nandodutra>
 * @author     Victor Feitoza <http://github.com/vfeitoza>
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
     * Para o cálculo do dígito verificador do nosso número, deverá ser utilizada a fórmula abaixo:
	 * Número da Cooperativa    9(4) – vide planilha "Capa" deste arquivo
	 * Código do Cliente   9(10) – vide planilha "Capa" deste arquivo
	 * Nosso Número   9(7) – Iniciado em 1
	 *
	 * Constante para cálculo  = 3197
	 *
	 * a) Concatenar na seqüência completando com zero à esquerda.
	 *      Ex.:Número da Cooperativa  = 0001
	 *            Número do Cliente  = 1-9
	 *            Nosso Número  = 21
	 *            000100000000190000021
	 *
	 * b) Alinhar a constante com a seqüência repetindo de traz para frente.
	 *      Ex.: 000100000000190000021
	 *             319731973197319731973
	 *
	 * c) Multiplicar cada componente da seqüência com o seu correspondente da constante e somar os resultados.
	 *      Ex.: 1*7 + 1*3 + 9*1 + 2*7 + 1*3 = 36
	 *
	 * d) Calcular o Resto através do Módulo 11.
	 *      Ex.: 36/11 = 3, resto = 3
	 *
	 * e) O resto da divisão deverá ser subtraído de 11 achando assim o DV (Se o Resto for igual a 0 ou 1 então o DV é igual a 0).
	 *      Ex.: 11 – 3 = 8, então Nosso Número + DV = 21-8
     *
     * @return string
     */
    protected function gerarNossoNumero()
    {
        $numero = self::zeroFill($this->getSequencial(), 7);
        $sequencia = $this->getAgencia() . self::zeroFill($this->getConvenio(), 10) . $numero;
        
        $cont=0;
        $calculoDv = '';
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
            $calculoDv = $calculoDv + (substr($sequencia, $num, 1) * $constante);
        }
        
        $resto = $calculoDv % 11;
        $dv = 11 - $resto;
        if (($dv == 0) || ($dv == 1) || ($dv == 9)) { 
            $dv = 0;
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
