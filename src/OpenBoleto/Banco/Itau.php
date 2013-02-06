<?php
/**
 * OpenBoleto - Geração de boletos bancários em PHP
 *
 * Classe boleto Itaú S/A
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
use OpenBoleto\Exception;

class Itau extends BoletoAbstract
{
    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '341';

    /**
     * Localização do logotipo do banco, referente ao diretório de imagens
     * @var string
     */
    protected $logoBanco = 'itau.jpg';

    /**
     * Nome do arquivo de template a ser usado
     * @var string
     */
    protected $layout = 'default.phtml';

    /**
     * Linha de local de pagamento
     * @var string
     */
    protected $localPagamento = 'Até o vencimento, pague preferencialmente no Itaú. Após o vencimento pague somente no Itaú';

    /**
     * Define as carteiras disponíveis para este banco
     * @var array
     */
    protected $carteiras = array(
        '148', '149', '153', '108', '180', '121', '150', '109', '191', '116', '117', '119',
        '134', '135', '136', '104', '188', '147', '112', '115', '177', '172', '107', '204',
        '205', '206', '173', '196', '103', '102', '174', '198', '167', '202', '203', '175',
    );

    /**
     * Campo obrigatório para emissão de boletos com carteira 198 fornecido pelo Banco com 5 dígitos
     * @var int
     */
    protected $codigoCliente;

    /**
     * Dígito verificador da carteira/nosso número para impressão no boleto
     * @var int
     */
    protected $carteiraDv;

    /**
     * Define o código do cliente
     *
     * @param int $codigoCliente
     */
    public function setCodigoCliente($codigoCliente)
    {
        $this->codigoCliente = $codigoCliente;
    }

    /**
     * Retorna o código do cliente
     *
     * @return int
     */
    public function getCodigoCliente()
    {
        return $this->codigoCliente;
    }

    /**
     * Método para gerar o código da posição de 20 a 44
     *
     * @return string
     * @throws \OpenBoleto\Exception
     */
    public function getChaveAsbace()
    {
        $nossoNumero = self::zeroFill($this->getNossoNumero(), 8);
        $carteira = self::zeroFill($this->getCarteira(), 3);
        $agencia = self::zeroFill($this->getAgencia(), 4);
        $conta = self::zeroFill($this->getConta(), 5);

        // Carteira 198 - (Nosso Número com 15 posições) - Anexo 5 do manual
        if (in_array($this->getCarteira(), array('107', '122', '142', '143', '196', '198'))) {
            $codigo = $carteira .
                $nossoNumero .
                self::zeroFill($this->getNumeroDocumento(), 7) .
                self::zeroFill($this->getCodigoCliente(), 5);

            // Define o DV da carteira para a view
            $this->carteiraDv = $modulo = self::modulo10($codigo);

            return $codigo . $modulo . '0';
        }

        // Geração do DAC - Anexo 4 do manual
        if (!in_array($this->getCarteira(), array('126', '131', '146', '150', '168'))) {
            // Define o DV da carteira para a view
            $this->carteiraDv = $dvAgContaCarteira = self::modulo10($agencia . $conta . $carteira . $nossoNumero);
        } else {
            // Define o DV da carteira para a view
            $this->carteiraDv = $dvAgContaCarteira = self::modulo10($carteira . $nossoNumero);
        }

        // Módulo 10 Agência/Conta
        $dvAgConta = self::modulo10($agencia . $conta);

        return $carteira . $nossoNumero . $dvAgContaCarteira . $agencia . $conta . $dvAgConta . '000';
    }

    /**
     * Define nomes de campos específicos do boleto do Itaú
     *
     * @return array
     */
    public function getViewVars()
    {
        return array(
            'carteira' => null, // Campo não utilizado pelo Itaú
            'nosso_numero' => self::zeroFill($this->getCarteira(), 3) . '/' . self::zeroFill($this->getNossoNumero(), 8) . '-' . $this->carteiraDv,
        );
    }
}
