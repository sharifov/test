<?php

namespace sales\model\authClient\entity;

use common\models\Employee;
use common\models\query\EmployeeQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "auth_client".
 *
 * @property int $ac_id
 * @property int $ac_user_id
 * @property int $ac_source
 * @property string $ac_source_id
 * @property string|null $ac_email
 * @property string|null $ac_ip
 * @property string|null $ac_useragent
 * @property string|null $ac_created_dt
 *
 * @property Employee $user
 */
class AuthClient extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_client';
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ac_created_dt'],
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
            [['ac_user_id', 'ac_source', 'ac_source_id'], 'required'],
            [['ac_user_id', 'ac_source'], 'integer'],
            [['ac_source'], 'in', 'range' => array_keys(AuthClientSources::getList())],
            [['ac_created_dt'], 'safe'],
            [['ac_source_id', 'ac_useragent'], 'string', 'max' => 255],
            [['ac_email'], 'string', 'max' => 100],
            [['ac_ip'], 'string', 'max' => 20],
            [['ac_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ac_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ac_id' => 'ID',
            'ac_user_id' => 'User ID',
            'ac_source' => 'Source',
            'ac_source_id' => 'Source ID',
            'ac_email' => 'Email',
            'ac_ip' => 'Ip',
            'ac_useragent' => 'Useragent',
            'ac_created_dt' => 'Created Dt',
        ];
    }

    /**
     * Gets query for [[AcUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'ac_user_id']);
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
        $self->ac_user_id = $userId;
        $self->ac_source_id = $sourceId;
        $self->ac_email = $email;
        $self->ac_ip = $ip;
        $self->ac_useragent = $userAgent;
        return $self;
    }

    public function setGoogleSource(): void
    {
        $this->ac_source = AuthClientSources::GOOGLE;
    }
}
