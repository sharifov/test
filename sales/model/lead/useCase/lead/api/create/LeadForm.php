<?php

namespace sales\model\lead\useCase\lead\api\create;

use common\models\Lead;
use common\models\Sources;
use sales\helpers\lead\LeadHelper;
use sales\yii\validators\IsArrayValidator;
use yii\base\Model;

/**
 * Class LeadForm
 *
 * @property $sub_sources_code
 * @property $source_id
 * @property $project_id
 * @property $status
 * @property $uid
 * @property $trip_type
 * @property $cabin
 * @property $adults
 * @property $children
 * @property $infants
 * @property $request_ip
 * @property $discount_id
 * @property $user_agent
 * @property array $segments
 * @property array $client
 * @property SegmentForm[] $segmentsForm
 * @property ClientForm $clientForm
 */
class LeadForm extends Model
{
    public $sub_sources_code;
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
    public $segments;
    public $client;

    public $segmentsForm;
    public $clientForm;

    public function rules(): array
    {
        return [
            ['sub_sources_code', 'required'],
            ['sub_sources_code', 'string'],
            ['sub_sources_code', function () {
                if ($source = Sources::find()->select(['id', 'project_id'])->where(['cid' => $this->sub_sources_code])->asArray()->limit(1)->one()) {
                    $this->source_id = $source['id'];
                    $this->project_id = $source['project_id'];
                } else {
                    $this->addError('sub_sources_code', 'Source not found');
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

            ['discount_id', 'string'],

            ['uid', 'required'],
            ['uid', 'string'],
            ['uid', 'unique', 'targetClass' => Lead::class, 'targetAttribute' => ['uid' => 'uid']],

            ['user_agent', 'string'],

            ['status', 'required'],
            ['status', 'in', 'range' => [Lead::STATUS_BOOK_FAILED, Lead::STATUS_ALTERNATIVE]],

            ['segments', 'required'],
            ['segments', IsArrayValidator::class],
            ['segments', function () { $this->loadAndValidateSegments(); }, 'skipOnError' => true, 'skipOnEmpty' => true],

            ['client', 'required'],
            ['client', IsArrayValidator::class],
            ['client', function () { $this->loadAndValidateClient($this->client); }, 'skipOnError' => true, 'skipOnEmpty' => true],
        ];
    }

    public function formName(): string
    {
        return 'lead';
    }

    private function loadAndValidateSegments(): void
    {
        $isValid = true;
        foreach ($this->segments as $key => $flight) {
            /** @var array $flight */
            if (!$this->loadAndValidateSegment($key, $flight)) {
                $isValid = false;
            }
        }
        if (!$isValid) {
            return;
        }
        $this->validateDateOfPreviousSegment();
    }

    private function loadAndValidateSegment(int $key, array $flight): bool
    {
        $segment = new SegmentForm();
        if (!$segment->load($flight, '')) {
            $this->addError('segments[' . $key . ']', 'Cant load segment');
            return false;
        }
        if ($segment->validate()) {
            $this->segmentsForm[] = $segment;
            return true;
        }
        foreach ($segment->errors as $attr => $error) {
            foreach ($error as $err) {
                $this->addError('segments[' . $key . '][' . $attr . ']', $err);
            }
        }
        return false;
    }

    private function validateDateOfPreviousSegment(): void
    {
        foreach ($this->segmentsForm as $key => $segment) {
            if (isset($this->segmentsForm[$key - 1])) {
                $dateFrom = strtotime($this->segmentsForm[$key - 1]->departure);
                $dateTo = strtotime($this->segmentsForm[$key]->departure);
                if ($dateTo < $dateFrom) {
                    $this->addError('segments[' . $key . '][departure]', 'Date can not be less than the date of the previous segment');
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
