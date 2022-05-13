<?php

namespace src\forms\cases;

use common\models\ClientPhone;
use src\services\client\InternalPhoneValidator;
use yii\base\Model;
use src\entities\cases\Cases;
use borales\extensions\phoneInput\PhoneInputValidator;

/**
 * Class CasesAddPhoneForm
 *
 * @property string $phone
 * @property int $type
 */
class CasesAddPhoneForm extends Model
{
    public $phone;
    public $caseGid;
    public $type;

    /**
     * CasesChangeStatusForm constructor.
     * @param Cases $case
     * @param array $config
     */
    public function __construct(Cases $case, $config = [])
    {
        parent::__construct($config);
        $this->caseGid = $case->cs_gid;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['phone', 'required'],
            [['phone'], PhoneInputValidator::class],
            ['phone', InternalPhoneValidator::class, 'allowInternalPhone' => \Yii::$app->params['settings']['allow_contact_internal_phone']],
            ['type', 'integer'],
            ['type', 'default', 'value' => ClientPhone::PHONE_NOT_SET],
            ['type', 'in', 'range' => array_keys(ClientPhone::PHONE_TYPE)]
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'phone' => 'Phone',
        ];
    }
}
