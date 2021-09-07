<?php

namespace webapi\src\forms\cases;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\ClientPhone;
use yii\base\Model;

/**
 * Class GetCasesByPhoneForm
 * @package webapi\src\forms\cases
 *
 * @property string $contact_phone
 * @property boolean $active_only
 * @property int|null $cases_project_id
 * @property int|null $cases_department_id
 * @property int|null $results_limit
 */
class GetCasesByPhoneForm extends Model
{
    public $contact_phone;
    public $active_only;
    public $cases_project_id;
    public $cases_department_id;
    public $results_limit;

    public function rules(): array
    {
        return [
            [['contact_phone', 'active_only'], 'required'],
            ['contact_phone', 'string', 'max' => 20],
            ['contact_phone', 'trim'],
            ['contact_phone', PhoneInputValidator::class],
            ['contact_phone', 'exist', 'targetClass' => ClientPhone::class, 'targetAttribute' => ['contact_phone' => 'phone'], 'message' => 'Client Phone number not found in DB.'],
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
