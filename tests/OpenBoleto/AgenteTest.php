<?php

namespace Tests\OpenBoleto;
use OpenBoleto\Agente;
class_alias('\PHPUnit_Framework_TestCase', 'PHPUnit\Framework\TestCase');

class AgenteTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiationWithoutArgumentsShouldWork()
    {
        $instance = new Agente('nome','123.456.789-01');
        $this->assertInstanceOf('OpenBoleto\Agente', $instance);
    }
}
