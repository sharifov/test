<?php

namespace modules\shiftSchedule\controllers;

use frontend\controllers\FController;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\search\ShiftScheduleRequestSearch;
use modules\shiftSchedule\src\entities\shiftScheduleRequestHistory\search\ShiftScheduleRequestHistorySearch;
use modules\shiftSchedule\src\forms\ScheduleDecisionForm;
use modules\shiftSchedule\src\services\ShiftScheduleRequestService;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use DomainException;
use Throwable;

/**
 * ShiftScheduleRequestController implements the CRUD actions for ShiftScheduleRequest model.
 */
class ShiftScheduleRequestController extends FController
{
    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    /** @abac ShiftAbacObject::ACT_MY_SHIFT_SCHEDULE, ShiftAbacObject::ACTION_ACCESS, Access to page shift-schedule/index */
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'get-event', 'get-history'],
                        'allow' => \Yii::$app->abac->can(
                            null,
                            ShiftAbacObject::ACT_USER_SHIFT_SCHEDULE,
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
     * Lists all ShiftScheduleRequest models.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new ShiftScheduleRequestSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ShiftScheduleRequest model.
     * @param int $ssr_id Srh ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $ssr_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($ssr_id),
        ]);
    }

    /**
     * Creates a new ShiftScheduleRequest model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ShiftScheduleRequest();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'ssr_id' => $model->ssr_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ShiftScheduleRequest model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $ssr_id Srh ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $ssr_id)
    {
        $model = $this->findModel($ssr_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ssr_id' => $model->ssr_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ShiftScheduleRequest model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $ssr_id Srh ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(int $ssr_id): Response
    {
        $this->findModel($ssr_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ShiftScheduleRequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $ssr_id Srh ID
     * @return ShiftScheduleRequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $ssr_id): ShiftScheduleRequest
    {
        if (($model = ShiftScheduleRequest::findOne(['ssr_id' => $ssr_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * @param int $id
     * @return string
     * @throws InvalidConfigException
     */
    public function actionGetHistory(int $id): string
    {
        $searchModel = new ShiftScheduleRequestHistorySearch();
        $dataProvider = $searchModel->search(ArrayHelper::merge(
            Yii::$app->request->queryParams,
            [
                $searchModel->formName() => [
                    'ssrh_ssr_id' => $id,
                ],
            ]
        ));

        return $this->renderAjax('request-history', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     */
    public function actionGetEvent(): string
    {
        $eventId = (int)Yii::$app->request->get('id');

        if (!$eventId) {
            throw new BadRequestHttpException('Invalid request param');
        }

        $requestModel = ShiftScheduleRequest::find()->where(['ssr_id' => $eventId])->limit(1)->one();

        if (!$requestModel) {
            throw new NotFoundHttpException('Not exist this Shift Schedule (' . $eventId . ')');
        }

        $event = $requestModel->srhUss;

        if (!$event) {
            throw new NotFoundHttpException('Not exist this Shift Schedule (' . $eventId . ')');
        }

        if (!in_array($event->uss_user_id, ShiftScheduleRequestService::getUserListArray())) {
            throw new NotAcceptableHttpException('Permission Denied (' . $eventId . ')');
        }

        $decisionFormModel = new ScheduleDecisionForm();

        try {
            $decisionFormModel->status = $requestModel->ssr_status_id;
            $userTimeZone = Auth::user()->timezone ?: 'UTC';
            return $this->renderAjax('partial/_get_event', [
                'event' => $event,
                'model' => $decisionFormModel,
                'success' => $success ?? false,
                'userTimeZone' => $userTimeZone,
            ]);
        } catch (DomainException $e) {
            Yii::error(AppHelper::throwableLog($e), 'UserShiftScheduleRequestController:actionGetEvent:DomainException');
        } catch (Throwable $e) {
            Yii::error(AppHelper::throwableLog($e), 'UserShiftScheduleRequestController:actionGetEvent:Throwable');
        }
        throw new BadRequestHttpException();
    }
}
