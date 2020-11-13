<?php

namespace sales\model\clientChatNote\entity;

use common\models\Employee;
use sales\model\clientChat\entity\ClientChat;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

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
        return '{{%client_chat_note}}';
    }

    public function rules(): array
    {
        return [
            [['ccn_note', 'ccn_chat_id'], 'required'],
            [['ccn_chat_id', 'ccn_user_id', 'ccn_deleted'], 'integer'],
            [['ccn_note'], 'string', 'min' => 1, 'max' => 1000],
            [['ccn_created_dt', 'ccn_updated_dt'], 'safe'],
            [['ccn_chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientChat::class, 'targetAttribute' => ['ccn_chat_id' => 'cch_id']],
            [['ccn_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ccn_user_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'ccn_id' => 'ID',
            'ccn_chat_id' => 'Chat',
            'ccn_user_id' => 'User',
            'ccn_note' => 'Note',
            'ccn_deleted' => 'Deleted',
            'ccn_created_dt' => 'Created',
            'ccn_updated_dt' => 'Updated',
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ccn_created_dt', 'ccn_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ccn_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ]
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getChat(): ActiveQuery
    {
        return $this->hasOne(ClientChat::class, ['cch_id' => 'ccn_chat_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ccn_user_id']);
    }

    public static function find(): ClientChatNoteScopes
    {
        return new ClientChatNoteScopes(static::class);
    }

    public static function create(int $chatId, int $userId, string $note): ClientChatNote
	{
		$model = new self();
		$model->ccn_chat_id = $chatId;
		$model->ccn_user_id = $userId;
		$model->ccn_note = $note;
		return $model;
	}
}
