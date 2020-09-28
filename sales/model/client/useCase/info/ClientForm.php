<?php

namespace sales\model\client\useCase\info;

use common\models\Project;
use yii\base\Model;

/**
 * Class ClientForm
 *
 * @property string|null $client_uuid
 * @property string|null $project_key
 */
class ClientForm extends Model
{
    public ?string $client_uuid = null;
    public ?string $project_key = null;

    public function rules(): array
    {
        return [
            ['client_uuid', 'required'],
            ['client_uuid', 'string'],
            ['client_uuid', 'trim'],

            ['project_key', 'required'],
            ['project_key', 'string'],
            ['project_key', 'trim'],
            ['project_key', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['project_key' => 'project_key']],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
