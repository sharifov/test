<?php

namespace sales\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

/**
 * Class SlugBehavior
 *
 * @property string $donorColumn
 * @property string $targetColumn
 */
class SlugBehavior extends Behavior
{
    public $donorColumn;
    public $targetColumn;

    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'slugGenerate',
        ];
    }

    public function slugGenerate(): void
    {
        if (!empty($this->owner->{$this->donorColumn}) && empty($this->owner->{$this->targetColumn})) {
            $this->owner->{$this->targetColumn} = Inflector::slug($this->owner->{$this->donorColumn}, '_');
        }
    }
}
