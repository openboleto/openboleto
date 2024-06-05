<?php

namespace Tests\OpenBoleto;

use OpenBoleto\BoletoFactory;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    /**
     * @return void
     * @throws \OpenBoleto\Exception
     */
    public function testWhetherLoadByBankIdReturnsTheRightInstance()
    {
        $this->assertInstanceOf(\OpenBoleto\Banco\BancoDoBrasil::class, BoletoFactory::loadByBankId(1));
        $this->assertInstanceOf(\OpenBoleto\Banco\Santander::class, BoletoFactory::loadByBankId(33));
        $this->assertInstanceOf(\OpenBoleto\Banco\Brb::class, BoletoFactory::loadByBankId(70));
        $this->assertInstanceOf(\OpenBoleto\Banco\Unicred::class, BoletoFactory::loadByBankId(90));
        $this->assertInstanceOf(\OpenBoleto\Banco\Bradesco::class, BoletoFactory::loadByBankId(237));
        $this->assertInstanceOf(\OpenBoleto\Banco\Itau::class, BoletoFactory::loadByBankId(341));
        $this->assertInstanceOf(\OpenBoleto\Banco\Caixa::class, BoletoFactory::loadByBankId(104));
        $this->assertInstanceOf(\OpenBoleto\Banco\Uniprime::class, BoletoFactory::loadByBankId(84));
    }

    /**
     * @return void
     * @throws \OpenBoleto\Exception
     */
    public function testWhetherLoadByBankNameReturnsTheRightInstance()
    {
        $this->assertInstanceOf(\OpenBoleto\Banco\BancoDoBrasil::class, BoletoFactory::loadByBankName('BancoDoBrasil'));
        $this->assertInstanceOf(\OpenBoleto\Banco\Santander::class, BoletoFactory::loadByBankName('Santander'));
        $this->assertInstanceOf(\OpenBoleto\Banco\Brb::class, BoletoFactory::loadByBankName('Brb'));
        $this->assertInstanceOf(\OpenBoleto\Banco\Unicred::class, BoletoFactory::loadByBankName('Unicred'));
        $this->assertInstanceOf(\OpenBoleto\Banco\Bradesco::class, BoletoFactory::loadByBankName('Bradesco'));
        $this->assertInstanceOf(\OpenBoleto\Banco\Itau::class, BoletoFactory::loadByBankName('Itau'));
        $this->assertInstanceOf(\OpenBoleto\Banco\Caixa::class, BoletoFactory::loadByBankName('Caixa'));
        $this->assertInstanceOf(\OpenBoleto\Banco\Uniprime::class, BoletoFactory::loadByBankName('Uniprime'));
    }
}
