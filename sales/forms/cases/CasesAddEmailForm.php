<?php

namespace sales\forms\cases;

use sales\entities\cases\Cases;
use sales\services\client\InternalEmailValidator;
use yii\base\Model;

/**
 * Class CasesAddEmailForm
 *
 * @property string $email
 * @property string $caseGid
 */
class CasesAddEmailForm extends Model
{
    public $email;
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
            ['email', 'required'],
            ['email', 'string', 'max' => 160],
            ['email', 'email'],
            ['email', InternalEmailValidator::class, 'allowInternalEmail' => \Yii::$app->params['settings']['allow_contact_internal_email']],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'email' => 'Email',
        ];
    }
}