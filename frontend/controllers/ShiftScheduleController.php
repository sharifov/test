<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\query\UserGroupAssignQuery;
use common\models\query\UserGroupQuery;
use common\models\UserGroup;
use common\models\UserGroupAssign;
use Exception;
use modules\shiftSchedule\src\abac\dto\ShiftAbacDto;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\shiftSchedule\src\entities\shiftScheduleRule\ShiftScheduleRule;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\search\ShiftScheduleRequestSearch;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\ShiftScheduleTypeLabel;
use modules\shiftSchedule\src\entities\userShiftAssign\UserShiftAssign;
use modules\shiftSchedule\src\entities\userShiftSchedule\search\SearchUserShiftSchedule;
use modules\shiftSchedule\src\entities\userShiftSchedule\search\TimelineCalendarFilter;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;
use modules\shiftSchedule\src\entities\userShiftScheduleLog\search\UserShiftScheduleLogSearch;
use modules\shiftSchedule\src\forms\ShiftScheduleCreateForm;
use modules\shiftSchedule\src\forms\ShiftScheduleEditForm;
use modules\shiftSchedule\src\forms\SingleEventCreateForm;
use modules\shiftSchedule\src\forms\UserShiftCalendarMultipleUpdateForm;
use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
use modules\shiftSchedule\src\forms\ScheduleRequestForm;
use modules\shiftSchedule\src\services\ShiftScheduleRequestService;
use modules\shiftSchedule\src\services\UserShiftScheduleService;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use src\repositories\NotFoundException;
use Yii;
use yii\db\Transaction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
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
                    [
                        'actions' => ['ajax-multiple-delete', 'add-event', 'get-event', 'ajax-get-logs', 'ajax-edit-event-form', 'ajax-multiple-update'],
                        'allow' => true,
                        'roles' => ['@']
                    ],
                    /** @abac ShiftAbacObject::ACT_MY_SHIFT_SCHEDULE, ShiftAbacObject::ACTION_ACCESS, Access to page shift-schedule/index */
                    [
                        'actions' => ['index', 'my-data-ajax', 'generate-example', 'remove-user-data',
                            'generate-user-schedule', 'legend-ajax', 'calendar', 'calendar-events-ajax', 'update-single-event',
                            'schedule-request-ajax', 'schedule-pending-requests', 'schedule-request-history-ajax'],
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

        return $this->render('index', array_merge(
            [
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
                'subtypeList' => $subtypeList,
            ],
            $this->getSchedulePendingRequestsParams(),
        ));
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
     * @throws NotAcceptableHttpException
     */
    public function actionGenerateExample(?int $userId = null): Response
    {
        $abac = Yii::$app->abac;
        /** @abac ShiftAbacObject::ACT_MY_SHIFT_SCHEDULE, ShiftAbacObject::ACTION_GENERATE_EXAMPLE_DATA, Access to generate-example shift-schedule/* */
        if (!$abac->can(null, ShiftAbacObject::ACT_MY_SHIFT_SCHEDULE, ShiftAbacObject::ACTION_GENERATE_EXAMPLE_DATA)) {
            throw new NotAcceptableHttpException('Access denied');
        }
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
     * @throws NotAcceptableHttpException
     */
    public function actionRemoveUserData(?int $userId = null): Response
    {
        $abac = Yii::$app->abac;
        /** @abac ShiftAbacObject::ACT_MY_SHIFT_SCHEDULE, ShiftAbacObject::ACTION_REMOVE_ALL_USER_SCHEDULE, Access to remove-user-data shift-schedule/* */
        if (!$abac->can(null, ShiftAbacObject::ACT_MY_SHIFT_SCHEDULE, ShiftAbacObject::ACTION_REMOVE_ALL_USER_SCHEDULE)) {
            throw new NotAcceptableHttpException('Access denied');
        }
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
     * @throws NotAcceptableHttpException
     */
    public function actionGenerateUserSchedule(?int $userId = null): Response
    {
        $abac = Yii::$app->abac;
        /** @abac ShiftAbacObject::ACT_MY_SHIFT_SCHEDULE, ShiftAbacObject::ACTION_GENERATE_USER_SCHEDULE, Access to generate-user-schedule shift-schedule/* */
        if (!$abac->can(null, ShiftAbacObject::ACT_MY_SHIFT_SCHEDULE, ShiftAbacObject::ACTION_GENERATE_USER_SCHEDULE)) {
            throw new NotAcceptableHttpException('Access denied');
        }
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


        $dto = new ShiftAbacDto();
        $dto->setIsEventOwner($event->isOwner(Auth::id()));
        if (!Yii::$app->abac->can($dto, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_READ)) {
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
     * @param int|null $userId
     * @return string
     */
    public function actionCalendar(?int $userId = null): string
    {
        $timelineCalendarFilter = new TimelineCalendarFilter();
        $timelineCalendarFilter->load(Yii::$app->request->queryParams);
        $timelineCalendarFilter->userId = $userId ?? Auth::id();

        /** @abac ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_VIEW_ALL_EVENTS, Access to view all events in calendar widget */
        $canViewAllEvents = Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_VIEW_ALL_EVENTS);
        if ($canViewAllEvents) {
            $userGroups = UserGroupQuery::getList();
        } else {
            $userGroups = UserGroupQuery::getListByUser($timelineCalendarFilter->userId);
        }

        $timelineCalendarFilter->userGroups = array_keys($userGroups);

        return $this->render('calendar', [
            'timelineCalendarFilter' => $timelineCalendarFilter,
            'userGroups' => $userGroups
        ]);
    }

    /**
     * @return array
     */
    public function actionCalendarEventsAjax(): array
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $data = [
            'data' => [],
            'resources' => [],
            'error' => false,
            'message' => ''
        ];

        $timelineCalendarFilter = new TimelineCalendarFilter();
        $timelineCalendarFilter->load(Yii::$app->request->queryParams);
        if (!$timelineCalendarFilter->validate()) {
            $data['error'] = true;
            $data['message'] = $timelineCalendarFilter->getErrorSummary(true)[0];
        } else {
            /** @abac ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_VIEW_ALL_EVENTS, Access to view all events in calendar widget */
            $canViewAllEvents = Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_VIEW_ALL_EVENTS);
            $userGroups = UserGroupQuery::findUserGroups(!$canViewAllEvents ? Auth::id() : null, $timelineCalendarFilter->userGroups ?? []);

            [$resourceList] = UserShiftScheduleHelper::prepareResourcesForTimelineCalendar($userGroups, $timelineCalendarFilter->usersIds);

            $timelineList = UserShiftScheduleQuery::getTimelineListByUser($timelineCalendarFilter);
            $data['data'] = UserShiftScheduleHelper::getCalendarEventsData($timelineList);
            $data['resources'] = $resourceList;
        }

        return $data;
    }

    public function actionAddEvent()
    {
        /** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_CREATE, Create user shift schedule event */
        if (!Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_CREATE)) {
            throw new ForbiddenHttpException('Access Denied');
        }

        $form = new ShiftScheduleCreateForm();

        $usersGroupAssign = [];
        if ($form->load(Yii::$app->request->post()) && !$form->getUsersByGroups && $form->validate()) {
            $timelineList = $this->shiftScheduleService->createManual($form, Auth::user()->timezone ?: null);
            $data = UserShiftScheduleHelper::getCalendarEventsData($timelineList);

            return '<script>(function() {$("#modal-md").modal("hide");let timelineData = ' . json_encode($data) . ';addTimelineEvent(timelineData);createNotify("Success", "Event created successfully", "success")})();</script>';
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
            $event = $this->shiftScheduleService->createSingleManual($form, Auth::user()->timezone ?: null);
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
            $nowDateTime = new \DateTimeImmutable('now', ($timezone = Auth::user()->timezone) ? new \DateTimeZone($timezone) : null);
            $startDateTimeWithTimezone = new \DateTimeImmutable($startDate, ($timezone = Auth::user()->timezone) ? new \DateTimeZone($timezone) : null);

            if ($startDateTimeWithTimezone < $nowDateTime) {
                throw new BadRequestHttpException('Start DateTime must be more than now');
            }

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
        /** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_DELETE, Access to soft delete event in calendar widget */
        if (!Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_DELETE)) {
            throw new ForbiddenHttpException('Access denied');
        }

        $shiftId = Yii::$app->request->post('shiftId');
        $deletePermanently = Yii::$app->request->post('deletePermanently');

        $userShiftSchedule = UserShiftSchedule::findOne($shiftId);
        if (!$userShiftSchedule) {
            return $this->asJson([
                'error' => true,
                'message' => 'Shift not found by id:' . $shiftId
            ]);
        }

        if ($deletePermanently == 1) {
            /** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_PERMANENTLY_DELETE, Access to permanently delete event in calendar widget */
            if (!Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_PERMANENTLY_DELETE)) {
                throw new ForbiddenHttpException('Access denied');
            }
            if (!$userShiftSchedule->delete()) {
                return $this->asJson([
                    'error' => true,
                    'message' => $userShiftSchedule->getErrorSummary(true)[0]
                ]);
            }
            return $this->asJson([
                'error' => false,
                'message' => 'Shift deleted successfully',
            ]);
        }
        $userShiftSchedule->uss_status_id = UserShiftSchedule::STATUS_DELETED;
        $userShiftSchedule->save();
        $userShiftScheduleData = UserShiftScheduleHelper::getDataForCalendar($userShiftSchedule);
        return $this->asJson([
            'error' => false,
            'message' => 'Shift deleted successfully',
            'timelineData' => json_encode($userShiftScheduleData)
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
        $diffMinutes = $interval->days * 24 * 60 + $interval->i + ($interval->h * 60);

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

    /**
     * @return string|Response
     * @throws Exception
     */
    public function actionScheduleRequestAjax()
    {
        $request = Yii::$app->request;
        $scheduleRequestModel = new ScheduleRequestForm();
        if ($request->isPost) {
            if ($scheduleRequestModel->load($request->post()) && $scheduleRequestModel->validate()) {
                if (ShiftScheduleRequestService::saveRequest($scheduleRequestModel, Auth::user())) {
                    $success = true;
                }
            }
        } else {
            $scheduleRequestModel->setAttributesRequest($request);
        }

        return $this->renderAjax('partial/_schedule_request', [
            'scheduleRequestModel' => $scheduleRequestModel,
            'success' => $success ?? false,
        ]);
    }

    public function actionAjaxShitRulesList(?string $q = null, ?int $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $out = ['results' => ['id' => '', 'text' => '', 'selection' => '']];

        if ($q !== null) {
            $query = ShiftScheduleRule::find();
            $data = $query->select(['ssr_id', 'text' => 'ssr_title'])
                ->where(['like', 'ssr_title', $q])
                ->orWhere(['ssr_id' => (int) $q])
                ->limit(20)
                //->indexBy('id')
                ->asArray()
                ->all();

            if ($data) {
                foreach ($data as $n => $item) {
                    $text = $item['text'] . ' (' . $item['id'] . ')';
                    $data[$n]['text'] = self::formatText($text, $q);
                    $data[$n]['selection'] = $item['text'];
                }
            }

            $out['results'] = $data; //array_values($data);
        } elseif ($id > 0) {
            $rule = ShiftScheduleRule::findOne($id);
            $out['results'] = ['id' => $id, 'text' => $rule ? $rule->ssr_title : '', 'selection' => $rule ? $rule->ssr_title : ''];
        }
        return $out;
    }

    public function actionAjaxMultipleDelete(): Response
    {
        /** @abac ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_MULTIPLE_DELETE_EVENTS, Access to delete multiple events */
        if (!Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_MULTIPLE_DELETE_EVENTS)) {
            throw new ForbiddenHttpException('Access denied');
        }

        $eventIds = Yii::$app->request->post('selectedEvents', []);

        UserShiftSchedule::deleteAll(['uss_id' => $eventIds]);

        return $this->asJson([
            'error' => false,
            'message' => ''
        ]);
    }

    public function actionAjaxMultipleUpdate()
    {
        /** @abac ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_MULTIPLE_UPDATE_EVENTS, Access to update multiple events */
        if (!Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_MULTIPLE_UPDATE_EVENTS)) {
            throw new ForbiddenHttpException('Access denied');
        }

        $multipleUpdateForm = new UserShiftCalendarMultipleUpdateForm();

        if ($multipleUpdateForm->load(Yii::$app->request->post()) && $multipleUpdateForm->validate()) {
            $eventIds = \yii\helpers\Json::decode($multipleUpdateForm->eventIds);

            if (!is_array($eventIds)) {
                throw new BadRequestHttpException('Invalid JSON data for decode');
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $returnEventsData = [];
                $form = $multipleUpdateForm;
                if (empty($form->scheduleType) && empty($form->description) && empty($form->status) && empty($form->dateTimeRange)) {
                    throw new \RuntimeException('Please fill/change at least one field');
                }
                foreach ($eventIds as $eventId) {
                    $event = UserShiftSchedule::findOne((int)$eventId);
                    if (!$event) {
                        throw new BadRequestHttpException('Not found event');
                    }
                    $this->shiftScheduleService->editMultiple($multipleUpdateForm, $event, Auth::user()->timezone ?: null);
                    if (!$multipleUpdateForm->hasErrors()) {
                        $returnEventsData[] = UserShiftScheduleHelper::getDataForCalendar($event);
                    }
                }
                $transaction->commit();

                $jsCode = '';
                foreach ($eventIds as $eventId) {
                    $jsCode .= 'window.inst.removeEvent(' . $eventId . ');';
                }

                return '<script>(function() {$("#modal-md").modal("hide");' . $jsCode . ';let timelinesData = ' . json_encode($returnEventsData) . ';addTimelineEvents(timelinesData);$("#btn-check-all").trigger("click");createNotify("Success", "Event(s) updated successfully", "success")})();</script>';
            } catch (\RuntimeException $e) {
                $transaction->rollBack();
                $multipleUpdateForm->addError('general', $e->getMessage());
            } catch (\Throwable $throwable) {
                $transaction->rollBack();
                Yii::error(AppHelper::throwableLog($throwable), 'ShiftScheduleController:actionAjaxMultipleUpdate:Throwable');
                $multipleUpdateForm->addError('general', 'Internal Server Error');
            }
        }

        return $this->renderAjax('partial/_multiple_update_events_form', [
            'multipleUpdateForm' => $multipleUpdateForm
        ]);
    }

    /**
     * @param string $str
     * @param string $term
     * @return string
     */
    private static function formatText(string $str, string $term): string
    {
        return preg_replace('~' . $term . '~i', '<b style="color: #e15554"><u>$0</u></b>', $str);
    }

    private function getSchedulePendingRequestsParams(): array
    {
        $searchModel = new ShiftScheduleRequestSearch();
        $queryParams = array_merge_recursive(
            Yii::$app->request->queryParams,
            [
                $searchModel->formName() => [
                    'ssr_status_id' => $searchModel::STATUS_PENDING,
                ],
            ]
        );
        $userId = Auth::id();
        $dataProvider = $searchModel->searchByUsers(
            $queryParams,
            [$userId],
            date('Y-m-d', strtotime('now')),
            date('Y-m-d', strtotime('+1 year'))
        );

        return [
            'searchModelPendingRequests' => $searchModel,
            'dataProviderPendingRequests' => $dataProvider,
        ];
    }

    public function actionSchedulePendingRequests(): string
    {
        return $this->renderAjax('partial/_pending_requests', $this->getSchedulePendingRequestsParams());
    }

    /**
     * Show User Request History
     * @return string|Response
     * @throws Exception
     */
    public function actionScheduleRequestHistoryAjax(): string
    {
        $searchModel = new ShiftScheduleRequestSearch();
        $userId = Auth::id();
        $dataProvider = $searchModel->searchByUsers(Yii::$app->request->queryParams, [$userId]);

        return $this->renderAjax('partial/_request-history', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAjaxGetLogs($id)
    {
        /** @abac ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_VIEW_EVENT_LOG, Access to view event logs */
        if (!Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_VIEW_EVENT_LOG)) {
            throw new ForbiddenHttpException('Access denied');
        }

        if (Yii::$app->request->isAjax) {
            $searchModel = new UserShiftScheduleLogSearch();
            $params = Yii::$app->request->queryParams;
            $searchModel->ussl_uss_id = (int)$id;
            $dataProvider = $searchModel->search($params);

            return $this->renderAjax('partial/_event_logs', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'id' => $id,
            ]);
        }
        throw new BadRequestHttpException();
    }

    public function actionAjaxEditEventForm(): string
    {
        /** @abac ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_VIEW_EVENT_LOG, Access to view event logs */
        if (!Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_UPDATE)) {
            throw new ForbiddenHttpException('Access denied');
        }

        $form = new ShiftScheduleEditForm();

        if (Yii::$app->request->isPost && Yii::$app->request->isPjax) {
            if ($form->load(Yii::$app->request->post()) && $form->validate()) {
                $event = UserShiftSchedule::findOne($form->eventId);
                if (!$event) {
                    throw new BadRequestHttpException('Not found event');
                }
                $this->shiftScheduleService->edit($form, $event, Auth::user()->timezone ?: null);
                if (!$form->hasErrors()) {
                    $eventData = UserShiftScheduleHelper::getDataForCalendar($event);
                    return '<script>(function() {$("#modal-md").modal("hide");window.inst.removeEvent(' . $event->uss_id . ');let timelineData = ' . json_encode($eventData) . ';addTimelineEvent(timelineData);createNotify("Success", "Event updated successfully", "success")})();</script>';
                }
            }
        } else {
            $eventId = (int)Yii::$app->request->get('eventId');
            $event = UserShiftSchedule::findOne($eventId);
            if (!$event) {
                throw new BadRequestHttpException('Not found event');
            }

            $form->fillInByEvent($event, Auth::user()->timezone ?: null);
        }
        return $this->renderAjax('partial/_edit_event_form', [
            'model' => $form,
        ]);
    }
}
