<?php

namespace common\components\jobs;

use common\components\i18n\Formatter;
use common\models\Employee;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use modules\shiftSchedule\src\services\ShiftScheduleRequestService;
use src\helpers\app\AppHelper;
use yii\helpers\ArrayHelper;
use Yii;
use yii\queue\JobInterface;

class ShiftScheduleRequestNotificationsAfterSaveJob extends BaseJob implements JobInterface
{
    public ShiftScheduleRequest $shiftScheduleRequest;
    public Employee $employee;

    public function execute($queue): bool
    {
        $this->waitingTimeRegister();

        Yii::$app->set('formatter', Formatter::class);

        try {
            ShiftScheduleRequestService::sendNotification(
                Employee::ROLE_SUPERVISION,
                $this->shiftScheduleRequest,
                $this->employee,
                ShiftScheduleRequestService::NOTIFICATION_TYPE_CREATE
            );

            return true;
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable, true), ['RequestID' => $this->shiftScheduleRequest->ssr_id]);
            Yii::warning($message, 'ShiftScheduleSaveRequestNotificationListener::handle::Throwable');
        }

        return false;
    }
}
