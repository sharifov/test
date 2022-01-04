<?php

namespace sales\model\call\useCase\createCall;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\Client;
use common\models\Employee;
use common\models\Lead;
use common\models\PhoneBlacklist;
use common\models\UserProjectParams;
use sales\entities\cases\Cases;
use sales\model\callLog\entity\callLog\CallLog;
use sales\model\phoneList\entity\PhoneList;
use sales\model\voip\phoneDevice\device\ReadyVoipDevice;
use yii\base\Model;

/**
 * Class CreateCallForm
 *
 * @property $createdUserId
 * @property $phoneListId
 * @property $toUserId
 * @property $from
 * @property $to
 * @property $historyCallSid
 * @property $clientId
 * @property $leadId
 * @property $caseId
 * @property $fromCase
 * @property $fromLead
 * @property $fromContacts
 * @property $deviceId
 * @property $voipDevice
 */
class CreateCallForm extends Model
{
    private $createdUserId;
    private $phoneListId;
    private $voipDevice;

    public $toUserId;
    public $from;
    public $to;
    public $historyCallSid;
    public $clientId;
    public $leadId;
    public $caseId;
    public $fromCase;
    public $fromLead;
    public $fromContacts;
    public $deviceId;

    public function __construct(int $createdUserId, $config = [])
    {
        parent::__construct($config);
        $this->createdUserId = $createdUserId;
    }

    public function rules(): array
    {
        return [
            ['fromCase', 'boolean', 'trueValue' => true, 'falseValue' => false],

            ['fromLead', 'boolean', 'trueValue' => true, 'falseValue' => false],

            ['fromContacts', 'boolean', 'trueValue' => true, 'falseValue' => false],

            ['toUserId', 'default', 'value' => null],
            ['toUserId', 'integer'],
            ['toUserId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],
            ['toUserId', function ($attribute) {
                if ($this->{$attribute} === $this->createdUserId) {
                    $this->addError($attribute, 'User is invalid');
                }
            }, 'skipOnEmpty' => true, 'skipOnError' => true],
            ['toUserId', 'exist', 'targetClass' => Employee::class, 'targetAttribute' => ['toUserId' => 'id'], 'skipOnEmpty' => true, 'skipOnError' => true],

            ['to', 'string'],
            ['to', 'filter', 'filter' => 'trim'],
            ['to', 'required', 'when' => fn () => !$this->toUserId, 'skipOnEmpty' => false, 'skipOnError' => true],
            ['to', PhoneInputValidator::class, 'when' => fn () => !$this->toUserId, 'skipOnError' => true, 'skipOnEmpty' => false],
            ['to', function ($attribute) {
                $userPhone = UserProjectParams::find()->byPhone($this->{$attribute}, false)->limit(1)->one();
                if ($userPhone) {
                    $this->toUserId = $userPhone->upp_user_id;
                }
            }, 'skipOnError' => true, 'skipOnEmpty' => true],
            ['to', function ($attribute) {
                $isBlockedPhone = PhoneBlacklist::find()->isExists($this->{$attribute});
                if ($isBlockedPhone) {
                    $this->addError($attribute, 'To phone is blocked.');
                }
            }, 'skipOnEmpty' => true, 'skipOnError' => true],

            ['historyCallSid', 'default', 'value' => null],
            ['historyCallSid', 'string'],
            ['historyCallSid', 'exist', 'targetClass' => CallLog::class, 'targetAttribute' => ['historyCallSid' => 'cl_call_sid'], 'skipOnError' => true, 'skipOnEmpty' => true],

            ['from', 'string'],
            ['from', 'filter', 'filter' => 'trim'],
            ['from', 'required', 'when' => fn () => !$this->isInternalCall() && !$this->fromHistoryCall(), 'skipOnEmpty' => false, 'skipOnError' => true],
            ['from', function ($attribute) {
                $phoneList = PhoneList::find()->select(['pl_id'])->andWhere(['pl_phone_number' => $this->{$attribute}])->asArray()->one();
                if ($phoneList) {
                    $this->phoneListId = (int)$phoneList['pl_id'];
                    return;
                }
                $this->addError($attribute, 'Not found phone in Phone List.');
            }, 'skipOnEmpty' => true, 'skipOnError' => true],

            ['clientId', 'default', 'value' => null],
            ['clientId', 'required', 'when' => fn () => $this->isFromContacts(), 'skipOnError' => true, 'skipOnEmpty' => false],
            ['clientId', 'integer'],
            ['clientId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],
            ['clientId', 'exist', 'targetClass' => Client::class, 'targetAttribute' => ['clientId' => 'id'], 'skipOnEmpty' => true, 'skipOnError' => true],

            ['leadId', 'default', 'value' => null],
            ['leadId', 'required', 'when' => fn () => $this->isFromLead(), 'skipOnError' => true, 'skipOnEmpty' => false],
            ['leadId', 'integer'],
            ['leadId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],
            ['leadId', 'exist', 'targetClass' => Lead::class, 'targetAttribute' => ['leadId' => 'id'], 'skipOnEmpty' => true, 'skipOnError' => true],

            ['caseId', 'default', 'value' => null],
            ['caseId', 'required', 'when' => fn () => $this->isFromCase(), 'skipOnError' => true, 'skipOnEmpty' => false],
            ['caseId', 'integer'],
            ['caseId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],
            ['caseId', 'exist', 'targetClass' => Cases::class, 'targetAttribute' => ['caseId' => 'cs_id'], 'skipOnEmpty' => true, 'skipOnError' => true],

            ['deviceId', 'required'],
            ['deviceId', 'integer'],
            [
                'deviceId',
                function ($attribute) {
                    try {
                        $this->voipDevice = (new ReadyVoipDevice())->find($this->{$attribute}, $this->getCreatedUserId());
                    } catch (\Throwable $e) {
                        $this->addError($attribute, $e->getMessage());
                    }
                },
                'skipOnEmpty' => true,
                'skipOnError' => true
            ],
        ];
    }

    public function isFromCase(): bool
    {
        return $this->fromCase ? true : false;
    }

    public function isFromLead(): bool
    {
        return $this->fromLead ? true : false;
    }

    public function isFromContacts(): bool
    {
        return $this->fromContacts ? true : false;
    }

    public function fromHistoryCall(): bool
    {
        return $this->historyCallSid ? true : false;
    }

    public function isInternalCall(): bool
    {
        return $this->toUserId !== null;
    }

    public function getPhoneListId(): int
    {
        return $this->phoneListId;
    }

    public function formName(): string
    {
        return '';
    }

    public function getCreatedUserId(): int
    {
        return $this->createdUserId;
    }

    public function getVoipDevice(): string
    {
        return $this->voipDevice;
    }
}
