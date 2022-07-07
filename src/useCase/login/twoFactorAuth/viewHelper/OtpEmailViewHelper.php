<?php

namespace src\useCase\login\twoFactorAuth\viewHelper;

use common\components\jobs\SendInternalEmailJob;
use common\models\Employee;
use src\helpers\setting\SettingHelper;
use src\useCase\login\twoFactorAuth\forms\OtpEmailForm;
use src\useCase\login\twoFactorAuth\service\OtpEmailService;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @property-read OtpEmailService $service
 */
class OtpEmailViewHelper extends View implements TwoFactorViewHelperInterface
{
    private OtpEmailService $service;

    public function __construct(OtpEmailService $service, $config = [])
    {
        parent::__construct($config);
        $this->service = $service;
    }

    public function renderView(ActiveForm $form, Employee $user): string
    {
        $model = new OtpEmailForm();

        $userProfile = $user->userProfile;
        if (empty($userProfile->up_otp_hashed_code) || $userProfile->isOtpExpiredDateTime()) {
            $code = $this->service->generateSecretCode();
            $curDateTime = new \DateTimeImmutable();
            $dateInterval = new \DateInterval('PT' . SettingHelper::getOtpEmailCodeLifeTime() . 'S');
            $userProfile->updateOtpData($this->service->hashKey((string)$code), $curDateTime->add($dateInterval));
            $userProfile->save();

            $emailTemplate = $this->render('/site/2fa/email_template', [
                'code' => $code,
                'secondsRemain' => $userProfile->getOtpSecondsLeft(),
                'emailSubject' => SettingHelper::getOtpEmailTemplateType()
            ]);

            $job = new SendInternalEmailJob(
                SettingHelper::getEmailFrom(),
                $user->email,
                $emailTemplate,
                SettingHelper::getOtpEmailTemplateSubject(),
            );
            \Yii::$app->queue_email_job->push($job);
        }
        return $this->renderAjax('/site/2fa/otp_email', [
            'model' => $model,
            'form' => $form,
            'secondsRemain' => $userProfile->getOtpSecondsLeft() ?? 0,
        ]);
    }
}
