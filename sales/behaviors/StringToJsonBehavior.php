<?php

namespace sales\behaviors;

use frontend\helpers\JsonHelper;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class StringToJsonBehavior
 *
 * @property string $jsonColumn
 */
class StringToJsonBehavior extends Behavior
{
    public $jsonColumn;

    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'insertToJson',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'updateToJson',
        ];
    }

    public function insertToJson(): void
    {
        if (!empty($this->owner->{$this->jsonColumn})) {
            $this->owner->{$this->jsonColumn} = JsonHelper::decode($this->owner->{$this->jsonColumn});
        }
    }

    public function updateToJson(): void
    {
        if (!empty($this->owner->{$this->jsonColumn}) && $this->owner->isAttributeChanged($this->jsonColumn)) {
            $this->owner->{$this->jsonColumn} = JsonHelper::decode($this->owner->{$this->jsonColumn});
        }
    }
}
