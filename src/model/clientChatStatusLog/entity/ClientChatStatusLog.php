<?php

namespace src\model\clientChatStatusLog\entity;

use common\models\Employee;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChatChannel\entity\ClientChatChannel;
use yii\helpers\Html;

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
 * @property int $csl_action_type
 * @property string|null $csl_rid
 *
 * @property ClientChat $cslCch
 * @property Employee $cslOwner
 * @property ClientChatChannel $cslPrevChannel
 * @property Employee $cslUser
 */
class ClientChatStatusLog extends \yii\db\ActiveRecord
{
    public const ACTION_OPEN = 1;
    public const ACTION_OPEN_BY_AGENT = 2;
    public const ACTION_TRANSFER = 3;
    public const ACTION_ACCEPT_TRANSFER = 4;
    public const ACTION_CANCEL_TRANSFER_BY_AGENT = 5;
    public const ACTION_CANCEL_TRANSFER_BY_SYSTEM = 6;
    public const ACTION_CLOSE = 7;
    public const ACTION_AUTO_CLOSE = 8;
    public const ACTION_MULTIPLE_UPDATE = 9;
    public const ACTION_HOLD = 10;
    public const ACTION_REVERT_TO_PROGRESS = 11;
    public const ACTION_TAKE = 12;
    public const ACTION_AUTO_IDLE = 13;
    public const ACTION_AUTO_REVERT_TO_PROGRESS = 14;
    public const ACTION_REOPEN = 15;
    public const ACTION_TIMEOUT_FINISH = 16;
    public const ACTION_CHAT_ACCEPT = 17;
    public const ACTION_AUTO_RETURN = 18;
    public const ACTION_AUTO_REOPEN = 19;
    public const ACTION_MULTIPLE_UPDATE_CLOSE = 20;
    public const ACTION_MULTIPLE_TAKE = 21;
    public const ACTION_MULTIPLE_ACCEPT = 22;
    public const ACTION_MULTIPLE_UPDATE_STATUS = 23;
    public const ACTION_PENDING_BY_DISTRIBUTION_LOGIC = 24;
    public const ACTION_VISITOR_ENABLED_SUBSCRIPTION = 25;
    public const ACTION_DIRECT_TRANSFER = 26;

    private const ACTION_LIST = [
        self::ACTION_OPEN => 'Open By Client',
        self::ACTION_OPEN_BY_AGENT => 'Open By Agent',
        self::ACTION_TRANSFER => 'Transfer',
        self::ACTION_ACCEPT_TRANSFER => 'Accept Transfer',
        self::ACTION_CANCEL_TRANSFER_BY_AGENT => 'Cancel Transfer By Agent',
        self::ACTION_CANCEL_TRANSFER_BY_SYSTEM => 'Cancel Transfer By System',
        self::ACTION_CLOSE => 'Close',
        self::ACTION_AUTO_CLOSE => 'Auto Close',
        self::ACTION_MULTIPLE_UPDATE => 'Multiple Update',
        self::ACTION_HOLD => 'Hold',
        self::ACTION_REVERT_TO_PROGRESS => 'Revert to progress',
        self::ACTION_TAKE => 'Take',
        self::ACTION_AUTO_IDLE => 'Auto Idle',
        self::ACTION_AUTO_REVERT_TO_PROGRESS => 'Auto Revert to progress',
        self::ACTION_REOPEN => 'Reopen',
        self::ACTION_TIMEOUT_FINISH => 'Timeout Finish',
        self::ACTION_CHAT_ACCEPT => 'Chat Accept',
        self::ACTION_AUTO_RETURN => 'Auto Return',
        self::ACTION_AUTO_REOPEN => 'Auto Reopen',
        self::ACTION_MULTIPLE_UPDATE_CLOSE => 'Multiple Update Close',
        self::ACTION_MULTIPLE_TAKE => 'Multiple Update Take',
        self::ACTION_MULTIPLE_ACCEPT => 'Multiple Update Accept',
        self::ACTION_MULTIPLE_UPDATE_STATUS => 'Multiple Update Status',
        self::ACTION_PENDING_BY_DISTRIBUTION_LOGIC => 'Distribution logic',
        self::ACTION_VISITOR_ENABLED_SUBSCRIPTION => 'Visitor enabled subscription',
        self::ACTION_DIRECT_TRANSFER => 'Direct transfer',
    ];

