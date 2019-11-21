<?php
/**
 * Created
 * User: alex.connor@techork.com
 * Date: 15/11/2019
 * Time: 17:05
 */
namespace sales\helpers\app;


class AppHelper
{

    /**
     * @param \Throwable $throwable
     * @return string
     */
    public static function throwableFormatter(\Throwable $throwable): string
    {
        $str = 'Message: ' . $throwable->getMessage() . ' (code: '.$throwable->getCode().'), File: ' . $throwable->getFile() . ': line ' . $throwable->getLine();
        return $str;
    }


}