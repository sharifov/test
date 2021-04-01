<?php

namespace modules\order\src\forms\api\createC2b;

use common\components\validators\CheckJsonValidator;
use modules\product\src\entities\productType\ProductType;
use sales\forms\CompositeForm;

/**
 * Class QuotesForm
 * @package modules\order\src\forms\api\createC2b
 *
 * @property string $productKey
 * @property string $status
 * @property string $originSearchData
 * @property string $quoteOtaId
 * @property int $orderId
 *
 * @property ProductHolderForm $holder
 * @property ProductType $productType
 * @property FlightPaxDataForm[] $flightPaxData
 * @property HotelPaxDataForm[] $hotelPaxData
 */
class QuotesForm extends CompositeForm
{
    public $productKey;

    public $status;

    public $originSearchData;

    public $quoteOtaId;

    public $orderId;

    public $productType;

    private const STATUS_BOOKED = 'booked';
    private const STATUS_FAILED = 'failed';

    public function rules(): array
    {
        return [
            [['productKey', 'originSearchData', 'quoteOtaId'], 'required'],
            [['status'], 'string'],
            [['status'], 'in', 'range' => [self::STATUS_BOOKED, self::STATUS_FAILED]],
            [['productKey'], 'validateProductType'],
            [['originSearchData'], CheckJsonValidator::class],
        ];
    }

    public function load($data, $formName = null): bool
    {
        $this->holder = new ProductHolderForm();
        $this->createFlightPaxDataForm($data);
        $this->createHotelPaxDataForm($data);
        return parent::load($data, $formName);
    }

    public function formName(): string
    {
        return "quotes";
    }

    /**
     * @inheritDoc
     */
    protected function internalForms(): array
    {
        return ['holder', 'flightPaxData', 'hotelPaxData'];
    }

    public function validateProductType(): bool
    {
        if (!$this->productType = ProductType::findOne(['pt_key' => $this->productKey])) {
            $this->addError('productKey', 'Product type not found by key: ' . $this->productKey);
            return false;
        }
        return true;
    }

    private function createFlightPaxDataForm(array $data): void
    {
        $this->flightPaxData = [];
        if (isset($data['flightPaxData']) && $paxCnt = count((array)$data['flightPaxData'])) {
            $paxData = [];
            for ($i = 0; $i < $paxCnt; $i++) {
                $paxData[] = new FlightPaxDataForm();
            }
            $this->flightPaxData = $paxData;
        }
    }

    private function createHotelPaxDataForm(array $data): void
    {
        $this->hotelPaxData = [];
        if (isset($data['hotelPaxData']) && $paxCnt = count((array)$data['hotelPaxData'])) {
            $paxData = [];
            for ($i = 0; $i < $paxCnt; $i++) {
                $paxData[] = new HotelPaxDataForm();
            }
            $this->hotelPaxData = $paxData;
        }
    }

    public function isBooked(): bool
    {
        return $this->status === self::STATUS_BOOKED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }
}
