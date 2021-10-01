<?php

namespace sales\model\leadRedial\assign;

use common\components\purifier\Purifier;
use common\models\Notifications;
use common\models\UserCallStatus;
use frontend\widgets\notification\NotificationMessage;
use sales\model\leadRedial\entity\CallRedialUserAccess;
use sales\model\leadRedial\entity\CallRedialUserAccessRepository;
use yii\helpers\VarDumper;

/**
 * Class LeadRedialAssigner
 *
 * @property CallRedialUserAccessRepository $callRedialUserAccessRepository
 */
class LeadRedialUnAssigner
{
    private CallRedialUserAccessRepository $callRedialUserAccessRepository;

    public function __construct(CallRedialUserAccessRepository $callRedialUserAccessRepository)
    {
        $this->callRedialUserAccessRepository = $callRedialUserAccessRepository;
    }

    public function acceptCall(int $userId): void
    {
        $accesses = CallRedialUserAccess::find()->byUserId($userId)->all();
        foreach ($accesses as $access) {
            $this->unAssign($access);
        }
    }

    public function emptyQueue(int $userId): void
    {
        $accesses = CallRedialUserAccess::find()->byUserId($userId)->all();
        foreach ($accesses as $access) {
            $this->unAssign($access);
        }
        Notifications::publish('resetPriorityCall', ['user_id' => $userId], ['data' => ['command' => 'resetPriorityCall']]);
    }

    public function unAssignByLeadWithTimeExpired(int $leadId, \DateTimeImmutable $date): bool
    {
        $accesses = CallRedialUserAccess::find()->byLeadId($leadId)->withExpired()->all();
        if (!$accesses) {
            return false;
        }
        foreach ($accesses as $access) {
            $this->unAssign($access);
            $this->offPhone($access->crua_user_id, $date);
            $this->missedCallNotification($access->crua_user_id);
        }
        return true;
    }

    private function missedCallNotification(int $userId): void
    {
        Notifications::createAndPublish(
            $userId,
            'Missed Call',
            'Missed redial call',
            Notifications::TYPE_WARNING,
            true
        );
    }

    private function offPhone(int $userId, \DateTimeImmutable $date): void
    {
        $callStatus = UserCallStatus::occupied($userId, $date);
        if (!$callStatus->save()) {
            \Yii::error(VarDumper::dumpAsString($callStatus->errors), 'LeadRedialUnAssigner:offPhone:save');
        } else {
            Notifications::publish(
                'updateUserCallStatus',
                ['user_id' => $callStatus->us_user_id],
                ['id' => 'ucs' . $callStatus->us_id, 'type_id' => $callStatus->us_type_id]
            );
        }
    }

    private function unAssign(CallRedialUserAccess $access): void
    {
        try {
            $this->callRedialUserAccessRepository->remove($access);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Cant remove Call Redial User Access',
                'exception' => $e->getMessage(),
            ], 'LeadRedialUnAssigner');
        }
    }
}
