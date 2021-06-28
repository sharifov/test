<?php

namespace sales\behaviors;

use sales\services\phone\checkPhone\CheckPhoneService;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class UidPhoneGeneratorBehavior
 *
 * @property string $donorColumn
 * @property string $targetColumn
 */
class UidPhoneGeneratorBehavior extends Behavior
{
    public $donorColumn;
    public $targetColumn;

    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'uidGenerate',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'uidGenerate',
        ];
    }

    public function uidGenerate(): void
    {
        if (
            !empty($this->owner->{$this->donorColumn}) &&
            $this->owner->getOldAttribute($this->donorColumn) !== $this->owner->{$this->donorColumn}
        ) {
            $this->owner->{$this->targetColumn} = CheckPhoneService::uidGenerator($this->owner->{$this->donorColumn});
        }
    }
}
