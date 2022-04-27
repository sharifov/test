<?php

namespace modules\shiftSchedule\src\entities\shiftScheduleType;

use common\models\Employee;
use modules\shiftSchedule\src\entities\shiftScheduleRule\ShiftScheduleRule;
use modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\ShiftScheduleTypeLabel;
use modules\shiftSchedule\src\entities\shiftScheduleTypeLabelAssign\ShiftScheduleTypeLabelAssign;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\BaseActiveRecord;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "shift_schedule_type".
 *
 * @property int $sst_id
 * @property string $sst_key
 * @property string $sst_name
 * @property string|null $sst_title
 * @property int $sst_enabled
 * @property int $sst_readonly
 * @property int $sst_work_time
 * @property string|null $sst_color
 * @property string|null $sst_icon_class
 * @property string|null $sst_css_class
 * @property string|null $sst_params_json
 * @property int|null $sst_sort_order
 * @property string|null $sst_updated_dt
 * @property int|null $sst_updated_user_id
 *
 * @property ShiftScheduleRule[] $shiftScheduleRules
 * @property ShiftScheduleTypeLabelAssign[] $shiftScheduleTypeLabelAssigns
 * @property Employee $sstUpdatedUser
 * @property ShiftScheduleTypeLabel[] $tlaStlKeys
 * @property UserShiftSchedule[] $userShiftSchedules
 */
class ShiftScheduleType extends \yii\db\ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'shift_schedule_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sst_key', 'sst_name'], 'required'],
            [['sst_enabled', 'sst_readonly', 'sst_work_time', 'sst_sort_order', 'sst_updated_user_id'], 'integer'],
            [['sst_params_json', 'sst_updated_dt'], 'safe'],
            [['sst_key', 'sst_name', 'sst_icon_class', 'sst_css_class'], 'string', 'max' => 100],
            [['sst_title'], 'string', 'max' => 255],
            [['sst_color'], 'string', 'max' => 20],
            [['sst_key'], 'unique'],
            [['sst_work_time', 'sst_readonly'], 'default', 'value' => true],
            [['sst_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class,
                'targetAttribute' => ['sst_updated_user_id' => 'id']],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'sst_id' => 'ID',
            'sst_key' => 'Key',
            'sst_name' => 'Name',
            'sst_title' => 'Title',
            'sst_enabled' => 'Enabled',
            'sst_readonly' => 'Readonly',
            'sst_work_time' => 'Work Time',
            'sst_color' => 'Color',
            'sst_icon_class' => 'Icon Class',
            'sst_css_class' => 'CSS Class',
            'sst_params_json' => 'Params Json',
            'sst_sort_order' => 'Sort Order',
            'sst_updated_dt' => 'Updated Dt',
            'sst_updated_user_id' => 'Updated User ID',
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['sst_updated_dt'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['sst_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'sst_updated_user_id',
                'updatedByAttribute' => 'sst_updated_user_id',
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getShiftScheduleRules(): ActiveQuery
    {
        return $this->hasMany(ShiftScheduleRule::class, ['ssr_sst_id' => 'sst_id']);
    }

    /**
     * Gets query for [[ShiftScheduleTypeLabelAssigns]].
     *
     * @return ActiveQuery
     */
    public function getShiftScheduleTypeLabelAssigns(): ActiveQuery
    {
        return $this->hasMany(ShiftScheduleTypeLabelAssign::class, ['tla_sst_id' => 'sst_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSstUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'sst_updated_user_id']);
    }

    /**
     * Gets query for [[TlaStlKeys]].
     *
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getTlaStlKeys(): ActiveQuery
    {
        return $this->hasMany(ShiftScheduleTypeLabel::class, ['stl_key' => 'tla_stl_key'])
            ->viaTable('shift_schedule_type_label_assign', ['tla_sst_id' => 'sst_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserShiftSchedules(): ActiveQuery
    {
        return $this->hasMany(UserShiftSchedule::class, ['uss_sst_id' => 'sst_id']);
    }

    /**
     * @return Scopes
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
        $query = self::find()->orderBy(['sst_sort_order' => SORT_ASC]);
        if ($enabled !== null) {
            $query->andWhere(['sst_enabled' => true]);
        }
        $data = $query->asArray()->all();
        return ArrayHelper::map($data, 'sst_id', 'sst_title');
    }

    /**
     * @return string
     */
    public function getColorLabel(): string
    {
        return $this->sst_color ? Html::tag(
            'span',
            '&nbsp;&nbsp;&nbsp;',
            ['class' => 'label', 'style' => 'background-color: ' . $this->sst_color]
        ) : '-';
    }

    /**
     * @return string
     */
    public function getIconLabel(): string
    {
        return $this->sst_icon_class ? Html::tag(
            'i',
            '',
            ['class' => $this->sst_icon_class] // , 'style' => 'color: ' . $model->sst_color
        ) : '-';
    }


    /**
     * @return array
     */
    public function getLabelList(): array
    {
        $labels = $this->tlaStlKeys;
        return $labels ? ArrayHelper::map($labels, 'stl_key', 'stl_name') : [];
    }

    /**
     * @param array|null $labelList
     * @return array
     * @throws StaleObjectException
     */
    public function updateLabelListAssign(?array $labelList = []): array
    {
        $list = [];
        if ($labelList) {
            foreach ($labelList as $key) {
                $exist = ShiftScheduleTypeLabelAssign::find()
                    ->where(['tla_stl_key' => $key, 'tla_sst_id' => $this->sst_id])->exists();
                if (!$exist) {
                    $tla = new ShiftScheduleTypeLabelAssign();
                    $tla->tla_sst_id = $this->sst_id;
                    $tla->tla_stl_key = $key;
                    if (!$tla->save()) {
                        \Yii::warning(
                            ['errors' => $tla->errors, 'data' => $tla->attributes],
                            'ShiftScheduleType:updateLabelListAssign:save'
                        );
                    }
                }
                $list[] = $key;
            }
        }

        $needRemove = ShiftScheduleTypeLabelAssign::find()
            ->where(['NOT IN', 'tla_stl_key', $list])
            ->andWhere(['tla_sst_id' => $this->sst_id])
            ->all();

        if ($needRemove) {
            foreach ($needRemove as $item) {
                $item->delete();
            }
        }

        return $list;
    }
}
