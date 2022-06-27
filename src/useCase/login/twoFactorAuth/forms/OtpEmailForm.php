<?php

namespace src\useCase\login\twoFactorAuth\forms;

use common\models\Employee;
use common\models\LoginForm;
use src\useCase\login\twoFactorAuth\service\OtpEmailService;
use Yii;
use yii\base\Model;

/**
 * @property-read Employee $user
 * @property-read OtpEmailService $service
 */
class OtpEmailForm extends Model implements TwoFactorFormInterface
{
    public $secretKey;

    private ?Employee $user = null;
    private OtpEmailService $service;

    public function __construct($config = [])
    {
        $this->service = Yii::createObject(OtpEmailService::class);
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['secretKey'], 'required'],
            [['secretKey'], 'string', 'max' => 6],
            [['secretKey'], 'validateKey']
        ];
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
            $this->afterLogin();
        }
        return $isLogin;
    }

    public function validateKey($attribute): bool
    {
        if (!$this->hasErrors()) {
            $userProfile = $this->user->userProfile;

            if (empty($userProfile->up_otp_hashed_code) || $this->service->hashKey($this->secretKey) !== $userProfile->up_otp_hashed_code) {
                $this->addError($attribute, 'Wrong verification code. Please verify your secret code and try again.');
                return false;
            }
            $otpExpiredDt = new \DateTimeImmutable($userProfile->up_otp_expired_dt);
            $curDateTime = new \DateTimeImmutable();
            if ($curDateTime > $otpExpiredDt) {
                $this->addError($attribute, 'Wrong verification code. Code has expired.');
                return false;
            }
        }
        return true;
    }

    private function afterLogin(): void
    {
        $this->user->userProfile->removeOtpData();
        $this->user->userProfile->save();
    }
}
