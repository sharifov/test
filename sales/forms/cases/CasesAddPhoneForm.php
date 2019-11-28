<?php


namespace sales\forms\cases;


use sales\services\client\InternalPhoneException;
use sales\services\client\InternalPhoneGuard;
use yii\base\Model;
use sales\entities\cases\Cases;
use borales\extensions\phoneInput\PhoneInputValidator;

/**
 * Class CasesAddPhoneForm
 *
 * @property string $phone
 */
class CasesAddPhoneForm extends Model
{
    public $phone;
    public $caseGid;

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
            [['phone'], PhoneInputValidator::className()],
			['phone', 'internalPhoneValidate']
        ];
    }

	public function internalPhoneValidate($attribute): void
	{
		try {
			$guard = \Yii::createObject(InternalPhoneGuard::class);
			$guard->guard($this->phone);
		} catch (InternalPhoneException $e) {
			$this->addError($attribute, $e->getMessage());
		}
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