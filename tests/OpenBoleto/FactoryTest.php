<?php

namespace OpenBoleto;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testIfLoadByBankIdReturnTheRightInstance()
    {
        $this->assertInstanceOf('OpenBoleto\Banco\BancoDoBrasil', BoletoFactory::loadByBankId(1));
        $this->assertInstanceOf('OpenBoleto\Banco\Santander', BoletoFactory::loadByBankId(33));
        $this->assertInstanceOf('OpenBoleto\Banco\Brb', BoletoFactory::loadByBankId(70));
        $this->assertInstanceOf('OpenBoleto\Banco\Unicred', BoletoFactory::loadByBankId(90));
        $this->assertInstanceOf('OpenBoleto\Banco\Bradesco', BoletoFactory::loadByBankId(237));
        $this->assertInstanceOf('OpenBoleto\Banco\Itau', BoletoFactory::loadByBankId(341));
    }
}