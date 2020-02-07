<?php

namespace modules\hotel\src\services\hotelQuote;

use modules\hotel\models\HotelQuote;
use modules\hotel\models\HotelQuoteRoom;
use modules\hotel\src\entities\hotelQuote\HotelQuoteRepository;
use modules\hotel\src\entities\hotelQuoteRoom\HotelQuoteRoomRepository;
use modules\hotel\src\repositories\hotel\HotelRepository;
use sales\services\TransactionManager;

/**
 * Class HotelQuoteCloneService
 *
 * @property TransactionManager $transactionManager
 * @property HotelQuoteRepository $hotelQuoteRepository
 * @property HotelQuoteRoomRepository $hotelQuoteRoomRepository
 * @property HotelRepository $hotelRepository
 */
class HotelQuoteCloneService
{
    private $transactionManager;
    private $hotelQuoteRepository;
    private $hotelQuoteRoomRepository;
    private $hotelRepository;

    public function __construct(
        TransactionManager $transactionManager,
        HotelQuoteRepository $hotelQuoteRepository,
        HotelQuoteRoomRepository $hotelQuoteRoomRepository,
        HotelRepository $hotelRepository
    )
    {
        $this->transactionManager = $transactionManager;
        $this->hotelQuoteRepository = $hotelQuoteRepository;
        $this->hotelQuoteRoomRepository = $hotelQuoteRoomRepository;
        $this->hotelRepository = $hotelRepository;
    }

    public function clone(int $originalQuoteId, int $toHotelId, int $toProductQuoteId): void
    {
        $originalQuote = $this->hotelQuoteRepository->find($originalQuoteId);
        $toHotel = $this->hotelRepository->find($toHotelId);

        $hotelQuote = HotelQuote::clone($originalQuote, $toHotel->ph_id, $toProductQuoteId);
        $this->hotelQuoteRepository->save($hotelQuote);

        foreach ($originalQuote->hotelQuoteRooms as $room) {
            $hotelQuoteRoom = HotelQuoteRoom::clone($room, $hotelQuote->hq_id);
            $this->hotelQuoteRoomRepository->save($hotelQuoteRoom);
        }
    }
}
