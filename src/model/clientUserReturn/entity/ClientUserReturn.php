<?php

namespace src\model\clientUserReturn\entity;

use common\models\Client;
use common\models\Employee;
use common\models\query\EmployeeQuery;
use src\repositories\client\ClientsQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "client_user_return".
 *
 * @property int $cur_client_id
 * @property int $cur_user_id
 * @property string|null $cur_created_dt
 *
 * @property Client $client
 * @property Employee $user
 */
class ClientUserReturn extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_user_return';
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cur_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cur_client_id', 'cur_user_id'], 'required'],
            [['cur_client_id', 'cur_user_id'], 'integer'],
            [['cur_created_dt'], 'safe'],
            [['cur_client_id', 'cur_user_id'], 'unique', 'targetAttribute' => ['cur_client_id', 'cur_user_id']],
            [['cur_client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['cur_client_id' => 'id']],
            [['cur_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cur_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cur_client_id' => 'Client ID',
            'cur_user_id' => 'User ID',
            'cur_created_dt' => 'Created Dt',
        ];
    }

    /**
     * Gets query for [[CurClient]].
     *
     * @return \yii\db\ActiveQuery|ClientsQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'cur_client_id']);
    }

    /**
     * Gets query for [[CurUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'cur_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return Scopes the active query used by this AR class.
     */
    public static function find()
    {
        return new Scopes(get_called_class());
    }
}
