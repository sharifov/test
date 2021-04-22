<?php

namespace modules\hotel\src\services\hotelQuote;

use modules\hotel\models\Hotel;
use modules\hotel\models\HotelList;
use modules\hotel\models\HotelQuote;
use modules\hotel\models\HotelQuoteRoomPax;
use modules\hotel\src\exceptions\HotelCodeException;
use modules\hotel\src\helpers\HotelQuoteHelper;
use modules\hotel\src\repositories\hotel\HotelRepository;
use modules\order\src\exceptions\OrderC2BDtoException;
use modules\order\src\exceptions\OrderC2BException;
use modules\order\src\forms\api\createC2b\QuotesForm;
use modules\product\src\entities\productHolder\ProductHolder;
use modules\product\src\entities\productHolder\ProductHolderRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\interfaces\Productable;
use modules\product\src\interfaces\ProductQuoteService;
use sales\helpers\app\AppHelper;
use yii\helpers\Json;

/**
 * Class HotelQuoteManageService
 * @package modules\hotel\src\services\hotelQuote
 *
 * @property ProductHolderRepository $productHolderRepository
 * @property ProductQuoteRepository $productQuoteRepository
 * @property HotelRepository $hotelRepository
 */
class HotelQuoteManageService implements ProductQuoteService
{
    private ProductHolderRepository $productHolderRepository;
    private ProductQuoteRepository $productQuoteRepository;
    private HotelRepository $hotelRepository;

    public function __construct(
        ProductHolderRepository $productHolderRepository,
        ProductQuoteRepository $productQuoteRepository,
        HotelRepository $hotelRepository
    ) {
        $this->productHolderRepository = $productHolderRepository;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->hotelRepository = $hotelRepository;
    }

    /**
     * @param Productable|Hotel $product
     * @param QuotesForm $form
     * @throws \yii\base\InvalidConfigException
     */
    public function c2bHandle(Productable $product, QuotesForm $form): void
    {
        try {
            $product->ph_destination_code = $form->hotelRequest->destinationCode;
            $product->ph_destination_label = $form->hotelRequest->destinationName;
            $product->ph_check_in_date = $form->hotelRequest->checkIn;
            $product->ph_check_out_date = $form->hotelRequest->checkOut;
            $this->hotelRepository->save($product);

            $hotelData = Json::decode($form->originSearchData);

            $currency = $hotelData['currency'] ?? 'USD';

            $hotelModel = HotelList::findOrCreateByData($hotelData);
            $hotelQuote = HotelQuote::findOrCreateByData($hotelData, $hotelModel, $product, null, $form->orderId, $currency);

            $productQuote = $hotelQuote->hqProductQuote;
            if ($form->isBooked()) {
                $productQuote->applied();
            } else {
                $productQuote->failed();
            }
            $this->productQuoteRepository->save($productQuote);

            $productHolder = ProductHolder::create(
                $product->ph_product_id,
                $form->holder->firstName,
                $form->holder->lastName,
                $form->holder->middleName,
                $form->holder->email,
                $form->holder->phone,
            );
            $this->productHolderRepository->save($productHolder);

            $hotelQuote->refresh();
            foreach ($hotelQuote->hotelQuoteRooms as $quoteRoom) {
                if ($quoteRoomPax = HotelQuoteRoomPax::find()->byQuoteRoomId($quoteRoom->hqr_id)->all()) {
                    $updatePaxKey = null;
                    foreach ($quoteRoomPax as $roomPax) {
                        foreach ($form->hotelPaxData as $key => $paxData) {
                            if ($updatePaxKey !== $key && $quoteRoom->hqr_key === $paxData->hotelRoomKey && $roomPax->getTypeShortName() ===  $paxData->type) {
                                $roomPax->hqrp_first_name = $paxData->first_name;
                                $roomPax->hqrp_last_name = $paxData->last_name;
                                $roomPax->hqrp_age = $paxData->age;
                                $roomPax->hqrp_dob = $paxData->birth_date;

                                if (!$roomPax->save()) {
                                    \Yii::warning('Hotel room pax personal info saving failed: ' . $roomPax->getErrorSummary(true)[0], 'HotelQuoteManageService::c2bHandle');
                                }

                                $updatePaxKey = $key;
                                break;
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            if ($e->getCode() >= 500) {
                \Yii::error(AppHelper::throwableLog($e, true), 'HotelQuoteManageService::c2bHandle');
                throw $e;
            }

            $dto = new OrderC2BDtoException(
                $product,
                $form->quoteOtaId
            );
            throw new OrderC2BException($dto, $e->getMessage(), HotelCodeException::API_C2B_HANDLE);
        }
    }
}
