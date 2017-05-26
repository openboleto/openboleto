<?php

namespace OpenBoleto;

use fpdf\FPDF;

class GeradorPdf
{
    public static function getDirImages()
    {
        return __DIR__ . '/../../resources/pdf/';
    }

    public function gerar(BoletoAbstract $boleto)
    {
        $PDF = new FPDF("P", 'mm', 'A4');

        $PDF->AddPage();


        $PDF->SetFont('Arial', '', 8);
        $PDF->Ln();

        //Select Arial bold 8
        $PDF->SetFont('Arial', 'B', 6);

        $PDF->Cell(190, 4, utf8_decode("Instrução de Impressão:"), '', 1, 'C');
        $PDF->Cell(
            190,
            4,
            utf8_decode(
                "Imprimir em impressora jato de tinta (ink jet) ou laser em qualidade normal. (Não use modo econômico)."
            ),
            '',
            1,
            'C'
        );

        $PDF->Cell(
            190,
            4,
            utf8_decode("Utilize folha A4 (210 x 297 mm) ou carta (216 x 279 mm) - Corte na linha indicada:"),
            '',
            1,
            'C'
        );

        $PDF->Ln();
        $PDF->SetFont('Arial', 'B', 6);
        $PDF->Cell(190, 2, 'Recibo do Sacado', '', 1, 'R');
        $PDF->SetFont('Arial', '', 12);
        $PDF->Cell(
            190,
            2,
            '--------------------------------------------------------------------------------------------------------------------------------------',
            '',
            0,
            'L'
        );

        $PDF->Ln();
        $PDF->Ln(15);
        $PDF->SetFont('Arial', '', 9);

        $PDF->Cell(50, 10, '', 'B', 0, 'L');
        $PDF->Image(self::getDirImages() . $boleto->getLogoBanco(), 10, 43, 40, 10);
        $PDF->SetFont('Arial', 'B', 14);
        $PDF->Cell(20, 10, $boleto->getCodigoBancoComDv(), 'LBR', 0, 'C');

        $PDF->SetFont('Arial', 'B', 9);
        $PDF->Cell(120, 10, $boleto->getLinhaDigitavel(), 'B', 1, 'R');

        $PDF->SetFont('Arial', '', 6);
        $PDF->Cell(85, 3, 'Cedente', 'LR', 0, 'L');
        $PDF->Cell(30, 3, utf8_decode('Agência/Código do Cedente'), 'R', 0, 'L');
        $PDF->Cell(15, 3, utf8_decode('Espécie'), 'R', 0, 'L');
        $PDF->Cell(20, 3, 'Quantidade', 'R', 0, 'L');
        $PDF->Cell(40, 3, utf8_decode('Carteira/Nosso número'), '', 1, 'L');

        $PDF->SetFont('Arial', '', 7);
        $PDF->Cell(85, 5, utf8_decode($boleto->getSacado()->getNome()), 'BLR', 0, 'L');
        $PDF->Cell(
            30,
            5,
            $boleto->getAgencia() . "-" . $boleto->getAgenciaDv() . " / " . $boleto->getConta() . "-" . $boleto->getContaDv(),
            'BR',
            0,
            'L'
        );
        $PDF->Cell(15, 5, $boleto->getEspecieDoc(), 'BR', 0, 'L');
        $PDF->Cell(20, 5, "001", 'BR', 0, 'L');
        $PDF->Cell(40, 5, $boleto->getCarteira() . " / " . $boleto->getNossoNumero(), 'B', 1, 'R');

        $PDF->SetFont('Arial', '', 6);
        $PDF->Cell(60, 3, utf8_decode('Número do Documento'), 'LR', 0, 'L');
        $PDF->Cell(35, 3, 'CPF/CNPJ', 'R', 0, 'L');
        $PDF->Cell(35, 3, 'Vencimento', 'R', 0, 'L');
        $PDF->Cell(60, 3, 'Valor Documento', 'L', 1, 'L');

        $PDF->SetFont('Arial', '', 7);
        $PDF->Cell(60, 5, $boleto->getNossoNumero(), 'BLR', 0, 'L');
        $PDF->Cell(35, 5, $boleto->getCedente()->getDocumento(), 'BR', 0, 'L');
        $PDF->Cell(35, 5, $boleto->getDataVencimento()->format('d/m/Y'), 'BR', 0, 'L');
        $PDF->Cell(60, 5, $boleto->getValor(), 'B', 1, 'R');

        $PDF->SetFont('Arial', '', 6);
        $PDF->Cell(33, 3, '(-)Desconto/Abatimentos', 'LR', 0, 'L');
        $PDF->Cell(32, 3, utf8_decode('(-)Outras deduções'), 'R', 0, 'L');
        $PDF->Cell(32, 3, '(+)Mora/Multa', 'R', 0, 'L');
        $PDF->Cell(33, 3, utf8_decode('(+)Outros acréscimos'), '', 0, 'L');
        $PDF->Cell(60, 3, '(*)Valor Cobrado', 'L', 1, 'L');

        $PDF->SetFont('Arial', '', 7);
        $PDF->Cell(33, 5, '', 'BLR', 0, 'L');
        $PDF->Cell(32, 5, '', 'BR', 0, 'L');
        $PDF->Cell(32, 5, '', 'BR', 0, 'L');
        $PDF->Cell(33, 5, '', 'BR', 0, 'L');
        $PDF->Cell(60, 5, '', 'B', 1, 'R');

        $PDF->SetFont('Arial', '', 6);
        $PDF->Cell(190, 3, 'Sacado', 'L', 1, 'L');

        $PDF->SetFont('Arial', '', 7);
        $PDF->Cell(190, 5, utf8_decode($boleto->getSacado()->getNome()), 'L', 1, 'L');
        $PDF->Cell(
            190,
            5,
            utf8_decode(
                $boleto->getSacado()->getEndereco()
            ),
            'L',
            1,
            'L'
        );
        $PDF->Cell(
            190,
            5,
            utf8_decode(
                $boleto->getSacado()->getCidade() . " - " . $boleto->getSacado()->getUf(
                ) . " - CEP: " . $boleto->getSacado()->getCep()
            ),
            'BL',
            1,
            'L'
        );

        $PDF->SetFont('Arial', '', 6);
        $PDF->Cell(170, 3, utf8_decode('Instruções'), '', 0, 'L');
        $PDF->Cell(20, 3, utf8_decode('Autênticação Mecânica'), '', 1, 'R');

        $PDF->SetFont('Arial', '', 7);

        foreach ($boleto->getInstrucoes() as $instrucao) {
            $PDF->Cell(190, 5, utf8_decode($instrucao), '', 1, 'L');
        }

        $PDF->Ln();
        $PDF->SetFont('Arial', 'B', 6);
        $PDF->Cell(190, 2, 'Corte na linha pontilhada', '', 1, 'R');
        $PDF->SetFont('Arial', '', 12);
        $PDF->Cell(
            190,
            2,
            '--------------------------------------------------------------------------------------------------------------------------------------',
            '',
            0,
            'L'
        );

        $PDF->Ln(10);
        $PDF->Ln(10);

        $PDF->Cell(50, 10, '', 'B', 0, 'L');
        $PDF->Image(self::getDirImages() . $boleto->getLogoBanco(), 10, 130, 40, 10);
        $PDF->SetFont('Arial', 'B', 14);
        $PDF->Cell(20, 10, $boleto->getCodigoBancoComDv(), 'LBR', 0, 'C');

        $PDF->SetFont('Arial', 'B', 9);
        $PDF->Cell(120, 10, $boleto->getLinhaDigitavel(), 'B', 1, 'R');

        $PDF->SetFont('Arial', '', 6);
        $PDF->Cell(130, 3, 'Local Pagamento', 'LR', 0, 'L');
        $PDF->Cell(60, 3, 'Vencimento', '', 1, 'L');

        $PDF->SetFont('Arial', '', 7);
        $PDF->Cell(130, 5, utf8_decode($boleto->getLocalPagamento()), 'BLR', 0, 'L');
        $PDF->Cell(60, 5, $boleto->getDataVencimento()->format('d/m/Y'), 'B', 1, 'R');

        $PDF->SetFont('Arial', '', 6);
        $PDF->Cell(130, 3, 'Cedente', 'LR', 0, 'L');
        $PDF->Cell(60, 3, utf8_decode('Agência/Código cedente'), '', 1, 'L');

        $PDF->SetFont('Arial', '', 7);
        $PDF->Cell(130, 5, utf8_decode($boleto->getCedente()->getNome()), 'BLR', 0, 'L');
        $PDF->Cell(
            60,
            5,
            $boleto->getAgencia() . "-" . $boleto->getAgenciaDv() . " / " . $boleto->getConta() . "-" . $boleto->getContaDv(),
            'B',
            1,
            'R'
        );

        $PDF->SetFont('Arial', '', 6);
        $PDF->Cell(28, 3, 'Data Documento', 'LR', 0, 'L');
        $PDF->Cell(40, 3, utf8_decode('Número do Documento'), 'R', 0, 'L');
        $PDF->Cell(20, 3, utf8_decode('Espécie doc.'), 'R', 0, 'L');
        $PDF->Cell(20, 3, 'Aceite', 'R', 0, 'L');
        $PDF->Cell(22, 3, 'Data processamento', '', 0, 'L');
        $PDF->Cell(60, 3, utf8_decode('Carteira / Nosso número'), 'L', 1, 'L');

        $PDF->SetFont('Arial', '', 7);
        $PDF->Cell(28, 5, $boleto->getDataDocumento()->format('d/m/Y'), 'BLR', 0, 'L');
        $PDF->Cell(40, 5, $boleto->getNumeroDocumento(), 'BR', 0, 'L');
        $PDF->Cell(20, 5, $boleto->getEspecieDoc(), 'BR', 0, 'L');
        $PDF->Cell(20, 5, "", 'BR', 0, 'L');
        $PDF->Cell(22, 5, $boleto->getDataProcessamento()->format('d/m/Y'), 'BR', 0, 'L');
        $PDF->Cell(60, 5, $boleto->getCarteira() . " / " . $boleto->getNossoNumero(), 'B', 1, 'R');

        $PDF->SetFont('Arial', '', 6);
        $PDF->Cell(28, 3, 'Uso do Banco', 'LR', 0, 'L');
        $PDF->Cell(25, 3, 'Carteira', 'R', 0, 'L');
        $PDF->Cell(15, 3, utf8_decode('Espécie'), 'R', 0, 'L');
        $PDF->Cell(40, 3, 'Quantidade', 'R', 0, 'L');
        $PDF->Cell(22, 3, '(x)Valor', '', 0, 'L');
        $PDF->Cell(60, 3, '(=)Valor Documento', 'L', 1, 'L');

        $PDF->SetFont('Arial', '', 7);
        $PDF->Cell(28, 5, '05/05/2014', 'BLR', 0, 'L');
        $PDF->Cell(25, 5, $boleto->getCarteiraNome(), 'BR', 0, 'L');
        $PDF->Cell(15, 5, $boleto->getEspecieDoc(), 'BR', 0, 'L');
        $PDF->Cell(40, 5, "001", 'BR', 0, 'L');
        $PDF->Cell(22, 5, '', 'BR', 0, 'L');
        $PDF->Cell(60, 5, $boleto->getValor(), 'B', 1, 'R');

        $PDF->SetFont('Arial', '', 6);
        $PDF->Cell(130, 3, utf8_decode('Instruções'), 'L', 0, 'L');
        $PDF->Cell(60, 3, '(-)Desconto/Abatimentos', 'L', 1, 'L');

        $l = 0;
        for ($i = 0; $i < 4; $i++) {
            $instrucao = isset($boleto->getInstrucoes()[$i]) ? $boleto->getInstrucoes()[$i] : null;

            $l++;
            $PDF->Cell(130, 5, utf8_decode($instrucao), 'L', 0, 'L');

            if (1 == $l) {
                $PDF->Cell(60, 5, '', 'LB', 1, 'R');
            } else if (2 == $l) {
                $PDF->SetFont('Arial', '', 6);
                $PDF->Cell(60, 3, utf8_decode('(-)Outras deduções'), 'L', 1, 'L');
            } else if (3 == $l) {
                $PDF->Cell(60, 5, '', 'LB', 1, 'R');
            } else {
                if (4 == $l) {
                    $PDF->SetFont('Arial', '', 6);
                    $PDF->Cell(60, 3, '(+)Mora/Multa', 'L', 1, 'L');
                }
            }
        }

        $PDF->SetFont('Arial', '', 7);
        $PDF->Cell(130, 5, '', 'L', 0, 'L');
        $PDF->Cell(60, 5, '', 'LB', 1, 'R');

        $PDF->Cell(130, 3, '', 'L', 0, 'L');
        $PDF->SetFont('Arial', '', 6);
        $PDF->Cell(60, 3, utf8_decode('(+)Outros acréscimos'), 'L', 1, 'L');

        $PDF->SetFont('Arial', '', 7);
        $PDF->Cell(130, 5, '', 'L', 0, 'L');
        $PDF->Cell(60, 5, '', 'LB', 1, 'R');

        $PDF->Cell(130, 3, '', 'L', 0, 'L');
        $PDF->SetFont('Arial', '', 6);
        $PDF->Cell(60, 3, '(=)Valor cobrado', 'L', 1, 'L');
        $PDF->SetFont('Arial', '', 7);
        $PDF->Cell(130, 5, '', 'LB', 0, 'L');
        $PDF->Cell(60, 5, '', 'LB', 1, 'R');

        $PDF->SetFont('Arial', '', 6);
        $PDF->Cell(190, 3, 'Sacado', 'L', 1, 'L');

        $PDF->SetFont('Arial', '', 7);
        $PDF->Cell(190, 5, utf8_decode($boleto->getSacado()->getNome()), 'L', 1, 'L');
        $PDF->Cell(
            190,
            5,
            utf8_decode(
                $boleto->getSacado()->getEndereco()
            ),
            'L',
            1,
            'L'
        );

        $PDF->Cell(
            190,
            5,
            utf8_decode(
                $boleto->getSacado()->getCidade() . " - " . $boleto->getSacado()->getUf(
                ) . " - CEP: " . $boleto->getSacado()->getCep()
            ),
            'BL',
            1,
            'L'
        );

        $PDF->SetFont('Arial', '', 6);
        $PDF->Cell(170, 3, 'Sacador/Avalista', '', 0, 'L');
        $PDF->Cell(20, 3, utf8_decode('Autênticação Mecânica - Ficha de Compensação'), '', 1, 'R');

        $this->fbarcode($boleto->getNumeroFebraban(), $PDF);

        $PDF->Ln(10);
        $PDF->SetY(260);
        $PDF->SetFont('Arial', 'B', 6);
        $PDF->Cell(190, 2, 'Corte na linha pontilhada', '', 1, 'R');
        $PDF->SetFont('Arial', '', 12);
        $PDF->Cell(
            190,
            2,
            '--------------------------------------------------------------------------------------------------------------------------------------',
            '',
            0,
            'L'
        );

        return $PDF;
    }

