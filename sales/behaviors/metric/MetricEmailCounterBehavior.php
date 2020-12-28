<?php

namespace sales\behaviors\metric;

use yii\helpers\ArrayHelper;

/**
 * Class MetricEmailCounterBehavior
 */
class MetricEmailCounterBehavior extends MetricObjectCounterBehavior
{
    public function fillCustomValue(): void
    {
        if (method_exists($this->owner, 'getTypeName')) {
            $this->labels = ArrayHelper::merge($this->labels, [
                'type_creation' => $this->owner->getTypeName()
            ]);
        }
    }
}
