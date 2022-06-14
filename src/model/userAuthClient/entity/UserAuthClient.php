<?php

namespace src\model\userAuthClient\entity;

use common\models\Employee;
use common\models\query\EmployeeQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "auth_client".
 *
 * @property int $uac_id
 * @property int $uac_user_id
 * @property int $uac_source
 * @property string $uac_source_id
 * @property string|null $uac_email
 * @property string|null $uac_ip
 * @property string|null $uac_useragent
 * @property string|null $uac_created_dt
 *
 * @property Employee $user
 */
class UserAuthClient extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_auth_client';
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['uac_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uac_user_id', 'uac_source', 'uac_source_id'], 'required'],
            [['uac_user_id', 'uac_source', 'uac_source_id'], 'unique', 'targetAttribute' => ['uac_user_id', 'uac_source', 'uac_source_id']],
            [['uac_user_id', 'uac_source'], 'integer'],
            [['uac_source'], 'in', 'range' => array_keys(UserAuthClientSources::getList())],
            [['uac_created_dt'], 'safe'],
            [['uac_source_id', 'uac_useragent'], 'string', 'max' => 255],
            [['uac_email'], 'string', 'max' => 100],
            [['uac_ip'], 'string', 'max' => 20],
            [['uac_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['uac_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'uac_id' => 'ID',
            'uac_user_id' => 'User ID',
            'uac_source' => 'Source',
            'uac_source_id' => 'Source ID',
            'uac_email' => 'Email',
            'uac_ip' => 'Ip',
            'uac_useragent' => 'Useragent',
            'uac_created_dt' => 'Created Dt',
        ];
    }

    /**
     * Gets query for [[AcUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'uac_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return Scopes the active query used by this AR class.
     */
    public static function find()
    {
        return new Scopes(get_called_class());
    }

    public static function create(
        int $userId,
        string $sourceId,
        string $email,
        string $ip,
        string $userAgent
    ): self {
        $self = new self();
        $self->uac_user_id = $userId;
        $self->uac_source_id = $sourceId;
        $self->uac_email = $email;
        $self->uac_ip = $ip;
        $self->uac_useragent = $userAgent;
        return $self;
    }

    public function setGoogleSource(): void
    {
        $this->uac_source = UserAuthClientSources::GOOGLE;
    }

    public function setMicrosoftSource(): void
    {
        $this->uac_source = UserAuthClientSources::MICROSOFT;
    }
}
