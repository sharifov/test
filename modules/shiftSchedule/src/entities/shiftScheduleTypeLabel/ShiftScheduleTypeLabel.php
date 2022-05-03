<?php

namespace modules\shiftSchedule\src\entities\shiftScheduleTypeLabel;

use common\models\Employee;
use frontend\helpers\JsonHelper;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\shiftScheduleTypeLabelAssign\ShiftScheduleTypeLabelAssign;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "shift_schedule_type_label".
 *
 * @property string $stl_key
 * @property string $stl_name
 * @property int $stl_enabled
 * @property string|null $stl_color
 * @property string|null $stl_icon_class
 * @property string|null $stl_params_json
 * @property int|null $stl_sort_order
 * @property string|null $stl_updated_dt
 * @property int|null $stl_updated_user_id
 *
 * @property ShiftScheduleTypeLabelAssign[] $shiftScheduleTypeLabelAssigns
 * @property Employee $stlUpdatedUser
 * @property ShiftScheduleType[] $tlaSsts
 */
class ShiftScheduleTypeLabel extends \yii\db\ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'shift_schedule_type_label';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['stl_key', 'stl_name'], 'required'],
            [['stl_enabled', 'stl_sort_order', 'stl_updated_user_id'], 'integer'],
            [['stl_params_json', 'stl_updated_dt'], 'safe'],
            [['stl_params_json'], 'filter', 'filter' => static function ($value) {
                return JsonHelper::decode($value);
            }],
            [['stl_key', 'stl_name'], 'string', 'max' => 100],
            [['stl_color'], 'string', 'max' => 20],
            [['stl_icon_class'], 'string', 'max' => 50],
            [['stl_key'], 'unique'],
            [['stl_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class,
                'targetAttribute' => ['stl_updated_user_id' => 'id']],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels()
    {
        return [
            'stl_key' => 'ID/Key',
            'stl_name' => 'Name',
            'stl_enabled' => 'Enabled',
            'stl_color' => 'Color',
            'stl_icon_class' => 'Icon Class',
            'stl_params_json' => 'Params Json',
            'stl_sort_order' => 'Sort Order',
            'stl_updated_dt' => 'Updated Dt',
            'stl_updated_user_id' => 'Updated User ID',
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['stl_updated_dt'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['stl_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'stl_updated_user_id',
                'updatedByAttribute' => 'stl_updated_user_id',
            ],
        ];
    }

    /**
     * Gets query for [[ShiftScheduleTypeLabelAssigns]].
     *
     * @return ActiveQuery
     */
    public function getShiftScheduleTypeLabelAssigns(): ActiveQuery
    {
        return $this->hasMany(ShiftScheduleTypeLabelAssign::class, ['tla_stl_key' => 'stl_key']);
    }

    /**
     * Gets query for [[StlUpdatedUser]].
     *
     * @return ActiveQuery
     */
    public function getStlUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'stl_updated_user_id']);
    }

    /**
     * Gets query for [[TlaSsts]].
     *
     * @return ActiveQuery
     */
    public function getTlaSsts(): ActiveQuery
    {
        return $this->hasMany(ShiftScheduleType::class, ['sst_id' => 'tla_sst_id'])
            ->viaTable('shift_schedule_type_label_assign', ['tla_stl_key' => 'stl_key']);
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
     * @param bool $enabled
     * @return array
     */
    public static function getList(?bool $enabled = null): array
    {
        $query = self::find()->orderBy(['stl_sort_order' => SORT_ASC]);
        if ($enabled !== null) {
            $query->andWhere(['stl_enabled' => true]);
        }
        $data = $query->asArray()->all();
        return ArrayHelper::map($data, 'stl_key', 'stl_name');
    }

    /**
     * @return string
     */
    public function getColorLabel(): string
    {
        return $this->stl_color ? Html::tag(
            'span',
            '&nbsp;&nbsp;&nbsp;',
            ['class' => 'label', 'style' => 'background-color: ' . $this->stl_color]
        ) : '-';
    }

    /**
     * @return string
     */
    public function getIconLabel(): string
    {
        return $this->stl_icon_class ? Html::tag(
            'i',
            '',
            ['class' => $this->stl_icon_class] // , 'style' => 'color: ' . $model->sst_color
        ) : '-';
    }
}
