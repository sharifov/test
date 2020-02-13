<?php

namespace modules\qaTask\src\listeners;

use common\models\Notifications;
use modules\qaTask\src\entities\qaTaskActionReason\QaTaskActionReason;
use Yii;
use modules\qaTask\src\useCases\qaTask\takeOver\QaTaskTakeOverEvent;
use sales\repositories\NotFoundException;
use sales\repositories\user\UserRepository;
use yii\helpers\Url;

/**
 * Class QaTaskTakeOverNotifierListener
 *
 * @property UserRepository $userRepository
 */
class QaTaskTakeOverNotifierListener
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(QaTaskTakeOverEvent $event): void
    {
        if (!$event->oldAssignedUserId || !$event->getChangeStateLog()->assignedId) {
            return;
        }

        try {
            $oldOwner = $this->userRepository->find($event->oldAssignedUserId);
        } catch (NotFoundException $e) {
            Yii::error('Not found employee (' . $event->oldAssignedUserId . ')', 'QaTaskTakeOverNotifierListener:not found Old Assigned User');
            return;
        }
        try {
            $newOwner = $this->userRepository->find($event->getChangeStateLog()->assignedId);
        } catch (NotFoundException $e) {
            Yii::error('Not found employee (' . $event->getChangeStateLog()->assignedId . ')', 'QaTaskTakeOverNotifierListener:not New Assigned User');
            return;
        }

        $task = $event->task;

        $subject = Yii::t('email', 'You task #{id} has been taken by {username}', ['id' => $task->t_id, 'username' => $newOwner->username]);

        $description = '';
        if ($reason = QaTaskActionReason::findOne($event->getChangeStateLog()->reasonId)) {
            $description = $reason->tar_name;
        }

        $body = Yii::t('email', "You task #{id} has been taken by {username} ({role}). Reason: {reason}. {url}",
            [
                'id' => $task->t_id,
                'username' => $newOwner->username,
                'role' => implode(',', $newOwner->getRoles(true)),
                'reason' => $description,
                'url' => Url::toRoute( ['/qa-task/qa-task/view', 'gid' => $task->t_gid], true),
            ]);

        if (Notifications::create($oldOwner->id, $subject, $body, Notifications::TYPE_INFO, true)) {
            //Notifications::socket($oldOwner->id, null, 'getNewNotification', [], true);
            Notifications::sendSocket('getNewNotification', ['user_id' => $oldOwner->id]);
        } else {
            Yii::error(
                'Not created Email notification to employee_id: ' . $oldOwner->id . ', task: ' . $task->t_id,
                'QaTaskTakeOverNotifierListener:sendNotification'
            );
        }
    }
}
