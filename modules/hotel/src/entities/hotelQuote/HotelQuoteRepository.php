<?php

namespace modules\hotel\src\entities\hotelQuote;

use modules\hotel\models\HotelQuote;
use modules\hotel\src\exceptions\HotelCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

/**
 * Class HotelQuoteRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class HotelQuoteRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): HotelQuote
    {
        if ($quote = HotelQuote::findOne($id)) {
            return $quote;
        }
        throw new NotFoundException('Hotel Quote is not found', HotelCodeException::HOTEL_QUOTE_NOT_FOUND);
    }

    public function save(HotelQuote $quote): int
    {
        if (!$quote->save(false)) {
            throw new \RuntimeException('Saving error', HotelCodeException::HOTEL_QUOTE_SAVE);
        }
        $this->eventDispatcher->dispatchAll($quote->releaseEvents());
        return $quote->hq_id;
    }

    public function remove(HotelQuote $quote): void
    {
        if (!$quote->delete()) {
            throw new \RuntimeException('Removing error', HotelCodeException::HOTEL_QUOTE_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($quote->releaseEvents());
    }
}
