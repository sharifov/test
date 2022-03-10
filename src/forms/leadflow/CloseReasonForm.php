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

    private const REASON_MAX_STRING_CHAR = 255;

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
            [['reason'], 'string'],
            [['reason'], 'filter', 'filter' => 'trim'],
            ['reason', 'validateReason', 'skipOnEmpty' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'reasonKey' => 'Reason',
            'reason' => 'Comment'
        ];
    }

    public function validateReason($attribute, $params, $validator)
    {
        $commentRequired = (bool)((LeadStatusReasonQuery::getLeadStatusReasonByKey($this->reasonKey))->lsr_comment_required ?? false);
        if ($commentRequired) {
            if (empty($this->reason)) {
                $this->addError('reason', 'Comment cannot be blank');
            }

            if (strlen($this->reason) > self::REASON_MAX_STRING_CHAR) {
                $this->addError('reason', 'Comment should contain at most ' . self::REASON_MAX_STRING_CHAR . ' characters');
            }
        } else {
            $this->reason = '';
        }
    }
}
