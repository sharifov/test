<?php

namespace src\behaviors\dateTime;

use yii\db\ActiveRecord;

class CreatedYearMonthBehavior extends \yii\base\Behavior
{
    public $createdColumn;
    public $yearColumn;
    public $monthColumn;

    public string $timeZone = 'UTC';

    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'fillData',
        ];
    }

    public function fillData(): void
    {
        $nowDT = (new \DateTimeImmutable('now', new \DateTimeZone($this->timeZone)));
        if (empty($this->owner->{$this->createdColumn})) {
            $this->owner->{$this->createdColumn} = $nowDT->format('Y-m-d H:i:s');
        }
        if (empty($this->owner->{$this->yearColumn})) {
            $this->owner->{$this->yearColumn} = $nowDT->format('Y');
        }
        if (empty($this->owner->{$this->monthColumn})) {
            $this->owner->{$this->monthColumn} = $nowDT->format('m');
        }
    }
}
