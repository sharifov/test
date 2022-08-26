<?php

namespace modules\app\src\events;

use modules\eventManager\src\EventInterface;
use modules\lead\src\events\handler\LeadHandler;
use yii\base\Component;

class AppEvents extends Component implements EventInterface
{
    private const NAME = 'app';

    public const EVENT_LOGIN = self::NAME . '/login';
    public const EVENT_LOGOUT = self::NAME . '/logout';

//    private const ACTION_CLOSE = 'action-close';

    private const HANDLER_APP = LeadHandler::class;



    private const EVENT_LIST = [
        self::EVENT_LOGIN => 'Login',
        self::EVENT_LOGIN => 'Logout',
    ];

    private const HANDLER_LIST = [
        self::EVENT_LOGIN => [
            self::HANDLER_APP => [
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
