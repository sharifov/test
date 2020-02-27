<?php

namespace modules\product\src\useCases\product\update;

use modules\product\src\entities\product\ProductRepository;

/**
 * Class ProductCreateService
 *
 * @property ProductRepository $productRepository
 */
class ProductUpdateService
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function update(ProductUpdateForm $form): void
    {
        $product = $this->productRepository->find($form->productId);

        $product->updateInfo($form->pr_name, $form->pr_description);
        $product->changeClientBudget($form->pr_client_budget);
        $product->changeMarketPrice($form->pr_market_price);

        $this->productRepository->save($product);
    }
}
