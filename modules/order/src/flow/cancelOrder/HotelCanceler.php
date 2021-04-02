<?php

namespace modules\order\src\flow\cancelOrder;

use modules\hotel\models\HotelQuote;
use modules\hotel\src\useCases\api\bookQuote\HotelQuoteCancelBookService;

/**
 * Class HotelCanceler
 *
 * @property HotelQuoteCancelBookService $cancelService
 */
class HotelCanceler
{
    private HotelQuoteCancelBookService $cancelService;

    public function __construct(HotelQuoteCancelBookService $cancelService)
    {
        $this->cancelService = $cancelService;
    }

    public function cancel(HotelQuote $quote): void
    {
        try {
            $resultCancel = $this->cancelService->cancelBook($quote);
            if ($resultCancel->status) {
                return;
            }
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Cancel Hotel Quote error.',
                'error' => $e->getMessage(),
                'hotel' => $quote->getAttributes(),
            ], 'HotelCanceler');
        }
        throw new HotelCanceledException();
    }
}
