<?php

namespace modules\requestControl\accessCheck\conditions;

use modules\requestControl\interfaces\ConditionInterface;
use yii\db\Query;

/**
 * Abstract class for any Condition
 * @package modules\requestControl\accessCheck\conditions
 */
abstract class AbstractCondition implements ConditionInterface
{
    /** @var null|array|string */
    protected $value = null;

    /**
     * Condition constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param array $result
     * @param array $data
     * @return array
     */
    public function reduceData($result, $data): array
    {
        return array_reduce($data, function ($acc, $x) {
            if (isset($x['rcr_type']) && isset($x['rcr_subject'])) {
                $isTypeValid = $x['rcr_type'] === $this->getType();
                $isValueValid = is_array($this->value) ? in_array($x['rcr_subject'], $this->value) : $this->value === $x['rcr_subject'];
                if ($isTypeValid && $isValueValid) {
                    $acc[] = $x;
                }
            }
            return $acc;
        }, $result);
    }

    /**
     * @param Query $query
     * @return Query
     */
    public function modifyQuery(Query $query): Query
    {
        return $query->orWhere(['rcr_type' => $this->getType(), 'rcr_subject' => $this->value]);
    }
}
