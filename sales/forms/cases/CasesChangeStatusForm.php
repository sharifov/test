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
 * @property string $caseGid
 */
class CasesChangeStatusForm extends Model
{

    public $status;
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
            ['status', 'integer'],
            ['status', 'in', 'range' => array_keys($this->getStatusList()), 'message' => 'This status disallow'],

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

}