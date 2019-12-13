<?php

namespace frontend\models\form;

use common\models\Currency;
use common\models\Order;
use Yii;
use yii\base\Model;

/**
 * This is the form class for table "invoice".
 *
 * @property int $inv_id
 * @property string $inv_gid
 * @property string $inv_uid
 * @property int $inv_order_id
 * @property int $inv_status_id
 * @property string $inv_sum
 * @property string $inv_client_sum
 * @property string $inv_client_currency
 * @property string $inv_currency_rate
 * @property string $inv_description
 */
class InvoiceForm extends Model
{

    public $inv_id;
    public $inv_gid;
    public $inv_uid;
    public $inv_order_id;
    public $inv_status_id;
    public $inv_sum;
    public $inv_client_sum;
    public $inv_client_currency;
    public $inv_currency_rate;
    public $inv_description;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inv_order_id', 'inv_sum'], 'required'],
            [['inv_order_id', 'inv_status_id', 'inv_id'], 'integer'],
            [['inv_sum', 'inv_client_sum', 'inv_currency_rate'], 'number'],
            [['inv_description'], 'string'],
            [['inv_gid'], 'string', 'max' => 32],
            [['inv_uid'], 'string', 'max' => 15],
            [['inv_client_currency'], 'string', 'max' => 3],
            [['inv_gid'], 'unique'],
            [['inv_uid'], 'unique'],
            [['inv_client_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['inv_client_currency' => 'cur_code']],
            [['inv_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['inv_order_id' => 'or_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'inv_id' => 'ID',
            'inv_gid' => 'GID',
            'inv_uid' => 'UID',
            'inv_order_id' => 'Order ID',
            'inv_status_id' => 'Status ID',
            'inv_sum' => 'Sum',
            'inv_client_sum' => 'Client Sum',
            'inv_client_currency' => 'Client Currency',
            'inv_currency_rate' => 'Currency Rate',
            'inv_description' => 'Description',
        ];
    }

}
