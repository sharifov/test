<?php

namespace sales\model\call\useCase\checkRecording;

use yii\base\Model;

/**
 * Class CheckRecordingForm
 *
 * @property $fromPhone
 * @property $toPhone
 * @property $projectId
 * @property $departmentId
 * @property $contactId
 */
class CheckRecordingForm extends Model
{
    public $fromPhone;
    public $toPhone;
    public $projectId;
    public $departmentId;
    public $contactId;

    public function rules(): array
    {
        return [
            ['fromPhone', 'default', 'value' => null],
            ['fromPhone', 'string'],

            ['toPhone', 'default', 'value' => null],
            ['toPhone', 'string'],

            ['projectId', 'default', 'value' => null],
            ['projectId', 'integer'],
            ['projectId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['departmentId', 'default', 'value' => null],
            ['departmentId', 'integer'],
            ['departmentId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['contactId', 'default', 'value' => null],
            ['contactId', 'integer'],
            ['contactId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
