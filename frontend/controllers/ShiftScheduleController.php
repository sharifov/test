<?php

namespace frontend\controllers;

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


        $curMonth = mktime(0, 0, 0, date("m"), 1, date("Y"));
        $prevMonth = mktime(0, 0, 0, date("m") - 1, 1, date("Y"));
        $nextMonth = mktime(0, 0, 0, date("m") + 1, 1, date("Y"));

        $monthList[date('m', $prevMonth)] = date('F, Y', $prevMonth);
        $monthList[date('m', $curMonth)] = date('F, Y', $curMonth);
        $monthList[date('m', $nextMonth)] = date('F, Y', $nextMonth);

        $data = UserShiftSchedule::find()
            ->select(['uss_type_id', 'uss_year' => 'YEAR(uss_start_utc_dt)', 'uss_month' => 'MONTH(uss_start_utc_dt)'])
            ->where(['uss_user_id' => Auth::id()])
            ->andWhere([
                'YEAR(uss_start_utc_dt)' => date('Y', $curMonth),
                'MONTH(uss_start_utc_dt)' => date('m', $curMonth),
                ])
            ->groupBy(['uss_type_id', 'uss_year', 'uss_month'])
            ->asArray()
            ->all();

        // VarDumper::dump($data, 10, true); exit;


        return $this->render('index', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
              'monthList' => $monthList
        ]);
    }
}
