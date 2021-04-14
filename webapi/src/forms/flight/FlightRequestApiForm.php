<?php

namespace  webapi\src\forms\flight;

use common\components\validators\CheckJsonValidator;
use frontend\helpers\JsonHelper;
use modules\flight\models\FlightQuote;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productType\ProductType;
use sales\helpers\ErrorsToStringHelper;
use webapi\src\forms\flight\flights\FlightApiForm;
use yii\base\Model;

/**
 * Class FlightRequestApiForm
 *
 * @property $fareId
 * @property $flights
 * @property $parentId
 * @property $parentBookingId
 *
 * @property Order $order
 * @property FlightQuote $flightQuote
 * @property FlightApiForm[] $flightApiForms
 */
class FlightRequestApiForm extends Model
{
    public $fareId;
    public $flights;
    public $parentId;
    public $parentBookingId;

    private Order $order;
    private $flightQuote;
    private array $flightApiForms;

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
        ];
    }

    public function detectOrder($attribute): void
    {
        if (!$this->order = Order::findOne(['or_fare_id' => $this->fareId])) {
            $this->addError($attribute, 'Order not found by fareId(' . $this->fareId . ')');
        }
        if (!$this->flightQuote = self::getFlightQuoteByOrderId($this->order->getId())) {
            $this->addError($attribute, 'FlightQuote not found in Order fareId(' . $this->fareId . ')');
        }
    }

    public function checkFlights($attribute): void
    {
        foreach ($this->flights as $key => $flight) {
            $flightApiForm = new FlightApiForm();
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

    public function formName(): string
    {
        return '';
    }

    public static function getFlightQuoteByOrderId(int $orderId): ?FlightQuote
    {
        $flightQuote = FlightQuote::find()
            ->innerJoin(ProductQuote::tableName(), 'pq_id = fq_product_quote_id')
            ->innerJoin(Product::tableName(), 'pr_id = pq_product_id')
            ->andWhere(['pq_order_id' => $orderId])
            ->andWhere(['pr_type_id' => ProductType::PRODUCT_FLIGHT])
            ->orderBy(['fq_id' => SORT_DESC])
            ->one();
        /** @var FlightQuote|null $flightQuote */
        return $flightQuote;
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
}
