<?php

namespace sales\model\phoneLine\phoneLinePhoneNumber\entity;

use common\models\Employee;
use sales\model\phoneLine\phoneLine\entity\PhoneLine;
use sales\model\phoneList\entity\PhoneList;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "phone_line_phone_number".
 *
 * @property int $plpn_line_id
 * @property int $plpn_pl_id
 * @property int|null $plpn_default
 * @property int|null $plpn_enabled
 * @property string|null $plpn_settings_json
 * @property int|null $plpn_created_user_id
 * @property int|null $plpn_updated_user_id
 * @property string|null $plpn_created_dt
 * @property string|null $plpn_updated_dt
 *
 * @property Employee $plpnCreatedUser
 * @property PhoneLine $plpnLine
 * @property PhoneList $plpnPl
 * @property Employee $plpnUpdatedUser
 */
class PhoneLinePhoneNumber extends \yii\db\ActiveRecord
{
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['plpn_created_dt', 'plpn_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['plpn_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['plpn_line_id', 'plpn_pl_id'], 'unique', 'targetAttribute' => ['plpn_line_id', 'plpn_pl_id']],

            ['plpn_created_dt', 'safe'],

            ['plpn_created_user_id', 'integer'],
            ['plpn_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['plpn_created_user_id' => 'id']],

            ['plpn_default', 'integer'],

            ['plpn_enabled', 'integer'],

            ['plpn_line_id', 'required'],
            ['plpn_line_id', 'integer'],
            ['plpn_line_id', 'exist', 'skipOnError' => true, 'targetClass' => PhoneLine::class, 'targetAttribute' => ['plpn_line_id' => 'line_id']],

            ['plpn_pl_id', 'required'],
            ['plpn_pl_id', 'integer'],
            ['plpn_pl_id', 'unique'],
            ['plpn_pl_id', 'exist', 'skipOnError' => true, 'targetClass' => PhoneList::class, 'targetAttribute' => ['plpn_pl_id' => 'pl_id']],

            ['plpn_settings_json', 'safe'],

            ['plpn_updated_dt', 'safe'],

            ['plpn_updated_user_id', 'integer'],
            ['plpn_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['plpn_updated_user_id' => 'id']],
        ];
    }

    public function getPlpnCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'plpn_created_user_id']);
    }

    public function getPlpnLine(): \yii\db\ActiveQuery
    {
        return $this->hasOne(PhoneLine::class, ['line_id' => 'plpn_line_id']);
    }

    public function getPlpnPl(): \yii\db\ActiveQuery
    {
        return $this->hasOne(PhoneList::class, ['pl_id' => 'plpn_pl_id']);
    }

    public function getPlpnUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'plpn_updated_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'plpn_line_id' => 'Line ID',
            'plpn_pl_id' => 'Pl ID',
            'plpn_default' => 'Default',
            'plpn_enabled' => 'Enabled',
            'plpn_settings_json' => 'Settings Json',
            'plpn_created_user_id' => 'Created User ID',
            'plpn_updated_user_id' => 'Updated User ID',
            'plpn_created_dt' => 'Created Dt',
            'plpn_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'phone_line_phone_number';
    }
}
