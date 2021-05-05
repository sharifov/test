<?php

namespace modules\order\src\entities\orderRequest;

class OrderRequestRepository
{
    public function save(OrderRequest $request): int
    {
        if ($request->save()) {
            return $request->orr_id;
        }
        throw new \RuntimeException('Order Requests save failed');
    }
}
