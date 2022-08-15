<?php

namespace frontend\controllers;

use common\components\BackOffice;
use common\helpers\LogHelper;
use common\models\CaseSale;
use common\models\Project;
use common\models\search\SaleSearch;
use modules\order\src\entities\order\Order;
use src\auth\Auth;
use src\entities\cases\Cases;
use src\helpers\app\AppHelper;
use src\model\caseOrder\entity\CaseOrder;
use src\services\cases\CasesSaleService;
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
    ) {
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
            $data['emails'] = LogHelper::hidePersonalData($emails, ['emails']);

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

            $result['error'] = false;
            $result['message'] = 'Tickets resend successfully';
        } catch (\Throwable $throwable) {
            $result['error'] = true;
            $result['message'] = $throwable->getMessage();
        }

        return $result;
    }

    public function actionPrepareCancelSale($caseId, $caseSaleId)
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

            return $this->renderAjax('/sale/partial/_sale_cancel', [
                'caseId' => $caseId,
                'caseSaleId' => $caseSaleId,
            ]);
        } catch (\Throwable $throwable) {
            return $throwable->getMessage();
        }
    }

    public function actionCancelSale()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];

        try {
            if (!Yii::$app->request->isAjax) {
                throw new BadRequestHttpException();
            }
            $caseId = \Yii::$app->request->post('caseId');
            $caseSaleId = \Yii::$app->request->post('caseSaleId');

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

            $data = [
                'apiKey' => $project->api_key,
                'FlightRequest' => [
                    'uid' => $caseSale->css_sale_book_id,
                ]
            ];
            $host = Yii::$app->params['backOffice']['urlV2'];
            $responseBO = BackOffice::sendRequest2('flight-request/cancel', $data, 'POST', 120, $host);

            if (!$responseBO->isOk) {
                Yii::error([
                    'message' => 'BO response error.',
                    'response' => VarDumper::dumpAsString($responseBO->content),
                    'data' => $data,
                ], 'SaleController:cancel');
                throw new \RuntimeException('Flight Cancel BO request error. ' . VarDumper::dumpAsString($responseBO->content));
            }

            $responseData = $responseBO->data;

            if (empty($responseData['status'])) {
                Yii::error([
                    'message' => 'BO response error. Not found Status',
                    'response' => VarDumper::dumpAsString($responseData),
                    'data' => $data,
                ], 'SaleController:cancelSale');
                throw new \DomainException('Undefined BO response. Not found Status');
            }

            if (!in_array($responseData['status'], ['Success', 'Failed'], false)) {
                Yii::error([
                    'message' => 'BO response undefined status.',
                    'response' => VarDumper::dumpAsString($responseData),
                    'data' => $data,
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
                ], 'SaleController:cancelSale');
                throw new \RuntimeException('Flight Cancel BO errors: ' . $errors);
            }

            if ($responseData['status'] !== 'Success') {
                Yii::error([
                    'data' => $data,
                    'response' => VarDumper::dumpAsString($responseData),
                ], 'SaleController:cancelSale');
                throw new \RuntimeException('Flight Cancel BO errors. Undefined error.');
            }

            $result['error'] = false;
            $result['message'] = 'Canceled successfully';
        } catch (\Throwable $throwable) {
            $result['error'] = true;
            $result['message'] = $throwable->getMessage();
        }

        return $result;
    }
}
