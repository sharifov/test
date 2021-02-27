<?php

namespace sales\services\pdf;

use kartik\mpdf\Pdf;
use yii\helpers\FileHelper;

/**
 * Class GeneratorPdfService
 */
class GeneratorPdfService
{
    /**
     * @param string $content
     * @param string $fileName
     * @return mixed
     * @throws \Mpdf\MpdfException
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \yii\base\InvalidConfigException
     */
    public static function generateForBrowserOutput(string $content, string $fileName = 'filename.pdf')
    {
        $pdf = new Pdf(['mode' => Pdf::MODE_CORE,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'filename' => $fileName,
        ]);
        return $pdf->render();
    }

    /**
     * @param string $content
     * @param string $fileName
     * @return string
     * @throws \Mpdf\MpdfException
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \yii\base\InvalidConfigException
     */
    public static function generateAsFile(string $content, string $fileName): string
    {
        $patchToDir =  \Yii::getAlias('@frontend/runtime/pdf/');
        $patchToFile = $patchToDir . $fileName;

        if (!file_exists($patchToDir)) {
            FileHelper::createDirectory($patchToDir);
        }
        if (file_exists($patchToFile)) {
            FileHelper::unlink($patchToFile);
        }

        $pdf = new Pdf(['mode' => Pdf::MODE_CORE,
            'destination' => Pdf::DEST_FILE,
            'content' => $content,
            'filename' => $patchToFile,
        ]);
        $pdf->render();

        return $patchToFile;
    }

    /**
     * @param string $content
     * @return string
     * @throws \Mpdf\MpdfException
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \yii\base\InvalidConfigException
     */
    public static function generateAsString(string $content): string
    {
        $pdf = new Pdf(['mode' => Pdf::MODE_CORE,
            'destination' => Pdf::DEST_STRING,
            'content' => $content,
        ]);
        return $pdf->render();
    }
}
