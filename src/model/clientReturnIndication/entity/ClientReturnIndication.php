<?php

namespace src\model\clientReturnIndication\entity;

use common\models\Client;
use common\models\Employee;
use common\models\query\ClientQuery;
use common\models\query\EmployeeQuery;
use Yii;

/**
 * This is the model class for table "client_return_indication".
 *
 * @property int $cri_client_id
 * @property int $cri_user_id
 * @property int $cri_type_id
 * @property string|null $cri_created_dt
 * @property string|null $cri_updated_dt
 *
 * @property Client $client
 * @property Employee $user
 */
class ClientReturnIndication extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'client_return_indication';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['cri_client_id', 'cri_user_id', 'cri_type_id'], 'required'],
            [['cri_client_id', 'cri_user_id', 'cri_type_id'], 'integer'],
            [['cri_created_dt', 'cri_updated_dt'], 'safe'],
            [['cri_client_id', 'cri_user_id'], 'unique', 'targetAttribute' => ['cri_client_id', 'cri_user_id']],
            [['cri_client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['cri_client_id' => 'id']],
            [['cri_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cri_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'cri_client_id' => 'Client ID',
            'cri_user_id' => 'User ID',
            'cri_type_id' => 'Type',
            'cri_created_dt' => 'Created Dt',
            'cri_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery|ClientQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'cri_client_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'cri_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return Scopes the active query used by this AR class.
     */
    public static function find()
    {
        return new Scopes(get_called_class());
    }

    public static function create(int $clientId, int $userId): self
    {
        $self = new self();
        $self->cri_client_id = $clientId;
        $self->cri_user_id = $userId;
        return $self;
    }

    public static function createReturnCustomer(int $clientId, int $userId): self
    {
        $self = self::create($clientId, $userId);
        $self->cri_type_id = ClientReturnIndicationType::RETURN_CUSTOMER;
        return $self;
    }
}
