<?php

namespace sales\model\clientChat\dashboard;

use common\models\Department;
use common\models\Employee;
use common\models\Project;
use sales\model\clientChat\entity\ClientChat;
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
 * @property array       $channels
 * @property Permissions $permissions
 */
class FilterForm extends Model
{
    public const DEFAULT_VALUE_CHANNEL_ID = 0;
    public const DEFAULT_VALUE_STATUS = ClientChat::TAB_ACTIVE;
    public const DEFAULT_VALUE_DEP = 0;
    public const DEFAULT_VALUE_PROJECT = 0;
    public const DEFAULT_VALUE_READ_UNREAD = ReadUnreadFilter::ALL;
    public const DEFAULT_VALUE_USER_ID = null;
    public const DEFAULT_VALUE_USER_NAME = null;
    public const DEFAULT_VALUE_CREATED_DATE = null;

    public $channelId;
    public $status;
    public $dep;
    public $project;
    public $group;
    public $readUnread;
    public $userId;
    public $userName;
    public $createdDate;

    private array $channels;

    public Permissions $permissions;

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
            ['status', 'in', 'range' => array_keys($this->getStatuses())],

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

    public function getStatuses(): array
    {
        return ArrayHelper::merge([ClientChat::TAB_ALL => 'All'], ClientChat::TAB_LIST_NAME);
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
    }

    public function loadDefaultValuesByPermissions(): void
    {
        if (!$this->permissions->canChannel()) {
            $this->channelId = self::DEFAULT_VALUE_CHANNEL_ID;
        }
        if (!$this->permissions->canStatus()) {
            $this->status = ClientChat::TAB_ALL;
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
    }

    public function getAvailableGroup(): array
    {
        $filter = GroupFilter::FULL_LIST;

        $filter = $this->processFilterGroupByPermissions($filter);

        if (isset($filter[GroupFilter::ALL]) && !$this->permissions->canAllOfGroup()) {
            unset($filter[GroupFilter::ALL]);
        }

        return $filter;
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
        if (isset($filter[GroupFilter::ALL])) {
            unset($filter[GroupFilter::ALL]);
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
        return Html::activeCheckbox($this, 'readUnread', ['label' => 'Unread', 'id' => $this->getReadUnreadInputId()]);
    }

    public function getReadUnreadInputId(): string
    {
        return  Html::getInputId($this, 'readUnread');
    }
}
