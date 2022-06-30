<?php

namespace modules\flight\controllers;

use common\components\BackOffice;
use common\models\Airports;
use common\models\Currency;
use common\models\Notifications;
use common\models\Project;
use DateTime;
use frontend\helpers\JsonHelper;
use modules\cases\src\abac\CasesAbacObject;
use modules\cases\src\abac\dto\CasesAbacDto;
use modules\flight\components\api\ApiFlightQuoteSearchService;
use modules\flight\components\api\FlightQuoteBookService;
use modules\flight\models\Flight;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuoteFlight;
use modules\flight\models\query\FlightQuoteTicketQuery;
use modules\flight\src\dto\flightQuoteSearchDTO\FlightQuoteSearchDTO;
use modules\flight\src\dto\itineraryDump\ItineraryDumpDTO;
use modules\flight\src\helpers\FlightQuoteHelper;
use modules\flight\src\repositories\flight\FlightRepository;
use modules\flight\src\repositories\flightQuotePaxPriceRepository\FlightQuotePaxPriceRepository;
use modules\flight\src\services\flight\FlightManageService;
use modules\flight\src\services\flightQuote\FlightQuoteBookGuardService;
use modules\flight\src\services\flightQuote\FlightQuoteTicketIssuedService;
use modules\flight\src\useCases\api\searchQuote\FlightQuoteSearchForm;
use modules\flight\src\useCases\api\searchQuote\FlightQuoteSearchHelper;
use modules\flight\src\useCases\flightQuote\createManually\FlightQuoteCreateForm;
use modules\flight\src\useCases\flightQuote\createManually\FlightQuotePaxPriceForm;
use modules\flight\src\useCases\flightQuote\createManually\helpers\FlightQuotePaxPriceHelper;
use modules\flight\src\useCases\flightQuote\FlightQuoteManageService;
use modules\flight\src\useCases\reProtectionQuoteManualCreate\form\ReProtectionQuoteCreateForm;
use modules\flight\src\useCases\reProtectionQuoteManualCreate\service\ReProtectionQuoteManualCreateService;
use modules\flight\src\useCases\voluntaryExchangeManualCreate\form\AddChangeForm;
use modules\flight\src\useCases\voluntaryExchangeManualCreate\form\VoluntaryQuoteCreateForm;
use modules\flight\src\useCases\voluntaryExchangeManualCreate\service\VoluntaryExchangeBOPrepareService;
use modules\flight\src\useCases\voluntaryExchangeManualCreate\service\VoluntaryExchangeBOService;
use modules\flight\src\useCases\voluntaryExchangeManualCreate\service\VoluntaryQuoteManualCreateService;
use modules\flight\src\useCases\voluntaryRefund\manualCreate\TicketForm;
use modules\flight\src\useCases\voluntaryRefund\manualCreate\VoluntaryRefundCreateForm;
use modules\flight\src\useCases\voluntaryRefund\VoluntaryRefundService;
use modules\order\src\entities\order\OrderRepository;
use modules\product\src\abac\ProductQuoteAbacObject;
use modules\product\src\abac\dto\ProductQuoteAbacDto;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\services\productQuote\ProductQuoteCloneService;
use modules\product\src\useCases\product\create\ProductCreateService;
use src\auth\Auth;
use src\entities\cases\Cases;
use src\exception\BoResponseException;
use src\forms\CompositeFormHelper;
use src\forms\segment\SegmentBaggageForm;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use src\helpers\product\ProductQuoteHelper;
use src\helpers\quote\BaggageHelper;
use src\repositories\lead\LeadRepository;
use src\repositories\NotFoundException;
use src\services\parsingDump\BaggageService;
use src\services\parsingDump\ReservationService;
use src\services\quote\addQuote\AddQuoteManualService;
use src\services\quote\addQuote\guard\GdsByQuoteGuard;
use src\services\TransactionManager;
use webapi\src\request\BoRequestDataHelper;
use Yii;
use modules\flight\models\FlightQuote;
use modules\flight\models\search\FlightQuoteSearch;
use frontend\controllers\FController;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use modules\product\src\abac\ProductQuoteChangeAbacObject;
use modules\product\src\abac\dto\ProductQuoteChangeAbacDto;
use modules\product\src\abac\RelatedProductQuoteAbacObject;
use modules\product\src\abac\dto\RelatedProductQuoteAbacDto;
use src\repositories\cases\CasesRepository;
use src\access\EmployeeGroupAccess;
use src\helpers\setting\SettingHelper;

/**
 * FlightQuoteController implements the CRUD actions for FlightQuote model.
 *
 * @property FlightRepository $flightRepository
 * @property FlightQuoteManageService $flightQuoteManageService
 * @property ProductQuoteRepository $productQuoteRepository
 * @property FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository
 * @property LeadRepository $leadRepository
 * @property ProductCreateService $productCreateService
 * @property FlightManageService $flightManageService
 * @property ApiFlightQuoteSearchService $apiFlightQuoteSearchService
 * @property ReProtectionQuoteManualCreateService $reProtectionQuoteManualCreateService
 * @property ProductQuoteCloneService $productQuoteCloneService
 * @property VoluntaryQuoteManualCreateService $voluntaryQuoteManualCreateService
 * @property OrderRepository $orderRepository
 * @property VoluntaryRefundService $voluntaryRefundService
 * @property ProductQuoteChangeRepository $productQuoteChangeRepository
 * @property CasesRepository $casesRepository
 */
class FlightQuoteController extends FController
{
    /**
     * @var FlightRepository
     */
    private $flightRepository;
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
     * @var CasesRepository
     */
    private $casesRepository;
    /**
     * @var ApiFlightQuoteSearchService
     */
    private ApiFlightQuoteSearchService $apiFlightQuoteSearchService;
    private ReProtectionQuoteManualCreateService $reProtectionQuoteManualCreateService;
    private ProductQuoteCloneService $productQuoteCloneService;
    private VoluntaryQuoteManualCreateService $voluntaryQuoteManualCreateService;
    private OrderRepository $orderRepository;
    private VoluntaryRefundService $voluntaryRefundService;
    private ProductQuoteChangeRepository $productQuoteChangeRepository;

