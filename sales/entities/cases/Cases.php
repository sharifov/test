<?php

namespace sales\entities\cases;

use common\models\Call;
use common\models\Client;
use common\models\Department;
use common\models\Employee;
use common\models\Lead;
use common\models\Project;
use sales\entities\cases\events\CasesAssignLeadEvent;
use sales\entities\cases\events\CasesFollowUpStatusEvent;
use sales\entities\cases\events\CasesOwnerChangeEvent;
use sales\entities\cases\events\CasesOwnerFreedEvent;
use sales\entities\cases\events\CasesPendingStatusEvent;
use sales\entities\cases\events\CasesProcessingStatusEvent;
use sales\entities\cases\events\CasesSolvedStatusEvent;
use sales\entities\cases\events\CasesStatusChangeEvent;
use sales\entities\cases\events\CasesTrashStatusEvent;
use sales\entities\EventTrait;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class Cases
 *
 * @property int $cs_id
 * @property string $cs_subject
 * @property string $cs_description
 * @property int $cs_category
 * @property int $cs_status
 * @property int $cs_user_id
 * @property int $cs_lead_id
 * @property int $cs_call_id
 * @property int $cs_dep_id
 * @property int $cs_project_id
 * @property int $cs_client_id
 * @property string $cs_created_dt
 * @property string $cs_updated_dt
 * @property string $cs_gid
 *
 * @property CasesCategory $category
 * @property Department $department
 * @property Lead $lead
 * @property Call $call
 * @property Employee $owner
 * @property CasesStatusLog $lastLogRecord
 * @property Client $client
 * @property Project $project
 */
class Cases extends ActiveRecord
{

    use EventTrait;

    public const STATUS_PENDING     = 1;
    public const STATUS_PROCESSING  = 2;
    public const STATUS_FOLLOW_UP   = 5;
    public const STATUS_SOLVED      = 10;
    public const STATUS_TRASH       = 11;

    public const STATUS_ROUTE_RULES = [
        self::STATUS_PENDING        => [self::STATUS_PROCESSING, self::STATUS_TRASH],
        self::STATUS_PROCESSING     => [self::STATUS_FOLLOW_UP, self::STATUS_TRASH, self::STATUS_SOLVED],
        self::STATUS_FOLLOW_UP      => [self::STATUS_TRASH], //self::STATUS_PROCESSING,
        self::STATUS_SOLVED         => [],
        self::STATUS_TRASH          => [self::STATUS_FOLLOW_UP],
    ];

    /**
     * @param int $clientId
     * @param int $callId
     * @param int $projectId
     * @param int|null $depId
     * @return Cases
     */
    public static function createByCall(int $clientId, int $callId, int $projectId, ?int $depId): self
    {
        $case = new static();
        $case->cs_client_id = $clientId;
        $case->cs_call_id = $callId;
        $case->cs_project_id = $projectId;
        $case->cs_dep_id = $depId;
        $case->cs_gid = self::generateGid();
        $case->cs_created_dt = date('Y-m-d H:i:s');
        $case->setStatus(self::STATUS_PENDING);
        return $case;
    }

    /**
     * @param int $projectId
     * @param string $category
     * @param string $clientId
     * @param int $depId
     * @param string|null $subject
     * @param string|null $description
     * @return Cases
     */
    public static function createByWeb(
        int $projectId,
        string $category,
        string $clientId,
        int $depId,
        ?string $subject,
        ?string $description
    ): self
    {
        $case = new static();
        $case->cs_project_id = $projectId;
        $case->cs_category = $category;
        $case->cs_client_id = $clientId;
        $case->cs_dep_id = $depId;
        $case->cs_subject = $subject;
        $case->cs_description = $description;
        $case->cs_gid = self::generateGid();
        $case->cs_created_dt = date('Y-m-d H:i:s');
        $case->setStatus(self::STATUS_PENDING);
        return $case;
    }

    /**
     * @return string
     */
    private static function generateGid(): string
    {
        return md5(uniqid('', true));
    }


    /**
     * @param int $status
     * @return array
     */
    public static function statusRouteRules(int $status): array
    {
        return self::STATUS_ROUTE_RULES[$status] ?? [];
    }

    /**
     * @return array
     */
    public function getStatusRouteRules(): array
    {
        return self::statusRouteRules($this->cs_status);
    }


    /**
     * @param int $leadId
     */
    public function assignLead(int $leadId): void
    {
        if ($this->cs_lead_id) {
            throw new \DomainException('This Lead is already assigned to case');
        }
        $this->recordEvent(new CasesAssignLeadEvent($this, $this->cs_lead_id, $leadId));
        $this->cs_lead_id = $leadId;
    }

    public function pending(): void
    {
        if ($this->isPending()) {
            throw new \DomainException('Case is already is pending');
        }
        $this->recordEvent(new CasesPendingStatusEvent($this, $this->cs_status, $this->cs_user_id));
        $this->setStatus(self::STATUS_PENDING);
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->cs_status === self::STATUS_PENDING;
    }

