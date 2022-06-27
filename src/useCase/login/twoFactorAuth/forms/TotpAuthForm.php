<?php

namespace src\useCase\login\twoFactorAuth\forms;

use common\components\TwoFactorService;
use common\models\Employee;
use common\models\LoginForm;
use src\useCase\login\twoFactorAuth\service\TotpAuthService;
use Yii;
use yii\base\Model;

/**
 * @var string $secretKey
 * @var Employee $user
 * @var TotpAuthService $service
 */
class TotpAuthForm extends Model implements TwoFactorFormInterface
{
    public $secretKey;

    private ?Employee $user = null;

    private TotpAuthService $service;

    public function __construct($config = [])
    {
        $this->service = Yii::createObject(TotpAuthService::class);
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['secretKey'], 'required'],
            [['secretKey'], 'trim'],
            [['secretKey'], 'string', 'max' => 50],
            [['secretKey'], 'validateKey'],
        ];
    }

    /**
     * @param $attribute
     * @param $params
     * @return void
     */
    public function validateKey($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            $valid = (new TwoFactorService())->verifyCode($this->service->getOrSetSecretAuthKey($this->user), $this->secretKey);

            if (!$valid) {
                $this->addError($attribute, 'Wrong verification code. Please verify your secret code and try again.');
            }
        }
    }

    public function formName(): string
    {
        return '';
    }

    public function setUser(Employee $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function login(bool $rememberMe): bool
    {
        $isLogin = Yii::$app->user->login($this->user, $rememberMe ? 3600 * 24 * 30 : 0);
        if ($isLogin) {
            LoginForm::sendWsIdentityCookie(Yii::$app->user->identity, $rememberMe ? 3600 * 24 * 30 : 0);
            $this->saveDataAfterLogin();
        }
        return $isLogin;
    }

    /**
     * @param Employee $user
     */
    private function saveDataAfterLogin(): void
    {
        $userProfile = $this->user->userProfile;
        $userProfile->up_2fa_secret = $this->service->getOrSetSecretAuthKey($this->user);
        $userProfile->removeOtpData();
        $userProfile->save();
        $this->service->removeSecretAuthKey();
    }
}
