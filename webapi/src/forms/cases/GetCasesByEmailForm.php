<?php

namespace webapi\src\forms\cases;

use common\models\ClientEmail;
use yii\base\Model;

/**
 * Class CaseRequestApiForm
 * @package webapi\src\forms\cases
 *
 * @property string $contact_email
 * @property string $active_only
 * @property int|null $project_key
 * @property int|null $department_key
 * @property int|null $results_limit
 */
class GetCasesByEmailForm extends Model
{
    public $contact_email;
    public $active_only;
    public $project_key;
    public $department_key;
    public $results_limit;

    public function rules(): array
    {
        return [
            [['contact_email', 'active_only'], 'required'],
            ['contact_email', 'email'],
            ['contact_email', 'exist', 'targetClass' => ClientEmail::class, 'targetAttribute' => ['contact_email' => 'email'], 'message' => 'Client Email not found in DB.'],
            ['active_only', 'filter', 'filter' => 'strtolower'],
            ['active_only', 'in', 'range' => ['true', 'false', '1', '0']],
            ['results_limit', 'integer'],
            ['project_key', 'string', 'max' => 50],
            ['department_key', 'string', 'max' => 20],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
