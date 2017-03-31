<?php

namespace Tests\OpenBoleto\Banco;
use OpenBoleto\Banco\CaixaSICOB;

class CaixaSICOBTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateWithoutArgumentsShouldWork()
    {
        $this->assertInstanceOf('OpenBoleto\\Banco\\CaixaSICOB', new CaixaSICOB());
    }

    public function testInstantiateShouldWork()
    {
        $instance = new CaixaSICOB(array(
            'dataVencimento' => new \DateTime('2015-08-27'),
            'valor' => 294.94,
            'sequencial' => '288027',
            'agencia' => '0501',
            'carteira' => 'SR',
            'conta' => '43375600001',
        ));

        $this->assertInstanceOf('OpenBoleto\\Banco\\CaixaSICOB', $instance);
        $this->assertEquals('10498.00020 88027.050140 33756.000015 7 65330000029494', $instance->getLinhaDigitavel());
        $this->assertSame('8000288027-8', (string) $instance->getNossoNumero());
    }

}
