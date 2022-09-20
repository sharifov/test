<?php

namespace modules\quoteAward\src\entities;

use common\models\Employee;
use common\models\Quote;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

/**
 * This is the model class for table "quote_flight".
 *
 * @property int $qf_id
 * @property int $qf_quote_id
 * @property string|null $qf_booking_type
 * @property int|null $qf_user_id
 * @property string|null $qf_record_locator
 * @property string|null $qf_gds
 * @property string|null $qf_gds_pcc
 * @property string|null $qf_cabin
 * @property string|null $qf_trip_type
 * @property int|null $qf_check_payment
 * @property string|null $qf_fare_type
 * @property string|null $qf_created_dt
 * @property string|null $qf_updated_dt
 * @property int|null $qf_updated_user_id
 *
 * @property Quote $qfQuote
 * @property Employee $qfUpdatedUser
 * @property Employee $qfUser
 * @property QuoteFlightPrice[] $quoteFlightPrices
 */
class QuoteFlight extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'quote_flight';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['qf_quote_id'], 'required'],
            [['qf_quote_id', 'qf_user_id', 'qf_check_payment', 'qf_updated_user_id'], 'integer'],
            [['qf_created_dt', 'qf_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
            [['qf_booking_type', 'qf_fare_type'], 'string', 'max' => 20],
            [['qf_record_locator'], 'string', 'max' => 8],
            [['qf_gds', 'qf_cabin'], 'string', 'max' => 1],
            [['qf_gds_pcc'], 'string', 'max' => 50],
            [['qf_trip_type'], 'string', 'max' => 2],
            [['qf_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quote::className(), 'targetAttribute' => ['qf_quote_id' => 'id']],
            [['qf_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['qf_updated_user_id' => 'id']],
            [['qf_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['qf_user_id' => 'id']],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['qf_created_dt', 'qf_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['qf_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['qf_updated_user_id'],
                ],
                'defaultValue' => null
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'qf_id' => 'Qf ID',
            'qf_quote_id' => 'Qf Quote ID',
            'qf_booking_type' => 'Qf Booking Type',
            'qf_user_id' => 'Qf User ID',
            'qf_record_locator' => 'Qf Record Locator',
            'qf_gds' => 'Qf Gds',
            'qf_gds_pcc' => 'Qf Gds Pcc',
            'qf_cabin' => 'Qf Cabin',
            'qf_trip_type' => 'Qf Trip Type',
            'qf_check_payment' => 'Qf Check Payment',
            'qf_fare_type' => 'Qf Fare Type',
            'qf_created_dt' => 'Qf Created Dt',
            'qf_updated_dt' => 'Qf Updated Dt',
            'qf_updated_user_id' => 'Qf Updated User ID',
        ];
    }

    /**
     * Gets query for [[QfQuote]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQfQuote(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Quote::className(), ['id' => 'qf_quote_id']);
    }

    /**
     * Gets query for [[QfUpdatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQfUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::className(), ['id' => 'qf_updated_user_id']);
    }

    /**
     * Gets query for [[QfUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQfUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::className(), ['id' => 'qf_user_id']);
    }

    /**
     * Gets query for [[QuoteFlightPrices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuoteFlightPrices()
    {
        return $this->hasMany(QuoteFlightPrice::className(), ['fpr_qf_id' => 'qf_id']);
    }
}
