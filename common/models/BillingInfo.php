<?php

namespace common\models;

use modules\order\src\entities\order\Order;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "billing_info".
 *
 * @property int $bi_id
 * @property string $bi_first_name
 * @property string $bi_last_name
 * @property string|null $bi_middle_name
 * @property string|null $bi_company_name
 * @property string $bi_address_line1
 * @property string|null $bi_address_line2
 * @property string $bi_city
 * @property string|null $bi_state
 * @property string $bi_country
 * @property string|null $bi_zip
 * @property string|null $bi_contact_phone
 * @property string|null $bi_contact_email
 * @property string|null $bi_contact_name
 * @property int|null $bi_payment_method_id
 * @property int|null $bi_cc_id
 * @property int|null $bi_order_id
 * @property int|null $bi_status_id
 * @property int|null $bi_created_user_id
 * @property int|null $bi_updated_user_id
 * @property string|null $bi_created_dt
 * @property string|null $bi_updated_dt
 *
 * @property CreditCard $biCc
 * @property Employee $biCreatedUser
 * @property Order $biOrder
 * @property Employee $biUpdatedUser
 */
class BillingInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'billing_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bi_first_name', 'bi_last_name', 'bi_address_line1', 'bi_city', 'bi_country'], 'required'],
            [['bi_payment_method_id', 'bi_cc_id', 'bi_order_id', 'bi_status_id', 'bi_created_user_id', 'bi_updated_user_id'], 'integer'],
            [['bi_created_dt', 'bi_updated_dt'], 'safe'],
            [['bi_first_name', 'bi_last_name', 'bi_middle_name', 'bi_city'], 'string', 'max' => 30],
            [['bi_company_name', 'bi_state'], 'string', 'max' => 40],
            [['bi_address_line1', 'bi_address_line2'], 'string', 'max' => 50],
            [['bi_country'], 'string', 'max' => 2],
            [['bi_zip'], 'string', 'max' => 10],
            [['bi_contact_phone'], 'string', 'max' => 20],
            [['bi_contact_email'], 'string', 'max' => 160],
            [['bi_contact_name'], 'string', 'max' => 60],
            [['bi_cc_id'], 'exist', 'skipOnError' => true, 'targetClass' => CreditCard::class, 'targetAttribute' => ['bi_cc_id' => 'cc_id']],
            [['bi_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['bi_created_user_id' => 'id']],
            [['bi_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['bi_order_id' => 'or_id']],
            [['bi_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['bi_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bi_id' => 'ID',
            'bi_first_name' => 'First Name',
            'bi_last_name' => 'Last Name',
            'bi_middle_name' => 'Middle Name',
            'bi_company_name' => 'Company Name',
            'bi_address_line1' => 'Address Line1',
            'bi_address_line2' => 'Address Line2',
            'bi_city' => 'City',
            'bi_state' => 'State',
            'bi_country' => 'Country',
            'bi_zip' => 'Zip',
            'bi_contact_phone' => 'Contact Phone',
            'bi_contact_email' => 'Contact Email',
            'bi_contact_name' => 'Contact Name',
            'bi_payment_method_id' => 'Payment Method ID',
            'bi_cc_id' => 'CC ID',
            'bi_order_id' => 'Order ID',
            'bi_status_id' => 'Status ID',
            'bi_created_user_id' => 'Created User ID',
            'bi_updated_user_id' => 'Updated User ID',
            'bi_created_dt' => 'Created Dt',
            'bi_updated_dt' => 'Updated Dt',
        ];
    }


    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['bi_created_dt', 'bi_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['bi_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'bi_created_user_id',
                'updatedByAttribute' => 'bi_updated_user_id',
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBiCc()
    {
        return $this->hasOne(CreditCard::class, ['cc_id' => 'bi_cc_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBiCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'bi_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBiOrder()
    {
        return $this->hasOne(Order::class, ['or_id' => 'bi_order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBiUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'bi_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\BillingInfoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\BillingInfoQuery(static::class);
    }
}
