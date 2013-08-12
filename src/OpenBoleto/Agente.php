<?php

/*
 * OpenBoleto - Geração de boletos bancários em PHP
 *
 * LICENSE: The MIT License (MIT)
 *
 * Copyright (C) 2013 Estrada Virtual
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this
 * software and associated documentation files (the "Software"), to deal in the Software
 * without restriction, including without limitation the rights to use, copy, modify,
 * merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies
 * or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace OpenBoleto;

/**
 * Classe de agente usuário do boleto
 *
 * @package    OpenBoleto
 * @author     Daniel Garajau <http://github.com/kriansa>
 * @copyright  Copyright (c) 2013 Estrada Virtual (http://www.estradavirtual.com.br)
 * @license    MIT License
 * @version    1.0
 */
class Agente
{
    /**
     * @var string
     */
    protected $nome;

    /**
     * @var string
     */
    protected $endereco;

    /**
     * @var string
     */
    protected $cep;

    /**
     * @var string
     */
    protected $uf;

    /**
     * @var string
     */
    protected $cidade;

    /**
     * @var string
     */
    protected $documento;

    /**
     * Construtor
     *
     * @param string $nome
     * @param string $documento
     * @param string $endereco
     * @param string $cep
     * @param string $cidade
     * @param string $uf
     */
    public function __construct($nome, $documento, $endereco = null, $cep = null, $cidade = null, $uf = null)
    {
        $this->setNome($nome);
        $this->setDocumento($documento);
        $endereco and $this->setEndereco($endereco);
        $cep and $this->setCep($cep);
        $cidade and $this->setCidade($cidade);
        $uf and $this->setUf($uf);
    }

    /**
     * Define o CEP
     *
     * @param string $cep
     */
    public function setCep($cep)
    {
        $this->cep = $cep;
    }

    /**
     * Retorna o CEP
     *
     * @return string
     */
    public function getCep()
    {
        return $this->cep;
    }

    /**
     * Define a cidade
     *
     * @param string $cidade
     */
    public function setCidade($cidade)
    {
        $this->cidade = $cidade;
    }

    /**
     * Retorna a cidade
     *
     * @return string
     */
    public function getCidade()
    {
        return $this->cidade;
    }

    /**
     * Define o documento (CPF ou CNPJ)
     *
     * @param string $documento
     */
    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }

    /**
     * Retorna o documento (CPF ou CNPJ)
     *
     * @return string
     */
    public function getDocumento()
    {
        return $this->documento;
    }

    /**
     * Define o endereço
     *
     * @param string $endereco
     */
    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;
    }

    /**
     * Retorna o endereço
     *
     * @return string
     */
    public function getEndereco()
    {
        return $this->endereco;
    }

    /**
     * Define o nome
     *
     * @param string $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     * Retorna o nome
     *
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Define a UF
     *
     * @param string $uf
     */
    public function setUf($uf)
    {
        $this->uf = $uf;
    }

    /**
     * Retorna a UF
     *
     * @return string
     */
    public function getUf()
    {
        return $this->uf;
    }

    /**
     * Retorna o nome e o documento formatados
     *
     * @return string
     */
    public function getNomeDocumento()
    {
        if (!$this->getDocumento()) {
            return $this->getNome();
        } else {
            return $this->getNome() . ' / ' . $this->getTipoDocumento() . ': ' . $this->getDocumento();
        }
    }

    /**
     * Retorna se o tipo do documento é CPF ou CNPJ ou Documento
     *
     * @return string
     */
    public function getTipoDocumento()
    {
        $documento = trim($this->getDocumento());

        if (preg_match('/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}$/', $documento)) {
            return 'CPF';
        } else if (preg_match('#^[0-9]{2}\.[0-9]{3}\.[0-9]{3}/[0-9]{4}-[0-9]{2}$#', $documento)) {
            return 'CNPJ';
        }

        return 'Documento';
    }

    /**
     * Retorna o endereço formatado para a linha 2 de endereço
     *
     * Ex: 71000-000 - Brasília - DF
     *
     * @return string
     */
    public function getCepCidadeUf()
    {
        $dados = array_filter(array($this->getCep(), $this->getCidade(), $this->getUf()));
        return implode(' - ', $dados);
    }
}