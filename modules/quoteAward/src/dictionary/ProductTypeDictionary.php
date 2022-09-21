<?php

namespace modules\quoteAward\src\dictionary;

class ProductTypeDictionary
{
    public const PRODUCT_MP = 'mp';
    public const PRODUCT_TF = 'tf';
    public const PRODUCT_ATL = 'atl';
    public const PRODUCT_DFN = 'dfn';
    public const PRODUCT_REGULAR = 'regular';

    public static function getList(): array
    {
        return [
            self::PRODUCT_MP => 'MP',
            self::PRODUCT_TF => 'TF',
            self::PRODUCT_ATL => 'ATL',
            self::PRODUCT_DFN => 'DFN',
            self::PRODUCT_REGULAR => 'Regular',
        ];
    }
}
