<?php

namespace modules\order\controllers;

use frontend\controllers\FController;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\entities\orderUserProfit\OrderUserProfit;
use modules\order\src\forms\OrderUserProfitFormComposite;
use sales\forms\CompositeForm;
use sales\forms\CompositeFormHelper;
use sales\model\user\entity\profit\service\OrderUserProfitService;
use yii\base\Model;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;

/**
 * Class OrderUserProfitController
 * @package modules\order\controllers
 *
 * @property OrderUserProfitService $orderUserProfitService
 * @property OrderRepository $orderRepository
 */
class OrderUserProfitController extends FController
{
	/**
	 * @var OrderUserProfitService
	 */
	private $orderUserProfitService;
	/**
	 * @var OrderRepository
	 */
	private $orderRepository;

	public function __construct($id, $module, OrderUserProfitService $orderUserProfitService, OrderRepository $orderRepository, $config = [])
	{
		parent::__construct($id, $module, $config);
		$this->orderUserProfitService = $orderUserProfitService;
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
					'delete-ajax' => ['POST'],
				],
			],
		];
		return ArrayHelper::merge(parent::behaviors(), $behaviors);
	}

	/**
	 * @return string|void
	 * @throws NotFoundHttpException
	 */
	public function actionAjaxManageOrderUserProfit()
	{
		if (\Yii::$app->request->isPost) {
			$orderId = \Yii::$app->request->post('orderId');

			$data = CompositeFormHelper::prepareDataForMultiInput(
				\Yii::$app->request->post(),
				'OrderUserProfitFormComposite',
				['orderUserProfits' => 'OrderUserProfit']
			);
			$orderUserProfits = OrderUserProfit::find()->where(['oup_order_id' => $orderId])->all();
			$form = new OrderUserProfitFormComposite($orderUserProfits, count($data['post']['OrderUserProfit']));

			if ($form->load($data['post'])) {
				if ($form->validate()) {
					try {
						$order = $this->orderRepository->find($orderId);
						$this->orderUserProfitService->updateMultiple($form, $order);

						\Yii::$app->session->setFlash('success', 'Order User Profit updated successfully');
					} catch (\RuntimeException $e) {
						$form->addError('orderUserProfits.0.oup_user_id', $e->getMessage());
					} catch (\Throwable $e) {
						$form->addError('orderUserProfits.0.oup_user_id', 'Internal Server Error');
						\Yii::error($e->getMessage() . $e->getTraceAsString(), 'OrderUserProfitController::actionAjaxManageOrderUserProfit::Throwable');
					}
				}
			}

			return $this->renderAjax('order_user_profit_view', [
				'model' => $form,
				'orderId' => $orderId
			]);
		}
		throw new NotFoundHttpException('Page not found', 404);
	}

	/**
	 * Finds the OrderUserProfit model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $oup_order_id
	 * @param integer $oup_user_id
	 * @return OrderUserProfit the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($oup_order_id, $oup_user_id)
	{
		if (($model = OrderUserProfit::findOne(['oup_order_id' => $oup_order_id, 'oup_user_id' => $oup_user_id])) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
