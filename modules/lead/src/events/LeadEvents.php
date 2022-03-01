<?php

namespace modules\lead\src\events;

use modules\eventManager\src\EventInterface;
use modules\lead\src\events\handler\LeadHandler;
use yii\base\Component;

class LeadEvents extends Component implements EventInterface
{
    private const NAME = 'lead';

    public const EVENT_CLOSE = self::NAME . '/close';

//    private const ACTION_CLOSE = 'action-close';

    private const HANDLER_LEAD = LeadHandler::class;



    private const EVENT_LIST = [
        self::EVENT_CLOSE => 'Close'
    ];

    private const HANDLER_LIST = [
        self::EVENT_CLOSE => [
            self::HANDLER_LEAD => [
                'close'
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
