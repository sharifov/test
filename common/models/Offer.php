<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "offer".
 *
 * @property int $of_id
 * @property string $of_gid
 * @property string $of_uid
 * @property string $of_name
 * @property int $of_lead_id
 * @property int $of_status_id
 * @property int $of_owner_user_id
 * @property int $of_created_user_id
 * @property int $of_updated_user_id
 * @property string $of_created_dt
 * @property string $of_updated_dt
 *
 * @property Employee $ofCreatedUser
 * @property Lead $ofLead
 * @property Employee $ofOwnerUser
 * @property Employee $ofUpdatedUser
 * @property OfferProduct[] $offerProducts
 * @property ProductQuote[] $opProductQuotes
 */
class Offer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'offer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['of_gid', 'of_lead_id'], 'required'],
            [['of_lead_id', 'of_status_id', 'of_owner_user_id', 'of_created_user_id', 'of_updated_user_id'], 'integer'],
            [['of_created_dt', 'of_updated_dt'], 'safe'],
            [['of_gid'], 'string', 'max' => 32],
            [['of_uid'], 'string', 'max' => 15],
            [['of_name'], 'string', 'max' => 40],
            [['of_gid'], 'unique'],
            [['of_uid'], 'unique'],
            [['of_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['of_created_user_id' => 'id']],
            [['of_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['of_lead_id' => 'id']],
            [['of_owner_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['of_owner_user_id' => 'id']],
            [['of_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['of_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'of_id' => 'ID',
            'of_gid' => 'Gid',
            'of_uid' => 'Uid',
            'of_name' => 'Name',
            'of_lead_id' => 'Lead ID',
            'of_status_id' => 'Status ID',
            'of_owner_user_id' => 'Owner User ID',
            'of_created_user_id' => 'Created User ID',
            'of_updated_user_id' => 'Updated User ID',
            'of_created_dt' => 'Created Dt',
            'of_updated_dt' => 'Updated Dt',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['of_created_dt', 'of_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['of_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'of_created_user_id',
                'updatedByAttribute' => 'of_updated_user_id',
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'of_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'of_lead_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfOwnerUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'of_owner_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'of_updated_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferProducts()
    {
        return $this->hasMany(OfferProduct::class, ['op_offer_id' => 'of_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOpProductQuotes()
    {
        return $this->hasMany(ProductQuote::class, ['of_id' => 'op_product_quote_id'])->viaTable('offer_product', ['op_offer_id' => 'of_id']);
    }

    /**
     * {@inheritdoc}
     * @return OfferQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OfferQuery(get_called_class());
    }
}
