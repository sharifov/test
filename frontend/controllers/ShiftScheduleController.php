<?php

namespace frontend\controllers;

use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use src\auth\Auth;
use src\model\shiftSchedule\entity\userShiftSchedule\UserShiftSchedule;
use Yii;
use src\model\shiftSchedule\entity\shiftScheduleRule\search\SearchShiftScheduleRule;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class ShiftScheduleController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
//        $searchModel = new SearchShiftScheduleRule();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        //$currentMonth = date('m');

        $scheduleTypeList = null;

        $curMonth = mktime(0, 0, 0, date("m"), 1, date("Y"));
        $prevMonth = mktime(0, 0, 0, date("m") - 1, 1, date("Y"));
        $nextMonth = mktime(0, 0, 0, date("m") + 1, 1, date("Y"));

        $monthList[date('Y-m', $prevMonth)] = date('F, Y', $prevMonth);
        $monthList[date('Y-m', $curMonth)] = date('F, Y', $curMonth);
        $monthList[date('Y-m', $nextMonth)] = date('F, Y', $nextMonth);

        $data = UserShiftSchedule::find()
            ->select(['uss_sst_id', 'uss_year' => 'YEAR(uss_start_utc_dt)', 'uss_month' => 'MONTH(uss_start_utc_dt)'])
            ->where(['uss_user_id' => Auth::id()])
            ->andWhere([
                'YEAR(uss_start_utc_dt)' => date('Y', $curMonth),
                'MONTH(uss_start_utc_dt)' => date('m', $curMonth),
                ])
            ->groupBy(['uss_sst_id', 'uss_year', 'uss_month'])
            ->asArray()
            ->all();

        $scheduleTypeData = [];
        $scheduleSumData = [];
        if ($data) {
            foreach ($data as $item) {
                $scheduleTypeData[$item['uss_sst_id']] = $item['uss_sst_id'];
                $month = $item['uss_year'] . '-' . $item['uss_month'];
                $scheduleSumData[$item['uss_sst_id']][$month] = $item;
            }
        }

        if ($scheduleTypeData) {
            $scheduleTypeList = ShiftScheduleType::find()->where(['sst_id' => $scheduleTypeData])
                ->orderBy(['sst_sort_order' => SORT_ASC])->all();
        }


        // VarDumper::dump($data, 10, true); exit;


        return $this->render('index', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
              'monthList' => $monthList,
              'scheduleTypeList' => $scheduleTypeList,
              'scheduleSumData' => $scheduleSumData,
        ]);
    }
}
