<?php

namespace frontend\controllers;

use common\components\BackOffice;
use common\models\CaseSale;
use common\models\search\SaleSearch;
use sales\auth\Auth;
use sales\entities\cases\Cases;
use sales\helpers\app\AppHelper;
use sales\services\cases\CasesSaleService;
use Yii;
use yii\base\Exception;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * @property CasesSaleService $casesSaleService
 */
class SaleController extends FController
{
    private $casesSaleService;

    /**
     * SaleController constructor.
     * @param $id
     * @param $module
     * @param CasesSaleService $casesSaleService
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        CasesSaleService $casesSaleService,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->casesSaleService = $casesSaleService;
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }


    /**
     * @return string
     */
    public function actionSearch()
    {
        $searchModel = new SaleSearch();
        $params = Yii::$app->request->queryParams;

        try {
            $dataProvider = $searchModel->search($params);
        } catch (\Exception $exception) {
            $dataProvider = new ArrayDataProvider();
            Yii::error(VarDumper::dumpAsString([$exception->getFile(), $exception->getCode(), $exception->getMessage()]), 'SaleController:actionSearch');
            Yii::$app->session->setFlash('error', $exception->getMessage());
        }

        //VarDumper::dump($dataProvider->allModels); exit;

        return $this->render('search', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        $hash = Yii::$app->request->get('h', '');
        $withFareRules = (int) Yii::$app->request->get('wfr', 0);

        try {
            $arr = explode('|', base64_decode($hash));
            $id = (int) ($arr[1] ?? 0);

            $saleData = $this->casesSaleService->detailRequestToBackOffice($id, $withFareRules);

        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable), 'SaleController:actionView:ErrorBoRequest');
        }

        if (!count($saleData)) {
            throw new BadRequestHttpException('Error. Broken data from BackOffice. ');
        }

        $result = [
            'data' => $saleData,
            'additionalData' => [
                'hash' => $hash,
                'withFareRules' => $withFareRules,
            ],
        ];

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', $result);
        }

        return $this->render('view', $result);
    }

    /**
     * @param int $id
     * @return array
     */
    public function actionDeleteAjax(int $id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['error' => '', 'status' => 0];

        if (Yii::$app->request->isAjax) {
            $saleId = Yii::$app->request->post('sale_id');
            $csId = (int)Yii::$app->request->post('case_id');

            $model = $this->findCase($csId);

            if (!Auth::can('cases/update', ['case' => $model])) {
                $result['error'] = 'Access denied.';
                return $result;
            }

            if ($sale = CaseSale::findOne(['css_cs_id' => $csId, 'css_sale_id' => $saleId])) {
                try {
                    $sale->delete();
                    $result['status'] = 1;
                } catch (\Throwable $throwable) {
                    $result['error'] = $throwable->getMessage();
                }
            }
        }
        return $result;
    }

    /**
     * @param $id
     * @return Cases
     * @throws NotFoundHttpException
     */
    protected function findCase($id): Cases
    {
        if (($model = Cases::findOne(['cs_id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested case does not exist.');
    }
}
