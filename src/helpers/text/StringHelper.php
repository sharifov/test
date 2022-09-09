<?php

namespace src\helpers\text;

use yii\helpers\ArrayHelper;

class StringHelper extends \yii\helpers\StringHelper
{
    /**
     * @param $text
     * @return mixed
     */
    public static function stripHtmlTags($text)
    {
        $text = preg_replace(
            [
                // Remove invisible content
                '@<head[^>]*?>.*?</head>@siu',
                '@<style[^>]*?>.*?</style>@siu',
                '@<script[^>]*?.*?</script>@siu',
                '@<object[^>]*?.*?</object>@siu',
                '@<embed[^>]*?.*?</embed>@siu',
                '@<applet[^>]*?.*?</applet>@siu',
                '@<noframes[^>]*?.*?</noframes>@siu',
                '@<noscript[^>]*?.*?</noscript>@siu',
                '@<noembed[^>]*?.*?</noembed>@siu',
                // Add line breaks before and after blocks
                '@</?((address)|(blockquote)|(center)|(del))@iu',
                '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
                '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
                '@</?((table)|(th)|(td)|(caption))@iu',
                '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
                '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
                '@</?((frameset)|(frame)|(iframe))@iu',
            ],
            [
                ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
                "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
                "\n\$0", "\n\$0",
            ],
            $text
        );

        $text = strip_tags($text);
        $text = preg_replace('!\s+!', ' ', $text);

        return $text;
    }

    /**
     * @param string $text Use {{data}} for access to object properties
     * @param $object
     * @return string
     * @throws \Exception
     */
    public static function parseStringWithObjectTemplate(string $text, $object): string
    {
        preg_match_all('/{{(.*?)}}/m', $text, $keys);

        if (!isset($keys[1])) {
            return $text;
        }

        $placeholders = [];
        foreach ($keys[1] as $index => $key) {
            if (empty($key)) {
                continue;
            }

            $placeholders[$keys[0][$index]] = ArrayHelper::getValue($object, $key, '');
        }

        return strtr($text, $placeholders);
    }
}
