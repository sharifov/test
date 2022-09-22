<?php

namespace frontend\controllers;

use common\components\BackOffice;
use common\components\bootstrap4\activeForm\ActiveForm;
use common\helpers\LogHelper;
use common\models\CaseSale;
use common\models\Project;
use common\models\search\SaleSearch;
use modules\cases\src\abac\saleList\SaleListAbacObject;
use modules\cases\src\entities\caseSale\CancelSaleReason;
use modules\featureFlag\FFlag;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use src\auth\Auth;
use src\entities\cases\Cases;
use src\exception\BoResponseException;
use src\forms\caseSale\CaseSaleCancelForm;
use src\helpers\app\AppHelper;
use src\model\caseOrder\entity\CaseOrder;
use src\model\saleTicket\useCase\create\SaleTicketService;
use src\services\cases\CasesSaleService;
use src\services\caseSale\FareRulesService;
use Yii;
use yii\base\Exception;
use yii\data\ArrayDataProvider;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * @property CasesSaleService $casesSaleService
 * @property SaleTicketService $saleTicketService
 */
class SaleController extends FController
{
    private $casesSaleService;
    private FareRulesService $fareRulesService;
    private SaleTicketService $saleTicketService;

    /**
     * SaleController constructor.
     * @param $id
     * @param $module
     * @param CasesSaleService $casesSaleService
     * @param FareRulesService $fareRulesService
     * @param SaleTicketService $saleTicketService
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        CasesSaleService $casesSaleService,
        FareRulesService $fareRulesService,
        SaleTicketService $saleTicketService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->casesSaleService = $casesSaleService;
        $this->fareRulesService = $fareRulesService;
        $this->saleTicketService = $saleTicketService;
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
        $withFareRules = (int)Yii::$app->request->get('wfr', 0);
        $saleData = [];

        try {
            $arr = explode('|', base64_decode($hash));
            $id = (int)($arr[1] ?? 0);

            $saleData = $this->casesSaleService->detailRequestToBackOffice($id, $withFareRules);
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable), 'SaleController:actionView:ErrorBoRequest');
        }

        if (!count($saleData)) {
            throw new BadRequestHttpException('Error. Broken data from BackOffice. ');
        }

        $result = [
            'csId' => 0,
            'data' => $saleData,
            'additionalData' => [
                'hash' => $hash,
                'withFareRules' => $withFareRules,
            ],
            'disableMasking' => false
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
                    if ($order = Order::findOne(['or_sale_id' => $saleId])) {
                        if ($caseOrder = CaseOrder::findOne(['co_order_id' => $order->getId(), 'co_case_id' => $csId])) {
                            $caseOrder->delete();
                        }
                    }
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

    public function actionPrepareResendTickets($caseId, $caseSaleId)
    {
        try {
            if (!Yii::$app->request->isAjax) {
                throw new BadRequestHttpException();
            }
            $case = Cases::findOne(['cs_id' => $caseId]);
            if (!$case) {
                throw new \DomainException('Not found case. Id (' . $caseId . ')');
            }

            if (!Auth::can('cases/update', ['case' => $case])) {
                throw new ForbiddenHttpException('Access denied.');
            }

            $caseSale = CaseSale::findOne(['css_cs_id' => $caseId, 'css_sale_id' => $caseSaleId]);
            if (!$caseSale) {
                throw new \DomainException('Not found case sale. Id (' . $caseSaleId . ')');
            }

            return $this->renderAjax('/sale/partial/_sale_resend_tickets', [
                'caseSale' => $caseSale,
            ]);
        } catch (\Throwable $throwable) {
            return $throwable->getMessage();
        }
    }

    public function actionResendTickets()
    {
        $result = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            if (!Yii::$app->request->isAjax) {
                throw new BadRequestHttpException();
            }
            $caseId = \Yii::$app->request->post('caseId');
            $caseSaleId = \Yii::$app->request->post('caseSaleId');
            $emails = \Yii::$app->request->post('emails');

            if (!is_array($emails) || count($emails) == 0) {
                throw new \DomainException('Emails not found');
            }

            $case = Cases::findOne(['cs_id' => $caseId]);
            if (!$case) {
                throw new \DomainException('Not found case. Id (' . $caseId . ')');
            }

            if (!Auth::can('cases/update', ['case' => $case])) {
                throw new ForbiddenHttpException('Access denied.');
            }

            $caseSale = CaseSale::findOne(['css_cs_id' => $caseId, 'css_sale_id' => $caseSaleId]);
            if (!$caseSale) {
                throw new \DomainException('Not found case sale. Id (' . $caseSaleId . ')');
            }

            $projectId = $case->cs_project_id;
            $project = Project::findOne($projectId);
            if (!$project) {
                throw new \DomainException('Not found Project. Id ' . $projectId);
            }
            if (!$project->api_key) {
                throw new \DomainException('Not found API KEY. Project. Id ' . $projectId);
            }

            $confNumber = ArrayHelper::getValue($caseSale->getSaleDataDecoded(), 'bookingId');
            $data = [
                'apiKey' => $project->api_key,
                'bookingId' => $confNumber !== $caseSale->css_sale_book_id ? $confNumber : $caseSale->css_sale_book_id,
                'emails' => $emails,
                'visibilityType' => 'all',
            ];
            $host = Yii::$app->params['backOffice']['urlV3'];
            $responseBO = BackOffice::sendRequest2('flight-request/resend-tickets', $data, 'POST', 120, $host);
            $data['emails'] = LogHelper::hidePersonalData($data['emails'], array_keys($emails));

            if (!$responseBO->isOk) {
                Yii::error([
                    'message' => 'BO response error.',
                    'response' => VarDumper::dumpAsString($responseBO->content),
                    'data' => $data,
                ], 'SaleController:resendTickets');
                throw new \RuntimeException('Resend tickets BO request error. ' . VarDumper::dumpAsString($responseBO->content));
            }

            $responseData = $responseBO->data;

            if (empty($responseData['status'])) {
                Yii::error([
                    'message' => 'BO response error. Not found Status',
                    'response' => VarDumper::dumpAsString($responseData),
                    'data' => $data,
                ], 'SaleController:resendTickets');
                throw new \DomainException('Undefined BO response. Not found Status');
            }

            if (!in_array($responseData['status'], ['Success', 'Failed'], false)) {
                Yii::error([
                    'message' => 'BO response undefined status.',
                    'response' => VarDumper::dumpAsString($responseData),
                    'data' => $data,
                ], 'SaleController:resendTickets');
                throw new \DomainException('Undefined BO response Status');
            }

            if (!empty($responseData['errors'])) {
                $errors = '';
                foreach ($responseData['errors'] as $error) {
                    if (is_array($error)) {
                        $errors .= implode('; ', $error);
                    } else {
                        $errors .= $error . '; ';
                    }
                }
                Yii::error([
                    'message' => 'BO response error.',
                    'error' => $errors,
                    'response' => VarDumper::dumpAsString($responseData),
                    'data' => $data,
                ], 'SaleController:resendTickets');
                throw new \RuntimeException('Resend tickets BO errors: ' . $errors);
            }

            if ($responseData['status'] !== 'Success') {
                Yii::error([
                    'data' => $data,
                    'response' => VarDumper::dumpAsString($responseData),
                ], 'SaleController:resendTickets');
                throw new \RuntimeException('Resend tickets BO errors. Undefined error.');
            }

            $username = Auth::user()->username ?? null;
            $case->addEventLog(null, 'Sent Ticket Receipts To: (' . implode(', ', $data['emails']) . '), By username: ' . $username);

            $result['error'] = false;
            $result['message'] = 'Tickets resend successfully';
        } catch (\Throwable $throwable) {
            $result['error'] = true;
            $result['message'] = $throwable->getMessage();
        }

        return $result;
    }

    public function actionCancelSale()
    {
        $caseId = (int)Yii::$app->request->get('caseId');
        $caseSaleId = (int)Yii::$app->request->get('caseSaleId');
        $caseSaleCancelForm = new CaseSaleCancelForm($caseId, $caseSaleId);

        if (Yii::$app->request->isAjax && $caseSaleCancelForm->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($caseSaleCancelForm);
        }

        if ($caseSaleCancelForm->load(Yii::$app->request->post()) && $caseSaleCancelForm->validate()) {
            $case = Cases::findOne(['cs_id' => $caseSaleCancelForm->caseId]);

            try {
                if (!Auth::can('cases/update', ['case' => $case])) {
                    throw new ForbiddenHttpException('Access denied.');
                }

                $caseSale = CaseSale::findOne(['css_cs_id' => $caseSaleCancelForm->caseId, 'css_sale_id' => $caseSaleCancelForm->caseSaleId]);
                if (!$caseSale) {
                    throw new \DomainException('Not found case sale. Id (' . $caseSaleCancelForm->caseSaleId . ')');
                }

                if (!$case->project) {
                    throw new \DomainException('Not found Project. Id ' . $case->cs_project_id);
                }
                if (!$case->project->api_key) {
                    throw new \DomainException('Not found API KEY. Project. Id ' . $case->cs_project_id);
                }
                $project = $case->project;
                $projectName = $project->name ?? null;

                $additionalInfo = $this->casesSaleService->getAdditionalInfoForCancelSale($caseSaleCancelForm);
                $data = [
                    'apiKey' => $project->api_key,
                    'bookingId' => $caseSale->css_sale_book_id,
                    'additionalInfo' => $additionalInfo,
                ];

                $host = Yii::$app->params['backOffice']['urlV3'];
                $responseBO = BackOffice::sendRequest2('flight-request/cancel', $data, 'POST', 120, $host);

                if (!$responseBO->isOk) {
                    Yii::error([
                        'message' => 'BO response error.',
                        'response' => VarDumper::dumpAsString($responseBO->content),
                        'data' => $data,
                        'caseId' => $case->cs_id,
                    ], 'SaleController:cancel');
                    throw new \DomainException('Flight Cancel BO request error. ' . VarDumper::dumpAsString($responseBO->content));
                }

                $responseData = $responseBO->data;

                if (empty($responseData['status'])) {
                    Yii::error([
                        'message' => 'BO response error. Not found Status',
                        'response' => VarDumper::dumpAsString($responseData),
                        'data' => $data,
                        'caseId' => $case->cs_id,
                    ], 'SaleController:cancelSale');
                    throw new \DomainException('Undefined BO response. Not found Status');
                }

                if (!in_array($responseData['status'], ['Success', 'Failed'], false)) {
                    Yii::error([
                        'message' => 'BO response undefined status.',
                        'response' => VarDumper::dumpAsString($responseData),
                        'data' => $data,
                        'caseId' => $case->cs_id,
                    ], 'SaleController:cancelSale');
                    throw new \DomainException('Undefined BO response Status');
                }

                if (!empty($responseData['errors'])) {
                    $errors = '';
                    foreach ($responseData['errors'] as $error) {
                        if (is_array($error)) {
                            $errors .= implode('; ', $error);
                        } else {
                            $errors .= $error . '; ';
                        }
                    }
                    Yii::error([
                        'message' => 'BO response error.',
                        'error' => $errors,
                        'response' => VarDumper::dumpAsString($responseData),
                        'data' => $data,
                        'caseId' => $case->cs_id,
                    ], 'SaleController:cancelSale');
                    throw new \DomainException('Flight Cancel BO errors: ' . $errors);
                }

                if ($responseData['status'] !== 'Success') {
                    Yii::error([
                        'data' => $data,
                        'response' => VarDumper::dumpAsString($responseData),
                        'caseId' => $case->cs_id,
                    ], 'SaleController:cancelSale');
                    throw new \DomainException('Flight Cancel BO errors. Undefined error.');
                }

                $saleData = $this->casesSaleService->detailRequestToBackOffice((int)$caseSale->css_sale_id, 0, 120, 1);
                $caseSale = $this->casesSaleService->refreshOriginalSaleData($caseSale, $case, $saleData);
                $this->saleTicketService->refreshSaleTicketBySaleData($case->cs_id, $caseSale, $saleData);

                $bookingId = $saleData['bookingId'] ?? null;
                $reason = CancelSaleReason::getName($caseSaleCancelForm->reasonId);
                $reasonNotes = $caseSaleCancelForm->reasonId == CancelSaleReason::OTHER ? '(' . $caseSaleCancelForm->message . ')' : null;
                $logMessage = 'Cancelled Sale ID: ' . $caseSale->css_sale_id . ', BookId: ' . $bookingId . ', By: Username: ' . Auth::user()->username . ', Cancellation reason: ' . $reason . ' ' . $reasonNotes;
                $case->addEventLog(null, $logMessage);

                $this->casesSaleService->sendNotificationToAgentAboutCloseSale($projectName, $case->cs_id, $bookingId, $caseSale->css_sale_id);

                $this->casesSaleService->sendEmailToClientAboutCloseSale($case, $project, $responseData, $saleData);

                if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_UPDATE_PRODUCT_QUOTE_STATUS_BY_BO_SALE_STATUS)) {
                    if (!empty($bookingId)) {
                        $originalProductQuote = ProductQuoteQuery::getProductQuoteByBookingId($bookingId);
                        if (!empty($originalProductQuote)) {
                            ProductQuote::updateProductQuoteStatusByBOSaleStatus($originalProductQuote, $saleData);
                        }
                    }
                }

                Yii::$app->session->addFlash('success', 'Canceled successfully');
            } catch (\DomainException | \RuntimeException $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            } catch (\Throwable $e) {
                Yii::$app->session->setFlash('error', 'Server error');
                Yii::error(AppHelper::throwableLog($e), 'SaleController::cancelSale');
            }

            return $this->redirect(['cases/view', 'gid' => $case->cs_gid]);
        }

        return $this->renderAjax('/sale/partial/_sale_cancel', [
            'caseSaleCancelForm' => $caseSaleCancelForm,
        ]);
    }

    /**
     * @param int $caseSaleId
     * @return string
     */
    public function actionViewFareRules(int $caseSaleId): string
    {
        $fareRules = [];
        try {
            if (!Yii::$app->request->isAjax) {
                throw new BadRequestHttpException();
            }

            /** @abac null, SaleListAbacObject::UI_BLOCK_SALE_LIST, SaleListAbacObject::ACTION_VIEW_FARE_RULES, View Fare Rules */
            if (!Yii::$app->abac->can(null, SaleListAbacObject::UI_BLOCK_SALE_LIST, SaleListAbacObject::ACTION_VIEW_FARE_RULES)) {
                throw new ForbiddenHttpException('Access denied');
            }

            $caseSale = CaseSale::findOne(['css_sale_id' => $caseSaleId]);
            if (!$caseSale) {
                throw new NotFoundHttpException('Not found case sale. Id (' . $caseSaleId . ')');
            }

            $saleWithFareRules = [];
            try {
                $saleWithFareRules = $this->casesSaleService->detailRequestToBackOffice($caseSale->css_sale_id, 1);
            } catch (\DomainException | \RuntimeException | BoResponseException $e) {
                \Yii::warning(AppHelper::throwableLog($e), 'SaleController:actionViewFareRules:DomainException|RuntimeException|BoResponseException');
            }

            $fareRules = $this->fareRulesService->parseResponse($saleWithFareRules);
        } catch (\Throwable $throwable) {
            \Yii::warning(AppHelper::throwableLog($throwable), 'SaleController:actionViewFareRules:DomainException|RuntimeException|BoResponseException');
        }

        return $this->renderAjax('/sale/partial/_sale_fare_rules', [
            'fareRules' => $fareRules
        ]);
    }
}
