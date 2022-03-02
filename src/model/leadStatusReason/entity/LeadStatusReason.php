<?php

namespace src\model\leadStatusReason\entity;

use common\models\Employee;
use common\models\query\EmployeeQuery;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "lead_status_reason".
 *
 * @property int $lsr_id
 * @property string|null $lsr_key
 * @property string|null $lsr_name
 * @property string|null $lsr_description
 * @property int|null $lsr_enabled
 * @property int|null $lsr_comment_required
 * @property string|null $lsr_params
 * @property int|null $lsr_created_user_id
 * @property int|null $lsr_updated_user_id
 * @property string|null $lsr_created_dt
 * @property string|null $lsr_updated_dt
 *
 * @property Employee $lsrCreatedUser
 * @property Employee $lsrUpdatedUser
 */
class LeadStatusReason extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lead_status_reason';
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['lsr_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['lsr_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['lsr_created_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['lsr_updated_user_id'],
                ]
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lsr_enabled', 'lsr_comment_required', 'lsr_created_user_id', 'lsr_updated_user_id'], 'integer'],
            [['lsr_params', 'lsr_created_dt', 'lsr_updated_dt'], 'safe'],
            [['lsr_key'], 'string', 'max' => 30],
            [['lsr_name'], 'string', 'max' => 50],
            [['lsr_description'], 'string', 'max' => 255],
            [['lsr_key'], 'unique'],
            [['lsr_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['lsr_created_user_id' => 'id']],
            [['lsr_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['lsr_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lsr_id' => 'Lsr ID',
            'lsr_key' => 'Lsr Key',
            'lsr_name' => 'Lsr Name',
            'lsr_description' => 'Lsr Description',
            'lsr_enabled' => 'Lsr Enabled',
            'lsr_comment_required' => 'Lsr Comment Required',
            'lsr_params' => 'Lsr Params',
            'lsr_created_user_id' => 'Lsr Created User ID',
            'lsr_updated_user_id' => 'Lsr Updated User ID',
            'lsr_created_dt' => 'Lsr Created Dt',
            'lsr_updated_dt' => 'Lsr Updated Dt',
        ];
    }

    /**
     * Gets query for [[LsrCreatedUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getLsrCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'lsr_created_user_id']);
    }

    /**
     * Gets query for [[LsrUpdatedUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getLsrUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'lsr_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return Scopes the active query used by this AR class.
     */
    public static function find()
    {
        return new Scopes(get_called_class());
    }
}
