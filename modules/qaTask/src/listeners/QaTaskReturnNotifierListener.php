<?php

namespace modules\qaTask\src\listeners;

use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use modules\qaTask\src\useCases\qaTask\returnTask\QaTaskReturnEvent;
use Yii;
use common\models\Notifications;
use modules\qaTask\src\entities\qaTaskActionReason\QaTaskActionReason;
use sales\repositories\NotFoundException;
use sales\repositories\user\UserRepository;
use yii\helpers\Url;

/**
 * Class QaTaskReturnNotifierListener
 *
 * @property UserRepository $userRepository
 */
class QaTaskReturnNotifierListener
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(QaTaskReturnEvent $event): void
    {
        if (!$event->oldAssignedUserId || !$event->getChangeStateLog()->creatorId) {
            return;
        }

        if ($event->oldAssignedUserId === $event->getChangeStateLog()->creatorId) {
            return;
        }

        try {
            $creator = $this->userRepository->find($event->getChangeStateLog()->creatorId);
        } catch (NotFoundException $e) {
            Yii::error('Not found employee (' . $event->getChangeStateLog()->creatorId . ')', 'QaTaskReturnNotifierListener:not found Creator User');
            return;
        }
        try {
            $oldAssigned = $this->userRepository->find($event->oldAssignedUserId);
        } catch (NotFoundException $e) {
            Yii::error('Not found employee (' . $event->oldAssignedUserId . ')', 'QaTaskReturnNotifierListener:not found Old Assigned User');
            return;
        }

        $task = $event->task;

        $subject = Yii::t('email', 'You task #{id} has been returned to {status} by {username}', [
            'id' => $task->t_id,
            'status' => QaTaskStatus::getName($event->getChangeStateLog()->endStatusId),
            'username' => $creator->username,
        ]);

        $reason = '';
        if ($reasonModel = QaTaskActionReason::findOne($event->getChangeStateLog()->reasonId)) {
            $reason = $reasonModel->tar_name;
        }

        $body = Yii::t('email', "You task #{id} has been returned to {status} by {username} ({role}). Reason: {reason}. {url}",
            [
                'id' => $task->t_id,
                'status' => QaTaskStatus::getName($event->getChangeStateLog()->endStatusId),
                'username' => $creator->username,
                'role' => implode(',', $creator->getRoles(true)),
                'reason' => $reason,
                'url' => Url::toRoute( ['/qa-task/qa-task/view', 'gid' => $task->t_gid], true),
            ]);

        if (Notifications::create($oldAssigned->id, $subject, $body, Notifications::TYPE_INFO, true)) {
            //Notifications::socket($oldAssigned->id, null, 'getNewNotification', [], true);
            Notifications::sendSocket('getNewNotification', ['user_id' => $oldAssigned->id]);
        } else {
            Yii::error(
                'Not created Email notification to employee_id: ' . $oldAssigned->id . ', task: ' . $task->t_id,
                'QaTaskReturnNotifierListener:sendNotification'
            );
        }
    }
}
