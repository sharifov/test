<?php

namespace sales\services\authentication;

/**
 * Class AntiBruteForceHelper
 */
class AntiBruteForceHelper
{
    /**
     * @return string
     */
    public static function getClientIPAddress(): string
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipAddress = 'UNKNOWN';
        }
        return $ipAddress;
    }

    /**
     * @return string
     */
    public static function getBrowserName(): string
    {
        $exactBrowserName = strtolower($_SERVER['HTTP_USER_AGENT']);

        if (strpos($exactBrowserName, 'safari/') && strpos($exactBrowserName, 'opr/')) {
            $browserName = 'Opera';
        } elseif (strpos($exactBrowserName, 'safari/') && strpos($exactBrowserName, 'chrome/')) {
            $browserName = 'Chrome';
        } elseif (strpos($exactBrowserName, 'msie')) {
            $browserName = 'Internet Explorer';
        } elseif (strpos($exactBrowserName, 'firefox/')) {
            $browserName = 'Firefox';
        } elseif (
            strpos($exactBrowserName, 'safari/') && strpos($exactBrowserName, 'opr/') === false &&
            strpos($exactBrowserName, 'chrome/') === false
        ) {
                $browserName = 'Safari';
        } else {
            $browserName = 'Browser not defined';
        }
        return $browserName;
    }
}
