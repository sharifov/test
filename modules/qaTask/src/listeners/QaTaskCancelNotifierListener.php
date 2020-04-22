<?php

namespace modules\qaTask\src\listeners;

use frontend\widgets\notification\NotificationMessage;
use modules\qaTask\src\useCases\qaTask\cancel\QaTaskCancelEvent;
use Yii;
use common\models\Notifications;
use modules\qaTask\src\entities\qaTaskActionReason\QaTaskActionReason;
use sales\repositories\NotFoundException;
use sales\repositories\user\UserRepository;
use yii\helpers\Url;

/**
 * Class QaTaskCancelNotifierListener
 *
 * @property UserRepository $userRepository
 */
class QaTaskCancelNotifierListener
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(QaTaskCancelEvent $event): void
    {
        if (!$event->getChangeStateLog()->assignedId || !$event->getChangeStateLog()->creatorId) {
            return;
        }

        if ($event->getChangeStateLog()->assignedId === $event->getChangeStateLog()->creatorId) {
            return;
        }

        try {
            $creator = $this->userRepository->find($event->getChangeStateLog()->creatorId);
        } catch (NotFoundException $e) {
            Yii::error('Not found employee (' . $event->getChangeStateLog()->creatorId . ')', 'QaTaskCancelNotifierListener:not found Creator User');
            return;
        }
        try {
            $assigned = $this->userRepository->find($event->getChangeStateLog()->assignedId);
        } catch (NotFoundException $e) {
            Yii::error('Not found employee (' . $event->getChangeStateLog()->assignedId . ')', 'QaTaskCancelNotifierListener:not found Assigned User');
            return;
        }

        $task = $event->task;

        $subject = Yii::t('email', 'You task #{id} has been canceled by {username}', ['id' => $task->t_id, 'username' => $creator->username]);

        $reason = '';
        if ($reasonModel = QaTaskActionReason::findOne($event->getChangeStateLog()->reasonId)) {
            $reason = $reasonModel->tar_name;
        }

        $body = Yii::t('email', "You task #{id} has been canceled by {username} ({role}). Reason: {reason}. {url}",
            [
                'id' => $task->t_id,
                'username' => $creator->username,
                'role' => implode(',', $creator->getRoles(true)),
                'reason' => $reason,
                'url' => Url::toRoute( ['/qa-task/qa-task/view', 'gid' => $task->t_gid], true),
            ]);

        if ($ntf = Notifications::create($assigned->id, $subject, $body, Notifications::TYPE_INFO, true)) {
            //Notifications::socket($assigned->id, null, 'getNewNotification', [], true);
            $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
            Notifications::publish('getNewNotification', ['user_id' => $assigned->id], $dataNotification);
        } else {
            Yii::error(
                'Not created Email notification to employee_id: ' . $assigned->id . ', task: ' . $task->t_id,
                'QaTaskCancelNotifierListener:sendNotification'
            );
        }
    }
}
