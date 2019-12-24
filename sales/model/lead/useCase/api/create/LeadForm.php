<?php

namespace sales\model\lead\useCase\api\create;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\Lead;
use common\models\Sources;
use sales\helpers\lead\LeadHelper;
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
 * @property $phone
 * @property $flights
 * @property SegmentForm[] $segments
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
    public $phone;
    public $flights;
    public $segments;

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

            ['status', 'in', 'range' => [Lead::STATUS_BOOK_FAILED, Lead::STATUS_ALTERNATIVE]],

            ['phone', 'required'],
            ['phone', PhoneInputValidator::class],

            ['flights', 'required'],
            ['flights', function ($attribute) {
                if (!is_array($this->$attribute)) {
                    $this->addError($attribute, ucfirst($attribute) . ' must be array');
                }
                $this->segments = null;
                foreach ($this->$attribute as $key => $flight) {
                    if (!$this->loadAndValidateSegment($key, $flight)) {
                        $this->segments = null;
                        return;
                    }
                }
                if ($this->segments === null) {
                    $this->addError($attribute, ucfirst($attribute) . ' segments cannot be empty');
                } elseif (!$this->validateDateOfPreviousSegment()) {
                    $this->segments = null;
                }
            }, 'skipOnEmpty' => true],
        ];
    }

    private function validateDateOfPreviousSegment(): bool
    {
        foreach ($this->segments as $key => $segment) {
            if (isset($this->segments[$key - 1])) {
                $dateFrom = strtotime($this->segments[$key - 1]->departure);
                $dateTo = strtotime($this->segments[$key]->departure);
                if ($dateTo < $dateFrom) {
                    $this->addError('flights[' . $key . '][departure]', 'Date can not be less than the date of the previous segment');
                    return false;
                }
            }
        }
        return true;
    }

    private function loadAndValidateSegment(int $key, array $flight): bool
    {
        $segment = new SegmentForm();
        if (!$segment->load($flight, '')) {
            $this->addError('flights[' . $key . ']', 'Cant load segment');
            return false;
        }
        if ($segment->validate()) {
            $this->segments[] = $segment;
            return true;
        }
        if ($segment->errors) {
            foreach ($segment->errors as $attr => $error) {
                $this->addError('flights[' . $key . '][' . $attr . ']', $error);
            }
        }
        return false;
    }
}
