<?php

namespace sales\forms\leadflow;

use common\models\Employee;
use common\models\Lead;
use sales\access\ListsAccess;
use yii\base\Model;

/**
 * Class ReturnReasonForm
 *
 * @property int $leadId
 * @property int $leadGid
 * @property string $reason
 * @property string $other
 * @property string $description
 * @property string $return
 * @property int $userId
 */
class ReturnReasonForm extends Model
{

    public const REASON_OTHER = 'Other';

    public const REASON_LIST = [
        self::REASON_OTHER => self::REASON_OTHER,
    ];

    public const RETURN_FOLLOW_UP = 'Follow Up';
    public const RETURN_PROCESSING = 'Processing';

    public const RETURN_LIST = [
        self::RETURN_FOLLOW_UP => self::RETURN_FOLLOW_UP,
        self::RETURN_PROCESSING => self::RETURN_PROCESSING,
    ];

    public $leadId;
    public $leadGid;
    public $reason;
    public $other;
    public $description;
    public $return;
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
                return $this->isOtherReason();
            }],
            ['other', 'filter', 'filter' => 'trim', 'skipOnArray' => true],
            ['other', 'string'],

            ['return', 'required'],
            ['return', 'in', 'range' => array_keys(self::RETURN_LIST)],

            ['userId', 'required', 'when' => function () {
                return $this->isReturnToProcessing();
            }, 'message' => 'Agent cannot be blank'],
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
     * @return bool
     */
    public function isReturnToProcessing(): bool
    {
        return $this->return === self::RETURN_PROCESSING;
    }

    /**
     * @return bool
     */
    public function isReturnToFollowUp(): bool
    {
        return $this->return === self::RETURN_FOLLOW_UP;
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
            } else {
                $this->description = self::REASON_LIST[$this->reason];
            }
            return true;
        }
        return false;
    }

}
