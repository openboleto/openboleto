<?php

namespace OpenBoleto;

use chillerlan\QRCode\{QRCode};

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
