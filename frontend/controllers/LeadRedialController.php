<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\Lead;
use common\models\search\LeadQcallSearch;
use sales\services\lead\LeadRedialService;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class LeadRedialController
 *
 * @property LeadRedialService $leadRedialService
 */
class LeadRedialController extends FController
{

    private $leadRedialService;

    public function __construct($id, $module, LeadRedialService $leadRedialService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->leadRedialService = $leadRedialService;
    }

    public function actionIndex(): string
    {
        $searchModel = new LeadQcallSearch();
        $dataProvider = $searchModel->searchList(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionRedial(): Response
    {
        $gid = Yii::$app->request->get('gid');
        $lead = $this->findLeadById($gid);
        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        try {
            $this->leadRedialService->redial($lead, $user);
        } catch (\DomainException $e) {
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }

        return $this->asJson([
            'success' => true,
            'data' => $this->renderAjax('redial', ['lead' => $lead])
        ]);
    }

    public function actionTake(): Response
    {
        $gid = Yii::$app->request->get('gid');
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

    protected function findLeadById($gid): Lead
    {
        if ($model = Lead::findOne(['gid' => $gid])) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
