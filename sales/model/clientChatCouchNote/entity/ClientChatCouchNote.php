<?php

namespace sales\model\clientChatCouchNote\entity;

use common\models\Employee;
use sales\model\clientChat\entity\ClientChat;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "client_chat_couch_note".
 *
 * @property int $cccn_id
 * @property int|null $cccn_cch_id
 * @property string|null $cccn_rid
 * @property string|null $cccn_message
 * @property string|null $cccn_alias
 * @property int|null $cccn_created_user_id
 * @property string|null $cccn_created_dt
 *
 * @property ClientChat $clientChat
 * @property Employee $createdUser
 */
class ClientChatCouchNote extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'client_chat_couch_note';
    }

    public function rules(): array
    {
        return [
            [['cccn_rid', 'cccn_message'], 'required'],
            ['cccn_alias', 'string', 'max' => 50],

            ['cccn_cch_id', 'integer'],
            ['cccn_cch_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChat::class, 'targetAttribute' => ['cccn_cch_id' => 'cch_id']],

            ['cccn_created_dt', 'safe'],

            ['cccn_created_user_id', 'integer'],
            ['cccn_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cccn_created_user_id' => 'id']],

            ['cccn_rid', 'string', 'max' => 150],
            [['cccn_message'], 'string', 'max' => 500],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cccn_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'cccn_created_user_id',
                'updatedByAttribute' => false,
            ],
        ];
    }

    public function getClientChat(): ActiveQuery
    {
        return $this->hasOne(ClientChat::class, ['cch_id' => 'cccn_cch_id']);
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cccn_created_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cccn_id' => 'ID',
            'cccn_cch_id' => 'Chat ID',
            'cccn_rid' => 'Rid',
            'cccn_message' => 'Message',
            'cccn_alias' => 'Alias',
            'cccn_created_user_id' => 'Created User',
            'cccn_created_dt' => 'Created Dt',
        ];
    }

    public static function create(int $chatId, string $rid, string $alias, string $message): self
    {
        $model = new static();
        $model->cccn_cch_id = $chatId;
        $model->cccn_rid = $rid;
        $model->cccn_message = $message;
        $model->cccn_alias = $alias;
        return $model;
    }
}
