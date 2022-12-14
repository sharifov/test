<?php

namespace src\model\clientChat\dashboard;

use common\models\Department;
use common\models\Employee;
use common\models\Project;
use common\models\UserGroup;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChatChannel\entity\ClientChatChannel;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class FilterForm
 *
 * @property $channelId
 * @property $page
 * @property $status
 * @property $dep
 * @property $project
 * @property $group
 * @property $readUnread
 * @property $userId
 * @property $userName
 * @property $createdDate
 * @property $fromDate
 * @property $toDate
 * @property $rangeDate
 * @property $showFilter
 * @property $clientName
 * @property $chatId
 * @property $userGroups
 * @property $clientEmail
 * @property $resetAdditionalFilter
 * @property $sortPriority
 * @property array       $channels
 * @property Permissions $permissions
 */
class FilterForm extends Model
{
    public const DEFAULT_VALUE_CHANNEL_ID = 0;
    public const DEFAULT_VALUE_STATUS = null;
    public const DEFAULT_VALUE_DEP = 0;
    public const DEFAULT_VALUE_PROJECT = 0;
    public const DEFAULT_VALUE_READ_UNREAD = ReadUnreadFilter::ALL;
    public const DEFAULT_VALUE_USER_ID = null;
    public const DEFAULT_VALUE_USER_NAME = null;
    public const DEFAULT_VALUE_CREATED_DATE = null;
    public const DEFAULT_VALUE_FROM_DATE = null;
    public const DEFAULT_VALUE_TO_DATE = null;
    public const DEFAULT_VALUE_SHOW_FILTER = 1;
    public const DEFAULT_VALUE_CLIENT_NAME = null;
    public const DEFAULT_VALUE_CLIENT_EMAIL = null;

    public const SORT_PRIORITY_LAST_MESSAGE = 1;
    public const SORT_PRIORITY_LAST_UPDATE = 2;
    public const SORT_PRIORITY_OLDEST = 3;
    public const SORT_PRIORITY_NEWEST = 4;

    public const SORT_PRIORITY_LIST = [
        self::SORT_PRIORITY_LAST_MESSAGE => 'Last Message',
        self::SORT_PRIORITY_LAST_UPDATE => 'Last Update',
        self::SORT_PRIORITY_OLDEST => 'Oldest',
        self::SORT_PRIORITY_NEWEST => 'Newest',
    ];

    public const SORT_PRIORITY_DEFAULT = self::SORT_PRIORITY_LAST_MESSAGE;

    public const SORT_PRIORITY_VALUE = [
        self::SORT_PRIORITY_LAST_MESSAGE => [
            'last_message_date' => SORT_DESC
        ],
        self::SORT_PRIORITY_LAST_UPDATE => [
            'cch_updated_dt' => SORT_DESC
        ],
        self::SORT_PRIORITY_OLDEST => [
            'cch_created_dt' => SORT_ASC
        ],
        self::SORT_PRIORITY_NEWEST => [
            'cch_created_dt' => SORT_DESC
        ],
    ];

    public $channelId;
    public $status;
    public $dep;
    public $project;
    public $group;
    public $readUnread;
    public $userId;
    public $userName;
    public $createdDate;
    public $fromDate;
    public $toDate;
    public $rangeDate;
    public $resetAdditionalFilter = false;
    public $showFilter;
    public $clientName;
    public $clientEmail;
    public $sortPriority;
    public $userGroups;
    public $chatId;

    private array $channels;

    public Permissions $permissions;

    private array $additionalFilterAttributes = [
        'project',
        'userId',
        'rangeDate',
        'status',
        'clientName',
        'clientEmail',
        'sortPriority',
        'userGroups',
        'chatId'
    ];

    public function __construct(array $channels, $config = [])
    {
        parent::__construct($config);
        $this->channels = $channels;
        $this->permissions = new Permissions();
    }

