<?php

namespace frontend\controllers;

use frontend\widgets\userTasksList\helpers\UserTasksListHelper;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;
use modules\shiftSchedule\src\services\UserShiftScheduleService;
use modules\taskList\abac\dto\TaskListAbacDto;
use modules\taskList\abac\TaskListAbacObject;
use modules\taskList\src\entities\userTask\repository\UserTaskRepository;
use modules\taskList\src\entities\userTask\UserTask;
use modules\taskList\src\entities\userTask\UserTaskQuery;
use modules\taskList\src\entities\userTask\UserTaskSearch;
use modules\user\src\events\UserEvents;
use modules\user\userActivity\service\UserActivityService;
use modules\taskList\src\forms\UserTaskNoteForm;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use Yii;
use yii\caching\TagDependency;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
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

                    [
                        'actions' => ['ajax-add-note', 'ajax-user-task-details', 'ajax-delete-note'],
                        'allow' => true,
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

        $startDate = new \DateTime($startDate);
        $endDate = (new \DateTime($endDate))->setTime(23, 59, 59);

        $dataProvider = $searchModel->searchByUserId(Yii::$app->request->queryParams, $user->id, $startDate, $endDate);

        $startDateTime = date('Y-m-d H:i', strtotime('-24 hours'));
        $endDateTime = date('Y-m-d H:i', strtotime('+34 hours'));

        $scheduleEventList = UserShiftScheduleQuery::getExistEventList(
            $user->id,
            $startDateTime,
            $endDateTime,
            null,
            [ShiftScheduleType::SUBTYPE_WORK_TIME]
        );


        $userActiveEvents = UserActivityService::getUniteEventsByUserId(
            $user->id,
            date('Y-m-d H:i:s', strtotime($startDateTime)),
            date('Y-m-d H:i:s'),
            UserEvents::EVENT_ACTIVE,
            3,
            3
        );

        $userOnlineEvents = UserActivityService::getUniteEventsByUserId(
            $user->id,
            date('Y-m-d H:i:s', strtotime($startDateTime)),
            date('Y-m-d H:i:s'),
            UserEvents::EVENT_ONLINE,
            5,
            3
        );



        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'userTimeZone' => $userTimeZone,
                'user' => $user,
                'startDateTime' => $startDateTime,
                'endDateTime' => $endDateTime,
                'scheduleEventList' => $scheduleEventList,
                'userActiveEvents' => $userActiveEvents,
                'userOnlineEvents' => $userOnlineEvents,
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
        $taskList = UserTaskQuery::getTaskListByUser($userId, $startDt, $endDt, [UserTask::STATUS_PROCESSING]);
        $userTimeZone = Auth::user()->timezone ?: 'UTC';

        $timeLineData = UserShiftScheduleQuery::getCalendarTimelineJsonData($timelineList, $userTimeZone);
        $taskListData = UserTaskQuery::getCalendarTaskJsonData($taskList, $userTimeZone);

        $data = array_merge($timeLineData, $taskListData);
        unset($timeLineData, $taskListData, $userEventData);
        return $data;
    }


    public function actionAjaxAddNote(int $userTaskId)
    {
        $form = new UserTaskNoteForm($userTaskId);

        $dto = new TaskListAbacDto();
        $userTask = $form->getUserTask();
        $dto->setIsUserTaskOwner($userTask && $userTask->isOwner(Auth::id()));
        /** @abac TaskListAbacObject::OBJ_USER_TASK, TaskListAbacObject::ACTION_ADD_NOTE, Access to add UserTask Note */
        if (!Yii::$app->abac->can($dto, TaskListAbacObject::OBJ_USER_TASK, TaskListAbacObject::ACTION_ADD_NOTE)) {
            throw new ForbiddenHttpException('Permission Denied (' . $userTaskId . ')');
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $userTask = $form->getUserTask();
            $userTask->ut_description = $form->note;
            (new UserTaskRepository($userTask))->save(true);
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            TagDependency::invalidate(\Yii::$app->cache, UserTasksListHelper::getUserTasksListCacheTag((int)$userTask->ut_target_object_id, Auth::id()));
            return ['note' => $form->note, 'truncateNote' => StringHelper::truncate($form->note, 15), 'userTaskId' => $form->userTaskId];
        }

        return $this->renderAjax('partial/_add_note', [
            'addNoteForm' => $form,
            'abacDto' => $dto
        ]);
    }

    public function actionAjaxDeleteNote(int $userTaskId)
    {
        $abacDto = new TaskListAbacDto();
        $userTask = UserTask::findOne($userTaskId);
        $abacDto->setIsUserTaskOwner(!empty($userTask) && $userTask->isOwner(Auth::id()));
        $result = [
            'isSuccess' => false,
            'userTaskId' => $userTaskId,
        ];

        /** @abac TaskListAbacObject::OBJ_USER_TASK, TaskListAbacObject::ACTION_ADD_NOTE, Access to delete UserTask Note */
        if (!Yii::$app->abac->can($abacDto, TaskListAbacObject::OBJ_USER_TASK, TaskListAbacObject::ACTION_REMOVE_NOTE)) {
            throw new ForbiddenHttpException('Permission denied to delete UserTask note (' . $userTaskId . ')');
        }

        try {
            $userTask->ut_description = '';
            (new UserTaskRepository($userTask))->save();
            $result['isSuccess'] = true;

            TagDependency::invalidate(\Yii::$app->cache, UserTasksListHelper::getUserTasksListCacheTag($userTask->ut_target_object_id, Auth::id()));
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableLog($e), 'TaskListController:actionAjaxDeleteNote:Throwable');
            $result['message'] = 'Something went wrong';
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $result;
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     */
    public function actionAjaxUserTaskDetails(): string
    {
        $userTaskId = (int)Yii::$app->request->get('id');

        if (!$userTaskId) {
            throw new BadRequestHttpException('Invalid request param');
        }

        $userTask = UserTask::find()->where(['ut_id' => $userTaskId])->limit(1)->one();

        if (!$userTask) {
            throw new NotFoundHttpException('Not exist this UserTask (' . $userTaskId . ')');
        }

        $userTimeZone = Auth::user()->timezone ?: 'UTC';

        $dto = new TaskListAbacDto();
        $dto->setIsUserTaskOwner($userTask->isOwner(Auth::id()));

        /** @abac TaskListAbacObject::OBJ_USER_TASK, TaskListAbacObject::ACTION_READ, Access to view UserTask details */
        if (!Yii::$app->abac->can($dto, TaskListAbacObject::OBJ_USER_TASK, TaskListAbacObject::ACTION_READ)) {
            throw new NotAcceptableHttpException('Permission Denied (' . $userTaskId . ')');
        }

        return $this->renderAjax('partial/_get_user_task', [
            'userTask' => $userTask,
            'userTimeZone' => $userTimeZone,
            'user' => Auth::user(),
        ]);
    }
}
