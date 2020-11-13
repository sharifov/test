<?php

namespace sales\model\clientChatChannelTransfer\entity;

use common\models\Employee;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%client_chat_channel_transfer}}".
 *
 * @property int $cctr_from_ccc_id
 * @property int $cctr_to_ccc_id
 * @property int|null $cctr_created_user_id
 * @property string|null $cctr_created_dt
 *
 * @property Employee $createdUser
 * @property ClientChatChannel $from
 * @property ClientChatChannel $to
 */
class ClientChatChannelTransfer extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [
                ['cctr_from_ccc_id', 'cctr_to_ccc_id'],
                'unique',
                'targetAttribute' => ['cctr_from_ccc_id', 'cctr_to_ccc_id']
            ],

            ['cctr_from_ccc_id', 'required'],
            ['cctr_from_ccc_id', 'integer'],
            ['cctr_from_ccc_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            [
                'cctr_from_ccc_id',
                'exist',
                'skipOnError' => true,
                'targetClass' => ClientChatChannel::class,
                'targetAttribute' => ['cctr_from_ccc_id' => 'ccc_id']
            ],

            ['cctr_to_ccc_id', 'required'],
            ['cctr_to_ccc_id', 'integer'],
            ['cctr_to_ccc_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            [
                'cctr_to_ccc_id',
                'exist',
                'skipOnError' => true,
                'targetClass' => ClientChatChannel::class,
                'targetAttribute' => ['cctr_to_ccc_id' => 'ccc_id']
            ],
            ['cctr_to_ccc_id', 'validateToProject', 'skipOnError' => true],
        ];
    }

    public function validateToProject(): void
    {
        if ($this->hasErrors('cctr_from_ccc_id')) {
            return;
        }
        $fromProjectId = $this->from->ccc_project_id;
        $toProject = $this->to->ccc_project_id;
        if ($fromProjectId !== $toProject) {
            $this->addError(
                'cctr_to_ccc_id',
                'Different project From channel(' . $this->from->cccProject->name . ') and To channel(' . $this->to->cccProject->name . ')'
            );
        }
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cctr_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cctr_created_user_id'],
                ]
            ],
        ];
    }

    public function getCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cctr_created_user_id']);
    }

    public function getFrom(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientChatChannel::class, ['ccc_id' => 'cctr_from_ccc_id']);
    }

    public function getTo(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientChatChannel::class, ['ccc_id' => 'cctr_to_ccc_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cctr_from_ccc_id' => 'From Channel',
            'cctr_to_ccc_id' => 'To Channel',
            'cctr_created_user_id' => 'Created User',
            'cctr_created_dt' => 'Created Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%client_chat_channel_transfer}}';
    }
}
