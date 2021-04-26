<?php

namespace modules\product\src\services;

use modules\hotel\src\services\HotelCloneService;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\product\ProductRepository;

/**
 * Class ProductCloneService
 *
 * @property ProductRepository $productRepository
 * @property HotelCloneService $hotelCloneService
 */
class ProductCloneService
{
    private ProductRepository $productRepository;
    private HotelCloneService $hotelCloneService;

    public function __construct(ProductRepository $productRepository, HotelCloneService $hotelCloneService)
    {
        $this->productRepository = $productRepository;
        $this->hotelCloneService = $hotelCloneService;
    }

    public function clone(int $productId, int $leadId, ?int $createdUserId): Product
    {
        $product = $this->productRepository->find($productId);

        $cloneProduct = Product::clone($product, $leadId, $createdUserId);
        $this->productRepository->save($cloneProduct);

        if ($cloneProduct->isHotel()) {
            $this->hotelCloneService->clone($product->pr_id, $cloneProduct->pr_id);
        }

        return $product;
    }
}
