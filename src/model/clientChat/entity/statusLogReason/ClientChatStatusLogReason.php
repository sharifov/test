<?php

namespace src\model\clientChat\entity\statusLogReason;

use src\model\clientChat\entity\actionReason\ClientChatActionReason;
use src\model\clientChatStatusLog\entity\ClientChatStatusLog;
use Yii;

/**
 * This is the model class for table "client_chat_status_log_reason".
 *
 * @property int $cslr_id
 * @property int|null $cslr_status_log_id
 * @property int|null $cslr_action_reason_id
 * @property string|null $cslr_comment
 *
 * @property ClientChatActionReason $cslrActionReason
 * @property ClientChatStatusLog $cslrStatusLog
 */
class ClientChatStatusLogReason extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['cslr_action_reason_id', 'integer'],
            ['cslr_action_reason_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChatActionReason::class, 'targetAttribute' => ['cslr_action_reason_id' => 'ccar_id']],

            ['cslr_comment', 'string', 'max' => 100],

            ['cslr_status_log_id', 'integer'],
            ['cslr_status_log_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChatStatusLog::class, 'targetAttribute' => ['cslr_status_log_id' => 'csl_id']],
        ];
    }

    public function getCslrActionReason(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientChatActionReason::class, ['ccar_id' => 'cslr_action_reason_id']);
    }

    public function getCslrStatusLog(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientChatStatusLog::class, ['csl_id' => 'cslr_status_log_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cslr_id' => 'ID',
            'cslr_status_log_id' => 'Status Log ID',
            'cslr_action_reason_id' => 'Action Reason ID',
            'cslr_comment' => 'Comment',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'client_chat_status_log_reason';
    }

    public static function create(int $statusLog, int $reasonId, ?string $comment): self
    {
        $model = new self();
        $model->cslr_status_log_id = $statusLog;
        $model->cslr_action_reason_id = $reasonId;
        $model->cslr_comment = $comment;
        return $model;
    }
}
