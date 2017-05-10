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
use OpenBoleto\Agente;

/**
 * Classe boleto Caixa Economica Federal - Modelo SICOB.
 * Estende a classe Caixa
 *
 * @package    OpenBoleto
 * @author     Lúcio Abrantes <http://github.com/lucioabrantes>
 * @license    MIT License
 * @version    1.0
 */
class CaixaSICOB extends Caixa
{

    /**
     * Define o número da conta
     *
     * Overrided porque o cedente da Caixa TEM QUE TER 11 posições, senão não é válido
     *
     * @param int $conta
     * @return BoletoAbstract
     */
    public function setConta($conta)
    {
        $this->conta = self::zeroFill($conta, 11);
        return $this;
    }

    /**
     * Gera o Nosso Número
     *
     * @throws Exception
     * @return string
     */
    protected function gerarNossoNumero()
    {
        $conta = $this->getConta();
        $sequencial = $this->getSequencial();

        // Inicia o número de acordo com o tipo de cobrança, provavelmente só será usado Sem Registro, mas
        // se futuramente o projeto permitir a geração de lotes para inclusão, o tipo registrado pode ser útil
        // 9 => registrada, 8 => sem registro.
        $carteira = $this->getCarteira();
        if ($carteira == 'SR'){
            $numero = '80';
        } else {
            $numero = '9';
        }

        // As 8 próximas posições no nosso número são a critério do beneficiário, utilizando o sequencial
        // Depois, calcula-se o código verificador por módulo 11
        $modulo = self::modulo11($numero.self::zeroFill($sequencial, 8));
        $numero .= self::zeroFill($sequencial, 8) . '-' . $modulo['digito'];

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
        $campoLivre = substr($this->gerarNossoNumero(), 0, 10);
        $campoLivre .= $this->getAgencia();
        $campoLivre .= $this->getConta();

        return $campoLivre;
    }
}
