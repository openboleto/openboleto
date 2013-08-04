<?php

namespace Tests\OpenBoleto;

use OpenBoleto\BoletoAbstract;

class BancoMock extends BoletoAbstract
{
    protected $carteiras = array('10');

    /**
     * Retorna o sequencial como nosso número
     *
     * @return int|string
     */
    public function gerarNossoNumero()
    {
        return $this->getSequencial();
    }

    /**
     * Retorna 1 * 25
     *
     * @return string
     */
    public function getCampoLivre()
	{
        return str_repeat('1', 25);
	}
}
