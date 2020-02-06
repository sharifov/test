<?php

namespace modules\product\src\services\productQuote;

use modules\hotel\src\services\hotelQuote\HotelQuoteCloneService;
use modules\product\src\entities\product\ProductRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\entities\productQuoteOption\ProductQuoteOption;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionRepository;
use sales\services\TransactionManager;
use yii\helpers\VarDumper;

/**
 * Class ProductQuoteCloneService
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property TransactionManager $transactionManager
 * @property ProductQuoteOptionRepository $productQuoteOptionRepository
 * @property HotelQuoteCloneService $hotelQuoteCloneService
 * @property ProductRepository $productRepository
 */
class ProductQuoteCloneService
{
    private $productQuoteRepository;
    private $transactionManager;
    private $productQuoteOptionRepository;
    private $hotelQuoteCloneService;
    private $productRepository;

    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        ProductQuoteOptionRepository $productQuoteOptionRepository,
        TransactionManager $transactionManager,
        HotelQuoteCloneService $hotelQuoteCloneService,
        ProductRepository $productRepository
    )
    {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->transactionManager = $transactionManager;
        $this->productQuoteOptionRepository = $productQuoteOptionRepository;
        $this->hotelQuoteCloneService = $hotelQuoteCloneService;
        $this->productRepository = $productRepository;
    }

    public function clone(int $productQuoteId, int $toProductId, ?int $ownerId, ?int $creatorId): ProductQuote
    {
        $originalQuote = $this->productQuoteRepository->find($productQuoteId);
        $toProduct = $this->productRepository->find($toProductId);

        if (!$originalProduct = $originalQuote->pqProduct) {
            throw new \DomainException('Not found relation Product in Product Quote.');
        }

        if ($toProduct->pr_type_id !== $originalProduct->pr_type_id) {
            throw new \DomainException('Different product types.');
        }

        $clone = $this->transactionManager->wrap(function () use ($originalQuote, $toProduct, $ownerId, $creatorId) {

            $productQuote = ProductQuote::clone($originalQuote, $toProduct->pr_id, $ownerId, $creatorId);
            $this->productQuoteRepository->save($productQuote);

            foreach ($originalQuote->productQuoteOptions as $originalProductQuoteOption) {
                $productQuoteOption = ProductQuoteOption::clone($originalProductQuoteOption, $productQuote->pq_id);
                $this->productQuoteOptionRepository->save($productQuoteOption);
            }

            $childQuote = $originalQuote->getChildQuote();
            $childProduct = $toProduct->getChildProduct();

            if ($childQuote && $childProduct) {
                if ($originalQuote->isHotel()) {
                    $this->hotelQuoteCloneService->clone($childQuote->getId(), $childProduct->getId(), $productQuote->pq_id);
                } elseif ($originalQuote->isFlight()) {

                }
            }

            return $productQuote;

        });

        return $clone;
    }
}
