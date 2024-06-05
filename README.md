# OpenBoleto

O OpenBoleto é uma biblioteca de código aberto para geração de boletos bancários, um meio de pagamento muito comum no Brasil. O foco é ser simples e ter uma arquitetura compatível com os recursos mais modernos do PHP.

Se esse projeto te auxiliou de alguma forma, você pode contribuir financeiramente para que eu possa continuar a desenvolver projetos open source. As contribuições podem ser:

# Ajude o Projeto a continuar, faça uma doação no PIX!

    b230c7a8-8743-459f-bae5-541ea46a3533

![302100763-dc530122-d9fb-41e4-9edf-dc9c32e74adc](https://github.com/fredroo/cnpj2sql/assets/6863089/1a6505e5-5039-4609-87af-ba84f03d4b08)

## Instalação

### Composer

Se você já conhece o **Composer** (o que é extremamente recomendado), simplesmente adicione a dependência abaixo à diretiva _"require"_ em seu **composer.json**:

```
"openboleto/openboleto": "dev-master"
```

### PSR-0 autoloader

Hoje praticamente qualquer framework utiliza deste padrão, então você deve [baixar](https://github.com/fredroo/openboleto/archive/master.zip) o OpenBoleto, colocar em uma pasta específica (geralmente _lib_ ou _vendor_) e procurar na documentação do seu framework para fazer com que o seu autoloader aponte o namespace **OpenBoleto** para a pasta **src** do OpenBoleto.

### Stand-alone library

Se você quer simplesmente baixar e dar um include, também é muito simples. Primeiro [baixe](https://github.com/fredroo/openboleto/archive/master.zip) (ou dê clone no repositório), e coloque em uma pasta específica. Depois, dê um include no arquivo **autoloader.php** e voilà!

## Gerando boletos

Essa é a melhor parte. Não poderia ser mais simples, veja um exemplo básico:

```php
use OpenBoleto\Agente;
use OpenBoleto\Banco\BancoDoBrasil;

$sacado = new Agente('Fernando Maia', '023.434.234-34', 'ABC 302 Bloco N', '72000-000', 'Brasília', 'DF');
$cedente = new Agente('Empresa de cosméticos LTDA', '02.123.123/0001-11', 'CLS 403 Lj 23', '71000-000', 'Brasília', 'DF');

$boleto = new BancoDoBrasil(array(
    // Parâmetros obrigatórios
    'dataVencimento' => new DateTime('2013-01-24'),
    'valor' => 23.00,
    'sequencial' => 1234567, // Para gerar o nosso número
    'sacado' => $sacado,
    'cedente' => $cedente,
    'agencia' => 1724, // Até 4 dígitos
    'carteira' => 18,
    'conta' => 10403005, // Até 8 dígitos
    'convenio' => 1234, // 4, 6 ou 7 dígitos
    'qrCode' => 'otpauth://totp/test?secret=B3JX4VCVJDVNXNZ5&issuer=chillerlan.net', // Caso o Boleto tenha registro para uso de QrCode Pix
));

echo $boleto->getOutput();
```
## Como adiquirir o QrCode Pix

O uso padrão do PIX não pode ser usado no boleto, à instituição bancária tem um serviço da qual você passa o boleto registrado e solicita a PIX, o banco vai te retornar uma string na qual é o PIX que identifica o boleto, para quando realizar o pagamento via PIX, tenha referência do boleto para ser baixado.


Sim, só isso! Lembre-se de que cada banco possui alguma particularidade, mas em geral são estes parâmetros os obrigatórios. Na pasta **samples** existe um exemplo funcional de cada banco, você pode verificar lá quais são os parâmetros necessários para cada banco.

## Bancos suportados

Atualmente o OpenBoleto funciona com os bancos abaixo:

<table>
 <tr>
  <th>Banco</th>
  <th>Situação HTML</th>
  <th>Situação JasperPHP(PDF)</th>
 </tr>
 <tr>
 <td>Banco de Brasília (BRB)</td>
 <td>Beta</td>
  <td>Beta</td>
 </tr>
 <tr>
 <td>Banco do Brasil</td>
 <td>Homologado</td>
  <td>Homologado</td>
 </tr>
 <tr>
  <td>Banco do Nordeste</td>
  <td>Beta</td>
   <td>Beta</td>
  </tr>
 <tr>
 <td>Banese</td>
 <td>Beta</td>
 <td>Beta</td>
 </tr>
 <tr>
 <td>Bradesco</td>
 <td>Homologado</td>
 <td>Homologado</td>
 </tr>
<tr>
 <td>Caixa (SIGCB)</td>
 <td>Beta</td>
 <td>Homologado</td>
 </tr>
<tr>
 <td>Cecred</td>
 <td>Beta</td>
 <td>Beta</td>
 </tr>
 <tr>
   <td>HSBC</td>
   <td>Beta</td>
   <td>Beta</td>
 </tr>
<tr>
 <td>Itaú</td>
 <td>Beta</td>
 <td>Homologado</td>
 </tr>
<tr>
 <td>Santander</td>
 <td>Beta</td>
 <td>Beta</td>
 </tr>
<tr>
 <td>Sicoob</td>
 <td>Beta</td>
 <td>Beta</td>
 </tr>
<tr>
 <td>Unicred</td>
 <td>Homologado</td>
 <td>Beta</td>
 </tr>
<tr>
 <td>Viacredi</td>
 <td>Beta</td>
 <td>Beta</td>
 </tr>
<tr>
 <td>Sicredi</td>
 <td>Beta</td>
 <td>Beta</td>
 </tr>
 <tr>
 <td>Banrisul</td>
 <td>Beta</td>
 <td>Beta</td>
 </tr>
  <tr>
 <td>C6 Bank</td>
 <td>Homologado</td>
 <td>Homologado</td>
 </tr>
  <tr>
 <td>ABC</td>
 <td>Homologado</td>
 <td>Homologado</td>
 </tr>
 <tr>
 <td>BV</td>
 <td>--</td>
 <td>BETA</td>
 </tr>

 </table>

### API Banco Inter

O Banco Inter está disponibilizando uma API para emissão de boletos. O projeto [ctodobom/APInter-PHP](https://github.com/ctodobom/APInter-PHP) implementa funções para facilitar o acesso à essa API.

## Integração com QuilhaSoft/JasperPHP

QuilhaSoft/JasperPHP é uma biblioteca puro PHP, que gera o boleto no formato PDF sem conversão do HTML, o layout do boleto pode ser editado com o JasperSoft Studio

Para ver funcionando abra o exemplo em https://github.com/QuilhaSoft/JasperPHP-OpenBoleto

Toda contribuição é bem vinda. Se você deseja adaptar o OpenBoleto a algum outro banco, fique à vontade para explorar o código, veja como é bastante simples integrar qualquer banco à biblioteca.

## Remessa e Retorno

https://github.com/QuilhaSoft/OpenCnabPHP

## Licença

- MIT License
