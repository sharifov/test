<?php

namespace modules\order\src\forms\api\createC2b;

use common\components\validators\CheckJsonValidator;
use modules\product\src\entities\productType\ProductType;
use src\forms\CompositeForm;

/**
 * Class QuotesForm
 * @package modules\order\src\forms\api\createC2b
 *
 * @property string $productKey
 * @property string $status
 * @property string $originSearchData
 * @property string $quoteOtaId
 * @property int $orderId
 * @property string $bookingId
 *
 * @property ProductHolderForm $holder
 * @property HotelRequestForm $hotelRequest
 * @property ProductType $productType
 * @property FlightPaxDataForm[] $flightPaxData
 * @property HotelPaxDataForm[] $hotelPaxData
 * @property ProductQuoteOptionsForm[] $options
 */
class QuotesForm extends CompositeForm
{
    public $productKey;

    public $status;

    public $originSearchData;

    public $quoteOtaId;

    public $orderId;

    public $productType;

    public $bookingId;

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
        $this->createHotelRequestForm($data);
        $this->createOptionsForm($data);
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
        return ['holder', 'flightPaxData', 'hotelPaxData', 'options', 'hotelRequest'];
    }

    public function validateProductType(): bool
    {
        if (!$this->productType = ProductType::findOne(['pt_key' => $this->productKey])) {
            $this->addError('productKey', 'Product type not found by key: ' . $this->productKey);
            return false;
        }

        if ($this->productType->isHotel() && empty($this->hotelRequest)) {
            $this->addError('hotelRequest', 'Hotel request not found');
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

    private function createHotelRequestForm(array $data): void
    {
        $this->hotelRequest = [];
        if (isset($data['hotelRequest'])) {
            $this->hotelRequest = new HotelRequestForm();
        }
    }

    private function createOptionsForm(array $data): void
    {
        $this->options = [];
        if (isset($data['options']) && $paxCnt = count((array)$data['options'])) {
            $options = [];
            for ($i = 0; $i < $paxCnt; $i++) {
                $options[] = new ProductQuoteOptionsForm();
            }
            $this->options = $options;
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
