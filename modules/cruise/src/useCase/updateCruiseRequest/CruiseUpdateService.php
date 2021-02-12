<?php

namespace modules\cruise\src\useCase\updateCruiseRequest;

use modules\cruise\src\entity\cruise\CruiseRepository;

/**
 * Class CruiseUpdateService
 *
 * @property CruiseRepository $cruiseRepository
 */
class CruiseUpdateService
{
    private CruiseRepository $cruiseRepository;

    public function __construct(CruiseRepository $cruiseRepository)
    {
        $this->cruiseRepository = $cruiseRepository;
    }

    public function update(CruiseUpdateRequestForm $form): void
    {
        $hotel = $this->cruiseRepository->find($form->getCruiseId());
        $hotel->updateRequest($form);
        $this->cruiseRepository->save($hotel);
    }
}
