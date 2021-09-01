<?php

namespace webapi\src\forms\cases;

use common\models\ClientEmail;
use yii\base\Model;

/**
 * Class CaseRequestApiForm
 * @package webapi\src\boWebhook
 *
 * @property string $contact_phone
 * @property int|null $typeId
 * @property array $data
 */
class FindCaseByCaseGidForm extends Model
{
    public string $case_gid = '';

    public function rules(): array
    {
        return [
            ['case_gid', 'required'],
            ['case_gid', 'string', 'max' => 50],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
