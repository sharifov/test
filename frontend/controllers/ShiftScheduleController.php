<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\UserGroup;
use Exception;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\ShiftScheduleTypeLabel;
use modules\shiftSchedule\src\entities\userShiftAssign\UserShiftAssign;
use modules\shiftSchedule\src\entities\userShiftSchedule\search\SearchUserShiftSchedule;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\shiftSchedule\src\services\UserShiftScheduleService;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
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
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    /** @abac ShiftAbacObject::ACT_MY_SHIFT_SCHEDULE, ShiftAbacObject::ACTION_ACCESS, Access to page shift-schedule/index */
                    [
                        'actions' => ['index', 'my-data-ajax', 'generate-example', 'remove-user-data', 'get-event',
                            'generate-user-schedule', 'legend-ajax', 'calendar', 'calendar-events-ajax'],
                        'allow' => \Yii::$app->abac->can(
                            null,
                            ShiftAbacObject::ACT_MY_SHIFT_SCHEDULE,
                            ShiftAbacObject::ACTION_ACCESS
                        ),
                        'roles' => ['@'],
                    ],
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
        $scheduleTypeLabelList = null;

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

        $labelData = UserShiftScheduleService::getUserShiftScheduleLabelDataStats(
            $user->id,
            $minDate,
            $maxDate,
            [UserShiftSchedule::STATUS_APPROVED, UserShiftSchedule::STATUS_DONE]
        );

       // VarDumper::dump($labelData, 10, true); exit;

        $scheduleTypeData = [];
        $scheduleTypeLabelData = [];
        $scheduleSumData = [];
        $scheduleLabelSumData = [];

        if ($data) {
            foreach ($data as $item) {
                $scheduleTypeData[$item['uss_sst_id']] = $item['uss_sst_id'];
                $month = $item['uss_year'] . '-' . $item['uss_month'];
                $scheduleSumData[$item['uss_sst_id']][$month] = $item;
            }
        }
        unset($data);

        if ($labelData) {
            foreach ($labelData as $item) {
                $scheduleTypeLabelData[$item['stl_key']] = $item['stl_key'];
                $month = $item['uss_year'] . '-' . $item['uss_month'];
                $scheduleLabelSumData[$item['stl_key']][$month] = $item;
            }
        }
        unset($labelData);

        if ($scheduleTypeData) {
            $scheduleTypeList = ShiftScheduleType::find()->where(['sst_id' => $scheduleTypeData])
                ->orderBy(['sst_sort_order' => SORT_ASC])->all();
        }

        if ($scheduleTypeLabelData) {
            $scheduleTypeLabelList = ShiftScheduleTypeLabel::find()->where(['stl_key' => $scheduleTypeLabelData])
                ->orderBy(['stl_sort_order' => SORT_ASC])->all();
        }

