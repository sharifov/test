<?php

namespace sales\model\userAuthClient\entity;

use sales\model\userAuthClient\handler\ClientHandler;
use sales\model\userAuthClient\handler\GoogleHandler;

class UserAuthClientSources
{
    public const GOOGLE = 1;

    private const LIST = [
        self::GOOGLE => 'google'
    ];

    private const FACTORY = [
        self::GOOGLE => GoogleHandler::class
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
