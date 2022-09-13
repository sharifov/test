<?php

namespace common\implementations\twoFactorAuth;

use common\components\email\dto\EmailDto;
use common\models\UserConnection;
use kivork\TwoFactorAuth\Otp\Email;
use src\model\user\entity\monitor\UserMonitor;

/**
 * Class EmailAuth
 * @package common\implementations\twoFactorAuth
 */
class EmailAuth extends Email
{
    /**
     * @param \common\models\LoginForm $object
     * @return bool
     * @throws \Exception
     */
    public function auth($object): bool
    {
        if ($object->login()) {
            if (UserConnection::isIdleMonitorEnabled()) {
                UserMonitor::addEvent(\Yii::$app->user->id, UserMonitor::TYPE_LOGIN);
            }
            return true;
        }
        return false;
    }

    /**
     * @param $user
     * @return mixed|string
     * @throws \yii\base\Exception
     */
    public function generateCode($user)
    {
        return \Yii::$app->security->generateRandomString(8);
    }

    /**
     * @param \common\models\LoginForm $loginForm
     * @param $code
     * @return bool|mixed
     */
    public function sendCode($loginForm, $code)
    {
        $emailDto = new EmailDto(
            $loginForm->getUserEmail(),
            isset($this->options['from']) ? $this->options['from'] : '',
            'Authentication code',
            $this->renderBody(['code' => $code])
        );
        return \Yii::$app->email->send($emailDto);
    }

    /**
     * @param $object
     * @return string
     */
    public function getAuthTypeName($object): string
    {
        return 'Auth by Email';
    }
}
