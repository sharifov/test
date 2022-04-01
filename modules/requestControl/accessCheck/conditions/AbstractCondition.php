<?php
/**
 * User: shakarim
 * Date: 3/31/22
 * Time: 9:49 PM
 */

namespace modules\requestControl\accessCheck\conditions;


/**
 * Abstract class for any Condition
 * @package modules\requestControl\accessCheck\conditions
 */
abstract class AbstractCondition
{
    /**
     * @var null|array|string
     */
    protected $value = null;

    /**
     * Condition constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }
}