<?php

namespace webapi\src\forms\cases;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\ClientPhone;
use sales\model\phoneList\entity\PhoneList;
use sales\services\client\InternalPhoneValidator;
use webapi\src\request\BoWebhook;
use yii\base\Model;
use common\components\validators\IsArrayValidator;

/**
 * Class CaseRequestApiForm
 * @package webapi\src\boWebhook
 *
 * @property string $contact_phone
 * @property int|null $typeId
 * @property array $data
 */
class FindCasesByPhoneForm extends Model
{
    public string $contact_phone = '';
    public bool $active_only = false;
    public ?int $case_project_id = null;
    public ?int $case_department_id = null;
    public ?int $results_limit = null;

    public function rules(): array
    {
        return [
            ['contact_phone', 'required'],
            ['contact_phone', 'string', 'max' => 20],
            ['contact_phone', 'trim'],
            ['contact_phone', PhoneInputValidator::class],
            ['contact_phone', 'exist', 'targetClass' => ClientPhone::class, 'targetAttribute' => ['contact_phone' => 'phone'], 'message' => 'Client Phone number not found in DB.'],
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