    private const ACTION_LABEL_LIST = [
        self::ACTION_OPEN => 'badge badge-info',
        self::ACTION_OPEN_BY_AGENT => 'badge badge-info',
        self::ACTION_TRANSFER => 'badge badge-yellow',
        self::ACTION_ACCEPT_TRANSFER => 'badge badge-yellow',
        self::ACTION_CANCEL_TRANSFER_BY_AGENT => 'badge badge-yellow',
        self::ACTION_CANCEL_TRANSFER_BY_SYSTEM => 'badge badge-yellow',
        self::ACTION_CLOSE => 'badge badge-red',
        self::ACTION_AUTO_CLOSE => 'badge badge-red',
        self::ACTION_MULTIPLE_UPDATE => 'badge badge-awake',
        self::ACTION_HOLD => 'badge badge-info',
        self::ACTION_REVERT_TO_PROGRESS => 'badge badge-info',
        self::ACTION_TAKE => 'badge badge-info',
        self::ACTION_AUTO_IDLE => 'badge badge-info',
        self::ACTION_AUTO_REVERT_TO_PROGRESS => 'badge badge-info',
        self::ACTION_REOPEN => 'badge badge-info',
        self::ACTION_TIMEOUT_FINISH => 'badge badge-info',
        self::ACTION_CHAT_ACCEPT => 'badge badge-success',
        self::ACTION_AUTO_RETURN => 'badge badge-info',
        self::ACTION_AUTO_REOPEN => 'badge badge-info',
        self::ACTION_MULTIPLE_UPDATE_CLOSE => 'badge badge-red',
        self::ACTION_MULTIPLE_TAKE => 'badge badge-info',
        self::ACTION_MULTIPLE_UPDATE_STATUS => 'badge badge-yellow',
        self::ACTION_PENDING_BY_DISTRIBUTION_LOGIC => 'badge badge-yellow',
    ];

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

            ['csl_action_type', 'integer'],
            ['csl_action_type', 'in', 'range' => array_keys(self::getActionList())],

            ['csl_user_id', 'integer'],
            ['csl_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['csl_user_id' => 'id']],

            ['csl_rid', 'string', 'max' => 150],
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
            'csl_action_type' => 'Action Type',
            'csl_rid' => 'Room Id',
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
        int $actionType,
        ?string $rid
    ): self {
        $status = new self();
        $status->csl_cch_id = $chatId;
        $status->csl_from_status = $fromStatus;
        $status->csl_to_status = $toStatus;
        $status->csl_owner_id = $ownerId;
        $status->csl_user_id = $creatorId;
        $status->csl_prev_channel_id = $channelId;
        $status->csl_action_type = $actionType;
        $status->csl_rid = $rid;
        $status->csl_start_dt = date('Y-m-d H:i:s');
        return $status;
    }

    public static function tableName(): string
    {
        return 'client_chat_status_log';
    }

    public static function getActionList(): array
    {
        return self::ACTION_LIST;
    }

    public static function getActionName(int $id): ?string
    {
        return self::getActionList()[$id] ?? null;
    }

    public static function getActionLabelClass(int $id): string
    {
        return self::ACTION_LABEL_LIST[$id] ?? 'badge badge-white';
    }

    public static function getActionLabel(?int $id): ?string
    {
        return $id ? Html::tag('span', self::getActionName($id), ['class' => self::getActionLabelClass($id)]) : null;
    }
}
