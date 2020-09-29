<?php

namespace sales\model\clientChat\dashboard;

use common\models\Department;
use common\models\Project;
use sales\model\clientChat\entity\ClientChat;
use yii\base\Model;

/**
 * Class FilterForm
 *
 * @property $channelId
 * @property $page
 * @property $chatId
 * @property $status
 * @property $dep
 * @property $project
 * @property $group
 * @property $read
 */
class FilterForm extends Model
{
    public $channelId;
    public $page;
    public $chatId;
    public $status;
    public $dep;
    public $project;
    public $group;
    public $read;

    public function rules(): array
    {
        return [
            ['channelId', 'integer'],
            ['channelId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['channelId', 'default', 'value' => null],

            ['page', 'integer'],
            ['page', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['page', 'default', 'value' => 1],

            ['chatId', 'integer'],
            ['chatId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['chatId', 'default', 'value' => null],

            ['status', 'integer'],
            ['status', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['status', 'default', 'value' => ClientChat::TAB_ACTIVE],
            ['status', 'in', 'range' => array_merge([ClientChat::TAB_ALL], array_keys($this->getStatuses()))],

            ['dep', 'integer'],
            ['dep', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['dep', 'default', 'value' => null],
            ['dep', 'in', 'range' => array_keys($this->getDepartments())],

            ['project', 'integer'],
            ['project', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['project', 'default', 'value' => null],
            ['project', 'in', 'range' => array_keys($this->getProjects())],

            ['group', 'integer'],
            ['group', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['group', 'default', 'value' => GroupFilter::MY],
            ['group', 'in', 'range' => array_keys(GroupFilter::LIST)],

            ['read', 'integer'],
            ['read', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['read', 'default', 'value' => ReadFilter::ALL],
            ['read', 'in', 'range' => array_merge([ReadFilter::ALL], array_keys($this->getReadFilter()))],
        ];
    }

    public function getReadFilter(): array
    {
        return ReadFilter::LIST;
    }

    public function getStatuses(): array
    {
        return ClientChat::TAB_LIST_NAME;
    }

    public function getDepartments(): array
    {
        return Department::DEPARTMENT_LIST;
    }

    public function getProjects(): array
    {
        return Project::getList();
    }

    public function loadDefaultValues(): void
    {
        $this->channelId = null;
        $this->page = 1;
        $this->chatId = null;
        $this->status = ClientChat::TAB_ACTIVE;
        $this->dep = null;
        $this->project = null;
        $this->group = GroupFilter::MY;
        $this->read = ReadFilter::ALL;
    }
}
