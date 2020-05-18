<?php

namespace modules\twilio\src\entities\conferenceLog;

use Yii;

/**
 * This is the model class for table "conference_log".
 *
 * @property int $cl_id
 * @property string $cl_cf_sid
 * @property int $cl_cf_id
 * @property int|null $cl_sequence_number
 * @property string|null $cl_status_callback_event
 * @property string|null $cl_json_data
 * @property string|null $cl_created_dt
 *
 * @property Conference $clCf
 */
class ConferenceLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'conference_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cl_cf_sid', 'cl_cf_id'], 'required'],
            [['cl_cf_id', 'cl_sequence_number'], 'integer'],
            [['cl_json_data', 'cl_created_dt'], 'safe'],
            [['cl_cf_sid'], 'string', 'max' => 34],
            [['cl_status_callback_event'], 'string', 'max' => 30],
            [['cl_cf_id'], 'exist', 'skipOnError' => true, 'targetClass' => Conference::className(), 'targetAttribute' => ['cl_cf_id' => 'cf_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cl_id' => 'Cl ID',
            'cl_cf_sid' => 'Cl Cf Sid',
            'cl_cf_id' => 'Cl Cf ID',
            'cl_sequence_number' => 'Cl Sequence Number',
            'cl_status_callback_event' => 'Cl Status Callback Event',
            'cl_json_data' => 'Cl Json Data',
            'cl_created_dt' => 'Cl Created Dt',
        ];
    }

    /**
     * Gets query for [[ClCf]].
     *
     * @return \yii\db\ActiveQuery|ConferenceQuery
     */
    public function getClCf()
    {
        return $this->hasOne(Conference::className(), ['cf_id' => 'cl_cf_id']);
    }

    /**
     * {@inheritdoc}
     * @return Scopes the active query used by this AR class.
     */
    public static function find()
    {
        return new Scopes(get_called_class());
    }
}
