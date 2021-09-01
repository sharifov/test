<?php

namespace webapi\src\forms\cases;

use yii\base\Model;

/**
 * Class CaseRequestApiForm
 * @package webapi\src\boWebhook
 *
 * @property string $contact_phone
 * @property int|null $typeId
 * @property array $data
 */
class FindCasesByEmailForm extends Model
{
    public string $contact_email = '';
    public bool $active_only = false;
    public ?int $case_project_id = null;
    public ?int $case_department_id = null;
    public ?int $results_limit = null;

    public function rules(): array
    {
        return [
            ['contact_email', 'required'],
            ['contact_email', 'email'],
            ['active_only', 'boolean'],
            ['case_project_id', 'integer'],
            ['case_department_id', 'integer'],
            ['results_limit', 'integer'],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
