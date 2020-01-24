<?php

namespace modules\product\src\useCases\product\create;

use common\models\Product;
use modules\product\src\guards\ProductAvailableGuard;
use modules\product\src\entities\product\ProductRepository;
use modules\product\src\repositories\ProductableRepository;
use modules\product\src\services\ProductFactory;
use sales\repositories\lead\LeadRepository;
use sales\services\TransactionManager;

/**
 * Class ProductCreateService
 *
 * @property ProductFactory $factory
 * @property TransactionManager $transactionManager
 * @property ProductRepository $productRepository
 * @property ProductableRepository $productableRepository
 * @property LeadRepository $leadRepository
 */
class ProductCreateService
{
    private $factory;
    private $transactionManager;
    private $productRepository;
    private $productableRepository;
    private $leadRepository;

    public function __construct(
        ProductFactory $factory,
        TransactionManager $transactionManager,
        ProductRepository $productRepository,
        ProductableRepository $productableRepository,
        LeadRepository $leadRepository
    )
    {
        $this->factory = $factory;
        $this->transactionManager = $transactionManager;
        $this->productRepository = $productRepository;
        $this->productableRepository = $productableRepository;
        $this->leadRepository = $leadRepository;
    }

    public function create(ProductCreateForm $form): int
    {
        ProductAvailableGuard::check($form->pr_type_id);

        $productId = $this->transactionManager->wrap(function () use ($form) {

            $product = Product::create($form);

            $this->productRepository->save($product);

            $productItem = $this->factory->create($form->pr_type_id, $product->pr_id);

            $this->productableRepository->save($form->pr_type_id, $productItem);

            return $product->pr_id;

        });

        return $productId;
    }
}
