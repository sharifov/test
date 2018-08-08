<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "employees".
 *
 * @property int $id
 * @property string $username
 * @property string $full_name
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $last_activity
 * @property boolean $acl_rules_activated
 *
 * @property EmployeeProfile[] $employeeProfiles
 * @property Lead[] $leads
 * @property EmployeeAcl[] $employeeAcl
 * @property ProjectEmployeeAccess[] $projectEmployeeAccesses
 */
class Employee extends \yii\db\ActiveRecord implements IdentityInterface
{
    const SCENARIO_REGISTER = 'register';

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    public $password;
    public $deleted;
    public $role;
    public $employeeAccess;
    public $viewItemsEmployeeAccess;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employees';
    }

    public function afterFind()
    {
        parent::afterFind();

        $roles = $this->getRoles();
        $this->role = array_keys($roles)[0];

        $this->deleted = !($this->status);

        if ($this->role != 'admin') {
            $this->employeeAccess = array_keys(ArrayHelper::map($this->projectEmployeeAccesses, 'project_id', 'project_id'));
        }
    }

    public function afterValidate()
    {
        parent::afterValidate();

        $this->updated_at = date('Y-m-d H:i:s');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeeAcl()
    {
        return $this->hasMany(EmployeeAcl::className(), ['employee_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectEmployeeAccesses()
    {
        return $this->hasMany(ProjectEmployeeAccess::className(), ['employee_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    public static function getAllEmployees()
    {
        return ArrayHelper::map(self::find()->where(['status' => self::STATUS_ACTIVE])->all(), 'id', 'username');
    }

    public static function getAllRoles()
    {
        $roles = [];
        $query = new Query();
        $result = $query->select(['name', 'description'])
            ->from('auth_item')->where(['type' => 1])
            ->all();
        foreach ($result as $item) {
            $roles[$item['name']] = $item['description'];
        }
        return $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'auth_key', 'password_hash', 'email', 'role'], 'required'],
            [['password'], 'required', 'on' => self::SCENARIO_REGISTER],
            [['email', 'password', 'username'], 'trim'],
            [['password'], 'string', 'min' => 6],
            [['status'], 'integer'],
            [['created_at', 'updated_at', 'last_activity', 'acl_rules_activated', 'full_name'], 'safe'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeeProfiles()
    {
        return $this->hasMany(EmployeeProfile::className(), ['employee_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeads()
    {
        return $this->hasMany(Lead::className(), ['employee_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function prepareSave($attr)
    {
        $this->username = $attr['username'];
        $this->email = $attr['email'];
        $this->full_name = $attr['full_name'];
        $this->password = $attr['password'];
        if (!empty($this->password)) {
            $this->setPassword($this->password);
        }
        if (isset($attr['deleted'])) {
            $this->status = empty($attr['deleted'])
                ? self::STATUS_ACTIVE : self::STATUS_DELETED;
        }
        if (isset($attr['acl_rules_activated'])) {
            $this->acl_rules_activated = $attr['acl_rules_activated'];
        }
        $this->role = $attr['role'];
        $this->generateAuthKey();

        return $this->isNewRecord;
    }

    public function addRole($isNew)
    {
        // the following three lines were added:
        $auth = \Yii::$app->authManager;
        if (!$isNew) {
            $auth->revokeAll($this->id);
        }

        $authorRole = $auth->getRole($this->role);
        $auth->assign($authorRole, $this->id);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function getRoles()
    {
        return ArrayHelper::map(Yii::$app->authManager->getRolesByUser($this->id), 'name', 'description');
    }
}
