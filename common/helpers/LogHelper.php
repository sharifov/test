<?php

/**
 * @author AlexConnor
 * @cdata 2021-10-15
 */

namespace common\helpers;

use Yii;

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
                'git.branch' => self::getGitBranch(),
                'endpoint'   => 'frontend',
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
                'git.branch' => self::getGitBranch(),
                'endpoint'   => 'console',
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
                'git.branch' => self::getGitBranch(),
                'endpoint'   => 'webapi',
                'ip' => self::getIp(),
                'userId' => self::getUserId(),
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
}