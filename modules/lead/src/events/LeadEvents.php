<?php

namespace modules\lead\src\events;

use modules\eventManager\src\EventInterface;
use modules\lead\src\events\handler\LeadCloseHandler;

class LeadEvents implements EventInterface
{

    public const EVENT_CLOSE = 'close';

    private const ACTION_CLOSE = 'action-close';

    private const HANDLER_LEAD_CLOSE = LeadCloseHandler::class;



    private const EVENT_LIST = [
        self::EVENT_CLOSE => 'Close'
    ];

    private const ACTION_LIST = [
        self::EVENT_CLOSE => [
            self::ACTION_CLOSE => 'Lead Close'
        ]
    ];


    /**
     * @return array|string[]
     */
    public static function getEventList(): array
    {
        return self::EVENT_LIST ?? [];
    }
}
