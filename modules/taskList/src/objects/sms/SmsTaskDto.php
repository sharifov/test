<?php

namespace modules\taskList\src\objects\sms;

use common\models\Sms;
use yii\helpers\ArrayHelper;

class SmsTaskDto extends \stdClass
{
    public ?string $project_key = null;
    public ?string $template_type_key = null;
    public ?bool $sms_has_client = null;

    public function __construct(Sms $sms)
    {
        $this->project_key = $sms->sProject->project_key ?? null;
        $this->template_type_key = $sms->sTemplateType->stp_key ?? null;
        $this->sms_has_client = (bool) $sms->s_client_id;
    }
}
