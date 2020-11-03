<?php


namespace Source\OpenBoleto\Files;

use Dompdf\Dompdf;

/**
 * Class Pdf
 * @package Source\OpenBoleto\Files
 */
class Pdf
{
    /** @var string */
    protected string $filename;

    /** @var string */
    protected string $basePath;

    /** @var string|null */
    protected ?string $message = null;

    /**
     * Pdf constructor.
     * @param string $filename
     * @param string $basePath
     */
    public function __construct(string $filename, string $basePath = __DIR__)
    {
        $this->filename = $filename;
        $this->basePath = $basePath;
    }

    /**
     * @param string $htmlOutput
     * @return string|null
     */
    public function generateFile(string $htmlOutput): ?string
    {
        if (empty($htmlOutput)) {
            $this->message = "Não foi informado nenhum conteúdo html";
            return null;
        }

        $pdf = new Dompdf();
        $pdf->setPaper('A4');
        $pdf->loadHtml($htmlOutput);
        $pdf->render();

        $output = $pdf->output();

        file_put_contents("{$this->basePath}/{$this->filename}", $output);

        return "{$this->basePath}/{$this->filename}";
    }

    /**
     * @return string|null
     */
    public function message(): ?string
    {
        return $this->message;
    }
}
