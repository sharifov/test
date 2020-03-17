<?php

namespace modules\product\src\useCases\product\api\create\flight;

use modules\product\src\entities\product\dto\CreateDto;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\product\ProductRepository;
use modules\product\src\entities\productType\ProductType;
use modules\product\src\repositories\ProductableRepository;
use modules\product\src\services\ProductFactory;
use sales\services\TransactionManager;
use webapi\models\ApiLead;

/**
 * Class Handler
 *
 * @property TransactionManager $transactionManager
 * @property ProductRepository $productRepository
 * @property ProductFactory $factory
 * @property ProductableRepository $productableRepository
 */
class Handler
{
    private $transactionManager;
    private $productRepository;
    private $factory;
    private $productableRepository;

    public function __construct(
        TransactionManager $transactionManager,
        ProductRepository $productRepository,
        ProductFactory $factory,
        ProductableRepository $productableRepository
    )
    {
        $this->transactionManager = $transactionManager;
        $this->productRepository = $productRepository;
        $this->factory = $factory;
        $this->productableRepository = $productableRepository;
    }

    public function handle(int $leadId, ApiLead $request): void
    {
        $this->transactionManager->wrap(function () use ($leadId, $request) {

            $product = Product::create(new CreateDto($leadId, ProductType::PRODUCT_FLIGHT, null, null));
            $this->productRepository->save($product);

            $productItem = $this->factory->create(ProductType::PRODUCT_FLIGHT, $product->pr_id);
            $this->productableRepository->save(ProductType::PRODUCT_FLIGHT, $productItem);


            return $product->pr_id;

        });
    }
}
