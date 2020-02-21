<?php
namespace modules\order\src\events;

use modules\order\src\entities\order\OrderRepository;
use yii\base\Component;
use yii\helpers\VarDumper;

class OrderEvent extends Component
{
	public const EVENT_LINK_ORDER_OWNER = 'link_order_owner';
	public const EVENT_BEFORE_INIT_SAVE = 'before_init_save';
	/**
	 * @var OrderRepository
	 */
	private static $orderRepository;

	public function __construct(OrderRepository $orderRepository, $config = [])
	{
		parent::__construct($config);
		self::$orderRepository = $orderRepository;
	}

	public function linkOrderOwner($params): void
	{
		$order = $params->data;
		$orderRepository = self::getOrderRepository();

		try {
			$findOrder = $orderRepository->find(234234);
		} catch (\Throwable $e) {
			\Yii::error($e->getMessage(), 'OrderEvent::EVENT_LINK_ORDER_OWNER::linkOrderOwner::Throwable');
		}

		\Yii::warning(VarDumper::dumpAsString($order), 'OrderEvent::EVENT_LINK_ORDER_OWNER::linkOrderOwner');
//		\Yii::warning(VarDumper::dumpAsString($params), 'OrderEvent::EVENT_LINK_ORDER_OWNER::linkOrderOwner');
	}

	public function beforeInitSave($params): void
	{
		\Yii::warning('Before init save event', 'OrderEvent::EVENT_LINK_ORDER_OWNER::beforeInitSave');
	}

	private static function getOrderRepository()
	{
		return \Yii::createObject(OrderRepository::class);
	}
}