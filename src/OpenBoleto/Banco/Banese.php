<?php

/*
 * OpenBoleto - Geração de boletos bancários em PHP
 *
 * LICENSE: The MIT License (MIT)
 *
 * Copyright (C) 2020
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
 * Classe boleto Banese
 *
 * @package    OpenBoleto
 * @author     Deividson Damasio <http://github.com/dadeke>
 * @copyright  Copyright (c) 2020
 * @license    MIT License
 * @version    1.0
 */
class Banese extends BoletoAbstract
{
    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '047';

    /**
     * Localização do logotipo do banco, referente ao diretório de imagens
     * @var string
     */
    protected $logoBanco = 'banese.png';

    /**
     * Linha de local de pagamento
     * @var string
     */
    protected $localPagamento = 'Pagável preferencialmente na rede Banese';

    /**
     * Define as carteiras disponíveis para este banco.
     * @var array
     */
    protected $carteiras = array('CS');

    /**
     * Gera o Nosso Número.
     *
     * @return string
     */
    protected function gerarNossoNumero() {
        $sequencial = self::zeroFill($this->getSequencial(), 8);
        return $sequencial . '-' . $this->gerarDigitoVerificadorNossoNumero();
    }

    protected function gerarDigitoVerificadorNossoNumero() {
        $sequencial = $this->getAgencia() . self::zeroFill($this->getSequencial(), 8);
        $digitoVerificador = static::modulo11($sequencial);
        
        return $digitoVerificador['digito'];
    }

    /**
     * Gera o Dúplo Dígito.
     * 
     * (Cálculo do duplo dígito verificador da Chave ASBACE)
     * (BANESE_Manual_do_Bloqueto_BANESE_20061219.pdf - Página 7)
     * 
     * @return string
     */
    protected function gerarDuploDigito($chave) {
        $digito1 = null;
        $digito2 = 0;
        $pesos = '21212121212121212121212';
        $somatorio = 0;
        for($indice = 0; $indice < 23; $indice++) {
            $resultado = $chave[$indice] * $pesos[$indice];
            if($resultado > 9) {
                $somatorio += ($resultado - 9);
            }
            else {
                $somatorio += $resultado;
            }
        }
        $resto = $somatorio % 10;
        if($resto == 0) {
            $digito1 = 0;
        }
        else if($resto > 0) {
            $digito1 = 10 - $resto;
        }
        $digito1 = strval($digito1);
        $resultado = static::modulo11($chave . $digito1, 7);
        $digito2 = strval($resultado['digito']);
        return $digito1 . $digito2;
    }
    
    /**
     * Método para gerar o código da posição de 20 a 44 liberado pela FEBRABAN.
     * 
     * (Chave ASBACE)
     * (BANESE_Manual_do_Bloqueto_BANESE_20061219.pdf - Página 6)
     *
     * @return string
     * @throws \OpenBoleto\Exception
     */
    public function getCampoLivre() {
        $chave = self::caracteresDireita($this->getAgencia(), 2) .
            self::zeroFill($this->getConta(), 8) . $this->getContaDv() .
            self::zeroFill($this->getSequencial(), 8) .
            $this->gerarDigitoVerificadorNossoNumero() .
            $this->codigoBanco;

        $chave .= $this->gerarDuploDigito($chave);

        return $chave;
    }
}
