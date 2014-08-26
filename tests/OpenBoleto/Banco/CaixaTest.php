<?php

namespace Tests\OpenBoleto\Banco;
use OpenBoleto\Banco\Caixa;

class CaixaTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateWithoutArgumentsShouldWork()
    {
        $this->assertInstanceOf('OpenBoleto\\Banco\\Caixa', new Caixa());
    }

    public function testInstantiateShouldWork()
    {
        $instance = new Caixa(array(
            'dataVencimento' => new \DateTime('2014-03-18'),
            'valor' => 810.94,
            'sequencial' => '5000000061',
            'agencia' => '0501',
            'carteira' => 'SR',
            'conta' => '433756',
        ));

        $this->assertInstanceOf('OpenBoleto\\Banco\\Caixa', $instance);
        $this->assertEquals('10494.33756 65000.200546 00000.006106 8 60060000081094', $instance->getLinhaDigitavel());
        $this->assertSame('24000005000000061-2', (string) $instance->getNossoNumero());
    }

}
