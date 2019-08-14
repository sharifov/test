<?php

namespace common\models;

use sales\entities\AggregateRoot;
use sales\entities\EventTrait;
use Yii;

/**
 * This is the model class for table "client_email".
 *
 * @property int $id
 * @property int $client_id
 * @property string $email
 * @property string $created
 * @property string $updated
 * @property string $comments
 *
 * @property Client $client
 */
class ClientEmail extends \yii\db\ActiveRecord implements AggregateRoot
{

    use EventTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_email';
    }

    /**
     * @param string $email
     * @param int $clientId
     * @return ClientEmail
     */
    public static function create(string $email, int $clientId): self
    {
        $clientEmail = new static();
        $clientEmail->email = $email;
        $clientEmail->client_id = $clientId;
        return $clientEmail;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['email'], 'email'],
            [['client_id'], 'integer'],
            [['created', 'updated', 'comments'], 'safe'],
            [['email'], 'string', 'max' => 100],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['client_id' => 'id']],
            [['email', 'client_id'], 'unique', 'targetAttribute' => ['email', 'client_id']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'email' => 'Email',
            'created' => 'Created',
            'updated' => 'Updated',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }

    public function beforeValidate()
    {
        $this->updated = date('Y-m-d H:i:s');

        if (strpos($this->email, 'wowfare') !== false) {
            $this->addError('email', 'Email is invalid!');
        }

        return parent::beforeValidate();
    }
}
