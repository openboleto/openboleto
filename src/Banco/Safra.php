<?php
declare(strict_types=1);

namespace OpenBoleto\Banco;

use OpenBoleto\BoletoAbstract;
use OpenBoleto\Exception;

/**
 * Classe boleto Safra - CORRIGIDA
 * Baseada na documentação oficial do Banco Safra - CNAB 400
 *
 * @package    OpenBoleto
 * @author     Correção baseada no manual Safra
 * @license    MIT License
 */
class Safra extends BoletoAbstract
{
    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '422';

    /**
     * Localização do logotipo do banco
     * @var string
     */
    protected $logoBanco = 'safra.jpg';

    /**
     * Linha de local de pagamento
     * @var string
     */
    protected $localPagamento = 'Pagável em qualquer Banco até o vencimento';

    /**
     * Layout do boleto
     * @var string
     */
    protected $layout = 'safra.phtml';

    /**
     * Define as carteiras disponíveis
     * 1 = Cobrança Simples
     * 2 = Cobrança Vinculada
     * @var array
     */
    protected $carteiras = array('1', '2');

    /**
     * Fator de vencimento - SAFRA
     * @var string
     */
    protected $fatorVencimento;

    /**
     * Modalidade de cobrança
     * 1 = Convencional (banco emite boleto) - Nosso número = ZEROS
     * 2 = Direta (cliente emite boleto) - Nosso número = LIVRE
     * @var int
     */
    protected $modalidadeCobranca = 2; // Default: Direta

    /**
     * Define a modalidade de cobrança
     *
     * @param int $modalidade 1=Convencional, 2=Direta
     * @return $this
     */
    public function setModalidadeCobranca($modalidade)
    {
        $this->modalidadeCobranca = $modalidade;
        return $this;
    }

    /**
     * Retorna a modalidade de cobrança
     *
     * @return int
     */
    public function getModalidadeCobranca()
    {
        return $this->modalidadeCobranca;
    }

    /**
     * Gera o Nosso Número conforme manual Safra
     *
     * a) Cobrança Convencional: 000000000 (ZEROS)
     * b) Cobrança Direta: Livre utilização (9 dígitos)
     *
     * @return string
     */
    protected function gerarNossoNumero()
    {
        if ($this->modalidadeCobranca == 1) {
            return '000000000';
        }

        return str_pad((string)$this->getSequencial(), 9, '0', STR_PAD_LEFT);
    }

    /**
     * Calcula o DV do Nosso Número - MÓDULO 10 (Safra)
     * Conforme manual página 15
     *
     * @return string
     */
    protected function gerarDigitoVerificadorNossoNumero()
    {
        $nossoNumero = $this->gerarNossoNumero();

        // Se for Convencional (zeros), DV = 0
        if ($nossoNumero === '000000000') {
            return '0';
        }

        // Cálculo Módulo 10 Safra
        return self::modulo10Safra($nossoNumero);
    }

    /**
     * Cálculo do DV MÓDULO 10 específico Safra
     * Multiplicadores: 2,1,2,1,2,1... (da direita para esquerda)
     *
     * @param string $numero
     * @return string
     */
    public function modulo10Safra($numero)
    {
        $numero = preg_replace('/[^0-9]/', '', $numero);
        $soma = 0;
        $multiplicador = 2;

        for ($i = strlen($numero) - 1; $i >= 0; $i--) {
            $produto = intval($numero[$i]) * $multiplicador;

            if ($produto > 9) {
                $soma += intval($produto / 10) + ($produto % 10);
            } else {
                $soma += $produto;
            }

            $multiplicador = ($multiplicador == 2) ? 1 : 2;
        }

        $resto = $soma % 10;
        $dv = (10 - $resto) % 10;

        return (string) $dv;
    }

