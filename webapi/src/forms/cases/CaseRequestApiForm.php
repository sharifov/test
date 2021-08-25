<?php

namespace webapi\src\forms\cases;

use borales\extensions\phoneInput\PhoneInputValidator;
use sales\services\client\InternalPhoneValidator;
use webapi\src\request\BoWebhook;
use yii\base\Model;
use common\components\validators\IsArrayValidator;

/**
 * Class BoWebhookForm
 * @package webapi\src\boWebhook
 *
 * @property string $contact_phone
 * @property int|null $typeId
 * @property array $data
 */
class CaseRequestApiForm extends Model
{
    public string $contact_phone = '';
    public string $active_only = '';
    public ?int $case_project_id = null;
    public ?int $case_department_id = null;
    public ?int $limit = null;

    public function rules(): array
    {
        return [
            ['contact_phone', 'required'],
            ['contact_phone', 'string', 'max' => 20],
            ['active_only', 'string', 'max' => 5],
            ['case_project_id', 'integer'],
            ['case_department_id', 'integer'],
            ['contact_phone', PhoneInputValidator::class],
            ['contact_phone', 'filter', 'filter' => static function ($value) {
                return str_replace(['-', ' '], '', trim($value));
            }, 'skipOnEmpty' => true, 'skipOnError' => true],
            ['contact_phone', InternalPhoneValidator::class,
                'skipOnError' => true, 'skipOnEmpty' => true,
                'allowInternalPhone' => \Yii::$app->params['settings']['allow_contact_internal_phone']],
            ['limit', 'integer'],
        ];
    }

//    public function afterValidate(): void
//    {
//    }

    public function formName(): string
    {
        return '';
    }
}
