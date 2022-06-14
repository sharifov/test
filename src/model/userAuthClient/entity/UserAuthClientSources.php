<?php

namespace src\model\userAuthClient\entity;

use src\model\userAuthClient\handler\ClientHandler;
use src\model\userAuthClient\handler\GoogleHandler;
use src\model\userAuthClient\handler\MicrosoftHandler;

class UserAuthClientSources
{
    public const GOOGLE = 1;
    public const MICROSOFT = 2;

    private const LIST = [
        self::GOOGLE => 'google',
        self::MICROSOFT => 'microsoft',
    ];

    private const FACTORY = [
        self::GOOGLE => GoogleHandler::class,
        self::MICROSOFT => MicrosoftHandler::class
    ];

    public static function getList(): array
    {
        return self::LIST;
    }

    public static function getName(int $source): string
    {
        return self::getList()[$source] ?? '--';
    }

    public static function getIdByValue(string $source): ?int
    {
        return array_search($source, self::getList());
    }

    public static function clientSourceFactory(string $source): ClientHandler
    {
        if (($clientId = self::getIdByValue($source)) && $handlerNamespace = (self::FACTORY[$clientId] ?? null)) {
            return \Yii::createObject($handlerNamespace);
        }
        throw new \RuntimeException('Not found handler for source: ' . self::getName($source) . '; by source: ' . $source);
    }
}
