<?php

namespace Tests\OpenBoleto\Banco;
use OpenBoleto\Banco\BancoDoBrasil;

class BancoDoBrasilTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateWithoutArgumentsShouldWork()
    {
        $this->assertInstanceOf('OpenBoleto\\Banco\\BancoDoBrasil', new BancoDoBrasil());
    }

    public function testInstantiateWithConvenio7LengthShouldWork()
    {
        $instance = new BancoDoBrasil(array(
            // Parâmetros obrigatórios
            'dataVencimento' => new \DateTime('2013-01-01'),
            'valor' => 10.50,
            'sequencial' => 1,
            'agencia' => 1545, // Até 4 dígitos
            'carteira' => 18,
            'conta' => 10403005, // Até 8 dígitos
            'convenio' => 1234567, // 4, 6 ou 7 dígitos
        ));

        $this->assertInstanceOf('OpenBoleto\\Banco\\BancoDoBrasil', $instance);
        $this->assertEquals('00190.00009 01234.567004 00000.001180 7 55650000001050', $instance->getLinhaDigitavel());
        $this->assertSame('12345670000000001', (string) $instance->getNossoNumero());
    }

    public function testInstantiateWithConvenio6LengthShouldWork()
    {
        $instance = new BancoDoBrasil(array(
            // Parâmetros obrigatórios
            'dataVencimento' => new \DateTime('2013-01-01'),
            'valor' => 10.50,
            'sequencial' => 1,
            'agencia' => 1545, // Até 4 dígitos
            'carteira' => 18,
            'conta' => 10403005, // Até 8 dígitos
            'convenio' => 123456, // 4, 6 ou 7 dígitos
        ));

        $this->assertInstanceOf('OpenBoleto\\Banco\\BancoDoBrasil', $instance);
        $this->assertEquals('00191.23454 60000.115455 10403.005183 1 55650000001050', $instance->getLinhaDigitavel());
        $this->assertEquals('12345600001-7', $instance->getNossoNumero());
    }

    public function testInstantiateWithConvenio6LengthAndCarteira21ShouldWork()
    {
        $instance = new BancoDoBrasil(array(
            // Parâmetros obrigatórios
            'dataVencimento' => new \DateTime('2013-01-01'),
            'valor' => 10.50,
            'sequencial' => 12345678901234567, // 17 dígitos
            'agencia' => 1545, // Até 4 dígitos
            'carteira' => 21, // Carteira especial para sequencial com 17 dígitos, porém equivalente a carteira 18 ou 18
            'conta' => 10403005, // Até 8 dígitos
            'convenio' => 123456, // 4, 6 ou 7 dígitos
        ));

        $this->assertInstanceOf('OpenBoleto\\Banco\\BancoDoBrasil', $instance);
        $this->assertEquals('00191.23454 61234.567891 01234.567210 6 55650000001050', $instance->getLinhaDigitavel());
        $this->assertSame('12345678901234567', (string) $instance->getNossoNumero());
    }

    public function testInstantiateWithConvenio4LengthShouldWork()
    {
        $instance = new BancoDoBrasil(array(
            // Parâmetros obrigatórios
            'dataVencimento' => new \DateTime('2013-01-01'),
            'valor' => 10.50,
            'sequencial' => 1,
            'agencia' => 1545, // Até 4 dígitos
            'carteira' => 18,
            'conta' => 10403005, // Até 8 dígitos
            'convenio' => 1234, // 4, 6 ou 7 dígitos

            // Caso queira um número sequencial de 17 dígitos, a cobrança deverá:
            // - Ser sem registro (Carteiras 16 ou 17)
            // - Convênio com 6 dígitos
            // Para isso, defina a carteira como 21 (mesmo sabendo que ela é 16 ou 17, isso é uma regra do banco)
        ));

        $this->assertInstanceOf('OpenBoleto\\Banco\\BancoDoBrasil', $instance);
        $this->assertEquals('00191.23405 00000.115451 10403.005183 5 55650000001050', $instance->getLinhaDigitavel());
        $this->assertSame('12340000001-1', (string) $instance->getNossoNumero());
    }
}
