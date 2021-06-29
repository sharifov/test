<?php

namespace sales\model\clientChatMessage\entity;

use DateTime;
use sales\entities\EventTrait;
use sales\model\clientChat\ClientChatPlatform;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatRequest\useCase\api\create\ClientChatRequestApiForm;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "client_chat_message".
 *
 * @property int $ccm_id
 * @property string $ccm_rid
 * @property int $ccm_cch_id
 * @property int|null $ccm_client_id
 * @property int|null $ccm_user_id
 * @property string $ccm_sent_dt
 * @property array $ccm_body
 * @property int $ccm_has_attachment
 * @property string $message
 * @property string $username
 * @property int|null $ccm_event
 * @property int $ccm_platform_id
 */
class ClientChatMessage extends \yii\db\ActiveRecord
{
    use EventTrait;

    public const BY_BOT = 'bot';
    public const BY_AGENT = 'agent';
    public const BY_CLIENT = 'client';
    public const BOT_EMAIL = 'bot@techork.com';

    private const EVENT_GUEST_UTTERED = ClientChatRequest::EVENT_GUEST_UTTERED;
    private const EVENT_AGENT_UTTERED = ClientChatRequest::EVENT_AGENT_UTTERED;

    private const EVENT_LIST = [
        self::EVENT_GUEST_UTTERED => self::EVENT_GUEST_UTTERED,
        self::EVENT_AGENT_UTTERED => self::EVENT_AGENT_UTTERED,
    ];

    public function isGuestUttered(): bool
    {
        return $this->ccm_event === self::EVENT_GUEST_UTTERED;
    }

    public function isAgentUttered(): bool
    {
        return $this->ccm_event === self::EVENT_AGENT_UTTERED;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_chat_message';
    }

