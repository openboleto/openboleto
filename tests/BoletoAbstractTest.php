<?php

namespace Tests\OpenBoleto;


use PHPUnit\Framework\TestCase;

class BoletoAbstractTest extends TestCase
{
    /**
     * @return void
     */
    public function testInstantiateShouldSetDefaultResourcePath()
    {
        $bank = new BancoMock();
        $this->assertTrue(file_exists($bank->getResourcePath()));
    }

    /**
     * @return void
     */
    public function testShouldReturnUserResourcePathIfPassed()
    {
        $bank = new BancoMock(array('resourcePath' => __DIR__));
        $this->assertEquals(__DIR__, $bank->getResourcePath());
    }

    /**
     * @return void
     */
    public function testInvalidCarteiraExceptionsShouldBeThrown()
    {
        $this->expectException(\OpenBoleto\Exception::class);
        new BancoMock(
            array(
                'carteira' => 99,
            )
        );
    }

    /**
     * @return void
     */
    public function testValidCarteiraShouldWork()
    {
        $instance = new BancoMock(
            array(
                'carteira' => 10,
            )
        );

        $this->assertInstanceOf(\OpenBoleto\BoletoAbstract::class, $instance);
    }
}