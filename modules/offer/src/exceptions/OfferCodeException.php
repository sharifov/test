<?php

namespace modules\offer\src\exceptions;

use common\CodeExceptionsModule as Module;

class OfferCodeException
{
    public const OFFER_NOT_FOUND = Module::OFFER . 100;
    public const OFFER_SAVE = Module::OFFER . 101;
    public const OFFER_REMOVE = Module::OFFER . 102;

    public const OFFER_STATUS_LOG_SAVE = Module::OFFER . 110;
    public const OFFER_STATUS_LOG_REMOVE = Module::OFFER . 111;

    public const OFFER_SEND_LOG_SAVE = Module::OFFER . 112;
    public const OFFER_SEND_LOG_REMOVE = Module::OFFER . 113;

    public const OFFER_VIEW_LOG_SAVE = Module::OFFER . 114;
    public const OFFER_VIEW_LOG_REMOVE = Module::OFFER . 115;

    public const OFFER_PRODUCT_NOT_FOUND = Module::OFFER . 200;
    public const OFFER_PRODUCT_SAVE = Module::OFFER . 201;
    public const OFFER_PRODUCT_REMOVE = Module::OFFER . 202;

    public const API_OFFER_VIEW_NOT_FOUND_DATA_ON_REQUEST = Module::OFFER . 300;
    public const API_OFFER_VIEW_VALIDATE = Module::OFFER . 301;
    public const API_OFFER_VIEW_NOT_FOUND = Module::OFFER . 302;

    public const API_OFFER_CA_NOT_FOUND_DATA_ON_REQUEST = Module::OFFER . 400;
    public const API_OFFER_CA_VALIDATE = Module::OFFER . 401;
    public const API_OFFER_CA_NOT_FOUND = Module::OFFER . 402;
    public const API_OFFER_CA_NOT_ALTERNATIVE = Module::OFFER . 403;
    public const API_OFFER_CA_QUOTE_NOT_PROCESSED = Module::OFFER . 404;
}
