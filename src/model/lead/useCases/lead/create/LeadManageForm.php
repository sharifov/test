<?php

namespace src\model\lead\useCases\lead\create;

use common\models\Department;
use common\models\Lead;
use common\models\Sources;
use src\access\EmployeeProjectAccess;
use src\access\ListsAccess;
use src\forms\CompositeForm;
use src\services\client\ClientCreateForm;
use src\forms\lead\EmailCreateForm;
use src\forms\lead\PhoneCreateForm;
use src\forms\lead\PreferencesCreateForm;
use src\helpers\lead\LeadHelper;
use src\repositories\cases\CasesRepository;
use src\repositories\NotFoundException;

/**
 * Class LeadCreateForm2
 * @package src\forms\lead
 *
 * @property integer $source
 * @property integer $projectId
 * @property string $clientPhone
 * @property string $clientEmail
 * @property string $status
 * @property string $requestIp;
 * @property string $caseGid
 * @property int $depId
 * @property int|null $userId
 * @property ClientCreateForm $client
 * @property EmailCreateForm $email
 * @property PhoneCreateForm $phone
 * @property PreferencesCreateForm $preferences
 */
class LeadManageForm extends CompositeForm
{
    public $source;
    public $projectId;
    public $clientPhone;
    public $clientEmail;
    public $status;
    public $caseGid;
    public $depId;
    public $requestIp;

    private $userId;

    /**
     * LeadCreateForm constructor.
     * @param int|null $tipsUserId
     * @param array $config
     */
    public function __construct(?int $tipsUserId = null, $config = [])
    {
        $this->status = Lead::STATUS_PROCESSING;

        $this->client = new ClientCreateForm();

        $this->email = new EmailCreateForm();

        $this->phone = new PhoneCreateForm();

        $this->preferences = new PreferencesCreateForm();

        $this->userId = $tipsUserId;

        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [

            ['source', 'required'],
            ['source', 'integer'],
            ['source', 'exist', 'skipOnError' => true, 'targetClass' => Sources::class, 'targetAttribute' => ['source' => 'id']],
            ['source', function () {
                if ($projectId = Sources::find()->select('project_id')->where(['id' => $this->source])->asArray()->limit(1)->one()) {
                    $this->projectId = $projectId['project_id'];
                    if (!EmployeeProjectAccess::isInProject($this->projectId, $this->userId)) {
                        $this->addError('source', 'Access denied for this project');
                    }
                } else {
                    $this->addError('source', 'Project not found');
                }
                if ($source = Sources::findOne($this->source)) {
                    $this->validateRequiredRules($source->rule);
                } else {
                    $this->addError('source', 'Not found Source rules');
                }
            }],

            ['requestIp', 'ip'],

            [['email', 'phone'], 'safe'],

            [['phone'], 'required'],

            ['status', 'in', 'range' => array_keys(LeadHelper::statusList())],

            ['caseGid', 'string'],
            ['caseGid', function () {
                if (!$this->caseGid) {
                    return;
                }
                $casesRepository = \Yii::createObject(CasesRepository::class);
                try {
                    $case = $casesRepository->findFreeByGid((string)$this->caseGid);
                } catch (NotFoundException $e) {
                    $this->addError('caseGid', 'Case not found');
                } catch (\DomainException $e) {
                    $this->addError('caseGid', 'Case is already assigned to Lead');
                }
            }],

            ['depId', 'integer'],
            ['depId', 'required'],
            ['depId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
        ];
    }

    public function validateRequiredRules($ruleId): void
    {
        switch ($ruleId) {
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
        if (empty($this->phone->phone)) {
            $this->addError('sourceId', 'Phone cannot be blank.');
            return;
        }
        $this->phone->required = true;
    }

    private function sourceRulesEmailRequired(): void
    {
        if (empty($this->email->email)) {
            $this->addError('sourceId', 'Email cannot be blank.');
            return;
        }
        $this->email->required = true;
    }

    private function sourceRulesEmailAndPhoneRequired(): void
    {
        if (empty($this->email->email)) {
            $this->addError('sourceId', 'Email cannot be blank.');
            return;
        }
        if (empty($this->phone->phone)) {
            $this->addError('sourceId', 'Phone cannot be blank.');
            return;
        }
        $this->email->required = true;
        $this->phone->required = true;
    }

    private function sourceRulesEmailOrPhoneRequired(): void
    {
        if (empty($this->phone->phone) && empty($this->email->email)) {
            $this->addError('sourceId', 'Email or Phone cannot be blank.');
            return;
        }

        $oneEmailIsNotEmpty = false;
        $onePhoneIsNotEmpty = false;

        if ($this->email->email) {
            $oneEmailIsNotEmpty = true;
        }
        if ($this->phone->phone) {
            $onePhoneIsNotEmpty = true;
        }

        if (!$oneEmailIsNotEmpty && !$onePhoneIsNotEmpty) {
            $this->email->required = true;
            $this->email->message = 'Email or Phone cannot be blank.';

            $this->phone->required = true;
            $this->phone->message = 'Phone or Email cannot be blank.';
        } else {
            $this->email->required = false;
            $this->phone->required = false;
        }
    }

    public function validate($attributeNames = null, $clearErrors = true): bool
    {
        if (parent::validate($attributeNames, $clearErrors)) {
            $this->loadClientData();
            $this->checkEmptyPhone();
            $this->checkEmptyEmail();
            return true;
        }
        $this->checkEmptyPhone();
        $this->checkEmptyEmail();
        return false;
    }

    private function loadClientData(): void
    {
        if (!$this->hasErrors()) {
            if (isset($this->email) && $this->email->email) {
                $this->clientEmail = $this->email->email;
            } else {
                $this->clientEmail = '';
            }
            if (isset($this->phone) && $this->phone->phone) {
                $this->clientPhone = $this->phone->phone;
            } else {
                $this->clientPhone = '';
            }
        }
    }

    private function checkEmptyPhone(): void
    {
        if (empty($this->phone->phone)) {
            $this->addError('sourceId', 'Phone cannot be blank.');
        }
    }

    private function checkEmptyEmail(): void
    {
        if (empty($this->email->email)) {
            $this->addError('sourceId', 'Email cannot be blank.');
        }
    }

    /**
     * @param int $depId
     */
    public function assignDep(int $depId): void
    {
        $this->depId = $depId;
    }

    /**
     * @return array
     */
    public function listSources(): array
    {
        return (new ListsAccess($this->userId))->getSources();
    }

    /**
     * @return array
     */
    public function internalForms(): array
    {
        return ['client', 'email', 'phone', 'preferences'];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'requestIp' => 'Client IP',
            'source' => 'Marketing Info:',
            'depId' => 'Department:',
        ];
    }
}
