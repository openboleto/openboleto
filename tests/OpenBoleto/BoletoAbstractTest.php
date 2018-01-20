<?php

namespace Tests\OpenBoleto;

// Alias the PHPUnit 6.0 ancestor if available, else fall back to legacy ancestor
if (class_exists('\PHPUnit\Framework\TestCase', true)) {
  class KernelTestCaseAncestor extends \PHPUnit\Framework\TestCase {}
} else {
  class KernelTestCaseAncestor extends \PHPUnit_Framework_TestCase {}
}

class BoletoAbstractTest extends KernelTestCaseAncestor
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

    public function testInvalidCarteiraExceptionsShouldBeThrown()
    {
        $this->setExpectedException('OpenBoleto\\Exception');
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
