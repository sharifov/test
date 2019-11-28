<?php

namespace common\models;

use common\models\query\ProductQuery;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product".
 *
 * @property int $pr_id
 * @property int $pr_type_id
 * @property string|null $pr_name
 * @property int $pr_lead_id
 * @property string|null $pr_description
 * @property int|null $pr_status_id
 * @property float|null $pr_service_fee_percent
 * @property int|null $pr_created_user_id
 * @property int|null $pr_updated_user_id
 * @property string|null $pr_created_dt
 * @property string|null $pr_updated_dt
 *
 * @property Employee $prCreatedUser
 * @property Lead $prLead
 * @property ProductType $prType
 * @property Employee $prUpdatedUser
 * @property ProductQuote[] $productQuotes
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pr_type_id', 'pr_lead_id'], 'required'],
            [['pr_type_id', 'pr_lead_id', 'pr_status_id', 'pr_created_user_id', 'pr_updated_user_id'], 'integer'],
            [['pr_description'], 'string'],
            [['pr_service_fee_percent'], 'number'],
            [['pr_created_dt', 'pr_updated_dt'], 'safe'],
            [['pr_name'], 'string', 'max' => 40],
            [['pr_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pr_created_user_id' => 'id']],
            [['pr_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['pr_lead_id' => 'id']],
            [['pr_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductType::class, 'targetAttribute' => ['pr_type_id' => 'pt_id']],
            [['pr_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pr_updated_user_id' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pr_id' => 'ID',
            'pr_type_id' => 'Type ID',
            'pr_name' => 'Name',
            'pr_lead_id' => 'Lead ID',
            'pr_description' => 'Description',
            'pr_status_id' => 'Status ID',
            'pr_service_fee_percent' => 'Service Fee Percent',
            'pr_created_user_id' => 'Created User ID',
            'pr_updated_user_id' => 'Updated User ID',
            'pr_created_dt' => 'Created Dt',
            'pr_updated_dt' => 'Updated Dt',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pr_created_dt', 'pr_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['pr_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'pr_created_user_id',
                'updatedByAttribute' => 'pr_updated_user_id',
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'pr_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'pr_lead_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrType()
    {
        return $this->hasOne(ProductType::class, ['pt_id' => 'pr_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'pr_updated_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductQuotes()
    {
        return $this->hasMany(ProductQuote::class, ['pq_product_id' => 'pr_id']);
    }

    /**
     * {@inheritdoc}
     * @return ProductQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductQuery(get_called_class());
    }
}
