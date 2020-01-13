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
    public function getEmailTemplateData(): array;

    /**
     * @return array
     */
    public function getSmsTemplateData(): array;
}