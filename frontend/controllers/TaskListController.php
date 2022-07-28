<?php

namespace frontend\controllers;

use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;
use modules\taskList\abac\TaskListAbacObject;
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
    /**
     * @return void
     */
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    /** @abac TaskListAbacObject::ACT_MY_TASK_LIST, TaskListAbacObject::ACTION_ACCESS, Access to page task-list/index */
                    [
                        'actions' => ['index', 'my-data-ajax'],
                        'allow' => \Yii::$app->abac->can(
                            null,
                            TaskListAbacObject::ACT_MY_TASK_LIST,
                            TaskListAbacObject::ACTION_ACCESS
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
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = $userId ?: Auth::id();

        $startDt = Yii::$app->request->get('start', date('Y-m-d'));
        $endDt = Yii::$app->request->get('end', date('Y-m-d'));

        $timelineList = UserShiftScheduleQuery::getTimelineListByUserExcludeDeletedEvents($userId, $startDt, $endDt);
        $taskList = UserTaskQuery::getTaskListByUser($userId, $startDt, $endDt);
        $userTimeZone = Auth::user()->timezone ?: 'UTC';

        $timeLineData = UserShiftScheduleQuery::getCalendarTimelineJsonData($timelineList, $userTimeZone);
        $taskListData = UserTaskQuery::getCalendarTaskJsonData($taskList, $userTimeZone);

        $data = array_merge($timeLineData, $taskListData);
        unset($timeLineData, $taskListData);
        return $data;
    }
}
