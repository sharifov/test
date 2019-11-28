<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\Lead;
use common\models\LeadQcall;
use common\models\search\LeadQcallSearch;
use frontend\widgets\multipleUpdate\redial\MultipleUpdateForm;
use frontend\widgets\multipleUpdate\redialAll\UpdateAllForm;
use frontend\widgets\multipleUpdate\redialAll\UpdateAllService;
use sales\access\ListsAccess;
use sales\guards\lead\TakeGuard;
use sales\services\lead\LeadRedialService;
use frontend\widgets\multipleUpdate\redial\MultipleUpdateService;
use Yii;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Class LeadRedialController
 *
 * @property LeadRedialService $leadRedialService
 * @property TakeGuard $takeGuard
 * @property MultipleUpdateService $multipleUpdate
 * @property UpdateAllService $updateAllService
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
    private $multipleUpdate;
    private $updateAllService;

    public function __construct(
        $id,
        $module,
        LeadRedialService $leadRedialService,
        TakeGuard $takeGuard,
        MultipleUpdateService $multipleUpdate,
        UpdateAllService $updateAllService,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->leadRedialService = $leadRedialService;
        $this->multipleUpdate = $multipleUpdate;
        $this->takeGuard = $takeGuard;
        $this->updateAllService = $updateAllService;
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
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionMultipleUpdate(): Response
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        if (!$user->isAdmin()) {
            throw new ForbiddenHttpException('Access is denied.');
        }

        $form = new MultipleUpdateForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $report = $this->multipleUpdate->update($form);
            return $this->asJson(['success' => true, 'report' => $report]);
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionMultipleUpdateValidate(): array
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        if (!$user->isAdmin()) {
            throw new ForbiddenHttpException('Access is denied.');
        }

        $form = new MultipleUpdateForm();
        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionShow(): Response
    {
        $gid = Yii::$app->request->post('gid');
        $lead = $this->findLeadByGid($gid);
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
     * @param int $userId
     * @return array
     */
    private function guardAlreadyReservedOneLead(int $userId): array
    {
        if ($currentLead = LeadQcall::find()->currentReservedLeadByUser($userId)->one()) {
            if ($lead = $currentLead->lqcLead) {
                return [
                    'success' => false,
                    'message' => 'You already reserved one Lead. Try again later.',
                    'data' => $this->renderAjax('redial_from_queue', ['lead' => $lead])
                ];
            }
            return [
                'success' => false,
                'message' => 'You already reserved one Lead, but Lead not found. Try again later.',
            ];
        }
        return [];
    }

    public function actionNext(): Response
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        if ($data = $this->guardAlreadyReservedOneLead($user->id)) {
            return $this->asJson($data);
        }

        $dataProvider = (new LeadQcallSearch())->searchByRedial([], $user);
        $query = $dataProvider->query;
        $query->addOrderBy(($dataProvider->sort)->getOrders())->limit(1);
//        VarDumper::dump($query->createCommand()->getRawSql());die;

        foreach ($query->all() as $model) {
            try {
                $lead = $this->findLeadById($model->lqc_lead_id);
                $this->leadRedialService->redial($lead, $user);
                return $this->asJson([
                    'success' => true,
                    'data' => $this->renderAjax('redial_from_queue', ['lead' => $lead])
                ]);
            } catch (\DomainException $e) {
                return $this->asJson([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }

        return $this->asJson([
            'success' => false,
            'message' => 'Not found Leads. Try again.'
        ]);
    }

    /**
     * @param int $userId
     * @param int $leadId
     * @return array
     */
    private function guardAlreadyReservedCurrentLead(int $userId, int $leadId): array
    {
        if (($currentLead = LeadQcall::find()->currentReservedLeadByUser($userId)->one()) && $currentLead->isEqual($leadId)) {
            if ($lead = $currentLead->lqcLead) {
                return [
                    'success' => false,
                    'message' => 'You already reserved this Lead.',
                    'data' => $this->renderAjax('redial_from_queue', ['lead' => $lead])
                ];
            }
            return [
                'success' => false,
                'message' => 'You already reserved this Lead, but Lead not found. Try again later.',
            ];
        }
        return [];
    }

    public function actionRedial(): Response
    {
        $gid = Yii::$app->request->post('gid');
        $lead = $this->findLeadByGid($gid);
        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        if ($data = $this->guardAlreadyReservedCurrentLead($user->id, $lead->id)) {
            return $this->asJson($data);
        }

        try {
            $this->leadRedialService->redial($lead, $user);
        } catch (\DomainException $e) {
            return $this->asJson([
                'success' => false,
                'data' => $this->renderAjax('error', ['message' => $e->getMessage()]),
                'message' => $e->getMessage()
            ]);
        }

        return $this->asJson([
            'success' => true,
            'data' => $this->renderAjax('redial_from_queue', ['lead' => $lead])
        ]);
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionReservation(): Response
    {
        $gid = Yii::$app->request->post('gid');
        $lead = $this->findLeadByGid($gid);
        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        try {
            $this->leadRedialService->reservationBeforeCall($lead, $user);
            return $this->asJson(['success' => true]);
        } catch (\DomainException $e) {
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionRedialFromLastCalls(): Response
    {
        $gid = Yii::$app->request->post('gid');
        $lead = $this->findLeadByGid($gid);
        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        try {
            $this->leadRedialService->redialFromLastCalls($lead, $user);
        } catch (\DomainException $e) {
            return $this->asJson([
                'success' => false,
                'data' => $this->renderAjax('error', ['message' => $e->getMessage()]),
                'message' => $e->getMessage()
            ]);
        }

        return $this->asJson([
            'success' => true,
            'data' => $this->renderAjax('redial_from_last_calls', ['lead' => $lead])
        ]);
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionReservationFromLastCall(): Response
    {
        $gid = Yii::$app->request->post('gid');
        $lead = $this->findLeadByGid($gid);
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
        $lead = $this->findLeadByGid($gid);
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
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionPhoneNumberFrom(): Response
    {
        $gid = Yii::$app->request->post('gid');
        $lead = $this->findLeadByGid($gid);
        try {
            $phoneFrom = $this->leadRedialService->findOrUpdatePhoneNumberFrom($lead);
            return $this->asJson(['success' => true, 'phoneFrom' => $phoneFrom]);
        } catch (\DomainException $e) {
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionUpdateAllShow(): string
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        if (!$user->isAdmin()) {
            throw new ForbiddenHttpException('Access is denied.');
        }

        return $this->renderAjax('_update_all_show');
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionUpdateAll(): Response
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        if (!$user->isAdmin()) {
            throw new ForbiddenHttpException('Access is denied.');
        }
        
        $form = new UpdateAllForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $report = $this->updateAllService->update($form);
            return $this->asJson(['success' => true, 'text' => count($report) . ' rows updated.']);
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionUpdateAllValidation(): array
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        if (!$user->isAdmin()) {
            throw new ForbiddenHttpException('Access is denied.');
        }

        $form = new UpdateAllForm();
        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }
        throw new BadRequestHttpException();
    }

    /**
     * @param $gid
     * @return Lead
     * @throws NotFoundHttpException
     */
    protected function findLeadByGid($gid): Lead
    {
        if ($model = Lead::findOne(['gid' => $gid])) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param $id
     * @return Lead
     * @throws NotFoundHttpException
     */
    protected function findLeadById($id): Lead
    {
        if ($model = Lead::findOne($id)) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
