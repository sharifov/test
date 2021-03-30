<?php

namespace modules\hotel\src\services\hotelQuote;

use modules\hotel\models\Hotel;
use modules\hotel\models\HotelList;
use modules\hotel\models\HotelQuote;
use modules\hotel\src\exceptions\HotelCodeException;
use modules\order\src\exceptions\OrderC2BDtoException;
use modules\order\src\exceptions\OrderC2BException;
use modules\order\src\forms\api\createC2b\QuotesForm;
use modules\product\src\entities\productHolder\ProductHolder;
use modules\product\src\entities\productHolder\ProductHolderRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\interfaces\Productable;
use modules\product\src\interfaces\ProductQuoteService;
use yii\helpers\Json;

/**
 * Class HotelQuoteManageService
 * @package modules\hotel\src\services\hotelQuote
 *
 * @property ProductHolderRepository $productHolderRepository
 */
class HotelQuoteManageService implements ProductQuoteService
{
    /**
     * @var ProductHolderRepository
     */
    private ProductHolderRepository $productHolderRepository;

    public function __construct(ProductHolderRepository $productHolderRepository)
    {
        $this->productHolderRepository = $productHolderRepository;
    }

    /**
     * @param Productable|Hotel $product
     * @param QuotesForm $form
     * @throws \yii\base\InvalidConfigException
     */
    public function c2bHandle(Productable $product, QuotesForm $form): void
    {
        try {
            $hotelData = Json::decode($form->originSearchData);

            $currency = $hotelData['currency'] ?? 'USD';

            $hotelModel = HotelList::findOrCreateByData($hotelData);
            HotelQuote::findOrCreateByData($hotelData, $hotelModel, $product, null, $form->orderId, $currency);

            $productHolder = ProductHolder::create(
                $product->getId(),
                $form->holder->firstName,
                $form->holder->lastName,
                $form->holder->email,
                $form->holder->phone,
            );
            $this->productHolderRepository->save($productHolder);
        } catch (\Throwable $e) {
            $dto = new OrderC2BDtoException(
                $product,
                $form->quoteOtaId
            );
            throw new OrderC2BException($dto, $e->getMessage(), HotelCodeException::API_C2B_HANDLE);
        }
    }
}
