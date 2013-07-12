<?php

namespace OpenBoleto;

use OpenBoleto\BancoMock;

class BoletoAbstractTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateShouldSetDefaultResourcePath()
    {
        $bank = new BancoMock();
        $this->assertTrue( file_exists($bank->getResourcePath()) );
    }
    
    public function testShouldReturnUserResourcePathIfPassed()
    {
    	$bank = new BancoMock(array('resourcePath' => __DIR__));
        $this->assertEquals(__DIR__, $bank->getResourcePath());	
    }
}
