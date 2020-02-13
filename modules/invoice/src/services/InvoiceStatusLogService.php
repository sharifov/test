<?php

namespace modules\invoice\src\services;

use modules\invoice\src\entities\invoiceStatusLog\CreateDto;
use modules\invoice\src\entities\invoiceStatusLog\InvoiceStatusLog;
use modules\invoice\src\entities\invoiceStatusLog\InvoiceStatusLogRepository;

/**
 * Class InvoiceStatusLogService
 *
 * @property InvoiceStatusLogRepository $repository
 */
class InvoiceStatusLogService
{
    private $repository;

    public function __construct(InvoiceStatusLogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function log(CreateDto $dto): void
    {
        if ($previous = $this->repository->getPrevious($dto->invoiceId)) {
            $previous->end();
            $this->repository->save($previous);
        }
        $log = InvoiceStatusLog::create($dto);
        $this->repository->save($log);
    }
}
