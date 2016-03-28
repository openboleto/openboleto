<?php

require '../autoloader.php';

use OpenBoleto\Banco\Caixa;
use OpenBoleto\Agente;
use Dompdf\Dompdf;

$sacado = new Agente('Fernando Maia', '023.434.234-34', 'ABC 302 Bloco N', '72000-000', 'Brasília', 'DF');
$cedente = new Agente('Empresa de cosméticos LTDA', '02.123.123/0001-11', 'CLS 403 Lj 23', '71000-000', 'Brasília', 'DF');

$boleto = new Caixa(array(
	// Parâmetros obrigatórios
	'dataVencimento' => new DateTime('2013-01-24'),
	'valor' => 23.00,
	'sequencial' => 1234567,
	'sacado' => $sacado,
	'cedente' => $cedente,
	'agencia' => '0501', // Até 4 dígitos
	'carteira' => 'SR', // SR => Sem Registro ou RG => Registrada
	'conta' => '433756', // Até 6 dígitos

	// Parâmetros recomendáveis
	//'logoPath' => 'http://empresa.com.br/logo.jpg', // Logo da sua empresa
	'contaDv' => 2,
	'agenciaDv' => 1,
	'descricaoDemonstrativo' => array( // Até 5
		'Compra de materiais cosméticos',
		'Compra de alicate',
	),
	'instrucoes' => array( // Até 8
		'Após o dia 30/11 cobrar 2% de mora e 1% de juros ao dia.',
		'Não receber após o vencimento.',
	),

	// Parâmetros opcionais
	//'resourcePath' => '../resources',
	//'moeda' => Caixa::MOEDA_REAL,
	//'dataDocumento' => new DateTime(),
	//'dataProcessamento' => new DateTime(),
	//'contraApresentacao' => true,
	//'pagamentoMinimo' => 23.00,
	//'aceite' => 'N',
	//'especieDoc' => 'ABC',
	//'numeroDocumento' => '123.456.789',
	//'usoBanco' => 'Uso banco',
	//'layout' => 'caixa.phtml',
	//'logoPath' => 'http://boletophp.com.br/img/opensource-55x48-t.png',
	//'sacadorAvalista' => new Agente('Antônio da Silva', '02.123.123/0001-11'),
	//'descontosAbatimentos' => 123.12,
	//'moraMulta' => 123.12,
	//'outrasDeducoes' => 123.12,
	//'outrosAcrescimos' => 123.12,
	//'valorCobrado' => 123.12,
	//'valorUnitario' => 123.12,
	//'quantidade' => 1,
));
$sacado2 = new Agente('Fernando Maia', '023.434.234-34', 'ABC 302 Bloco N', '72000-000', 'Brasília', 'DF');
$cedente2 = new Agente('Empresa de cosméticos LTDA', '02.123.123/0001-11', 'CLS 403 Lj 23', '71000-000', 'Brasília', 'DF');

$boleto2 = new Caixa(array(
	// Parâmetros obrigatórios
	'dataVencimento' => new DateTime('2013-01-24'),
	'valor' => 23.00,
	'sequencial' => 1234567,
	'sacado' => $sacado,
	'cedente' => $cedente,
	'agencia' => '0501', // Até 4 dígitos
	'carteira' => 'SR', // SR => Sem Registro ou RG => Registrada
	'conta' => '433756', // Até 6 dígitos

	// Parâmetros recomendáveis
	//'logoPath' => 'http://empresa.com.br/logo.jpg', // Logo da sua empresa
	'contaDv' => 2,
	'agenciaDv' => 1,
	'descricaoDemonstrativo' => array( // Até 5
		'Compra de materiais cosméticos',
		'Compra de alicate',
	),
	'instrucoes' => array( // Até 8
		'Após o dia 30/11 cobrar 2% de mora e 1% de juros ao dia.',
		'Não receber após o vencimento.',
	),

	// Parâmetros opcionais
	//'resourcePath' => '../resources',
	//'moeda' => Caixa::MOEDA_REAL,
	//'dataDocumento' => new DateTime(),
	//'dataProcessamento' => new DateTime(),
	//'contraApresentacao' => true,
	//'pagamentoMinimo' => 23.00,
	//'aceite' => 'N',
	//'especieDoc' => 'ABC',
	//'numeroDocumento' => '123.456.789',
	//'usoBanco' => 'Uso banco',
	'layout' => 'default-carne-pdf.phtml',
	//'logoPath' => 'http://boletophp.com.br/img/opensource-55x48-t.png',
	//'sacadorAvalista' => new Agente('Antônio da Silva', '02.123.123/0001-11'),
	//'descontosAbatimentos' => 123.12,
	//'moraMulta' => 123.12,
	//'outrasDeducoes' => 123.12,
	//'outrosAcrescimos' => 123.12,
	//'valorCobrado' => 123.12,
	//'valorUnitario' => 123.12,
	//'quantidade' => 1,
));

$sacado3 = new Agente('Fernando Maia', '023.434.234-34', 'ABC 302 Bloco N', '72000-000', 'Brasília', 'DF');
$cedente3 = new Agente('Empresa de cosméticos LTDA', '02.123.123/0001-11', 'CLS 403 Lj 23', '71000-000', 'Brasília', 'DF');

