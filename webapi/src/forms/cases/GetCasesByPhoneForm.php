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
            ['active_only', 'boolean', 'skipOnEmpty' => false, 'strict' => true],
            [['cases_project_id', 'cases_department_id', 'results_limit'], 'integer']
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
