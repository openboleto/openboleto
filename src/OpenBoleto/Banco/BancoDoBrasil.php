<?php
/**
 * OpenBoleto - Geração de boletos bancários em PHP
 *
 * Classe boleto Banco do Brasil S/A
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

class BancoDoBrasil extends BoletoAbstract
{
    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '001';

    /**
     * Localização do logotipo do banco, referente ao diretório de imagens
     * @var string
     */
    protected $logoBanco = 'bb.jpg';

    /**
     * Nome do arquivo de template a ser usado
     * @var string
     */
    protected $layout = 'default.phtml';

    /**
     * Linha de local de pagamento
     * @var string
     */
    protected $localPagamento = 'Pagável em qualquer Banco até o vencimento';

    /**
     * Define as carteiras disponíveis para este banco
     * @var array
     */
    protected $carteiras = array('31', '11', '16', '17', '18', '12', '51');

    /**
     * Define o número do convênio (4, 6 ou 7 caracteres)
     * @var string
     */
    protected $convenio;

    /**
     * Define o campo nosso número do boleto, que é diferente do que é definido
     * @var string
     */
    protected $nossoNumeroOutput;

    /**
     * Define o número do convênio. Sempre use string pois a quantidade de caracteres é validada.
     *
     * @param string $convenio
     */
    public function setConvenio($convenio)
    {
        $this->convenio = $convenio;
    }

    /**
     * Retorna o número do convênio
     *
     * @return string
     */
    public function getConvenio()
    {
        return $this->convenio;
    }

    /**
     * Método para gerar o código da posição de 20 a 44
     *
     * @return string
     * @throws \OpenBoleto\Exception
     */
    public function getChaveAsbace()
    {
        $length = strlen($this->getConvenio());

        // Nosso número com 17 dígitos
        // Apenas para convênio com 6 dígitos, modalidade sem registro - carteira 16 e 18
        if (strlen($this->getNossoNumero()) > 10) {
            if ($length == 6 and in_array($this->getCarteira(), array('16', '18'))) {
                // Define o output do nosso número no campo do boleto
                $this->nossoNumeroOutput = $this->getNossoNumero();

                return self::zeroFill($this->getConvenio(), 6) . self::zeroFill($this->getNossoNumero(), 17) . '21';
            } else {
                throw new Exception('Só é possível criar um boleto com mais de 10 dígitos no nosso número quando a carteira é 16 ou 18 e o convênio tiver 6 dígitos.');
            }
        }

        // 14 caracteres contendo Agencia (4) + Conta (8) + Carteira (2)
        $agenciaContaCarteira = self::zeroFill($this->getAgencia(), 4) . self::zeroFill($this->getConta(), 8) . self::zeroFill($this->getCarteira(), 2);

        switch ($length) {
            case 4:
                // Define o output do nosso número no campo do boleto
                $this->nossoNumeroOutput = self::zeroFill($this->getConvenio(), 4) . self::zeroFill($this->getNossoNumero(), 7);
                $modulo = self::modulo11($this->nossoNumeroOutput);
                $this->nossoNumeroOutput .= '-' . $modulo['digito'];

                return self::zeroFill($this->getConvenio(), 4) . self::zeroFill($this->getNossoNumero(), 7) . $agenciaContaCarteira;
            case 6:
                // Define o output do nosso número no campo do boleto
                $this->nossoNumeroOutput = self::zeroFill($this->getConvenio(), 6) . self::zeroFill($this->getNossoNumero(), 5);
                $modulo = self::modulo11($this->nossoNumeroOutput);
                $this->nossoNumeroOutput .= '-' . $modulo['digito'];

                return self::zeroFill($this->getConvenio(), 6) . self::zeroFill($this->getNossoNumero(), 5) . $agenciaContaCarteira;
            case 7:
                // Define o output do nosso número no campo do boleto
                $this->nossoNumeroOutput = self::zeroFill($this->getConvenio(), 7) . self::zeroFill($this->getNossoNumero(), 10);

                return '000000' . self::zeroFill($this->getConvenio(), 7) . self::zeroFill($this->getNossoNumero(), 10) . self::zeroFill($this->getCarteira(), 2);
        }

        throw new Exception('Quantidade de caracteres do convênio inválida! (' . $length . ')');
    }

    /**
     * Define nomes de campos específicos do boleto do BRB
     *
     * @return array
     */
    public function getViewVars()
    {
        return array(
            'nosso_numero' => $this->nossoNumeroOutput,
        );
    }
}