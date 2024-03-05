<?php

namespace OpenBoleto;

use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\{QRCode, QROptions};

class QrCodePix
{
    public static function image($qrCode = null): string
    {
        if (empty($qrCode)) {
            return '';
        }

        return(new QRCode())->render($qrCode);
    }
}
