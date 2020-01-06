<?php

namespace modules\flight\models;

use common\models\Employee;
use Yii;

/**
 * This is the model class for table "flight_quote_status_log".
 *
 * @property int $qsl_id
 * @property int|null $qsl_created_user_id
 * @property int $qsl_flight_quote_id
 * @property int|null $qsl_status_id
 * @property string|null $qsl_created_dt
 *
 * @property Employee $qslCreatedUser
 * @property FlightQuote $qslFlightQuote
 */
class FlightQuoteStatusLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'flight_quote_status_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qsl_created_user_id', 'qsl_flight_quote_id', 'qsl_status_id'], 'integer'],
            [['qsl_flight_quote_id'], 'required'],
            [['qsl_created_dt'], 'safe'],
            [['qsl_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['qsl_created_user_id' => 'id']],
            [['qsl_flight_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => FlightQuote::class, 'targetAttribute' => ['qsl_flight_quote_id' => 'fq_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'qsl_id' => 'Qsl ID',
            'qsl_created_user_id' => 'Qsl Created User ID',
            'qsl_flight_quote_id' => 'Qsl Flight Quote ID',
            'qsl_status_id' => 'Qsl Status ID',
            'qsl_created_dt' => 'Qsl Created Dt',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQslCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'qsl_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQslFlightQuote()
    {
        return $this->hasOne(FlightQuote::class, ['fq_id' => 'qsl_flight_quote_id']);
    }

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\query\FlightQuoteStatusLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \modules\flight\models\query\FlightQuoteStatusLogQuery(static::class);
    }
}