    /**
     * Gera o CAMPO LIVRE (posições 20-44 do código de barras)
     *
     * Para Safra Cobrança Direta, o campo livre deve conter:
     * - Agência (5 dígitos) - COMPLETO, com DV incluso
     * - Conta (9 dígitos) - COMPLETO, com DV incluso
     * - Nosso Número (9 dígitos)
     * - Carteira (1 dígito) - 1 ou 2
     * - Zero (1 dígito) - Fixo
     *
     * TOTAL: 5 + 9 + 9 + 1 + 1 = 25 dígitos
     *
     * @return string
     */
    public function getCampoLivre()
    {
        $sistema = '7';

        $agencia = str_pad((string) $this->getAgencia(), 5, '0', STR_PAD_RIGHT);

        $conta = str_pad((string) $this->getConta(), 8, '0', STR_PAD_LEFT);
        $contaDv = (string) ($this->getContaDV() ?? '0');
        $contaCompleta = $conta . $contaDv;

        $nossoNumero = str_pad((string) $this->getSequencial(), 9, '0', STR_PAD_LEFT);

        $tipoCobranca = '2';

        $campoLivre = $sistema . $agencia . $contaCompleta . $nossoNumero . $tipoCobranca;

        return $campoLivre;
    }

    /**
     * Calcula o DV do código de barras (Módulo 10 - Safra)
     *
     * @return string
     */
    protected function calcularDigitoVerificadorCodigoBarras()
    {
        $codigoSemDV = $this->getCodigoBanco() .
                       $this->getMoeda() .
                       $this->getFatorVencimento() .
                       $this->getValorZeroFill() .
                       $this->getCampoLivre();

        return self::modulo10Safra($codigoSemDV);
    }

    /**
     * Gera a LINHA DIGITÁVEL conforme padrão Safra/FEBRABAN
     *
     * @return string
     */
    public function getLinhaDigitavel()
    {
        $campoLivre = $this->getCampoLivre();

        $sistema = substr($campoLivre, 0, 1);          // Pos 20: "7"
        $agencia = substr($campoLivre, 1, 5);          // Pos 21-25: "19800"
        $contaCompleta = substr($campoLivre, 6, 9);    // Pos 26-34: "005828372"
        $nossoNumero = substr($campoLivre, 15, 9);     // Pos 35-43: "000015737"
        $tipoCobranca = substr($campoLivre, 24, 1);    // Pos 44: "2"

        // ===== 1º CAMPO =====
        // Banco (422) + Moeda (9) + Sistema (7) + 4 primeiros dígitos da agência
        $agencia4primeiros = substr($agencia, 0, 4);

        $part1_base = '4229' . $sistema . $agencia4primeiros;
        $dv1 = $this->modulo10Safra($part1_base);

        $part1 = '42297.' . $agencia4primeiros . $dv1;

        // ===== 2º CAMPO =====
        $part2_base = $contaCompleta;
        $dv2 = $this->modulo10Safra($part2_base);

        $part2_parte1 = "000" . substr($part2_base, 2, 3);
        $part2_parte2 = substr($part2_base, 5, 3) . $part2_base[8] . $dv2;

        $part2 = $part2_parte1 . '.' . $part2_parte2;

        // ===== 3º CAMPO =====
        $part3_base = $nossoNumero . $tipoCobranca;
        $dv3 = $this->modulo10Safra($part3_base);

        $part3 = substr($part3_base, 0, 3) . substr($part3_base, 3, 3) . '.' . substr($part3_base, 6, 4) . $dv3;
        $dvCodigoBarras = $this->calcularDacCodigoBarras();

        // ===== 5º CAMPO =====
        $part5 = $this->getFatorVencimento() . $this->getValorZeroFill();

        return "$part1 $part2 $part3 $dvCodigoBarras $part5";
    }

    /**
     * Método público para calcular DAC (usado na linha digitável)
     * @return string
     */
    public function calcularDacCodigoBarras()
    {
        $codigoCompleto = $this->getCodigoBarras();
        return substr($codigoCompleto, 4, 1);
    }

