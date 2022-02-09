<?php

namespace src\model\leadUserRating\repository;

use src\model\leadUserRating\entity\LeadUserRating;
use src\repositories\AbstractBaseRepository;

class LeadUserRatingRepository extends AbstractBaseRepository
{
    public function getModel(): LeadUserRating
    {
        return $this->model;
    }
}
