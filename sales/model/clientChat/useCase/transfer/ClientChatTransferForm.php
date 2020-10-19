<?php

namespace sales\model\clientChat\useCase\transfer;

use common\components\validators\IsArrayValidator;
use common\models\Department;
use sales\helpers\clientChat\ClientChatHelper;
use sales\model\clientChat\entity\actionReason\ClientChatActionReasonQuery;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatChannelTransfer\entity\ClientChatChannelTransfer;
use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use sales\model\clientChatUserChannel\entity\search\ClientChatUserChannelSearch;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * Class ClientChatTransferForm
 * @package sales\model\clientChat\useCase\transfer
 *
 * @property int $type
 * @property int|null $channelId
 * @property int|null $projectId
 * @property int|null $originalChannelId
 * @property int|null $ownerId
 * @property int $chatId
 * @property int|null $depId
 * @property int|null $pjaxReload
 * @property array|null $agentId
 * @property ReasonDto[] $reasons
 * @property array|null $agents
 * @property array $availableUserChannels
 */
class ClientChatTransferForm extends Model
{
    public const TYPE_CHANNEL = 1;
    public const TYPE_AGENT = 2;

    public const TYPE_LIST = [
        self::TYPE_CHANNEL => 'Channel',
        self::TYPE_AGENT => 'Agent',
    ];

    public $chatId;

    public $type;

    public $channelId;

    public $pjaxReload;

    public $depId;

    public $agentId;

    public $reasonId;

    public $reasons;

    public $comment;

    private $projectId;

    private $originalChannelId;

    private $ownerId;

    private $agents;

    private $availableUserChannels;

    public function __construct(
        int $chatId,
        ?int $originalChannelId,
        ?int $projectId,
        ?int $ownerId,
        array $availableUserChannels,
        $config = []
    ) {
        parent::__construct($config);
        $this->chatId = $chatId;
        $this->originalChannelId = $originalChannelId;
        $this->projectId = $projectId;
        $this->ownerId = $ownerId;
        $this->reasons = ClientChatActionReasonQuery::getReasons(ClientChatStatusLog::ACTION_TRANSFER);
        $this->availableUserChannels = $availableUserChannels;
    }

    public function rules(): array
    {
        return [
            ['type', 'required'],
            ['type', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],
            ['type', 'in', 'range' => array_keys($this->getTypeList())],

            ['channelId', 'required'],
            ['channelId', 'integer'],
            ['channelId', 'filter', 'filter' => 'intval', 'skipOnError' => true, 'skipOnEmpty' => true],
            ['channelId', 'in', 'range' => array_keys($this->getChannels())],

            ['pjaxReload', 'integer'],
            ['pjaxReload', 'default', 'value' => null],

            ['depId', 'integer'],
            ['depId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['depId', 'in', 'range' => array_keys($this->getDepartments()), 'skipOnEmpty' => true],

            ['agentId', 'filter', 'filter' => static function ($value) {
                if (empty($value)) {
                    return [];
                }
                return $value;
            }],
            ['agentId', IsArrayValidator::class],
            ['agentId', 'required', 'when' => fn () => $this->isAgentTransfer()],
            ['agentId', 'each', 'rule' => ['filter', 'filter' => 'intval']],
            ['agentId', 'each', 'rule' => ['integer']],
            ['agentId', 'validateAgent', 'skipOnError' => true, 'skipOnEmpty' => true],

            ['reasonId', 'in', 'range' => array_keys($this->getReasonList())],

            ['comment', 'string', 'max' => 100],
            ['comment', 'required', 'when' => function () {
                return (isset($this->reasons[$this->reasonId]) && $this->reasons[$this->reasonId]->isCommentRequired());
            }, 'skipOnError' => true],
        ];
    }

    public function getChannels(): array
    {
        $channels = ClientChatChannel::find()
            ->select(['ccc_name', 'ccc_id'])
            ->enabled()
            ->byProject($this->projectId)
            ->andWhere(['ccc_id' => $this->availableUserChannels])
            ->andWhere(['ccc_id' => ClientChatChannelTransfer::find()->select(['cctr_to_ccc_id'])->fromChannel($this->originalChannelId)]);
        if ($this->depId) {
            $channels->byDepartment((int)$this->depId);
        }
        return $channels->orderBy(['ccc_name' => SORT_ASC])->indexBy('ccc_id')->column();
    }

    public function getReasonList(): array
    {
        $list = [];
        foreach ($this->reasons as $reason) {
            $list[$reason->id] = $reason->name;
        }
        return $list;
    }

    public function getTypeList(): array
    {
        return self::TYPE_LIST;
    }

    public function attributeLabels(): array
    {
        return [
            'cchId' => 'Client Chat Id',
            'depId' => 'Department',
            'isOnline' => 'Client Network Status',
            'agentId' => 'Agents',
            'reasonId' => 'Reason',
            'comment' => 'Comment',
            'channelId' => 'Channel',
        ];
    }

    public function isAgentTransfer(): bool
    {
        return (int)$this->type === self::TYPE_AGENT;
    }

    public function isChannelTransfer(): bool
    {
        return (int)$this->type === self::TYPE_CHANNEL;
    }

    public function getDepartments(): array
    {
        return Department::getList();
    }

    public function getAgents(): array
    {
        if (!$this->channelId) {
            return [];
        }

        if ($this->agents !== null) {
            return $this->agents;
        }

        $availableAgents = (new ClientChatUserChannelSearch())->getAvailableAgentForTransfer((int)$this->channelId);
        $this->agents = ArrayHelper::map($availableAgents, 'user_id', 'nickname');
        if ($this->ownerId && isset($this->agents[$this->ownerId])) {
            unset($this->agents[$this->ownerId]);
        }
        return $this->agents;
    }

    public function validateAgent()
    {
        foreach ($this->agentId as $item) {
            if (!array_key_exists($item, $this->getAgents())) {
                $this->addError('agentId', 'Agents is invalid');
                return;
            }
        }
    }
}
