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
class GetCasesByPhoneForm extends Model
{
    public string $contact_phone = '';
    public bool $active_only = false;
    public ?int $cases_project_id = null;
    public ?int $cases_department_id = null;
    public ?int $results_limit = null;

    public function rules(): array
    {
        return [
            [['contact_phone', 'active_only'], 'required'],
            ['contact_phone', 'string', 'max' => 20],
            ['active_only', 'boolean'],
            [['cases_project_id', 'cases_department_id', 'results_limit'], 'integer']
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