    public function rules(): array
    {
        return [
            ['channelId', 'safe'],
            ['channelId', 'each', 'rule' => ['filter', 'filter' => 'intval']],
            ['channelId', 'each', 'rule' => ['in', 'range' => array_keys($this->getChannels())]],

            ['status', 'safe'],
//            ['status', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
//            ['status', 'default', 'value' => self::DEFAULT_VALUE_STATUS],
            ['status', 'each', 'rule' => ['filter', 'filter' => 'intval']],
            ['status', 'each', 'rule' => ['in', 'range' => array_keys(ClientChat::getStatusList())]],

            ['showFilter', 'integer'],
            ['showFilter', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['showFilter', 'default', 'value' => self::DEFAULT_VALUE_SHOW_FILTER],
            ['showFilter', 'in', 'range' => array_keys($this->getShowFilter())],

            ['dep', 'integer'],
            ['dep', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['dep', 'default', 'value' => self::DEFAULT_VALUE_DEP],
            ['dep', 'in', 'range' => array_keys($this->getDepartments())],

            ['project', 'safe'],
//            ['project', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
//            ['project', 'default', 'value' => self::DEFAULT_VALUE_PROJECT],
            ['project', 'each', 'rule' => ['filter', 'filter' => 'intval']],
            ['project', 'each', 'rule' => ['in', 'range' => array_keys($this->getProjects())]],


            ['group', 'integer'],
            ['group', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['group', 'default', 'value' => $this->getDefaultGroupValue()],
            ['group', 'in', 'range' => array_keys($this->getAvailableGroup())],

            ['readUnread', 'integer'],
            ['readUnread', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['readUnread', 'default', 'value' => self::DEFAULT_VALUE_READ_UNREAD],
            ['readUnread', 'in', 'range' => array_keys($this->getReadFilter())],

            ['userId', 'safe'],
            ['userId', 'each', 'rule' => ['filter', 'filter' => 'intval']],
            ['userId', 'each', 'rule' => ['validateUser']],

            ['chatId', 'safe'],
            ['chatId', 'each', 'rule' => ['filter', 'filter' => 'intval']],

            ['createdDate', 'string'],
            ['createdDate', 'date', 'format' => 'php:d-m-Y'],
            ['createdDate', 'default', 'value' => self::DEFAULT_VALUE_CREATED_DATE],

            [['fromDate', 'toDate'], 'string'],
            [['fromDate', 'toDate'], 'date', 'format' => 'php:d-m-Y'],
            [['fromDate', 'toDate'], 'default', 'value' => self::DEFAULT_VALUE_CREATED_DATE],

            ['rangeDate', 'safe'],
            ['resetAdditionalFilter', 'boolean'],

            [['clientName'], 'string', 'max' => 30],

            ['clientEmail', 'email'],
            ['clientEmail', 'string', 'max' => 100],

            ['sortPriority', 'integer'],
            ['sortPriority', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['sortPriority', 'default', 'value' => self::SORT_PRIORITY_DEFAULT],
            ['sortPriority', 'in', 'range' => array_keys(self::SORT_PRIORITY_LIST)],

            ['userGroups', 'safe'],
//            ['group', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
//            ['group', 'default', 'value' => $this->getDefaultGroupValue()],
            ['userGroups', 'each', 'rule' => ['filter', 'filter' => 'intval']],
            ['userGroups', 'each', 'rule' => ['in', 'range' => array_keys($this->getUserGroups())]],
        ];
    }

    public function validateUser($attribute, $params, $model, $value): void
    {
        $user = Employee::find()->select(['id', 'username'])->andWhere(['id' => $value])->asArray()->one();
        if (!$user) {
            return;
        }
        $this->userName[$user['id']] = $user['username'];
    }

    public function getReadFilter(): array
    {
        return ReadUnreadFilter::LIST;
    }

    public function getShowFilter(): array
    {
        return ClientChat::getTabList();
    }

    public function getStatuses(): array
    {
        return ClientChat::getStatusList();
    }

    public function getDepartments(): array
    {
        return ArrayHelper::merge(['All'], Department::DEPARTMENT_LIST);
    }

    public function getProjects(): array
    {
        return Project::getList();
//        return ArrayHelper::merge(['All'], Project::getList());
    }

    public function getChannels(): array
    {
        if ($this->project) {
            $channels = ClientChatChannel::find()
                ->select(['ccc_name', 'ccc_id'])
                ->where(['ccc_project_id' => $this->project])
                ->andWhere(['IN', 'ccc_id', array_keys($this->channels)])
                ->indexBy('ccc_id')
                ->column();
            return $channels;
        }
        return $this->channels;
    }

    public function isEmptyChannels(): bool
    {
        return count($this->channels) === 0;
    }

    public function loadDefaultValues(): void
    {
//        if ($this->channelId === null || $this->hasErrors('channelId')) {
//            $this->channelId = null;
//        }
//        if ($this->status === null || $this->hasErrors('status')) {
//            $this->status = null;
//        }
        if ($this->showFilter === null || $this->hasErrors('showFilter')) {
            $this->showFilter = self::DEFAULT_VALUE_SHOW_FILTER;
        }
        if ($this->dep === null || $this->hasErrors('dep')) {
            $this->dep = self::DEFAULT_VALUE_DEP;
        }
//        if ($this->project === null || $this->hasErrors('project')) {
//            $this->project = null;
//        }
        if ($this->group === null || $this->hasErrors('group')) {
            $this->group = $this->getDefaultGroupValue();
        }
        if ($this->readUnread === null || $this->hasErrors('readUnread')) {
            $this->readUnread = self::DEFAULT_VALUE_READ_UNREAD;
        }
        if ($this->userId === null || $this->hasErrors('userId')) {
            $this->userId = self::DEFAULT_VALUE_USER_ID;
            $this->userName = self::DEFAULT_VALUE_USER_NAME;
        }
        if ($this->createdDate === null || $this->hasErrors('createdDate')) {
            $this->createdDate = self::DEFAULT_VALUE_CREATED_DATE;
        }
        if ($this->clientName === null || $this->hasErrors('clientName')) {
            $this->clientName = self::DEFAULT_VALUE_CLIENT_NAME;
        }
        if ($this->clientEmail === null || $this->hasErrors('clientEmail')) {
            $this->clientEmail = self::DEFAULT_VALUE_CLIENT_EMAIL;
        }
        if ($this->sortPriority === null || $this->hasErrors('sortPriority')) {
            $this->sortPriority = self::SORT_PRIORITY_DEFAULT;
        }
    }

    public function loadDefaultValuesByPermissions(): FilterForm
    {
//        if (!$this->permissions->canChannel()) {
//            $this->channelId = self::DEFAULT_VALUE_CHANNEL_ID;
//        }
//        if (!$this->permissions->canStatus()) {
//            $this->status = self::DEFAULT_VALUE_STATUS;
//        }
        if (!$this->permissions->canShow()) {
            $this->showFilter = ClientChat::TAB_ALL;
        }
        if (!$this->permissions->canDepartment()) {
            $this->dep = self::DEFAULT_VALUE_DEP;
        }
//        if (!$this->permissions->canProject()) {
//            $this->project = self::DEFAULT_VALUE_PROJECT;
//        }
        if (!$this->permissions->canOneOfGroup()) {
            $this->group = $this->getDefaultGroupValue();
        }
        if (!$this->permissions->canReadUnread()) {
            $this->readUnread = self::DEFAULT_VALUE_READ_UNREAD;
        }
        if (!$this->permissions->canUser()) {
            $this->userId = self::DEFAULT_VALUE_USER_ID;
            $this->userName = self::DEFAULT_VALUE_USER_NAME;
        }
        if (!$this->permissions->canCreatedDate()) {
            $this->createdDate = self::DEFAULT_VALUE_CREATED_DATE;
        }
        if (!$this->permissions->canSortPriority()) {
            $this->sortPriority = self::SORT_PRIORITY_DEFAULT;
        }
        return $this;
    }

    public function getAvailableGroup(): array
    {
        return $this->processFilterGroupByPermissions(GroupFilter::LIST);
    }

    public function getUserGroups(): array
    {
        return UserGroup::getList();
    }

    public function getDefaultGroupValue(): int
    {
        if ($this->permissions->canGroupMyChats()) {
            return GroupFilter::MY;
        }
        if ($this->permissions->canGroupOtherChats()) {
            return GroupFilter::OTHER;
        }
        if ($this->permissions->canGroupFreeToTake()) {
            return GroupFilter::FREE_TO_TAKE;
        }
        if ($this->permissions->canGroupTeamChats()) {
            return GroupFilter::TEAM_CHATS;
        }

        return GroupFilter::NOTHING;
    }

    public function getGroupFilterUI(): array
    {
        $filter = $this->getAvailableGroup();

        if (isset($filter[GroupFilter::NOTHING])) {
            unset($filter[GroupFilter::NOTHING]);
        }

        $filter = $this->processFilterGroupByPermissions($filter);

        return $filter;
    }

    private function processFilterGroupByPermissions(array $filter): array
    {
        if (isset($filter[GroupFilter::MY]) && !$this->permissions->canGroupMyChats()) {
            unset($filter[GroupFilter::MY]);
        }
        if (isset($filter[GroupFilter::OTHER]) && !$this->permissions->canGroupOtherChats()) {
            unset($filter[GroupFilter::OTHER]);
        }
        if (isset($filter[GroupFilter::FREE_TO_TAKE]) && !$this->permissions->canGroupFreeToTake()) {
            unset($filter[GroupFilter::FREE_TO_TAKE]);
        }
        if (isset($filter[GroupFilter::TEAM_CHATS]) && !$this->permissions->canGroupTeamChats()) {
            unset($filter[GroupFilter::TEAM_CHATS]);
        }

        return $filter;
    }

    public function getId(): string
    {
        return $this->formName() . '-Id';
    }

    public function getGroupInput(): string
    {
        return Html::hiddenInput(Html::getInputName($this, 'group'), $this->group, ['id' => $this->getGroupInputId()]);
    }

    public function getGroupInputId(): string
    {
        return  Html::getInputId($this, 'group');
    }

    public function getReadUnreadInput(): string
    {
        return Html::activeCheckbox($this, 'readUnread', ['label' => 'Unread', 'id' => $this->getReadUnreadInputId(), 'labelOptions' => ['style' => 'margin: 0']]);
    }

    public function getReadUnreadInputId(): string
    {
        return  Html::getInputId($this, 'readUnread');
    }

    public function attributeLabels(): array
    {
        return [
            'channelId' => 'Channel ID',
            'status' => 'Status',
            'project' => 'Project',
            'userId' => 'User ID',
            'rangeDate' => 'Created range dates',
            'clientName' => 'Client Name',
            'clientEmail' => 'Client Email',
            'userGroups' => 'User Groups',
            'chatId' => 'Chat Id'
        ];
    }

    public function getAdditionalFilterAttributes(): array
    {
        return $this->additionalFilterAttributes;
    }

    public function isAdditionalFilterActive(): bool
    {
        foreach ($this->getAdditionalFilterAttributes() as $key => $attrName) {
            if (!empty($this->{$attrName}) && $this->{$attrName} !== 0 && $this->{$attrName} !== self::SORT_PRIORITY_DEFAULT) {
                return true;
            }
        }
        return false;
    }

    public function resetAdditionalAttributes(): FilterForm
    {
        $this->project = null;
        $this->userId = self::DEFAULT_VALUE_USER_ID;
        $this->fromDate = self::DEFAULT_VALUE_FROM_DATE;
        $this->toDate = self::DEFAULT_VALUE_TO_DATE;
        $this->status = null;
        $this->clientName = self::DEFAULT_VALUE_CLIENT_NAME;
        $this->clientEmail = self::DEFAULT_VALUE_CLIENT_EMAIL;
        $this->userGroups = null;
        $this->chatId = null;
        return $this;
    }

    public function getOrderBy(): ?array
    {
        return self::SORT_PRIORITY_VALUE[$this->sortPriority] ?? null;
    }

    public function getSortPriorityList(): array
    {
        return self::SORT_PRIORITY_LIST;
    }
}
