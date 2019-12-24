<?php

namespace frontend\models\form;

use common\models\Employee;
use common\models\Lead;
use common\models\query\OrderQuery;
use Yii;
use yii\base\Model;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the Form class for table "order".
 *
 * @property int $or_id
 * @property string $or_gid
 * @property string $or_uid
 * @property string $or_name
 * @property int $or_lead_id
 * @property string $or_description
 * @property int $or_status_id
 * @property int $or_pay_status_id
 * @property string $or_app_total
 * @property string $or_app_markup
 * @property string $or_agent_markup
 * @property string $or_client_total
 * @property string $or_client_currency
 * @property string $or_client_currency_rate
 * @property int $or_owner_user_id
 *
 */
class OrderForm extends Model
{

    public $or_id;
    public $or_gid;
    public $or_uid;
    public $or_name;
    public $or_lead_id;
    public $or_description;
    public $or_status_id;
    public $or_pay_status_id;
    public $or_app_total;
    public $or_app_markup;
    public $or_agent_markup;
    public $or_client_total;
    public $or_client_currency;
    public $or_client_currency_rate;
    public $or_owner_user_id;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['or_lead_id'], 'required'],
            [['or_lead_id', 'or_status_id', 'or_pay_status_id', 'or_owner_user_id'], 'integer'],
            [['or_description'], 'string'],
            [['or_app_total', 'or_app_markup', 'or_agent_markup', 'or_client_total', 'or_client_currency_rate'], 'number'],
            [['or_gid'], 'string', 'max' => 32],
            [['or_uid'], 'string', 'max' => 15],
            [['or_name'], 'string', 'max' => 40],
            [['or_client_currency'], 'string', 'max' => 3],
            [['or_gid'], 'unique'],
            [['or_uid'], 'unique'],
            [['or_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['or_lead_id' => 'id']],
            [['or_owner_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['or_owner_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'or_id' => 'ID',
            'or_gid' => 'GID',
            'or_uid' => 'UID',
            'or_name' => 'Name',
            'or_lead_id' => 'Lead ID',
            'or_description' => 'Description',
            'or_status_id' => 'Status ID',
            'or_pay_status_id' => 'Pay Status ID',
            'or_app_total' => 'App Total',
            'or_app_markup' => 'App Markup',
            'or_agent_markup' => 'Agent Markup',
            'or_client_total' => 'Client Total',
            'or_client_currency' => 'Client Currency',
            'or_client_currency_rate' => 'Client Currency Rate',
            'or_owner_user_id' => 'Owner User ID',
        ];
    }

}
