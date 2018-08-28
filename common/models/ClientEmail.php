<?php

namespace common\models;

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
class ClientEmail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_email';
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
