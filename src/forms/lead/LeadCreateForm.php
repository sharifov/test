<?php

namespace src\forms\lead;

use common\models\Department;
use common\models\Lead;
use common\models\Sources;
use src\access\EmployeeProjectAccess;
use src\forms\CompositeForm;
use src\helpers\lead\LeadHelper;
use src\access\ListsAccess;
use src\repositories\cases\CasesRepository;
use src\repositories\NotFoundException;
use src\services\client\ClientCreateForm;

/**
 * @property string $cabin
 * @property integer $adults
 * @property integer $children
 * @property integer $infants
 * @property string $requestIp
 * @property integer $sourceId
 * @property integer $projectId
 * @property string $notesForExperts
 * @property string $clientPhone
 * @property string $clientEmail
 * @property string $status
 * @property string $caseGid
 * @property int $depId
 * @property boolean $delayedCharge
 * @property int|null $userId
 * @property ClientCreateForm $client
 * @property EmailCreateForm[] $emails
 * @property PhoneCreateForm[] $phones
 * @property SegmentCreateForm[] $segments
 * @property PreferencesCreateForm $preferences
 */
class LeadCreateForm extends CompositeForm
{
    public $cabin;
    public $adults;
    public $children;
    public $infants;
    public $requestIp;
    public $sourceId;
    public $projectId;
    public $notesForExperts;
    public $clientPhone;
    public $clientEmail;
    public $status;
    public $caseGid;
    public $depId;
    public $delayedCharge = 0;

    private $userId;



