<?php

namespace common\components\jobs;

use common\models\Call;
use common\models\DepartmentPhoneProject;
use src\model\call\services\RepeatMessageCallJobCreator;
use yii\queue\JobInterface;

/**
 * Class CallQueueRepeatMessageJob
 *
 * @property $callId
 * @property $departmentPhoneProjectId
 * @property $createdTime
 */
class CallQueueRepeatMessageJob extends BaseJob implements JobInterface
{
    public $callId;
    public $departmentPhoneProjectId;
    public $createdTime;

    public function __construct($callId, $departmentPhoneProjectId, $createdTime)
    {
        $this->callId = $callId;
        $this->departmentPhoneProjectId = $departmentPhoneProjectId;
        $this->createdTime = $createdTime;
        parent::__construct();
    }

    public function execute($queue)
    {
        $this->waitingTimeRegister();
        try {
            $call = $this->findCall();
            if (
                !
                (
                    $call->isStatusQueue()
                    && $call->getData()->repeat->departmentPhoneId === $this->departmentPhoneProjectId
                    && $call->getData()->repeat->createdJobTime === $this->createdTime
                )
            ) {
                return;
            }

            $params = $this->findParams();

            $result = \Yii::$app->comms->repeatMessage([
                'callSid' => $call->c_call_sid,
                'language' => $params['language'],
                'voice' => $params['voice'],
                'say' => $params['say'],
                'play' => $params['play'],
                'holdPlay' => $params['holdPlay']
            ]);

            if ($result['error']) {
                $code = 21220; // CODE_INVALID_CALL_STATE - call is not in-progress
                if ($result['code'] !== $code) {
                    \Yii::error([
                        'message' => 'Repeat message command Error',
                        'result' => $result,
                        'callId' => $this->callId,
                        'departmentPhoneProjectId' => $this->departmentPhoneProjectId,
                    ], 'CallQueueRepeatMessageJob');
                }
                return;
            }

            (new RepeatMessageCallJobCreator())->create($call, $this->departmentPhoneProjectId, $params);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'callId' => $this->callId,
                'departmentPhoneProjectId' => $this->departmentPhoneProjectId,
            ], 'CallQueueRepeatMessageJob');
        }
    }

    private function findCall(): Call
    {
        $call = Call::findOne($this->callId);
        if (!$call) {
            throw new \DomainException('Not found Call.');
        }
        return $call;
    }

    private function findParams(): array
    {
        $phone = DepartmentPhoneProject::findOne($this->departmentPhoneProjectId);
        if (!$phone) {
            throw new \DomainException('Not found DepartmentPhoneProject.');
        }
        $params = @json_decode($phone->dpp_params, true);

        $ivrParams = $params['ivr'] ?? [];

        if (empty($ivrParams['hold_play'])) {
            throw new \DomainException('Not found ivr.hold_play param.');
        }

        $repeatParams = $params['queue_repeat'] ?? [];

        if (!$repeatParams) {
            throw new \DomainException('Not found queue_repeat Params.');
        }

        if (!$repeatParams['enable']) {
            throw new \DomainException('Queue_repeat Params disabled.');
        }

        $repeatParams['holdPlay'] = $ivrParams['hold_play'];

        return $repeatParams;
    }
}
