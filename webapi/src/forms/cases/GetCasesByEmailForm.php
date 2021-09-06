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
class GetCasesByEmailForm extends Model
{
    public $contact_email;
    public $active_only;
    public $cases_project_id;
    public $cases_department_id;
    public $results_limit;

    public function rules(): array
    {
        return [
            [['contact_email', 'active_only'], 'required'],
            ['contact_email', 'email'],
            ['contact_email', 'exist', 'targetClass' => ClientEmail::class, 'targetAttribute' => ['contact_email' => 'email'], 'message' => 'Client Email not found in DB.'],
            ['active_only', 'boolean', 'skipOnEmpty' => false, 'strict' => true],
            [['cases_project_id', 'cases_department_id', 'results_limit'], 'integer'],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
