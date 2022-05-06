<?php

namespace src\forms\cases;

use common\models\ClientEmail;
use src\entities\cases\Cases;
use src\services\client\InternalEmailValidator;
use yii\base\Model;

/**
 * Class CasesAddEmailForm
 *
 * @property string $email
 * @property string $caseGid
 * @property int $type
 */
class CasesAddEmailForm extends Model
{
    public $email;
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
            ['email', 'required'],
            ['email', 'string', 'max' => 160],
            ['email', 'email'],
            ['email', InternalEmailValidator::class, 'allowInternalEmail' => \Yii::$app->params['settings']['allow_contact_internal_email']],
            ['type', 'integer'],
            ['type', 'default', 'value' => ClientEmail::EMAIL_NOT_SET],
            ['type', 'checkTypeForExistence']
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

    /**
     * @param $attribute
     * @param $params
     */
    public function checkTypeForExistence($attribute, $params): void
    {
        if (!isset(ClientEmail::EMAIL_TYPE[$this->type])) {
            $this->addError($attribute, 'Type of the email is not found');
        }
    }
}
