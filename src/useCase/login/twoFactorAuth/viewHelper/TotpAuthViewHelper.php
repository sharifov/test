<?php

namespace src\useCase\login\twoFactorAuth\viewHelper;

use common\components\TwoFactorService;
use common\models\Employee;
use src\useCase\login\twoFactorAuth\forms\TotpAuthForm;
use src\useCase\login\twoFactorAuth\service\TotpAuthService;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @property-read TotpAuthService $service
 */
class TotpAuthViewHelper extends View implements TwoFactorViewHelperInterface
{
    private TotpAuthService $service;

    public function __construct(TotpAuthService $service, $config = [])
    {
        parent::__construct($config);
        $this->service = $service;
    }

    public function renderView(ActiveForm $form, Employee $user): string
    {
        $twoFactorAuthSecretKey = $this->service->getOrSetSecretAuthKey($user);

        $qrcodeSrc = (new TwoFactorService())->getBase64($twoFactorAuthSecretKey, $user->username);

        $model = new TotpAuthForm();

        return $this->render('/site/2fa/totp_auth', [
            'model' => $model,
            'qrcodeSrc' => $qrcodeSrc,
            'twoFactorKeyExist' => !empty($user->userProfile->up_2fa_secret),
            'form' => $form
        ]);
    }
}
