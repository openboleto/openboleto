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
 * @package    OpenBoleto
 * @author     Fernando Dutra Neres <http://github.com/nandodutra>
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
    protected $logoBanco = 'sicoob.png';

    /**
     * Define as carteiras disponíveis para este banco
     * @var array
     */
    protected $carteiras = array('1');

    /**
     * Número da parcela a qual o boleto se refere
     * @var int
     */
    protected $numeroParcela;

    /**
     * Convênio
     * @var int
     */
    protected $convenio;

    /**
     * Definie o número da parcela
     * 
     * @param  int $numeroParcela
     * @return Sicoob
     */
    public function setNumeroParcela($numeroParcela)
    {
    	$this->numeroParcela = $numeroParcela;

    	return $this;
    }

    /**
     * Retorna o número da parcela
     * 		
     * @return int
     */
    public function getNumeroParcela()
    {
    	return $this->numeroParcela;
    }

    /**
     * Define o número do convênio
     * 
     * @param  int $convenio
     * @return Sicoob
     */
    public function setConvenio($convenio)
    {
    	$this->convenio = $convenio;

    	return $this;
    }

    /**
     * Retorna o número do convênio
     * 
     * @return int
     */
    public function getConvenio() 
    {
    	return $this->convenio;
    }

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
    	$agencia = self::zeroFill($this->getAgencia(), 4);
    	$convenio = self::zeroFill($this->getConvenio(), 10);
        $numero = self::zeroFill($this->getSequencial(), 7);
        $sequencia_constante = str_split('319731973197319731973');
        $fullfill = str_split($agencia . $convenio . $numero);

        $result = 0;
        for ($i=0; $i < count($fullfill); $i++) { 
        	$result += $fullfill[$i] * $sequencia_constante[$i];
        }

       	$resto = $result % 11;
       	
       	if($resto == 0 || $resto == 1) {
       		$dv = 0;
       	} else {
       		$dv = 11 - $resto;
       	}

        return ($numero+0) . '-' . $dv;
    }

    /**
     * O fator de vencimento do título é definido pela diferença da data de vencimento do título 
     * e a data base (03/07/2000), acrescido de 1000. Caso o titulo não tenha data de vencimento o fator 
     * será preenchido com zeros.
	 * fator de vencimento = (data de vencimento) - (03/07/2000) + 1000
     *
     * @return string
     */
    protected function getFatorVencimento()
    {
        if (!$this->getContraApresentacao()) {
            $date = new \DateTime('2000-07-03');
            return $date->diff($this->getDataVencimento())->days + 1000;
        } else {
            return '0000';
        }
    }

    /**
     * Método para gerar o código da posição de 20 a 44
     *
     * Composição do Campo Livre no Sicoob:
     * 
     * Posição     Tamanho     Conteúdo
     * 20 a 20      01			Código da carteira de cobrança
     * 21 a 24      04			Código da agência/cooperativa
     * 25 a 26      02			Código da modalidade
     * 27 a 33      07			Código do associado/cliente (convênio)
     * 34 a 41      08			Nosso número do boleto
     * 41 a 44      03			Número da parcela a que o boleto se refere - "001" se parcela única
     * 
     * @return string
     * @throws \OpenBoleto\Exception
     */
    public function getCampoLivre()
    {
        $nosso_numero = str_replace('-', '', $this->getNossoNumero());

        return $this->getCarteira() . self::zeroFill($this->getAgencia(), 4) . '01' . self::zeroFill($this->getConvenio(), 7) . $nosso_numero . $this->getNumeroParcela();
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
}
