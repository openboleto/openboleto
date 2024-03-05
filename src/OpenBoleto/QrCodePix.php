<?php

namespace OpenBoleto;

use chillerlan\QRCode\QRCode;


class QrCodePix
{
    public static function image($qrCode = null)
    {
        if (empty($qrCode)) {
            return '';
        }
        return(new QRCode)->render($qrCode);
    }
}