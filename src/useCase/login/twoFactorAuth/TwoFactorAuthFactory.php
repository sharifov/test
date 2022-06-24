<?php

namespace src\useCase\login\twoFactorAuth;

use src\useCase\login\twoFactorAuth\forms\OtpEmailForm;
use src\useCase\login\twoFactorAuth\forms\TotpAuthForm;
use src\useCase\login\twoFactorAuth\forms\TwoFactorFormInterface;
use src\useCase\login\twoFactorAuth\viewHelper\OtpEmailViewHelper;
use src\useCase\login\twoFactorAuth\viewHelper\TotpAuthViewHelper;
use src\useCase\login\twoFactorAuth\viewHelper\TwoFactorViewHelperInterface;
use yii\base\Model;

class TwoFactorAuthFactory
{
    public const TOTP_AUTH = 1;
    public const OTP_EMAIL_AUTH = 2;

    public const DEFAULT = self::TOTP_AUTH;

    public const LIST = [
        self::TOTP_AUTH => 'TOTP Authenticator',
        self::OTP_EMAIL_AUTH => 'OTP Email',
    ];

    public const VIEW_HELPER_LIST = [
        self::TOTP_AUTH => TotpAuthViewHelper::class,
        self::OTP_EMAIL_AUTH => OtpEmailViewHelper::class
    ];

    public const FORM_LIST = [
        self::TOTP_AUTH => TotpAuthForm::class,
        self::OTP_EMAIL_AUTH => OtpEmailForm::class
    ];

    public static function getViewHelperByMethod(int $id): TwoFactorViewHelperInterface
    {
        $helper = self::VIEW_HELPER_LIST[$id] ?? null;
        if (!$helper) {
            throw new \RuntimeException('Not found two factor view helper');
        }
        return \Yii::createObject($helper);
    }

    /**
     * @param int $id
     * @return Model|TwoFactorFormInterface
     * @throws \RuntimeException
     */
    public static function getFormByMethod(int $id)
    {
        $form = self::FORM_LIST[$id] ?? null;
        if (!$form) {
            throw new \RuntimeException('Not found two factor form');
        }
        return \Yii::createObject($form);
    }
}
