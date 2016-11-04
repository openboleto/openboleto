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

/**
 * Classe boleto Bradesco S/A.
 *
 * @package    OpenBoleto
 * @author     Daniel Garajau <http://github.com/kriansa>
 * @copyright  Copyright (c) 2013 Estrada Virtual (http://www.estradavirtual.com.br)
 * @license    MIT License
 * @version    1.0
 */
class Bradesco extends BoletoAbstract
{
    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '237';

    /**
     * Localização do logotipo do banco, referente ao diretório de imagens
     * @var string
     */
    protected $logoBanco = 'bradesco.jpg';

    /**
     * De acordo com o ramo de atividade, poderão ser utilizadas uma das siglas: DM-
     * Duplicata Mercantil, NP-Nota Promissória, NS-Nota de Seguro, CS-Cobrança
     * Seriada, REC-Recibo, LC-Letras de Câmbio, ND-Nota de Débito, DS-Duplicata de
     * Serviços, Outros
     * @var string
     */
    protected $especieDoc = 'DM';

    /**
     * Define as carteiras disponíveis para este banco
     * @var array
     */
    protected $carteiras = array('3', '6', '9');

    /**
     * Trata-se de código utilizado para identificar mensagens especificas ao cedente, sendo
     * que o mesmo consta no cadastro do Banco, quando não houver código cadastrado preencher
     * com zeros "000".
     *
     * @var int
     */
    protected $cip = '000';

    /**
     * Gera o Nosso Número.
     *
     * @return string
     */
    protected function gerarNossoNumero()
    {
        $carteira = str_pad($this->getCarteira(), 2, 0, STR_PAD_LEFT);
        $numero =  $carteira . str_pad($this->getSequencial(), 11, 0, STR_PAD_LEFT);
        $dv = $this->getDigitoVerificadorNossoNumero($numero);
        return substr($numero,0,2).'/'.substr($numero,2).'-'.$dv;
    }

    /**
     * Método para gerar o código da posição de 20 a 44
     *
     * @return string
     */
    public function getCampoLivre()
    {
        return static::zeroFill($this->getAgencia(), 4) .
            static::zeroFill($this->getCarteira(), 2) .
            substr($this->getNossoNumero(false),2) .
            static::zeroFill($this->getConta(), 6) .
            '0';
    }

    /**
     * Define o campo CIP do boleto
     *
     * @param int $cip
     * @return Bradesco
     */
    public function setCip($cip)
    {
        $this->cip = $cip;
        return $this;
    }

    /**
     * Retorna o campo CIP do boleto
     *
     * @return int
     */
    public function getCip()
    {
        return $this->cip;
    }

    /**
     * Define nomes de campos específicos do boleto do Bradesco
     *
     * @return array
     */
    public function getViewVars()
    {
        return array(
            'cip' => self::zeroFill($this->getCip(), 3),
            'mostra_cip' => true,
        );
    }


    /**
    +     * Calcúlo do dígito verificador do nosso Número
    +     *
    +     * @param  string $numero
    +     * @return int
    +     */
    static function getDigitoVerificadorNossoNumero($numero) {
        $modulo = self::modulo11($numero,7);

        return $modulo['digito'];
    }
}
