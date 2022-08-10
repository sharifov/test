<?php

namespace common\components\jobs;

use common\models\Lead;
use modules\lead\src\abac\taskLIst\LeadTaskListAbacDto;
use modules\lead\src\abac\taskLIst\LeadTaskListAbacObject;
use modules\lead\src\services\LeadTaskListService;
use src\helpers\app\AppHelper;
use Yii;
use yii\queue\JobInterface;

class UserTaskAssignJob extends BaseJob implements JobInterface
{
    private int $clientId;

    public function __construct(int $clientId, $timeStart = null, array $config = [])
    {
        $this->clientId = $clientId;

        parent::__construct($timeStart, $config);
    }

    public function execute($queue)
    {
        $this->waitingTimeRegister();

        try {
            $leads = Lead::find()
                ->byClient($this->clientId)
                ->andWhere(['NOT', ['employee_id' => null]])
                ->andWhere(['l_is_test' => 0])
                ->all();

            foreach ($leads as $lead) {
                if ($lead->employee) {
                    /** @abac $leadTaskListAbacDto, LeadTaskListAbacObject::PROCESSING_TASK, LeadTaskListAbacObject::ACTION_ACCESS, Lead to task List processing checker */
                    $canProcessingTask = Yii::$app->abac->can(
                        new LeadTaskListAbacDto($lead, $lead->employee_id),
                        LeadTaskListAbacObject::PROCESSING_TASK,
                        LeadTaskListAbacObject::ACTION_ACCESS,
                        $lead->employee
                    );

                    if ($canProcessingTask) {
                        (new LeadTaskListService($lead))->assign();
                    }
                }
            }
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['clientId'] = $this->clientId;
            \Yii::warning($message, 'UserTaskAssignJob:execute:Exception');
        } catch (\Throwable $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['clientId'] = $this->clientId;
            \Yii::error($message, 'UserTaskAssignJob:execute:Throwable');
        }
    }
}
