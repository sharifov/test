<?php

namespace sales\model\call\useCase\checkRecording;

use yii\base\Model;

/**
 * Class CheckRecordingForm
 *
 * @property $fromPhone
 * @property $projectId
 * @property $departmentId
 * @property $contactId
 */
class CheckRecordingForm extends Model
{
    public $fromPhone;
    public $projectId;
    public $departmentId;
    public $contactId;

    public function rules(): array
    {
        return [
           ['fromPhone', 'default', 'value' => null],
           ['fromPhone', 'string'],

           ['projectId', 'default', 'value' => null],
           ['projectId', 'integer'],

           ['departmentId', 'default', 'value' => null],
           ['departmentId', 'integer'],

           ['contactId', 'default', 'value' => null],
           ['contactId', 'integer'],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
