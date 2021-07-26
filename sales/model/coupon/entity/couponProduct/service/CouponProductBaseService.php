<?php

namespace sales\model\coupon\entity\couponProduct\service;

use modules\flight\coupon\CouponFlight;
use yii\helpers\ArrayHelper;

/**
 * Class CouponProductBaseService
 *
 * @property array $classMap
 * @property string $productType
 */
class CouponProductBaseService
{
    public ?string $productType;

    private array $classMap = [
        'flight' => CouponFlight::class,
    ];

    /**
     * @param string $productType
     */
    public function __construct(string $productType)
    {
        $this->productType = $productType;
    }

    public function initClass(): AbstractCouponProduct
    {
        $nameClass = $this->getClassNameByTable($this->productType);
        if (class_exists($nameClass)) {
            return new $nameClass();
        }
        throw new \DomainException('Class processing not found by product: ' . $this->productType);
    }

    private function getClassNameByTable(string $productType): string
    {
        if (ArrayHelper::keyExists($productType, $this->classMap)) {
            return $this->classMap[$productType];
        }
        throw new \DomainException('Class processing not mapped by product: ' . $this->productType);
    }
}
