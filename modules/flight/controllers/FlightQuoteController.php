<?php

namespace modules\flight\controllers;

use modules\flight\models\FlightPax;
use modules\flight\src\entities\flightQuotePaxPrice\FlightQuotePaxPriceQuery;
use modules\flight\src\helpers\FlightQuoteHelper;
use modules\flight\src\repositories\flight\FlightRepository;
use modules\flight\src\repositories\flightQuotePaxPriceRepository\FlightQuotePaxPriceRepository;
use modules\flight\src\useCases\api\searchQuote\FlightQuoteSearchForm;
use modules\flight\src\useCases\api\searchQuote\FlightQuoteSearchHelper;
use modules\flight\src\useCases\api\searchQuote\FlightQuoteSearchService;
use modules\flight\src\useCases\flightQuote\FlightQuoteManageService;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use sales\auth\Auth;
use sales\repositories\NotFoundException;
use Yii;
use modules\flight\models\FlightQuote;
use modules\flight\models\search\FlightQuoteSearch;
use frontend\controllers\FController;
use yii\data\ArrayDataProvider;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FlightQuoteController implements the CRUD actions for FlightQuote model.
 *
 * @property FlightRepository $flightRepository
 * @property FlightQuoteSearchService $quoteSearchService
 * @property FlightQuoteManageService $flightQuoteManageService
 * @property ProductQuoteRepository $productQuoteRepository
 * @property FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository
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
	 * @var FlightQuoteManageService
	 */
	private $flightQuoteManageService;
	/**
	 * @var ProductQuoteRepository
	 */
	private $productQuoteRepository;
	/**
	 * @var FlightQuotePaxPriceRepository
	 */
	private $flightQuotePaxPriceRepository;

	/**
	 * FlightQuoteController constructor.
	 * @param $id
	 * @param $module
	 * @param FlightRepository $flightRepository
	 * @param FlightQuoteSearchService $quoteSearchService
	 * @param FlightQuoteManageService $flightQuoteManageService
	 * @param ProductQuoteRepository $productQuoteRepository
	 * @param FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository
	 * @param array $config
	 */
	public function __construct(
		$id,
		$module,
		FlightRepository $flightRepository,
		FlightQuoteSearchService $quoteSearchService,
		FlightQuoteManageService $flightQuoteManageService,
		ProductQuoteRepository $productQuoteRepository,
		FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository,
		$config = []
	)
	{
		parent::__construct($id, $module, $config);
		$this->flightRepository = $flightRepository;
		$this->quoteSearchService = $quoteSearchService;
		$this->flightQuoteManageService = $flightQuoteManageService;
		$this->productQuoteRepository = $productQuoteRepository;
		$this->flightQuotePaxPriceRepository = $flightQuotePaxPriceRepository;
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
		$pjaxId = Yii::$app->request->post('pjaxId', '');

		$form = new FlightQuoteSearchForm();
		$form->load(Yii::$app->request->post() ?: Yii::$app->request->get());

		try {
			$flight = $this->flightRepository->find($flightId);

			$quotes = \Yii::$app->cacheFile->get($flight->fl_request_hash_key);

			if ($quotes === false) {
				$quotes = $this->quoteSearchService->search($flight, $gds);
				if ($quotes['results']) {
					\Yii::$app->cacheFile->set($flight->fl_request_hash_key, $quotes = FlightQuoteSearchHelper::formatQuoteData($quotes), 600);
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
		$viewData['pjaxId'] = $pjaxId;

		return $this->renderAjax('partial/_quote_search', $viewData);
	}

	/**
	 * @return \yii\web\Response
	 * @throws \Throwable
	 */
	public function actionAjaxAddQuote(): \yii\web\Response
	{
		$key = Yii::$app->request->post('key');
		$flightId = Yii::$app->request->post('flightId');

		$result = [
			'error' => '',
			'status' => false
		];

		try {
			$flight = $this->flightRepository->find((int)$flightId);

			if (!$key) {
				throw new \DomainException('Key is empty!');
			}

			$quotes = \Yii::$app->cacheFile->get($flight->fl_request_hash_key);

			if ($quotes === false && empty($quotes['results'])) {
				throw new \DomainException('Not found Quote from Search result from Cache. Please update search request!');
			}

			$selectedQuote = [];
			foreach ($quotes['results'] as $quote) {
				if ($quote['key'] === $key) {
					$selectedQuote = $quote;
					break;
				}
			}

			if (!$selectedQuote) {
				throw new \DomainException('Quote Key not equal to key from Cache');
			}

			$this->flightQuoteManageService->create($flight, $selectedQuote, Auth::id());

			$result['status'] = true;

			Yii::$app->session->setFlash('success', 'Quote added.');

		} catch (\DomainException | NotFoundException | \RuntimeException $e) {
			$result['error'] = $e->getMessage();
		} catch (\Throwable $e) {
			Yii::error($e->getMessage() . '; File: ' . $e->getFile() . '; Line: ' . $e->getLine(), 'Flight::FlightQuoteController::actionAjaxAddQuote::Throwable');
			$result['error'] = 'Server internal error; Try again letter';
		}

		return $this->asJson($result);
	}

	/**
	 * @return string
	 * @throws ForbiddenHttpException
	 */
	public function actionAjaxQuoteDetails(): string
	{
		$productQuoteId = Yii::$app->request->get('id');

		$productQuote = $this->productQuoteRepository->find($productQuoteId);
		$lead = $productQuote->pqProduct->prLead;

		if($lead->isInTrash() && Yii::$app->user->identity->canRole('agent')) {
			throw new ForbiddenHttpException('Access Denied for Agent');
		}

		return $this->renderPartial('partial/_quote_view_details', [
			'productQuote' => $productQuote,
			'flightQuote' => FlightQuote::findByProductQuote($productQuote)
		]);
	}

	/**
	 * @throws BadRequestHttpException
	 * @throws \Throwable
	 */
	public function actionAjaxUpdateAgentMarkup()
	{
		$extraMarkup = Yii::$app->request->post('extra_markup');

		$paxCode = array_key_first($extraMarkup);
		$fqId = array_key_first($extraMarkup[$paxCode]);
		$value = $extraMarkup[$paxCode][$fqId];

		if ($paxCode && $fqId && $value !== null) {
			try {

				$paxCodeId = FlightPax::getPaxId($paxCode);

				$flightQuotePaxPrice = $this->flightQuotePaxPriceRepository->findByIdAndCode($fqId, $paxCodeId);

				$this->flightQuoteManageService->updateAgentMarkup($flightQuotePaxPrice, $value);
			} catch (\RuntimeException $e) {
				return $this->asJson(['message' => $e->getMessage()]);
			}

			return $this->asJson(['output' => $value]);
		}

		throw new BadRequestHttpException();
	}

	public function actionQuoteStatusLog()
	{
		$productQuoteId = Yii::$app->request->get('quoteId');
		$quote = $this->productQuoteRepository->find($productQuoteId);
		$flightQuote = FlightQuote::findByProductQuote($quote);

		return $this->renderAjax('partial/_quote_status_log', [
			'flightQuote' => $flightQuote,
		]);
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
