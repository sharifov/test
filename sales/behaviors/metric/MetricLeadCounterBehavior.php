<?php

namespace sales\behaviors\metric;

use yii\helpers\ArrayHelper;

/**
 * Class MetricLeadCounterBehavior
 */
class MetricLeadCounterBehavior extends MetricObjectCounterBehavior
{
    public function fillCustomValue(): void
    {
        if (method_exists($this->owner, 'getTypeCreateName')) {
            $this->labels = ArrayHelper::merge($this->labels, ['type_creation' => $this->owner->getTypeCreateName()]);
        }
    }
}
