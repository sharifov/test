<?php

namespace modules\objectTask\src\entities;

use common\models\Employee;
use src\entities\EventTrait;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;

/**
 * This is the model class for table "object_task_status_log".
 *
 * @property int $otsl_id
 * @property string|null $otsl_ot_uuid
 * @property int|null $otsl_old_status
 * @property int|null $otsl_new_status
 * @property string|null $otsl_description
 * @property int|null $otsl_created_user_id
 * @property string|null $otsl_created_dt
 *
 * @property Employee $otslCreatedUser
 * @property ObjectTask $otslOtUu
 */
class ObjectTaskStatusLog extends \yii\db\ActiveRecord
{
    use EventTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'object_task_status_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['otsl_old_status', 'otsl_new_status', 'otsl_created_user_id'], 'integer'],
            [['otsl_created_dt'], 'date', 'format' => 'php:Y-m-d'],
            [['otsl_ot_uuid', 'otsl_description'], 'string', 'max' => 255],
            [['otsl_created_user_id'], 'exist', 'skipOnError' => true, 'skipOnEmpty' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['otsl_created_user_id' => 'id']],
            [['otsl_ot_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => ObjectTask::class, 'targetAttribute' => ['otsl_ot_uuid' => 'ot_uuid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'otsl_id' => 'ID',
            'otsl_ot_uuid' => 'Object Task uuid',
            'otsl_old_status' => 'Old Status',
            'otsl_new_status' => 'New Status',
            'otsl_description' => 'Description',
            'otsl_created_user_id' => 'Created User',
            'otsl_created_dt' => 'Created DateTime',
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['otsl_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['otsl_created_user_id'],
                ],
                'defaultValue' => null,
            ],
        ];
    }

    /**
     * Gets query for [[OtslCreatedUser]].
     *
     * @return \yii\db\ActiveQuery|Employee
     */
    public function getOtslCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'otsl_created_user_id']);
    }

    /**
     * Gets query for [[OtslOtUu]].
     *
     * @return \yii\db\ActiveQuery|ObjectTaskScopes
     */
    public function getOtslOtUu()
    {
        return $this->hasOne(ObjectTask::class, ['ot_uuid' => 'otsl_ot_uuid']);
    }

    /**
     * {@inheritdoc}
     * @return ObjectTaskStatusLogScopes the active query used by this AR class.
     */
    public static function find()
    {
        return new ObjectTaskStatusLogScopes(get_called_class());
    }

    public static function create(
        string $objectTaskUuid,
        int $newStatus,
        ?int $oldStatus = null,
        ?string $description = null,
        ?int $employeeId = null
    ): self {
        $model = new self();
        $model->otsl_ot_uuid = $objectTaskUuid;
        $model->otsl_new_status = $newStatus;
        $model->otsl_old_status = $oldStatus;
        $model->otsl_description = $description;
        $model->otsl_created_user_id = $employeeId;

        return $model;
    }
}
