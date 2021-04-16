<?php

namespace modules\order\src\events;

use modules\order\src\entities\orderContact\OrderContact;
use yii\base\Component;

/**
 * Class OrderContactCreateEvent
 * @package modules\order\src\events
 *
 * @property OrderContact $orderContact
 */
class OrderContactCreateEvent extends Component
{
    private OrderContact $orderContact;

    public function __construct(OrderContact $orderContact, $config = [])
    {
        $this->orderContact = $orderContact;
        parent::__construct($config);
    }
}
