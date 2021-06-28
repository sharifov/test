<?php

namespace sales\behaviors;

use sales\services\phone\checkPhone\CheckPhoneService;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class PhoneCleanerBehavior
 *
 * @property string $targetColumn
 */
class PhoneCleanerBehavior extends Behavior
{
    public $targetColumn;

    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'clean',
        ];
    }

    public function clean(): void
    {
        $this->owner->{$this->targetColumn} = CheckPhoneService::cleanPhone($this->owner->{$this->targetColumn});
    }
}
