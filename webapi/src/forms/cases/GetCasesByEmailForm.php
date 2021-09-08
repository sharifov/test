<?php

namespace webapi\src\forms\cases;

use common\models\ClientEmail;
use yii\base\Model;

/**
 * Class CaseRequestApiForm
 * @package webapi\src\forms\cases
 *
 * @property string $contact_email
 * @property boolean $active_only
 * @property int|null $cases_project_id
 * @property int|null $cases_department_id
 * @property int|null $results_limit
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
            ['active_only', 'filter', 'filter' => 'strtolower'],
            ['active_only', 'boolean', 'trueValue' => 'true', 'falseValue' => 'false', 'strict' => true],
            [['cases_project_id', 'cases_department_id', 'results_limit'], 'integer']
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
