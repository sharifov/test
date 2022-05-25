<?php

namespace modules\shiftSchedule\src\forms;

use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use src\auth\Auth;
use Yii;
use yii\base\Model;

class ScheduleDecisionForm extends Model
{
    const DESCRIPTION_MAX_LENGTH = 1000;

    public const NOTIFICATION_TO_AGENT = 'notification_to_agent';
    public const NOTIFICATION_TO_SUPERVISER = 'notification_to_superviser';
    public const NOTIFICATION_TYPE_CREATE = 'notification_create';
    public const NOTIFICATION_TYPE_UPDATE = 'notification_update';

    /**
     * @var string
     */
    public string $description = '';
    /**
     * @var int
     */
    public int $status = 0;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [
                'status',
                'required',
            ],
            [
                'description',
                'required',
            ],
            [
                'description',
                'string',
                'max' => self::DESCRIPTION_MAX_LENGTH,
            ],
        ];
    }

    /**
     * Save Decision request to Shift Schedule Request table
     * @param ShiftScheduleRequest $model
     * @return bool
     */
    public function saveDecision(ShiftScheduleRequest $model): bool
    {
        $scheduleRequest = new ShiftScheduleRequest();
        $scheduleRequest->attributes = $model->attributes;
        $scheduleRequest->ssr_status_id = $this->status;
        $scheduleRequest->ssr_description = $this->description;
        $scheduleRequest->ssr_updated_user_id = Auth::id();
        if ($scheduleRequest->getIsCanEditPreviousDate()) {
            if ($scheduleRequest->save()) {
                $this->sendNotification(self::NOTIFICATION_TO_AGENT, $scheduleRequest);
                $this->sendNotification(self::NOTIFICATION_TO_SUPERVISER, $scheduleRequest, self::NOTIFICATION_TYPE_UPDATE);
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'status' => 'Status',
            'description' => 'Description',
        ];
    }

    /**
     * Send Notification
     * @param string $whom
     * @param ShiftScheduleRequest $scheduleRequest
     * @param string|null $notificationType
     * @return void
     */
    public function sendNotification(string $whom, ShiftScheduleRequest $scheduleRequest, ?string $notificationType = null): void
    {
        $subject = 'Request Status';
        $authUser = Auth::user();
        $startTime = date('Y-m-d H:i:s', strtotime($scheduleRequest->srhUss->uss_start_utc_dt ?? ''));
        $endTime = date('Y-m-d H:i:s', strtotime($scheduleRequest->srhUss->uss_end_utc_dt ?? ''));
        if ($whom === self::NOTIFICATION_TO_AGENT) {
            $body = sprintf(
                'Your %s request for %s - %s was %s by %s',
                $scheduleRequest->getScheduleTypeTitle(),
                $startTime,
                $endTime,
                $scheduleRequest->getStatusName(),
                $authUser->username
            );
            $publishUserIds = [$scheduleRequest->ssr_created_user_id];
        } elseif ($whom === self::NOTIFICATION_TO_SUPERVISER) {
            if ($notificationType === self::NOTIFICATION_TYPE_CREATE) {
                $content = '%s request for %s - %s was created by %s';
            } elseif ($notificationType === self::NOTIFICATION_TYPE_UPDATE) {
                $content = '%s request for %s - %s was updated by %s';
            } else {
                $content = '%s request for %s - %s by %s';
            }
            $body = sprintf(
                $content,
                $scheduleRequest->getScheduleTypeTitle(),
                $startTime,
                $endTime,
                $authUser->username
            );
            $publishUserIds = $authUser->getSupervisionIdsByCurrentUser();
        }

        if (!empty($body) && !empty($publishUserIds)) {
            foreach ($publishUserIds as $userId) {
                if ($ntf = Notifications::create($userId, $subject, $body, Notifications::TYPE_INFO)) {
                    $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                    Notifications::publish('getNewNotification', ['user_id' => $userId], $dataNotification);
                }
            }
        }
    }
}
