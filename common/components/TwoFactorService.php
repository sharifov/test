<?php

namespace common\components;

use Endroid\QrCode\Builder\Builder;
use jp3cki\totp\Totp;
use Yii;

class TwoFactorService
{
    public function getSecret(): string
    {
        return Totp::generateKey();
    }

    public function getUrl(string $secret, string $label): string
    {
        return Totp::createKeyUriForGoogleAuthenticator(
            $secret,
            $label,
            Yii::$app->params['settings']['two_factor_company_name']
        );
    }

    public function getBase64(string $secret, string $label): string
    {
        $url = $this->getUrl($secret, $label);

        $result = Builder::create()
            ->data($url)
            ->size(170)
            ->margin(0)
            ->build();

        return $result->getDataUri();
    }

    public function verifyCode(string $secret, string $code, int $pastSteps = 2, int $futureSteps = 2): bool
    {
        return Totp::verify($code, $secret, time(), $pastSteps, $futureSteps);
    }
}
