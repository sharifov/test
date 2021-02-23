<?php

namespace sales\model\lead\useCases\lead\api\create;

use common\models\Language;
use common\models\Lead;
use common\models\Sources;
use sales\helpers\lead\LeadHelper;
use common\components\validators\IsArrayValidator;
use yii\base\Model;

/**
 * Class LeadForm
 *
 * @property string $source_code
 * @property int $source_id
 * @property int $project_id
 * @property int $status
 * @property string $uid
 * @property string $trip_type
 * @property string $cabin
 * @property int $adults
 * @property int $children
 * @property int $infants
 * @property string $request_ip
 * @property string $discount_id
 * @property string $user_agent
 * @property array $flights
 * @property array $client
 * @property int $flight_id
 * @property string|null $user_language
 * @property string|null $expire_at
 *
 * @property FlightForm[] $flightsForm
 * @property ClientForm $clientForm
 */
class LeadCreateForm extends Model
{
    public $source_code;
    public $source_id;
    public $project_id;
    public $status;
    public $uid;
    public $trip_type;
    public $cabin;
    public $adults;
    public $children;
    public $infants;
    public $request_ip;
    public $discount_id;
    public $user_agent;
    public $flights;
    public $client;
    public $flight_id;
    public ?string $user_language = null;
    public $expire_at;

    public $flightsForm;
    public $clientForm;

    public function rules(): array
    {
        return [
            ['source_code', 'required'],
            ['source_code', 'string', 'max' => 20],
            ['source_code', function () {
                if ($source = Sources::find()->select(['id', 'project_id'])->where(['cid' => $this->source_code])->asArray()->limit(1)->one()) {
                    $this->source_id = $source['id'];
                    $this->project_id = $source['project_id'];
                } else {
                    $this->addError('source_code', 'Source not found');
                }
            }],

            ['cabin', 'required'],
            ['cabin', 'string', 'max' => 1],
            ['cabin', 'in', 'range' => array_keys(LeadHelper::cabinList())],

            [['adults', 'children', 'infants'], 'required'],
            [['adults', 'children', 'infants'], 'filter', 'filter' => 'intval'],
            [['adults', 'children', 'infants'], 'integer', 'min' => 0, 'max' => 9],

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

            ['request_ip', 'ip'],

            ['discount_id', 'string', 'max' => 32],

            ['uid', 'required'],
            ['uid', 'string', 'max' => 15],
            ['uid', 'unique', 'targetClass' => Lead::class, 'targetAttribute' => ['uid' => 'uid']],

            ['user_agent', 'string'],

            ['status', 'required'],
            ['status', 'in', 'range' => [Lead::STATUS_BOOK_FAILED, Lead::STATUS_ALTERNATIVE]],

            ['flights', 'required'],
            ['flights', IsArrayValidator::class],
            ['flights', function () {
                $this->loadAndValidateFlights();
            }, 'skipOnError' => true, 'skipOnEmpty' => true],

            ['client', 'required'],
            ['client', IsArrayValidator::class],
            ['client', function () {
                $this->loadAndValidateClient($this->client);
            }, 'skipOnError' => true, 'skipOnEmpty' => true],

            ['flight_id', 'required'],
            ['flight_id', 'integer'],
            ['flight_id', 'filter', 'filter' => 'intval'],

            ['user_language', 'string', 'max' => 5],
            ['user_language', 'exist', 'skipOnError' => true, 'skipOnEmpty' => true,
                'targetClass' => Language::class, 'targetAttribute' => ['user_language' => 'language_id']],

            [['expire_at'], 'datetime', 'format' => 'php:Y-m-d H:i:s', 'skipOnEmpty' => true],
        ];
    }

    public function formName(): string
    {
        return 'lead';
    }

    private function loadAndValidateFlights(): void
    {
        $isValid = true;
        foreach ($this->flights as $key => $flight) {
            /** @var array $flight */
            if (!$this->loadAndValidateFlight($key, $flight)) {
                $isValid = false;
            }
        }
        if (!$isValid) {
            return;
        }
        $this->validateDateOfPreviousFlight();
    }

    private function loadAndValidateFlight(int $key, array $flight): bool
    {
        $flightForm = new FlightForm();
        if (!$flightForm->load($flight, '')) {
            $this->addError('flights[' . $key . ']', 'Cant load flight');
            return false;
        }
        if ($flightForm->validate()) {
            $this->flightsForm[] = $flightForm;
            return true;
        }
        foreach ($flightForm->errors as $attr => $error) {
            foreach ($error as $err) {
                $this->addError('flights[' . $key . '][' . $attr . ']', $err);
            }
        }
        return false;
    }

    private function validateDateOfPreviousFlight(): void
    {
        foreach ($this->flightsForm as $key => $flight) {
            if (isset($this->flightsForm[$key - 1])) {
                $dateFrom = strtotime($this->flightsForm[$key - 1]->departure);
                $dateTo = strtotime($this->flightsForm[$key]->departure);
                if ($dateTo < $dateFrom) {
                    $this->addError('flights[' . $key . '][departure]', 'Date can not be less than the date of the previous flight');
                }
            }
        }
    }

    private function loadAndValidateClient(array $data): void
    {
        $client = new ClientForm();
        if (!$client->load($data, '')) {
            $this->addError('client', 'Cant load client');
            return;
        }
        if ($client->validate()) {
            $this->clientForm = $client;
            return;
        }
        foreach ($client->errors as $attr => $error) {
            foreach ($error as $err) {
                $this->addError('client[' . $attr . ']', $err);
            }
        }
    }
}
