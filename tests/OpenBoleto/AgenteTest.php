<?php

namespace Tests\OpenBoleto;
use OpenBoleto\Agente;
use PHPUnit\Framework\TestCase;


class AgenteTest extends TestCase
{
    public function testInstantiationWithoutArgumentsShouldWork()
    {
        $instance = new Agente('nome','123.456.789-01');
        $this->assertInstanceOf('OpenBoleto\Agente', $instance);
    }
}
