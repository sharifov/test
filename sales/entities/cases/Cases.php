<?php

namespace sales\entities\cases;

use common\models\Call;
use common\models\CaseSale;
use common\models\Client;
use common\models\Department;
use common\models\DepartmentEmailProject;
use common\models\DepartmentPhoneProject;
use common\models\Employee;
use common\models\Lead;
use common\models\Project;
use sales\entities\cases\events\CasesAssignLeadEvent;
use sales\entities\cases\events\CasesCreatedEvent;
use sales\entities\cases\events\CasesFollowUpStatusEvent;
use sales\entities\cases\events\CasesOwnerChangeEvent;
use sales\entities\cases\events\CasesOwnerFreedEvent;
use sales\entities\cases\events\CasesPendingStatusEvent;
use sales\entities\cases\events\CasesProcessingStatusEvent;
use sales\entities\cases\events\CasesSolvedStatusEvent;
use sales\entities\cases\events\CasesStatusChangeEvent;
use sales\entities\cases\events\CasesTrashStatusEvent;
use sales\entities\EventTrait;
use sales\interfaces\Objectable;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use Yii;

/**
 * Class Cases
 *
 * @property int $cs_id
 * @property string $cs_subject
 * @property string $cs_description
 * @property string $cs_category
 * @property int $cs_status
 * @property int|null $cs_user_id
 * @property int $cs_lead_id
 * @property int $cs_call_id
 * @property int $cs_dep_id
 * @property int $cs_project_id
 * @property int $cs_client_id
 * @property string $cs_created_dt
 * @property string $cs_updated_dt
 * @property string $cs_gid
 * @property string $cs_last_action_dt
 * @property int|null $cs_source_type_id
 * @property string|null $cs_deadline_dt
 * @property bool $cs_need_action
 * @property string|null $cs_order_uid
 *
 * @property CasesCategory $category
 * @property Department $department
 * @property Lead $lead
 * @property Call $call
 * @property Employee $owner
 * @property CasesStatusLog $lastLogRecord
 * @property Client $client
 * @property Project $project
 * @property CasesStatusLog[] $casesStatusLogs
 * @property DepartmentPhoneProject[] $departmentPhonesByProjectAndDepartment
 * @property CaseSale[] $caseSale
 */
class Cases extends ActiveRecord implements Objectable
{

    use EventTrait;

    /**
     * @return static
     */
    private static function create(): self
    {
        $case = new static();
        $case->cs_gid = self::generateGid();
        $case->cs_created_dt = date('Y-m-d H:i:s');
        $case->recordEvent(new CasesCreatedEvent($case));
        return $case;
    }

    /**
     * @param int $clientId
     * @param int|null $projectId
     * @return static
     */
    public static function createExchangeByIncomingSms(int $clientId, ?int $projectId): self
    {
        $case = self::create();
        $case->cs_client_id = $clientId;
        $case->cs_project_id = $projectId;
        $case->cs_dep_id = Department::DEPARTMENT_EXCHANGE;
        $case->cs_source_type_id = CasesSourceType::SMS;
        $case->pending(null, 'Created by incoming sms');
        return $case;
    }

    /**
     * @param int $clientId
     * @param int|null $projectId
     * @return static
     */
    public static function createSupportByIncomingSms(int $clientId, ?int $projectId): self
    {
        $case = self::create();
        $case->cs_client_id = $clientId;
        $case->cs_project_id = $projectId;
        $case->cs_dep_id = Department::DEPARTMENT_SUPPORT;
        $case->cs_source_type_id = CasesSourceType::SMS;
        $case->pending(null, 'Created by incoming sms');
        return $case;
    }

    /**
     * @param int $clientId
     * @param int|null $projectId
     * @return static
     */
    public static function createSupportByIncomingEmail(int $clientId, ?int $projectId): self
    {
        $case = self::create();
        $case->cs_client_id = $clientId;
        $case->cs_project_id = $projectId;
        $case->cs_dep_id = Department::DEPARTMENT_SUPPORT;
        $case->cs_source_type_id = CasesSourceType::MAIL;
        $case->pending(null, 'Created by incoming email');
        return $case;
    }

    /**
     * @param int $clientId
     * @param int|null $projectId
     * @return static
     */
    public static function createExchangeByIncomingEmail(int $clientId, ?int $projectId): self
    {
        $case = self::create();
        $case->cs_client_id = $clientId;
        $case->cs_project_id = $projectId;
        $case->cs_dep_id = Department::DEPARTMENT_EXCHANGE;
        $case->cs_source_type_id = CasesSourceType::MAIL;
        $case->pending(null, 'Created by incoming email');
        return $case;
    }

