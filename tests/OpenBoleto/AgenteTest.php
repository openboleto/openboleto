<?php

namespace Tests\OpenBoleto;
use OpenBoleto\Agente;

// Alias the PHPUnit 6.0 ancestor if available, else fall back to legacy ancestor
if (class_exists('\PHPUnit\Framework\TestCase', true)) {
  class KernelTestCaseAncestor extends \PHPUnit\Framework\TestCase {}
} else {
  class KernelTestCaseAncestor extends \PHPUnit_Framework_TestCase {}
}

class AgenteTest extends KernelTestCaseAncestor
{
    public function testInstantiationWithoutArgumentsShouldWork()
    {
        $instance = new Agente('nome','123.456.789-01');
        $this->assertInstanceOf('OpenBoleto\Agente', $instance);
    }
}