    /**
     * FlightQuoteController constructor.
     * @param $id
     * @param $module
     * @param FlightRepository $flightRepository
     * @param FlightQuoteManageService $flightQuoteManageService
     * @param ProductQuoteRepository $productQuoteRepository
     * @param FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository
     * @param LeadRepository $leadRepository
     * @param ProductCreateService $productCreateService
     * @param FlightManageService $flightManageService
     * @param ApiFlightQuoteSearchService $apiFlightQuoteSearchService
     * @param ReProtectionQuoteManualCreateService $reProtectionQuoteManualCreateService
     * @param ProductQuoteCloneService $productQuoteCloneService
     * @param VoluntaryQuoteManualCreateService $voluntaryQuoteManualCreateService
     * @param OrderRepository $orderRepository
     * @param VoluntaryRefundService $voluntaryRefundService
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        FlightRepository $flightRepository,
        FlightQuoteManageService $flightQuoteManageService,
        ProductQuoteRepository $productQuoteRepository,
        FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository,
        LeadRepository $leadRepository,
        ProductCreateService $productCreateService,
        FlightManageService $flightManageService,
        ApiFlightQuoteSearchService $apiFlightQuoteSearchService,
        ReProtectionQuoteManualCreateService $reProtectionQuoteManualCreateService,
        ProductQuoteCloneService $productQuoteCloneService,
        VoluntaryQuoteManualCreateService $voluntaryQuoteManualCreateService,
        OrderRepository $orderRepository,
        VoluntaryRefundService $voluntaryRefundService,
        ProductQuoteChangeRepository $productQuoteChangeRepository,
        CasesRepository $casesRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->flightRepository = $flightRepository;
        $this->flightQuoteManageService = $flightQuoteManageService;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->flightQuotePaxPriceRepository = $flightQuotePaxPriceRepository;
        $this->leadRepository = $leadRepository;
        $this->productCreateService = $productCreateService;
        $this->flightManageService = $flightManageService;
        $this->apiFlightQuoteSearchService = $apiFlightQuoteSearchService;
        $this->reProtectionQuoteManualCreateService = $reProtectionQuoteManualCreateService;
        $this->productQuoteCloneService = $productQuoteCloneService;
        $this->voluntaryQuoteManualCreateService = $voluntaryQuoteManualCreateService;
        $this->orderRepository = $orderRepository;
        $this->voluntaryRefundService = $voluntaryRefundService;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
        $this->casesRepository = $casesRepository;
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
            'access' => [
                'allowActions' => [
                    'ajax-quote-details',
                    'create-voluntary-quote-refund',
                    'create-voluntary-quote',
                    'add-change',
                    'refresh-voluntary-price',
                    'ajax-prepare-dump',
                    'save-voluntary-quote',
                    'create-re-protection-quote',
                    'ajax-save-re-protection',
                    'segment-baggage-validate',
                    'show-osd'
                ]
            ]
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

    public function actionShowOsd()
    {
        $id = Yii::$app->request->get('id');

        $data = FlightQuote::find()->select(['fq_origin_search_data'])->where(['fq_id' => $id])->asArray()->one();

        return json_decode($data['fq_origin_search_data'] ?? '');
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

            $dto = new FlightQuoteSearchDTO($flight, $gds);
            $quotes = \Yii::$app->cacheFile->get($flight->fl_request_hash_key);

            if ($quotes === false) {
                $quotes = $this->apiFlightQuoteSearchService->search($dto->getAsArray())['data'];
                if (!empty($quotes['results'])) {
                    \Yii::$app->cacheFile->set($flight->fl_request_hash_key, $quotes = FlightQuoteSearchHelper::formatQuoteData($quotes), 600);
                }
            }

            $form->load(Yii::$app->request->post() ?: Yii::$app->request->get());

            $viewData = FlightQuoteSearchHelper::getAirlineLocationInfo($quotes);
            $viewData['flightQuoteSearchDTO'] = $dto;

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

            if (empty($quotes['results'])) {
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
        $caseId = Yii::$app->request->get('case_id');
        $orderId = Yii::$app->request->get('order_id');
        $pqcId = Yii::$app->request->get('pqc_id');

        $productQuote = $this->productQuoteRepository->find($productQuoteId);

        $lead = $productQuote->pqProduct->prLead;

        if ($lead && $lead->isInTrash() && Auth::user()->isAgent()) {
            throw new ForbiddenHttpException('Access Denied for Agent');
        }

        if (!$lead) {
            if ($caseId && $orderId) {
                $case = $this->casesRepository->find($caseId);
                $order = $this->orderRepository->find($orderId);
                if (!$productQuote->relateParent) {
                    $productQuoteAbacDto = new ProductQuoteAbacDto($productQuote);
                    $productQuoteAbacDto->mapCaseAttributes($case);
                    $productQuoteAbacDto->mapOrderAttributes($order);
                    /** @abac $productQuoteAbacDto, ProductQuoteAbacObject::OBJ_PRODUCT_QUOTE, CasesAbacObject::ACTION_ACCESS_DETAILS, Product quote view details */
                    if (!Yii::$app->abac->can($productQuoteAbacDto, ProductQuoteAbacObject::OBJ_PRODUCT_QUOTE, ProductQuoteAbacObject::ACTION_ACCESS_DETAILS)) {
                        throw new ForbiddenHttpException('Access denied');
                    }
                } else {
                    if ($pqcId) {
                        $productQuoteChange = $this->productQuoteChangeRepository->find($pqcId);
                        $relatedProductQuoteAbacDto = new RelatedProductQuoteAbacDto($productQuote);
                        $relatedProductQuoteAbacDto->mapOrderAttributes($order);
                        $relatedProductQuoteAbacDto->mapProductQuoteChangeAttributes($productQuoteChange);
                        $relatedProductQuoteAbacDto->mapCaseAttributes($case);

                        /** @abac $relatedProductQuoteAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_ACCESS_DETAILS, ReProtection Quote View Details */
                        if (!Yii::$app->abac->can($relatedProductQuoteAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_ACCESS_DETAILS)) {
                            throw new ForbiddenHttpException('Access denied');
                        }
                    }
                }
            }
        }

        return $this->renderPartial('partial/_quote_view_details', [
            'productQuote' => $productQuote,
            'flightQuote' => FlightQuote::findByProductQuoteId($productQuote)
        ]);
    }

    public function actionAjaxOriginReprotectionQuotesDiff()
    {
        $originQuoteId = Yii::$app->request->get('origin-quote-id', 0);
        $reprotectionQuoteId = Yii::$app->request->get('reprotection-quote-id', 0);
        $caseId = Yii::$app->request->get('case_id', 0);
        $orderId = Yii::$app->request->get('order_id', 0);
        $pqcId = Yii::$app->request->get('pqc_id', 0);

        $originQuote = $this->productQuoteRepository->find($originQuoteId);
        $reprotectionQuote = $this->productQuoteRepository->find($reprotectionQuoteId);
        $case = $this->casesRepository->find($caseId);
        $order = $this->orderRepository->find($orderId);
        $productQuoteChange = $this->productQuoteChangeRepository->find($pqcId);

        $relatedProductQuoteAbacDto = new RelatedProductQuoteAbacDto($reprotectionQuote);
        $relatedProductQuoteAbacDto->mapOrderAttributes($order);
        $relatedProductQuoteAbacDto->mapProductQuoteChangeAttributes($productQuoteChange);
        $relatedProductQuoteAbacDto->mapCaseAttributes($case);

        /** @abac $relatedPrQtAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_SEND_SC_EMAIL, ReProtection View Difference */
        if (!Yii::$app->abac->can($relatedProductQuoteAbacDto, RelatedProductQuoteAbacObject::OBJ_RELATED_PRODUCT_QUOTE, RelatedProductQuoteAbacObject::ACTION_ACCESS_DIFF)) {
            throw new ForbiddenHttpException('Access denied');
        }

        $originQuoteDetailsHtml = $this->renderPartial('partial/_quote_view_details', [
            'productQuote' => $originQuote,
            'flightQuote' => FlightQuote::findByProductQuoteId($originQuote)
        ]);

        $reprotectionQuoteDetailsHtml = $this->renderPartial('partial/_quote_view_details', [
            'productQuote' => $reprotectionQuote,
            'flightQuote' => FlightQuote::findByProductQuoteId($reprotectionQuote)
        ]);

        return $this->renderAjax('partial/_quote_diff', [
            'originQuoteHtml' => $originQuoteDetailsHtml,
            'reprotectionQuoteHtml' => $reprotectionQuoteDetailsHtml
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

            FlightQuoteTicketIssuedService::generateTicketIssued($flightQuote);

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
            Yii::error(AppHelper::throwableLog($throwable), 'FlightQuoteController:increaseLimits');
        }
    }

    public function actionAddChange(): string
    {
        $addChangeForm = new AddChangeForm();
        $errors = [];
        try {
            if (!$addChangeForm->load(Yii::$app->request->get())) {
                throw new \RuntimeException('AddChangeForm not loaded');
            }
            if (!$addChangeForm->validate()) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($addChangeForm));
            }
        } catch (\Throwable $throwable) {
            return $throwable->getMessage();
        }

        $originProductQuote = ProductQuote::findOne(['pq_id' => $addChangeForm->origin_quote_id]);
        $case = Cases::findOne(['cs_id' => $addChangeForm->case_id]);
        $order = $this->orderRepository->find($addChangeForm->order_id);

        $productQuoteAbacDto = new ProductQuoteAbacDto($originProductQuote);
        $productQuoteAbacDto->mapCaseAttributes($case);
        $productQuoteAbacDto->mapOrderAttributes($order);

        /** @abac new ProductQuoteAbacDto($originProductQuote), ProductQuoteAbacObject::OBJ_PRODUCT_QUOTE, ProductQuoteAbacObject::ACTION_CREATE_CHANGE, Product quote add change */
        if (!Yii::$app->abac->can($productQuoteAbacDto, ProductQuoteAbacObject::OBJ_PRODUCT_QUOTE, ProductQuoteAbacObject::ACTION_CREATE_CHANGE)) {
            throw new ForbiddenHttpException('Access denied');
        }

        if (Yii::$app->request->isPost) {
            try {
                if ($addChangeForm->load(Yii::$app->request->post()) && $addChangeForm->validate()) {
                    if ($addChangeForm->type_id === ProductQuoteChange::TYPE_VOLUNTARY_EXCHANGE) {
                        if (!$case) {
                            throw new \RuntimeException('Case not found by ID(' . $addChangeForm->case_id . ')');
                        }
                        if (!$originProductQuote) {
                            throw new \RuntimeException('OriginProductQuote not found by ID(' . $addChangeForm->origin_quote_id . ')');
                        }
                        $boPrepareService = new VoluntaryExchangeBOPrepareService($case->project, $originProductQuote);
                        $productQuoteChange = ProductQuoteChange::createVoluntaryExchange(
                            $addChangeForm->origin_quote_id,
                            $addChangeForm->case_id,
                            false
                        );
                    } else {
                        $productQuoteChange = ProductQuoteChange::createReProtection(
                            $addChangeForm->origin_quote_id,
                            $addChangeForm->case_id,
                            false
                        );
                    }
                    $this->productQuoteChangeRepository->save($productQuoteChange);

                    return "<script> $('#modal-sm').modal('hide'); pjaxReload({container: '#pjax-case-orders'});</script>";
                }
            } catch (\Throwable $throwable) {
                \Yii::warning(AppHelper::throwableLog($throwable), 'FlightQuoteController:actionAddChange:Warning');
                $errors[] = $throwable->getMessage();
            }
        }

        $params = [
            'addChangeForm' => $addChangeForm,
            'errors' => $errors,
        ];
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('partial/_add_change', $params);
        }
        return $this->render('partial/_add_change', $params);
    }

    public function actionCreateVoluntaryQuote(): string
    {
        $flightId = Yii::$app->request->get('flight_id', 0);
        $caseId = Yii::$app->request->get('case_id', 0);
        $originQuoteId = Yii::$app->request->get('origin_quote_id', 0);
        $changeId = Yii::$app->request->get('change_id', 0);

        $flight = $this->flightRepository->find($flightId);
        $getParams = Yii::$app->request->get();

        $productQuoteChange = ProductQuoteChange::findOne(['pqc_id' => $changeId]);

        $quoteChangeRelations = $productQuoteChange->productQuoteChangeRelations;
        $pqcAbacDto = new ProductQuoteChangeAbacDto($productQuoteChange);

        if ($quoteChangeRelations) {
            $quotesCnt = 0;
            foreach ($quoteChangeRelations as $quoteRelation) {
                if (in_array($quoteRelation->pqcrPq->pq_status_id, SettingHelper::getExchangeQuoteConfirmStatusList())) {
                    $quotesCnt++;
                }
            }
            $pqcAbacDto->maxConfirmableQuotesCnt = $quotesCnt;
        }

        /** @abac new $pqcAbacDto, ProductQuoteChangeAbacObject::OBJ_PRODUCT_QUOTE_CHANGE, ProductQuoteChangeAbacObject::ACTION_CREATE_VOLUNTARY_QUOTE, Act Flight Create Voluntary quote*/
        if (!Yii::$app->abac->can($pqcAbacDto, ProductQuoteChangeAbacObject::OBJ_PRODUCT_QUOTE_CHANGE, ProductQuoteChangeAbacObject::ACTION_CREATE_VOLUNTARY_QUOTE)) {
            throw new ForbiddenHttpException('You do not have access to perform this action.');
        }

        try {
            if (!$case = Cases::findOne(['cs_id' => $caseId])) {
                throw new \RuntimeException('Case not found');
            }
            if (!$originProductQuote = ProductQuote::findOne(['pq_id' => $originQuoteId])) {
                throw new \RuntimeException('OriginProductQuote not found');
            }
            if ((!$originProductQuote->flightQuote || !$flightQuotePaxPrices = $originProductQuote->flightQuote->flightQuotePaxPrices)) {
                throw new \RuntimeException('Sorry, Voluntary quote could not be created because originalQuote does not pricing');
            }
            if (!$productQuoteChange) {
                throw new \RuntimeException('ProductQuoteChange not found');
            }

            $boPrepareService = new VoluntaryExchangeBOPrepareService($case->project, $originProductQuote);
            try {
                $boPrepareService->fill();
            } catch (\Throwable $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $getParams);
                \Yii::warning($message, 'FlightQuoteController:actionCreateVoluntaryQuote:VoluntaryExchangeBOPrepareService');
            }

            $voluntaryExchangeBOService = new VoluntaryExchangeBOService($boPrepareService);
            try {
                $voluntaryExchangeBOService->requestProcessing();
            } catch (\Throwable $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $getParams);
                \Yii::warning($message, 'FlightQuoteController:actionCreateVoluntaryQuote:BoGetExchangeData');
            }
