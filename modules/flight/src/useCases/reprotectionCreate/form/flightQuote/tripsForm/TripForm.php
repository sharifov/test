<?php

namespace modules\flight\src\useCases\reprotectionCreate\form\flightQuote\tripsForm;

use common\components\validators\CheckJsonValidator;
use modules\flight\src\useCases\reprotectionCreate\form\flightQuote\tripsForm\segmentsForm\SegmentForm;
use sales\helpers\ErrorsToStringHelper;
use yii\base\Model;

/**
 * Class TripForm
 *
 * @property $duration
 * @property $segments
 *
 * @property SegmentForm[] $segmentForms
 */
class TripForm extends Model
{
    public $duration;
    public $segments;

    private array $segmentForms = [];

    public function rules(): array
    {
        return [
            [['segments'], 'required'],
            [['segments'], CheckJsonValidator::class, 'skipOnError' => true],
            [['segments'], 'checkSegmentForms'],

            [['duration'], 'integer'],
        ];
    }

    public function checkSegmentForms($attribute)
    {
        foreach ($this->segments as $key => $segment) {
            $segmentForm = new SegmentForm();
            if (!$segmentForm->load($segment)) {
                $this->addError($attribute, 'SegmentForm not loaded');
            } elseif (!$segmentForm->validate()) {
                $this->addError($attribute, ErrorsToStringHelper::extractFromModel($segmentForm, ' '));
            } else {
                $this->segmentForms[] = $segmentForm;
            }
        }
    }

    public function formName(): string
    {
        return '';
    }
}
