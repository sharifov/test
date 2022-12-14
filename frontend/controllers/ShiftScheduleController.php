<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\Notifications;
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
use modules\shiftSchedule\src\entities\userShiftSchedule\search\AgentShiftSummaryReportSearch;
use modules\shiftSchedule\src\entities\userShiftSchedule\search\SearchUserShiftSchedule;
use modules\shiftSchedule\src\entities\userShiftSchedule\search\TimelineCalendarFilter;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleRepository;
use modules\shiftSchedule\src\entities\userShiftScheduleLog\search\UserShiftScheduleLogSearch;
use modules\shiftSchedule\src\forms\ShiftScheduleCreateForm;
use modules\shiftSchedule\src\forms\ShiftScheduleEditForm;
use modules\shiftSchedule\src\forms\SingleEventCreateForm;
use modules\shiftSchedule\src\forms\UserShiftCalendarMultipleUpdateForm;
use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
use modules\shiftSchedule\src\forms\ScheduleRequestForm;
use modules\shiftSchedule\src\reports\AgentShiftSummaryReport;
use modules\shiftSchedule\src\services\ShiftScheduleRequestService;
use modules\shiftSchedule\src\services\UserShiftScheduleService;
use modules\taskList\src\entities\userTask\UserTaskSearch;
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
                        'actions' => ['ajax-multiple-delete', 'add-multiple-events', 'ajax-event-details', 'ajax-get-logs',
                            'ajax-edit-event-form', 'ajax-multiple-update', 'ajax-edit-event', 'add-event', 'delete-event'],
                        'allow' => true,
                        'roles' => ['@']
                    ],
                    /** @abac ShiftAbacObject::ACT_MY_SHIFT_SCHEDULE, ShiftAbacObject::ACTION_ACCESS, Access to page shift-schedule/index */
                    [
                        'actions' => ['index', 'my-data-ajax', 'generate-example', 'remove-user-data',
                            'generate-user-schedule', 'legend-ajax', 'schedule-request-ajax', 'schedule-pending-requests', 'schedule-request-history-ajax'],
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
                    /** @abac ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_ACCESS, Access to shift calendar page */
                    [
                        'actions' => ['calendar', 'ajax-get-events'],
                        'allow' => \Yii::$app->abac->can(
                            null,
                            ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR,
                            ShiftAbacObject::ACTION_ACCESS
                        ),
                        'roles' => ['@'],
                    ],
                    /** @abac ShiftAbacObject::ACT_USER_SHIFT_SCHEDULE, ShiftAbacObject::ACTION_REMOVE_FUTURE_USER_SCHEDULE, Access to remove-future-user-data shift-schedule */
                    [
                        'actions' => ['remove-future-user-data'],
                        'allow' => \Yii::$app->abac->can(
                            null,
                            ShiftAbacObject::ACT_USER_SHIFT_SCHEDULE,
                            ShiftAbacObject::ACTION_REMOVE_FUTURE_USER_SCHEDULE
                        ),
                        'roles' => ['@'],
                    ],
                    /** @abac ShiftAbacObject::ACT_SUMMARY_REPORT, ShiftAbacObject::ACTION_ACCESS, Access to shift-schedule/summary-report */
                    [
                        'actions' => ['summary-report'],
                        'allow' => \Yii::$app->abac->can(
                            null,
                            ShiftAbacObject::ACT_SUMMARY_REPORT,
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


        $data = UserShiftScheduleQuery::getUserShiftScheduleDataStats(
            $user->id,
            $minDate,
            $maxDate,
            [UserShiftSchedule::STATUS_APPROVED, UserShiftSchedule::STATUS_DONE]
        );

        $labelData = UserShiftScheduleQuery::getUserShiftScheduleLabelDataStats(
            $user->id,
            $minDate,
            $maxDate,
            [UserShiftSchedule::STATUS_APPROVED, UserShiftSchedule::STATUS_DONE]
        );

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

        $userTimeZone = $user->timezone ?: 'UTC'; //'UTC'; //'Europe/Chisinau'; //Auth::user()->userParams->up_timezone ?? 'local';
        $searchModel = new SearchUserShiftSchedule();

        $startDate = Yii::$app->request->get('startDate', date('Y-m-d'));
        $endDate = Yii::$app->request->get('endDate', date('Y-m-d', strtotime('+1 day')));

        /** @abac null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS, Hide Soft Deleted Schedule Events */
        if (\Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS)) {
            $searchModel->enableExcludeDeleteStatus();
        }

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


        $data = UserShiftScheduleQuery::getUserShiftScheduleDataStats(
            $user->id,
            $minDate,
            $maxDate,
            [UserShiftSchedule::STATUS_APPROVED, UserShiftSchedule::STATUS_DONE]
        );

        $labelData = UserShiftScheduleQuery::getUserShiftScheduleLabelDataStats(
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

        /** @abac null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS, Hide Soft Deleted Schedule Events */
        if (\Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS)) {
            $searchModel->enableExcludeDeleteStatus();
        }

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
        /** @abac null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS, Hide Soft Deleted Schedule Events */
        $canHideSoftDeleted = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS);

        \Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = $userId ?: Auth::id();

        $startDt = Yii::$app->request->get('start', date('Y-m-d'));
        $endDt = Yii::$app->request->get('end', date('Y-m-d'));

        $timelineList = $canHideSoftDeleted ? UserShiftScheduleQuery::getTimelineListByUserExcludeDeletedEvents($userId, $startDt, $endDt) :
            UserShiftScheduleQuery::getTimelineListByUser($userId, $startDt, $endDt);

        $userTimeZone = Auth::user()->timezone ?: 'UTC';
        return UserShiftScheduleQuery::getCalendarTimelineJsonData($timelineList, $userTimeZone);
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

        if (UserShiftScheduleQuery::removeDataByUser($userId)) {
            Yii::$app->session->addFlash('success', 'Successfully: Remove example data UserId (' . $userId . ')!');
        }
        return $this->redirect($route);
    }

    /**
     * @param int|null $userId
     * @return Response
     */
    public function actionRemoveFutureUserData(?int $userId = null): Response
    {
        if ($userId) {
            $route = ['shift-schedule/user', 'id' => $userId];
        } else {
            $userId = Auth::id();
            $route = ['shift-schedule/index'];
        }

        if (UserShiftScheduleQuery::removeFutureDataByUser($userId)) {
            Yii::$app->session->addFlash('success', 'Successfully: Remove Future UserShift data UserId (' . $userId . ')!');
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
    public function actionAjaxEventDetails(): string
    {
        $eventId = (int)Yii::$app->request->get('id');

        if (!$eventId) {
            throw new BadRequestHttpException('Invalid request param');
        }

        $event = UserShiftSchedule::find()->where(['uss_id' => $eventId])->limit(1)->one();

        if (!$event) {
            throw new NotFoundHttpException('Not exist this Shift Schedule (' . $eventId . ')');
        }


        $dto = new ShiftAbacDto();
        $dto->setIsEventOwner($event->isOwner(Auth::id()));
        /** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_READ, Access to view event details */
        if (!Yii::$app->abac->can($dto, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_READ)) {
            throw new NotAcceptableHttpException('Permission Denied (' . $eventId . ')');
        }

        try {
            $userTimeZone = Auth::user()->timezone ?: 'UTC';

            $tsSearchModel = new UserTaskSearch();
            $tsDataProvider = $tsSearchModel->searchByShiftScheduleEventId(
                $this->request->queryParams,
                (int) $event->uss_id
            );

            return $this->renderAjax('partial/_get_event', [
                'event' => $event,
                'userTimeZone' => $userTimeZone,
                'searchModel' => $tsSearchModel,
                'dataProvider' => $tsDataProvider,
                //'user' => Auth::user(),
            ]);
        } catch (\DomainException $e) {
//            return $this->renderAjax('_error', [
//                'error' => $e->getMessage()
//            ]);
            Yii::error(AppHelper::throwableLog($e), 'ShiftScheduleController:actionAjaxEventDetails:DomainException');
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableLog($e), 'ShiftScheduleController:actionAjaxEventDetails:Throwable');
        }
        throw new BadRequestHttpException();
    }

    /**
     * @param int|null $userId
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionCalendar(?int $userId = null): string
    {
        $timelineCalendarFilter = new TimelineCalendarFilter();
        $timelineCalendarFilter->load(Yii::$app->request->queryParams);
        $timelineCalendarFilter->userId = $userId ?? Auth::id();

        /** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_VIEW_ALL_EVENTS, Access to view all events in calendar widget */
        $canViewAllEvents = Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_VIEW_ALL_EVENTS);
        if ($canViewAllEvents) {
            $userGroups = UserGroupQuery::getList();
        } else {
            $userGroups = UserGroupQuery::getListByUser($timelineCalendarFilter->userId);
        }

        return $this->render('calendar', [
            'timelineCalendarFilter' => $timelineCalendarFilter,
            'userGroups' => $userGroups
        ]);
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionAjaxGetEvents(): array
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
            /** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_VIEW_ALL_EVENTS, Access to view all events in calendar widget */
            $canViewAllEvents = Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_VIEW_ALL_EVENTS);
            if ($canViewAllEvents) {
                $userGroups = array_keys(UserGroupQuery::getList());
            } else {
                $userGroups = array_keys(UserGroupQuery::getListByUser($timelineCalendarFilter->userId));
            }

            $timelineCalendarFilter->userGroups = $timelineCalendarFilter->userGroups ?: $userGroups;
            $userGroups = UserGroupQuery::findUserGroupsAndAssignedUsers($timelineCalendarFilter);

            [$resourceList, $firstLevelResources] = UserShiftScheduleHelper::prepareResourcesForTimelineCalendar($userGroups, $timelineCalendarFilter->getParsedResources());

            $timelineList = UserShiftScheduleQuery::getCalendarTimelineListByUser($timelineCalendarFilter);
            $data['data'] = UserShiftScheduleHelper::getCalendarEventsData($timelineList);
            $data['resources'] = array_values($resourceList);
            $data['firstLevelResources'] = $firstLevelResources;
        }

        return $data;
    }

    public function actionAddMultipleEvents()
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

            return '<script>(function() {$("#modal-md").modal("hide");let timelineData = ' . json_encode($data) . ';window._timeline.addEvents(timelineData);createNotify("Success", "Event created successfully", "success")})();</script>';
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

    public function actionAddEvent()
    {
        /** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_CREATE, Create user shift schedule event */
        if (!Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_CREATE)) {
            throw new ForbiddenHttpException('Access Denied');
        }

        $form = new SingleEventCreateForm();

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $event = $this->shiftScheduleService->createSingleManual($form, Auth::user()->timezone ?: null);
            $data = UserShiftScheduleHelper::getCalendarEventsData([$event]);
            return '<script>(function() {$("#modal-md").modal("hide");let timelineData = ' . json_encode($data) . ';window._timeline.addEvent(timelineData);createNotify("Success", "Event created successfully", "success")})();</script>';
        }

        if (!Yii::$app->request->isPost) {
            $userIdCreateFor = Yii::$app->request->get('userId', null);
            $startDate = Yii::$app->request->get('startDate', null);
            if (!$user = Employee::findOne(['id' => $userIdCreateFor])) {
                throw new BadRequestHttpException('User not found by id: ' . $userIdCreateFor, 404);
            }

            $form->userId = $userIdCreateFor;
            $startDateTime = (new \DateTimeImmutable($startDate));
            $nowDateTime = new \DateTimeImmutable('now', ($timezone = Auth::user()->timezone) ? new \DateTimeZone($timezone) : null);
            $startDateTimeWithTimezone = new \DateTimeImmutable($startDate, ($timezone = Auth::user()->timezone) ? new \DateTimeZone($timezone) : null);

            /** @abac null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_CREATE_PAST_EVENT, Access to create past event */
            if (
                $startDateTimeWithTimezone < $nowDateTime &&
                !Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_CREATE_PAST_EVENT)
            ) {
                return '<script>(function() {setTimeout(function() {$("#modal-md").modal("hide")}, 800);createNotify("Error", "Event cannot be created in the past", "error")})();</script>';
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
        /** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_SOFT_DELETE, Access to soft delete event in calendar widget */
        $canDelete = Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_SOFT_DELETE);
        /** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_DELETE, Access to delete event in calendar widget */
        $canSoftDelete = Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_DELETE);
        /** @abac null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS, Hide Soft Deleted Schedule Events */
        $canHideSoftDeleted = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS);

        if (!$canDelete && !$canSoftDelete) {
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
            if (!$canDelete) {
                throw new ForbiddenHttpException('Access denied');
            }
            if (!$userShiftSchedule->delete()) {
                return $this->asJson([
                    'error' => true,
                    'message' => $userShiftSchedule->getErrorSummary(true)[0]
                ]);
            }
            Notifications::createAndPublish(
                $userShiftSchedule->uss_user_id,
                'Shift event was removed',
                'Shift event scheduled for: ' . Yii::$app->formatter->asByUserDateTime($userShiftSchedule->uss_start_utc_dt) . ' was removed from your shift',
                Notifications::TYPE_INFO,
                false
            );
            return $this->asJson([
                'error' => false,
                'message' => 'Event removed successfully',
            ]);
        }
        $userShiftSchedule->setStatusDelete(Auth::id());
        (new UserShiftScheduleRepository($userShiftSchedule))->save(true);

        Notifications::createAndPublish(
            $userShiftSchedule->uss_user_id,
            'Shift event was removed',
            'Shift event scheduled for: ' . Yii::$app->formatter->asByUserDateTime($userShiftSchedule->uss_start_utc_dt) . ' was removed from your shift',
            Notifications::TYPE_INFO,
            false
        );
        $userShiftScheduleData = [];

        if (!($userShiftSchedule->isDeletedStatus() && $canHideSoftDeleted)) {
            $userShiftScheduleData = UserShiftScheduleHelper::getDataForCalendar($userShiftSchedule);
        }

        return $this->asJson([
            'error' => false,
            'message' => 'Event deleted successfully',
            'timelineData' => json_encode($userShiftScheduleData)
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
                ->orWhere(['ssr_id' => (int)$q])
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
        /** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_SOFT_DELETE, Access to soft delete event in calendar widget */
        $canSoftDelete = Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_SOFT_DELETE);
        /** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_DELETE, Access to delete event in calendar widget */
        $canDelete = Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_DELETE);
        /** @abac null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS, Hide Soft Deleted Schedule Events */
        $canHideSoftDeleted = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS);

        if (!$canDelete && !$canSoftDelete) {
            throw new ForbiddenHttpException('Access denied');
        }

        $eventIds = Yii::$app->request->post('selectedEvents', []);
        $deletePermanently = Yii::$app->request->post('deletePermanently');
        $events = UserShiftSchedule::findAll(['uss_id' => $eventIds]);

        if ($deletePermanently == 1 && !$canDelete) {
            throw new ForbiddenHttpException('Access denied');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $eventsData = [];
            foreach ($events as $event) {
                if ($deletePermanently == 1) {
                    $event->delete();
                } else {
                    $event->setStatusDelete(Auth::id());
                    (new UserShiftScheduleRepository($event))->save(true);

                    if (!$canHideSoftDeleted) {
                        $eventsData[] = UserShiftScheduleHelper::getDataForCalendar($event);
                    }
                }

                Notifications::createAndPublish(
                    $event->uss_user_id,
                    'Shift event was removed',
                    'Shift event scheduled for: ' . Yii::$app->formatter->asByUserDateTime($event->uss_start_utc_dt) . ' was removed from your shift',
                    Notifications::TYPE_INFO,
                    false
                );
            }

            $transaction->commit();
            return $this->asJson([
                'error' => false,
                'message' => '',
                'timelineData' => json_encode($eventsData)
            ]);
        } catch (\RuntimeException $e) {
            $transaction->rollBack();
            return $this->asJson([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            Yii::error(AppHelper::throwableLog($throwable), 'ShiftScheduleController:actionAjaxMultipleDelete:Throwable');
            return $this->asJson([
                'error' => true,
                'message' => 'Internal Server Error',
            ]);
        }
    }

    public function actionAjaxMultipleUpdate()
    {
        /** @abac null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS, Hide Soft Deleted Schedule Events */
        $canHideSoftDeleted = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS);

        /** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_UPDATE, Access to update multiple events */
        if (!Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_UPDATE)) {
            throw new ForbiddenHttpException('Access denied');
        }

        $multipleUpdateForm = new UserShiftCalendarMultipleUpdateForm();

        if ($multipleUpdateForm->load(Yii::$app->request->post()) && $multipleUpdateForm->validate() && !$multipleUpdateForm->showForm) {
            $eventIds = \yii\helpers\Json::decode($multipleUpdateForm->eventIds);

            if (!is_array($eventIds)) {
                throw new BadRequestHttpException('Invalid JSON data for decode');
            }

            $transaction = new Transaction(['db' => Yii::$app->db]);
            try {
                $returnEventsData = [];
                $form = $multipleUpdateForm;
                if (empty($form->scheduleType) && empty($form->description) && empty($form->status) && empty($form->dateTimeRange)) {
                    throw new \RuntimeException('Please fill/change at least one field');
                }

                $transaction->begin();
                foreach ($eventIds as $eventId) {
                    $event = UserShiftSchedule::findOne((int)$eventId);
                    if (!$event) {
                        throw new BadRequestHttpException('Not found event');
                    }
                    $this->shiftScheduleService->editMultiple($multipleUpdateForm, $event, Auth::user()->timezone ?: null);
                    if (!$multipleUpdateForm->hasErrors() && !($event->isDeletedStatus() && $canHideSoftDeleted)) {
                        $returnEventsData[] = UserShiftScheduleHelper::getDataForCalendar($event);
                    }
                }
                $transaction->commit();

                $jsCode = '';
                foreach ($eventIds as $eventId) {
                    $jsCode .= 'window._timeline.removeEvent(' . $eventId . ');';
                }

                return '<script>(function() {$("#modal-md").modal("hide");' . $jsCode . ';let timelinesData = ' . json_encode($returnEventsData) . ';window._timeline.addEvents(timelinesData);window._timeline.multipleManageModule.resetSelectedEvents();createNotify("Success", "Event(s) updated successfully", "success")})();</script>';
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
            [$userId]
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
        /** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_VIEW_EVENT_LOG, Access to view event logs */
        if (!Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_VIEW_EVENT_LOG)) {
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
        /** @abac null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS, Hide Soft Deleted Schedule Events */
        $canHideSoftDeleted = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS);

        /** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_UPDATE, Access to update event */
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
                    $eventData = [];
                    if (!($event->isDeletedStatus() && $canHideSoftDeleted)) {
                        $eventData = UserShiftScheduleHelper::getDataForCalendar($event);
                    }
                    return '<script>(function() {$("#modal-md").modal("hide");window._timeline.removeEvent(' . $event->uss_id . ');let timelineData = ' . json_encode($eventData) . ';window._timeline.addEvent(timelineData);createNotify("Success", "Event updated successfully", "success")})();</script>';
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

    public function actionAjaxEditEvent()
    {
        /** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_UPDATE, Access to update event */
        if (!Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_UPDATE)) {
            throw new ForbiddenHttpException('Access Denied');
        }

        $data = Yii::$app->request->post();

        $form = new ShiftScheduleEditForm();
        $form->setScenario(ShiftScheduleEditForm::SCENARIO_EDIT_DRAG_N_DROP);

        $eventId = $data['eventId'] ?? 0;
        $event = UserShiftSchedule::findOne($eventId);
        if (!$event) {
            return $this->asJson([
                'error' => true,
                'message' => 'Event not found: by id: ' . $eventId
            ]);
        }

        $timezone = Auth::user()->timezone ?: null;

        $form->fillInByEvent($event, $timezone);
        if ($form->load($data) && $form->validate()) {
            $this->shiftScheduleService->edit($form, $event, $timezone);

            if (!$form->hasErrors()) {
                return $this->asJson([
                    'error' => false,
                    'message' => ''
                ]);
            }
        }

        return $this->asJson([
            'error' => true,
            'message' => $form->getErrorSummary(true)[0]
        ]);
    }

    public function actionSummaryReport()
    {
        if (UserShiftScheduleService::shiftSummaryReportIsEnable() === false) {
            throw new ForbiddenHttpException('Access denied');
        }

        $searchModel = new AgentShiftSummaryReportSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $totalCountData = $searchModel->countData($this->request->queryParams);
        $scheduleTypeList = ShiftScheduleType::find()
            ->orderBy(['sst_sort_order' => SORT_ASC])->all();

        return $this->render('summary', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'scheduleTypeList' => $scheduleTypeList,
            'totalCountData' => $totalCountData,
        ]);
    }
}
