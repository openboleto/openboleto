<?php

namespace Tests\OpenBoleto\Banco;
use OpenBoleto\Banco\Bradesco;

class BradescoTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateWithoutArgumentsShouldWork()
    {
        $this->assertInstanceOf('OpenBoleto\\Banco\\Bradesco', new Bradesco());
    }

    public function testInstantiateShouldWork()
    {
        $instance = new Bradesco(array(
            // Parâmetros obrigatórios
            'dataVencimento' => new \DateTime('2013-01-01'),
            'valor' => 10.50,
            'sequencial' => 123456789, // Até 11 dígitos
            'agencia' => 1172, // Até 4 dígitos
            'carteira' => 6, // 3, 6 ou 9
            'conta' => 403005, // Até 7 dígitos
        ));

        $this->assertInstanceOf('OpenBoleto\\Banco\\Bradesco', $instance);
        $this->assertEquals('23791.17209 60012.345678 89040.300504 8 55650000001050', $instance->getLinhaDigitavel());
        $this->assertSame('123456789', (string) $instance->getNossoNumero());
    }
}
