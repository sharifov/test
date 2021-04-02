<?php

namespace modules\attraction\src\services\attractionQuote;

use modules\order\src\forms\api\createC2b\QuotesForm;
use modules\product\src\interfaces\Productable;
use modules\product\src\interfaces\ProductQuoteService;

class CreateQuoteService implements ProductQuoteService
{
    public function c2bHandle(Productable $product, QuotesForm $form): void
    {
    }
}