    /**
     * @param int $userId
     */
    public function processing(int $userId): void
    {
        if ($this->isProcessing() && $this->isOwner($userId)) {
            throw new \DomainException('Case is already processing to this user');
        }
        $this->recordEvent(new CasesProcessingStatusEvent($this, $this->cs_status, $userId, $this->cs_user_id));
        if (!$this->isOwner($userId)) {
            $this->setOwner($userId);
        }
        if (!$this->isProcessing()) {
            $this->setStatus(self::STATUS_PROCESSING);
        }
    }

    /**
     * @return bool
     */
    public function isProcessing(): bool
    {
        return $this->cs_status === self::STATUS_PROCESSING;
    }

    public function followUp(): void
    {
        if ($this->isFollowUp()) {
            throw new \DomainException('Case is already follow-up');
        }
        $this->recordEvent(new CasesFollowUpStatusEvent($this, $this->cs_status, $this->cs_user_id));
        if (!$this->isFreedOwner()) {
            $this->freedOwner();
        }
        $this->setStatus(self::STATUS_FOLLOW_UP);
    }

    /**
     * @return bool
     */
    public function isFollowUp(): bool
    {
        return $this->cs_status === self::STATUS_FOLLOW_UP;
    }

    public function solved(): void
    {
        if ($this->isSolved()) {
            throw new \DomainException('Case is already solved');
        }
        if (!$this->isProcessing()) {
            throw new \DomainException('Case must be in processing');
        }
        $this->recordEvent(new CasesSolvedStatusEvent($this, $this->cs_status, $this->cs_user_id));
        $this->setStatus(self::STATUS_SOLVED);
    }

    /**
     * @return bool
     */
    public function isSolved(): bool
    {
        return $this->cs_status === self::STATUS_SOLVED;
    }

    public function trash(): void
    {
        if ($this->isTrash()) {
            throw new \DomainException('Case is already trash');
        }
        $this->recordEvent(new CasesTrashStatusEvent($this, $this->cs_status, $this->cs_user_id));
        $this->setStatus(self::STATUS_TRASH);
    }

    /**
     * @return bool
     */
    public function isTrash(): bool
    {
        return $this->cs_status === self::STATUS_TRASH;
    }

    /**
     * @param int $userId
     */
    private function setOwner(int $userId): void
    {
        if ($this->isOwner($userId)) {
            throw new \DomainException('This user already is owner');
        }
        /** prob. for logs */
        $this->recordEvent(new CasesOwnerChangeEvent($this, $this->cs_user_id, $userId));
        $this->cs_user_id = $userId;
    }

    private function freedOwner(): void
    {
        if ($this->isFreedOwner()) {
            throw new \DomainException('Case is already freed owner');
        }
        $this->recordEvent(new CasesOwnerFreedEvent($this, $this->cs_user_id));
        $this->cs_user_id = null;
    }

    /**
     * @return bool
     */
    private function isFreedOwner(): bool
    {
        return $this->cs_user_id === null;
    }

    /**
     * @param int|null $userId
     * @return bool
     */
    public function isOwner(?int $userId): bool
    {
        if ($userId === null) {
            return false;
        }
        return $this->cs_user_id === $userId;
    }

    /**
     * @param int $status
     */
    private function setStatus(int $status): void
    {
        if (!array_key_exists($status, CasesStatusHelper::STATUS_LIST)) {
            throw new \InvalidArgumentException('Invalid Status');
        }
        if ($this->cs_status !== $status) {
            /** prob. for logs */
            $this->recordEvent(new CasesStatusChangeEvent($this, $status, $this->cs_status, $this->cs_user_id));
        }
        $this->cs_status = $status;
    }

    /**
     * @return ActiveQuery
     */
    public function getOwner(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cs_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCall(): ActiveQuery
    {
        return $this->hasOne(Call::class, ['c_id' => 'cs_call_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLead(): ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'cs_lead_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDepartment(): ActiveQuery
    {
        return $this->hasOne(Department::class, ['dep_id' => 'cs_dep_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(CasesCategory::class, ['cc_key' => 'cs_category']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLastLogRecord(): ActiveQuery
    {
        return $this->hasOne(CasesStatusLog::class, ['csl_case_id' => 'cs_id'])->orderBy(['csl_id' => SORT_DESC]);
    }

    /**
     * @return ActiveQuery
     */
    public function getClient(): ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'cs_client_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'cs_project_id']);
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'cs_id' => 'ID',
            'cs_gid' => 'GID',
            'cs_subject' => 'Subject',
            'cs_description' => 'Description',
            'cs_category' => 'Category',
            'cs_status' => 'Status',
            'cs_user_id' => 'User',
            'cs_lead_id' => 'Lead',
            'cs_call_id' => 'Call',
            'cs_dep_id' => 'Department',
            'cs_project_id' => 'Project',
            'cs_client_id' => 'Client',
            'cs_created_dt' => 'Created',
            'cs_updated_dt' => 'Updated',
        ];
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%cases}}';
    }
}
