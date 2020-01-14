<?php

namespace sales\helpers\email;

use Soundasleep\Html2Text;
use Soundasleep\Html2TextException;

class TextConvertingHelper
{
    /**
     * @param string $text
     * @param array $options
     * @return string
     */
    public static function htmlToText(string $text, array $options = ['ignore_errors' => true, 'drop_links' => false]): string
    {
        try {
            $text = Html2Text::convert($text, $options);
        } catch (Html2TextException $e) {
            $text = strip_tags($text);
        }

        return $text;
    }

    /**
     * @param string $text
     * @param int $level
     * @return false|string
     */
    public static function compress(string $text, int $level = 9)
    {
        return gzcompress($text, $level);
    }

    /**
     * @param $compressedText
     * @return false|string
     */
    public static function unCompress($compressedText)
    {
        return gzuncompress($compressedText);
    }

    /**
     * @param string $text
     * @return string
     * @throws \Soundasleep\Html2TextException
     */
    public static function unCompressAndHtmlToText(string $text)
    {
        return self::htmlToText(self::unCompress($text));
    }
}