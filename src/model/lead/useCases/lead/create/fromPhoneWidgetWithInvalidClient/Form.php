<?php

namespace src\model\lead\useCases\lead\create\fromPhoneWidgetWithInvalidClient;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\Call;
use common\models\Sources;
use src\model\phoneList\entity\PhoneList;
use src\services\client\InternalEmailValidator;
use src\services\client\InternalPhoneValidator;
use yii\base\Model;

/**
 * Class Form
 *
 * @property string|null $phone
 * @property string|null $email
 * @property string $firstName
 * @property string|null $middleName
 * @property string|null $lastName
 * @property Sources $source
 * @property int $projectId
 * @property int $departmentId
 * @property int $userId
 * @property string $callSid
 */
class Form extends Model
{
    public $phone;
    public $email;
    public $firstName;
    public $middleName;
    public $lastName;

    private $projectId;
    private $source;
    private $departmentId;
    private $userId;
    private $callSid;

    public function __construct(Call $call, int $userId, $config = [])
    {
        $this->userId = $userId;
        $this->departmentId = $call->c_dep_id;
        $this->projectId = $call->c_project_id;
        $this->callSid = $call->c_call_sid;

        $internalPhoneNumber = $call->getInternalPhoneNumber();
        if (!$internalPhoneNumber) {
            throw new \DomainException('Not detected internal phone. CallId: ' . $call->c_id);
        }
        $phoneList = PhoneList::find()
            ->innerJoinWith('departmentPhoneProject', true)
            ->andWhere(['pl_phone_number' => $internalPhoneNumber, 'pl_enabled' => true])
            ->andWhere(['dpp_project_id' => $call->c_project_id, 'dpp_enable' => true])
            ->one();
        if ($phoneList && $phoneList->departmentPhoneProject->dpp_source_id) {
            $this->source = $phoneList->departmentPhoneProject->dppSource;
        } else {
            $this->source = Sources::getByProjectId($call->c_project_id);
        }
        if (!$this->source) {
            throw new \DomainException('Not found Source. CallId: ' . $call->c_id);
        }

        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['phone', 'default', 'value' => null],
            ['phone', 'string'],
            ['phone', 'trim'],
            ['phone', 'filter', 'filter' => static function ($value) {
                return $value === null ? null : str_replace(['-', ' '], '', trim($value));
            }],
            ['phone', PhoneInputValidator::class],
            ['phone', InternalPhoneValidator::class, 'allowInternalPhone' => \Yii::$app->params['settings']['allow_contact_internal_phone']],

            ['email', 'default', 'value' => null],
            ['email', 'string'],
            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'filter', 'filter' => static function ($value) {
                return $value === null ? null : mb_strtolower(trim($value));
            }],
            ['email', InternalEmailValidator::class, 'allowInternalEmail' => \Yii::$app->params['settings']['allow_contact_internal_email']],

            ['firstName', 'required'],
            ['firstName', 'string', 'max' => 100],
            ['firstName', 'match', 'pattern' => "/^[a-z-\s\']+$/i"],
            ['firstName', 'filter', 'filter' => 'trim'],

            ['middleName', 'string'],
            ['middleName', 'string', 'max' => 100],
            ['middleName', 'match', 'pattern' => "/^[a-z-\s\']+$/i"],
            ['middleName', 'filter', 'filter' => 'trim'],

            ['lastName', 'string'],
            ['lastName', 'string', 'max' => 100],
            ['lastName', 'match', 'pattern' => "/^[a-z-\s\']+$/i"],
            ['lastName', 'filter', 'filter' => 'trim'],
        ];
    }

    public function getSourceId(): int
    {
        return $this->source->id;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getDepartmentId(): int
    {
        return $this->departmentId;
    }

    public function getCallSid(): string
    {
        return $this->callSid;
    }

    public function validateRequiredRules(): void
    {
        switch ($this->source->rule) {
            case Sources::RULE_EMAIL_REQUIRED:
                $this->sourceRulesEmailRequired();
                break;
            case Sources::RULE_EMAIL_OR_PHONE_REQUIRED:
                $this->sourceRulesEmailOrPhoneRequired();
                break;
            case Sources::RULE_EMAIL_AND_PHONE_REQUIRED:
                $this->sourceRulesEmailAndPhoneRequired();
                break;
            case Sources::RULE_PHONE_REQUIRED:
            default:
                $this->sourceRulesPhoneRequired();
        }
    }

    private function sourceRulesPhoneRequired(): void
    {
        if (empty($this->phone)) {
            $this->addError('phone', 'Phone cannot be blank.');
        }
    }

    private function sourceRulesEmailRequired(): void
    {
        if (empty($this->email)) {
            $this->addError('email', 'Email cannot be blank.');
        }
    }

    private function sourceRulesEmailAndPhoneRequired(): void
    {
        $this->sourceRulesPhoneRequired();
        $this->sourceRulesEmailRequired();
    }

    private function sourceRulesEmailOrPhoneRequired(): void
    {
        if (empty($this->phone) && empty($this->email)) {
            $this->addError('phone', 'Phone or Email cannot be blank.');
            $this->addError('email', 'Email or Phone cannot be blank.');
            return;
        }
    }

    public function beforeValidate()
    {
        $this->validateRequiredRules();
        return parent::beforeValidate();
    }
}
