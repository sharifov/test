<?php

namespace sales\forms\leadflow;

use common\models\Employee;
use common\models\Lead;
use sales\access\ListsAccess;
use yii\base\Model;

/**
 * Class ProcessingReasonForm
 *
 * @property int $leadId
 * @property int $leadGid
 * @property int $originId
 * @property string $reason
 * @property string $other
 * @property string $description
 * @property int $userId
 */
class ProcessingReasonForm extends Model
{

    public const REASON_NA = 'N/A';
    public const REASON_NO_AVAILABLE = 'No Available';
    public const REASON_VOICE_MAIL_SEND = 'Voice Mail Send';
    public const REASON_WILL_CALL_BACK = 'Will call back';
    public const REASON_WAITING_THE_OPTION = 'Waiting the option';
    public const REASON_OTHER = 'Other';

    public const REASON_LIST = [
        self::REASON_NA => self::REASON_NA,
        self::REASON_NO_AVAILABLE => self::REASON_NO_AVAILABLE,
        self::REASON_VOICE_MAIL_SEND => self::REASON_VOICE_MAIL_SEND,
        self::REASON_WILL_CALL_BACK => self::REASON_WILL_CALL_BACK,
        self::REASON_WAITING_THE_OPTION => self::REASON_WAITING_THE_OPTION,
        self::REASON_OTHER => self::REASON_OTHER,
    ];

    public $leadId;
    public $leadGid;
    public $reason;
    public $other;
    public $description;
    public $userId;

    /**
     * @param Lead $lead
     * @param array $config
     */
    public function __construct(Lead $lead, $config = [])
    {
        parent::__construct($config);
        $this->leadId = $lead->id;
        $this->leadGid = $lead->gid;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['leadId', 'required'],
            ['leadId', 'integer'],
            ['leadId', 'actualLeadValidate'],

            ['reason', 'required'],
            ['reason', 'in', 'range' => array_keys(self::REASON_LIST)],

            ['other', 'required', 'when' => function () {
                return $this->reason === self::REASON_OTHER;
            }],
            ['other', 'filter', 'filter' => 'trim', 'skipOnArray' => true],
            ['other', 'string'],

            ['userId', 'required'],
            ['userId', 'integer'],
            ['userId', 'exist', 'targetClass' => Employee::class, 'targetAttribute' => ['userId' => 'id']],
            ['userId', function () {
                $userIds = new ListsAccess();
                if (!$userIds || !array_key_exists($this->userId, $userIds->getEmployees())) {
                    $this->addError('userId', 'Cant access to this user');
                }
            }],
            ['userId', 'filter', 'filter' => 'intval'],
        ];
    }

    public function actualLeadValidate(): void
    {
        if ($lead = Lead::find()->select(['id', 'gid'])->andWhere(['id' => $this->leadId])->asArray()->one()) {
            if ($lead['gid'] !== $this->leadGid) {
                $this->addError('leadId', 'Different leadGid');
            }
            return;
        }
        $this->addError('leadId', 'Not found Lead: ' . $this->leadId);
    }

    /**
     * @return bool
     */
    public function isOtherReason(): bool
    {
        return $this->reason === self::REASON_OTHER;
    }

    /**
     * @param null $attributeNames
     * @param bool $clearErrors
     * @return bool
     */
    public function validate($attributeNames = null, $clearErrors = true): bool
    {
        if (parent::validate($attributeNames, $clearErrors)) {
            if ($this->isOtherReason()) {
                $this->description = $this->other;
            }  else {
                $this->description = self::REASON_LIST[$this->reason];
            }
            return true;
        }
        return false;
    }

}
