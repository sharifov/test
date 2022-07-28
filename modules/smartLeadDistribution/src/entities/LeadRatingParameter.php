<?php

namespace modules\smartLeadDistribution\src\entities;

use common\models\Employee;
use src\access\ConditionExpressionService;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "lead_rating_parameter".
 *
 * @property int $lrp_id
 * @property string|null $lrp_object
 * @property string|null $lrp_attribute
 * @property int|null $lrp_point
 * @property string|null $lrp_condition
 * @property string|null $lrp_condition_json
 * @property string|null $lrp_created_dt
 * @property int|null $lrp_created_user_id
 * @property string|null $lrp_updated_dt
 * @property int|null $lrp_updated_user_id
 *
 * @property Employee $lrpCreatedUser
 * @property Employee $lrpUpdatedUser
 */
class LeadRatingParameter extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'lead_rating_parameter';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['lrp_point', 'lrp_created_user_id', 'lrp_updated_user_id'], 'integer'],
            [['lrp_condition_json', 'lrp_created_dt', 'lrp_updated_dt'], 'safe'],
            [['lrp_object', 'lrp_attribute', 'lrp_condition'], 'string', 'max' => 255],
            [['lrp_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['lrp_created_user_id' => 'id']],
            [['lrp_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['lrp_updated_user_id' => 'id']],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['lrp_created_dt'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['lrp_updated_dt']
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['lrp_created_user_id'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['lrp_updated_user_id']
                ],
                'defaultValue' => null,
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'lrp_id' => 'ID',
            'lrp_object' => 'Object',
            'lrp_attribute' => 'Attribute',
            'lrp_point' => 'Point',
            'lrp_condition' => 'Condition',
            'lrp_condition_json' => 'Condition Json',
            'lrp_created_dt' => 'Created Dt',
            'lrp_created_user_id' => 'Created User ID',
            'lrp_updated_dt' => 'Updated Dt',
            'lrp_updated_user_id' => 'Updated User ID',
        ];
    }


    public function getLrpCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'lrp_created_user_id']);
    }


    public function getLrpUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'lrp_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return LeadRatingParameterScopes the active query used by this AR class.
     */
    public static function find()
    {
        return new LeadRatingParameterScopes(get_called_class());
    }


    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->lrp_condition_json) {
            $this->lrp_condition = $this->getDecodeCode();
        }

        return true;
    }

    public function getDecodeCode(): string
    {
        $code = '';
        if (is_string($this->lrp_condition_json)) {
            $rules = Json::decode($this->lrp_condition_json);
        } else {
            $rules = $this->lrp_condition_json;
        }

        if (is_array($rules)) {
            $code = ConditionExpressionService::decode($rules);
        }

        return $code;
    }
}