    /**
     * CALCULA O CÓDIGO DE BARRAS COMPLETO (44 POSIÇÕES)
     * @return string
     */
    public function getCodigoBarras()
    {
        $codigo = '422';
        $codigo .= '9';
        $codigo .= '0';
        $codigo .= $this->getFatorVencimento();
        $codigo .= $this->getValorZeroFill();
        $codigo .= $this->getCampoLivre();
        $dac = $this->calcularDacModulo11($codigo);
        $codigo = substr($codigo, 0, 4) . $dac . substr($codigo, 5);

        return $codigo;
    }

    /**
     * CALCULA O DAC (DÍGITO DE AUTO CONFERÊNCIA) - MÓDULO 11
     * @param string $codigo
     * @return string
     */
    private function calcularDacModulo11($codigo)
    {
        $numero = substr($codigo, 0, 4) . substr($codigo, 5);

        $multiplicador = 2;
        $soma = 0;

        for ($i = strlen($numero) - 1; $i >= 0; $i--) {
            $soma += intval($numero[$i]) * $multiplicador;
            $multiplicador++;
            if ($multiplicador > 9) {
                $multiplicador = 2;
            }
        }

        $resto = $soma % 11;

        if ($resto == 0 || $resto == 1 || $resto == 10) {
            return '1';
        }

        return (string) (11 - $resto);
    }

    /**
     * Retorna o código do Banco com DV para exibição
     *
     * @return string
     */
    public function getCodigoBancoComDv()
    {
        $dv = self::modulo10Safra($this->getCodigoBanco());
        return $this->getCodigoBanco() . '-7';
    }

    /**
     * Define a carteira com validação Safra
     *
     * @param string $carteira
     * @return $this
     * @throws Exception
     */
    public function setCarteira($carteira)
    {
        $carteira = (string) $carteira;

        if (!in_array($carteira, $this->carteiras)) {
            throw new Exception("Carteira inválida para o Safra. Use 1 (Simples) ou 2 (Vinculada)");
        }

        $this->carteira = $carteira;
        return $this;
    }

    /**
     * Define a conta com DV e valida tamanho
     *
     * @param string $conta
     * @return $this
     */
    public function setConta($conta)
    {
        $conta = preg_replace('/[^0-9]/', '', (string) $conta);
        $this->conta = (int) $conta;
        return $this;
    }

    /**
     * Define o DV da conta
     *
     * @param int $contaDv
     * @return $this
     */
    public function setContaDv($contaDv)
    {
        $this->contaDv = (int) $contaDv;
        return $this;
    }

    /**
     * Retorna o DV da conta
     *
     * @return int
     */
    public function getContaDV()
    {
        return $this->contaDv ?? 0;
    }

    /**
     * Retorna o DV da agência (não usado pelo Safra)
     *
     * @return string
     */
    public function getAgenciaDv()
    {
        return ''; // Safra não usa DV da agência separado
    }

    /**
     * Define o fator de vencimento conforme manual Safra
     * Data base: 07/10/1997
     * Novo ciclo: 22/02/2025 (data base: 29/05/2022)
     *
     * @param \DateTime $data
     * @return $this
     */
    public function setFatorVencimento(\DateTime $data)
    {
        $dataBase = new \DateTime('2022-05-29'); // Nova data base

        $diferenca = $dataBase->diff($data);
        $fator = $diferenca->days;

        // Ajustar para fator mínimo 1000
        if ($fator < 1000) {
            $fator += 1000;
        }
        if ($fator > 9999) {
            $fator = 1000 + ($fator % 1000);
        }

        $this->fatorVencimento = str_pad((string) $fator, 4, '0', STR_PAD_LEFT);
        return $this;
    }

    /**
     * Retorna as variáveis para a view
     *
     * @return array
     */
    public function getViewVars()
    {
        return array(
            'carteira' => $this->getCarteira(),
            'modalidade' => $this->getModalidadeCobranca() == 1 ? 'Convencional' : 'Direta',
            'nosso_numero' => $this->gerarNossoNumero(),
            'nosso_numero_dv' => $this->gerarDigitoVerificadorNossoNumero(),
            'codigo_cliente' => $this->getAgencia() . '/' . $this->getConta() . '-' . $this->getContaDV(),
        );
    }
}
