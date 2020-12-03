<?php

namespace sales\model\clientAccount\form;

use thamtech\uuid\validators\UuidValidator;
use yii\base\Model;

/**
 * Class ClientAccountGetApiForm
 * @property string|null $uuid
 * @property int|null $projectId
 */
class ClientAccountGetApiForm extends Model
{
    public $uuid;
    public $projectId;

    public function rules(): array
    {
        return [
            [['uuid', 'projectId'], 'required'],

            ['uuid', 'string', 'max' => 36],
            ['uuid', UuidValidator::class],

            ['projectId', 'integer'],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}