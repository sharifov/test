<?php

namespace sales\entities\cases;

use common\models\Employee;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class CaseStatusLog
 *
 * @property int $csl_id
 * @property int $csl_case_id
 * @property int $csl_from_status
 * @property int $csl_to_status
 * @property string $csl_start_dt
 * @property string $csl_end_dt
 * @property int $csl_time_duration
 * @property int $csl_created_user_id
 * @property int $csl_owner_id
 * @property string $csl_description
 *
 * @property Cases $cases
 * @property Employee $createdUser
 * @property Employee $owner
 */
class CaseStatusLog extends ActiveRecord
{

    /**
     * @param int $caseId
     * @param int $toStatus
     * @param int|null $fromStatus
     * @param int|null $creatorId
     * @param int|null $ownerId
     * @param string|null $description
     * @return CaseStatusLog
     */
    public static function create(
        int $caseId,
        int $toStatus,
        ?int $fromStatus,
        ?int $creatorId,
        ?int $ownerId,
        ?string $description = ''
    ): self
    {
        $status = new static();
        $status->csl_case_id = $caseId;
        $status->csl_to_status = $toStatus;
        $status->csl_from_status = $fromStatus;
        $status->csl_created_user_id = $creatorId;
        $status->csl_owner_id = $ownerId;
        $status->csl_description = $description;
        $status->csl_start_dt = date('Y-m-d H:i:s');
        return $status;
    }

    public function end(): void
    {
        $this->csl_end_dt = date('Y-m-d H:i:s');
        $this->csl_time_duration = (int) (strtotime($this->csl_end_dt) - strtotime($this->csl_start_dt));
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['csl_case_id', 'required'],
            ['csl_case_id', 'integer'],
            ['csl_case_id', 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['csl_case_id' => 'cs_id']],

            ['csl_to_status', 'required'],
            ['csl_to_status', 'integer'],

            ['csl_from_status', 'integer'],

            ['csl_start_dt', 'required'],
            ['csl_start_dt', 'string'],

            ['csl_end_dt', 'string'],

            ['csl_time_duration', 'integer'],

            ['csl_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['csl_created_user_id' => 'id']],

            ['csl_owner_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['csl_owner_id' => 'id']],

            ['csl_description', 'string']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'csl_id' => 'ID',
            'csl_case_id' => 'Case ID',
            'csl_from_status' => 'From Status',
            'csl_to_status' => 'To Status',
            'csl_start_dt' => 'Start Date',
            'csl_end_dt' => 'End Date',
            'csl_time_duration' => 'Duration',
            'csl_created_user_id' => 'Created',
            'csl_owner_id' => 'Employee',
            'csl_description' => 'Description',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCases(): ActiveQuery
    {
        return $this->hasOne(Cases::class, ['cs_id' => 'csl_case_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'csl_created_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOwner(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'csl_owner_id']);
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%case_status_log}}';
    }
}
