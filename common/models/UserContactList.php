<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_contact_list".
 *
 * @property int $ucl_user_id
 * @property int $ucl_client_id
 * @property string|null $ucl_title
 * @property string|null $ucl_description
 * @property string|null $ucl_created_dt
 * @property bool $ucl_favorite
 *
 * @property Client $client
 * @property \yii\db\ActiveQuery $employee
 * @property Employee $Employee
 */
class UserContactList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'user_contact_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ucl_user_id', 'ucl_client_id'], 'required'],
            [['ucl_user_id', 'ucl_client_id'], 'integer'],
            [['ucl_description'], 'string'],
            [['ucl_created_dt'], 'safe'],
            [['ucl_favorite'], 'boolean'],
            [['ucl_title'], 'string', 'max' => 100],
            [['ucl_user_id', 'ucl_client_id'], 'unique', 'targetAttribute' => ['ucl_user_id', 'ucl_client_id']],
            [['ucl_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ucl_user_id' => 'id']],
            [['ucl_client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['ucl_client_id' => 'id']],
        ];
    }

    public function behaviors(): array
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['ucl_created_dt'],
				],
				'value' => date('Y-m-d H:i:s')
			],
		];
	}

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ucl_user_id' => 'User ID',
            'ucl_client_id' => 'Client ID',
            'ucl_title' => 'Title',
            'ucl_description' => 'Description',
            'ucl_created_dt' => 'Created',
            'ucl_favorite' => 'Favorite'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'ucl_client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['id' => 'ucl_user_id']);
    }

    /**
     * @param int $userId
     * @param int $clientId
     * @return UserContactList|null
     */
    public static function getUserContact(int $userId, int $clientId): ?UserContactList
    {
        return self::findOne(['ucl_user_id' => $userId, 'ucl_client_id' => $clientId]);
    }
}
