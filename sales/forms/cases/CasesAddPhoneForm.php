<?php


namespace sales\forms\cases;


use yii\base\Model;
use sales\entities\cases\Cases;
use borales\extensions\phoneInput\PhoneInputValidator;

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