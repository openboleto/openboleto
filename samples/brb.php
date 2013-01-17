<?php

require '../src/OpenBoleto/BoletoAbstract.php';
require '../src/OpenBoleto/Agente.php';
require '../src/OpenBoleto/Banco/Brb.php';

use OpenBoleto\Banco\Brb;
use OpenBoleto\Agente;

$sacado = new Agente('Fernando Maia', 'ABC 302 Bloco N', '72000-000', 'Brasília', 'DF', '023.434.234-34');
$cedente = new Agente('Empresa de cosméticos LTDA', 'CLS 403 Lj 23', '71000-000', 'Brasília', 'DF', '02.232.123.123/0001-11');

$boleto = new Brb(array(
    'dataVencimento' => new DateTime('2013-10-11'),
    'sacado' => $sacado,
    'cedente' => $cedente,
    'valor' => 1235.45,
    'descricaoDemonstrativo' => array('Compra de materiais cosméticos', 'Compra de alicate'),
    'instrucoes' => array('Não pagar depois do vencimento'),
    'carteira' => 1,
    'nossoNumero' => '23342342',
    'agencia' => '435',
    'conta' => '6565654',
));

echo $boleto->getOutput();