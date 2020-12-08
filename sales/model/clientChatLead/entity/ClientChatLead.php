<?php

namespace sales\model\clientChatLead\entity;

use common\models\Lead;
use sales\model\clientChat\entity\ClientChat;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class ClientChatLead
 *
 * @property int $ccl_chat_id
 * @property int $ccl_lead_id
 * @property string $ccl_created_dt
 *
 * @property Lead $lead
 * @property ClientChat $chat
 */
class ClientChatLead extends ActiveRecord
{
    public function rules(): array
    {
        return [
            ['ccl_chat_id', 'integer'],
            ['ccl_chat_id', 'required'],
            ['ccl_chat_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChat::class, 'targetAttribute' => ['ccl_chat_id' => 'cch_id']],

            ['ccl_lead_id', 'integer'],
            ['ccl_lead_id', 'required'],
            ['ccl_lead_id', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['ccl_lead_id' => 'id']],

            [['ccl_chat_id', 'ccl_lead_id'], 'unique', 'targetAttribute' => ['ccl_chat_id', 'ccl_lead_id']],

            ['ccl_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function getChat(): ActiveQuery
    {
        return $this->hasOne(ClientChat::class, ['cch_id' => 'ccl_chat_id']);
    }

    public function getLead(): ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'ccl_lead_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ccl_chat_id' => 'Chat',
            'ccl_lead_id' => 'Lead',
            'ccl_created_dt' => 'Created Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'client_chat_lead';
    }

    public static function create(int $chatId, int $leadId, \DateTimeImmutable $time): self
    {
        $model = new self();
        $model->ccl_chat_id = $chatId;
        $model->ccl_lead_id = $leadId;
        $model->ccl_created_dt = $time->format('Y-m-d H:i:s');
        return $model;
    }
}
