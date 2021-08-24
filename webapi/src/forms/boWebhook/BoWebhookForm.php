<?php

namespace webapi\src\forms\boWebhook;

use webapi\src\request\BoWebhook;
use yii\base\Model;
use common\components\validators\IsArrayValidator;

/**
 * Class BoWebhookForm
 * @package webapi\src\boWebhook
 *
 * @property string $type
 * @property int|null $typeId
 * @property array $data
 */
class BoWebhookForm extends Model
{
    public string $type = '';
    public ?int $typeId = null;
    public array $data = [];

    public function rules(): array
    {
        return [
            ['type', 'required'],
            ['type', 'string', 'max' => 30],
            ['type', 'in', 'range' => BoWebhook::LIST_NAME],
            ['data', 'required'],
            ['data', IsArrayValidator::class],
        ];
    }

    public function afterValidate(): void
    {
        $this->typeId = BoWebhook::getTypeIdByName($this->type);
        parent::afterValidate();
    }

    public function formName(): string
    {
        return '';
    }
}
