<?php
namespace Tests\OpenBoleto\Banco;

use OpenBoleto\Banco\Sicoob;

class SicoobTest extends KernelTestCaseAncestor
{
    public function testInstantiateWithoutArgumentsShouldWork()
    {
        $this->assertInstanceOf('OpenBoleto\\Banco\\Sicoob', new Sicoob());
    }

    public function testInstantiateShouldWork()
    {
        $instance = new Sicoob(array(
            // Parâmetros obrigatórios
            // Parâmetros obrigatórios
            'dataVencimento' => new \DateTime('2018-05-25'),
            'valor' => 1575.75,
            'agencia' => 3069 , // Até 4 dígitos
            'carteira' => 1, // 1, 2 e 3
            'modalidade' => '01', // 01, 02 e 05
            'conta' => 153850, // Até 10 dígitos
            'convenio' => 153850, // Até 5 dígitos
            'sequencial' => '203210', // Até 10 dígitos
        ));

        $this->assertInstanceOf('OpenBoleto\\Banco\\Sicoob', $instance);        
        $this->assertEquals('75691.30698 01015.385006 20321.010017 9 75350000157575', $instance->getLinhaDigitavel());

        $this->assertSame('0203210-1', (string) $instance->getNossoNumero());
    }
}

