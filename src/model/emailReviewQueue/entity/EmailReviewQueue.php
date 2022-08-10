<?php

namespace src\model\emailReviewQueue\entity;

use common\models\Department;
use common\models\Email;
use common\models\Employee;
use common\models\Project;
use common\models\query\DepartmentQuery;
use common\models\query\EmailQuery;
use common\models\query\EmployeeQuery;
use common\models\query\ProjectQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use src\entities\email\Email as EmailNorm;
use common\models\Lead;
use src\entities\cases\Cases;

/**
 * This is the model class for table "email_review_queue".
 *
 * @property int $erq_id
 * @property int $erq_email_id
 * @property int|null $erq_project_id
 * @property int|null $erq_department_id
 * @property int|null $erq_owner_id
 * @property int|null $erq_status_id
 * @property int|null $erq_user_reviewer_id
 * @property string|null $erq_created_dt
 * @property string|null $erq_updated_dt
 * @property int|null $erq_email_is_norm
 *
 * @property Department $erqDepartment
 * @property Email $erqEmail
 * @property Email|EmailNorm $email
 * @property Employee $erqOwner
 * @property Project $erqProject
 * @property Employee $erqUserReviewer
 *
 * @property Cases $emailCase
 * @property Lead $emailLead
 * @property string $emailStatusName
 * @property string $emailTemplateName
 * @property string $emailSubject
 */
class EmailReviewQueue extends \yii\db\ActiveRecord
{
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['erq_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['erq_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'email_review_queue';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['erq_email_id'], 'required'],
            [['erq_email_id', 'erq_project_id', 'erq_department_id', 'erq_owner_id', 'erq_status_id', 'erq_user_reviewer_id'], 'integer'],
            [['erq_created_dt', 'erq_updated_dt'], 'safe'],
            ['erq_email_is_norm', 'integer'],
            [['erq_status_id'], 'in', 'range' => array_keys(EmailReviewQueueStatus::getList())],
            [['erq_department_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['erq_department_id' => 'dep_id']],
            [['erq_email_id'], 'exist', 'skipOnError' => true, 'targetRelation' => 'email'],
            [['erq_owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['erq_owner_id' => 'id']],
            [['erq_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['erq_project_id' => 'id']],
            [['erq_user_reviewer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['erq_user_reviewer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'erq_id' => 'ID',
            'erq_email_id' => 'Email',
            'erq_project_id' => 'Project',
            'erq_department_id' => 'Department',
            'erq_owner_id' => 'Owner',
            'erq_status_id' => 'Status',
            'erq_user_reviewer_id' => 'User Reviewer',
            'erq_created_dt' => 'Created Dt',
            'erq_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * Gets query for [[ErqDepartment]].
     *
     * @return \yii\db\ActiveQuery|DepartmentQuery
     */
    public function getErqDepartment()
    {
        return $this->hasOne(Department::class, ['dep_id' => 'erq_department_id']);
    }

    /**
     * Gets query for [[ErqEmail]].
     *
     * @return \yii\db\ActiveQuery|EmailQuery
     */
    public function getErqEmail()
    {
        return $this->hasOne(Email::class, ['e_id' => 'erq_email_id']);
    }

    /**
     *
     * @return \yii\db\ActiveQuery|EmailQuery
     */
    public function getEmail()
    {
        if ($this->erq_email_is_norm) {
            $emailLink = $this->hasOne(EmailNorm::class, ['e_id' => 'erq_email_id']);
        }

        return $emailLink ?? $this->hasOne(Email::class, ['e_id' => 'erq_email_id']);
    }

    public function getEmailSubject(): string
    {
        return $this->email->emailSubject ?? '--';
    }

    public function getEmailTemplateName(): string
    {
        return $this->email->templateTypeName ?? '--';
    }

    public function getEmailStatusName(): string
    {
        return $this->email->statusName ?? '--';
    }

    public function getEmailLead(): ?Lead
    {
        return $this->email->lead ?? null;
    }

    public function getEmailCase(): ?Cases
    {
        return $this->email->case  ?? null;
    }

    /**
     * Gets query for [[ErqOwner]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getErqOwner()
    {
        return $this->hasOne(Employee::class, ['id' => 'erq_owner_id']);
    }

    /**
     * Gets query for [[ErqProject]].
     *
     * @return \yii\db\ActiveQuery|ProjectQuery
     */
    public function getErqProject()
    {
        return $this->hasOne(Project::class, ['id' => 'erq_project_id']);
    }

    /**
     * Gets query for [[ErqUserReviewer]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getErqUserReviewer()
    {
        return $this->hasOne(Employee::class, ['id' => 'erq_user_reviewer_id']);
    }

    /**
     * {@inheritdoc}
     * @return Scopes the active query used by this AR class.
     */
    public static function find()
    {
        return new Scopes(get_called_class());
    }

    public function statusToReviewed(): void
    {
        $this->erq_status_id = EmailReviewQueueStatus::REVIEWED;
    }

    public function statusToReject(): void
    {
        $this->erq_status_id = EmailReviewQueueStatus::REJECTED;
    }

    public function statusToInProgress(): void
    {
        $this->erq_status_id = EmailReviewQueueStatus::IN_PROGRESS;
    }

    public function isInProgress(): bool
    {
        return $this->erq_status_id === EmailReviewQueueStatus::IN_PROGRESS;
    }

    public function isPending(): bool
    {
        return $this->erq_status_id === EmailReviewQueueStatus::PENDING;
    }

    public function canTake(int $userId): bool
    {
        return $this->isInProgress() && $this->erq_user_reviewer_id !== $userId;
    }

    public function canReview(int $userId): bool
    {
        return $this->isInProgress() && $this->erq_user_reviewer_id === $userId;
    }

    public function statusToPending(): void
    {
        $this->erq_status_id = EmailReviewQueueStatus::PENDING;
    }

    public static function create(
        int $emailId,
        ?int $projectId,
        ?int $departmentId,
        ?int $emailCreatorId,
        ?int $emailIsNorm
    ): self {
        $self = new self();
        $self->erq_email_id = $emailId;
        $self->erq_project_id = $projectId;
        $self->erq_department_id = $departmentId;
        $self->erq_owner_id = $emailCreatorId;
        $self->erq_email_is_norm = $emailIsNorm;
        return $self;
    }
}
