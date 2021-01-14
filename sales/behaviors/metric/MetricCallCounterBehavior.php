<?php

namespace sales\behaviors\metric;

use yii\helpers\ArrayHelper;

/**
 * Class MetricCallCounterBehavior
 */
class MetricCallCounterBehavior extends MetricObjectCounterBehavior
{
    public function fillCustomValue(): void
    {
        if (method_exists($this->owner, 'getCallTypeName')) {
            $this->labels = ArrayHelper::merge($this->labels, [
                'type_creation' => $this->owner->getCallTypeName()
            ]);
        }
    }
}
