<?php

namespace modules\flight\src\useCases\voluntaryExchangeCreate\form\flightQuote\tripsForm;

use common\components\validators\CheckJsonValidator;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\flightQuote\tripsForm\segmentsForm\SegmentForm;
use src\helpers\ErrorsToStringHelper;
use src\traits\FormNameModelTrait;
use yii\base\Model;

/**
 * Class TripForm
 *
 * @property SegmentForm[] $segmentForms
 */
class TripForm extends Model
{
    use FormNameModelTrait;

    public $tripId;
    public $duration;
    public $segments;

    private array $segmentForms = [];

    public function rules(): array
    {
        return [
            [['segments'], 'required'],
            [['segments'], CheckJsonValidator::class, 'skipOnError' => true],
            [['segments'], 'checkSegmentForms'],

            [['tripId'], 'integer'],
            [['duration'], 'integer'],
        ];
    }

    public function checkSegmentForms(string $attribute): void
    {
        foreach ($this->segments as $key => $segment) {
            $segmentForm = new SegmentForm();
            $segmentForm->setFormName('');
            if (!$segmentForm->load($segment)) {
                $this->addError($attribute, 'SegmentForm not loaded');
            } elseif (!$segmentForm->validate()) {
                $this->addError($attribute, 'SegmentForm.' . $key . '.' . ErrorsToStringHelper::extractFromModel($segmentForm, ', '));
            } else {
                $this->segmentForms[] = $segmentForm;
            }
        }
    }

    public function getSegmentForms(): array
    {
        return $this->segmentForms;
    }
}
