<?php

namespace src\model\clientChatFormResponse\entity;

use src\model\clientChat\entity\ClientChat;
use src\model\clientChatForm\entity\ClientChatForm;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Class ClientChatFormResponse
 * @package src\model\ClientChatFormResponse\entity
 *
 * @property int $ccfr_id
 * @property string $ccfr_uid
 * @property int $ccfr_client_chat_id
 * @property int $ccfr_form_id
 * @property string $ccfr_rc_created_dt
 * @property string|null $ccfr_created_dt
 * @property string|null $ccfr_value
 */
class ClientChatFormResponse extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%client_chat_form_response}}';
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ccfr_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ]
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['ccfr_uid', 'ccfr_client_chat_id', 'ccfr_form_id',  'ccfr_rc_created_dt'], 'required'],
            [['ccfr_uid'], 'string', 'max' => 64],
            [['ccfr_value'], 'string', 'max' => 255],
            [['ccfr_client_chat_id', 'ccfr_form_id'], 'integer'],
            ['ccfr_rc_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['ccfr_created_dt'], 'safe'],
            ['ccfr_client_chat_id', 'exist', 'targetClass' => ClientChat::class, 'targetAttribute' => 'cch_id', 'skipOnEmpty' => false],
            ['ccfr_form_id', 'exist', 'targetClass' => ClientChatForm::class, 'targetAttribute' => 'ccf_id', 'skipOnEmpty' => false]
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'ccfr_id' => 'ID',
            'ccfr_uid' => 'UID',
            'ccfr_client_chat_id' => 'Chat Id',
            'ccfr_form_id' => 'Client chat form id',
            'ccfr_rc_created_dt' => 'Created Dt in RocketChat',
            'ccfr_created_dt' => 'Created Dt',
            'ccfr_value' => 'Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientChat()
    {
        return $this->hasOne(ClientChat::class, ['cch_id' => 'ccfr_client_chat_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientChatForm()
    {
        return $this->hasOne(ClientChatForm::class, ['ccf_id' => 'ccfr_form_id']);
    }
}
