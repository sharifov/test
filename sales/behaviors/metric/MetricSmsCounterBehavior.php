<?php

namespace sales\behaviors\metric;

use yii\helpers\ArrayHelper;

/**
 * Class MetricSmsCounterBehavior
 */
class MetricSmsCounterBehavior extends MetricObjectCounterBehavior
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
