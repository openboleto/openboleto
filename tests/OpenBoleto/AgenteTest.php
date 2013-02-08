<?php

namespace OpenBoleto;
use OpenBoleto\Agente;

class AgenteTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiationWithoutArgumentsShouldWork()
    {
        $instance = new \OpenBoleto\Agente('nome','dcumento');
        $this->assertInstanceOf('OpenBoleto\Agente', $instance);
    }
}