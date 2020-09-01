<?php

namespace sales\model\phoneLine\userPersonalPhoneNumber\entity;

use common\models\Employee;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_personal_phone_number".
 *
 * @property int $upn_id
 * @property int $upn_user_id
 * @property string $upn_phone_number
 * @property string|null $upn_title
 * @property int|null $upn_approved
 * @property int|null $upn_enabled
 * @property int|null $upn_created_user_id
 * @property int|null $upn_updated_user_id
 * @property string|null $upn_created_dt
 * @property string|null $upn_updated_dt
 *
 * @property Employee $upnCreatedUser
 * @property Employee $upnUpdatedUser
 * @property Employee $upnUser
 */
class UserPersonalPhoneNumber extends \yii\db\ActiveRecord
{
	public function behaviors(): array
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['upn_created_dt', 'upn_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['upn_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s'),
			],
		];
	}

    public function rules(): array
    {
        return [
            ['upn_approved', 'integer'],

            ['upn_created_dt', 'safe'],

            ['upn_created_user_id', 'integer'],
            ['upn_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['upn_created_user_id' => 'id']],

            ['upn_enabled', 'integer'],

            ['upn_phone_number', 'required'],
            ['upn_phone_number', 'string', 'max' => 15],

            ['upn_title', 'string', 'max' => 100],

            ['upn_updated_dt', 'safe'],

            ['upn_updated_user_id', 'integer'],
            ['upn_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['upn_updated_user_id' => 'id']],

            ['upn_user_id', 'required'],
            ['upn_user_id', 'integer'],
            ['upn_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['upn_user_id' => 'id']],
        ];
    }

    public function getUpnCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'upn_created_user_id']);
    }

    public function getUpnUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'upn_updated_user_id']);
    }

    public function getUpnUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'upn_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'upn_id' => 'ID',
            'upn_user_id' => 'User ID',
            'upn_phone_number' => 'Phone Number',
            'upn_title' => 'Title',
            'upn_approved' => 'Approved',
            'upn_enabled' => 'Enabled',
            'upn_created_user_id' => 'Created User ID',
            'upn_updated_user_id' => 'Updated User ID',
            'upn_created_dt' => 'Created Dt',
            'upn_updated_dt' => 'Updated Dt',
        ];
    }

	public static function find(): Scopes
	{
		return new Scopes(static::class);
	}

    public static function tableName(): string
    {
        return 'user_personal_phone_number';
    }
}
