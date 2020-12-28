<?php

namespace sales\behaviors\metric;

use yii\helpers\ArrayHelper;

/**
 * Class MetricClientChatCounterBehavior
 */
class MetricClientChatCounterBehavior extends MetricObjectCounterBehavior
{
    public function fillCustomValue(): void
    {
        if (method_exists($this->owner, 'getSourceTypeName')) {
            $this->labels = ArrayHelper::merge($this->labels, [
                'type_creation' => $this->owner->getSourceTypeName() ?? 'Undefined'
            ]);
        }
    }
}
