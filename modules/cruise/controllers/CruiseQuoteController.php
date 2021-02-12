<?php

namespace modules\cruise\controllers;

use frontend\controllers\FController;
use modules\cruise\src\entity\cruise\Cruise;
use modules\cruise\src\entity\cruiseQuote\search\CruiseQuoteSearch;
use modules\cruise\src\services\search\CruiseQuoteSearch as SearchService;
use modules\cruise\src\services\search\Params;
use modules\cruise\src\useCase\createQuote\CreateQuoteService;
use modules\cruise\src\useCase\updateMarkup\CruiseMarkupService;
use sales\auth\Auth;
use Yii;
use modules\cruise\src\entity\cruiseQuote\CruiseQuote;
use yii\base\Exception;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class CruiseQuoteController extends FController
{
    private \modules\cruise\src\services\search\CruiseQuoteSearch $cruiseQuoteSearch;
    private CreateQuoteService $createQuoteService;
    private CruiseMarkupService $cruiseMarkupService;

    public function __construct(
        $id,
        $module,
        SearchService $cruiseQuoteSearch,
        CreateQuoteService $createQuoteService,
        CruiseMarkupService $cruiseMarkupService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->cruiseQuoteSearch = $cruiseQuoteSearch;
        $this->createQuoteService = $createQuoteService;
        $this->cruiseMarkupService = $cruiseMarkupService;
    }

    /**
    * @return array
    */
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new CruiseQuoteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new CruiseQuote();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->crq_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->crq_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     * @return CruiseQuote
     * @throws NotFoundHttpException
     */
    protected function findModel($id): CruiseQuote
    {
        if (($model = CruiseQuote::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionSearchAjax()
    {
        $cruiseId = (int) \Yii::$app->request->get('id');
        $cruise = Cruise::findOne($cruiseId);

        if (!$cruise) {
            throw new NotFoundHttpException('Not found.');
        }

        try {
            if (!$cruise->cabins) {
                throw new \DomainException('Please add cabin.');
            }
            if (!$cruise->crs_departure_date_from && !$cruise->crs_arrival_date_to) {
                throw new \DomainException('Please update request.');
            }
            $params = new Params($cruise);
            if (!$params->validate()) {
                throw new \DomainException('Invalid params. Errors: ' . VarDumper::dumpAsString($params->getErrors()));
            }
            $cruises = $this->cruiseQuoteSearch->search($params);
        } catch (\DomainException $e) {
            $cruises = [];
            \Yii::$app->session->setFlash('error', $e->getMessage());
        } catch (\Throwable $e) {
            $cruises = [];
            \Yii::error($e->getMessage(), 'CruiseQuoteSearchError');
            \Yii::$app->session->setFlash('error', 'Server error. Try again later.');
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $cruises,
            'pagination' => [
                'pageSize' => 10,
            ],
//            'sort' => [
//                'attributes' => ['ranking', 'name', 's2C'],
//            ],
        ]);

        return $this->renderAjax('search/_search_quotes', [
            'dataProvider' => $dataProvider,
            'cruise' => $cruise,
        ]);
    }

    public function actionAddAjax(): array
    {
        $cruiseId = (int) Yii::$app->request->get('cruiseId');
        $quoteId = (string) Yii::$app->request->post('quoteId');
        $cabinCode = (string) Yii::$app->request->post('cabinCode');

        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            if (!$cruiseId) {
                throw new Exception('Cruise Request param not found', 2);
            }

            if (!$quoteId) {
                throw new Exception('Cruise Quote Id param not found', 3);
            }

            if (!$cabinCode) {
                throw new Exception('Cabin code param not found', 4);
            }

            $cruise = Cruise::findOne($cruiseId);

            if (!$cruise) {
                throw new NotFoundHttpException('Cruise not found.');
            }

            $productId = $cruise->crs_product_id;

            $params = new Params($cruise);
            if (!$params->validate()) {
                throw new \DomainException('Invalid params. Errors: ' . VarDumper::dumpAsString($params->getErrors()));
            }

            $cruises = $this->cruiseQuoteSearch->search($params);

            if (!$cruises) {
                throw new \DomainException('Not found Cruises');
            }

            $quote = [];
            foreach ($cruises as $quoteItem) {
                if ($quoteItem['id'] === $quoteId) {
                    $quote = $quoteItem;
                    $cabin = [];
                    foreach ($quoteItem['cabins'] as $cabinItem) {
                        if ($cabinItem['code'] === $cabinCode) {
                            $cabin = $cabinItem;
                            break;
                        }
                    }
                    unset($quote['cabins']);
                    $quote['cabin'] = $cabin;
                    break;
                }
            }

            if (!$quote) {
                throw new \DomainException('Not found selected quote');
            }

            $createdQuoteId = $this->createQuoteService->create(Auth::id(), $quote, $cruise, 'USD');
        } catch (\Throwable $throwable) {
            Yii::warning(VarDumper::dumpAsString($throwable->getTraceAsString()), 'app');
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return [
            'product_id' => $productId,
            'message' => 'Successfully added quote. Cruise "' . Html::encode($quote['cruiseLine']['name']) . '", Cruise Quote Id: (' . $createdQuoteId . ')'
        ];
    }

    public function actionAjaxUpdateAgentMarkup(): Response
    {
        $extraMarkup = Yii::$app->request->post('extra_markup');

        $cruiseQuoteId = array_key_first($extraMarkup);
        $value = $extraMarkup[$cruiseQuoteId];

        if ($cruiseQuoteId && $value !== null) {
            try {
                $this->cruiseMarkupService->updateAgentMarkup($cruiseQuoteId, $value);
            } catch (\RuntimeException $e) {
                return $this->asJson(['message' => $e->getMessage()]);
            } catch (\Throwable $e) {
                Yii::error($e->getTraceAsString(), 'HotelQuoteController::actionAjaxUpdateAgentMarkup::Throwable');
                return $this->asJson(['message' => 'Server error']);
            }

            return $this->asJson(['output' => $value]);
        }

        throw new BadRequestHttpException();
    }
}
