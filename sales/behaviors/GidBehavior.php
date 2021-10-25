<?php

namespace sales\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class GidBehavior
 *
 * @property string $donorColumn
 * @property string $targetColumn
 */
class GidBehavior extends Behavior
{
    public $value;
    public $targetColumn;

    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'gidGenerate',
        ];
    }

    public function gidGenerate(): void
    {
        $this->owner->{$this->targetColumn} = $this->value;
    }
}
