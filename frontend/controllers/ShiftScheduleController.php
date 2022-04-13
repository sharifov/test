<?php

namespace frontend\controllers;

use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use src\model\shiftSchedule\entity\shift\Shift;
use src\model\shiftSchedule\entity\shiftScheduleRule\ShiftScheduleRule;
use src\model\shiftSchedule\entity\userShiftSchedule\search\SearchUserShiftSchedule;
use src\model\shiftSchedule\entity\userShiftSchedule\UserShiftSchedule;
use Yii;
use src\model\shiftSchedule\entity\shiftScheduleRule\search\SearchShiftScheduleRule;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;

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

        $monthList[date('Y-n', $prevMonth)] = date('F (n), Y', $prevMonth);
        $monthList[date('Y-n', $curMonth)] = date('F (n), Y', $curMonth);
        $monthList[date('Y-n', $nextMonth)] = date('F (n), Y', $nextMonth);

        $minDate = date('Y-m-d H:i:s', $prevMonth);
        $maxDate = date('Y-m-d H:i:s', mktime(0, 0, 0, date("m") + 2, 1, date("Y")));

        $data = UserShiftSchedule::find()
            ->select(['uss_sst_id', 'uss_year' => 'YEAR(uss_start_utc_dt)',
                'uss_month' => 'MONTH(uss_start_utc_dt)',
                'uss_cnt' => 'COUNT(*)',
                'uss_duration' => 'SUM(uss_duration)',
                ])
            ->where(['uss_user_id' => Auth::id()])
            ->andWhere(['AND',
                ['>=', 'uss_start_utc_dt', $minDate],
                ['<=', 'uss_start_utc_dt', $maxDate]
                ])
            ->andWhere(['uss_status_id' => [UserShiftSchedule::STATUS_APPROVED, UserShiftSchedule::STATUS_DONE]])
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

