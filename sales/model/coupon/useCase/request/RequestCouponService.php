<?php

namespace sales\model\coupon\useCase\request;

use common\components\SearchService;

class RequestCouponService
{
    public function request(RequestForm $form): array
    {
        if (!$coupons = SearchService::getCoupons($form->getParams())) {
            throw new \DomainException('Request error.');
        }
        return [];
    }
}
