<?php

namespace modules\order\controllers;

use frontend\controllers\FController;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\entities\orderTipsUserProfit\OrderTipsUserProfit;
use modules\order\src\forms\OrderTipsUserProfitFormComposite;
use modules\order\src\services\OrderTipsUserProfitService;
use sales\forms\CompositeFormHelper;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Class OrderTipsUserProfitController
 * @package modules\order\controllers
 *
 * @property OrderTipsUserProfitService $orderTipsUserProfitService
 * @property OrderRepository $orderRepository
 */
class OrderTipsUserProfitController extends FController
{
	/**
	 * @var OrderTipsUserProfitService
	 */
	private $orderTipsUserProfitService;
	/**
	 * @var OrderRepository
	 */
	private $orderRepository;

	public function __construct($id, $module, OrderTipsUserProfitService $orderTipsUserProfitService, OrderRepository $orderRepository, $config = [])
	{
		parent::__construct($id, $module, $config);
		$this->orderTipsUserProfitService = $orderTipsUserProfitService;
		$this->orderRepository = $orderRepository;
	}

	/**
	 * @return array
	 */
	public function behaviors(): array
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
	 * @return string|void
	 * @throws NotFoundHttpException
	 */
	public function actionAjaxManageOrderTipsUserProfit()
	{
		if (\Yii::$app->request->isPost) {
			$orderId = \Yii::$app->request->post('orderId');

			$data = CompositeFormHelper::prepareDataForMultiInput(
				\Yii::$app->request->post(),
				'OrderTipsUserProfitFormComposite',
				['orderTipsUserProfits' => 'OrderTipsUserProfit']
			);
			$orderTipsUserProfits = OrderTipsUserProfit::find()->where(['otup_order_id' => $orderId])->all();
			$form = new OrderTipsUserProfitFormComposite($orderTipsUserProfits, count($data['post']['OrderTipsUserProfit']));
			$order = $this->orderRepository->find($orderId);

			if ($form->load($data['post']) && $form->validate()) {
				try {
					$this->orderTipsUserProfitService->updateMultiple($form, $order);

					\Yii::$app->session->setFlash('success', 'Order User Profit updated successfully');
				} catch (\RuntimeException $e) {
					$form->addError('orderTipsUserProfits.0.otup_user_id', $e->getMessage());
				} catch (\Throwable $e) {
					$form->addError('orderTipsUserProfits.0.otup_user_id', 'Internal Server Error');
					\Yii::error($e->getMessage() . $e->getTraceAsString(), 'OrderTipsUserProfitController::actionAjaxManageOrderTipsUserProfit::Throwable');
				}
			}

			return $this->renderAjax('order_tips_user_profit_view', [
				'model' => $form,
				'order' => $order
			]);
		}
		throw new NotFoundHttpException('Page not found', 404);
	}
}
