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
use modules\shiftSchedule\src\forms\ScheduleRequestForm;
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
                            'generate-user-schedule', 'legend-ajax', 'calendar', 'calendar-events-ajax', 'add-event', 'add-single-event',
                            'schedule-request-ajax'],
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
     * @return string
     */
    public function actionLegendAjax(): string
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
                    'img' => '',
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
            return '<script>(function() {$("#modal-md").modal("hide");let timelineData = ' . json_encode($data) . ';addTimelineEvent(timelineData);})();</script>';
        }

        if (!Yii::$app->request->isPost) {
            $userIdCreateFor = Yii::$app->request->get('userId', null);
            $startDate = Yii::$app->request->get('startDate', null);
            if (!$user = Employee::findOne(['id' => $userIdCreateFor])) {
                throw new yii\web\MethodNotAllowedHttpException('User not found by id: ' . $userIdCreateFor, 404);
            }

            $form->userId = $userIdCreateFor;
            $form->startDateTime = (new \DateTimeImmutable($startDate))->format('Y-m-d H:i');
            $form->duration = UserShiftSchedule::DEFAULT_DURATION;
            $form->status = UserShiftSchedule::STATUS_APPROVED;
        }

        return $this->renderAjax('partial/_shift_schedule_create_form_single_event', [
            'singleEventForm' => $form
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionScheduleRequestAjax()
    {
        $request = Yii::$app->request;
        $scheduleRequestModel = new ScheduleRequestForm();
        if ($request->isPost) {
            if ($scheduleRequestModel->load($request->post()) && $scheduleRequestModel->validate()) {
                if ($scheduleRequestModel->saveRequest()) {
                    return $this->redirect(['shift-schedule/index']);
                }
            }
        } else {
            if (!empty($start = $request->get('start'))) {
                $scheduleRequestModel->startDt = date('Y-m-d', strtotime($start));
                $scheduleRequestModel->validate(['startDt']);
            }
            if (!empty($end = $request->get('end'))) {
                $scheduleRequestModel->endDt = date('Y-m-d', strtotime($end));
                $scheduleRequestModel->validate(['endDt']);
            }
        }

        return $this->renderAjax('partial/_schedule_request', [
            'scheduleRequestModel' => $scheduleRequestModel,
        ]);
    }
}
