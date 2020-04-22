<?php
namespace common\models;

use Yii;
use yii\base\Model;
use yii\web\IdentityInterface;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = false;

    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();

            if($user) {
                if (!$this->checkByIp($user)) {
                    return false;
                }

                $isLogin = Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
                if ($isLogin) {
                    self::sendWsIdentityCookie(Yii::$app->user->identity, $this->rememberMe ? 3600 * 24 * 30 : 0);
                }
                return $isLogin;
            }
        }
        return false;
    }



    /**
     * @return Employee|null
     */
    public function checkedUser(): ?Employee
    {
        if ($this->validate()) {
            $user = $this->getUser();

            if($user) {
                if (!$this->checkByIp($user)) {
                    return null;
                }
                return $user;
            }
        }
        return null;
    }

    /**
     * @param $user
     * @return bool
     */
    private function checkByIp($user): bool
    {
        if($user->acl_rules_activated) {
            $clientIP = $this->getClientIPAddress();
            if ($clientIP === 'UNKNOWN' ||  (!GlobalAcl::isActiveIPRule($clientIP) && !EmployeeAcl::isActiveIPRule($clientIP, $user->id))) {
                $this->addError('username', sprintf('Remote Address %s Denied! Please, contact your Supervision or Administrator.', $clientIP));
                return false;
            }
        }
        return true;
    }


    /**
     * @return string
     */
    private function getClientIPAddress()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipAddress = 'UNKNOWN';

        return $ipAddress;
    }

    /**
     * Finds user by [[username]]
     *
     * @return Employee|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Employee::findByUsername($this->username);
        }

        return $this->_user;
    }

    /**
     * @param IdentityInterface $identity
     * @param int $duration
     * @throws \yii\base\InvalidConfigException
     */
    public static function sendWsIdentityCookie(IdentityInterface $identity, int $duration = 0): void
    {
        $identityCookie = Yii::$app->params['wsIdentityCookie'] ?? [];

        $cookie = Yii::createObject(array_merge($identityCookie, [
            'class' => 'yii\web\Cookie',
            'value' => json_encode([
                $identity->getId(),
                $identity->getAuthKey(),
                $duration,
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'expire' => $duration ? time() + $duration : 0,
        ]));
        Yii::$app->getResponse()->getCookies()->add($cookie);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public static function removeWsIdentityCookie(): void
    {
        $identityCookie = Yii::$app->params['wsIdentityCookie'] ?? [];
        Yii::$app->getResponse()->getCookies()->remove(Yii::createObject(array_merge($identityCookie, [
            'class' => 'yii\web\Cookie',
        ])));
    }
}
