<?php

namespace modules\twilio\src\entities\twilioJwtToken;

use Yii;

/**
 * This is the model class for table "twilio_jwt_token".
 *
 * @property int $jt_id
 * @property string $jt_agent
 * @property string $jt_token
 * @property string $jt_app_sid
 * @property string|null $jt_expire_dt
 * @property string|null $jt_created_dt
 * @property string|null $jt_updated_dt
 */
class TwilioJwtToken extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'twilio_jwt_token';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jt_agent', 'jt_token', 'jt_app_sid'], 'required'],
            [['jt_token', 'jt_app_sid'], 'string'],
            [['jt_expire_dt', 'jt_created_dt', 'jt_updated_dt'], 'safe'],
            [['jt_agent'], 'string', 'max' => 50],
            [['jt_agent'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jt_id' => 'Jt ID',
            'jt_agent' => 'Jt Agent',
            'jt_token' => 'Jt Token',
            'jt_app_sid' => 'Jt App Sid',
            'jt_expire_dt' => 'Jt Expire Dt',
            'jt_created_dt' => 'Jt Created Dt',
            'jt_updated_dt' => 'Jt Updated Dt',
        ];
    }

    /**
     * {@inheritdoc}
     * @return Scopes the active query used by this AR class.
     */
    public static function find(): Scopes
	{
        return new Scopes(static::class);
    }
}
