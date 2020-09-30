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
 * @property $read
 * @property array $channels
 * @property $agentId
 * @property $agentName
 * @property $createdDate
 */
class FilterForm extends Model
{
    public $channelId;
    public $status;
    public $dep;
    public $project;
    public $group;
    public $read;
    public $agentId;
    public $agentName;
    public $createdDate;

    private array $channels;

    public function __construct(array $channels, $config = [])
    {
        parent::__construct($config);
        $this->channels = $channels;
    }

    public function rules(): array
    {
        return [
            ['channelId', 'integer'],
            ['channelId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['channelId', 'default', 'value' => 0],
            ['channelId', 'in', 'range' => array_keys($this->getChannels())],

            ['status', 'integer'],
            ['status', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['status', 'default', 'value' => ClientChat::TAB_ACTIVE],
            ['status', 'in', 'range' => array_keys($this->getStatuses())],

            ['dep', 'integer'],
            ['dep', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['dep', 'default', 'value' => 0],
            ['dep', 'in', 'range' => array_keys($this->getDepartments())],

            ['project', 'integer'],
            ['project', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['project', 'default', 'value' => 0],
            ['project', 'in', 'range' => array_keys($this->getProjects())],

            ['group', 'integer'],
            ['group', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['group', 'default', 'value' => GroupFilter::MY],
            ['group', 'in', 'range' => array_merge([GroupFilter::ALL], array_keys($this->getGroupFilter()))],

            ['read', 'integer'],
            ['read', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['read', 'default', 'value' => ReadFilter::ALL],
            ['read', 'in', 'range' => array_keys($this->getReadFilter())],

            ['agentId', 'integer'],
            ['agentId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['agentId', 'validateAgent', 'skipOnEmpty' => true, 'skipOnError' => true],

            ['createdDate', 'string'],
            ['createdDate', 'date', 'format' => 'php:d-m-Y'],
            ['createdDate', 'default', 'value' => null],
        ];
    }

    public function validateAgent(): void
    {
        $agent = Employee::find()->select(['id', 'username'])->andWhere(['id' => $this->agentId])->asArray()->one();
        if (!$agent) {
            return;
        }
        $this->agentName = $agent['username'];
    }

    public function getGroupFilter(): array
    {
        return GroupFilter::LIST;
    }

    public function getReadFilter(): array
    {
        return ReadFilter::LIST;
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
        $this->channelId = 0;
        $this->status = ClientChat::TAB_ACTIVE;
        $this->dep = 0;
        $this->project = 0;
        $this->group = GroupFilter::MY;
        $this->read = ReadFilter::ALL;
        $this->agentId = null;
        $this->agentName = null;
        $this->createdDate = null;
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

    public function getReadInput(): string
    {
        return Html::activeCheckbox($this, 'read', ['label' => 'Unread', 'id' => $this->getReadInputId()]);
    }

    public function getReadInputId(): string
    {
        return  Html::getInputId($this, 'read');
    }
}
