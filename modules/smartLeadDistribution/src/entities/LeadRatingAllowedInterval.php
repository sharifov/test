<?php

namespace modules\smartLeadDistribution\src\entities;

use yii\base\Model;

/**
 * @property-read int $from
 * @property-read int $to
 */
class LeadRatingAllowedInterval extends Model
{
    private array $data;

    public function __construct(array $data, $config = [])
    {
        $this->data = $data;

        parent::__construct($config);
    }

    public function getFrom(): int
    {
        return (int)($this->data['from'] ?? 0);
    }

    public function getTo(): int
    {
        return (int)($this->data['to'] ?? 0);
    }
}
