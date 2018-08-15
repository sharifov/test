<?php

namespace webapi\models;

use common\models\Client;
use common\models\Employee;
use common\models\Source;
use Yii;
use yii\base\Model;

/**
 * This is the model class for table "leads".
 *
 * @property int $id
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
 *
 * @property array $emails
 * @property array $phones
 * @property array $flights
 * @property array $client_first_name
 * @property array $client_last_name
 *
 */
class ApiLead extends Model
{

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

    public $flights;
    public $emails;
    public $phones;

    public $client_first_name;
    public $client_last_name;


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
            [['source_id', 'adults', 'flights'], 'required'],
            [['source_id'], 'checkIsSource'],
            [['client_first_name', 'client_last_name'], 'string', 'max' => 100],
            [['emails'], 'each', 'rule' => ['email']],
            [['phones'], 'each', 'rule' => ['string', 'max' => 20]],
            [['source_id'], 'checkEmailAndPhone'],

            [['adults', 'children', 'infants'], 'integer', 'min' => 0, 'max' => 9],
            [['adults'], 'integer', 'min' => 1],
            [['client_id', 'employee_id', 'status', 'project_id', 'source_id', 'adults', 'children', 'infants', 'rating'], 'integer'],
            [['notes_for_experts', 'request_ip_detail'], 'string'],
            [['created', 'updated', 'snooze_for', 'flights', 'emails', 'phones'], 'safe'],
            [['uid', 'request_ip', 'offset_gmt'], 'string', 'max' => 255],
            [['trip_type'], 'string', 'max' => 2],
            [['cabin'], 'string', 'max' => 1],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['client_id' => 'id']],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['employee_id' => 'id']],
            [['flights'], 'checkIsFlights'],
        ];
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


    /**
     *
     */
    public function checkEmailAndPhone()
    {

        if (empty($this->emails) || empty($this->phones)) {
            $this->addError('emails', "Phones or Emails cannot be blank");
            $this->addError('phones', "Phones or Emails cannot be blank");
        }

    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'employee_id' => 'Employee ID',
            'status' => 'Status',
            'uid' => 'Uid',
            'project_id' => 'Project ID',
            'source_id' => 'Source ID',
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


        ];
    }


}
