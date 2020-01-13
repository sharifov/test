<?php

namespace sales\interfaces;

/**
 * Interface QuoteCommunicationInterface
 * @package sales\interfaces
 */
interface QuoteCommunicationInterface
{
    /**
     * @return array
     */
    public function getExtraData(): array;
}