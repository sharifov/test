<?php

namespace webapi\models;

use common\models\Client;
use common\models\Employee;
use common\models\Lead;
use common\models\Source;
use Yii;
use yii\base\Model;

/**
 * This is the model class for table "leads".
 *
 * @property int $lead_id
 * @property int $client_id
 * @property int $employee_id
 * @property int $status
 * @property string $uid
 * @property int $project_id
 * @property int $source_id
 * @property string $trip_type
 * @property string $cabin
 * @property int $adults
 * @property int $children
 * @property int $infants
 * @property string $notes_for_experts
 * @property string $created
 * @property string $updated
 * @property string $request_ip
 * @property string $request_ip_detail
 * @property string $offset_gmt
 * @property string $snooze_for
 * @property int $rating
 * @property string $sub_sources_code
 * @property int $discount_id
 *
 * @property array $emails
 * @property array $phones
 * @property array $flights
 * @property string $client_first_name
 * @property string $client_middle_name
 * @property string $client_last_name
 * @property string $user_agent
 * @property string $user_language
 *
 */
class ApiLead extends Model
{

    CONST SCENARIO_CREATE = 'create';
    CONST SCENARIO_UPDATE = 'update';
    CONST SCENARIO_GET = 'get';

    public $lead_id;
    public $client_id;
    public $employee_id;
    public $status;
    public $uid;
    public $project_id;
    public $source_id;
    public $trip_type;
    public $cabin;
    public $adults;
    public $children;
    public $infants;
    public $notes_for_experts;
    public $created;
    public $updated;
    public $request_ip;
    public $request_ip_detail;
    public $offset_gmt;
    public $snooze_for;
    public $rating;
    public $sub_sources_code;
    public $discount_id;

    public $flights;
    public $emails;
    public $phones;

    public $client_first_name;
    public $client_last_name;
    public $client_middle_name;
    public $user_agent;
    public $user_language;


    public function formName()
    {
        return 'lead';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['source_id'], 'required'],
            [['lead_id'], 'required', 'on' => [self::SCENARIO_UPDATE, self::SCENARIO_GET]],
            [['adults', 'flights'], 'required', 'except' => [self::SCENARIO_UPDATE, self::SCENARIO_GET]],

            [['lead_id', 'source_id', 'discount_id'], 'integer'],
            [['sub_sources_code'], 'string', 'max' => 20],

            [['sub_sources_code'], 'checkIsSourceCode'],
            [['source_id'], 'checkIsSource'],

            [['client_first_name', 'client_last_name', 'client_middle_name'], 'string', 'max' => 100],
            [['emails'], 'each', 'rule' => ['email']],
            [['phones'], 'each', 'rule' => ['string', 'max' => 20]],
            [['source_id'], 'checkEmailAndPhone', 'except' => [self::SCENARIO_UPDATE, self::SCENARIO_GET]],

            [['adults', 'children', 'infants'], 'integer', 'min' => 0, 'max' => 9],
            [['adults'], 'integer', 'min' => 1],
            [['client_id', 'employee_id', 'status', 'project_id', 'source_id', 'adults', 'children', 'infants', 'rating'], 'integer'],
            [['notes_for_experts', 'request_ip_detail', 'user_agent'], 'string'],
            [['created', 'updated', 'snooze_for', 'flights', 'emails', 'phones'], 'safe'],
            [['uid', 'request_ip', 'offset_gmt'], 'string', 'max' => 255],

            [['uid'], 'unique', 'targetClass' => Lead::class, 'targetAttribute' => ['uid', 'project_id'], 'message'=>'Lead UID ({value}) already exists!', 'except' => [self::SCENARIO_GET]],

            [['trip_type'], 'string', 'max' => 2],
            [['cabin'], 'string', 'max' => 1],

