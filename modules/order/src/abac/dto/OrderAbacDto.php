<?php

namespace modules\order\src\abac\dto;

use modules\order\src\entities\order\Order;

/**
 * Class OrderAbacDto
 * @package modules\order\src\abac\dto
 */
class OrderAbacDto extends \stdClass
{
    public ?int $status_id = null;
    public ?float $profit_amount = null;
    public int $n = 0;

    public function __construct(?Order $order)
    {
        if ($order) {
            $this->status_id = (int) $order->or_status_id;
            $this->profit_amount = (float) $order->or_profit_amount;
        }
    }
}