    /**
     * LeadCreateForm constructor.
     * @param int $countEmails
     * @param int $countPhones
     * @param int $countSegments
     * @param int|null $userId
     * @param array $config
     */
    public function __construct(int $countEmails = 1, int $countPhones = 1, int $countSegments = 1, ?int $userId = null, $config = [])
    {
        $this->adults = 1;
        $this->children = 0;
        $this->infants = 0;
        $this->status = Lead::STATUS_PROCESSING;

        $this->client = new ClientCreateForm();

        $this->emails = array_map(function () {
            return new EmailCreateForm();
        }, self::createCountMultiField($countEmails));

        $this->phones = array_map(function () {
            return new PhoneCreateForm();
        }, self::createCountMultiField($countPhones));

        $this->segments = array_map(function () {
            return new SegmentCreateForm();
        }, self::createCountMultiField($countSegments));

        $this->preferences = new PreferencesCreateForm();

        $this->userId = $userId;

        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [

            ['cabin', 'required'],
            ['cabin', 'string', 'max' => 1],
            ['cabin', 'in', 'range' => array_keys(LeadHelper::cabinList())],

            [['adults', 'children', 'infants'], 'required'],
            [['adults', 'children', 'infants'], 'integer', 'min' => 0, 'max' => 9],
            [['adults', 'children', 'infants'], 'in', 'range' => array_keys(LeadHelper::adultsChildrenInfantsList())],

            ['adults', function () {
                if (!$this->adults && !$this->children) {
                    $this->addError('adults', 'Adults or Children must be more than 0');
                }
            }],

            ['infants', function () {
                if ($this->infants > $this->adults) {
                    $this->addError('infants', 'Infants must be no greater than Adults');
                }
            }],

            ['requestIp', 'ip'],

            ['sourceId', 'required'],
            ['sourceId', 'integer'],
            ['sourceId', 'exist', 'skipOnError' => true, 'targetClass' => Sources::class, 'targetAttribute' => ['sourceId' => 'id']],
            ['sourceId', function () {
                if ($projectId = Sources::find()->select('project_id')->where(['id' => $this->sourceId])->asArray()->limit(1)->one()) {
                    $this->projectId = $projectId['project_id'];
                    if (!EmployeeProjectAccess::isInProject($this->projectId, $this->userId)) {
                        $this->addError('sourceId', 'Access denied for this project');
                    }
                } else {
                    $this->addError('sourceId', 'Project not found');
                }
                if ($source = Sources::findOne($this->sourceId)) {
                    $this->validateRequiredRules($source->rule);
                } else {
                    $this->addError('sourceId', 'Not found Source rules');
                }
            }],

            ['notesForExperts', 'string'],

            [['emails', 'phones', 'segments'], 'safe'],

            ['segments', function () {
                foreach ($this->segments as $key => $segment) {
                    if (isset($this->segments[$key - 1])) {
                        $dateFrom = strtotime($this->segments[$key - 1]->departure);
                        $dateTo = strtotime($this->segments[$key]->departure);
                        if ($dateTo < $dateFrom) {
                            $this->addError('segments[' . $key . '][departure]', 'Date can not be less than the date of the previous segment');
                        }
                    }
                }
            }],

            ['segments', function () {
                if (count($this->segments) < 1) {
                    $this->addError('segments', 'Segments must be more than 0');
                }
            }],

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

            ['delayedCharge', 'boolean'],
            ['delayedCharge', 'default', 'value' => false],

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
        if (count($this->phones) < 1) {
            $this->addError('sourceId', 'Phone cannot be blank.');
            return;
        }
        foreach ($this->phones as $phone) {
            $phone->required = true;
        }
    }

    private function sourceRulesEmailRequired(): void
    {
        if (count($this->emails) < 1) {
            $this->addError('sourceId', 'Email cannot be blank.');
            return;
        }
        foreach ($this->emails as $email) {
            $email->required = true;
        }
    }

    private function sourceRulesEmailAndPhoneRequired(): void
    {
        if (count($this->emails) < 1) {
            $this->addError('sourceId', 'Email cannot be blank.');
            return;
        }
        if (count($this->phones) < 1) {
            $this->addError('sourceId', 'Phone cannot be blank.');
            return;
        }
        foreach ($this->emails as $email) {
            $email->required = true;
        }
        foreach ($this->phones as $phone) {
            $phone->required = true;
        }
    }

    private function sourceRulesEmailOrPhoneRequired(): void
    {
        if (count($this->emails) < 1 && count($this->phones) < 1) {
            $this->addError('sourceId', 'Email or Phone cannot be blank.');
            return;
        }

        $oneEmailIsNotEmpty = false;
        $onePhoneIsNotEmpty = false;

        foreach ($this->emails as $email) {
            if ($email->email) {
                $oneEmailIsNotEmpty = true;
            }
        }
        foreach ($this->phones as $phone) {
            if ($phone->phone) {
                $onePhoneIsNotEmpty = true;
            }
        }

        if (!$oneEmailIsNotEmpty && !$onePhoneIsNotEmpty) {
            foreach ($this->emails as $email) {
                $email->required = true;
                $email->message = 'Email or Phone cannot be blank.';
            }
            foreach ($this->phones as $phone) {
                $phone->required = true;
                $phone->message = 'Phone or Email cannot be blank.';
            }
        } else {
            foreach ($this->emails as $email) {
                $email->required = false;
            }
            foreach ($this->phones as $phone) {
                $phone->required = false;
            }
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
     * @param string $caseGid
     */
    public function assignCase(string $caseGid): void
    {
        $this->caseGid = $caseGid;
    }

    public function validate($attributeNames = null, $clearErrors = true): bool
    {
        if (parent::validate($attributeNames, $clearErrors)) {
            $this->loadClientData();
            $this->checkEmptyPhones();
            $this->checkEmptyEmails();
            return true;
        }
        $this->checkEmptyPhones();
        $this->checkEmptyEmails();
        return false;
    }

    private function loadClientData(): void
    {

        if (!$this->hasErrors()) {
            if (isset($this->emails[0]) && $this->emails[0]->email) {
                $this->clientEmail = $this->emails[0]->email;
            } else {
                $this->clientEmail = '';
            }
            if (isset($this->phones[0]) && $this->phones[0]->phone) {
                $this->clientPhone = $this->phones[0]->phone;
            } else {
                $this->clientPhone = '';
            }
        }
    }

    private function checkEmptyPhones(): void
    {
        $errors = false;
        foreach ($this->phones as $key => $phone) {
            if ($key > 0 && !$phone->phone) {
                if (!$this->getErrors('phones.' . $key . '.phone')) {
                    $errors = true;
                    $this->addError('phones.' . $key . '.phone', 'Phone cannot be blank.');
                }
            }
        }
        if (!$errors && count($this->phones) > 1 && isset($this->phones[0]->phone) && !$this->phones[0]->phone) {
            if (!$this->getErrors('phones.0.phone')) {
                $this->addError('phones.0.phone', 'Phone cannot be blank.');
            }
        }
    }

    private function checkEmptyEmails(): void
    {
        $errors = false;
        foreach ($this->emails as $key => $email) {
            if ($key > 0 && !$email->email) {
                if (!$this->getErrors('emails.' . $key . '.email')) {
                    $errors = true;
                    $this->addError('emails.' . $key . '.email', 'Email cannot be blank.');
                }
            }
        }
        if (!$errors && count($this->emails) > 1 && isset($this->emails[0]->email) && !$this->emails[0]->email) {
            if (!$this->getErrors('emails.0.email')) {
                $this->addError('emails.0.email', 'Email cannot be blank.');
            }
        }
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
        return ['segments', 'client', 'emails', 'phones', 'preferences'];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'requestIp' => 'Client IP',
            'sourceId' => 'Marketing Info:',
            'depId' => 'Department:',
        ];
    }
}
