<?php

namespace Tests\OpenBoleto\Banco;
use OpenBoleto\Banco\Santander;

// Alias the PHPUnit 6.0 ancestor if available, else fall back to legacy ancestor
if (class_exists('\PHPUnit\Framework\TestCase', true)) {
  class KernelTestCaseAncestor extends \PHPUnit\Framework\TestCase {}
} else {
  class KernelTestCaseAncestor extends \PHPUnit_Framework_TestCase {}
}

class SantanderTest extends KernelTestCaseAncestor
{
    public function testInstantiateWithoutArgumentsShouldWork()
    {
        $this->assertInstanceOf('OpenBoleto\\Banco\\Santander', new Santander());
    }

    public function testInstantiateShouldWork()
    {
        $instance = new Santander(array(
            // Parâmetros obrigatórios
            'dataVencimento' => new \DateTime('2013-01-01'),
            'valor' => 23.00,
            'sequencial' => 12345678901, // Até 13 dígitos
            'agencia' => 1234, // Até 4 dígitos
            'carteira' => 102, // 101, 102 ou 201
            'conta' => 12345678, // Código do cedente: Até 8 dígitos
            // IOS – Seguradoras (Se 7% informar 7. Limitado a 9%)
            // Demais clientes usar 0 (zero)
            'ios' => '0', // Apenas para o Santander
        ));

        $this->assertInstanceOf('OpenBoleto\\Banco\\Santander', $instance);
        $this->assertEquals('03399.12347 56780.012342 56789.010107 6 55650000002300', $instance->getLinhaDigitavel()); 
        $this->assertSame('001234567890-0', (string) $instance->getNossoNumero());
    }
}
