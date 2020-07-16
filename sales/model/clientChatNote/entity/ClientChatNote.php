<?php

namespace sales\model\clientChatNote\entity;

use common\models\Employee;
use sales\model\clientChat\entity\ClientChat;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "client_chat_note".
 *
 * @property int $ccn_id
 * @property int|null $ccn_chat_id
 * @property int|null $ccn_user_id
 * @property string|null $ccn_note
 * @property int|null $ccn_deleted
 * @property string|null $ccn_created_dt
 * @property string|null $ccn_updated_dt
 *
 * @property ClientChat $chat
 * @property Employee $user
 */
class ClientChatNote extends ActiveRecord
{

    public static function tableName(): string
    {
        return 'client_chat_note';
    }

    public function rules(): array
    {
        return [
            [['ccn_chat_id', 'ccn_user_id', 'ccn_deleted'], 'integer'],
            [['ccn_note'], 'string'],
            [['ccn_created_dt', 'ccn_updated_dt'], 'safe'],
            [['ccn_chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientChat::class, 'targetAttribute' => ['ccn_chat_id' => 'cch_id']],
            [['ccn_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ccn_user_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'ccn_id' => 'ID',
            'ccn_chat_id' => 'Chat ID',
            'ccn_user_id' => 'User ID',
            'ccn_note' => 'Note',
            'ccn_deleted' => 'Deleted',
            'ccn_created_dt' => 'Created Dt',
            'ccn_updated_dt' => 'Updated Dt',
        ];
    }

    public function getChat(): ActiveQuery
    {
        return $this->hasOne(ClientChat::class, ['cch_id' => 'ccn_chat_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ccn_user_id']);
    }

    public static function find(): clientChatNoteScopes
    {
        return new clientChatNoteScopes(static::class);
    }
}
