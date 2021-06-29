<?php

namespace sales\model\clientChat;

use yii\helpers\Html;

class ClientChatPlatform
{
    private const WEB = 1;
    private const ANDROID = 2;
    private const IOS = 3;
    private const WHATSAPP = 4;
    private const MESSENGER = 5;

    private const LIST_NAME = [
        self::WEB => 'Web',
        self::ANDROID => 'Android',
        self::IOS => 'Ios',
        self::WHATSAPP => 'Whatsapp',
        self::MESSENGER => 'Messenger'
    ];

    private const LIST_ICON = [
        self::WEB => 'fab fa-internet-explorer',
        self::ANDROID => 'fab fa-android',
        self::IOS => 'fab fa-apple',
        self::WHATSAPP => 'fab fa-whatsapp',
        self::MESSENGER => 'fab fa-facebook-messenger'
    ];

    public static function getListName(): array
    {
        return self::LIST_NAME;
    }

    public static function getDefaultPlatform(): int
    {
        return self::WEB;
    }

    public static function getPlatformIdByName(string $name): int
    {
        return array_search(ucfirst(mb_strtolower($name)), self::getListName()) ?: self::getDefaultPlatform();
    }

    public static function getIconById(int $id): string
    {
        return self::LIST_ICON[$id] ?? '';
    }

    public static function getNameById(int $id): string
    {
        return self::getListName()[$id] ?? 'Unknown platform';
    }

    public static function getNameWithIcon(int $id): string
    {
        $iconClass = self::getIconById($id);
        $i = $iconClass ? Html::tag('i', null, ['class' => self::getIconById($id)]) : '';
        return $i . ' ' . self::getNameById($id);
    }

    public static function getIconWithTitle(int $id): string
    {
        $iconClass = self::getIconById($id);
        $title = 'Platform: ' . self::getNameById($id);
        return $iconClass ? Html::tag('i', null, ['class' => self::getIconById($id), 'title' => $title, 'data-toggle' => 'tooltip']) : Html::tag('i', null, ['class' => 'fa fa-comment-o', 'title' => $title, 'data-toggle' => 'tooltip']);
    }
}
