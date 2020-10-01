<?php

namespace sales\model\clientChatHold\entity;

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "client_chat_hold".
 *
 * @property int $cchd_id
 * @property int $cchd_cch_id
 * @property int|null $cchd_cch_status_log_id
 * @property string $cchd_deadline_dt
 *
 * @property ClientChat $clientChat
 * @property ClientChatStatusLog $clientChatStatusLog
 */
class ClientChatHold extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%client_chat_hold}}';
    }

    public function rules(): array
    {
        return [
            [['cchd_cch_id', 'cchd_cch_status_log_id', 'cchd_deadline_dt'], 'required'],
            [['cchd_cch_id', 'cchd_cch_status_log_id'], 'integer'],
            [['cchd_deadline_dt'], 'safe'],
            [['cchd_cch_status_log_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientChatStatusLog::class, 'targetAttribute' => ['cchd_cch_status_log_id' => 'csl_id']],
            [['cchd_cch_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientChat::class, 'targetAttribute' => ['cchd_cch_id' => 'cch_id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'cchd_id' => 'ID',
            'cchd_cch_id' => 'Client Chat ID',
            'cchd_cch_status_log_id' => 'Chat Status Log ID',
            'cchd_deadline_dt' => 'Deadline Dt',
        ];
    }

    public function getClientChat(): ActiveQuery
    {
        return $this->hasOne(ClientChat::class, ['cch_id' => 'cchd_cch_id']);
    }

    public function getClientChatStatusLog(): ActiveQuery
    {
        return $this->hasOne(ClientChatStatusLog::class, ['csl_id' => 'cchd_cch_status_log_id']);
    }

    public static function find(): ClientChatHoldScopes
    {
        return new ClientChatHoldScopes(static::class);
    }
}
