<?php

namespace modules\shiftSchedule\controllers;

use DomainException;
use frontend\controllers\FController;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\search\ShiftScheduleRequestSearch;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use modules\shiftSchedule\src\forms\ScheduleRequestForm;
use modules\shiftSchedule\src\services\ShiftScheduleRequestService;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use Throwable;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class UserShiftScheduleRequestController extends FController
{
    public function actionIndex(): string
    {
        $user = Yii::$app->user->identity;
        $userTimeZone = Yii::t('shedule-request', 'local');
        $searchModel = new ShiftScheduleRequestSearch();

        $startDate = Yii::$app->request->get('startDate');
        $endDate = Yii::$app->request->get('endDate');

        $dataProvider = $searchModel->searchByUsers(
            Yii::$app->request->queryParams,
            ShiftScheduleRequestService::getUserList(Auth::user()),
            $startDate,
            $endDate
        );

        return $this->render('index', [
            'user' => $user,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'userTimeZone' => $userTimeZone,
        ]);
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
        $eventId = (int) Yii::$app->request->get('id');

        if (!$eventId) {
            throw new BadRequestHttpException('Invalid request param');
        }

        $model = ShiftScheduleRequest::find()->where(['srh_id' => $eventId])->limit(1)->one();

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
                $formModel->status = $model->srh_status_id;
            }

            return $this->renderAjax('partial/_get_event', [
                'event' => $event,
                'model' => $formModel,
                'success' => $success ?? false,
            ]);
        } catch (DomainException $e) {
            Yii::error(AppHelper::throwableLog($e), 'UserShiftScheduleRequestController:actionGetEvent:DomainException');
        } catch (Throwable $e) {
            Yii::error(AppHelper::throwableLog($e), 'UserShiftScheduleRequestController:actionGetEvent:Throwable');
        }
        throw new BadRequestHttpException();
    }
}
