<?php
namespace common\models;

use Yii;
use yii\base\Model;
use Da\TwoFA\Manager;

/**
 * Class LoginStepTwoForm
 *
 * @property string $secret_key
 * @property string $userEmail
 * @property bool $rememberMe
 * @property string $twoFactorAuthKey *
 * @property int $twoFactorAuthCycles
 */
class LoginStepTwoForm extends Model
{
    public $secret_key;

    private $userEmail;
    private $rememberMe;
    private $twoFactorAuthKey;
    private $twoFactorAuthCycles = 2;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['secret_key'], 'required'],
            [['secret_key'], 'string', 'max' => 50],
            ['secret_key', 'validateKey'],
        ];
    }


    /**
     * @param $attribute
     * @param $params
     * @throws \Da\TwoFA\Exception\InvalidCharactersException
     * @throws \Da\TwoFA\Exception\InvalidSecretKeyException
     */
    public function validateKey($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            $valid = (new Manager())
                ->setCycles($this->twoFactorAuthCycles)
                ->verify($this->secret_key, $this->twoFactorAuthKey);

            \yii\helpers\VarDumper::dump(['validateKey' => $valid], 10, true); exit();  /* FOR DEBUG:: must by remove */

            if (!$valid) {
                $this->addError($attribute, 'Incorrect secret key.');
            }
        }
    }

    /**
     * @param string $email
     * @param bool $rememberMe
     * @return bool
     */
    public function login(): bool
    {
        if($user = Employee::findOne(['email' => $this->userEmail])) {
            return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    /**
     * @param string $userEmail
     * @return LoginStepTwoForm
     */
    public function setUserEmail(string $userEmail): LoginStepTwoForm
    {
        $this->userEmail = $userEmail;
        return $this;
    }

    /**
     * @param string $twoFactorKey
     * @return LoginStepTwoForm
     */
    public function setTwoFactorAuthKey(string $twoFactorAuthKey): LoginStepTwoForm
    {
        $this->twoFactorAuthKey = $twoFactorAuthKey;
        return $this;
    }

    /**
     * @param bool $rememberMe
     * @return LoginStepTwoForm
     */
    public function setRememberMe(bool $rememberMe): LoginStepTwoForm
    {
        $this->rememberMe = $rememberMe;
        return $this;
    }
}
