<?php

namespace modules\shiftSchedule\controllers;

use DomainException;
use frontend\controllers\FController;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\search\ShiftScheduleRequestSearch;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use modules\shiftSchedule\src\forms\ScheduleRequestForm;
use modules\shiftSchedule\src\services\ShiftScheduleRequestService;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use Throwable;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class UserShiftScheduleRequestController extends FController
{
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    /** @abac ShiftAbacObject::ACT_MY_SHIFT_SCHEDULE, ShiftAbacObject::ACTION_ACCESS, Access to page shift-schedule/index */
                    [
                        'actions' => ['index', 'schedule-pending-requests', 'schedule-all-requests',
                            'my-data-ajax', 'get-event', 'get-history'],
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

    public function actionIndex(): string
    {
        $user = Yii::$app->user->identity;
        $userTimeZone = 'local'; //'UTC'; //'Europe/Chisinau'; //Auth::user()->userParams->up_timezone ?? 'local';

        return $this->render('index', array_merge(
            [
                'user' => $user,
                'userTimeZone' => $userTimeZone,
            ],
            $this->getSchedulePendingRequestsParams(),
            $this->getScheduleAllRequests()
        ));
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
        $dataProvider = $searchModel->searchByUsers(
            $queryParams,
            ShiftScheduleRequestService::getUserList(Auth::user()),
            date('Y-m-d', strtotime('now')),
            date('Y-m-d', strtotime('+1 year'))
        );

        return [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ];
    }

    public function actionSchedulePendingRequests(): string
    {
        return $this->renderAjax('partial/_pending_requests', $this->getSchedulePendingRequestsParams());
    }

    private function getScheduleAllRequests(): array
    {
        $searchModelAll = new ShiftScheduleRequestSearch();
        $dataProviderAll = $searchModelAll->searchByUsers(
            Yii::$app->request->queryParams,
            ShiftScheduleRequestService::getUserList(Auth::user())
        );

        return [
            'searchModelAll' => $searchModelAll,
            'dataProviderAll' => $dataProviderAll,
        ];
    }

    public function actionScheduleAllRequests(): string
    {
        return $this->renderAjax('partial/_all_requests', $this->getScheduleAllRequests());
    }

    /**
     * @return array
     */
    public function actionMyDataAjax(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $timelineList = ShiftScheduleRequestService::getTimelineListByUserList(
            ShiftScheduleRequestService::getUserList(Auth::user()),
            Yii::$app->request->get('start', date('Y-m-d'))
        );
        return ShiftScheduleRequestService::getCalendarTimelineJsonData($timelineList);
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

        $model = ShiftScheduleRequest::find()->where(['ssr_id' => $eventId])->limit(1)->one();

        if (!$model) {
            throw new NotFoundHttpException('Not exist this Shift Schedule (' . $eventId . ')');
        }

        $event = $model->srhUss;

        if (!$event) {
            throw new NotFoundHttpException('Not exist this Shift Schedule (' . $eventId . ')');
        }

        if (!in_array($event->uss_user_id, ShiftScheduleRequestService::getUserListArray())) {
            throw new NotAcceptableHttpException('Permission Denied (' . $eventId . ')');
        }

        $formModel = new ScheduleRequestForm([
            'scenario' => ScheduleRequestForm::SCENARIO_DECISION,
        ]);

        try {
            if (Yii::$app->request->isPost) {
                if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
                    $event->uss_status_id = $model->getCompatibleStatus($formModel->status);
                    if ($event->save()) {
                        $success = $formModel->saveDecision($model);
                    }
                }
            } else {
                $formModel->status = $model->ssr_status_id;
            }

            return $this->renderAjax('partial/_get_event', [
                'event' => $event,
                'model' => $formModel,
                'success' => $success ?? false,
                'canEditPreviousDate' => $model->getIsCanEditPreviousDate(),
            ]);
        } catch (DomainException $e) {
            Yii::error(AppHelper::throwableLog($e), 'UserShiftScheduleRequestController:actionGetEvent:DomainException');
        } catch (Throwable $e) {
            Yii::error(AppHelper::throwableLog($e), 'UserShiftScheduleRequestController:actionGetEvent:Throwable');
        }
        throw new BadRequestHttpException();
    }

    /**
     * @param int $id
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionGetHistory(int $id): string
    {
        $searchModel = new ShiftScheduleRequestSearch();
        $dataProvider = $searchModel->search([
            $searchModel->formName() => [
                'ssr_uss_id' => $id,
            ],
        ]);

        return $this->renderAjax('request-history', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
