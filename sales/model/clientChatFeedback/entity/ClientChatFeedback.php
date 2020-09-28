<?php

namespace sales\model\clientChatFeedback\entity;

use common\models\Client;
use sales\model\clientChat\entity\ClientChat;
use Yii;
use common\models\Employee;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 *
 * @property int $ccf_id
 * @property int $ccf_client_chat_id
 * @property int|null $ccf_user_id
 * @property int|null $ccf_client_id
 * @property int|null $ccf_rating
 * @property string|null $ccf_message
 * @property string|null $ccf_created_dt
 * @property string|null $ccf_updated_dt
 *
 * @property Client $client
 * @property ClientChat $clientChat
 * @property Employee $employee
 */
class ClientChatFeedback extends ActiveRecord
{
    public const RATING_LIST = [
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5,
    ];

    public static function tableName(): string
    {
        return '{{%client_chat_feedback}}';
    }

    public function rules(): array
    {
        return [
            [['ccf_client_chat_id', 'ccf_user_id', 'ccf_client_id'], 'required'],
            [['ccf_client_chat_id', 'ccf_user_id', 'ccf_client_id'], 'integer'],
            [['ccf_message'], 'string'],
            [['ccf_created_dt', 'ccf_updated_dt'], 'safe'],
            [['ccf_client_chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientChat::class, 'targetAttribute' => ['ccf_client_chat_id' => 'cch_id']],
            [['ccf_client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['ccf_client_id' => 'id']],
            [['ccf_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ccf_user_id' => 'id']],

            [['ccf_rating'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            [['ccf_rating'], 'integer', 'min' => 1, 'max' => 5],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'ccf_id' => 'ID',
            'ccf_client_chat_id' => 'Client Chat ID',
            'ccf_user_id' => 'Employee ID',
            'ccf_client_id' => 'Client ID',
            'ccf_rating' => 'Rating',
            'ccf_message' => 'Message',
            'ccf_created_dt' => 'Created Dt',
            'ccf_updated_dt' => 'Updated Dt',
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ccf_created_dt', 'ccf_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ccf_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ]
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getClient(): ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'ccf_client_id']);
    }

    public function getClientChat(): ActiveQuery
    {
        return $this->hasOne(ClientChat::class, ['cch_id' => 'ccf_client_chat_id']);
    }

    public function getEmployee(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ccf_user_id']);
    }

    public static function find(): clientChatFeedbackScopes
    {
        return new clientChatFeedbackScopes(static::class);
    }
}
