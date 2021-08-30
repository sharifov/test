<?php

namespace sales\model\clientChatDataRequest\entity;

use sales\model\clientChat\entity\ClientChat;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "client_chat_data_request".
 *
 * @property int $ccdr_id
 * @property int $ccdr_chat_id
 * @property string|null $ccdr_data_json
 * @property string|null $ccdr_created_dt
 *
 * @property ClientChat $ccdrChat
 */
class ClientChatDataRequest extends \yii\db\ActiveRecord
{
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ccdr_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_chat_data_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ccdr_chat_id'], 'required'],
            [['ccdr_chat_id'], 'integer'],
            [['ccdr_data_json', 'ccdr_created_dt'], 'safe'],
            [['ccdr_chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientChat::class, 'targetAttribute' => ['ccdr_chat_id' => 'cch_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ccdr_id' => Yii::t('app', 'ID'),
            'ccdr_chat_id' => Yii::t('app', 'Chat ID'),
            'ccdr_data_json' => Yii::t('app', 'Data Json'),
            'ccdr_created_dt' => Yii::t('app', 'Created Dt'),
        ];
    }

    /**
     * Gets query for [[CcdrChat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCcdrChat()
    {
        return $this->hasOne(ClientChat::class, ['cch_id' => 'ccdr_chat_id']);
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
