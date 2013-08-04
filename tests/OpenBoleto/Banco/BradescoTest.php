<?php

namespace Tests\OpenBoleto\Banco;
use OpenBoleto\Agente;
use OpenBoleto\Banco\Bradesco;

class BradescoTest extends \PHPUnit_Framework_TestCase
{
    protected static function getAgentes()
    {
        return array(
            new Agente('Fernando Maia', '023.434.234-34', 'ABC 302 Bloco N', '72000-000', 'Brasília', 'DF'),
            new Agente('Empresa de cosméticos LTDA', '02.123.123/0001-11', 'CLS 403 Lj 23', '71000-000', 'Brasília', 'DF')
        );
    }

    public function testInstantiateWithoutArgumentsShouldWork()
    {
        $this->assertInstanceOf('OpenBoleto\\Banco\\Bradesco', new Bradesco());
    }

    public function testInstantiateShouldWork()
    {
        list($sacado, $cedente) = static::getAgentes();

        $instance = new Bradesco(array(
            // Parâmetros obrigatórios
            'dataVencimento' => new \DateTime('2013-01-01'),
            'valor' => 10.50,
            'sequencial' => 123456789, // Até 11 dígitos
            'sacado' => $sacado,
            'cedente' => $cedente,
            'agencia' => 1172, // Até 4 dígitos
            'carteira' => 6, // 3, 6 ou 9
            'conta' => 403005, // Até 7 dígitos
        ));

        $this->assertInstanceOf('OpenBoleto\\Banco\\Bradesco', $instance);
        $this->assertEquals('23791.17209 60012.345678 89040.300504 8 55650000001050', $instance->getLinhaDigitavel());
        $this->assertSame('1172060012345678904030050', (string) $instance->getCampoLivre());
        $this->assertSame('123456789', (string) $instance->getNossoNumero());
    }
}
