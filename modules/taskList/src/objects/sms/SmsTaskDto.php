<?php

namespace modules\taskList\src\objects\sms;

use common\models\Sms;
use yii\helpers\ArrayHelper;

class SmsTaskDto extends \stdClass
{
    public ?string $project_key = null;

    public function __construct(Sms $sms)
    {
        $this->project_key = $sms->sProject->project_key ?? null;
    }

    public function toArray(): array
    {
        return ArrayHelper::toArray($this);
    }
}
