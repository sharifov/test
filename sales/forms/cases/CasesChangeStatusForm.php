<?php

namespace sales\forms\cases;

use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use yii\base\Model;

/**
 * Class CasesChangeStatusForm
 *
 * @property int $status
 * @property string $message
 * @property int $caseStatus
 * @property array $statusList
 * @property string $caseGid
 */
class CasesChangeStatusForm extends Model
{

    public $status;
    public $reason;
    public $message;

    public $caseGid;
    private $caseStatus;

    /**
     * CasesChangeStatusForm constructor.
     * @param Cases $case
     * @param array $config
     */
    public function __construct(Cases $case, $config = [])
    {
        parent::__construct($config);
        $this->caseStatus = $case->cs_status;
        $this->caseGid = $case->cs_gid;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['status', 'required'],
            [['status'], 'integer'],
            ['status', 'in', 'range' => array_keys($this->getStatusList()), 'message' => 'This status disallow'],
            ['status', 'validateReason'],

            ['reason', 'string'],

            ['message', 'string'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'status' => 'Status',
            'reason' => 'Reason',
            'message' => 'Message',
        ];
    }

    /**
     * @return array
     */
    public function getStatusList(): array
    {
        $list = CasesStatus::getAllowList($this->caseStatus);
        if (isset($list[CasesStatus::STATUS_PROCESSING])) {
            unset($list[CasesStatus::STATUS_PROCESSING]);
        }
        return $list;
    }

    /**
     * @return array
     */
    public function getReasonsList(): array
    {
        return CasesStatus::getReasonListByStatus($this->status == '' ? null : $this->status);
    }

    /**
     * @param $attribute_name
     * @param $params
     * @return bool
     */
    public function validateReason($attribute_name, $params): bool
    {
        if (!empty(CasesStatus::STATUS_REASON_LIST[$this->status])) {

            if (!empty(CasesStatus::STATUS_REASON_LIST[$this->status][$this->reason])) {

                if ($this->reason == 'Other' && empty($this->message)) {
                    $this->addError('message', 'Type the reason');
                    return false;
                }

            } else {
                $this->addError('reason', 'Unknown Reason');
                return false;
            }
        }

        return true;
    }

    public function afterValidate(): void
    {
        if (!empty(CasesStatus::STATUS_REASON_LIST[$this->status]) && !empty($this->message)) {
            $this->message = sprintf('%s: %s', $this->reason, $this->message);
        } else {
            $this->message = $this->reason;
        }
    }

}