<?php

namespace modules\flight\controllers;

use common\models\Notifications;
use frontend\helpers\JsonHelper;
use modules\flight\components\api\FlightQuoteBookService;
use modules\flight\models\Flight;
use modules\flight\models\FlightPax;
use modules\flight\src\helpers\FlightQuoteHelper;
use modules\flight\src\repositories\flight\FlightRepository;
use modules\flight\src\repositories\flightQuotePaxPriceRepository\FlightQuotePaxPriceRepository;
use modules\flight\src\services\flight\FlightManageService;
use modules\flight\src\services\flightQuote\FlightQuoteBookGuardService;
use modules\flight\src\services\flightQuote\FlightQuotePdfService;
use modules\flight\src\services\flightQuoteFlight\FlightQuoteFlightPdfService;
use modules\flight\src\useCases\api\searchQuote\FlightQuoteSearchForm;
use modules\flight\src\useCases\api\searchQuote\FlightQuoteSearchHelper;
use modules\flight\src\useCases\api\searchQuote\FlightQuoteSearchService;
use modules\flight\src\useCases\flightQuote\createManually\FlightQuoteCreateForm;
use modules\flight\src\useCases\flightQuote\createManually\helpers\FlightQuotePaxPriceHelper;
use modules\flight\src\useCases\flightQuote\FlightQuoteManageService;
use modules\order\src\events\OrderFileGeneratedEvent;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\entities\productType\ProductType;
use modules\product\src\useCases\product\create\ProductCreateService;
use sales\auth\Auth;
use sales\forms\CompositeFormHelper;
use sales\helpers\app\AppHelper;
use sales\repositories\lead\LeadRepository;
use sales\repositories\NotFoundException;
use Yii;
use modules\flight\models\FlightQuote;
use modules\flight\models\search\FlightQuoteSearch;
use frontend\controllers\FController;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * FlightQuoteController implements the CRUD actions for FlightQuote model.
 *
 * @property FlightRepository $flightRepository
 * @property FlightQuoteSearchService $quoteSearchService
 * @property FlightQuoteManageService $flightQuoteManageService
 * @property ProductQuoteRepository $productQuoteRepository
 * @property FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository
 * @property LeadRepository $leadRepository
 * @property ProductCreateService $productCreateService
 * @property FlightManageService $flightManageService
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
     * @var LeadRepository
     */
    private $leadRepository;
    /**
     * @var ProductCreateService
     */
    private $productCreateService;
    /**
     * @var FlightManageService
     */
    private $flightManageService;

    /**
     * FlightQuoteController constructor.
     * @param $id
     * @param $module
     * @param FlightRepository $flightRepository
     * @param FlightQuoteSearchService $quoteSearchService
     * @param FlightQuoteManageService $flightQuoteManageService
     * @param ProductQuoteRepository $productQuoteRepository
     * @param FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository
     * @param LeadRepository $leadRepository
     * @param ProductCreateService $productCreateService
     * @param FlightManageService $flightManageService
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
        LeadRepository $leadRepository,
        ProductCreateService $productCreateService,
        FlightManageService $flightManageService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->flightRepository = $flightRepository;
        $this->quoteSearchService = $quoteSearchService;
        $this->flightQuoteManageService = $flightQuoteManageService;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->flightQuotePaxPriceRepository = $flightQuotePaxPriceRepository;
        $this->leadRepository = $leadRepository;
        $this->productCreateService = $productCreateService;
        $this->flightManageService = $flightManageService;
    }

    /**
     * {@inheritdoc}
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

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->fq_ticket_json = JsonHelper::decode($model->fq_ticket_json);
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->fq_id]);
            }
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

        $flight = $this->flightRepository->find($flightId);
        $form = new FlightQuoteSearchForm();

        $this->increaseLimits();

        try {
            if (empty($flight->flightSegments)) {
                throw new \DomainException('Flight Segment data is not found; Create a new flight request;');
            }

            $quotes = \Yii::$app->cacheFile->get($flight->fl_request_hash_key);

            if ($quotes === false) {
                $quotes = $this->quoteSearchService->search($flight, $gds);
                if (!empty($quotes['results'])) {
                    \Yii::$app->cacheFile->set($flight->fl_request_hash_key, $quotes = FlightQuoteSearchHelper::formatQuoteData($quotes), 600);
                }
            }

            $form->load(Yii::$app->request->post() ?: Yii::$app->request->get());

            $viewData = FlightQuoteSearchHelper::getAirlineLocationInfo($quotes);

            if (Yii::$app->request->isPost) {
                $params = ['page' => 1];
            }

            if (!empty($quotes['results'])) {
                $quotes = $form->applyFilters($quotes);
            }

            $dataProvider = new ArrayDataProvider([
                'allModels' => $quotes['results'] ?? [],
                'pagination' => [
                    'pageSize' => 10,
                    'params' => array_merge(Yii::$app->request->get(), $form->getFilters(), $params ?? []),
                ],
                'sort' => [
                    'attributes' => ['price', 'duration'],
                    'defaultOrder' => [$form->getSortBy() => $form->getSortType()],
                ],
            ]);

            if (empty($pjaxId)) {
                $pjaxId = 'pjax-product-' . $flight->fl_product_id;
            }
        } catch (NotFoundHttpException | \DomainException $e) {
            $errorMessage = $e->getMessage();
            $quotes = [];
        } catch (\Throwable $e) {
            Yii::error($e->getMessage() . '; File: ' . $e->getFile() . '; Line: ' . $e->getLine(), 'Flight::FlightQuoteController::actionAjaxSearchQuote::Throwable');
            $quotes = [];
            $errorMessage = $e->getMessage();
        }

        $viewData['quotes'] = $quotes;
        $viewData['dataProvider'] = $dataProvider ?? new ArrayDataProvider();
        $viewData['flightId'] = $flight->fl_id ?? null;
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

                if (isset($quote['ngsItineraries'])) {
                    foreach ($quote['ngsItineraries'] as $ngsItinerary) {
                        if ($ngsItinerary['key'] === $key) {
                            $selectedQuote = $ngsItinerary;
                        }
                    }
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

        if ($lead->isInTrash() && Auth::user()->isAgent()) {
            throw new ForbiddenHttpException('Access Denied for Agent');
        }

        return $this->renderPartial('partial/_quote_view_details', [
            'productQuote' => $productQuote,
            'flightQuote' => FlightQuote::findByProductQuoteId($productQuote)
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

                $this->flightQuoteManageService->updateAgentMarkup($flightQuotePaxPrice, (float)$value);
                $leadId = $flightQuotePaxPrice->qppFlightQuote->fqProductQuote->pqProduct->pr_lead_id ?? null;
                if ($leadId) {
                    Notifications::pub(
                        ['lead-' . $leadId],
                        'reloadOrders',
                        ['data' => []]
                    );
                    Notifications::pub(
                        ['lead-' . $leadId],
                        'reloadOffers',
                        ['data' => []]
                    );
                }
            } catch (\RuntimeException $e) {
                return $this->asJson(['message' => $e->getMessage()]);
            }

            return $this->asJson(['output' => $value]);
        }

        throw new BadRequestHttpException();
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionQuoteStatusLog()
    {
        $productQuoteId = Yii::$app->request->get('quoteId');
        if (!$productQuoteId) {
            throw new BadRequestHttpException('Product quote id is not provided');
        }
        try {
            $quote = $this->productQuoteRepository->find($productQuoteId);
            $flightQuote = FlightQuote::findByProductQuoteId($quote);
        } catch (\Throwable $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        return $this->renderAjax('partial/_quote_status_log', [
            'flightQuote' => $flightQuote,
        ]);
    }

    public function actionAjaxAddQuoteContent(): string
    {
        $leadId = Yii::$app->request->post('leadId', 0);
        $flightId = Yii::$app->request->post('flightId', 0);
        $pjaxReloadId = Yii::$app->request->post('pjaxReloadId', 0);
        $action = Yii::$app->request->post('action', 0);

        $message = null;

        try {
            $lead = $this->leadRepository->find($leadId);
            $flight = $this->flightRepository->find($flightId);

            if (empty($flight->flightSegments)) {
                throw new \DomainException('Flight Segment data is not found; Create a new flight request;');
            }

            $data = CompositeFormHelper::prepareDataForMultiInput(
                Yii::$app->request->post(),
                'FlightQuoteCreateForm',
                ['prices' => 'FlightQuotePaxPriceForm']
            );

            $form = new FlightQuoteCreateForm($flight, Auth::id());

            if ($form->load($data['post'])) {
                if (empty($action) && $form->validate()) {
                    $preparedData = $this->flightQuoteManageService->prepareFlightQuoteData($form);
                    $this->flightQuoteManageService->create($flight, $preparedData, $form->quoteCreator);
                    return '<script>createNotify("Quote created", "Quote successfully created", "success"); $("#modal-md").modal("hide");pjaxReload({container: "#' . $pjaxReloadId . '"})</script>';
                }

                if ($action === FlightQuoteCreateForm::ACTION_CREATE_NEW_PRODUCT) {
                    $form->scenario = FlightQuoteCreateForm::ACTION_CREATE_NEW_PRODUCT;
                    if ($form->validate()) {
                        $preparedData = $this->flightQuoteManageService->prepareFlightQuoteData($form);
                        $this->flightManageService->createNewProductAndAssignNewQuote($form, $lead, $preparedData);
                        return '<script>createNotify("Created new Product: Flight", "Quote successfully assigned to a new product Flight", "success"); $("#modal-md").modal("hide");pjaxReload({container: "#pjax-lead-products-wrap"})</script>';
                    }
                } elseif ($action === FlightQuoteCreateForm::ACTION_UPDATE_FLIGHT_REQUEST) {
                    $form->scenario = FlightQuoteCreateForm::ACTION_CREATE_NEW_PRODUCT;
                    if ($form->validate()) {
                        $preparedData = $this->flightQuoteManageService->prepareFlightQuoteData($form);
                        $this->flightManageService->updateFlightRequestAndAssignNewQuote($form, $flight, $preparedData);
                        return '<script>createNotify("Flight Request successfully updated", "Quote successfully assigned to a product Flight", "success"); $("#modal-md").modal("hide");pjaxReload({container: "#pjax-lead-products-wrap"})</script>';
                    }
                } elseif ($action === FlightQuoteCreateForm::ACTION_APPLY_PRICING_INFO) {
                    $dump = FlightQuoteHelper::parsePriceDump($form->pricingInfo);
                    $form->updateDataByPricingDump($dump);
                    FlightQuoteHelper::quoteCalculatePaxPrices($form);
                } elseif ($action === FlightQuoteCreateForm::ACTION_CALCULATE_PRICES) {
                    FlightQuoteHelper::quoteCalculatePaxPrices($form);
                }
            }
        } catch (\RuntimeException | \DomainException $e) {
            if (isset($form)) {
                $form->addError('general', $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
            } else {
                $message = $e->getMessage();
            }
        } catch (\Throwable $e) {
            $message = 'Internal Server Error';
            Yii::error($e->getMessage() . '; File: ' . $e->getFile() . '; Line: ' . $e->getLine(), 'FlightModule::FlightQuoteController::actionAjaxAddQuoteContent::Throwable');
        }

        return $this->renderAjax('partial/_add_quote_manual', [
            'message' => $message,
            'createQuoteForm' => $form ?? new FlightQuoteCreateForm(),
            'flight' => $flight ?? new Flight(),
            'pjaxReloadId' => $pjaxReloadId,
            'lead' => $lead
        ]);
    }

    public function actionAjaxBook(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $flightQuoteId = (int) Yii::$app->request->post('id', 0);
        $result = ['status' => 0, 'message' => '', 'data' => []];

        $flightQuote = $this->findModel($flightQuoteId);
        try {
            FlightQuoteBookGuardService::guard($flightQuote);
            $requestData = ArrayHelper::getValue($flightQuote, 'fqProductQuote.pqOrder.or_request_data');

            if ($responseData = FlightQuoteBookService::requestBook($requestData)) {
                if (FlightQuoteBookService::createBook($flightQuote, $responseData)) {
                    Notifications::pub(
                        ['lead-' . ArrayHelper::getValue($flightQuote, 'fqProductQuote.pqProduct.pr_lead_id')],
                        'quoteBooked',
                        ['data' => ['productId' => ArrayHelper::getValue($flightQuote, 'fqProductQuote.pq_product_id')]]
                    );
                    $result['status'] = 1;
                    $result['message'] = 'Booking request accepted. RecordLocator: ' . ArrayHelper::getValue($responseData, 'recordLocator');
                    return $result;
                }
            }
            throw new \DomainException('Create Book is failed.');
        } catch (\Throwable $throwable) {
            $this->errorBook($flightQuote);
            $result['message'] = $throwable->getMessage();
            \Yii::error(AppHelper::throwableLog($throwable, true), 'FlightQuoteController:actionAjaxBook');
        }
        return $result;
    }

    public function actionAjaxFileGenerate(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $flightQuoteId = (int) Yii::$app->request->post('id', 0);
        $result = ['status' => 0, 'message' => ''];

        try {
            $flightQuote = $this->findModel($flightQuoteId);

            if (!$flightQuote->isBooked()) {
                throw new \DomainException('Quote should have Booked status.');
            }
            if (!$flightQuote->flightQuoteFlights) {
                throw new NotFoundException('FlightQuoteFlights not found in FlightQuote Id(' . $flightQuoteId . ')');
            }

            foreach ($flightQuote->flightQuoteFlights as $flightQuoteFlight) {
                $flightQuoteFlightPdfService = new FlightQuoteFlightPdfService($flightQuoteFlight);
                $flightQuoteFlightPdfService->setProductQuoteId($flightQuoteFlight->fqfFq->fq_product_quote_id);
                $flightQuoteFlightPdfService->processingFile();
            }

            $result['status'] = 1;
            $result['message'] = 'Document have been successfully generated';
        } catch (\Throwable $throwable) {
            $result['message'] = $throwable->getMessage();
            \Yii::error(AppHelper::throwableLog($throwable), 'FlightQuoteController:actionAjaxFileGenerate');
        }
        return $result;
    }

    private function errorBook(FlightQuote $flightQuote): void
    {
        try {
            $productQuoteRepository = \Yii::createObject(ProductQuoteRepository::class);
            $productQuote = $flightQuote->fqProductQuote;
            $productQuote->error(null, 'Auto booking error');
            $productQuoteRepository->save($productQuote);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Flight Quote Transfer to "Error" error',
                'flightQuoteId' => $flightQuote->fq_id,
            ], 'FlightQuoteController:errorBook');
        }
    }

    public function actionCancel(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $flightQuoteId = (int) Yii::$app->request->post('id', 0);

        try {
            $flightQuote = $this->findModel($flightQuoteId);
            $projectId = $flightQuote->fqProductQuote->pqProduct->prLead->project_id ?? null;
            if (!$projectId) {
                throw new \DomainException('Not found Project');
            }
            if (!$flightQuote->fq_flight_request_uid) {
                throw new \DomainException('Not found Request UID');
            }
            FlightQuoteBookService::cancel($flightQuote->fq_flight_request_uid, $projectId);
            $productQuote = $this->productQuoteRepository->find($flightQuote->fq_product_quote_id);
            $productQuote->cancelled(Auth::id());
            $this->productQuoteRepository->save($productQuote);
            return [
                'error' => false,
                'message' => 'OK',
            ];
        } catch (\Throwable $throwable) {
            return [
                'error' => true,
                'message' => $throwable->getMessage()
            ];
        }
    }

    public function actionVoid(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $flightQuoteId = (int) Yii::$app->request->post('id', 0);

        try {
            $flightQuote = $this->findModel($flightQuoteId);
            $projectId = $flightQuote->fqProductQuote->pqProduct->prLead->project_id ?? null;
            if (!$projectId) {
                throw new \DomainException('Not found Project');
            }
            if (!$flightQuote->fq_flight_request_uid) {
                throw new \DomainException('Not found Request UID');
            }
            FlightQuoteBookService::void($flightQuote->fq_flight_request_uid, $projectId);
            $productQuote = $this->productQuoteRepository->find($flightQuote->fq_product_quote_id);
            $productQuote->cancelled(Auth::id());
            $this->productQuoteRepository->save($productQuote);
            return [
                'error' => false,
                'message' => 'OK',
            ];
        } catch (\Throwable $throwable) {
            return [
                'error' => true,
                'message' => $throwable->getMessage()
            ];
        }
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

        throw new NotFoundHttpException('FlightQuote not found.');
    }

    private function increaseLimits(string $memoryLimit = '640M', int $timeLimit = 300): void
    {
        try {
            ini_set('memory_limit', $memoryLimit);
            set_time_limit($timeLimit);
            if (isset(Yii::$app->log->targets['debug']->enabled)) {
                Yii::$app->log->targets['debug']->enabled = false;
            }
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'HotelQuoteController:increaseLimits');
        }
    }
}
