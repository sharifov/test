<?php

namespace modules\flight\controllers;

use modules\flight\src\repositories\flight\FlightRepository;
use modules\flight\src\useCases\api\searchQuote\FlightQuoteSearchHelper;
use modules\flight\src\useCases\api\searchQuote\FlightQuoteSearchService;
use Yii;
use modules\flight\models\FlightQuote;
use modules\flight\models\search\FlightQuoteSearch;
use frontend\controllers\FController;
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
	 */
    public function actionAjaxSearchQuote(): string
	{
		$flightId = Yii::$app->request->get('id');
		$gds = Yii::$app->request->post('gds', '');
		$flight = $this->flightRepository->find($flightId);

		$quotes = $this->quoteSearchService->search($flight, $gds);

		$viewData = FlightQuoteSearchHelper::getAirlineLocationInfo($quotes);
		$viewData['result'] = $quotes;
		$viewData['flightId'] = $flight->fl_id;
		$viewData['gds'] = $gds;
		$viewData['flight'] = $flight;

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
