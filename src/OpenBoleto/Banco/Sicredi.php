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
 * Classe boleto Sicredi S/A
 *
 * @package    OpenBoleto
 * @author     Diogo Santos <http://github.com/diogoand1>
 * @copyright  Copyright (c) 2013 Estrada Virtual (http://www.dreamsnet.com.br)
 * @license    MIT License
 * @version    1.0
 */
class Sicredi extends BoletoAbstract
{
    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '748';

    /**
     * Localização do logotipo do banco, referente ao diretório de imagens
     * @var string
     */
    protected $logoBanco = 'sicredi.jpg';

    /**
     * Linha de local de pagamento
     * @var string
     */
    protected $localPagamento = 'PAGÁVEL PREFERENCIALMENTE NAS COOPERATIVAS DE CRÉDITO DO Sicredi';

    /**
     * Define as carteiras disponíveis para este banco
     * @var array
     */
    protected $carteiras = array('A', 'B', 'C', 'D', 'E', 'G', 'H', 'I', 'J', 'K');  
    
    /**
     * Define os nomes das carteiras para exibição no boleto
     * @var array
     */
    protected $carteirasNomes = array(
        'A' => 'DMI', 'B' => 'DR', 'C' => 'NP', 'D' => 'NR', 'E' => 'NS', 
        'G' => 'RC', 'H' => 'LC', 'I' => 'ND', 'J' => 'DSI', 'K' => 'OS'
    );

    /**
     * Gera o Nosso Número.
     *
     * @return string
     */
    protected function gerarNossoNumero()
    {
        $aa = date('y');
        $b = 2;
        $numero = self::zeroFill($this->getSequencial(), 5);
        $numero = $aa . '/' . $b . $numero;
        $dv = static::modulo11($numero);
        $numero .= '-' . $dv['digito'];

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
        $numero = '31' . self::zeroFill($this->getNossoNumero(false), 9) . self::zeroFill($this->getAgencia(), 4) . '61' . self::zeroFill($this->getConta(), 5) . '10'; 
        // 16 = 6 112 20 = 3 4510      
        $modulo = static::modulo11($numero);
        
        if ($modulo['resto'] == 0 or $modulo['resto'] == 1) {
            $dv = 0;
        } else if ($modulo['resto'] > 1) {
            $dv = $modulo['digito'];
        }
        
        return $numero . $dv;                
    }

    /**
     * Retorna o campo Agência/Cedente do boleto
     *
     * @return string
     */
    public function getAgenciaCodigoCedente()
    {
        return static::zeroFill($this->getAgencia(), 4) . '.61.' . static::zeroFill($this->getConta(), 5);
    }
    
    public function getCodigoBancoComDv()
    {      
        $codigoBanco = $this->getCodigoBanco();
        return $codigoBanco . '-X';
    }
}
