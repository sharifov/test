<?php

namespace modules\shiftSchedule\controllers;

use DomainException;
use frontend\controllers\FController;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\search\ShiftScheduleRequestSearch;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use modules\shiftSchedule\src\entities\shiftScheduleRequestHistory\search\ShiftScheduleRequestHistorySearch;
use modules\shiftSchedule\src\entities\shiftScheduleRequestHistory\ShiftScheduleRequestHistory;
use modules\shiftSchedule\src\forms\ScheduleDecisionForm;
use modules\shiftSchedule\src\services\ShiftScheduleRequestService;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
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
                            'my-data-ajax', 'get-history'],
                        'allow' => \Yii::$app->abac->can(
                            null,
                            ShiftAbacObject::ACT_USER_SHIFT_SCHEDULE,
                            ShiftAbacObject::ACTION_ACCESS
                        ),
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['get-event'],
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

    public function actionIndex(): string
    {
        $user = Yii::$app->user->identity;
        $userTimeZone = $user->timezone ?: 'UTC'; //'UTC'; //'Europe/Chisinau'; //Auth::user()->userParams->up_timezone ?? 'local';

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
        $jsonData = [];
        Yii::$app->response->format = Response::FORMAT_JSON;
        $startDate = Yii::$app->request->get('start');
        $endDate = Yii::$app->request->get('end');

        if (!empty($startDate) && !empty($endDate)) {
            $timelineList = ShiftScheduleRequestService::getTimelineListByUserList(
                ShiftScheduleRequestService::getUserList(Auth::user()),
                $startDate,
                $endDate
            );
            $userTimeZone = Auth::user()->timezone ?: 'UTC';
            $jsonData = ShiftScheduleRequestService::getCalendarTimelineJsonData($timelineList, $userTimeZone);
        }

        return $jsonData;
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
            if (Yii::$app->request->isPost) {
                if ($decisionFormModel->load(Yii::$app->request->post()) && $decisionFormModel->validate()) {
                    $event->uss_status_id = $requestModel->getCompatibleStatus($decisionFormModel->status);
                    $event->uss_description = $decisionFormModel->description;
                    if ($event->save()) {
                        $success = ShiftScheduleRequestService::saveDecision($requestModel, $decisionFormModel, Auth::user());
                    }
                }
            } else {
                $decisionFormModel->status = $requestModel->ssr_status_id;
            }
            $userTimeZone = Auth::user()->timezone ?: 'UTC';
            return $this->renderAjax('partial/_get_event', [
                'event' => $event,
                'model' => $decisionFormModel,
                'success' => $success ?? false,
                'canEditPreviousDate' => $requestModel->getIsCanEditPreviousDate(),
                'userTimeZone' => $userTimeZone,
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
}
