<?php

namespace sales\rbac\rules;

use modules\product\src\entities\product\Product;
use modules\product\src\entities\product\ProductRepository;
use Yii;
use yii\rbac\Rule;

/**
 * Class ProductRule
 * @package sales\rbac\rules
 *
 * @property ProductRepository $productRepository
 */
abstract class ProductRule extends Rule
{
	/**
	 * @var ProductRepository
	 */
	private $productRepository;

	/**
	 * ProductRule constructor.
	 * @param array $config
	 * @throws \yii\base\InvalidConfigException
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);
		$this->productRepository = Yii::createObject(ProductRepository::class);
	}

	/**
	 * @param int|string $userId
	 * @param \yii\rbac\Item $item
	 * @param array $params
	 * @return bool
	 */
	public function execute($userId, $item, $params)
	{
		if (!isset($params['productId']) && !isset($params['product'])) {
			throw new \InvalidArgumentException('productId or Product must be set');
		}
		/** @var  Product $params['product'] */
		$productId = $params['productId'] ?? $params['Product']->id;
		$productId = (int)$productId;
		$key = $this->name . '-' . $userId . '-' . $productId;
		$can = Yii::$app->user->identity->getCache($key);
		if ($can === null) {
			try {
				$product = $params['product'] ?? $this->productRepository->find($productId);
				$data = $this->getData($userId, $product);
				$can = Yii::$app->user->identity->setCache($key, $data);
			} catch (\Throwable $e) {
				$can = false;
			}
		}
		return $can;
	}

	/**
	 * @param int $userId
	 * @param Product $product
	 * @return mixed
	 */
	abstract public function getData(int $userId, Product $product);
}