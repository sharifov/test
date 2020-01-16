<?php

namespace modules\flight\controllers;

use modules\flight\src\repositories\flight\FlightRepository;
use modules\flight\src\useCases\api\searchQuote\FlightQuoteSearchForm;
use modules\flight\src\useCases\api\searchQuote\FlightQuoteSearchHelper;
use modules\flight\src\useCases\api\searchQuote\FlightQuoteSearchService;
use Yii;
use modules\flight\models\FlightQuote;
use modules\flight\models\search\FlightQuoteSearch;
use frontend\controllers\FController;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FlightQuoteController implements the CRUD actions for FlightQuote model.
 *
 * @property FlightRepository $flightRepository
 * @property FlightQuoteSearchService $quoteSearchService
 */
class FlightQuoteController extends FController
{
	/**
	 * @var FlightRepository
	 */
	private $flightRepository;
	/**
	 * @var FlightQuoteSearchService
	 */
	private $quoteSearchService;

	/**
	 * FlightQuoteController constructor.
	 * @param $id
	 * @param $module
	 * @param FlightRepository $flightRepository
	 * @param FlightQuoteSearchService $quoteSearchService
	 * @param array $config
	 */
	public function __construct($id, $module, FlightRepository $flightRepository, FlightQuoteSearchService $quoteSearchService, $config = [])
	{
		parent::__construct($id, $module, $config);
		$this->flightRepository = $flightRepository;
		$this->quoteSearchService = $quoteSearchService;
	}

	/**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all FlightQuote models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FlightQuoteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FlightQuote model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new FlightQuote model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FlightQuote();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->fq_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing FlightQuote model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->fq_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

	/**
	 * Deletes an existing FlightQuote model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

	/**
	 * @return string
	 * @throws \ReflectionException
	 */
    public function actionAjaxSearchQuote(): string
	{
		$flightId = Yii::$app->request->get('id');
		$gds = Yii::$app->request->post('gds', '');

		$form = new FlightQuoteSearchForm();
		$form->load(Yii::$app->request->post() ?: Yii::$app->request->get());

		try {
			$flight = $this->flightRepository->find($flightId);

			$keyCache = $flight->generateQuoteSearchKeyCache();
			$quotes = \Yii::$app->cache->get($keyCache);

			if ($quotes === false) {
				$quotes = $this->quoteSearchService->search($flight, $gds);
				if ($quotes['results']) {
					\Yii::$app->cache->set($keyCache, $quotes = FlightQuoteSearchHelper::formatQuoteData($quotes), 600);
				}
			}
		} catch (NotFoundHttpException | \DomainException $e) {
			$errorMessage = $e->getMessage();
			$quotes = [];
		} catch (\Throwable $e) {
			Yii::error($e->getMessage() . '; File: ' . $e->getFile() . '; Line: ' . $e->getLine(), 'Flight::FlightQuoteController::actionAjaxSearchQuote::Throwable');
			$quotes = [];
		}

		$viewData = FlightQuoteSearchHelper::getAirlineLocationInfo($quotes);

		if (Yii::$app->request->isPost) {
			$params = ['page' => 1];
		}

		$quotes = $form->applyFilters($quotes);

		$dataProvider = new ArrayDataProvider([
			'allModels' => $quotes['results'] ?? [],
			'pagination' => [
				'pageSize' => 10,
				'params' => array_merge(Yii::$app->request->get(), $form->getFilters(), $params ?? []),
			],
			'sort'=> [
				'attributes' => ['price', 'duration'],
				'defaultOrder' => [$form->getSortBy() => $form->getSortType()],
			],
		]);

		$viewData['quotes'] = $quotes;
		$viewData['dataProvider'] = $dataProvider;
		$viewData['flightId'] = $flight->fl_id;
		$viewData['gds'] = $gds;
		$viewData['flight'] = $flight;
		$viewData['searchForm'] = $form;
		$viewData['errorMessage'] = $errorMessage ?? '';

		return $this->renderAjax('partial/_quote_search', $viewData);
	}

    /**
     * Finds the FlightQuote model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FlightQuote the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FlightQuote::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