            [['user_language'], 'string', 'max' => 5],
            [['user_agent'], 'string'],

            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['client_id' => 'id']],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['employee_id' => 'id']],
            [['flights'], 'checkIsFlights'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UPDATE] = ['lead_id', 'status', 'uid', 'trip_type', 'cabin', 'adults', 'children', 'infants', 'notes_for_experts', 'request_ip', 'request_ip_detail', 'offset_gmt', 'snooze_for', 'rating', 'flights', 'emails', 'phones',
            'client_first_name', 'client_last_name', 'client_middle_name', 'discount_id', 'sub_sources_code'];
        $scenarios[self::SCENARIO_GET] = ['lead_id', 'uid', 'source_id'];
        return $scenarios;
    }


    public function checkIsFlights($attribute, $params)
    {
        if (empty($this->flights)) {
            $this->addError('flights', "Flights cannot be empty");
        }
        elseif (!is_array($this->flights)) {
            $this->addError('config', "Flights must be array.");
        }
        else{
            foreach ($this->flights as $key => $flight) {

                $model = new ApiLeadFlightSegment();
                $model->attributes = $flight;

                if(!$model->validate()) {

                    if($model->firstErrors) $error = $model->firstErrors[key($model->firstErrors)];
                    else $error = 'ApiLeadFlightSegment validate error';

                    $this->addError('flights ', 'Flight ['.$key.']: ' . $error);
                }
            }
        }
    }


    public function checkIsSource()
    {
        if (empty($this->source_id)) {
            $this->addError('source_id', "Source ID cannot be empty");
        } else {
            $source = Source::findOne(['id' => $this->source_id, 'project_id' => $this->project_id]);
            if(!$source) $this->addError('source_id', "Invalid Source ID (project: ".$this->project_id.")");
        }
    }

    public function checkIsSourceCode()
    {
        if (!empty($this->sub_sources_code)) {
            $source = Source::findOne(['cid' => $this->sub_sources_code, 'project_id' => $this->project_id]);
            if(!$source) {
                $this->addError('source_id', "Invalid Source Code (project: ".$this->project_id.")");
            } else {
                $this->source_id = $source->id;
            }
        } else {
            $sources = Source::findAll(['project_id' => $this->project_id]);
            $this->source_id = $sources[0]->id;
        }
    }


    /**
     *
     */
    public function checkEmailAndPhone()
    {

        if (empty($this->emails) && empty($this->phones)) {
            $this->addError('emails', 'Phones or Emails cannot be blank');
            $this->addError('phones', 'Phones or Emails cannot be blank');
        }

    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lead_id' => 'Lead ID',
            'client_id' => 'Client ID',
            'employee_id' => 'Employee ID',
            'status' => 'Status',
            'uid' => 'Uid',
            'project_id' => 'Project ID',
            'source_id' => 'Source ID',
            'sub_sources_code' => 'Source Code',
            'discount_id' => 'Discount',

            'trip_type' => 'Trip Type',
            'cabin' => 'Cabin',
            'adults' => 'Adults',
            'children' => 'Children',
            'infants' => 'Infants',
            'notes_for_experts' => 'Notes For Experts',
            'created' => 'Created',
            'updated' => 'Updated',
            'request_ip' => 'Request Ip',
            'request_ip_detail' => 'Request Ip Detail',
            'offset_gmt' => 'Offset Gmt',
            'snooze_for' => 'Snooze For',
            'rating' => 'Rating',

            'emails' => 'Emails',
            'phones' => 'Phones',

            'client_first_name' => 'Client first name',
            'client_last_name' => 'Client last name',
            'client_middle_name'    => 'Client middle name',

            'user_language'    => 'Client Language',
            'user_agent'    => 'Client UserAgent',
        ];
    }

    /**
     * @return string
     */
    public function getRequestHash() : string
    {
        $hashArray = [];
        $hashArray[] = $this->request_ip;
        $hashArray[] = $this->project_id;
        $hashArray[] = $this->adults;
        $hashArray[] = $this->children;
        $hashArray[] = $this->infants;
        $hashArray[] = $this->cabin;
        $hashArray[] = date('Y-m-d');

        if($this->phones) {
            foreach ($this->phones as $phone) {
                $hashArray[] = $phone;
            }
        }

        if($this->flights) {
            foreach ($this->flights as $flight) {

                $hashArray[] = $flight['origin'];
                $hashArray[] = $flight['destination'];
                $hashArray[] = $flight['departure'];
            }
        }

        $hash = md5(implode('|', $hashArray));
        return $hash;
    }


}
