<?php

namespace sales\forms\lead;

use common\models\Lead;
use common\models\ProjectEmployeeAccess;
use common\models\Sources;
use sales\forms\CompositeForm;
use sales\helpers\lead\LeadHelper;

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

        $this->emails = array_map(function() {
            return new EmailCreateForm();
        }, self::createCountMultiField($countEmails));

        $this->phones = array_map(function() {
            return new PhoneCreateForm();
        }, self::createCountMultiField($countPhones));

        $this->segments = array_map(function() {
            return new SegmentCreateForm();
        }, self::createCountMultiField($countSegments));

        $this->preferences = new PreferencesCreateForm();

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
            ['sourceId', function() {
                if ($projectId = Sources::find()->where(['id' => $this->sourceId])->select('project_id')->asArray()->limit(1)->one()) {
                    $this->projectId = $projectId['project_id'];
                } else {
                    $this->addError('sourceId', 'Project not found');
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

            ['status', 'in', 'range' => array_keys(LeadHelper::statusList())]

        ];
    }

    public function afterValidate()
    {
        parent::afterValidate();
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
