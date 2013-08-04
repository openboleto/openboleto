<?php

namespace Tests\OpenBoleto\Banco;
use OpenBoleto\Banco\Itau;

class ItauTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateWithoutArgumentsShouldWork()
    {
        $this->assertInstanceOf('OpenBoleto\\Banco\\Itau', new Itau());
    }

    public function testInstantiateShouldWork()
    {
        $instance = new Itau(array(
            // Parâmetros obrigatórios
            'dataVencimento' => new \DateTime('2013-01-01'),
            'valor' => 10.50,
            'sequencial' => 12345678, // 8 dígitos
            'agencia' => 1724, // 4 dígitos
            'carteira' => 112, // 3 dígitos
            'conta' => 12345, // 5 dígitos

            // Parâmetro obrigatório somente se a carteira for
            // 107, 122, 142, 143, 196 ou 198
            'codigoCliente' => 12345, // 5 dígitos
            'numeroDocumento' => 1234567, // 7 dígitos
        ));

        $this->assertInstanceOf('OpenBoleto\\Banco\\Itau', $instance);
        $this->assertEquals('34191.12127 34567.881726 41234.580003 1 55650000001050', $instance->getLinhaDigitavel());
        $this->assertSame('112/12345678-8', (string) $instance->getNossoNumero());
    }

    public function testInstantiateWithCarteira107ShouldWork()
    {
        $instance = new Itau(array(
            // Parâmetros obrigatórios
            'dataVencimento' => new \DateTime('2013-01-01'),
            'valor' => 10.50,
            'sequencial' => 12345678, // 8 dígitos
            'agencia' => 1724, // 4 dígitos
            'carteira' => 107, // 3 dígitos
            'conta' => 12345, // 5 dígitos

            // Parâmetro obrigatório somente se a carteira for
            // 107, 122, 142, 143, 196 ou 198
            'codigoCliente' => 66677, // 5 dígitos
            'numeroDocumento' => 1234567, // 7 dígitos
        ));

        $this->assertInstanceOf('OpenBoleto\\Banco\\Itau', $instance);
        $this->assertEquals('34191.07127 34567.812341 56766.677001 9 55650000001050', $instance->getLinhaDigitavel());
        $this->assertSame('107/12345678-0', (string) $instance->getNossoNumero());
    }
}
