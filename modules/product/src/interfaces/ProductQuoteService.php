<?php

namespace modules\product\src\interfaces;

use modules\order\src\forms\api\createC2b\QuotesForm;

interface ProductQuoteService
{
    public function c2bHandle(Productable $product, QuotesForm $form): void;
}
