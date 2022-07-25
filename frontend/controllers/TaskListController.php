<?php

namespace frontend\controllers;

use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\shiftSchedule\src\entities\userShiftSchedule\search\SearchUserShiftSchedule;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;
use modules\shiftSchedule\src\services\UserShiftScheduleService;
use modules\taskList\src\entities\taskList\search\TaskListSearch;
use modules\taskList\src\entities\userTask\UserTaskQuery;
use modules\taskList\src\entities\userTask\UserTaskSearch;
use src\auth\Auth;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Response;

class TaskListController extends FController
{
    /*private UserShiftScheduleService $shiftScheduleService;

    public function __construct($id, $module, UserShiftScheduleService $shiftScheduleService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->shiftScheduleService = $shiftScheduleService;
    }*/

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
                        'actions' => ['index', 'my-data-ajax'],
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
        $userTimeZone = $user->timezone ?: 'UTC';
        $searchModel = new UserTaskSearch();
        $startDate = Yii::$app->request->get('startDate', date('Y-m-d'));
        $endDate = Yii::$app->request->get('endDate', date('Y-m-d', strtotime('+1 day')));

        /** @abac null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS, Hide Soft Deleted Schedule Events */
//        if (
//            \Yii::$app->abac->can(
//                null,
//                ShiftAbacObject::OBJ_USER_SHIFT_EVENT,
//                ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS
//            )
//        ) {
//            $searchModel->enableExcludeDeleteStatus();
//        }

        $dataProvider = $searchModel->searchByUserId(Yii::$app->request->queryParams, $user->id, $startDate, $endDate);

        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'userTimeZone' => $userTimeZone,
                'user' => $user,
            ]
        );
    }


    /**
     * @param int|null $userId
     * @return array
     */
    public function actionMyDataAjax(?int $userId = null): array
    {
//        /** @abac null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS, Hide Soft Deleted Schedule Events */
//        $canHideSoftDeleted = \Yii::$app->abac->can(
//            null,
//            ShiftAbacObject::OBJ_USER_SHIFT_EVENT,
//            ShiftAbacObject::ACTION_HIDE_SOFT_DELETED_EVENTS
//        );

        \Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = $userId ?: Auth::id();

        $startDt = Yii::$app->request->get('start', date('Y-m-d'));
        $endDt = Yii::$app->request->get('end', date('Y-m-d'));

        $timelineList = UserShiftScheduleQuery::getTimelineListByUserExcludeDeletedEvents($userId, $startDt, $endDt);
        $taskList = UserTaskQuery::getTaskListByUser($userId, $startDt, $endDt);

        //$canHideSoftDeleted ?            UserShiftScheduleQuery::getTimelineListByUserExcludeDeletedEvents($userId, $startDt, $endDt) :

        $userTimeZone = Auth::user()->timezone ?: 'UTC';

        $timeLineData = UserShiftScheduleQuery::getCalendarTimelineJsonData($timelineList, $userTimeZone);
        $taskListData = UserTaskQuery::getCalendarTaskJsonData($taskList, $userTimeZone);

        $data = array_merge($timeLineData, $taskListData);
        unset($timeLineData, $taskListData);
        return $data;
    }
}