//        VarDumper::dump($scheduleTypeLabelData, 10, true);
//        exit;
//
//        VarDumper::dump($monthList, 10, true); exit;
//        VarDumper::dump($data, 10, true); exit;

        $userTimeZone = 'local'; //'UTC'; //'Europe/Chisinau'; //Auth::user()->userParams->up_timezone ?? 'local';
        $searchModel = new SearchUserShiftSchedule();

        $startDate = Yii::$app->request->get('startDate', date('Y-m-d'));
        $endDate = Yii::$app->request->get('endDate', date('Y-m-d', strtotime('+1 day')));

        $dataProvider = $searchModel->searchByUserId(Yii::$app->request->queryParams, $user->id, $startDate, $endDate);

        $assignedShifts = UserShiftAssign::find()->where(['usa_user_id' => $user->id])->all();
        $subtypeList = ShiftScheduleType::getSubtypeList();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'monthList' => $monthList,

            'scheduleTypeList' => $scheduleTypeList,
            'scheduleTypeLabelList' => $scheduleTypeLabelList,

            'scheduleSumData' => $scheduleSumData,
            'scheduleLabelSumData' => $scheduleLabelSumData,

            'userTimeZone' => $userTimeZone,
            'user' => $user,
            'assignedShifts' => $assignedShifts,
            'subtypeList' => $subtypeList
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
     * @return array
     */
    public function actionLegendAjax()
    {
        //\Yii::$app->response->format = Response::FORMAT_JSON;

        $scheduleTypes = ShiftScheduleType::find()->where(['sst_enabled' => true])->all();


        return $this->renderPartial('partial/_legend', [
            'scheduleTypes' => $scheduleTypes,
        ]);
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
     * @return Response
     * @throws Exception
     */
    public function actionGenerateUserSchedule(): Response
    {
        $userId = Auth::id();
        $limit = SettingHelper::getShiftScheduleDaysLimit();
        $offset = SettingHelper::getShiftScheduleDaysOffset();
        $data = UserShiftScheduleService::generateUserSchedule($limit, $offset, null, [$userId]);

        if ($data) {
            Yii::$app->session->addFlash('success', 'Successfully: Generate User Schedule data (' . count($data) . ')!');
        } else {
            Yii::$app->session->addFlash('warning', 'Warning: Generate User Schedule data is empty!');
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

    /**
     * @return string
     */
    public function actionCalendar(): string
    {
        $user = Auth::user();

        $resourceList = [];

        $userGroups = UserGroup::find()
            ->where(['ug_disable' => false])
            ->andWhere(['like', 'ug_name', 'test'])
            ->orderBy(['ug_name' => SORT_ASC])
            //->limit(2)
            ->all();

        if ($userGroups) {
            foreach ($userGroups as $group) {
                $resource = [
                    'id' => 'ug-' . $group->ug_id,
                    'name' => $group->ug_name,
                    'color' => '#1dab2f',
                    'img' => '',
                    'title' => $group->ug_key,
                    //'collapsed' => true
                ];

                $users = Employee::find()
                    ->joinWith(['userGroupAssigns'])
                    ->where(['ugs_group_id' => $group->ug_id])
                    ->andWhere(['status' => Employee::STATUS_ACTIVE])
                    ->orderBy(['username' => SORT_ASC])
                    ->limit(7)
                    ->all();
                if ($users) {
                    $userList = [];
                    foreach ($users as $user) {
                        $userList[] = [
                            'id' => 'us-' . $user->id,
                            'name' => $user->username,
                            'color' => '#1dab2f',
                            //'img' => $user->getGravatarUrl(),
                            'title' => $user->email
                        ];
                    }
                    $resource['title'] = 'users: ' . count($userList);
                    $resource['children'] = $userList;
                }
                $resourceList[] = $resource;
            }
        }


//        $users = Employee::find()->all();
//
//        foreach ($users as $user) {
//            $resourceList[] = [
//                'id' => $user->id,
//                'name' => $user->username,
//                'color' => '#1dab2f',
//                'img' => $user->getGravatarUrl(),
//                'title' => $user->email
//            ];
//        }


        return $this->render('calendar', [
            'resourceList' => $resourceList
        ]);
    }

    /**
     * @return array
     */
    public function actionCalendarEventsAjax(): array
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $data = [];
        $resources = [];

        $userId = Auth::id();
        $startDt = Yii::$app->request->get('start', date('Y-m-d', strtotime('-1 day')));
        $endDt = Yii::$app->request->get('end', date('Y-m-d H:i:s', strtotime('+1 day')));

        $timelineList = UserShiftScheduleService::getTimelineListByUser($userId, $startDt, $endDt);


//        $users = Employee::find()->all();
//
//
//        foreach ($users as $user) {
//            $resources[] = ['id' => $user->id, 'name' => $user->username,
//                'color' => '#1dab2f', 'img' => $user->getGravatarUrl(),
//                'title' => $user->email
//            ];
//        }



//        $dataItem = [];
//        $dataItem[] = [
//            'id' => 987456,
//            //groupId: '999',
//            'title' => 'TEST 12:30-15:30 UTC',
//            'description' => 'TEST',
//            'start' => date('c', strtotime(date('Y-m-d 12:30:00'))),
//            'end' => date('c', strtotime(date('Y-m-d 15:30:00'))),
//            'color' => 'red',
//
//            'resource' => 'us-' . $userId, //random_int(1, 1),
//            'extendedProps' => [
//                'icon' => 'fa fa-user',
//            ]
//        ];

        //$data['resources'] = $resources;
        $data['data'] = UserShiftScheduleService::getCalendarEventsJsonData($timelineList);

        return $data;
    }
}
