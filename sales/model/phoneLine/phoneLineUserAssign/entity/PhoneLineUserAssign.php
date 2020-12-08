<?php

namespace sales\model\phoneLine\phoneLineUserAssign\entity;

use common\models\Employee;
use sales\model\phoneLine\phoneLine\entity\PhoneLine;
use sales\model\userVoiceMail\entity\UserVoiceMail;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "phone_line_user_assign".
 *
 * @property int $plus_line_id
 * @property int $plus_user_id
 * @property int|null $plus_allow_in
 * @property int|null $plus_allow_out
 * @property int|null $plus_uvm_id
 * @property int|null $plus_enabled
 * @property string|null $plus_settings_json
 * @property int|null $plus_created_user_id
 * @property int|null $plus_updated_user_id
 * @property string|null $plus_created_dt
 * @property string|null $plus_updated_dt
 *
 * @property Employee $plusCreatedUser
 * @property PhoneLine $plusLine
 * @property Employee $plusUpdatedUser
 * @property Employee $plusUser
 * @property UserVoiceMail $plusUvm
 */
class PhoneLineUserAssign extends \yii\db\ActiveRecord
{
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['plus_created_dt', 'plus_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['plus_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['plus_line_id', 'plus_user_id'], 'unique', 'targetAttribute' => ['plus_line_id', 'plus_user_id']],

            ['plus_allow_in', 'integer'],

            ['plus_allow_out', 'integer'],

            ['plus_created_dt', 'safe'],

            ['plus_created_user_id', 'integer'],
            ['plus_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['plus_created_user_id' => 'id']],

            ['plus_enabled', 'integer'],

            ['plus_line_id', 'required'],
            ['plus_line_id', 'integer'],
            ['plus_line_id', 'exist', 'skipOnError' => true, 'targetClass' => PhoneLine::class, 'targetAttribute' => ['plus_line_id' => 'line_id']],

            ['plus_settings_json', 'safe'],

            ['plus_updated_dt', 'safe'],

            ['plus_updated_user_id', 'integer'],
            ['plus_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['plus_updated_user_id' => 'id']],

            ['plus_user_id', 'required'],
            ['plus_user_id', 'integer'],
            ['plus_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['plus_user_id' => 'id']],

            ['plus_uvm_id', 'integer'],
            ['plus_uvm_id', 'exist', 'skipOnError' => true, 'targetClass' => UserVoiceMail::class, 'targetAttribute' => ['plus_uvm_id' => 'uvm_id']],
        ];
    }

    public function getPlusCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'plus_created_user_id']);
    }

    public function getPlusLine(): \yii\db\ActiveQuery
    {
        return $this->hasOne(PhoneLine::class, ['line_id' => 'plus_line_id']);
    }

    public function getPlusUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'plus_updated_user_id']);
    }

    public function getPlusUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'plus_user_id']);
    }

    public function getPlusUvm(): \yii\db\ActiveQuery
    {
        return $this->hasOne(UserVoiceMail::class, ['uvm_id' => 'plus_uvm_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'plus_line_id' => 'Line ID',
            'plus_user_id' => 'User ID',
            'plus_allow_in' => 'Allow In',
            'plus_allow_out' => 'Allow Out',
            'plus_uvm_id' => 'Uvm ID',
            'plus_enabled' => 'Enabled',
            'plus_settings_json' => 'Settings Json',
            'plus_created_user_id' => 'Created User ID',
            'plus_updated_user_id' => 'Updated User ID',
            'plus_created_dt' => 'Created Dt',
            'plus_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'phone_line_user_assign';
    }
}
