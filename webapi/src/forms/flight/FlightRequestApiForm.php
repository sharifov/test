<?php

namespace  webapi\src\forms\flight;

use common\components\validators\CheckJsonValidator;
use frontend\helpers\JsonHelper;
use modules\flight\models\FlightQuote;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionStatus;
use modules\product\src\entities\productType\ProductType;
use sales\helpers\ErrorsToStringHelper;
use webapi\src\forms\flight\flights\FlightApiForm;
use webapi\src\forms\flight\options\OptionApiForm;
use webapi\src\services\flight\FlightManageApiService;
use yii\base\Model;

/**
 * Class FlightRequestApiForm
 *
 * @property $fareId
 * @property $flights
 * @property $parentId
 * @property $parentBookingId
 * @property $options
 *
 * @property Order $order
 * @property FlightQuote $flightQuote
 * @property FlightApiForm[] $flightApiForms
 * @property OptionApiForm[] $optionApiForms
 */
class FlightRequestApiForm extends Model
{
    public $fareId;
    public $flights;
    public $parentId;
    public $parentBookingId;
    public $options;

    private $order;
    private $flightQuote;
    private array $flightApiForms = [];
    private array $optionApiForms = [];

    public function rules(): array
    {
        return [
            [['fareId'], 'required'],
            [['fareId'], 'string', 'max' => 255],
            [['fareId'], 'detectOrder'],

            [['parentBookingId'], 'string', 'max' => 50],

            [['parentId'], 'integer'],
            [['parentId'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            [['flights'], 'required'],
            [['flights'], CheckJsonValidator::class],
            [['flights'], 'filter', 'filter' => static function ($value) {
                return JsonHelper::decode($value);
            }],
            [['flights'], 'checkFlights'],

            [['options'], 'checkOptions'],
        ];
    }

    public function detectOrder($attribute): void
    {
        if (!$this->order = Order::findOne(['or_fare_id' => $this->fareId])) {
            $this->addError($attribute, 'Order not found by fareId(' . $this->fareId . ')');
        }
        if ($this->order && !$this->flightQuote = FlightManageApiService::getFlightQuoteByOrderId($this->order->getId())) {
            $this->addError($attribute, 'FlightQuote not found in Order fareId(' . $this->fareId . ')');
        }
    }

    public function checkFlights($attribute): void
    {
        foreach ($this->flights as $key => $flight) {
            $flightApiForm = new FlightApiForm($flight);
            if (!$flightApiForm->load($flight)) {
                $this->addError($attribute, 'FlightApiForm is not loaded');
                break;
            }
            if (!$flightApiForm->validate()) {
                $this->addError($attribute, 'FlightApiForm error: ' . ErrorsToStringHelper::extractFromModel($flightApiForm));
                break;
            }
            $this->flightApiForms[$key] = $flightApiForm;
        }
    }

    public function checkOptions($attribute): void
    {
        if (!empty($this->options) && JsonHelper::isValidJson($this->options) && $options = JsonHelper::decode($this->options)) {
            foreach ($options as $key => $option) {
                $optionApiForm = new OptionApiForm($option);
                if (!$optionApiForm->load($option)) {
                    $this->addError($attribute, 'OptionApiForm is not loaded');
                    break;
                }
                if (!$optionApiForm->validate()) {
                    $this->addError($attribute, 'OptionApiForm error: ' . ErrorsToStringHelper::extractFromModel($optionApiForm));
                    break;
                }
                $this->optionApiForms[$key] = $optionApiForm;
            }
        }
    }

    public function formName(): string
    {
        return '';
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @return FlightApiForm[]
     */
    public function getFlightApiForms(): array
    {
        return $this->flightApiForms;
    }

    public function getFlightQuote(): FlightQuote
    {
        return $this->flightQuote;
    }

    /**
     * @return OptionApiForm[]
     */
    public function getOptionApiForms(): array
    {
        return $this->optionApiForms;
    }
}
