<?php

namespace frontend\controllers;

use Exception;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\services\UserShiftScheduleService;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use src\model\shiftSchedule\entity\userShiftSchedule\search\SearchUserShiftSchedule;
use src\model\shiftSchedule\entity\userShiftSchedule\UserShiftSchedule;
use Yii;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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
        $user = Auth::user();
        $scheduleTypeList = null;

        $curMonth = mktime(0, 0, 0, date("m"), 1, date("Y"));
        $prevMonth = mktime(0, 0, 0, date("m") - 1, 1, date("Y"));
        $nextMonth = mktime(0, 0, 0, date("m") + 1, 1, date("Y"));

        $monthList[date('Y-n', $prevMonth)] = date('F (n), Y', $prevMonth);
        $monthList[date('Y-n', $curMonth)] = date('F (n), Y', $curMonth);
        $monthList[date('Y-n', $nextMonth)] = date('F (n), Y', $nextMonth);

        $minDate = date('Y-m-d H:i:s', $prevMonth);
        $maxDate = date('Y-m-d H:i:s', mktime(0, 0, 0, date("m") + 2, 1, date("Y")));


        $data = UserShiftScheduleService::getUserShiftScheduleDataStats(
            $user->id,
            $minDate,
            $maxDate,
            [UserShiftSchedule::STATUS_APPROVED, UserShiftSchedule::STATUS_DONE]
        );

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
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $userId = Auth::id();
        $startDt = Yii::$app->request->get('start', date('Y-m-d'));
        $endDt = Yii::$app->request->get('end', date('Y-m-d'));

        $timelineList = UserShiftScheduleService::getTimelineListByUser($userId, $startDt, $endDt);
        return UserShiftScheduleService::getCalendarTimelineJsonData($timelineList);
    }


    /**
     * @return Response
     * @throws Exception
     */
    public function actionGenerateExample(): Response
    {
        $userId = Auth::id();
        $cnt = UserShiftScheduleService::generateExampleDataByUser($userId);

        if ($cnt > 0) {
            Yii::$app->session->addFlash('success', 'Successfully: Generate example data (' . $cnt . ')!');
        } else {
            Yii::$app->session->addFlash('error', 'Error: Generate example data is empty!');
        }
        return $this->redirect(['index']);
    }

    /**
     * @return Response
     */
    public function actionRemoveUserData(): Response
    {
        $userId = Auth::id();
        if (UserShiftScheduleService::removeDataByUser($userId)) {
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