    /**
     * @param int $clientId
     * @param int $callId
     * @param int|null $projectId
     * @param int|null $depId
     * @return Cases
     */
    public static function createByCall(int $clientId, int $callId, ?int $projectId, ?int $depId): self
    {
        $case = self::create();
        $case->cs_client_id = $clientId;
        $case->cs_call_id = $callId;
        $case->cs_project_id = $projectId;
        $case->cs_dep_id = $depId;
        $case->cs_source_type_id = CasesSourceType::CALL;
        $case->pending(null, 'Created by call');
        return $case;
    }

    /**
     * @param int|null $projectId
     * @param string $category
     * @param string $clientId
     * @param int $depId
     * @param string|null $subject
     * @param string|null $description
     * @param int|null $creatorId
     * @param int|null $sourceTypeId
     * @param string|null $orderUid
     * @return Cases
     */
    public static function createByWeb(
        ?int $projectId,
        string $category,
        string $clientId,
        int $depId,
        ?string $subject,
        ?string $description,
        ?int $creatorId,
        ?int $sourceTypeId,
        ?string $orderUid
    ): self
    {
        $case = self::create();
        $case->cs_project_id = $projectId;
        $case->cs_category = $category;
        $case->cs_client_id = $clientId;
        $case->cs_dep_id = $depId;
        $case->cs_subject = $subject;
        $case->cs_description = $description;
        $case->cs_source_type_id = $sourceTypeId;
        $case->cs_order_uid = $orderUid;
        $case->pending($creatorId, 'Created by web');
        return $case;
    }

    public static function createByApi(
        int $clientId,
        int $projectId,
        int $departmentId,
        ?string $orderUid,
        ?string $subject,
        ?string $description,
        string $category
    ): self
    {
        $case = self::create();
        $case->cs_client_id = $clientId;
        $case->cs_project_id = $projectId;
        $case->cs_dep_id = $departmentId;
        $case->cs_order_uid = $orderUid;
        $case->cs_subject = $subject;
        $case->cs_description = $description;
        $case->cs_category = $category;
        $case->cs_source_type_id = CasesSourceType::API;
        $case->pending(null, 'Created by api');
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
     * @param int $leadId
     */
    public function assignLead(int $leadId): void
    {
        if ($this->cs_lead_id === $leadId) {
            throw new \DomainException('This Lead is already assigned to case');
        }
        $this->recordEvent(new CasesAssignLeadEvent($this, $this->cs_lead_id, $leadId));
        $this->cs_lead_id = $leadId;
    }

    /**
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function pending(?int $creatorId, ?string $description = ''): void
    {
        CasesStatus::guard($this->cs_status, CasesStatus::STATUS_PENDING);
        $this->recordEvent(new CasesPendingStatusEvent($this, $this->cs_status, $this->cs_user_id, $creatorId, $description));
        $this->setStatus(CasesStatus::STATUS_PENDING);
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->cs_status === CasesStatus::STATUS_PENDING;
    }

	/**
	 * @return bool
	 */
    public function isDepartmentSupport(): bool
	{
		return $this->cs_dep_id === Department::DEPARTMENT_SUPPORT;
	}

    /**
     * @param int $userId
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function processing(int $userId, ?int $creatorId, ?string $description = ''): void
    {
        CasesStatus::guard($this->cs_status, CasesStatus::STATUS_PROCESSING);
        if ($this->isProcessing() && $this->isOwner($userId)) {
            throw new \DomainException('Case is already processing to this user');
        }
        $this->recordEvent(new CasesProcessingStatusEvent($this, $this->cs_status, $userId, $this->cs_user_id, $creatorId, $description));
        if (!$this->isOwner($userId)) {
            $this->setOwner($userId);
        }
        if (!$this->isProcessing()) {
            $this->setStatus(CasesStatus::STATUS_PROCESSING);
        }
    }

    /**
     * @return bool
     */
    public function isProcessing(): bool
    {
        return $this->cs_status === CasesStatus::STATUS_PROCESSING;
    }

    /**
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function followUp(?int $creatorId, ?string $description = ''): void
    {
        CasesStatus::guard($this->cs_status, CasesStatus::STATUS_FOLLOW_UP);
        $this->recordEvent(new CasesFollowUpStatusEvent($this, $this->cs_status, $this->cs_user_id, $creatorId, $description));
        if (!$this->isFreedOwner()) {
            $this->freedOwner();
        }
        $this->setStatus(CasesStatus::STATUS_FOLLOW_UP);
    }

    /**
     * @return bool
     */
    public function isFollowUp(): bool
    {
        return $this->cs_status === CasesStatus::STATUS_FOLLOW_UP;
    }

    /**
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function solved(?int $creatorId, ?string $description = ''): void
    {
        CasesStatus::guard($this->cs_status, CasesStatus::STATUS_SOLVED);
        $this->recordEvent(new CasesSolvedStatusEvent($this, $this->cs_status, $this->cs_user_id, $creatorId, $description));
        $this->setStatus(CasesStatus::STATUS_SOLVED);
    }

    /**
     * @return bool
     */
    public function isSolved(): bool
    {
        return $this->cs_status === CasesStatus::STATUS_SOLVED;
    }

    /**
     * @param int|null $creatorId
     * @param string|null $description
     */
    public function trash(?int $creatorId, ?string $description = ''): void
    {
        CasesStatus::guard($this->cs_status, CasesStatus::STATUS_TRASH);
        $this->recordEvent(new CasesTrashStatusEvent($this, $this->cs_status, $this->cs_user_id, $creatorId, $description));
        $this->setStatus(CasesStatus::STATUS_TRASH);
    }

    /**
     * @return bool
     */
    public function isTrash(): bool
    {
        return $this->cs_status === CasesStatus::STATUS_TRASH;
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

    public function freedOwner(): void
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
    public function isFreedOwner(): bool
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
        if (!array_key_exists($status, CasesStatus::STATUS_LIST)) {
            throw new \InvalidArgumentException('Invalid Status');
        }
        if ($this->cs_status !== $status) {
            /** prob. for logs */
            $this->recordEvent(new CasesStatusChangeEvent($this, $status, $this->cs_status, $this->cs_user_id));
        }
        $this->cs_status = $status;
    }

    public function updateInfo(
        string $category,
        ?string $subject,
        ?string $description,
        ?string $orderUid
    ): void
    {
        $this->updateCategory($category);
        $this->cs_subject = $subject;
        $this->cs_description = $description;
        $this->cs_order_uid = $orderUid;
    }

    /**
     * @param string $category
     */
    public function updateCategory(string $category): void
    {
        $this->cs_category = $category;
    }

    public function setDeadline(string $deadline)
    {
        $this->cs_deadline_dt = $deadline;
    }

    public function onNeedAction(): void
    {
        if ($this->isNeedAction()) {
            throw new \DomainException('Case is already enabled need action.');
        }

        $this->cs_need_action = true;
    }

    public function offNeedAction(): void
    {
        if (!$this->isNeedAction()) {
            throw new \DomainException('Case is already mark checked.');
        }

        $this->cs_need_action = false;
    }

    public function isNeedAction(): bool
    {
        return $this->cs_need_action ? true : false;
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
    public function getCasesStatusLogs(): ActiveQuery
    {
        return $this->hasMany(CasesStatusLog::class, ['csl_case_id' => 'cs_id']);
    }

	/**
	 * @return ActiveQuery
	 */
    public function getDepartmentPhonesByProjectAndDepartment(): ActiveQuery
	{
		return $this->hasMany(DepartmentPhoneProject::class, ['dpp_project_id' => 'cs_project_id', 'dpp_dep_id' => 'cs_dep_id']);
	}

	/**
	 * @return ActiveQuery
	 */
    public function getDepartmentEmailsByProjectAndDepartment(): ActiveQuery
	{
		return $this->hasMany(DepartmentEmailProject::class, ['dep_project_id' => 'cs_project_id', 'dep_dep_id' => 'cs_dep_id']);
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
     * @return ActiveQuery
     */
    public function getCaseSale(): ActiveQuery
    {
        return $this->hasMany(CaseSale::class, ['css_cs_id' => 'cs_id']);
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
            'cs_last_action_dt' => 'Last Action',
            'cs_source_type_id' => 'Source type',
            'cs_deadline_dt' => 'Deadline',
            'cs_need_action' => 'Need Action',
            'cs_order_uid' => 'Booking ID ',
        ];
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%cases}}';
    }

