<?php

namespace common\models\search\employee;

use common\models\query\EmployeeQuery;
use yii\base\Model;

/**
 * Class SortParameters
 * @package common\models\search\employee
 *
 * @property SortParameter[] $sortParameters
 */
class SortParameters
{
    /**
     * @var Model[]
     */
    private const AVAILABLE_PARAMETERS = [
        'pastAcceptedChatsNumber' => PastAcceptedChatsNumber::class,
        'skillLevel' => SkillLevel::class
    ];

    private array $sortParameters = [];

    public function __construct(array $sortParameters)
    {
        foreach ($sortParameters as $key => $sortParameterData) {
            $this->sortParameters[] = $this->loadParameter((string)$key, $sortParameterData);
        }
    }

    public function apply(EmployeeQuery $query): void
    {
        foreach ($this->sortParameters as $sortParameter) {
            $sortParameter->apply($query);
        }
    }

    public function sortByPriority(): self
    {
        if ($this->sortParameters) {
            usort($this->sortParameters, static function ($firstItem, $secondItem) {
                /** @var SortParameter $firstItem */
                /** @var SortParameter $secondItem */
                if ($firstItem->getSortPriority() === $secondItem->getSortPriority()) {
                    return 0;
                }
                return $firstItem->getSortPriority() < $secondItem->getSortPriority() ? 1 : -1;
            });
        }
        return $this;
    }

    private function loadParameter(string $key, $sortParameterData): SortParameter
    {
        if (!array_key_exists($key, self::AVAILABLE_PARAMETERS)) {
            throw new \InvalidArgumentException('Employee sort parameters: provided key(' . $key . ') is not in available parameters list');
        }
        /** @var Model|SortParameter $sortParameter */
        $sortParameter = \Yii::createObject(self::AVAILABLE_PARAMETERS[$key]);
        if (!$sortParameter->load($sortParameterData) && !$sortParameter->validate()) {
            throw new \InvalidArgumentException('Employee sort parameter(' . $key . ') validation failed: ' . $sortParameter->getErrorSummary(true)[0]);
        }
        return $sortParameter;
    }
}
