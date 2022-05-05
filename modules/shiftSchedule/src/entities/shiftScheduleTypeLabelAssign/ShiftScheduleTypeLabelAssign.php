<?php

namespace modules\shiftSchedule\src\entities\shiftScheduleTypeLabelAssign;

use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\ShiftScheduleTypeLabel;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

/**
 * This is the model class for table "shift_schedule_type_label_assign".
 *
 * @property string $tla_stl_key
 * @property int $tla_sst_id
 * @property string|null $tla_created_dt
 *
 * @property ShiftScheduleType $tlaSst
 * @property ShiftScheduleTypeLabel $tlaStlKey
 */
class ShiftScheduleTypeLabelAssign extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'shift_schedule_type_label_assign';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['tla_stl_key', 'tla_sst_id'], 'required'],
            [['tla_sst_id'], 'integer'],
            [['tla_created_dt'], 'safe'],
            [['tla_stl_key'], 'string', 'max' => 100],
            [['tla_stl_key', 'tla_sst_id'], 'unique', 'targetAttribute' => ['tla_stl_key', 'tla_sst_id']],
            [['tla_stl_key'], 'exist', 'skipOnError' => true,
                'targetClass' => ShiftScheduleTypeLabel::class, 'targetAttribute' => ['tla_stl_key' => 'stl_key']],
            [['tla_sst_id'], 'exist', 'skipOnError' => true,
                'targetClass' => ShiftScheduleType::class, 'targetAttribute' => ['tla_sst_id' => 'sst_id']],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels()
    {
        return [
            'tla_stl_key' => 'Label Key',
            'tla_sst_id' => 'Shift Type ID',
            'tla_created_dt' => 'Created Dt',
        ];
    }

    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['tla_created_dt'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['tla_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ]
        ];
    }

    /**
     * Gets query for [[TlaSst]].
     *
     * @return ActiveQuery
     */
    public function getTlaSst(): ActiveQuery
    {
        return $this->hasOne(ShiftScheduleType::class, ['sst_id' => 'tla_sst_id']);
    }

    /**
     * Gets query for [[TlaStlKey]].
     *
     * @return ActiveQuery
     */
    public function getTlaStlKey(): ActiveQuery
    {
        return $this->hasOne(ShiftScheduleTypeLabel::class, ['stl_key' => 'tla_stl_key']);
    }

    /**
     * {@inheritdoc}
     * @return Scopes the active query used by this AR class.
     */
    public static function find(): Scopes
    {
        return new Scopes(get_called_class());
    }

    /**
     * @return string
     */
    public function getShiftTypeName(): string
    {
        return $this->tlaSst ? $this->tlaSst->sst_title : '-';
    }

    /**
     * @return string
     */
    public function getShiftTypeLabel(): string
    {
        return $this->tlaStlKey ? $this->tlaStlKey->stl_name : '-';
    }
}
