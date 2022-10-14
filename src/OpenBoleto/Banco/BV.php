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
 * Classe boleto Caixa Economica Federal - Modelo SIGCB.
 *
 * @package    OpenBoleto
 * @author     Lucas Zardo <http://github.com/zardo>
 * @copyright  Copyright (c) 2013 Delivery Much (http://deliverymuch.com.br)
 * @license    MIT License
 * @version    1.0
 */
class BV extends BoletoAbstract
{
    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '655';

    /**
     * Localização do logotipo do banco, referente ao diretório de imagens
     * @var string
     */
    protected $logoBanco = 'BV.png';

    /**
     * Linha de local de pagamento
     * @var string
     */
    protected $localPagamento = 'Pagável em qualquer banco';

    /**
     * Define as carteiras disponíveis para este banco
     * @var array
     */
    protected $carteiras = array('110');

    /**
     * Nome do arquivo de template a ser usado
     *
     * A Caixa obriga-nos a usar campos não presentes no projeto original, além de alterar cedente
     * para beneficiário e sacado para pagador. Segundo o banco, estas regras muitas vezes não são
     * observadas na homologação, mas, considerando o caráter subjetivo de quem vai analisar na Caixa,
     * preferi incluir todos de acordo com o manual. Para conhecimento, foi copiado o modelo 3.5.1 adaptado
     * Também removi os campos Espécie, REAL, Quantidade e Valor por considerar desnecessários e não obrigatórios
     *
     * @var string
     */
    protected $layout = 'caixa.phtml';

    /**
     * Define o número da conta
     *
     * Overrided porque o cedente da Caixa TEM QUE TER 6 posições, senão não é válido
     *
     * @param int $conta
     * @return BoletoAbstract
     */
    public function setConta($conta)
    {
        $this->conta = self::zeroFill($conta, 9);
        return $this;
    }
    
    public function setConvenio($convenio)
    {
        $this->convenio = $convenio;
    }

    public function getConvenio()
    {
        return $this->convenio;
    }

    /**
     * Gera o Nosso Número.
     *
     * @throws Exception
     * @return string
     */
    protected function gerarNossoNumero()
    {
        $sequencial = $this->getSequencial();

        // As 15 próximas posições no nosso número são a critério do beneficiário, utilizando o sequencial
        // Depois, calcula-se o código verificador por módulo 11
        $modulo = self::modulo10($sequencial);
        $numero = self::zeroFill($sequencial, 9) . '-' . $modulo;

        return $numero;
    }

    /**
     * Método para gerar o código da posição de 20 a 44
     * 
     * O campo livre da Caixa é cheio de nove horas. Transcrição do manual:
     * O Campo Livre contém 25 posições dispostas da seguinte forma:  
     * 
     * Descrição -------------------- Posição no Código de Barras --- Observação 
     * 
     * Código do Beneficiário ------- Posição: 20-25 
     * DV do Código do Beneficiário - Posição: 26-26 ---------------- ANEXO VI 
     * Nosso Número – Seqüência 1 --- Posição: 27-29 ---------------- 3ª a 5ª posição do Nosso Número 
     * Constante 1 ------------------ Posição: 30-30 ---------------- 1ª posição do Nosso Numero: 
     *                                                                (1-Registrada / 2-Sem Registro) 
     * Nosso Número – Seqüência 2 --- Posição: 31-33 ---------------- 6ª a 8ª posição do Nosso Número 
     * Constante 2 ------------------ Posição: 34-34 ---------------- 2ª posição do Nosso Número: 
     *                                                                Ident da Emissão do Boleto (4-Beneficiário) 
     * Nosso Número – Seqüência 3 --- Posição: 35-43 ---------------- 9ª a 17ª posição do Nosso Número 
     * DV do Campo Livre ------------ Posição: 44-44 ---------------- Item 5.3.1 (abaixo) 
     * 
     * @return string
     * @throws \OpenBoleto\Exception
     */
    public function getCampoLivre()
    {
        $nossoNumero = $this->gerarNossoNumero();
        $convenio = $this->getConvenio();

        // Código do beneficiário + DV]
        //$modulo = self::modulo11($convenio);
        $campoLivre = self::zeroFill($convenio,10) . '500';

        // Sequencia 1 (posições 3-5 NN) + Constante 1 (1 => registrada, 2 => sem registro)
       
        $campoLivre .= str_replace('-','',$nossoNumero). '00';
       return $campoLivre;
    }

}
