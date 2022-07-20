<?php

namespace modules\shiftSchedule\src\reports;

use common\models\Employee;
use modules\shiftSchedule\src\entities\userShiftSchedule\search\AgentShiftSummaryReportSearch;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use yii\helpers\ArrayHelper;

class AgentShiftSummaryReport extends Employee
{
    public $uss_count;

    public function getTypes(AgentShiftSummaryReportSearch $searchModel): array
    {
        $date = $searchModel->getParsedStartDate();
        $userShiftScheduleList = $this->hasOne(UserShiftSchedule::class, ['uss_user_id' => 'id'])
            ->select([
                'uss_user_id',
                'uss_sst_id',
                'uss_duration' => 'SUM(uss_duration)',
                'uss_count' => 'COUNT(*)',
            ])
            ->andWhere([
                'uss_status_id' => $searchModel->statuses
            ])
            ->andWhere(['between', 'DATE(uss_start_utc_dt)', $date['from'], $date['to']])
            ->groupBy(['uss_sst_id', 'uss_user_id'])
            ->asArray()
            ->all();

        return ArrayHelper::index($userShiftScheduleList, 'uss_sst_id');
    }
}