$boleto3 = new Caixa(array(
	// Parâmetros obrigatórios
	'dataVencimento' => new DateTime('2013-01-24'),
	'valor' => 23.00,
	'sequencial' => 1234567,
	'sacado' => $sacado,
	'cedente' => $cedente,
	'agencia' => '0501', // Até 4 dígitos
	'carteira' => 'SR', // SR => Sem Registro ou RG => Registrada
	'conta' => '433756', // Até 6 dígitos

	// Parâmetros recomendáveis
	//'logoPath' => 'http://empresa.com.br/logo.jpg', // Logo da sua empresa
	'contaDv' => 2,
	'agenciaDv' => 1,
	'descricaoDemonstrativo' => array( // Até 5
		'Compra de materiais cosméticos',
		'Compra de alicate',
	),
	'instrucoes' => array( // Até 8
		'Após o dia 30/11 cobrar 2% de mora e 1% de juros ao dia.',
		'Não receber após o vencimento.',
	),

	// Parâmetros opcionais
	//'resourcePath' => '../resources',
	//'moeda' => Caixa::MOEDA_REAL,
	//'dataDocumento' => new DateTime(),
	//'dataProcessamento' => new DateTime(),
	//'contraApresentacao' => true,
	//'pagamentoMinimo' => 23.00,
	//'aceite' => 'N',
	//'especieDoc' => 'ABC',
	//'numeroDocumento' => '123.456.789',
	//'usoBanco' => 'Uso banco',
	'layout' => 'default-carne-pdf.phtml',
	//'logoPath' => 'http://boletophp.com.br/img/opensource-55x48-t.png',
	//'sacadorAvalista' => new Agente('Antônio da Silva', '02.123.123/0001-11'),
	//'descontosAbatimentos' => 123.12,
	//'moraMulta' => 123.12,
	//'outrasDeducoes' => 123.12,
	//'outrosAcrescimos' => 123.12,
	//'valorCobrado' => 123.12,
	//'valorUnitario' => 123.12,
	//'quantidade' => 1,
));

$sacado4 = new Agente('Fernando Maia', '023.434.234-34', 'ABC 302 Bloco N', '72000-000', 'Brasília', 'DF');
$cedente4 = new Agente('Empresa de cosméticos LTDA', '02.123.123/0001-11', 'CLS 403 Lj 23', '71000-000', 'Brasília', 'DF');

$boleto4 = new Caixa(array(
	// Parâmetros obrigatórios
	'dataVencimento' => new DateTime('2013-01-24'),
	'valor' => 23.00,
	'sequencial' => 1234567,
	'sacado' => $sacado,
	'cedente' => $cedente,
	'agencia' => '0501', // Até 4 dígitos
	'carteira' => 'SR', // SR => Sem Registro ou RG => Registrada
	'conta' => '433756', // Até 6 dígitos

	// Parâmetros recomendáveis
	//'logoPath' => 'http://empresa.com.br/logo.jpg', // Logo da sua empresa
	'contaDv' => 2,
	'agenciaDv' => 1,
	'descricaoDemonstrativo' => array( // Até 5
		'Compra de materiais cosméticos',
		'Compra de alicate',
	),
	'instrucoes' => array( // Até 8
		'Após o dia 30/11 cobrar 2% de mora e 1% de juros ao dia.',
		'Não receber após o vencimento.',
	),

	// Parâmetros opcionais
	//'resourcePath' => '../resources',
	//'moeda' => Caixa::MOEDA_REAL,
	//'dataDocumento' => new DateTime(),
	//'dataProcessamento' => new DateTime(),
	//'contraApresentacao' => true,
	//'pagamentoMinimo' => 23.00,
	//'aceite' => 'N',
	//'especieDoc' => 'ABC',
	//'numeroDocumento' => '123.456.789',
	//'usoBanco' => 'Uso banco',
	'layout' => 'default-carne-pdf.phtml',
	//'logoPath' => 'http://boletophp.com.br/img/opensource-55x48-t.png',
	//'sacadorAvalista' => new Agente('Antônio da Silva', '02.123.123/0001-11'),
	//'descontosAbatimentos' => 123.12,
	//'moraMulta' => 123.12,
	//'outrasDeducoes' => 123.12,
	//'outrosAcrescimos' => 123.12,
	//'valorCobrado' => 123.12,
	//'valorUnitario' => 123.12,
	//'quantidade' => 1,
));

$htmlout = $boleto->getOutput(1); //pega o output do primeiro boleto 
$htmlout .= $boleto2->getOutput(2); // pega o output do segundo boleto
$htmlout .= $boleto3->getOutput(3); // pega o output do terceiro boleto
$htmlout .= $boleto4->getOutput(1); // pega o output do quarto boleto -- note que o parametro passado volta a ser um se usar 
					// em um looping use $counter = (is_int($counter/3))?1:$counter+1;
$htmlout = str_ireplace(array("::RECIBO::"),array($htmlout),$boleto2->getHtmlBase()); // substiui $htmlout na base para multiplos boletos

// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml($boleto->getOutput());

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');


// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream("teste",array('Attachment'=>0));
