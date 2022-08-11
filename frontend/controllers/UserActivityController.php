<?php

namespace frontend\controllers;

use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;
use modules\taskList\src\entities\userTask\UserTaskSearch;
use modules\user\src\events\UserEvents;
use modules\user\userActivity\entity\UserActivity;
use modules\user\userActivity\entity\search\UserActivitySearch;
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

        $startDateTime = date('Y-m-d H:i', strtotime('-24 hours'));
        $endDateTime = date('Y-m-d H:i', strtotime('+34 hours'));

        $scheduleEventList = UserShiftScheduleQuery::getExistEventList(
            $user->id,
            $startDateTime,
            $endDateTime,
            null,
            [ShiftScheduleType::SUBTYPE_WORK_TIME]
        );


        $userOnlineEvents = UserActivityService::getUniteEventsByUserId(
            $user->id,
            date('Y-m-d H:i:s', strtotime($startDateTime)),
            date('Y-m-d H:i:s'),
            UserEvents::EVENT_ONLINE,
            5,
            3
        );


        $beforeMin = 60;
        $afterMin = 60;
        $userOnlineData = [];

        if ($scheduleEventList) {
            foreach ($scheduleEventList as $item) {
                $startDateTimeUTC = $item['uss_start_utc_dt'];
                $endDateTimeUTC = $item['uss_end_utc_dt'];

                $lateArrivalStartDateTime = date('Y-m-d H:i:s', strtotime($startDateTimeUTC) - $beforeMin * 60);
                $overtimeEndDateTime = date('Y-m-d H:i:s', strtotime($endDateTimeUTC) + $afterMin * 60);

                $lateArrival = UserActivityService::getUniteEventsByUserId(
                    $user->id,
                    $lateArrivalStartDateTime,
                    $startDateTimeUTC,
                    UserEvents::EVENT_ONLINE,
                    5,
                    3
                );

                $overtime = UserActivityService::getUniteEventsByUserId(
                    $user->id,
                    $endDateTimeUTC,
                    $overtimeEndDateTime,
                    UserEvents::EVENT_ONLINE,
                    5,
                    3
                );

                $shift = UserActivityService::getUniteEventsByUserId(
                    $user->id,
                    $startDateTimeUTC,
                    $endDateTimeUTC,
                    UserEvents::EVENT_ONLINE,
                    5,
                    3
                );

                $earlyLeave = [];
                if ($shift && !empty($shift[0])) {
                    $firstEvent = $shift[0];
                    $earlyLeave = [
                      [
                          'start' => $startDateTimeUTC,
                          'end' => $firstEvent['start'],
                          'duration' => (int) (strtotime($firstEvent['start']) - strtotime($startDateTimeUTC)) / 60
                      ]
                    ];
                }

                $earlyArrival = [];
                if ($shift) {
                    $lastEvent = end($shift);
                    $earlyArrival = [
                        [
                            'start' => $lastEvent['end'],
                            'end' => $endDateTimeUTC,
                            'duration' => (int) (strtotime($endDateTimeUTC) - strtotime($lastEvent['end'])) / 60
                        ]
                    ];
                }


                $userOnlineData[$item['uss_id']] = [
                    'lateArrival' => $lateArrival,
                    'overtime' => $overtime,
                    'shift' => $shift,
                    'earlyLeave' => $earlyLeave,
                    'earlyArrival' => $earlyArrival,
                ];
            }
        }

       // VarDumper::dump($userOnlineData, 10, true);


        $userActiveEvents = UserActivityService::getUniteEventsByUserId(
            $user->id,
            date('Y-m-d H:i:s', strtotime($startDateTime)),
            date('Y-m-d H:i:s'),
            UserEvents::EVENT_ACTIVE,
            3,
            3
        );





        return $this->render(
            '/user/user-activity/dashboard',
            [
                // 'userTimeZone' => $userTimeZone,
                'user' => $user,
                'startDateTime' => $startDateTime,
                'endDateTime' => $endDateTime,
                'scheduleEventList' => $scheduleEventList,
                'userActiveEvents' => $userActiveEvents,
                'userOnlineEvents' => $userOnlineEvents,
                'userOnlineData' => $userOnlineData,
            ]
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
