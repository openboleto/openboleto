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
     * Define o número sequencial definido pelo cliente para compor o nosso número
     * @var int
     */
    protected $sequencial;

    /**
     * Define o campo nosso número do boleto, que é diferente do que é definido
     * @var string
     */
    protected $nossoNumeroOutput;

    /**
     * Define o número do convênio. Sempre use string pois a quantidade de caracteres é validada.
     *
     * @param string $convenio
     * @return $this
     */
    public function setConvenio($convenio)
    {
        $this->convenio = $convenio;
        return $this;
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
     * Define o número sequencial definido pelo cliente para compor o nosso número
     *
     * @param int $sequencial
     * @return $this
     */
    public function setSequencial($sequencial)
    {
        $this->sequencial = $sequencial;
        return $this;
    }

    /**
     * Retorna o número sequencial definido pelo cliente para compor o nosso número
     *
     * @return int
     */
    public function getSequencial()
    {
        return $this->sequencial;
    }

    /**
     * Define o valor do Nosso Número (identificador único do boleto)
     * => No Banco do Brasil, o nosso número não é modificado pelo usuário
     * => Caso deseje alterar o número sequencial único, use o
     * => BancoDoBrasil::setSequencial(). O BB utiliza uma nomenclatura diferente
     *
     * @see Documentação BB, arquivo "Leiaute Cobranca BRB 2012.pdf", página 4
     *
     * @param int $nossoNumero
     * @return $this|void
     * @throws \OpenBoleto\Exception
     */
    public function setNossoNumero($nossoNumero)
    {
        throw new Exception('Não é possível definir o nosso número do Banco do Brasil diretamente! Utilize o método BancoDoBrasil::setSequencial()');
    }

    /**
     * Retorna o valor do Nosso Número (identificador único do boleto)
     *
     * @return int
     * @throws \OpenBoleto\Exception
     */
    public function getNossoNumero()
    {
        $convenio = $this->getConvenio();

        switch (strlen($convenio)) {
            // Convênio de 4 dígitos, são 11 dígitos no nosso número
            case 4:
                return self::zeroFill($convenio, 4) . self::zeroFill($this->getSequencial(), 7);

            // Convênio de 6 dígitos, são 11 dígitos no nosso número
            case 6:
                // Exceto no caso de ter a carteira 21, onde são 17 dígitos
                if ($this->getCarteira() == 21) {
                    return self::zeroFill($this->getSequencial(), 17);
                }

                return self::zeroFill($convenio, 6) . self::zeroFill($this->getSequencial(), 5);

            // Convênio de 7 dígitos, são 17 dígitos no nosso número
            case 7:
                return self::zeroFill($convenio, 7) . self::zeroFill($this->getSequencial(), 10);
        }

        throw new Exception('O código do convênio precisa ter 4, 6 ou 7 dígitos!');
    }

    /**
     * Método para gerar o código da posição de 20 a 44
     *
     * @return string
     * @throws \OpenBoleto\Exception
     */
    public function getCampoLivre()
    {
        $length = strlen($this->getConvenio());

        // Nosso número
        $nossoNumero = $this->getNossoNumero();

        // Define o output do nosso número no campo do boleto
        $this->nossoNumeroOutput = $nossoNumero;

        // Sequencial do cliente com 17 dígitos
        // Apenas para convênio com 6 dígitos, modalidade sem registro - carteira 16 e 18 (definida para 21)
        if (strlen($this->getSequencial()) > 10) {
            if ($length == 6 and $this->getCarteira() == 21) {
                // Convênio (6) + Nosso número (17) + Carteira (2)
                return self::zeroFill($this->getConvenio(), 6) . $nossoNumero . '21';
            } else {
                throw new Exception('Só é possível criar um boleto com mais de 10 dígitos no nosso número quando a carteira é 21 e o convênio possuir 6 dígitos.');
            }
        }

        switch ($length) {
            case 4:
            case 6:
                // Define o output do nosso número no campo do boleto com dígito verificador
                $modulo = self::modulo11($nossoNumero);
                $this->nossoNumeroOutput .= '-' . $modulo['digito'];

                // Nosso número (11) + Agencia (4) + Conta (8) + Carteira (2)
                return $nossoNumero . self::zeroFill($this->getAgencia(), 4) . self::zeroFill($this->getConta(), 8) . self::zeroFill($this->getCarteira(), 2);
            case 7:
                // Zeros (6) + Nosso número (17) + Carteira (2)
                return '000000' . $nossoNumero . self::zeroFill($this->getCarteira(), 2);
        }

        throw new Exception('O código do convênio precisa ter 4, 6 ou 7 dígitos!');
    }

    /**
     * Define nomes de campos específicos do boleto do Banco do Brasil
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
