<?php

namespace sales\entities\cases;

use common\models\Call;
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
 * @property int $cs_user_id
 * @property int $cs_lead_id
 * @property int $cs_call_id
 * @property int $cs_dep_id
 * @property int $cs_project_id
 * @property int $cs_client_id
 * @property string $cs_created_dt
 * @property string $cs_updated_dt
 * @property string $cs_gid
 * @property string $cs_last_action_dt
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
 */
class Cases extends ActiveRecord
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
        $case->pending('Created by incoming sms');
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
        $case->pending('Created by incoming sms');
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
        $case->pending('Created by incoming email');
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
        $case->pending('Created by incoming email');
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
        $case->pending('Created by call');
        return $case;
    }

    /**
     * @param int|null $projectId
     * @param string $category
     * @param string $clientId
     * @param int $depId
     * @param string|null $subject
     * @param string|null $description
     * @return Cases
     */
    public static function createByWeb(
        ?int $projectId,
        string $category,
        string $clientId,
        int $depId,
        ?string $subject,
        ?string $description
    ): self
    {
        $case = self::create();
        $case->cs_project_id = $projectId;
        $case->cs_category = $category;
        $case->cs_client_id = $clientId;
        $case->cs_dep_id = $depId;
        $case->cs_subject = $subject;
        $case->cs_description = $description;
        $case->pending('Created by web');
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
        if ($this->cs_lead_id) {
            throw new \DomainException('This Lead is already assigned to case');
        }
        $this->recordEvent(new CasesAssignLeadEvent($this, $this->cs_lead_id, $leadId));
        $this->cs_lead_id = $leadId;
    }

    /**
     * @param string|null $description
     */
    public function pending(?string $description = ''): void
    {
        CasesStatus::guard($this->cs_status, CasesStatus::STATUS_PENDING);
        $this->recordEvent(new CasesPendingStatusEvent($this, $this->cs_status, $this->cs_user_id, $description));
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
     * @param string|null $description
     */
    public function processing(int $userId, ?string $description = ''): void
    {
        CasesStatus::guard($this->cs_status, CasesStatus::STATUS_PROCESSING);
        if ($this->isProcessing() && $this->isOwner($userId)) {
            throw new \DomainException('Case is already processing to this user');
        }
        $this->recordEvent(new CasesProcessingStatusEvent($this, $this->cs_status, $userId, $this->cs_user_id, $description));
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
     * @param string|null $description
     */
    public function followUp(?string $description = ''): void
    {
        CasesStatus::guard($this->cs_status, CasesStatus::STATUS_FOLLOW_UP);
        $this->recordEvent(new CasesFollowUpStatusEvent($this, $this->cs_status, $this->cs_user_id, $description));
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
     * @param string|null $description
     */
    public function solved(?string $description = ''): void
    {
        CasesStatus::guard($this->cs_status, CasesStatus::STATUS_SOLVED);
        $this->recordEvent(new CasesSolvedStatusEvent($this, $this->cs_status, $this->cs_user_id, $description));
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
     * @param string|null $description
     */
    public function trash(?string $description = ''): void
    {
        CasesStatus::guard($this->cs_status, CasesStatus::STATUS_TRASH);
        $this->recordEvent(new CasesTrashStatusEvent($this, $this->cs_status, $this->cs_user_id, $description));
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
        if (!array_key_exists($status, CasesStatus::STATUS_LIST)) {
            throw new \InvalidArgumentException('Invalid Status');
        }
        if ($this->cs_status !== $status) {
            /** prob. for logs */
            $this->recordEvent(new CasesStatusChangeEvent($this, $status, $this->cs_status, $this->cs_user_id));
        }
        $this->cs_status = $status;
    }

    /**
     * @param string $category
     */
    public function updateCategory(string $category): void
    {
        $this->cs_category = $category;
    }

    /**
     * @param string|null $subject
     */
    public function updateSubject(?string $subject): void
    {
        $this->cs_subject = $subject;
    }

    /**
     * @param string|null $description
     */
    public function updateDescription(?string $description): void
    {
        $this->cs_description = $description;
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
}
