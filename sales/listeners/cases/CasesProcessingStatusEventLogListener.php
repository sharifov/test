<?php

namespace sales\listeners\cases;

use common\components\purifier\Purifier;
use common\models\Employee;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use sales\entities\cases\CasesStatus;
use sales\entities\cases\events\CasesProcessingStatusEvent;
use sales\services\cases\CaseStatusLogService;
use Yii;
use yii\helpers\Html;

/**
 * Class CasesProcessingStatusEventLogListener
 *
 * @property CaseStatusLogService $caseStatusLogService
 */
class CasesProcessingStatusEventLogListener
{
    private $caseStatusLogService;

    public function __construct(CaseStatusLogService $caseStatusLogService)
    {
        $this->caseStatusLogService = $caseStatusLogService;
    }

    public function handle(CasesProcessingStatusEvent $event): void
    {
        try {
            $this->caseStatusLogService->log(
                $event->case->cs_id,
                CasesStatus::STATUS_PROCESSING,
                $event->oldStatus,
                $event->newOwnerId,
                $event->creatorId,
                $event->description
            );

            if ($event->newOwnerId !== $event->creatorId) {
                $creator = Employee::findOne($event->creatorId);
                $title = 'Title: New Case Assigned';
                $linkToCase = Purifier::createCaseShortLink($event->case);
                $message = 'Message: Case (' . $linkToCase . ') has been assigned to you by user ' . Html::encode( $creator ? $creator->username : '');

                if ($ntf = Notifications::create($event->newOwnerId, $title, $message, Notifications::TYPE_WARNING, true)) {
                    $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                    Notifications::publish('getNewNotification', ['user_id' => $event->newOwnerId], $dataNotification);
                }
            }

        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:CasesProcessingStatusEventLogListener');
        }
    }
}
