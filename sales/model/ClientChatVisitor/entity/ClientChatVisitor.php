<?php

namespace sales\model\ClientChatVisitor\entity;

use common\models\Client;

/**
 * This is the model class for table "client_chat_visitor".
 *
 * @property int $ccv_id
 * @property int|null $ccv_client_id
 * @property string|null $ccv_visitor_rc_id
 *
 * @property Client $ccvClient
 */
class ClientChatVisitor extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['ccv_client_id', 'integer'],
            ['ccv_client_id', 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['ccv_client_id' => 'id']],

            ['ccv_visitor_rc_id', 'string', 'max' => 50],
			['ccv_visitor_rc_id', 'unique'],
		];
    }

    public function getCcvClient(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'ccv_client_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ccv_id' => 'Ccv ID',
            'ccv_client_id' => 'Ccv Client ID',
            'ccv_visitor_rc_id' => 'Ccv Visitor Rc ID',
        ];
    }

    public static function tableName(): string
    {
        return 'client_chat_visitor';
    }

	public static function find(): Scopes
	{
		return new Scopes(static::class);
	}

	public static function create(int $clientId, string $visitorId): self
	{
		$_self = new self();
		$_self->ccv_client_id = $clientId;
		$_self->ccv_visitor_rc_id = $visitorId;
		return $_self;
	}
}
