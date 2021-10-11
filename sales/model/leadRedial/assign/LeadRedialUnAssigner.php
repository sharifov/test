<?php

namespace sales\model\leadRedial\assign;

use common\models\Notifications;
use common\models\UserCallStatus;
use sales\model\leadRedial\entity\CallRedialUserAccess;
use sales\model\leadRedial\entity\CallRedialUserAccessRepository;
use sales\model\leadRedial\entity\CallRedialUserAccessWithCallStatus;
use sales\model\user\entity\userStatus\UserStatus;
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

    public function acceptRedialCall(int $userId, int $leadId): void
    {
        $accesses = CallRedialUserAccess::find()->byUserId($userId)->all();
        foreach ($accesses as $access) {
            $this->unAssign($access);
        }
    }

    public function acceptCall(int $userId): void
    {
        $accesses = CallRedialUserAccess::find()->byUserId($userId)->all();
        foreach ($accesses as $access) {
            $this->unAssign($access);
        }
    }

    public function createCall(int $userId): void
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
        $accesses = CallRedialUserAccessWithCallStatus::find()
            ->select(['*', 'us_is_on_call as userIsOnCall'])
            ->byLeadId($leadId)
            ->withExpired()
            ->leftJoin(UserStatus::tableName(), 'us_user_id = crua_user_id')
            ->all();
        if (!$accesses) {
            return false;
        }

        /** @var CallRedialUserAccessWithCallStatus $access */
        foreach ($accesses as $access) {
            $this->unAssign($access);
            if (!$access->userIsOnCall) {
                $this->offPhone($access->crua_user_id, $date);
                $this->missedCallNotification($access->crua_user_id);
            }
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