    public function fbarcode($valor, FPDF $PDF)
    {
        $fino = UnidadeMedida::px2milimetros(1); // valores em px
        $largo = UnidadeMedida::px2milimetros(2.3); // valor em px
        $altura = UnidadeMedida::px2milimetros(40); // valor em px

        $barcodes[0] = "00110";
        $barcodes[1] = "10001";
        $barcodes[2] = "01001";
        $barcodes[3] = "11000";
        $barcodes[4] = "00101";
        $barcodes[5] = "10100";
        $barcodes[6] = "01100";
        $barcodes[7] = "00011";
        $barcodes[8] = "10010";
        $barcodes[9] = "01010";
        for ($f1 = 9; $f1 >= 0; $f1--) {
            for ($f2 = 9; $f2 >= 0; $f2--) {
                $f = ($f1 * 10) + $f2;
                $texto = "";
                for ($i = 1; $i < 6; $i++) {
                    $texto .= substr($barcodes[$f1], ($i - 1), 1) . substr($barcodes[$f2], ($i - 1), 1);
                }
                $barcodes[$f] = $texto;
            }
        }

        // Guarda inicial
        $PDF->Image(self::getDirImages() . '/p.png', $PDF->GetX(), $PDF->GetY(), $fino, $altura);
        $PDF->SetX($PDF->GetX() + $fino);
        $PDF->Image(self::getDirImages() . '/b.png', $PDF->GetX(), $PDF->GetY(), $fino, $altura);
        $PDF->SetX($PDF->GetX() + $fino);
        $PDF->Image(self::getDirImages() . '/p.png', $PDF->GetX(), $PDF->GetY(), $fino, $altura);
        $PDF->SetX($PDF->GetX() + $fino);
        $PDF->Image(self::getDirImages() . '/b.png', $PDF->GetX(), $PDF->GetY(), $fino, $altura);
        $PDF->SetX($PDF->GetX() + $fino);

        $texto = $valor;
        if ((strlen($texto) % 2) <> 0) {
            $texto = "0" . $texto;
        }

        // Draw dos dados
        while (strlen($texto) > 0) {
            $i = round(Substr::esquerda($texto, 2));
            $texto = Substr::direita($texto, strlen($texto) - 2);
            $f = $barcodes[$i];
            for ($i = 1; $i < 11; $i += 2) {
                if (substr($f, ($i - 1), 1) == "0") {
                    $f1 = $fino;
                } else {
                    $f1 = $largo;
                }

                $PDF->Image(self::getDirImages() . '/p.png', $PDF->GetX(), $PDF->GetY(), $f1, $altura);
                $PDF->SetX($PDF->GetX() + $f1);

                if (substr($f, $i, 1) == "0") {
                    $f2 = $fino;
                } else {
                    $f2 = $largo;
                }

                $PDF->Image(self::getDirImages() . '/b.png', $PDF->GetX(), $PDF->GetY(), $f2, $altura);
                $PDF->SetX($PDF->GetX() + $f2);
            }
        }

        // Draw guarda final
        $PDF->Image(self::getDirImages() . '/p.png', $PDF->GetX(), $PDF->GetY(), $largo, $altura);
        $PDF->SetX($PDF->GetX() + $largo);
        $PDF->Image(self::getDirImages() . '/b.png', $PDF->GetX(), $PDF->GetY(), $fino, $altura);
        $PDF->SetX($PDF->GetX() + $fino);
        $PDF->Image(self::getDirImages() . '/p.png', $PDF->GetX(), $PDF->GetY(), $fino, $altura);
        $PDF->SetX($PDF->GetX() + $fino);
        $PDF->Image(
            self::getDirImages() . '/b.png',
            $PDF->GetX(),
            $PDF->GetY(),
            UnidadeMedida::px2milimetros(1),
            $altura
        );
        $PDF->SetX($PDF->GetX() + UnidadeMedida::px2milimetros(1));

    } //Fim da função
}