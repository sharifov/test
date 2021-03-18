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
     * @param bool $cutStyle
     * @return mixed
     * @throws \Mpdf\MpdfException
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \yii\base\InvalidConfigException
     */
    public static function generateForBrowserOutput(string $content, string $fileName = 'filename.pdf', bool $cutStyle = true)
    {
        $pdf = new Pdf(['mode' => Pdf::MODE_CORE,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $cutStyle ? self::cutStyle($content) : $content,
            'filename' => $fileName,
            'cssInline' => self::getSectionFromContent($content, 'style', true),
        ]);
        return $pdf->render();
    }

    /**
     * @param string $content
     * @param string $fileName
     * @param bool $cutStyle
     * @return string
     * @throws \Mpdf\MpdfException
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public static function generateAsFile(string $content, string $fileName, bool $cutStyle = true): string
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
            'content' => $cutStyle ? self::cutStyle($content) : $content,
            'filename' => $patchToFile,
            'cssInline' => self::getSectionFromContent($content, 'style', true),
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
            'cssInline' => self::getSectionFromContent($content, 'style', true),
        ]);
        return $pdf->render();
    }

    /**
     * @param $content
     * @param $target
     * @param bool $removeTarget
     * @return string
     */
    public static function getSectionFromContent($content, string $target, $removeTarget = false): string
    {
        $closeTag = '</' . $target . '>';
        $startTag = strpos($content, '<' . $target, 0);
        $endTag = strpos($content, $closeTag, 0);

        $content = substr($content, $startTag, ($endTag + strlen($closeTag) - $startTag));

        if ($removeTarget) {
            $content = str_replace([$closeTag, '<' . $target . '>'], '', $content);
        }
        return $content;
    }

    /**
     * @param string $content
     * @return string
     */
    public static function cutStyle(string $content): ?string
    {
        $style = self::getSectionFromContent($content, 'style');
        return str_replace($style, '', $content);
    }
}