    /**
     * @return object
     */
    public static function getDb()
    {
        return Yii::$app->get('db_postgres');
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ["ccm_id"];
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ccm_rid', 'ccm_sent_dt', 'ccm_body'], 'required'],
            [['ccm_client_id', 'ccm_user_id'], 'default', 'value' => null],
            [['ccm_client_id', 'ccm_user_id', 'ccm_has_attachment', 'ccm_cch_id'], 'integer'],
            [['ccm_sent_dt', 'ccm_body'], 'safe'],
            [['ccm_rid'], 'string', 'max' => 150],
            ['ccm_event', 'in', 'range' => array_keys(self::EVENT_LIST)],
            ['ccm_platform_id', 'integer'],
            ['ccm_platform_id', 'default', 'value' => ClientChatPlatform::getDefaultPlatform()],
            ['ccm_platform_id', 'validatePlatform']
        ];
    }


    /**
     * Create a partition table with indicated from and to date
     *
     * @param DateTime $partFromDateTime partition start date
     * @param DateTime $partToDateTime partition end date
     * @return string table_name created table
     */
    public static function createMonthlyPartition(DateTime $partFromDateTime, DateTime $partToDateTime): string
    {
        $db = self::getDb();
        $partTableName = self::tableName() . "_" . date_format($partFromDateTime, "Y_m");
        $cmd = $db->createCommand("create table " . $partTableName . " PARTITION OF " . self::tableName() .
            " FOR VALUES FROM ('" . date_format($partFromDateTime, "Y-m-d") . "') TO ('" . date_format($partToDateTime, "Y-m-d") . "')");
        $cmd->execute();
        return $partTableName;
    }

    /**
     * Calculate from and to dates from a given date.
     * Given date -> from = start of the month, to = next month start date
     *
     * @param DateTime $date partition start date
     * @return array DateTime table_name created table
     * @throws \RuntimeException any errors occurred during execution
     */
    public static function partitionDatesFrom(DateTime $date): array
    {
        $monthBegin = date('Y-m-d', strtotime(date_format($date, 'Y-m-1')));
        if (!$monthBegin) {
            throw new \RuntimeException("invalid partition start date");
        }

        $partitionStartDate = date_create_from_format('Y-m-d', $monthBegin);
        $partitionEndDate = date_create_from_format('Y-m-d', $monthBegin);

        date_add($partitionEndDate, date_interval_create_from_date_string("1 month"));

        return [$partitionStartDate, $partitionEndDate];
    }

    public static function createByApi(ClientChatRequestApiForm $form, int $event, int $platformId): ClientChatMessage
    {
        $message = new self();
        $message->ccm_rid = $form->data['rid'] ?? '';
        $date = new DateTime();
        $date->setTimestamp($form->data['timestamp'] / 1000);
        $message->ccm_sent_dt = $date->format('Y-m-d H:i:s');
        $message->ccm_body = $form->data;
        $message->ccm_event = $event;
        $message->ccm_platform_id = $platformId;

        if (array_key_exists('file', $form->data)) {
            $message->ccm_has_attachment = 1;
        }

        return $message;
    }

    public function assignToChat(int $chatId, ?int $clientId, ?int $ownerUserId): void
    {
        $this->ccm_cch_id = $chatId;
        $this->ccm_client_id = $clientId;
        $this->ccm_user_id = $ownerUserId;
    }

    public function isMessageFromClient(): bool
    {
        return $this->ccm_client_id && !$this->ccm_user_id;
    }

    public function isMessageFromBot(): bool
    {
        return (isset($this->ccm_body['agent']['email']) && $this->ccm_body['agent']['email'] === self::BOT_EMAIL);
    }

    public function getByType(): string
    {
        if ($this->isMessageFromClient()) {
            return self::BY_CLIENT;
        }
        if ($this->isMessageFromBot()) {
            return self::BY_BOT;
        }
        if (!empty($this->ccm_user_id)) {
            return self::BY_AGENT;
        }
        return 'undefined';
    }

    public function getMessage(): string
    {
        return $this->ccm_body['msg'] ?? '';
    }

    public function getUsername(): string
    {
        return $this->ccm_body['u']['username'] ?? 'NoName';
    }

    public function getAgentId(): string
    {
        return $this->ccm_body['u']['_id'] ?? '';
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ccm_id' => 'ID',
            'ccm_rid' => 'Room ID',
            'ccm_cch_id' => 'Client Chat ID',
            'ccm_client_id' => 'Client ID',
            'ccm_user_id' => 'User ID',
            'ccm_sent_dt' => 'Sent',
            'ccm_body' => 'Message',
            'ccm_has_attachment' => 'Has Attachment',
            'files' => 'Files',
        ];
    }

    /**
     * @param int $chatId
     * @return int|null
     */
    public static function countByChatId(int $chatId): ?int
    {
        return self::find()->where(['ccm_cch_id' => $chatId])->count();
    }

    public static function getLastMessageByClient(int $cchId): ?self
    {
        return ClientChatMessage::find()->andWhere(['ccm_cch_id' => $cchId])->andWhere(['is', 'ccm_user_id', null])->orderBy(['ccm_id' => SORT_DESC])->limit(1)->one();
    }

    public static function getLastMessageByAgent(int $cchId): ?self
    {
        return ClientChatMessage::find()->andWhere(['ccm_cch_id' => $cchId])->andWhere(['is not', 'ccm_user_id', null])->orderBy(['ccm_id' => SORT_DESC])->limit(1)->one();
    }

    public static function removeAllMessages(int $cchId)
    {
        self::deleteAll(['ccm_cch_id' => $cchId]);
    }

    public function validatePlatform(): bool
    {
        if (!array_key_exists($this->ccm_platform_id, ClientChatPlatform::getListName())) {
            Yii::warning('Unknown chat platform on message validation: ' . VarDumper::dumpAsString($this), 'ClientChatMessage::validatePlatform');
            $this->ccm_platform_id = ClientChatPlatform::getDefaultPlatform();
        }
        return true;
    }
}
