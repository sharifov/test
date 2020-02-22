<?php

namespace modules\hotel\src\useCases\request\update;

use modules\hotel\src\repositories\hotel\HotelRepository;
use modules\product\src\entities\product\ProductRepository;
use sales\services\TransactionManager;

/**
 * Class HotelRequestUpdateService
 *
 * @property ProductRepository $productRepository
 * @property HotelRepository $hotelRepository
 * @property TransactionManager $transactionManager
 */
class HotelRequestUpdateService
{
    private $productRepository;
    private $hotelRepository;
    private $transactionManager;

    public function __construct(
        ProductRepository $productRepository,
        HotelRepository $hotelRepository,
        TransactionManager $transactionManager
    )
    {
        $this->productRepository = $productRepository;
        $this->hotelRepository = $hotelRepository;
        $this->transactionManager = $transactionManager;
    }

    public function update(HotelUpdateRequestForm $form): void
    {
        $hotel = $this->hotelRepository->find($form->getHotelId());

        $this->transactionManager->wrap(function () use ($hotel, $form) {

            $product = $hotel->phProduct;
            $product->changeMarketPrice($form->pr_market_price);
            $product->changeClientBudget($form->pr_client_budget);
            $this->productRepository->save($product);

            $hotel->updateRequest($form);
            $this->hotelRepository->save($hotel);

        });
    }
}
