<?php

namespace modules\offer\src\exceptions;

use common\CodeExceptionsModule as Module;

class OfferCodeException
{
    public const OFFER_NOT_FOUND = Module::OFFER . 100;
    public const OFFER_SAVE = Module::OFFER . 101;
    public const OFFER_REMOVE = Module::OFFER . 102;

    public const OFFER_PRODUCT_NOT_FOUND = Module::OFFER . 200;
    public const OFFER_PRODUCT_SAVE = Module::OFFER . 201;
    public const OFFER_PRODUCT_REMOVE = Module::OFFER . 202;
}
