<?php

namespace modules\objectSegment\src\entities;

use common\models\Employee;
use common\models\Lead;
use modules\objectSegment\src\service\ObjectSegmentService;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 *  * This is the model class for table "object_segment_rule".
 *
 * @property int $osr_id
 * @property int $osr_osl_id
 * @property string osr_title
 * @property string osr_rule_condition
 * @property string $osr_rule_condition_json
 * @property bool $osr_enabled
 * @property string $osr_created_dt
 * @property string $osr_updated_dt
 * @property integer $osr_updated_user_id
 * @property ObjectSegmentList $osrObjectSegmentList
 * @property Employee $osrUpdatedUser
 */
class ObjectSegmentRule extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'object_segment_rules';
    }

    public function rules(): array
    {
        return [
            [['osr_osl_id', 'osr_title'], 'required'],
            [
                'osr_osl_id',
                'exist',
                'skipOnError'     => true,
                'targetClass'     => ObjectSegmentList::class,
                'targetAttribute' => ['osr_osl_id' => 'osl_id']
            ],
            [['osr_title'], 'string', 'max' => 100],
            [['osr_rule_condition_json'], 'string', 'max' => 1000],
            [['osr_rule_condition'], 'string', 'max' => 1000],
            ['osr_enabled', 'boolean'],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class'      => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['osr_created_dt', 'osr_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['osr_updated_dt'],
                ],
                'value'      => date('Y-m-d H:i:s')
            ],
            'attribute' => [
                'class'      => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['osr_updated_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['osr_updated_user_id'],
                ],
                'value'      => isset(Yii::$app->user) ? Yii::$app->user->id : null,
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($this->osr_rule_condition_json) {
            $this->osr_rule_condition = $this->getDecodeCode();
        }

        return true;
    }

    public function attributeLabels(): array
    {
        return [
            'osr_id'                  => 'ID',
            'osr_ost_id'              => 'Object Segment Type ID',
            'osl_title'               => 'Title',
            'osr_rule_condition'      => 'Rule condition',
            'osr_rule_condition_json' => 'Rule condition JSON',
            'osr_enabled'             => 'Enabled',
            'osr_updated_user_id'     => 'Updated User ID',
            'osr_created_dt'          => 'Created Dt',
            'osr_updated_dt'          => 'Updated Dt',
        ];
    }

    public function getOsrObjectSegmentList()
    {
        return $this->hasOne(ObjectSegmentList::class, ['osl_id' => 'osr_osl_id']);
    }

    public function getOsrUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'osr_updated_user_id']);
    }

    /**
     * @param bool $human
     * @return string
     */
    public function getDecodeCode(bool $human = false): string
    {
        $code = '';
        $rules = @json_decode($this->osr_rule_condition_json, true);
        if (is_array($rules)) {
            $code = ObjectSegmentService::conditionDecode($rules);

            if ($human) {
                $code = ObjectSegmentService::humanConditionCode($code);
            }
        }
        return $code;
    }
}
