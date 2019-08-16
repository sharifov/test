<?php

namespace sales\forms\cases;

use common\models\Employee;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatusHelper;
use sales\repositories\cases\CasesRepository;
use yii\base\Model;

/**
 * Class CasesChangeStatusForm
 *
 * @property int $status
 * @property string $message
 *
 * @property Cases $case
 * @property Employee $user
 * @property CasesRepository $casesRepository
 */
class CasesChangeStatusForm extends Model
{

    public $status;
    public $message;
    public $case_id;

    private $user;
    private $case;

    private $casesRepository;



    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->casesRepository = \Yii::createObject(CasesRepository::class);
        //$this->case = $case;
        //$this->user = $user;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['status', 'case_id'], 'required'],
            ['message', 'string'],
            ['status', 'in', 'range' => array_keys(CasesStatusHelper::STATUS_LIST)], //$this->getStatusList())],
            [['case_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['case_id' => 'cs_id']],
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
            'case_id' => 'Case'
        ];
    }

    public function getStatusList(): array
    {
        $this->case = $this->casesRepository->find((int) $this->case_id);
        $rules = Cases::statusRouteRules((int) $this->case->cs_status);

        if($rules) {
            foreach ($rules as $statusId) {
                if(isset(CasesStatusHelper::STATUS_LIST[$statusId])) {
                    $statusList[$statusId] = CasesStatusHelper::STATUS_LIST[$statusId];
                }
            }
        }

        return $statusList;
    }

}