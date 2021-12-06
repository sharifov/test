<?php

namespace common\models\search\employee;

use common\models\query\EmployeeQuery;
use yii\base\Model;

/**
 * Class SkillLevel
 * @package common\models\search\employee
 *
 * @property string $sortDirection
 * @property int $sortPriority
 * @property array $sortDirectionInt
 * @property bool $enabled
 */
class SkillLevel extends Model implements SortParameter
{
    public $sortDirection;
    public $sortPriority;
    public $enabled;

    private array $sortDirectionInt = [
        'ASC' => SORT_ASC,
        'DESC' => SORT_DESC
    ];

    public function rules(): array
    {
        return [
            [['sortDirection', 'sortPriority'], 'required'],
            [['sortDirection'], 'string'],
            [['sortDirection'], 'filter', 'filter' => 'trim'],
            [['sortDirection'], 'filter', 'filter' => 'uppercase'],
            [['sortDirection'], 'in', 'range' => ['ASC', 'DESC']],
            [['sortPriority'], 'integer'],
            [['sortPriority'], 'filter', 'filter' => 'intval'],
            [['enabled'], 'boolean']
        ];
    }

    public function apply(EmployeeQuery $query): void
    {
        $order = $this->sortDirectionInt[$this->sortDirection] ?? 0;
        if ($order && $this->enabled) {
            $query->joinUserProfile()->addOrderByUserProfileSkillLevel($order);
        }
    }

    public function getSortPriority(): int
    {
        return $this->sortPriority;
    }

    public function formName(): string
    {
        return '';
    }
}
