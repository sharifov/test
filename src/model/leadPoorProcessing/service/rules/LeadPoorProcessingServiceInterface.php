<?php

namespace src\model\leadPoorProcessing\service\rules;

/**
 * Class LeadPoorProcessingServiceInterface
 */
interface LeadPoorProcessingServiceInterface
{
    public function handle(): void;
    public function checkCondition(): bool;
}
