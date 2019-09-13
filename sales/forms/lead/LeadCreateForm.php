<?php

namespace sales\forms\lead;

use common\models\Department;
use common\models\Lead;
use common\models\ProjectEmployeeAccess;
use common\models\Sources;
use sales\forms\CompositeForm;
use sales\helpers\lead\LeadHelper;
use sales\repositories\cases\CasesRepository;
use sales\repositories\NotFoundException;

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

    public $jivoChatId;

    /**
     * LeadCreateForm constructor.
     * @param int $countEmails
     * @param int $countPhones
     * @param int $countSegments
     * @param array $config
     */
    public function __construct(int $countEmails = 1, int $countPhones = 1, int $countSegments = 1, $config = [])
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

        $this->jivoChatId = \Yii::$app->params['settings']['jivo_chat_id'] ?? null;

        if (!$this->jivoChatId) {
            \Yii::error('Jivo chat Id not found');
        }

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
                if ($projectId = Sources::find()->where(['id' => $this->sourceId])->select('project_id')->asArray()->limit(1)->one()) {
                    $this->projectId = $projectId['project_id'];
                } else {
                    $this->addError('sourceId', 'Project not found');
                }
                $this->validateJivoChat();
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

            ['depId', 'required'],
            ['depId', 'in', 'range' => [Department::DEPARTMENT_SALES, Department::DEPARTMENT_EXCHANGE]],

        ];
    }

    public function validateJivoChat(): void
    {
        if ((int)$this->sourceId !== $this->jivoChatId) {
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
                $email->emailIsRequired = true;
                $email->message = 'Email or Phone cannot be blank.';
            }
            foreach ($this->phones as $phone) {
                $phone->phoneIsRequired = true;
                $phone->message = 'Phone or Email cannot be blank.';
            }
        } else {
            foreach ($this->emails as $email) {
                $email->emailIsRequired = false;
            }
            foreach ($this->phones as $phone) {
                $phone->phoneIsRequired = false;
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
        if (!$errors && count($this->phones) > 1  &&  isset($this->phones[0]->phone) && !$this->phones[0]->phone) {
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
        if (!$errors && count($this->emails) > 1  &&  isset($this->emails[0]->email) && !$this->emails[0]->email) {
            if (!$this->getErrors('emails.0.email')) {
                $this->addError('emails.0.email', 'Email cannot be blank.');
            }
        }
    }

    /**
     * @return array
     */
    public function listSourceId(): array
    {
        return ProjectEmployeeAccess::getAllSourceByEmployee();
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
        ];
    }
}
