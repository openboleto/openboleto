# JasperPHP - OpenBoleto 

O OpenBoleto é uma biblioteca de código aberto para geração de boletos bancários, um meio de pagamento muito comum no Brasil. O foco é ser simples e ter uma arquitetura compatível com os recursos mais modernos do PHP.

## Instalação
### Composer
Se você já conhece o **Composer** (o que é extremamente recomendado), simplesmente adicione a dependência abaixo à diretiva *"require"* seu **composer.json**:
```
"kriansa/openboleto": "dev-master"
```

Ou, caso você deseje usar o release v1.0 (12/08/2013)
```
"kriansa/openboleto": "v1.0"
```

###PSR-0 autoloader
Hoje praticamente qualquer framework utiliza deste padrão, então você deve [baixar](https://github.com/kriansa/openboleto/archive/master.zip) o OpenBoleto, colocar em uma pasta específica (geralmente *lib* ou *vendor*) e procurar na documentação do seu framework para fazer com que o seu autoloader aponte o namespace **OpenBoleto** para a pasta **src** do OpenBoleto.

###Stand-alone library
Se você quer simplesmente baixar e dar um include, também é muito simples. Primeiro [baixe](https://github.com/kriansa/openboleto/archive/master.zip) (ou dê clone no repositório), e coloque em uma pasta específica. Depois, dê um include no arquivo **autoloader.php** e voilá!

## Gerando boletos
Essa é a melhor parte. Não poderia ser mais simples, veja um exemplo básico:
```php
use OpenBoleto\Banco\BancoDoBrasil;
use OpenBoleto\Agente;
 
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
));
 
echo $boleto->getOutput();
```

Sim, só isso! Lembre-se de que cada banco possui alguma particularidade, mas em geral são estes parâmetros os obrigatórios. Na pasta **samples** possui um exemplo funcional de cada banco, você pode verificar lá quais são os parâmetros necessários para cada banco.

## Bancos suportados
Atualmente o OpenBoleto funciona com os bancos abaixo:

* Banco de Brasília (BRB)
* Banco do Brasil
* Bradesco
* Caixa (SIGCB)
* Itaú
* Santander
* Unicred

Toda contribuição é bem vinda. Se você deseja adaptar o OpenBoleto a algum outro banco, fique à vontade para explorar o código, veja como é bastante simples integrar qualquer banco à biblioteca.

## QuilhaSoft/JasperPHP
Biblioteca que gera boletos em PDF atravez de layouts preconstruidos atravez do editor JasperSoft Studio, biblioteca contruida inteiramente em php sem a necessidade de adicionar um servidor JAVA
Um exemplo de utilização esta no arquivo https://github.com/QuilhaSoft/JasperPHP-openboleto/blob/master/samples/itauJasper.php
Amostra de pdf
<img src="https://cloud.githubusercontent.com/assets/17881422/19604441/09b452f4-9794-11e6-836a-397b75947e51.png">
Tela do editor
<img src="https://cloud.githubusercontent.com/assets/17881422/19604460/195d01a6-9794-11e6-8d82-6aac6c8647ff.png">

Para editar o layout do boleto instale o JasperSoft Studio e abra os arquivos .xlmr na pasta https://github.com/QuilhaSoft/JasperPHP-openboleto/tree/master/samples/app.jrxml/bol01Files
## Homologação
Os layouts produzidos nesse exemplo estão Homologados para os bancos Caixa e Itau

Para usar instale as duas bibliotecas

## Licença

* MIT License
