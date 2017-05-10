<?php

namespace OpenBoletoTest;

class BoletoAbstractTest extends \PHPUnit\Framework\TestCase
{
    public function testInstantiateShouldSetDefaultResourcePath()
    {
        $bank = new BancoMock();
        $this->assertTrue(file_exists($bank->getResourcePath()));
    }
    
    public function testShouldReturnUserResourcePathIfPassed()
    {
        $bank = new BancoMock(array('resourcePath' => __DIR__));
        $this->assertEquals(__DIR__, $bank->getResourcePath());
    }

    /**
     * @expectedException OpenBoleto\Exception
     */
    public function testInvalidCarteiraExceptionsShouldBeThrown()
    {
        new BancoMock(array(
            'carteira' => 99,
        ));
    }

    public function testValidCarteiraShouldWork()
    {
        $instance = new BancoMock(array(
            'carteira' => 10,
        ));

        $this->assertInstanceOf('OpenBoleto\BoletoAbstract', $instance);
    }
}
