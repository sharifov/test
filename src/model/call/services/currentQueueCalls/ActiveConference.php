<?php

namespace src\model\call\services\currentQueueCalls;

use yii\base\Model;

class ActiveConference extends Model
{
    public $sid;
    public $duration;
    public $participants;
    public $recordingDisabled;

    public function getData(): array
    {
        return $this->getAttributes();
    }
}
