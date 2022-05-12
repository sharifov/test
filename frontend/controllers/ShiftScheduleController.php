<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\query\UserGroupAssignQuery;
use common\models\query\UserGroupQuery;
use common\models\UserGroup;
use common\models\UserGroupAssign;
use Exception;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\ShiftScheduleTypeLabel;
use modules\shiftSchedule\src\entities\userShiftAssign\UserShiftAssign;
use modules\shiftSchedule\src\entities\userShiftSchedule\search\SearchUserShiftSchedule;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;
use modules\shiftSchedule\src\forms\ShiftScheduleCreateForm;
use modules\shiftSchedule\src\forms\SingleEventCreateForm;
use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
use modules\shiftSchedule\src\services\UserShiftScheduleService;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use src\repositories\NotFoundException;
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
    private UserShiftScheduleService $shiftScheduleService;

    public function __construct($id, $module, UserShiftScheduleService $shiftScheduleService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->shiftScheduleService = $shiftScheduleService;
    }

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
                            'generate-user-schedule', 'legend-ajax', 'calendar', 'calendar-events-ajax', 'add-event', 'update-single-event'],
                        'allow' => \Yii::$app->abac->can(
                            null,
                            ShiftAbacObject::ACT_MY_SHIFT_SCHEDULE,
                            ShiftAbacObject::ACTION_ACCESS
                        ),
                        'roles' => ['@'],
                    ],
                    /** @abac ShiftAbacObject::ACT_USER_SHIFT_SCHEDULE, ShiftAbacObject::ACTION_ACCESS, Access to page shift-schedule/user */
                    [
                        'actions' => ['user'],
                        'allow' => \Yii::$app->abac->can(
                            null,
                            ShiftAbacObject::ACT_USER_SHIFT_SCHEDULE,
                            ShiftAbacObject::ACTION_ACCESS
                        ),
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['delete-event'],
                        'allow' => \Yii::$app->abac->can(
                            null,
                            ShiftAbacObject::OBJ_USER_SHIFT_EVENT,
                            ShiftAbacObject::ACTION_DELETE
                        ),
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['add-single-event'],
                        'allow' => \Yii::$app->abac->can(
                            null,
                            ShiftAbacObject::OBJ_USER_SHIFT_EVENT,
                            ShiftAbacObject::ACTION_CREATE_ON_DOUBLE_CLICK
                        ),
                        'roles' => ['@'],
                    ]
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


