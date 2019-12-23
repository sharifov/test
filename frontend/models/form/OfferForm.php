<?php

namespace frontend\models\form;

use common\models\Currency;
use common\models\Employee;
use common\models\Lead;
use Yii;
use yii\base\Model;

/**
 * This is the Form class for table "offer".
 *
 * @property int $of_id
//  @property string $of_gid
//  @property string $of_uid
 * @property string $of_name
 * @property int $of_lead_id
 * @property int $of_status_id
 * @property int $of_owner_user_id
 * @property string|null $of_client_currency
 * @property float|null $of_client_currency_rate
 * @property float|null $of_app_total
 * @property float|null $of_client_total
 *
 */
class OfferForm extends Model
{

    public $of_id;
//    public $of_gid;
//    public $of_uid;
    public $of_name;
    public $of_lead_id;
    public $of_status_id;
    public $of_owner_user_id;
    public $of_client_currency;
    public $of_client_currency_rate;
    public $of_app_total;
    public $of_client_total;


    public function rules()
    {
        return [
            [['of_lead_id'], 'required'],
            [['of_lead_id', 'of_status_id', 'of_owner_user_id'], 'integer'],
            [['of_client_currency_rate', 'of_app_total', 'of_client_total'], 'number'],

//            [['of_gid'], 'string', 'max' => 32],
//            [['of_uid'], 'string', 'max' => 15],
            [['of_client_currency'], 'string', 'max' => 3],
            [['of_name'], 'string', 'max' => 40],
//            [['of_gid'], 'unique'],
//            [['of_uid'], 'unique'],
            [['of_client_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['of_client_currency' => 'cur_code']],
            [['of_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['of_lead_id' => 'id']],
            [['of_owner_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['of_owner_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'of_id'                 => 'ID',
//            'of_gid'                => 'Gid',
//            'of_uid'                => 'Uid',
            'of_name'               => 'Name',
            'of_lead_id'            => 'Lead ID',
            'of_status_id'          => 'Status ID',
            'of_owner_user_id'      => 'Owner User ID',
            'of_client_currency' => 'Client Currency',
            'of_client_currency_rate' => 'Client Currency Rate',
            'of_app_total' => 'App Total',
            'of_client_total' => 'Client Total',
        ];
    }

}
