<?php

namespace Tests\OpenBoleto;
use OpenBoleto\BoletoFactory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testWhetherLoadByBankIdReturnsTheRightInstance()
    {
        $this->assertInstanceOf('OpenBoleto\Banco\BancoDoBrasil', BoletoFactory::loadByBankId(1));
        $this->assertInstanceOf('OpenBoleto\Banco\Santander', BoletoFactory::loadByBankId(33));
        $this->assertInstanceOf('OpenBoleto\Banco\Brb', BoletoFactory::loadByBankId(70));
        $this->assertInstanceOf('OpenBoleto\Banco\Unicred', BoletoFactory::loadByBankId(90));
        $this->assertInstanceOf('OpenBoleto\Banco\Bradesco', BoletoFactory::loadByBankId(237));
        $this->assertInstanceOf('OpenBoleto\Banco\Itau', BoletoFactory::loadByBankId(341));
    }

    public function testWhetherLoadByBankNameReturnsTheRightInstance()
    {
        $this->assertInstanceOf('OpenBoleto\Banco\BancoDoBrasil', BoletoFactory::loadByBankName('BancoDoBrasil'));
        $this->assertInstanceOf('OpenBoleto\Banco\Santander', BoletoFactory::loadByBankName('Santander'));
        $this->assertInstanceOf('OpenBoleto\Banco\Brb', BoletoFactory::loadByBankName('Brb'));
        $this->assertInstanceOf('OpenBoleto\Banco\Unicred', BoletoFactory::loadByBankName('Unicred'));
        $this->assertInstanceOf('OpenBoleto\Banco\Bradesco', BoletoFactory::loadByBankName('Bradesco'));
        $this->assertInstanceOf('OpenBoleto\Banco\Itau', BoletoFactory::loadByBankName('Itau'));
    }
}