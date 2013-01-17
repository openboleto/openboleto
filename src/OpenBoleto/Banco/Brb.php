<?php
/**
 * OpenBoleto - Geração de boletos bancários em PHP
 *
 * Classe boleto BRB - Banco de Brasília
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
 *
 * @package    OpenBoleto
 * @author     Daniel Garajau <http://github.com/kriansa>
 * @copyright  Copyright (c) 2013 Estrada Virtual (http://www.estradavirtual.com.br)
 * @license    MIT License
 * @version    0.1
 */

namespace OpenBoleto\Banco;
use OpenBoleto\BoletoAbstract;

class Brb extends BoletoAbstract
{
    /**
     * @var string
     */
    protected $codigoBanco = '070';

    /**
     * @var string
     */
    protected $logoBanco = 'brb.png';

    /**
     * @var string
     */
    protected $viewName = 'brb.phtml';

    /**
     * @var string
     */
    protected $localPagamento = 'Até o vencimento pagar em qualquer Banco, depois só no BRB';

    /**
     * Método para gerar o código da posição de 20 a 44
     *
     * @return string
     */
    public function getChaveAsbace()
    {
        $nossoNumero = static::zeroFill($this->getNossoNumero(), 6);
        $nossoNumero = array(
            '1-3' => substr($nossoNumero, 0, 3),
            '4-9' => substr($nossoNumero, 3),
        );

        $chave = $nossoNumero['1-3'] . static::zeroFill($this->getAgencia(), 3) . static::zeroFill($this->getConta(), 7) . $this->getCarteira() . $nossoNumero['4-9'] . $this->getCodigoBanco();
        $d1 = static::modulo10($chave);

        CalculaD2:
        $modulo = static::modulo11($chave . $d1, 7);

        if ($modulo['resto'] == 0) {
            $d2 = 0;
        } else if ($modulo['resto'] > 1) {
            $d2 = 11 - $modulo['resto'];
        } else if ($modulo['resto'] == 1) {
            $d1 = $d1 + 1;
            if ($d1 == 10) {
                $d1 = 0;
            }
            goto CalculaD2;
        }

        return $chave . $d1 . $d2;
    }

    /**
     * Define nomes de campos específicos do boleto do BRB
     *
     * @return array
     */
    public function getViewVars()
    {
        return array(
            'carteira' => 'COB',
            'agencia_codigo_cedente' => '000 - ' . $this->getAgencia() . ' - ' . $this->getConta(),
        );
    }
}
