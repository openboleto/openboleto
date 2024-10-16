<?php

namespace Tests\OpenBoleto\Banco;
use OpenBoleto\Banco\Caixa;
use OpenBoleto\Exception;
use PHPUnit\Framework\TestCase;

class CaixaTest extends TestCase
{
    /**
     * @return void
     */
    public function testInstantiateWithoutArgumentsShouldWork()
    {
        $this->assertInstanceOf(\OpenBoleto\Banco\Caixa::class, new Caixa());
    }

    /**
     * @return void
     */
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

        $this->assertInstanceOf(\OpenBoleto\Banco\Caixa::class, $instance);
        $this->assertEquals('10494.33756 65000.200546 00000.006106 8 60060000081094', $instance->getLinhaDigitavel());
        $this->assertSame('24000005000000061-2', (string) $instance->getNossoNumero());
    }

    /**
     * @return void
     */
    public function testInstantiatingShouldWorkWithSevenDigitBeneficiary()
    {
        $instance = new Caixa(array(
            'dataVencimento' => new \DateTime('2024-11-04'),
            'valor' => 20.00,
            'sequencial' => '157460299',
            'agencia' => '3454',
            'carteira' => 'RG',
            'conta' => '1242119',
        ));

        $this->assertInstanceOf(\OpenBoleto\Banco\Caixa::class, $instance);

        $this->assertEquals('10491.24215 19000.100040 15746.029915 1 98900000002000', $instance->getLinhaDigitavel());
        $this->assertSame('14000000157460299-6', (string) $instance->getNossoNumero());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testInstantiatingShouldFailWithOutOfRangeBeneficiary()
    {

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Código do beneficiário fora da faixa permitida pela Caixa.');

        $instance = $this->getMockBuilder(\OpenBoleto\Banco\Caixa::class)
            ->onlyMethods(['getConta'])
            ->getMock();

        $instance->method('getConta')->willReturn('1100000');

        $instance->getCampoLivre();
    }
}
