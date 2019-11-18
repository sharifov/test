<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\Lead;
use common\models\search\LeadQcallSearch;
use sales\access\ListsAccess;
use sales\guards\lead\TakeGuard;
use sales\services\lead\LeadRedialService;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class LeadRedialController
 *
 * @property LeadRedialService $leadRedialService
 * @property TakeGuard $takeGuard
 */
class LeadRedialController extends FController
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['guardAutoRedial'] = [
            'class' => \yii\filters\AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'matchCallback' => static function ($rule, $action) {
                        /** @var Employee $user */
                        return ($user = Yii::$app->user->identity) && ($profile = $user->userProfile) && $profile->up_auto_redial;
                    }
                ]
            ]
        ];
        return $behaviors;
    }

    private $leadRedialService;
    private $takeGuard;

    public function __construct(
        $id,
        $module,
        LeadRedialService $leadRedialService,
        TakeGuard $takeGuard,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->leadRedialService = $leadRedialService;
        $this->takeGuard = $takeGuard;
    }

    public function actionIndex(): string
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;

		$params = Yii::$app->request->queryParams;
		$params['is_test'] = Yii::$app->request->get('is_test', 0);
        $searchModel = new LeadQcallSearch();
        $dataProvider = $searchModel->searchByRedial($params, $user);
        $dataProviderLastCalls = $searchModel->searchLastCalls([], $user);

        $guard = [];

        $flowDescriptions = LeadRedialService::getFlowDescriptions();

        if ((bool)\Yii::$app->params['settings']['enable_take_frequency_minutes']) {
            try {
                $this->takeGuard->frequencyMinutesGuard($user, $flowDescriptions);
            } catch (\DomainException $e) {
                $guard[] = $e->getMessage();
            }
        }

        if ((bool)\Yii::$app->params['settings']['enable_min_percent_take_leads']) {
            try {
                $this->takeGuard->minPercentGuard($user, $flowDescriptions);
            } catch (\DomainException $e) {
                $guard[] = $e->getMessage();
            }
        }

        if ((bool)\Yii::$app->params['settings']['enable_redial_shift_time_limits']) {
            try {
                $this->takeGuard->shiftTimeGuard($user);
            } catch (\DomainException $e) {
                $guard[] = $e->getMessage();
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataProviderLastCalls' => $dataProviderLastCalls,
            'guard' => $guard,
        ]);
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionShow(): Response
    {
        $gid = Yii::$app->request->post('gid');
        $lead = $this->findLeadById($gid);
        return $this->asJson([
            'success' => true,
            'data' => $this->renderAjax('show', ['lead' => $lead])
        ]);
    }

    /**
     * @return string
     */
    public function actionShowLastCalls(): string
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        return $this->renderPartial('_last_calls', [
            'dataProvider' => (new LeadQcallSearch())->searchLastCalls([], $user),
            'list' => new ListsAccess($user->id),
            'userIsFreeForCall' => $user->isCallFree(),
            'user' => $user
        ]);
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionRedial(): string
    {
        $gid = Yii::$app->request->post('gid');
        $lead = $this->findLeadById($gid);
        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        try {
            $this->leadRedialService->redial($lead, $user);
        } catch (\DomainException $e) {
            return $this->renderAjax('error', ['message' => $e->getMessage()]);
        }

        return $this->renderAjax('redial_from_queue', ['lead' => $lead]);
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionReservation(): Response
    {
        $gid = Yii::$app->request->post('gid');
        $lead = $this->findLeadById($gid);
        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        try {
            $this->leadRedialService->reservation($lead, $user);
            return $this->asJson(['success' => true]);
        } catch (\DomainException $e) {
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionRedialFromLastCalls(): string
    {
        $gid = Yii::$app->request->post('gid');
        $lead = $this->findLeadById($gid);
        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        try {
            $this->leadRedialService->redialFromLastCalls($lead, $user);
        } catch (\DomainException $e) {
            return $this->renderAjax('error', ['message' => $e->getMessage()]);
        }

        return $this->renderAjax('redial_from_last_calls', ['lead' => $lead]);
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionReservationFromLastCall(): Response
    {
        $gid = Yii::$app->request->post('gid');
        $lead = $this->findLeadById($gid);
        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        try {
            $this->leadRedialService->reservationFromLastCalls($lead, $user);
            return $this->asJson(['success' => true]);
        } catch (\DomainException $e) {
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionTake(): Response
    {
        $gid = Yii::$app->request->post('gid');
        $lead = $this->findLeadById($gid);
        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        try {
            $this->leadRedialService->take($lead, $user, Yii::$app->user->id);
            return $this->asJson(['success' => true]);
        } catch (\DomainException $e) {
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @param $gid
     * @return Lead
     * @throws NotFoundHttpException
     */
    protected function findLeadById($gid): Lead
    {
        if ($model = Lead::findOne(['gid' => $gid])) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
