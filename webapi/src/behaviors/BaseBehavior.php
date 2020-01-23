<?php

namespace webapi\src\behaviors;

use yii\base\Action;
use yii\base\Behavior;
use yii\helpers\StringHelper;

class BaseBehavior extends Behavior
{
    /**
     * @var array list of action IDs that this filter should apply to. If this property is not set,
     * then the filter applies to all actions, unless they are listed in [[except]].
     * If an action ID appears in both [[only]] and [[except]], this filter will NOT apply to it.
     *
     * Note that if the filter is attached to a module, the action IDs should also include child module IDs (if any)
     * and controller IDs.
     *
     * Since version 2.0.9 action IDs can be specified as wildcards, e.g. `site/*`.
     *
     * @see except
     */
    public $only;
    /**
     * @var array list of action IDs that this filter should not apply to.
     * @see only
     */
    public $except = [];

    /**
     * Returns a value indicating whether the filter is active for the given action.
     * @param Action $action the action being filtered
     * @return bool whether the filter is active for the given action.
     */
    protected function isDisabled(Action $action): bool
    {
        $actionId = $action->id;

        if (empty($this->only)) {
            $onlyMatch = true;
        } else {
            $onlyMatch = false;
            foreach ($this->only as $pattern) {
                if (StringHelper::matchWildcard($pattern, $actionId)) {
                    $onlyMatch = true;
                    break;
                }
            }
        }

        $exceptMatch = false;
        foreach ($this->except as $pattern) {
            if (StringHelper::matchWildcard($pattern, $actionId)) {
                $exceptMatch = true;
                break;
            }
        }

        return !(!$exceptMatch && $onlyMatch);
    }
}
