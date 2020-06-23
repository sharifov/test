<?php

namespace frontend\controllers;

use sales\auth\Auth;
use sales\model\saleTicket\useCase\create\SaleTicketRepository;
use sales\model\saleTicket\useCase\sendEmail\SaleTicketEmailService;
use sales\repositories\cases\CasesSaleRepository;
use sales\repositories\creditCard\CreditCardRepository;
use Yii;
use sales\model\saleTicket\entity\SaleTicket;
use sales\model\saleTicket\entity\search\SaleTicketSearch;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

/**
 * Class SaleTicketController
 * @package frontend\controllers
 *
 * @property SaleTicketRepository $saleTicketRepository
 * @property SaleTicketEmailService $saleTicketEmailService
 * @property CasesSaleRepository $casesSaleRepository
 * @property CreditCardRepository $creditCardRepository
 */
class SaleTicketController extends FController
{

	/**
	 * @var SaleTicketRepository
	 */
	private $saleTicketRepository;
	/**
	 * @var SaleTicketEmailService
	 */
	private $saleTicketEmailService;
	/**
	 * @var CasesSaleRepository
	 */
	private $casesSaleRepository;
	/**
	 * @var CreditCardRepository
	 */
	private $creditCardRepository;

	public function __construct($id, $module, SaleTicketRepository $saleTicketRepository, SaleTicketEmailService $saleTicketEmailService, CasesSaleRepository $casesSaleRepository, CreditCardRepository $creditCardRepository, $config = [])
	{
		parent::__construct($id, $module, $config);
		$this->saleTicketRepository = $saleTicketRepository;
		$this->saleTicketEmailService = $saleTicketEmailService;
		$this->casesSaleRepository = $casesSaleRepository;
		$this->creditCardRepository = $creditCardRepository;
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
    public function actionIndex(): string
    {
        $searchModel = new SaleTicketSearch();
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
        $model = new SaleTicket();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->st_id]);
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
            return $this->redirect(['view', 'id' => $model->st_id]);
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

    public function actionAjaxSaleTicketEditInfo()
	{
		$id = Yii::$app->request->get('st_id');
		$out = [
			'output' => '',
			'message' => '',
		];
		try {
			if (!$id) {
				throw new BadRequestHttpException('Sale Ticket Id is not provided');
			}

			$saleTicket = $this->findModel($id);

			if (!$saleTicket->load(Yii::$app->request->post())
				|| !$saleTicket->validate() || !$saleTicket->save()) {
				throw new \RuntimeException($saleTicket->getErrorSummary(false)[0]);
			}

		} catch (\Throwable $e) {
			$out['message'] = $e->getMessage();
		}
		return $this->asJson($out);
	}

	public function actionAjaxSendEmail()
	{
		$caseId = Yii::$app->request->get('case_id');
		$saleId = Yii::$app->request->get('sale_id');
		$bookingId = Yii::$app->request->get('booking_id');

		if (!$caseId || !$saleId) {
			throw new BadRequestHttpException('Case Id or Sale Id is not provided');
		}

		try {
			$response = ['error' => false, 'message' => 'Email was sent successfully'];
			$saleTickets = $this->saleTicketRepository->findByCaseAndSale((int)$caseId, (int)$saleId);
			$emailSettings = Yii::$app->params['settings']['case_sale_ticket_email_data'];

			$caseSale = $this->casesSaleRepository->getSaleByPrimaryKeys((int)$caseId, (int)$saleId);
			$creditCards = $this->creditCardRepository->findBySaleId((int)$saleId);
			$user = Auth::user();
			$html = $this->renderPartial('partial/ _email_body', ['saleTickets' => $saleTickets, 'caseSale' => $caseSale, 'creditCards' => $creditCards, 'user' => $user]);

			$this->saleTicketEmailService->generateAndSendEmail($saleTickets, $emailSettings, $html, $caseId, $bookingId, $user, $caseSale);
		} catch (\Throwable $e) {
			$response['error'] = true;
			$response['message'] = $e->getMessage();
			Yii::error($e->getMessage() . '; File: ' . $e->getFile() . '; On Line: ' . $e->getLine(), 'SaleTicketController::actionAjaxSendEmail::Throwable');
		}
		$this->asJson($response);
	}

    /**
     * @param integer $id
     * @return SaleTicket
     * @throws NotFoundHttpException
     */
    protected function findModel($id): SaleTicket
    {
        if (($model = SaleTicket::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