//        $ids = UserShiftScheduleService::getUserEventListId($user->id, '2022-05-22 00:00:00', '2022-05-23 00:00:00',
//            [UserShiftSchedule::STATUS_PENDING],//[]
//            [ShiftScheduleType::SUBTYPE_HOLIDAY]
//        );
//        VarDumper::dump($ids, 10, true);
        // exit;

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
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUser(int $id): string
    {
        $user = $this->findUserModel($id);
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


//        $ids = UserShiftScheduleService::getUserEventListId($user->id, '2022-05-22 00:00:00', '2022-05-23 00:00:00',
//            [UserShiftSchedule::STATUS_PENDING],//[]
//            [ShiftScheduleType::SUBTYPE_HOLIDAY]
//        );
//        VarDumper::dump($ids, 10, true);
        // exit;

        return $this->render('user', [
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
     * @param int|null $userId
     * @return array
     */
    public function actionMyDataAjax(?int $userId = null): array
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = $userId ?: Auth::id();

        $startDt = Yii::$app->request->get('start', date('Y-m-d'));
        $endDt = Yii::$app->request->get('end', date('Y-m-d'));

        $timelineList = UserShiftScheduleService::getTimelineListByUser($userId, $startDt, $endDt);
        return UserShiftScheduleService::getCalendarTimelineJsonData($timelineList);
    }

    /**
     * @return string
     */
    public function actionLegendAjax(): string
    {
        $scheduleTypes = ShiftScheduleType::find()->where(['sst_enabled' => true])->all();
        return $this->renderPartial('partial/_legend', [
            'scheduleTypes' => $scheduleTypes,
        ]);
    }


    /**
     * @param int|null $userId
     * @return Response
     * @throws Exception
     */
    public function actionGenerateExample(?int $userId): Response
    {
        if ($userId) {
            $route = ['shift-schedule/user', 'id' => $userId];
        } else {
            $userId = Auth::id();
            $route = ['shift-schedule/index'];
        }

        $cnt = UserShiftScheduleService::generateExampleDataByUser($userId);

        if ($cnt > 0) {
            Yii::$app->session->addFlash('success', 'Successfully: Generate example data (' . $cnt . ')!');
        } else {
            Yii::$app->session->addFlash('error', 'Error: Generate example data is empty!');
        }
        return $this->redirect($route);
    }

    /**
     * @param int|null $userId
     * @return Response
     */
    public function actionRemoveUserData(?int $userId): Response
    {
        if ($userId) {
            $route = ['shift-schedule/user', 'id' => $userId];
        } else {
            $userId = Auth::id();
            $route = ['shift-schedule/index'];
        }

        if (UserShiftScheduleService::removeDataByUser($userId)) {
            Yii::$app->session->addFlash('success', 'Successfully: Remove example data UserId (' . $userId . ')!');
        }
        return $this->redirect($route);
    }

    /**
     * @param int|null $userId
     * @return Response
     */
    public function actionGenerateUserSchedule(?int $userId): Response
    {

        if ($userId) {
            $route = ['shift-schedule/user', 'id' => $userId];
        } else {
            $userId = Auth::id();
            $route = ['shift-schedule/index'];
        }

        $limit = SettingHelper::getShiftScheduleDaysLimit();
        $offset = SettingHelper::getShiftScheduleDaysOffset();
        $data = UserShiftScheduleService::generateUserSchedule($limit, $offset, null, [$userId]);

        if ($data) {
            Yii::$app->session->addFlash('success', 'Successfully: Generate User Schedule data (' . count($data) . ')!');
        } else {
            Yii::$app->session->addFlash('warning', 'Warning: Generate User Schedule data is empty!');
        }
        return $this->redirect($route);
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

        if ($event->uss_user_id !== Auth::id() && (!Auth::user()->isAdmin() && !Auth::user()->isSuperAdmin())) {
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
        $resourceList = [];
        $groupIds = [];

        $userGroups = UserGroup::find()
            ->where(['ug_disable' => false])
            ->join('inner join', UserGroupAssign::tableName(), 'ug_id = ugs_group_id')
            ->andWhere(['ugs_user_id' => Auth::id()])
            ->orderBy(['ug_name' => SORT_ASC])
//            ->limit(5)
            ->all();

        if ($userGroups) {
            foreach ($userGroups as $key => $group) {
                $resource = [
                    'id' => 'ug-' . $group->ug_id,
                    'name' => $group->ug_name,
                    'color' => '#1dab2f',
                    'title' => $group->ug_key,
                    'collapsed' => $key !== 0
                ];

                $users = Employee::find()
                    ->joinWith(['userGroupAssigns'])
                    ->where(['ugs_group_id' => $group->ug_id])
                    ->andWhere(['status' => Employee::STATUS_ACTIVE])
                    ->orderBy(['username' => SORT_ASC])
                    ->all();
                if ($users) {
                    $userList = [];
                    foreach ($users as $user) {
                        $userList[] = [
                            'id' => 'us-' . $user->id,
                            'name' => $user->username,
                            'color' => '#1dab2f',
                            'title' => $user->email
                        ];
                    }
                    $resource['title'] = 'users: ' . count($userList);
                    $resource['children'] = $userList;
                }
                $resourceList[] = $resource;
                $groupIds[] = $group->ug_id;
            }
        }

        return $this->render('calendar', [
            'resourceList' => $resourceList,
            'groupIds' => $groupIds
        ]);
    }

    /**
     * @return array
     */
    public function actionCalendarEventsAjax(): array
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $data = [];

        $startDt = Yii::$app->request->get('start', date('Y-m-d', strtotime('-1 day')));
        $endDt = Yii::$app->request->get('end', date('Y-m-d H:i:s', strtotime('+1 day')));
        $groups = Yii::$app->request->get('groups', '');
        $groups = explode(',', (string)$groups);
        $timelineList = UserShiftScheduleQuery::getTimelineListByUser($startDt, $endDt, $groups);
        $data['data'] = UserShiftScheduleHelper::getCalendarEventsData($timelineList);

        return $data;
    }

    public function actionAddEvent()
    {
        $form = new ShiftScheduleCreateForm();

        $usersGroupAssign = [];
        if ($form->load(Yii::$app->request->post()) && !$form->getUsersByGroups && $form->validate()) {
            $timelineList = $this->shiftScheduleService->createManual($form, Auth::id(), Auth::user()->timezone ?: null);
            $data = UserShiftScheduleHelper::getCalendarEventsData($timelineList);

            return '<script>(function() {$("#modal-md").modal("hide");let timelineData = ' . json_encode($data) . ';addTimelineEvent(timelineData);})();</script>';
        }

        if ($form->userGroups) {
            $usersGroupAssign = UserGroupAssignQuery::getGroupedDataByGroups($form->userGroups);
            $usersGroupAssign = ArrayHelper::map($usersGroupAssign, 'userId', 'username', 'groupName');
        }

        $form->getUsersByGroups = false;
        return $this->renderAjax('partial/_shift_schedule_create_form', [
            'model' => $form,
            'usersGroupAssign' => $usersGroupAssign
        ]);
    }

    public function actionAddSingleEvent()
    {
        $form = new SingleEventCreateForm();

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $event = $this->shiftScheduleService->createSingleManual($form, Auth::id(), Auth::user()->timezone ?: null);
            $data = UserShiftScheduleHelper::getCalendarEventsData([$event]);
            return '<script>(function() {$("#modal-md").modal("hide");let timelineData = ' . json_encode($data) . ';addTimelineEvent(timelineData);createNotify("Success", "Event created successfully", "success")})();</script>';
        }

        if (!Yii::$app->request->isPost) {
            $userIdCreateFor = Yii::$app->request->get('userId', null);
            $startDate = Yii::$app->request->get('startDate', null);
            if (!$user = Employee::findOne(['id' => $userIdCreateFor])) {
                throw new yii\web\MethodNotAllowedHttpException('User not found by id: ' . $userIdCreateFor, 404);
            }

            $form->userId = $userIdCreateFor;
            $startDateTime = (new \DateTimeImmutable($startDate));
            $endDateTime = $startDateTime->add(new \DateInterval('PT' . UserShiftSchedule::DEFAULT_DURATION_HOURS . 'H'));
            $interval = $startDateTime->diff($endDateTime);
            $form->defaultDuration = $interval->format('%H:%I');
            $form->dateTimeRange = $startDateTime->format('Y-m-d H:i') . ' - ' . $endDateTime->format('Y-m-d H:i');
            $form->status = UserShiftSchedule::STATUS_APPROVED;
        }

        return $this->renderAjax('partial/_shift_schedule_create_form_single_event', [
            'singleEventForm' => $form
        ]);
    }

    public function actionDeleteEvent()
    {
        $shiftId = Yii::$app->request->post('shiftId');

        $userShiftSchedule = UserShiftSchedule::findOne($shiftId);
        if (!$userShiftSchedule) {
            return $this->asJson([
                'error' => true,
                'message' => 'Shift not found by id:' . $shiftId
            ]);
        }
        if (!$userShiftSchedule->delete()) {
            return $this->asJson([
                'error' => true,
                'message' => $userShiftSchedule->getErrorSummary(true)[0]
            ]);
        }
        return $this->asJson([
            'error' => false,
            'message' => 'Shift deleted successfully'
        ]);
    }

    public function actionUpdateSingleEvent()
    {
        $data = Yii::$app->request->post();

        $event = UserShiftSchedule::findOne((int)$data['eventId']);
        if (!$event) {
            return $this->asJson([
                'error' => true,
                'message' => 'Event not found: by id: ' . $data['eventId']
            ]);
        }

        $timezone = Auth::user()->timezone ?: null;

        $startDateTime = new \DateTimeImmutable($data['startDate'], $timezone ? new \DateTimeZone($timezone) : null);
        $startDateTime = $startDateTime->setTimezone(new \DateTimeZone('UTC'));
        $endDateTime = new \DateTimeImmutable($data['endDate'], $timezone ? new \DateTimeZone($timezone) : null);
        $endDateTime = $endDateTime->setTimezone(new \DateTimeZone('UTC'));
        $interval = $startDateTime->diff($endDateTime);
        $diffMinutes = $interval->i + ($interval->h * 60);

        $event->uss_start_utc_dt = $startDateTime->format('Y-m-d H:i:s');
        $event->uss_end_utc_dt = $endDateTime->format('Y-m-d H:i:s');
        $event->uss_duration = $diffMinutes;
        if ($data['newUserId'] !== $data['oldUserId']) {
            $event->uss_user_id = $data['newUserId'];
        }

        $event->save();

        return $this->asJson([
            'error' => false,
            'message' => ''
        ]);
    }

    /**
     * @param int $userId
     * @return Employee|null
     * @throws NotFoundHttpException
     */
    protected function findUserModel(int $userId): ?Employee
    {
        if (($model = Employee::findOne($userId)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested User does not exist.');
    }
}
