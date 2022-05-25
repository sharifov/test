<?php

namespace common\models;

use frontend\models\UserFailedLogin;
use src\services\authentication\AntiBruteForceHelper;
use src\services\authentication\AntiBruteForceService;
use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\IdentityInterface;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = false;
    public $verifyCode;
    public $captcha;

    private $_user;
    private bool $userChecked = false;

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
            ['username', 'checkIsBlocked'],
            ['password', 'validatePassword'],
            ['verifyCode', 'captcha', 'captchaAction' => Url::to('/site/captcha'), 'when' => static function () {
                return (new AntiBruteForceService())->checkCaptchaEnable();
            }],
        ];
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function checkIsBlocked($attribute): void
    {
        $userBlocked = Employee::findByUsername($this->username, Employee::STATUS_BLOCKED);
        if ($userBlocked) {
            $this->addError($attribute, 'The user is blocked.');
        }
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
        if ($this->userChecked === false) {
            if (!$this->validate()) {
                return false;
            }
        }

        $user = $this->getUser();

        if ($user) {
            if (!$this->checkByIp($user)) {
                return false;
            }

            $isLogin = Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
            if ($isLogin) {
                self::sendWsIdentityCookie(Yii::$app->user->identity, $this->rememberMe ? 3600 * 24 * 30 : 0);
            }
            return $isLogin;
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

            if ($user) {
                if (!$this->checkByIp($user)) {
                    return null;
                }
                $this->userChecked = true;
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
        if ($user->acl_rules_activated) {
            $clientIP = AntiBruteForceHelper::getClientIPAddress();
            if ($clientIP === 'UNKNOWN' ||  (!GlobalAcl::isActiveIPRule($clientIP) && !EmployeeAcl::isActiveIPRule($clientIP, $user->id))) {
                $this->addError('username', sprintf('Remote Address %s Denied! Please, contact your Supervision or Administrator.', $clientIP));
                return false;
            }
        }
        return true;
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

    public function afterValidate(): void
    {
        if ($this->hasErrors() && (isset($this->_user) && !$this->_user->isBlocked() || !isset($this->_user))) {
            $user = $this->_user ?? Employee::findOne(['username' => $this->username]);
            $userFailedLogin = UserFailedLogin::create(
                $this->username,
                $user ? $user->id : null,
                AntiBruteForceHelper::getBrowserName() . ' UserAgent:' . Yii::$app->request->getUserAgent(),
                AntiBruteForceHelper::getClientIPAddress(),
                Yii::$app->session->id
            );
            if (!$userFailedLogin->save()) {
                \Yii::error(
                    VarDumper::dumpAsString($userFailedLogin->getErrors(), 10),
                    'LoginForm:afterValidate:saveFailed'
                );
            }
        }
        if ($this->hasErrors()) {
            if ($this->_user) {
                (new AntiBruteForceService())->checkAttempts($this->_user);
            }
        }
        parent::afterValidate();
    }

    public function setUser(Employee $user): void
    {
        $this->_user = $user;
    }

    public function setUserChecked(bool $userChecked): void
    {
        $this->userChecked = $userChecked;
    }
}
