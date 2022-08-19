<?php

namespace modules\user\src\events;

use modules\eventManager\src\EventInterface;
use modules\user\src\events\handler\UserHandler;
use yii\base\Component;

class UserEvents extends Component implements EventInterface
{
    private const NAME = 'user';

    public const EVENT_LOGIN = self::NAME . '/login';
    public const EVENT_LOGOUT = self::NAME . '/logout';
    public const EVENT_ONLINE = self::NAME . '/online';
    public const EVENT_DEVICE_ACTIVE = self::NAME . '/device_active';
    public const EVENT_ACTIVE = self::NAME . '/active';
    public const EVENT_ON_CALL = self::NAME . '/on_call';


    private const HANDLER_USER = UserHandler::class;

    private const EVENT_LIST = [
        self::EVENT_LOGIN => 'Login',
        self::EVENT_LOGOUT => 'Logout',
        self::EVENT_ONLINE => 'Online',
        self::EVENT_DEVICE_ACTIVE => 'Device Active',
        self::EVENT_ACTIVE => 'Active',
        self::EVENT_ON_CALL => 'On Call',
    ];

    private const HANDLER_LIST = [
        self::EVENT_LOGIN => [
            self::HANDLER_USER => [
                'login'
            ]
        ]
    ];

    /**
     * @return string
     */
    public static function getName(): string
    {
        return self::NAME;
    }

    /**
     * @return array|string[]
     */
    public static function getEventList(): array
    {
        return self::EVENT_LIST ?? [];
    }


    /**
     * @param string|null $eventName
     * @return array
     */
    public static function getHandlerList(?string $eventName = null): array
    {
        $list = [];
        if (self::HANDLER_LIST) {
            foreach (self::HANDLER_LIST as $eventList) {
                if ($eventList) {
                    foreach ($eventList as $handlerClass => $handlerName) {
                        $methods = get_class_methods($handlerClass);
                        if ($methods) {
                            foreach ($methods as $method) {
                                $list[] = $handlerClass . '::' . $method;
                            }
                        }
                    }
                }
            }
        }
        return $list;
    }
}
