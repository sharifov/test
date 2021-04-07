<?php

namespace modules\order\src\entities\orderContact;

class OrderContactRepository
{
    public function save(OrderContact $orderContact): int
    {
        if ($orderContact->save()) {
            return $orderContact->oc_id;
        }
        throw new \RuntimeException($orderContact->getErrorSummary(true)[0]);
    }
}
