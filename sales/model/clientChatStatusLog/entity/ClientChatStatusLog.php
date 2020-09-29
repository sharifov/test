<?php

namespace sales\model\clientChatStatusLog\entity;

use common\models\Employee;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatChannel\entity\ClientChatChannel;

/**
 * This is the model class for table "client_chat_status_log".
 *
 * @property int $csl_id
 * @property int $csl_cch_id
 * @property int|null $csl_from_status
 * @property int|null $csl_to_status
 * @property string|null $csl_start_dt
 * @property string|null $csl_end_dt
 * @property int|null $csl_owner_id
 * @property string|null $csl_description
 * @property int|null $csl_user_id
 * @property int|null $csl_prev_channel_id
 *
 * @property ClientChat $cslCch
 * @property Employee $cslOwner
 * @property ClientChatChannel $cslPrevChannel
 * @property Employee $cslUser
 */
class ClientChatStatusLog extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['csl_cch_id', 'required'],
            ['csl_cch_id', 'integer'],
            ['csl_cch_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChat::class, 'targetAttribute' => ['csl_cch_id' => 'cch_id']],

            ['csl_description', 'string', 'max' => 255],

            ['csl_end_dt', 'safe'],

            ['csl_from_status', 'integer'],

            ['csl_owner_id', 'integer'],
            ['csl_owner_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['csl_owner_id' => 'id']],

			['csl_prev_channel_id', 'integer'],
			['csl_prev_channel_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChatChannel::class, 'targetAttribute' => ['csl_prev_channel_id' => 'ccc_id']],

            ['csl_start_dt', 'safe'],

            ['csl_to_status', 'integer'],

			['csl_user_id', 'integer'],
			['csl_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['csl_user_id' => 'id']],
		];
    }

    public function getCslCch(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientChat::class, ['cch_id' => 'csl_cch_id']);
    }

    public function getCslOwner(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'csl_owner_id']);
    }

	public function getCslPrevChannel(): \yii\db\ActiveQuery
	{
		return $this->hasOne(ClientChatChannel::class, ['ccc_id' => 'csl_prev_channel_id']);
	}
	
	public function getCslUser(): \yii\db\ActiveQuery
	{
		return $this->hasOne(Employee::class, ['id' => 'csl_user_id']);
	}

    public function attributeLabels(): array
    {
        return [
            'csl_id' => 'ID',
            'csl_cch_id' => 'Client Chat ID',
            'csl_from_status' => 'From Status',
            'csl_to_status' => 'To Status',
            'csl_start_dt' => 'Start Dt',
            'csl_end_dt' => 'End Dt',
            'csl_owner_id' => 'Owner ID',
            'csl_description' => 'Description',
			'csl_user_id' => 'User ID',
			'csl_prev_channel_id' => 'Prev Channel ID',
        ];
    }

	public function end(): void
	{
		$this->csl_end_dt = date('Y-m-d H:i:s');
	}

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function create(
		int $chatId,
		?int $fromStatus,
		int $toStatus,
		?int $ownerId,
		?int $creatorId,
		?int $channelId,
		?string $description = ''
	): self
	{
		$status = new self();
		$status->csl_cch_id = $chatId;
		$status->csl_from_status = $fromStatus;
		$status->csl_to_status = $toStatus;
		$status->csl_owner_id = $ownerId;
		$status->csl_user_id = $creatorId;
		$status->csl_prev_channel_id = $channelId;
		$status->csl_description = $description;
		$status->csl_start_dt = date('Y-m-d H:i:s');
		return $status;
	}

    public static function tableName(): string
    {
        return 'client_chat_status_log';
    }
}
