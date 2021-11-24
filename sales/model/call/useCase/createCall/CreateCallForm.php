<?php

namespace sales\model\call\useCase\createCall;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\Call;
use common\models\Client;
use common\models\Department;
use common\models\Employee;
use common\models\Lead;
use common\models\PhoneBlacklist;
use common\models\Project;
use common\models\UserProjectParams;
use sales\entities\cases\Cases;
use sales\model\callLog\entity\callLog\CallLog;
use sales\model\phoneList\entity\PhoneList;
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
 * @property $projectId
 * @property $departmentId
 * @property $clientId
 * @property $sourceTypeId
 * @property $leadId
 * @property $caseId
 */
class CreateCallForm extends Model
{
    private $createdUserId;
    private $phoneListId;

    public $toUserId;
    public $from;
    public $to;
    public $historyCallSid;
    public $projectId;
    public $departmentId;
    public $clientId;
    public $sourceTypeId;
    public $leadId;
    public $caseId;

    public function __construct(int $createdUserId, $config = [])
    {
        parent::__construct($config);
        $this->createdUserId = $createdUserId;
    }

    public function rules(): array
    {
        return [
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
            ['from', 'required', 'when' => fn () => !$this->toUserId && !$this->historyCallSid, 'skipOnEmpty' => false, 'skipOnError' => true],
            ['from', function ($attribute) {
                $phoneList = PhoneList::find()->select(['pl_id'])->andWhere(['pl_phone_number' => $this->{$attribute}])->asArray()->one();
                if ($phoneList) {
                    $this->phoneListId = (int)$phoneList['pl_id'];
                    return;
                }
                $this->addError($attribute, 'Not found phone in Phone List.');
            }, 'skipOnEmpty' => true, 'skipOnError' => true],

            ['projectId', 'default', 'value' => null],
            ['projectId', 'integer'],
            ['projectId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],
            ['projectId', 'exist', 'targetClass' => Project::class, 'targetAttribute' => ['projectId' => 'id'], 'skipOnEmpty' => true, 'skipOnError' => true],

            ['departmentId', 'default', 'value' => null],
            ['departmentId', 'integer'],
            ['departmentId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],
            ['departmentId', 'exist', 'targetClass' => Department::class, 'targetAttribute' => ['departmentId' => 'dep_id'], 'skipOnEmpty' => true, 'skipOnError' => true],

            ['clientId', 'default', 'value' => null],
            ['clientId', 'integer'],
            ['clientId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],
            ['clientId', 'exist', 'targetClass' => Client::class, 'targetAttribute' => ['clientId' => 'id'], 'skipOnEmpty' => true, 'skipOnError' => true],

            ['sourceTypeId', 'default', 'value' => null],
            ['sourceTypeId', 'integer'],
            ['sourceTypeId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],
            ['sourceTypeId', 'in', 'range' => array_keys(Call::SOURCE_LIST)],

            ['leadId', 'default', 'value' => null],
            ['leadId', 'integer'],
            ['leadId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],
            ['leadId', 'exist', 'targetClass' => Lead::class, 'targetAttribute' => ['leadId' => 'id'], 'skipOnEmpty' => true, 'skipOnError' => true],

            ['caseId', 'default', 'value' => null],
            ['caseId', 'integer'],
            ['caseId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],
            ['caseId', 'exist', 'targetClass' => Cases::class, 'targetAttribute' => ['caseId' => 'cs_id'], 'skipOnEmpty' => true, 'skipOnError' => true],
        ];
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
}
