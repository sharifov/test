<?php

namespace sales\model\clientChatCase\entity;

use sales\entities\cases\Cases;
use sales\model\clientChat\entity\ClientChat;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class ClientChatCase
 *
 * @property int $cccs_chat_id
 * @property int $cccs_case_id
 * @property string $cccs_created_dt
 *
 * @property ClientChat $chat
 * @property Cases $case
 */
class ClientChatCase extends ActiveRecord
{
    public function rules(): array
    {
        return [
            ['cccs_chat_id', 'integer'],
            ['cccs_chat_id', 'required'],
            ['cccs_chat_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChat::class, 'targetAttribute' => ['cccs_chat_id' => 'cch_id']],

            ['cccs_case_id', 'integer'],
            ['cccs_case_id', 'required'],
            ['cccs_case_id', 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['cccs_case_id' => 'cs_id']],

            [['cccs_chat_id', 'cccs_case_id'], 'unique', 'targetAttribute' => ['cccs_chat_id', 'cccs_case_id']],

            ['cccs_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function getChat(): ActiveQuery
    {
        return $this->hasOne(ClientChat::class, ['cch_id' => 'cccs_chat_id']);
    }

    public function getCase(): ActiveQuery
    {
        return $this->hasOne(Cases::class, ['cs_id' => 'cccs_case_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cccs_chat_id' => 'Chat',
            'cccs_case_id' => 'Case',
            'cccs_created_dt' => 'Created Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'client_chat_case';
    }

    public static function create(int $chatId, int $caseId, \DateTimeImmutable $time): self
	{
		$model = new self();
		$model->cccs_chat_id = $chatId;
		$model->cccs_case_id = $caseId;
		$model->cccs_created_dt = $time->format('Y-m-d H:i:s');
		return $model;
	}
}
