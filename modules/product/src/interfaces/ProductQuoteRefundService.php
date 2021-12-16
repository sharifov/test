<?php

namespace modules\product\src\interfaces;

interface ProductQuoteRefundService
{
    public function getRefundStructureObject(int $id): ProductQuoteObjectRefundStructure;
}
