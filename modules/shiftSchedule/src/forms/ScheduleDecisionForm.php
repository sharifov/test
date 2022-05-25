<?php

namespace modules\shiftSchedule\src\forms;

use common\models\Employee;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use modules\shiftSchedule\src\services\ShiftScheduleRequestService;
use src\auth\Auth;
use yii\base\Model;

class ScheduleDecisionForm extends Model
{
    const DESCRIPTION_MAX_LENGTH = 1000;

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
                ShiftScheduleRequestService::sendNotification(
                    Employee::ROLE_AGENT,
                    $scheduleRequest,
                    ShiftScheduleRequestService::NOTIFICATION_TYPE_CREATE
                );
                ShiftScheduleRequestService::sendNotification(
                    Employee::ROLE_SUPERVISION,
                    $scheduleRequest,
                    ShiftScheduleRequestService::NOTIFICATION_TYPE_UPDATE
                );
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
}
