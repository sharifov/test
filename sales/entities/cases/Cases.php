<?php

namespace sales\entities\cases;

use common\models\Call;
use common\models\Employee;
use common\models\Lead;
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
 * @property int $cs_depart_id
 * @property string $cs_created_dt
 */
class Cases extends ActiveRecord
{

    use EventTrait;

    public const STATUS_PENDING     = 1;
    public const STATUS_PROCESSING  = 2;
    public const STATUS_FOLLOW_UP   = 5;
    public const STATUS_SOLVED      = 10;
    public const STATUS_TRASH       = 11;

    public const STATUS_LIST = [
        self::STATUS_PENDING        => 'Pending',
        self::STATUS_PROCESSING     => 'Processing',
        self::STATUS_FOLLOW_UP      => 'Follow Up',
        self::STATUS_SOLVED         => 'Solved',
        self::STATUS_TRASH          => 'Trash',
    ];

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
        $this->recordEvent(new CasesProcessingStatusEvent($this, $this->cs_status, $this->cs_user_id, $userId));
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
    private function isOwner(?int $userId): bool
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
        if (!array_key_exists($status, self::STATUS_LIST)) {
            throw new \InvalidArgumentException('Invalid Status');
        }
        if ($this->cs_status !== $status) {
            /** prob. for logs */
            $this->recordEvent(new CasesStatusChangeEvent($this, $this->cs_status, $status, $this->cs_user_id));
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
     * @return array
     */
    public function rules(): array
    {
        return [
            [['cs_subject', 'cs_category', 'cs_status'], 'required'],
            [['cs_description'], 'string'],
            [['cs_category', 'cs_status', 'cs_user_id', 'cs_lead_id', 'cs_call_id', 'cs_depart_id'], 'integer'],
            [['cs_created_dt'], 'safe'],
            [['cs_subject'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'cs_id' => 'Cs ID',
            'cs_subject' => 'Cs Subject',
            'cs_description' => 'Cs Description',
            'cs_category' => 'Cs Category',
            'cs_status' => 'Cs Status',
            'cs_user_id' => 'Cs User ID',
            'cs_lead_id' => 'Cs Lead ID',
            'cs_call_id' => 'Cs Call ID',
            'cs_depart_id' => 'Cs Depart ID',
            'cs_created_dt' => 'Cs Created Dt',
        ];
    }

    /**
     * @return CasesQuery
     */
    public static function find(): CasesQuery
    {
        return new CasesQuery(get_called_class());
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%cases}}';
    }
}
