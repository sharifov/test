<?php

namespace frontend\controllers;

use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;
use modules\taskList\src\entities\userTask\UserTaskSearch;
use modules\user\src\events\UserEvents;
use modules\user\userActivity\entity\UserActivity;
use modules\user\userActivity\entity\search\UserActivitySearch;
use modules\user\userActivity\forms\DashboardSearchForm;
use modules\user\userActivity\service\UserActivityService;
use src\auth\Auth;
use Yii;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * UserActivityController implements the CRUD actions for UserActivity model.
 */
class UserActivityController extends FController
{
    /**
     * @return void
     */
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

//    /**
//     * @return bool|string|null
//     */
//    public function getViewPath(): bool|string|null
//    {
//        return Yii::getAlias('@frontend/views/user/user-activity');
//    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST']
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }


    /**
     * Lists all UserActivity models.
     *
     * @return string
     */
    public function actionDashboard(): string
    {
        $user = Auth::user();
//        $userTimeZone = $user->timezone ?: 'UTC';

        $searchModel = new UserActivitySearch();

//
//        $startDateTime = date('Y-m-d H:i', strtotime('-15 days'));
//        $endDateTime = date('Y-m-d H:i', strtotime('+34 hours'));

        $data = $searchModel->searchUserActivity(Yii::$app->request->queryParams, $user);

        $data['user'] = $user;
        $data['searchModel'] = $searchModel;


        $metricData = [
            'EarlyStart' => [],
            'EarlyFinish' => [],
            'LateStart' => [],
            'LateFinish' => [],
            'UsefulTime' => [],
            'online' => [],
            'activity' => [],
        ];

        $data['metricData'] = $metricData;

        return $this->render(
            '/user/user-activity/dashboard',
            $data
            /*[
            // 'userTimeZone' => $userTimeZone,
            'user' => $user,
            'startDateTime' => $startDateTime,
            'endDateTime' => $endDateTime,
            'scheduleEventList' => $scheduleEventList,
            'userActiveEvents' => $userActiveEvents,
            'userOnlineEvents' => $userOnlineEvents,
            'userOnlineData' => $userOnlineData,
            'summary' => $summary,
            'searchModel' => $searchModel
            ]*/
        );
    }

    /**
     * Lists all UserActivity models.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new UserActivitySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $eventList = Yii::$app->event->getObjectEventList();

        return $this->render('/user/user-activity/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'eventList' => $eventList,
        ]);
    }

    /**
     * Displays a single UserActivity model.
     * @param string $ua_start_dt Start DateTime
     * @param int $ua_user_id User ID
     * @param string $ua_object_event Object Event
     * @param int $ua_object_id Object ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(string $ua_start_dt, $ua_user_id, $ua_object_event, $ua_object_id): string
    {
        return $this->render('/user/user-activity/view', [
            'model' => $this->findModel($ua_start_dt, $ua_user_id, $ua_object_event, $ua_object_id),
        ]);
    }

    /**
     * Creates a new UserActivity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate(): Response|string
    {
        $model = new UserActivity();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'ua_start_dt' => $model->ua_start_dt,
                    'ua_user_id' => $model->ua_user_id,
                    'ua_object_event' => $model->ua_object_event, 'ua_object_id' => $model->ua_object_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('/user/user-activity/create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserActivity model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $ua_start_dt Start DateTime
     * @param int $ua_user_id User ID
     * @param string $ua_object_event Object Event
     * @param int $ua_object_id Object ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(string $ua_start_dt, $ua_user_id, $ua_object_event, $ua_object_id): Response|string
    {
        $model = $this->findModel($ua_start_dt, $ua_user_id, $ua_object_event, $ua_object_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ua_start_dt' => $model->ua_start_dt,
                'ua_user_id' => $model->ua_user_id,
                'ua_object_event' => $model->ua_object_event, 'ua_object_id' => $model->ua_object_id]);
        }

        return $this->render('/user/user-activity/update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserActivity model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $ua_start_dt Start DateTime
     * @param int $ua_user_id User ID
     * @param string $ua_object_event Object Event
     * @param int $ua_object_id Object ID
     * @return Response
     * @throws NotFoundHttpException|StaleObjectException if the model cannot be found
     */
    public function actionDelete($ua_start_dt, $ua_user_id, $ua_object_event, $ua_object_id): Response
    {
        $this->findModel($ua_start_dt, $ua_user_id, $ua_object_event, $ua_object_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserActivity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $ua_start_dt Start DateTime
     * @param int $ua_user_id User ID
     * @param string $ua_object_event Object Event
     * @param int $ua_object_id Object ID
     * @return UserActivity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(string $ua_start_dt, $ua_user_id, $ua_object_event, $ua_object_id): UserActivity
    {
        if (
            ($model = UserActivity::findOne(['ua_start_dt' => $ua_start_dt,
                'ua_user_id' => $ua_user_id, 'ua_object_event' => $ua_object_event,
                'ua_object_id' => $ua_object_id])) !== null
        ) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
