<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "client_phone".
 *
 * @property int $id
 * @property int $client_id
 * @property string $phone
 * @property string $created
 * @property string $updated
 * @property string $comments
 *
 * @property Client $client
 */
class ClientPhone extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_phone';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone'], 'required'],
            [['client_id'], 'integer'],
            [['created', 'updated', 'comments'], 'safe'],
            [['phone'], 'string', 'max' => 100],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['client_id' => 'id']],
            [['phone', 'client_id'], 'unique', 'targetAttribute' => ['phone', 'client_id']]
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
            'phone' => 'Phone',
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
        $this->phone = str_replace('-', '', $this->phone);
        $this->phone = str_replace(' ', '', $this->phone);
        $this->updated = date('Y-m-d H:i:s');
        return parent::beforeValidate();
    }

    /**
     * @param string $phoneNumber
     * @return null|string|string[]
     */
    public static function clearNumber(string $phoneNumber = '')
    {
        $phoneNumber = preg_replace('~[^0-9\+]~', '', $phoneNumber);
        if(isset($phoneNumber[0])) {
            $phoneNumber = ($phoneNumber[0] === '+' ? '+' : '') . str_replace('+', '', $phoneNumber);
        }
        return $phoneNumber;
    }
}
