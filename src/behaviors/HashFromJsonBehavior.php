<?php

namespace src\behaviors;

use modules\flight\models\FlightRequest;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class HashFromJsonBehavior
 *
 * @property string $donorColumn
 * @property string $targetColumn
 */
class HashFromJsonBehavior extends Behavior
{
    public $donorColumn;
    public $targetColumn;

    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'hashGenerate',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'hashGenerate',
        ];
    }

    public function hashGenerate(): void
    {
        if (!empty($this->owner->{$this->donorColumn}) && is_array($this->owner->{$this->donorColumn})) {
            $this->owner->{$this->targetColumn} = FlightRequest::generateHashFromDataJson($this->owner->{$this->donorColumn});
        }
    }
}
