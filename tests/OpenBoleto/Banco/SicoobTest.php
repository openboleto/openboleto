<?php

namespace Tests\OpenBoleto\Banco;
use OpenBoleto\Banco\Sicoob;

class SicoobTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateWithoutArgumentsShouldWork()
    {
        $this->assertInstanceOf('OpenBoleto\\Banco\\Sicoob', new Sicoob());
    }

    public function testInstantiateShouldWork()
    {
        $instance = new Sicoob(array(
            // Parâmetros obrigatórios
            'dataVencimento' => new \DateTime('2014-04-01'),
            'valor' => 10,
            'agencia' => 3231, // Até 4 dígitos
            'carteira' => 1, // 11, 21, 31, 41 ou 51
            'conta' => 50237, // Até 10 dígitos
            'convenio' => 4847, 
            'numeroParcela' => '001',
            'sequencial' => 9, // Até 10 dígitos
        ));

        $this->assertInstanceOf('OpenBoleto\\Banco\\Sicoob', $instance);
        $this->assertEquals('75691.32314 01000.484707 00000.090019 1 60200000001000', $instance->getLinhaDigitavel());
        $this->assertSame('0000009-3', (string) $instance->getNossoNumero());
    }
}