    /**
     * Returns current client Time in 24-hour format;
     * If c_offset_gmt field is null then returns "-";
     *
     * @return string
     * @throws \Exception
     */
    public function getClientTime(): string
    {
        $clientTime = '-';
        $offset = $this->call->c_offset_gmt ?? false;

        if ($offset) {
            if(is_numeric($offset) && $offset > 0) {
                $offset = '+' . $offset;
            }

            $timezoneName = timezone_name_from_abbr('',intval($offset) * 3600, true);

            $dt = new \DateTime();
            if($timezoneName) {
                $timezone = new \DateTimeZone($timezoneName);
                $dt->setTimezone($timezone);
            }
            $clientTime =  $dt->format('H:i');

            $clientTime = '<b title="TZ ('.$offset.')"><i class="fa fa-clock-o '.($this->call->c_offset_gmt ? 'success': '').'"></i> ' . Html::encode($clientTime) . '</b>';
        }

        return $clientTime;
    }

    /**
     * @return CasesQuery
     */
    public static function find(): CasesQuery
    {
        return new CasesQuery(get_called_class());
    }

    /**
     * @return int
     */
    public function updateLastAction(): int
    {
        return self::updateAll(['cs_last_action_dt' => date('Y-m-d H:i:s')], ['cs_id' => $this->cs_id]);
    }

    public function getProjectId(): ?int
    {
        return $this->cs_project_id;
    }

    public function getDepartmentId(): ?int
    {
        return $this->cs_dep_id;
    }
}
