<?php


namespace OpenBoleto;

use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;

class QrCodePix
{
    public static function image($qrCode = null)
    {
        if (empty($qrCode)) {
            return '';
        }
        $obQrCode = new QrCode($qrCode);
        return (new Output\Svg)->output($obQrCode, 150);
    }
}
