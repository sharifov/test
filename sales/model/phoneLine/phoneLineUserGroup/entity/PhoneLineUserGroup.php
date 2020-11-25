<?php

namespace sales\model\phoneLine\phoneLineUserGroup\entity;

use common\models\UserGroup;
use sales\model\phoneLine\phoneLine\entity\PhoneLine;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "phone_line_user_group".
 *
 * @property int $plug_line_id
 * @property int $plug_ug_id
 * @property string|null $plug_created_dt
 * @property string|null $plug_updated_dt
 *
 * @property PhoneLine $plugLine
 * @property UserGroup $plugUg
 */
class PhoneLineUserGroup extends \yii\db\ActiveRecord
{
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['plug_created_dt', 'plug_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['plug_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['plug_line_id', 'plug_ug_id'], 'unique', 'targetAttribute' => ['plug_line_id', 'plug_ug_id']],

            ['plug_created_dt', 'safe'],

            ['plug_line_id', 'required'],
            ['plug_line_id', 'integer'],
            ['plug_line_id', 'exist', 'skipOnError' => true, 'targetClass' => PhoneLine::class, 'targetAttribute' => ['plug_line_id' => 'line_id']],

            ['plug_ug_id', 'required'],
            ['plug_ug_id', 'integer'],
            ['plug_ug_id', 'exist', 'skipOnError' => true, 'targetClass' => UserGroup::class, 'targetAttribute' => ['plug_ug_id' => 'ug_id']],

            ['plug_updated_dt', 'safe'],
        ];
    }

    public function getPlugLine(): \yii\db\ActiveQuery
    {
        return $this->hasOne(PhoneLine::class, ['line_id' => 'plug_line_id']);
    }

    public function getPlugUg(): \yii\db\ActiveQuery
    {
        return $this->hasOne(UserGroup::class, ['ug_id' => 'plug_ug_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'plug_line_id' => 'Line ID',
            'plug_ug_id' => 'Ug ID',
            'plug_created_dt' => 'Created Dt',
            'plug_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'phone_line_user_group';
    }
}