//
//        VarDumper::dump($monthList, 10, true); exit;
//        VarDumper::dump($data, 10, true); exit;

        $userTimeZone = 'local'; //'UTC'; //'Europe/Chisinau'; //Auth::user()->userParams->up_timezone ?? 'local';

        $user = Auth::user();
        $searchModel = new SearchUserShiftSchedule();

        $startDate = Yii::$app->request->get('startDate', date('Y-m-d'));
        $endDate = Yii::$app->request->get('endDate', date('Y-m-d', strtotime('+1 day')));

        $dataProvider = $searchModel->searchByUserId(Yii::$app->request->queryParams, $user->id, $startDate, $endDate);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
              'monthList' => $monthList,
              'scheduleTypeList' => $scheduleTypeList,
              'scheduleSumData' => $scheduleSumData,
              'userTimeZone' => $userTimeZone,
                'user' => $user,
        ]);
    }

    /**
     * @return array
     */
    public function actionMyDataAjax(): array
    {
        $data = [];
        $userId = Auth::id();

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $startDt = Yii::$app->request->get('start', date('Y-m-d'));
        $endDt = Yii::$app->request->get('end', date('Y-m-d'));

        $timelineList = UserShiftSchedule::find()
            ->where(['uss_user_id' => $userId])
            ->andWhere(['AND',
                ['>=', 'uss_start_utc_dt', date('Y-m-d H:i:s', strtotime($startDt))],
                ['<=', 'uss_start_utc_dt', date('Y-m-d H:i:s', strtotime($endDt))]
            ])
            ->all();


        if ($timelineList) {
            foreach ($timelineList as $item) {
                $dataItem = [
                    'id' => $item->uss_id,
                    //groupId: '999',
                    'title' => $item->getScheduleTypeKey() . '-' . $item->uss_id,
                    'description' => $item->getScheduleTypeTitle() . "\r\n" . ', duration: ' .
                        Yii::$app->formatter->asDuration($item->uss_duration * 60),
                        //. "\r\n" . $item->uss_description,
                    'start' => date('c', strtotime($item->uss_start_utc_dt)),
                    'end' => date('c', strtotime($item->uss_end_utc_dt)),
                    'color' => $item->shiftScheduleType ? $item->shiftScheduleType->sst_color : 'gray',

                    'display' => 'block', // 'list-item' , 'background'
                    //'textColor' => 'black' // an option!
                    'extendedProps' => [
                        'icon' => $item->shiftScheduleType->sst_icon_class,
                    ]
                ];

                if (!in_array($item->uss_status_id, [UserShiftSchedule::STATUS_DONE, UserShiftSchedule::STATUS_APPROVED])) {
                    $dataItem['borderColor'] = '#000000';
                    $dataItem['title'] .= ' (' . $item->getStatusName() . ')';
                    $dataItem['description'] .= ' (' . $item->getStatusName() . ')';
                    //$data[$item->uss_id]['extendedProps']['icon'] = 'rgb(255,0,0)';
                }

                $data[] = $dataItem;
            }
        }

        return $data;
    }


    /**
     * @throws \Exception
     */
    public function actionGenerateExample(): \yii\web\Response
    {
        $userId = Auth::id();

        $statuses = array_keys(UserShiftSchedule::getTypeList());
        $shiftList = array_keys(Shift::getList());
        $shiftRuleList = array_keys(ShiftScheduleRule::getList());
        $shiftTypeList = array_keys(ShiftScheduleType::getList());

        $minutes = [0, 10, 20, 30, 40];

        //VarDumper::dump($statuses, 10, true); exit;

        $cnt = 0;

        for ($i = 1; $i <= 90; $i++) {
            $hour = random_int(12, 22);
            $min = $minutes[random_int(1, count($minutes)) - 1];
            $timeStart = mktime($hour, $min, 0, date("m") - 1, $i, date("Y"));
            $duration = random_int(2, 8) * 60 + $minutes[random_int(1, count($minutes)) - 1];
            $timeEnd = mktime($hour, $duration + $min, 0, date("m") - 1, $i, date("Y"));


            $statusId = $statuses[random_int(0, count($statuses) - 1)];
            $shiftId = $shiftList[random_int(0, count($shiftList) - 1)];
            $shiftRuleId = $shiftRuleList[random_int(0, count($shiftRuleList) - 1)];
            $shiftTypeId = $shiftTypeList[random_int(0, count($shiftTypeList) - 1)];

            $tl = new UserShiftSchedule();
            $tl->uss_user_id = $userId;
            $tl->uss_sst_id = $shiftTypeId;
            $tl->uss_type_id = random_int(1, 2);
            $tl->uss_start_utc_dt = date('Y-m-d H:i:s', $timeStart);
            $tl->uss_end_utc_dt = date('Y-m-d H:i:s', $timeEnd);
            $tl->uss_duration = $duration;
            $tl->uss_ssr_id = $shiftRuleId;
            $tl->uss_shift_id = $shiftId;
            $tl->uss_status_id = $statusId;
            $tl->uss_description = 'Description UserShiftSchedule ' . $tl->uss_start_utc_dt;
            if ($tl->save()) {
                $cnt++;
            }
        }

        if ($cnt > 0) {
            Yii::$app->session->addFlash('success', 'Successfully: Generate example data (' . $cnt . ')!');
        } else {
            Yii::$app->session->addFlash('error', 'Error: Generate example data is empty!');
        }
        return $this->redirect(['index']);
    }

    public function actionRemoveUserData(): \yii\web\Response
    {
        $userId = Auth::id();
        if (UserShiftSchedule::deleteAll(['uss_user_id' => $userId])) {
            Yii::$app->session->addFlash('success', 'Successfully: Remove example data UserId (' . $userId . ')!');
        }
        return $this->redirect(['index']);
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     */
    public function actionGetEvent(): string
    {
        $eventId = (int) Yii::$app->request->get('id');

        if (!$eventId) {
            throw new BadRequestHttpException('Invalid request param');
        }

        $event = UserShiftSchedule::find()->where(['uss_id' => $eventId])->limit(1)->one();

        if (!$event) {
            throw new NotFoundHttpException('Not exist this Shift Schedule (' . $eventId . ')');
        }

        if ($event->uss_user_id !== Auth::id()) {
            throw new NotAcceptableHttpException('Permission Denied (' . $eventId . ')');
        }

        try {
            //$userTimeZone = Auth::user()->userParams->up_timezone ?? 'local';
            return $this->renderAjax('partial/_get_event', [
                'event' => $event,
                //'user' => Auth::user(),
                //'userTimeZone' => $userTimeZone
            ]);
        } catch (\DomainException $e) {
//            return $this->renderAjax('_error', [
//                'error' => $e->getMessage()
//            ]);
            Yii::error(AppHelper::throwableLog($e), 'ShiftScheduleController:actionGetEvent:DomainException');
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableLog($e), 'ShiftScheduleController:actionGetEvent:Throwable');
        }
        throw new BadRequestHttpException();
    }
}
