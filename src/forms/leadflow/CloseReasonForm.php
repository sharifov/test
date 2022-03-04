<?php

namespace src\forms\leadflow;

use common\models\Lead;
use src\model\leadStatusReason\entity\LeadStatusReason;
use src\model\leadStatusReason\entity\LeadStatusReasonQuery;
use yii\base\Model;

class CloseReasonForm extends Model
{
    public $reasonKey;
    public $leadGid;
    public $reason;

    public function __construct(Lead $lead, $config = [])
    {
        $this->leadGid = $lead->gid;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['reasonKey', 'leadGid'], 'required'],
            ['reasonKey', 'exist', 'skipOnEmpty' => false, 'skipOnError' => true, 'targetClass' => LeadStatusReason::class, 'targetAttribute' => ['reasonKey' => 'lsr_key']],
            ['reason', 'required', 'when' => function (): bool {
                return (bool)((LeadStatusReasonQuery::getLeadStatusReasonByKey($this->reasonKey))->lsr_comment_required ?? false);
            }, 'skipOnError' => true],
            ['reason', 'string', 'max' => 255],
        ];
    }
}
