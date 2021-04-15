<?php

namespace webapi\src\forms\flight\flights\price;

use common\components\validators\CheckJsonValidator;
use common\models\Currency;
use frontend\helpers\JsonHelper;
use sales\helpers\ErrorsToStringHelper;
use webapi\src\forms\flight\flights\price\detail\PriceDetailApiForm;
use yii\base\Model;

/**
 * Class PriceApiForm
 *
 * @property $tickets
 * @property $selling
 * @property $currentProfit
 * @property $fare
 * @property $net
 * @property $taxes
 * @property $tips
 * @property $currency
 * @property $detail
 *
 * @property PriceDetailApiForm[] $priceDetailApiForms
 */
class PriceApiForm extends Model
{
    public $tickets;
    public $selling;
    public $currentProfit;
    public $fare;
    public $net;
    public $taxes;
    public $tips;
    public $currency;
    public $detail;

    private array $priceDetailApiForms = [];

    public function rules(): array
    {
        return [
            [['tickets'], 'integer'],

            [['selling'], 'number'],
            [['currentProfit'], 'number'],
            [['fare'], 'number'],
            [['net'], 'number'],
            [['taxes'], 'number'],
            [['tips'], 'number'],

            [['currency'], 'string', 'max' => 3],
            [['currency'], 'string', 'max' => 3],
            [['currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['currency' => 'cur_code']],

            [['detail'], 'required'],
            [['detail'], CheckJsonValidator::class],
            [['detail'], 'filter', 'filter' => static function ($value) {
                return JsonHelper::decode($value);
            }],
            [['detail'], 'checkDetail'],
        ];
    }

    public function checkDetail($attribute): void
    {
        foreach ($this->detail as $paxType => $value) {
            $priceDetailApiForm = new PriceDetailApiForm($paxType);
            if (!$priceDetailApiForm->load($value)) {
                $this->addError($attribute, 'PriceDetailApiForm is not loaded');
                break;
            }
            if (!$priceDetailApiForm->validate()) {
                $this->addError($attribute, 'PriceDetailApiForm: ' . ErrorsToStringHelper::extractFromModel($priceDetailApiForm));
                break;
            }
            $this->priceDetailApiForms[$paxType] = $priceDetailApiForm;
        }
    }

    public function getPriceDetailApiForms(): array
    {
        return $this->priceDetailApiForms;
    }
}
