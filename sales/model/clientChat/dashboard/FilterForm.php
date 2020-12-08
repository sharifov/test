<?php

namespace sales\model\clientChat\dashboard;

use common\models\Department;
use common\models\Employee;
use common\models\Project;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatChannel\entity\ClientChatChannel;
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
 * @property $clientName
 * @property $resetAdditionalFilter
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
    public const DEFAULT_VALUE_SHOW_FILTER = 0;
    public const DEFAULT_VALUE_CLIENT_NAME = null;

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

    private array $channels;

    public Permissions $permissions;

    private array $additionalFilterAttributes = [
        'project',
        'userId',
        'rangeDate',
        'status',
        'clientName',
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
            ['channelId', 'integer'],
            ['channelId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['channelId', 'default', 'value' => self::DEFAULT_VALUE_CHANNEL_ID],
            ['channelId', 'in', 'range' => array_keys($this->getChannels())],

            ['status', 'integer'],
            ['status', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['status', 'default', 'value' => self::DEFAULT_VALUE_STATUS],
            ['status', 'in', 'range' => array_keys(ClientChat::getStatusList())],

            ['showFilter', 'integer'],
            ['showFilter', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['showFilter', 'default', 'value' => self::DEFAULT_VALUE_SHOW_FILTER],
            ['showFilter', 'in', 'range' => array_keys($this->getShowFilter())],

            ['dep', 'integer'],
            ['dep', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['dep', 'default', 'value' => self::DEFAULT_VALUE_DEP],
            ['dep', 'in', 'range' => array_keys($this->getDepartments())],

            ['project', 'integer'],
            ['project', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['project', 'default', 'value' => self::DEFAULT_VALUE_PROJECT],
            ['project', 'in', 'range' => array_keys($this->getProjects())],

            ['group', 'integer'],
            ['group', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['group', 'default', 'value' => $this->getDefaultGroupValue()],
            ['group', 'in', 'range' => array_keys($this->getAvailableGroup())],

            ['readUnread', 'integer'],
            ['readUnread', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['readUnread', 'default', 'value' => self::DEFAULT_VALUE_READ_UNREAD],
            ['readUnread', 'in', 'range' => array_keys($this->getReadFilter())],

            ['userId', 'integer'],
            ['userId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['userId', 'default', 'value' => self::DEFAULT_VALUE_USER_ID],
            ['userId', 'validateUser', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['createdDate', 'string'],
            ['createdDate', 'date', 'format' => 'php:d-m-Y'],
            ['createdDate', 'default', 'value' => self::DEFAULT_VALUE_CREATED_DATE],

            [['fromDate', 'toDate'], 'string'],
            [['fromDate', 'toDate'], 'date', 'format' => 'php:d-m-Y'],
            [['fromDate', 'toDate'], 'default', 'value' => self::DEFAULT_VALUE_CREATED_DATE],

            ['rangeDate', 'safe'],
            ['resetAdditionalFilter', 'boolean'],

            [['clientName'], 'string', 'max' => 30],
        ];
    }

    public function validateUser(): void
    {
        $user = Employee::find()->select(['id', 'username'])->andWhere(['id' => $this->userId])->asArray()->one();
        if (!$user) {
            return;
        }
        $this->userName = $user['username'];
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
        return ArrayHelper::merge(['All'], ClientChat::getStatusList());
    }

    public function getDepartments(): array
    {
        return ArrayHelper::merge(['All'], Department::DEPARTMENT_LIST);
    }

    public function getProjects(): array
    {
        return ArrayHelper::merge(['All'], Project::getList());
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
            return ArrayHelper::merge(['All'], $channels);
        }
        return ArrayHelper::merge(['All'], $this->channels);
    }

    public function isEmptyChannels(): bool
    {
        return count($this->channels) === 0;
    }

    public function loadDefaultValues(): void
    {
        if ($this->channelId === null || $this->hasErrors('channelId')) {
            $this->channelId = self::DEFAULT_VALUE_CHANNEL_ID;
        }
        if ($this->status === null || $this->hasErrors('status')) {
            $this->status = self::DEFAULT_VALUE_STATUS;
        }
        if ($this->showFilter === null || $this->hasErrors('showFilter')) {
            $this->showFilter = self::DEFAULT_VALUE_SHOW_FILTER;
        }
        if ($this->dep === null || $this->hasErrors('dep')) {
            $this->dep = self::DEFAULT_VALUE_DEP;
        }
        if ($this->project === null || $this->hasErrors('project')) {
            $this->project = self::DEFAULT_VALUE_PROJECT;
        }
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
    }

    public function loadDefaultValuesByPermissions(): FilterForm
    {
        if (!$this->permissions->canChannel()) {
            $this->channelId = self::DEFAULT_VALUE_CHANNEL_ID;
        }
        if (!$this->permissions->canStatus()) {
            $this->status = self::DEFAULT_VALUE_STATUS;
        }
        if (!$this->permissions->canShow()) {
            $this->showFilter = ClientChat::TAB_ALL;
        }
        if (!$this->permissions->canDepartment()) {
            $this->dep = self::DEFAULT_VALUE_DEP;
        }
        if (!$this->permissions->canProject()) {
            $this->project = self::DEFAULT_VALUE_PROJECT;
        }
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
        return $this;
    }

    public function getAvailableGroup(): array
    {
        return $this->processFilterGroupByPermissions(GroupFilter::LIST);
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
        ];
    }

    public function getAdditionalFilterAttributes(): array
    {
        return $this->additionalFilterAttributes;
    }

    public function isAdditionalFilterActive(): bool
    {
        foreach ($this->getAdditionalFilterAttributes() as $key => $attrName) {
            if (!empty($this->{$attrName}) && $this->{$attrName} !== 0) {
                return true;
            }
        }
        return false;
    }

    public function resetAdditionalAttributes(): FilterForm
    {
        $this->project = self::DEFAULT_VALUE_PROJECT;
        $this->userId = self::DEFAULT_VALUE_USER_ID;
        $this->fromDate = self::DEFAULT_VALUE_FROM_DATE;
        $this->toDate = self::DEFAULT_VALUE_TO_DATE;
        $this->status = self::DEFAULT_VALUE_STATUS;
        $this->clientName = self::DEFAULT_VALUE_CLIENT_NAME;
        return $this;
    }
}
