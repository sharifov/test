<?php

namespace src\model\coupon\useCase\apiCreate;

use common\components\validators\CheckJsonValidator;
use modules\product\src\entities\productType\ProductType;
use src\helpers\ErrorsToStringHelper;
use src\model\coupon\entity\couponProduct\service\CouponProductBaseService;
use yii\base\Model;

/**
 * Class CouponCreateForm
 * @property $currencyCode
 * @property $amount
 * @property $percent
 * @property $startDate
 * @property $expirationDate
 * @property $reusableCount
 * @property $public
 * @property $reusable
 * @property $product
 *
 * @property array $couponProductServices
 */
class CouponCreateForm extends Model
{
    public $currencyCode;
    public $amount;
    public $percent;
    public $startDate;
    public $expirationDate;
    public $reusableCount;
    public $public;
    public $reusable;
    public $product;

    private array $couponProductServices = [];

    public function rules(): array
    {
        return [
            ['currencyCode', 'required'],
            ['currencyCode', 'in', 'range' => array_keys(\common\models\Currency::getList())],

            ['amount', 'required'],
            ['amount', 'integer'],
            ['amount', 'filter', 'skipOnEmpty' => true, 'skipOnError' => true, 'filter' => 'intval'],

            ['percent', 'integer'],
            ['percent', 'filter', 'skipOnEmpty' => true, 'skipOnError' => true, 'filter' => 'intval'],

            [['startDate', 'expirationDate'], 'date', 'format' => 'php:Y-m-d'],

            [['startDate'], 'filter', 'filter' => static function ($value) {
                return date('Y-m-d 00:00:00', strtotime($value));
            }, 'skipOnEmpty' => true, 'skipOnError' => true],

            [['expirationDate'], 'filter', 'filter' => static function ($value) {
                return date('Y-m-d 23:59:59', strtotime($value));
            }, 'skipOnEmpty' => true, 'skipOnError' => true],

            [['expirationDate'], 'checkDate'],

            ['reusableCount', 'integer'],
            ['reusableCount', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            [['reusable', 'public'], 'boolean', 'strict' => true, 'trueValue' => true, 'falseValue' => false, 'skipOnEmpty' => true],

            ['product', CheckJsonValidator::class, 'skipOnEmpty' => true, 'skipOnError' => true],
            ['product', 'checkProduct', 'skipOnEmpty' => true],
        ];
    }

    public function checkDate($attribute): void
    {
        if ($this->startDate && $this->expirationDate && ($this->startDate > $this->expirationDate)) {
            $this->addError($attribute, 'expirationDate must be older startDate');
        }
    }

    public function checkProduct($attribute): void
    {
        foreach ($this->product as $typeKey => $productFields) {
            if (!is_array($productFields) || empty($productFields)) {
                $this->addError($attribute, 'Product error. Message: productFields must by array and not empty');
                break;
            }
            if (!$productType = ProductType::find()->byKey($typeKey)->enabled()->one()) {
                $this->addError($attribute, 'ProductType (' . $typeKey . ') not found');
                break;
            }

            $couponProductBaseService = new CouponProductBaseService($typeKey);
            $couponProductService = $couponProductBaseService->initClass();
            $couponProductService->load($productFields);
            if (!$couponProductService->validate()) {
                $this->addError($attribute, 'Product (' . $typeKey . ') error. Message: ' .
                    ErrorsToStringHelper::extractFromModel($couponProductService));
                break;
            }
            $this->couponProductServices[$typeKey] = $couponProductService->getAttributes();
        }
    }

    public function formName(): string
    {
        return '';
    }

    public function getAmountCurrencyCode(): ?string
    {
        if ($this->amount && $this->currencyCode) {
            return $this->currencyCode . $this->amount;
        }
        return null;
    }

    public function getCouponProductServices(): array
    {
        return $this->couponProductServices;
    }
}
