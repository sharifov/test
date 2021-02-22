<?php

namespace modules\attraction\src\useCases\request\update;

use modules\attraction\src\repositories\attraction\AttractionRepository;
use modules\product\src\entities\product\ProductRepository;

/**
 * Class AttractionRequestUpdateService
 *
 * @property ProductRepository $productRepository
 */
class AttractionRequestUpdateService
{
    private $attractionRepository;

    public function __construct(AttractionRepository $attractionRepository)
    {
        $this->attractionRepository = $attractionRepository;
    }

    public function update(AttractionUpdateRequestForm $form): void
    {
        $attraction = $this->attractionRepository->find($form->getAttractionId());
        $attraction->updateRequest($form);
        $this->attractionRepository->save($attraction);
    }
}
