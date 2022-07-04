<?php

namespace console\controllers;

use common\models\Setting;
use DomainException;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;
use modules\shiftSchedule\src\forms\ScheduleDecisionForm;
use modules\shiftSchedule\src\services\ShiftScheduleRequestService;
use modules\shiftSchedule\src\services\UserShiftScheduleService;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use src\repositories\NotFoundException;
use Throwable;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class ShiftController extends Controller
{
    /**
     * @return void
     */
    public function actionGenerateUserSchedule(): void
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
        $timeStart = microtime(true);

        if (SettingHelper::getShiftScheduleGenerateEnabled()) {
            $limit = SettingHelper::getShiftScheduleDaysLimit();
            $offset = SettingHelper::getShiftScheduleDaysOffset();
            $data = UserShiftScheduleService::generateUserSchedule($limit, $offset, null);
        } else {
            echo 'Warning: SiteSetting[shift_schedule][generate_enabled] is not true!' . PHP_EOL;
        }

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        echo Console::renderColoredString('%g --- Count: %w' . count($data) . '%g, Execute Time: %w[' . $time .
            ' s] %g'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
    }

    public function actionFindAndDeclineShiftsWithPendingStatusIfWtOrWtrExists()
    {
        $shifts = UserShiftScheduleQuery::getPendingListWithIntersectionByWTAndWTR();

        if (empty($shifts)) {
            return false;
        }

        foreach ($shifts as $shift) {
            try {
                $ssr = ShiftScheduleRequest::findOne(['ssr_uss_id' => $shift->uss_id]);

                if (empty($ssr)) {
                    throw new NotFoundException("Not exist this Shift Schedule Request ({$shift->uss_id})");
                }

                $decisionFormModel = new ScheduleDecisionForm();
                $decisionFormModel->status = UserShiftSchedule::STATUS_CANCELED;
                $decisionFormModel->description = 'Declined by system';

                $shift->uss_status_id = $decisionFormModel->status;
                $shift->uss_description = $decisionFormModel->description;

                if ($shift->save()) {
                    ShiftScheduleRequestService::saveDecision($ssr, $decisionFormModel, $shift->user);
                }
            } catch (DomainException $e) {
                Yii::error(AppHelper::throwableLog($e), 'ShiftController:actionFindAndDeclineShiftsWithPendingStatusIfWtOrWtrExists:DomainException');
            } catch (Throwable $e) {
                Yii::error(AppHelper::throwableLog($e), 'ShiftController:actionFindAndDeclineShiftsWithPendingStatusIfWtOrWtrExists:Throwable');
            }
        }
    }
}
