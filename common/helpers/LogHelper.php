<?php

/**
 * @author AlexConnor
 * @cdata 2021-10-15
 */

namespace common\helpers;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class LogHelper
{
    /**
     * @return array
     */
    public static function getFrontendPrefixData(): array
    {
        try {
            return [
                'hostname' => self::getHostname(),
                'version' => self::getReleaseVersion(),
                'gitBranch' => self::getShortGitBranch(),
                'type'   => 'frontend',
                'ip' => self::getIp(),
                'userId' => self::getUserId(),
                //'sessionId' => self::getSessionId()
            ];
        } catch (\Throwable $throwable) {
            return [];
        }
    }

    /**
     * @return array
     */
    public static function getConsolePrefixData(): array
    {
        try {
            return [
                'hostname' => self::getHostname(),
                'version' => self::getReleaseVersion(),
                'gitBranch' => self::getShortGitBranch(),
                'type'   => 'console',
            ];
        } catch (\Throwable $throwable) {
            return [];
        }
    }

    /**
     * @return array
     */
    public static function getWebapiPrefixData(): array
    {
        try {
            return [
                'hostname' => self::getHostname(),
                'version' => self::getReleaseVersion(),
                'gitBranch' => self::getShortGitBranch(),
                'type'   => 'webapi',
                'ip' => self::getIp(),
                'userId' => self::getUserId(),
            ];
        } catch (\Throwable $throwable) {
            return [];
        }
    }

    /**
     * @return array
     */
    public static function getAnalyticPrefixData(): array
    {
        try {
            return [
//                'hostname' => self::getHostname(),
//                'version' => self::getReleaseVersion(),
//                'gitBranch' => self::getShortGitBranch(),
//                'type'   => 'console',
                'srv_name' => 'analytics',
//                'srv_type' => ''
            ];
        } catch (\Throwable $throwable) {
            return [];
        }
    }


    /**
     * @return string
     */
    public static function getFrontendPrefixDB(): string
    {
        $data = self::getFrontendPrefixData();

        $dataList = [];
        foreach ($data as $v) {
            $dataList[] = '[' . $v . ']';
        }

        return implode('', $dataList);
    }

    /**
     * @return string
     */
    public static function getConsolePrefixDB(): string
    {
        $data = self::getConsolePrefixData();

        $dataList = [];
        foreach ($data as $v) {
            $dataList[] = '[' . $v . ']';
        }

        return implode('', $dataList);
    }

    /**
     * @return string
     */
    public static function getWebapiPrefixDB(): string
    {
        $data = self::getWebapiPrefixData();

        $dataList = [];
        foreach ($data as $v) {
            $dataList[] = '[' . $v . ']';
        }

        return implode('', $dataList);
    }



    /**
     * @return mixed|string
     */
    public static function getReleaseVersion()
    {
        return Yii::$app->params['release']['version'] ?? '';
    }


    /**
     * @return mixed|string
     */
    public static function getGitBranch()
    {
        return Yii::$app->params['release']['git_branch'] ?? '';
    }

    /**
     * @return array|mixed|string|string[]
     */
    public static function getShortGitBranch()
    {
        return str_replace('refs/heads/', '', self::getGitBranch());
    }

    /**
     * @return mixed|string
     */
    public static function getUserId()
    {
        return Yii::$app->user->isGuest ? '-' : Yii::$app->user->id;
    }

    /**
     * @return mixed
     */
    public static function getIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * @return string
     */
    public static function getHostname(): string
    {
        return php_uname('n');
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public static function getSessionId(): string
    {
        $session = Yii::$app->has('session', true) ? Yii::$app->get('session') : null;
        return $session && $session->getIsActive() ? $session->getId() : '';
    }

    public static function hidePersonalData(
        array $data,
        array $personalDataKeys,
        int $showLen = 2,
        int $maxLen = 3,
        string $substitute = '*'
    ): array {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::hidePersonalData($value, $personalDataKeys, $showLen, $maxLen, $substitute);
            }
            if (array_key_exists($key, $personalDataKeys) || in_array($key, $personalDataKeys, true)) {
                $data[$key] = self::replaceSource($value, $showLen, $maxLen, $substitute);
            }
        }
        return $data;
    }

    /**
     * @param $source
     * @param int $showLen
     * @param int $maxLen
     * @param string $substitute
     * @return mixed|string
     */
    public static function replaceSource($source, int $showLen = 1, int $maxLen = 3, string $substitute = '*')
    {
        if (is_numeric($source)) {
            $source = (string) $source;
        }
        if (!is_string($source)) {
            return $source;
        }

        $length = strlen($source);
        if ($length <= $maxLen || $length <= $showLen * 2) {
            return str_repeat($substitute, $length);
        }

        $middle = str_repeat($substitute, $length - ($showLen * 2));
        $first = substr($source, 0, $showLen);
        $last = substr($source, -$showLen);

        return $first . $middle . $last;
    }

    public static function prepareErrorDataForLogFromModel(Model $model): array
    {
        $log = [
            'errors' => $model->getErrors(),
        ];

        foreach ($model->getFirstErrors() as $attribute => $error) {
            $log['data'][$attribute] = $model->{$attribute};
        }

        return $log;
    }
}