//            Yii::info(var_export($voluntaryExchangeBOService->getResult(), true), 'info\*');//'FlightQuoteController:actionCreateVoluntaryQuote:LOG');

            $productQuoteChange->pqc_data_json = JsonHelper::encode($voluntaryExchangeBOService->getResult());
            if (!empty($productQuoteChange->pqc_data_json)) {
                $this->productQuoteChangeRepository->save($productQuoteChange);
            }

            $form = new VoluntaryQuoteCreateForm(Auth::id(), $flight, true, $voluntaryExchangeBOService->getServiceFeeAmount());
            $form->setCustomerPackage($voluntaryExchangeBOService->getCustomerPackage());
            $form->setServiceFeeCurrency($voluntaryExchangeBOService->getServiceFeeCurrency());
            $form->setExpirationDate(FlightQuoteHelper::getExpirationDate($originProductQuote->flightQuote));
        } catch (\RuntimeException | \DomainException $exception) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($exception), $getParams);
            Yii::warning($message, 'FlightQuoteController:actionCreateVoluntaryQuote:Exception');
            return $exception->getMessage();
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $getParams);
            Yii::error($message, 'FlightQuoteController:actionCreateVoluntaryQuote:Throwable');
            return $throwable->getMessage();
        }

        $params = [
            'createQuoteForm' => $form,
            'flight' => $flight,
            'flightQuotePaxPrices' => $flightQuotePaxPrices,
            'originProductQuote' => $originProductQuote,
            'changeId' => $changeId,
            'originQuoteId' => $originQuoteId,
            'caseId' => $caseId,
        ];
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('partial/_add_voluntary_quote_manual', $params);
        }
        return $this->render('partial/_add_voluntary_quote_manual', $params);
    }

    public function actionSaveVoluntaryQuote()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $response = ['message' => '', 'status' => 0];
            $flightId = Yii::$app->request->get('flight_id', 0);

            $caseId = Yii::$app->request->post('case_id', 0);
            $originQuoteId = Yii::$app->request->post('origin_quote_id', 0);
            $changeId = Yii::$app->request->post('change_id', 0);
            $post = Yii::$app->request->post();

            try {
                $flight = $this->flightRepository->find($flightId);

                if (!$case = Cases::findOne(['cs_id' => $caseId])) {
                    throw new \RuntimeException('Case not found');
                }
                if (!$originProductQuote = ProductQuote::findOne(['pq_id' => $originQuoteId])) {
                    throw new \RuntimeException('OriginProductQuote not found');
                }
                if (!$productQuoteChange = ProductQuoteChange::findOne(['pqc_id' => $changeId])) {
                    throw new \RuntimeException('ProductQuoteChange not found');
                }

                $form = new VoluntaryQuoteCreateForm(Auth::id(), $flight, false);
                if (!$form->load(Yii::$app->request->post())) {
                    throw new \RuntimeException('VoluntaryQuoteCreateForm not loaded');
                }
                if (!$form->validate()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($form));
                }

                $gds = GdsByQuoteGuard::guard($form->gds);
                $itinerary = $segments = [];
                $reservationService = new ReservationService($gds);
                $reservationService->parseReservation($form->reservationDump, true, $itinerary);
                if ($reservationService->parseStatus && $reservationService->parseResult) {
                    $segments = $reservationService->parseResult;
                }

                [$pastSegmentsItinerary, $pastSegments, $totalPastTrips] = AddQuoteManualService::getPastSegmentsByProductQuote($gds, $originProductQuote);

                $form->keyTripList = $this->updateKeyTripList($form, $totalPastTrips);
                $form->itinerary = array_merge($pastSegmentsItinerary, $itinerary);
                $mergedSegments = array_merge($pastSegments, $segments);
                $totalTrips = count(explode(',', $form->keyTripList));

                $updatedSegmentTripFormData = AddQuoteManualService::updateSegmentTripFormsData($form, $totalTrips, $pastSegmentsItinerary);
                $form->setSegmentTripFormsData($updatedSegmentTripFormData);

                $userId = Auth::id();
                $flightQuote = Yii::createObject(TransactionManager::class)
                    ->wrap(function () use ($flight, $originProductQuote, $form, $userId, $mergedSegments, $changeId) {
                        return $this->voluntaryQuoteManualCreateService->createProcessing(
                            $flight,
                            $originProductQuote,
                            $form,
                            $userId,
                            $mergedSegments,
                            $changeId,
                        );
                    });

                $response['message'] = 'Success. FlightQuote ID(' . $flightQuote->getId() . ') created';
                $response['status'] = 1;
            } catch (\RuntimeException | \DomainException $exception) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($exception), ['post' => $post]);
                Yii::warning($message, 'FlightQuoteController:actionSaveVoluntaryQuote:Exception');
                $response['message'] = VarDumper::dumpAsString($exception->getMessage());
            } catch (\Throwable $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['post' => $post]);
                Yii::error($message, 'FlightQuoteController:actionSaveVoluntaryQuote:Throwable');
                $response['message'] = 'Internal Server Error';
            }
            return $response;
        }
        throw new BadRequestHttpException();
    }

    public function actionRefreshVoluntaryPrice(): array
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $response = ['message' => '', 'status' => 0, 'data' => ''];

            $flightId = Yii::$app->request->get('flight_id', 0);
            $originQuoteId = Yii::$app->request->get('origin_quote_id', 0);

            try {
                $flight = $this->flightRepository->find($flightId);
                if (!$originProductQuote = ProductQuote::findOne(['pq_id' => $originQuoteId])) {
                    throw new \RuntimeException('OriginProductQuote not found');
                }

                $createQuoteForm = new VoluntaryQuoteCreateForm(Auth::id(), $flight, false);
                if (!$createQuoteForm->load(Yii::$app->request->post())) {
                    throw new \RuntimeException('VoluntaryQuoteCreateForm not loaded');
                }

                $createQuoteForm = FlightQuotePaxPriceHelper::refreshChangeQuotePrice($createQuoteForm);

                $response['data'] = $this->renderAjax('partial/_flight_quote_pax_price', [
                    'originProductQuote' => $originProductQuote,
                    'createQuoteForm' => $createQuoteForm,
                    'form' => new ActiveForm(),
                ]);
                $response['message'] = 'Success';
                $response['status'] = 1;
            } catch (\RuntimeException | \DomainException $exception) {
                Yii::info(AppHelper::throwableLog($exception), 'FlightQuoteController:ActionRefreshVoluntaryPrice:Exception');
                $response['message'] = VarDumper::dumpAsString($exception->getMessage());
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableLog($throwable), 'FlightQuoteController:ActionRefreshVoluntaryPrice:throwable');
                $response['message'] = 'Internal Server Error';
            }
            return $response;
        }
        throw new BadRequestHttpException();
    }

    public function actionCreateReProtectionQuote(): string
    {
        $flightId = Yii::$app->request->get('flight_id', 0);
        $flight = $this->flightRepository->find($flightId);
        $changeId = Yii::$app->request->get('change_id', 0);

        try {
            if (!$originProductQuote = $this->reProtectionQuoteManualCreateService::getOriginQuoteByFlight($flightId)) {
                throw new \RuntimeException('OriginProductQuote not found');
            }
            if ((!$originProductQuote->flightQuote || !$flightQuotePaxPrices = $originProductQuote->flightQuote->flightQuotePaxPrices)) {
                throw new \RuntimeException('Sorry, reProtection quote could not be created because originalQuote does not pricing');
            }
            if (!$productQuoteChange = ProductQuoteChange::findOne(['pqc_id' => $changeId])) {
                throw new \RuntimeException('ProductQuoteChange not found');
            }

            $quoteChangeRelations = $productQuoteChange->productQuoteChangeRelations;

            $pqcAbacDto = new ProductQuoteChangeAbacDto($productQuoteChange);

            if ($quoteChangeRelations) {
                $quotesCnt = 0;
                foreach ($quoteChangeRelations as $quoteRelation) {
                    if (in_array($quoteRelation->pqcrPq->pq_status_id, SettingHelper::getExchangeQuoteConfirmStatusList())) {
                        $quotesCnt++;
                    }
                }
                $pqcAbacDto->maxConfirmableQuotesCnt = $quotesCnt;
            }

            /** @abac $pqcAbacDto, ProductQuoteChangeAbacObject::OBJ_PRODUCT_QUOTE_CHANGE, ProductQuoteChangeAbacObject::ACTION_CREATE_RE_PROTECTION_QUOTE, Act Flight Create Reprotection quote from dump*/
            if (!Yii::$app->abac->can($pqcAbacDto, ProductQuoteChangeAbacObject::OBJ_PRODUCT_QUOTE_CHANGE, ProductQuoteChangeAbacObject::ACTION_CREATE_RE_PROTECTION_QUOTE)) {
                throw new ForbiddenHttpException('You do not have access to perform this action.');
            }

            $form = new ReProtectionQuoteCreateForm(Auth::id(), $flightId);
            $form->setExpirationDate(FlightQuoteHelper::getExpirationDate($originProductQuote->flightQuote));
        } catch (\Throwable $throwable) {
            Yii::warning(AppHelper::throwableLog($throwable), 'FlightQuoteController:actionCreateReProtectionQuote:Throwable');
            return $throwable->getMessage();
        }

        $params = [
            'createQuoteForm' => $form,
            'flight' => $flight,
            'flightQuotePaxPrices' => $flightQuotePaxPrices,
            'originProductQuote' => $originProductQuote,
            'changeId' => $changeId,
        ];
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('partial/_add_re_protection_manual', $params);
        }
        return $this->render('partial/_add_re_protection_manual', $params);
    }

    public function updateKeyTripList($form, $totalPastTrips)
    {
        if ($totalPastTrips > 0) {
            while ($totalPastTrips > 0) {
                $receivedTrips = explode(',', $form->keyTripList);
                $addTrip = count($receivedTrips) + 1;
                $form->keyTripList = $form->keyTripList . ',' . $addTrip;
                $totalPastTrips--;
            }
        }

        return $form->keyTripList;
    }

    public function actionAjaxSaveReProtection(): array
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $response = ['message' => '', 'status' => 0];
            $flightId = Yii::$app->request->get('flight_id', 0);
            $changeId = Yii::$app->request->get('change_id', 0);

            try {
                $flight = $this->flightRepository->find($flightId);

                if (!$originProductQuote = $this->reProtectionQuoteManualCreateService::getOriginQuoteByFlight($flightId)) {
                    throw new \RuntimeException('OriginProductQuote not found');
                }
                if ((!$originProductQuote->flightQuote || !$flightQuotePaxPrices = $originProductQuote->flightQuote->flightQuotePaxPrices)) {
                    throw new \RuntimeException('Sorry, reProtection quote could not be created because originalQuote does not pricing');
                }
                if (!($productQuoteChange = ProductQuoteChange::findOne(['pqc_id' => $changeId])) || !$productQuoteChange->isTypeReProtection()) {
                    throw new \RuntimeException('ProductQuoteChange is not type ReProtection');
                }

                $quoteChangeRelations = $productQuoteChange->productQuoteChangeRelations;
                $pqcAbacDto = new ProductQuoteChangeAbacDto($productQuoteChange);

                if ($quoteChangeRelations) {
                    $quotesCnt = 0;
                    foreach ($quoteChangeRelations as $quoteRelation) {
                        if (in_array($quoteRelation->pqcrPq->pq_status_id, SettingHelper::getExchangeQuoteConfirmStatusList())) {
                            $quotesCnt++;
                        }
                    }
                    $pqcAbacDto->maxConfirmableQuotesCnt = $quotesCnt;
                }

                /** @abac $pqcAbacDto, ProductQuoteChangeAbacObject::OBJ_PRODUCT_QUOTE_CHANGE, ProductQuoteChangeAbacObject::ACTION_CREATE_RE_PROTECTION_QUOTE, Act Flight Create Reprotection quote from dump*/
                if (!Yii::$app->abac->can($pqcAbacDto, ProductQuoteChangeAbacObject::OBJ_PRODUCT_QUOTE_CHANGE, ProductQuoteChangeAbacObject::ACTION_CREATE_RE_PROTECTION_QUOTE)) {
                    throw new ForbiddenHttpException('You do not have access to perform this action.');
                }

                $form = new ReProtectionQuoteCreateForm(Auth::id(), $flightId);
                if (!$form->load(Yii::$app->request->post())) {
                    throw new \RuntimeException('ReProtectionQuoteCreateForm not loaded');
                }
                if (!$form->validate()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($form));
                }

                $gds = GdsByQuoteGuard::guard($form->gds);
                $itinerary = $segments = [];
                $reservationService = new ReservationService($gds);
                $reservationService->parseReservation($form->reservationDump, true, $itinerary);
                if ($reservationService->parseStatus && $reservationService->parseResult) {
                    $segments = $reservationService->parseResult;
                }

                [$pastSegmentsItinerary, $pastSegments, $totalPastTrips] = AddQuoteManualService::getPastSegmentsByProductQuote($gds, $originProductQuote);

                $form->keyTripList = $this->updateKeyTripList($form, $totalPastTrips);
                $form->itinerary = array_merge($pastSegmentsItinerary, $itinerary);
                $mergedSegments = array_merge($pastSegments, $segments);
                $totalTrips = count(explode(',', $form->keyTripList));

                $updatedSegmentTripFormData = AddQuoteManualService::updateSegmentTripFormsData($form, $totalTrips, $pastSegmentsItinerary);
                $form->setSegmentTripFormsData($updatedSegmentTripFormData);

                $userId = Auth::id();
                $flightQuote = Yii::createObject(TransactionManager::class)
                    ->wrap(function () use ($flight, $originProductQuote, $form, $userId, $mergedSegments, $changeId) {
                        return $this->reProtectionQuoteManualCreateService->createReProtectionManual(
                            $flight,
                            $originProductQuote,
                            $form,
                            $userId,
                            $mergedSegments,
                            $changeId,
                        );
                    });

                $response['message'] = 'Success. FlightQuote ID(' . $flightQuote->getId() . ') created';
                $response['status'] = 1;
            } catch (\RuntimeException | \DomainException $exception) {
                Yii::warning(AppHelper::throwableLog($exception, true), 'FlightQuoteController:actionAjaxSaveReProtection:Exception');
                $response['message'] = VarDumper::dumpAsString($exception->getMessage());
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableLog($throwable, true), 'FlightQuoteController:actionAjaxSaveReProtection:throwable');
                $response['message'] = 'Internal Server Error';
            }
            return $response;
        }
        throw new BadRequestHttpException();
    }

    public function actionAjaxPrepareDump(): array
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $response = ['message' => '', 'status' => 0, 'reservation_dump' => [], 'segments' => '', 'key_trip_list' => ''];
            $originSegmentsBaggage = [];
            $defaultBaggage = null;
            $withBaggageForm = true;
            $flightId = Yii::$app->request->get('flight_id', 0);
            $tripType = (int) Yii::$app->request->post('tripType', 0);
            $changeId = Yii::$app->request->get('change_id');

            try {
                if (!$dump = Yii::$app->request->post('reservationDump')) {
                    throw new \RuntimeException('Param dump is required');
                }
                if (!$gds = Yii::$app->request->post('gds')) {
                    throw new \RuntimeException('Param gds is required');
                }
                if (!$originProductQuote = $this->reProtectionQuoteManualCreateService::getOriginQuoteByFlight($flightId)) {
                    throw new \RuntimeException('OriginProductQuote not found');
                }
                if (!$originProductQuote->flightQuote) {
                    throw new \RuntimeException('Origin FlightQuote not found');
                }

                if ($flightQuoteSegments = $originProductQuote->flightQuote->flightQuoteSegments) {
                    foreach ($flightQuoteSegments as $originSegment) {
                        $key = $originSegment->fqs_departure_airport_iata . $originSegment->fqs_arrival_airport_iata;
                        $originSegmentsBaggage[$key] = BaggageService::generateBaggageFromFlightSegment($originSegment);
                    }
                }

                $gds = GdsByQuoteGuard::guard($gds);
                $itinerary = [];
                $reservationService = new ReservationService($gds);
                $reservationService->parseReservation($dump, true, $itinerary);
                if (!$reservationService->parseStatus || empty($reservationService->parseResult)) {
                    throw new \RuntimeException('Parsing dump is failed');
                }

                $segments = $reservationService->parseResult;
                $trips = [];
                $tripIndex = 1;

                foreach ($segments as $key => $segment) {
                    $keyIata = $segment['segmentIata'];
                    if (array_key_exists($keyIata, $originSegmentsBaggage)) {
                        $segments[$key]['baggage'] = $originSegmentsBaggage[$keyIata];
                        $defaultBaggage = $defaultBaggage ?? $originSegmentsBaggage[$keyIata];
                    }

                    if ($key === 0) {
                        $trips[$tripIndex]['duration'] = $segment['flightDuration'];
                        $segment['tripIndex'] = $tripIndex;
                        $trips[$tripIndex]['segments'][] = $segment;
                    } else {
                        $prevSegment = $segments[$key - 1] ?? $segments[$key];
                        $isNextTrip = false;

                        try {
                            $isNextTrip = FlightQuoteHelper::isNextTrip($prevSegment, $segment);
                        } catch (\Throwable $throwable) {
                            \Yii::warning(
                                AppHelper::throwableLog($throwable),
                                'FlightQuoteController:actionAjaxPrepareDump:isNextTrip'
                            );
                        }

                        if ($isNextTrip) {
                            ++$tripIndex;
                            $trips[$tripIndex]['duration'] = $segment['flightDuration'] + $segment['layoverDuration'];
                        } else {
                            $trips[$tripIndex]['duration'] += $segment['flightDuration'] + $segment['layoverDuration'];
                        }
                        $segment['tripIndex'] = $tripIndex;
                        $trips[$tripIndex]['segments'][] = $segment;
                    }
                }

                if (empty($trips)) {
                    throw new \RuntimeException('Trips processing failed');
                }

                $response['segments'] = $this->renderAjax('partial/_segment_rows', [
                    'trips' => $trips,
                    'segments' => $segments,
                    'sourceHeight' => BaggageHelper::getBaggageHeightValuesCombine(),
                    'sourceWeight' => BaggageHelper::getBaggageWeightValuesCombine(),
                    'defaultBaggage' => $defaultBaggage,
                    'withBaggageForm' => $withBaggageForm,
                ]);

                $response['key_trip_list'] = implode(',', array_keys($trips));

                $response['message'] = 'Success';
                $response['status'] = 1;
            } catch (\RuntimeException | \DomainException $exception) {
                $response['message'] = VarDumper::dumpAsString($exception->getMessage());
                Yii::warning(AppHelper::throwableLog($exception), 'FlightQuoteController:actionAjaxPrepareDump:exception');
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableLog($throwable), 'FlightQuoteController:actionAjaxPrepareDump:throwable');
                $response['message'] = 'Internal Server Error';
            }
            return $response;
        }
        throw new BadRequestHttpException();
    }

    public function actionSegmentBaggageValidate(string $iata): ?array
    {
        if (Yii::$app->request->isAjax) {
            $segmentBaggageForm = new SegmentBaggageForm($iata);
            $formName = $segmentBaggageForm->formName();
            $post = Yii::$app->request->post();

            $models = [];
            foreach ($post[$formName]['baggageData'] as $key => $value) {
                $model = new SegmentBaggageForm($iata);
                $model->setAttributes($value);
                $models[$key] = $model;
            }

            Model::loadMultiple($models, Yii::$app->request->post());
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validateMultiple($models);
        }
        throw new BadRequestHttpException('Not found POST request');
    }

    public function actionCreateVoluntaryQuoteRefund()
    {
        if (Yii::$app->request->isPjax) {
            $form = new VoluntaryRefundCreateForm();
            if ($form->load(Yii::$app->request->post()) && $form->validate()) {
                try {
                    $productQuote = $this->productQuoteRepository->find($form->originProductQuoteId);
                    $order = $this->orderRepository->find($form->orderId);

                    $this->voluntaryRefundService->createManual($order, $form->caseId, $productQuote, $form);

                    return "<script>$('#modal-lg').modal('hide');createNotify('Success', 'Refund created', 'success');pjaxReload({container: '#pjax-case-orders'})</script>";
                } catch (NotFoundException | \RuntimeException | \DomainException $e) {
                    $form->disableReadOnlyAllFields();
                    $form->addError('general', $e->getMessage());
                } catch (\Throwable $e) {
                    $form->addError('general', 'Server error, check system logs');
                    Yii::error(AppHelper::throwableLog($e, true), 'FlightQuoteController::actionCreateVoluntaryQuoteRefund::refundCreation::Throwable');
                }
            }
            return $this->renderAjax('partial/_voluntary_refund_create', [
                'message' => '',
                'errors' => [],
                'refundForm' => $form
            ]);
        }

        if (Yii::$app->request->isAjax) {
            $flightQuoteId = Yii::$app->request->get('flight_quote_id');
            $projectId = Yii::$app->request->get('project_id');
            $originProductQuoteId = Yii::$app->request->get('origin_product_quote_id');
            $orderId = Yii::$app->request->get('order_id');
            $caseId = Yii::$app->request->get('case_id');

            $productQuote = $this->productQuoteRepository->find($originProductQuoteId);

            $case = $this->casesRepository->find($caseId);
            $order = $this->orderRepository->find($orderId);

            $productQuoteAbacDto = new ProductQuoteAbacDto($productQuote);
            $productQuoteAbacDto->mapCaseAttributes($case);
            $productQuoteAbacDto->mapOrderAttributes($order);

            /** @abac new ProductQuoteAbacDto($model), ProductQuoteAbacObject::OBJ_PRODUCT_QUOTE, ProductQuoteAbacObject::ACTION_CREATE_VOL_REFUND, Create Voluntary Quote Refund */
            if (!Yii::$app->abac->can($productQuoteAbacDto, ProductQuoteAbacObject::OBJ_PRODUCT_QUOTE, ProductQuoteAbacObject::ACTION_CREATE_VOL_REFUND)) {
                throw new ForbiddenHttpException('Access denied');
            }

            $message = '';
            $errors = [];

            try {
                if (!$project = Project::findOne(['id' => $projectId])) {
                    throw new NotFoundException('Project not found');
                }

                if (!$flightQuoteFlight = FlightQuoteFlight::findOne(['fqf_fq_id' => $flightQuoteId])) {
                    throw new NotFoundException('BookingId not found');
                }

                if (!$originProductQuote = ProductQuote::findOne(['pq_id' => $originProductQuoteId])) {
                    throw new \RuntimeException('OriginProductQuote not found');
                }
                if (!$originProductQuote->flightQuote) {
                    throw new \RuntimeException('Sorry, Refund quote could not be created because originalQuote does not find a flight');
                }

                $form = new VoluntaryRefundCreateForm();
                $form->setExpirationDate(FlightQuoteHelper::getExpirationDate($originProductQuote->flightQuote));

                $boDataRequest = BoRequestDataHelper::getRequestDataForVoluntaryRefundData($project->api_key, $flightQuoteFlight->fqf_booking_id);
                $result = BackOffice::voluntaryRefund($boDataRequest, 'flight-request/get-refund-data');

                if (!empty($result['status']) && mb_strtolower($result['status']) === 'failed') {
                    throw new BoResponseException($result['errors'] ? ErrorsToStringHelper::extractFromGetErrors($result['errors']) : $result['message']);
                }

                if (empty($result['refund']['tickets'])) {
                    throw new \DomainException('Tickets not found');
                }

                $form->load($result);
                $form->bookingId = $flightQuoteFlight->fqf_booking_id;
                $form->originProductQuoteId = $originProductQuoteId;
                $form->orderId = $orderId;
                $form->caseId = $caseId;
                $form->originData = $result;

                if (!$form->airlineAllow && empty($form->getRefundForm()->currency)) {
                    $form->getRefundForm()->currency = Currency::getDefaultCurrencyCodeByDb();
//                    throw new \RuntimeException('Refund not allowed by Airline');
                }

                $refundForm = $form->getRefundForm();

                if ($refundForm && !$refundForm->getTicketForms() && !empty($result['refund']['tickets'])) {
                    foreach ($result['refund']['tickets'] as $ticket) {
                        $ticketForm = new TicketForm();
                        $ticketForm->number = $ticket['number'] ?? '';
                        $refundForm->setTicketForm($ticketForm);
                    }
                    $form->disableReadOnlyAllFields();
                    Yii::$app->getSession()->setFlash('warning', 'Not all data received from BO');
                }
            } catch (\DomainException | NotFoundException | \RuntimeException $e) {
                $message = $e->getMessage();
            } catch (\Throwable $e) {
                $message = 'Internal Server Error';
                Yii::error(AppHelper::throwableLog($e, true), 'FlightQuoteController::actionCreateVoluntaryQuoteRefund::Throwable');
            }

            return $this->renderAjax('partial/_voluntary_refund_create', [
                'message' => $message,
                'errors' => $errors,
                'refundForm' => $form
            ]);
        }
        throw new BadRequestHttpException('Method not allowed');
    }
}
