<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "api_user".
 *
 * @property int $au_id
 * @property string $au_name
 * @property string $au_api_username
 * @property string $au_api_password
 * @property string $au_email
 * @property int $au_project_id
 * @property bool $au_enabled
 * @property string $au_updated_dt
 * @property int $au_updated_user_id
 *
 * @property Project $auProject
 */
class ApiUser extends ActiveRecord implements IdentityInterface
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'api_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['au_name', 'au_api_username'], 'required'],


            [['au_api_password'], 'required', 'on' => 'insert'],
            [['au_api_password'], 'string', 'min' => 7],

            [['au_project_id'], 'integer'],

            [['au_enabled'], 'boolean'],
            [['au_updated_dt'], 'safe'],
            [['au_updated_user_id'], 'default', 'value' => null],
            [['au_updated_user_id'], 'integer'],
            [['au_name', 'au_api_username', 'au_api_password'], 'string', 'max' => 100],
            [['au_email'], 'string', 'max' => 160],
            [['au_api_username'], 'unique'],
            //[['au_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['au_updated_user_id' => 'id']],
            [['au_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['au_project_id' => 'id']],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'au_id' => 'ID',
            'au_name' => 'Name',
            'au_api_username' => 'Api Username',
            'au_api_password' => 'Api Password',
            'au_email' => 'Email',
            'au_enabled' => 'Enabled',
            'au_updated_dt' => 'Updated Date',
            'au_updated_user_id' => 'Updated User',
            'au_project_id'     => 'Project'
        ];
    }


    public function getAuProject()
    {
        return $this->hasOne(Project::class, ['id' => 'au_project_id']);
    }


    /**
     * {@inheritdoc}
     * @return ApiUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ApiUserQuery(get_called_class());
    }

    /**
     * @return array
     */
    public static function getList()
    {
        return ArrayHelper::map(self::find()->where(['au_enabled' => true])->orderBy(['au_id' => SORT_ASC])->asArray()->all(),
            'au_id', 'au_name');
    }

    /**
     * @inheritdoc
     */

    public static function findIdentity($id)
    {
        if (Yii::$app->getSession()->has('user-'.$id)) {
            return new self(Yii::$app->getSession()->get('user-'.$id));
        }
        else {
            return static::findOne(['au_id' => $id]);
        }
    }


    public static function findIdentityByAccessToken($token, $type = null)
    {
        //return static::findOne(['access_token' => $token]);
        //echo base64_decode($token); exit;
        return static::findOne(['au_access_token' => $token]);
        //auth_key
        //throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['au_api_username' => $username]);
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
    public function getAuthKey()
    {
        return ''; //$this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return sha1($password) == $this->au_api_password ? TRUE : FALSE;
        //Yii::$app->security->validatePassword($password, $this->ac_api_password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->au_api_password = sha1($password); //Yii::$app->security->generatePasswordHash($password);
    }  
}
