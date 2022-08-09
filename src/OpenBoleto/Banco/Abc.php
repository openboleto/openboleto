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
class Abc extends BoletoAbstract
{
    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '246';

    /**
     * Localização do logotipo do banco, referente ao diretório de imagens
     * @var string
     */
    protected $logoBanco = 'abc.png';

    /**
     * Linha de local de pagamento
     * @var string
     */
    protected $localPagamento = 'Pagável em qualquer banco';

    /**
     * Define as carteiras disponíveis para este banco
     * @var array
     */
    protected $carteiras = array('101', '110', '201');

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
    protected $layout = 'jasper.phtml';

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
        // $conta = $this->getConta();
        // $sequencial = $this->getSequencial();
        // // Inicia o número de acordo com o tipo de cobrança, provavelmente só será usado Sem Registro, mas
        // // se futuramente o projeto permitir a geração de lotes para inclusão, o tipo registrado pode ser útil
        // // 1 => registrada, 2 => sem registro. O número 4 indica que é o beneficiário que está gerando o boleto
        // $carteira = $this->getCarteira();
        // if ($carteira == '110'){
        //     $numero = '24';
        // } else {
        //     $numero = '14';
        // }
        // // As 15 próximas posições no nosso número são a critério do beneficiário, utilizando o sequencial
        // // Depois, calcula-se o código verificador por módulo 11
        // $modulo = self::modulo11($numero.self::zeroFill($sequencial, 15));
        // $numero .= self::zeroFill($sequencial, 15) . '-' . $modulo['digito'];
        
        $agencia = $this->getAgencia();
        $carteira = $this->getCarteira();
        $numeroSemDigito = $this->getSequencial();

        $modulo = self::modulo10($agencia . $carteira . $numeroSemDigito);

        $numero = $numeroSemDigito . $modulo;

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
        $campoLivre = self::zeroFill($this->getAgencia(), 4);
        $campoLivre .= self::zeroFill($this->getCarteira(), 3);
        $campoLivre .= self::zeroFill($this->getConvenio(), 7); //convenio
        $campoLivre .= self::zeroFill($this->getNossoNumero(), 11);

        return $campoLivre;
    }
}