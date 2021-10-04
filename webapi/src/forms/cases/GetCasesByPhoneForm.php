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
 * @property string $active_only
 * @property int|null $project_key
 * @property int|null $department_key
 * @property int|null $results_limit
 */
class GetCasesByPhoneForm extends Model
{
    public $contact_phone;
    public $active_only;
    public $project_key;
    public $department_key;
    public $results_limit;

    public function rules(): array
    {
        return [
            [['contact_phone', 'active_only'], 'required'],
            [['contact_phone', 'department_key'],'string', 'max' => 20],
            ['contact_phone', 'trim'],
            ['contact_phone', PhoneInputValidator::class],
            ['contact_phone', 'exist', 'targetClass' => ClientPhone::class, 'targetAttribute' => ['contact_phone' => 'phone'], 'message' => 'Client Phone number not found in DB.'],
            ['active_only', 'in', 'range' => [0, 1]],
            [['active_only', 'results_limit'], 'integer'],
            ['project_key', 'string', 'max' => 50],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
