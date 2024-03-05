<?php

namespace OpenBoleto;

use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\{QRCode, QROptions};

class QrCodePix
{
    /**
     * Generate a QR Code for Pix
     *
     * @param string|null $qrCode
     * @return string
     */
    public static function image($qrCode = null)
    {
        if (empty($qrCode)) {
            return '';
        }

        return(new QRCode())->render($qrCode);
    }
}
