<?php
namespace Tests\OpenBoleto\Banco;

use OpenBoleto\Banco\Uniprime;

class UniprimeTest extends KernelTestCaseAncestor
{
    public function testInstantiateWithoutArgumentsShouldWork()
    {
        $this->assertInstanceOf('OpenBoleto\\Banco\\Uniprime', new Uniprime());
    }

    public function testInstantiateShouldWork()
    {
        $instance = new Uniprime(array(
            // Parâmetros obrigatórios
            'dataVencimento' => new \DateTime('2013-01-01'),
            'valor' => 10.50,
            'sequencial' => '123456789', // Até 11 dígitos
            'agencia' => 1172, // Até 4 dígitos
            'carteira' => 9, //  9
            'conta' => 403005, // Até 7 dígitos
            'contaDv' => 1,
            'agenciaDv' => 9,
            'convenio' => 1234567, // 4, 6 ou 7 dígitos
        ));

        $this->assertInstanceOf('OpenBoleto\\Banco\\Uniprime', $instance);
        $this->assertEquals('08491.17205 90012.345675 89040.300504 3 55650000001050', $instance->getLinhaDigitavel());

        $this->assertSame('00123456789', (string) $instance->getNossoNumero());
    }
}
