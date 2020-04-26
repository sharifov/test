<?php

namespace modules\qaTask\src\listeners;

use common\components\Purifier;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use modules\qaTask\src\entities\qaTaskActionReason\QaTaskActionReason;
use Yii;
use modules\qaTask\src\useCases\qaTask\takeOver\QaTaskTakeOverEvent;
use sales\repositories\NotFoundException;
use sales\repositories\user\UserRepository;

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
            Yii::error('Not found employee (' . $event->getChangeStateLog()->assignedId . ')', 'QaTaskTakeOverNotifierListener:not found New Assigned User');
            return;
        }

        $task = $event->task;

        $subject = Yii::t('email', 'You task (Id: {id}) has been taken by {username}', ['id' => $task->t_id, 'username' => $newOwner->username]);

        $reason = '';
        if ($reasonModel = QaTaskActionReason::findOne($event->getChangeStateLog()->reasonId)) {
            $reason = $reasonModel->tar_name;
        }

        $body = Yii::t('email', "You task (Id: {id}) has been taken by {username} ({role}). Reason: {reason}.",
            [
                'id' => Purifier::createQaTaskShortLink($task),
                'username' => $newOwner->username,
                'role' => implode(',', $newOwner->getRoles(true)),
                'reason' => $reason,
            ]);

        if ($ntf = Notifications::create($oldOwner->id, $subject, $body, Notifications::TYPE_INFO, true)) {
            //Notifications::socket($oldOwner->id, null, 'getNewNotification', [], true);
            $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
            Notifications::publish('getNewNotification', ['user_id' => $oldOwner->id], $dataNotification);
        } else {
            Yii::error(
                'Not created Email notification to employee_id: ' . $oldOwner->id . ', task: ' . $task->t_id,
                'QaTaskTakeOverNotifierListener:sendNotification'
            );
        }
    }
}
