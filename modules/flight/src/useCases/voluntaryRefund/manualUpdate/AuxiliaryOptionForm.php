<?php

namespace modules\flight\src\useCases\voluntaryRefund\manualUpdate;

use common\components\validators\CheckIsNumberValidator;
use common\components\validators\CheckJsonValidator;
use common\components\validators\IsArrayValidator;
use common\components\validators\CheckIsBooleanValidator;
use frontend\helpers\JsonHelper;
use modules\product\src\entities\productQuoteOptionRefund\ProductQuoteOptionRefund;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class AuxiliaryOptionForm
 * @package modules\flight\src\useCases\api\voluntaryRefundCreate
 *
 * @property int $id
 * @property string $type
 * @property float $amount
 * @property float $refundable
 * @property string $status
 * @property bool $refundAllow
 * @property string $details
 * @property string $amountPerPax
 */
class AuxiliaryOptionForm extends \yii\base\Model
{
    public $id;

    public $type;

    public $amount;

    public $refundable;

    public $status;

    public $refundAllow;

    public $details;

    public $amountPerPax;

    public function __construct(ProductQuoteOptionRefund $option, $config = [])
    {
        $this->id = $option->pqor_id;
        $this->type = $option->productQuoteOption->pqoProductOption->po_name ?? '';
        $this->amount = $option->pqor_client_selling_price;
        $this->refundable = $option->pqor_client_refund_amount;
        $this->refundAllow = $option->pqor_refund_allow;
        $this->status = ArrayHelper::getValue(JsonHelper::decode($option->pqor_data_json), 'status');
        $this->details = $option->pqor_data_json['details'] ?? [];
        $this->amountPerPax = $option->pqor_data_json['amountPerPax'] ?? [];
        parent::__construct($config);
    }

//    public function rules(): array
//    {
//        return [
//            [['amount', 'refundable', 'refundAllow'], 'required'],
//            [['refundAllow'], CheckIsBooleanValidator::class],
//            [['refundable', 'amount'], CheckIsNumberValidator::class, 'allowInt' => true]
//        ];
//    }
}
