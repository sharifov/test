<?php

namespace modules\objectTask\src\entities;

use common\models\Lead;
use common\models\Queue;
use src\entities\EventTrait;
use Yii;

/**
 * This is the model class for table "object_task".
 *
 * @property string $ot_uuid
 * @property int|null $ot_q_id
 * @property string $ot_object
 * @property int $ot_object_id
 * @property string $ot_execution_dt
 * @property string $ot_command
 * @property integer $ot_ots_id
 * @property string $ot_group_hash
 * @property int $ot_status
 * @property string|null $ot_created_dt
 *
 * @property Queue $otQ
 * @property Lead $lead
 * @property ObjectTaskScenario $objectTaskScenario
 */
class ObjectTask extends \yii\db\ActiveRecord
{
    use EventTrait;

    public const STATUS_PENDING = 1;
    public const STATUS_IN_PROGRESS = 2;
    public const STATUS_DONE = 3;
    public const STATUS_CANCELED = 4;
    public const STATUS_FAILED = 5;

    public const STATUS_LIST = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_IN_PROGRESS => 'In progress',
        self::STATUS_DONE => 'Done',
        self::STATUS_CANCELED => 'Canceled',
        self::STATUS_FAILED => 'Failed',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'object_task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['ot_uuid', 'ot_object', 'ot_object_id', 'ot_execution_dt', 'ot_command', 'ot_status', 'ot_group_hash', 'ot_ots_id'], 'required'],
            [['ot_q_id', 'ot_object_id', 'ot_status', 'ot_ots_id'], 'integer'],
            [['ot_execution_dt', 'ot_created_dt'], 'safe'],
            [['ot_uuid', 'ot_object', 'ot_command', 'ot_group_hash'], 'string', 'max' => 255],
            [['ot_uuid'], 'unique'],
            [['ot_q_id'], 'exist', 'skipOnError' => true, 'skipOnEmpty' => true, 'targetClass' => Queue::class, 'targetAttribute' => ['ot_q_id' => 'id']],
            [['ot_q_id'], 'exist', 'skipOnError' => false, 'targetClass' => ObjectTaskScenario::class, 'targetAttribute' => ['ot_ots_id' => 'ots_id']],
        ];
    }

    public function beforeSave($insert): bool
    {
        if ($insert === false) {
            if (!empty($this->ot_q_id)) {
                $oldStatus = $this->oldAttributes['ot_status'] ?? null;

                if ($oldStatus !== $this->ot_status) {
                    Yii::$app->queue_db->remove(
                        $this->ot_q_id
                    );
                    $this->ot_q_id = null;
                }
            }
        }

        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'ot_uuid' => 'Uuid',
            'ot_q_id' => 'Queue ID',
            'ot_object' => 'Object',
            'ot_object_id' => 'Object ID',
            'ot_execution_dt' => 'Execution Dt',
            'ot_command' => 'Command',
            'ot_group_hash' => 'Group Hash',
            'ot_status' => 'Status',
            'ot_created_dt' => 'Created Dt',
        ];
    }

    /**
     * Gets query for [[OtQ]].
     *
     * @return \yii\db\ActiveQuery|Queue
     */
    public function getOtQ()
    {
        return $this->hasOne(Queue::class, ['id' => 'ot_q_id']);
    }

    public function getObjectTaskScenario()
    {
        return $this->hasOne(ObjectTaskScenario::class, ['ots_id' => 'ot_ots_id']);
    }

    /**
     * @return \yii\db\ActiveQuery|Lead
     */
    public function getLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'ot_object_id']);
    }

    /**
     * {@inheritdoc}
     * @return ObjectTaskScopes the active query used by this AR class.
     */
    public static function find()
    {
        return new ObjectTaskScopes(get_called_class());
    }

    public static function create(
        string $uuid,
        int $queueID,
        int $scenarioID,
        string $object,
        int $objectID,
        string $command,
        string $executionDt,
        string $groupHash,
        int $status = self::STATUS_PENDING
    ): self {
        $model = new self();
        $model->ot_uuid = $uuid;
        $model->ot_q_id = $queueID;
        $model->ot_ots_id = $scenarioID;
        $model->ot_object = $object;
        $model->ot_object_id = $objectID;
        $model->ot_command = $command;
        $model->ot_status = $status;
        $model->ot_group_hash = $groupHash;
        $model->ot_execution_dt = $executionDt;

        return $model;
    }

    public function setPendingStatus(): void
    {
        $this->ot_status = self::STATUS_PENDING;
    }

    public function setInProgressStatus(): void
    {
        $this->ot_status = self::STATUS_IN_PROGRESS;
    }

    public function setDoneStatus(): void
    {
        $this->ot_status = self::STATUS_DONE;
    }

    public function setCanceledStatus(): void
    {
        $this->ot_status = self::STATUS_CANCELED;
    }

    public function setFailedStatus(): void
    {
        $this->ot_status = self::STATUS_FAILED;
    }

    public static function getStatusList(array $excludeStatusList = []): array
    {
        $statusList = self::STATUS_LIST;

        foreach ($excludeStatusList as $excludeStatus) {
            if (isset($statusList[$excludeStatus])) {
                unset($statusList[$excludeStatus]);
            }
        }

        return $statusList;
    }
}
