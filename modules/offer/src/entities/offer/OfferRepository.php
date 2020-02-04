<?php

namespace modules\offer\src\entities\offer;

use modules\offer\src\exceptions\OfferCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

/**
 * Class OfferRepository
 *
 * @property EventDispatcher $eventDispatcher
 *
 * @method Offer|null getByGid(string $gid)
 */
class OfferRepository extends Repository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): Offer
    {
        if ($offer = Offer::findOne($id)) {
            return $offer;
        }
        throw new NotFoundException('Offer is not found', OfferCodeException::OFFER_NOT_FOUND);
    }

    public function findByGid(string $gid): Offer
    {
        if ($offer = Offer::find()->byGid($gid)->one()) {
            return $offer;
        }
        throw new NotFoundException('Offer is not found', OfferCodeException::OFFER_NOT_FOUND);
    }

    public function save(Offer $offer): int
    {
        if (!$offer->save(false)) {
            throw new \RuntimeException('Saving error', OfferCodeException::OFFER_SAVE);
        }
        $this->eventDispatcher->dispatchAll($offer->releaseEvents());
        return $offer->of_id;
    }

    public function remove(Offer $offer): void
    {
        if (!$offer->delete()) {
            throw new \RuntimeException('Removing error', OfferCodeException::OFFER_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($offer->releaseEvents());
    }
}
