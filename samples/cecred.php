<?php

    require '../autoloader.php';

    use OpenBoleto\Banco\Cecred;
    use OpenBoleto\Agente;

    $sacado = new Agente(
        'Nome do Pagador',
        '000.000.000-00',
        'Endereço do Pagador',
        '89000-000', // CEP
        'Cidade do Pagador',
        'UF do Pagador'
    );

    $cedente = new Agente(
        'Nome do Benefíciário',
        '00.000.000/0001-01', // CPF ou CNPJ do beneficiário
        'Endereço do Benefíciário',
        '89000-000', // CEP
        'Cidade do Benefíciário',
        'UF do Benefíciário'
    );

    // Vencimento para daqui a 5 dias
    $vencimento = new DateTime('now');
    $vencimento->add(new DateInterval('P5D'));

    $boleto = new Cecred(array(
        // Parâmetros obrigatórios
        'dataVencimento' => $vencimento,
        'valor' => 2.95,
        'sequencial' => 1,
        'sacado' => $sacado,
        'cedente' => $cedente,
        'agencia' => '9999', // Até 4 dígitos
        'agenciaDv' => 1,
        'carteira' => 1,
        'conta' => '99999', // Até 8 dígitos
        'contaDv' => 2,
        'convenio' => '010120', // 6 digitos

        // Parâmetros recomendáveis
        'descricaoDemonstrativo' => array( // Limite de até 5 linhas no array
            '',
            '',
            '',
            '',
            ''
        ),
        'instrucoes' => array( // Limite de até 8 linhas
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'Após o vencimento, acesse http://www.viacredi.coop.br para atualizar seu boleto.'
        ),

        // Parâmetros opcionais
        'dataDocumento' => new DateTime(),
        'dataProcessamento' => new DateTime(),
        'aceite' => 'N',
        'especieDoc' => 'DM',
        'numeroDocumento' => '27.030195.10',
        //'contraApresentacao' => true,
        //'pagamentoMinimo' => 23.00,
        //'usoBanco' => 'Uso banco',
        //'sacadorAvalista' => new Agente('Antônio da Silva', '02.123.123/0001-11'),
        //'descontosAbatimentos' => 123.12,
        //'moraMulta' => 123.12,
        //'outrasDeducoes' => 123.12,
        //'outrosAcrescimos' => 123.12,
        //'valorCobrado' => 123.12,
        //'valorUnitario' => 123.12,
        //'quantidade' => 1,
    ));

    echo $boleto->getOutput();