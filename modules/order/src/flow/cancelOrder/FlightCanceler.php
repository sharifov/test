<?php

namespace modules\order\src\flow\cancelOrder;

use modules\flight\components\api\FlightQuoteBookService;
use modules\flight\models\FlightQuote;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteRepository;

/**
 * Class FlightCanceler
 *
 * @property ProductQuoteRepository $repository
 */
class FlightCanceler
{
    private ProductQuoteRepository $repository;

    public function __construct(ProductQuoteRepository $repository)
    {
        $this->repository = $repository;
    }

    public function cancel(ProductQuote $quote, int $projectId): void
    {
        /** @var FlightQuote $flightQuote */
        $flightQuote = $quote->childQuote;
        if (!$flightQuote->fq_flight_request_uid) {
            throw new CanceledException('Unable to process flight cancellation.');
        }
        try {
            if ($quote->isBooked()) {
                FlightQuoteBookService::void($flightQuote->fq_flight_request_uid, $projectId);
            } elseif ($quote->isInProgress()) {
                FlightQuoteBookService::cancel($flightQuote->fq_flight_request_uid, $projectId);
            } else {
                throw new CanceledException('Unable to process flight cancellation.');
            }
            $quote->cancelled(null, 'Cancel Flow');
            $this->repository->save($quote);
            return;
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Cancel Flight Quote error.',
                'error' => $e->getMessage(),
                'hotel' => $quote->getAttributes(),
            ], 'FlightCanceler');
        }
        throw new CanceledException('Unable to process flight cancellation.');
    }
}
