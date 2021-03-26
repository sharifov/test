<?php

namespace modules\order\src\processManager\clickToBook;

use modules\order\src\processManager\Status;
use modules\order\src\processManager\Type;
use sales\entities\EventTrait;
use modules\order\src\processManager\events;
use modules\order\src\processManager\OrderProcessManager as BaseProcessManager;

class OrderProcessManager extends BaseProcessManager
{
    use EventTrait;

    public static function create(int $orderId, \DateTimeImmutable $date): self
    {
        $process = new static();
        $process->opm_id = $orderId;
        $process->opm_status = Status::NEW;
        $process->opm_type = Type::CLICK_TO_BOOK;
        $process->opm_created_dt = $date->format('Y-m-d H:i:s');
        $process->recordEvent(new events\CreatedEvent($process, $date->format('Y-m-d H:i:s.u')));
        return $process;
    }

    public function waitBoResponse(\DateTimeImmutable $date): void
    {
        if (!$this->isNew()) {
            throw new \DomainException('OrderProcessManager is not New. Id: ' . $this->opm_id . ' Status: ' . $this->opm_status);
        }
        $this->opm_status = Status::WAIT_BO_RESPONSE;
        $this->recordEvent(new events\WaitBoResponseEvent($this->opm_id, $date->format('Y-m-d H:i:s.u')));
    }

    public function flightProductProcessed(\DateTimeImmutable $date): void
    {
        if (!$this->isWaitBoResponse()) {
            throw new \DomainException('OrderProcessManager is not Wait BO Response. Id: ' . $this->opm_id . ' Status: ' . $this->opm_status);
        }
        $this->opm_status = Status::FLIGHT_PRODUCT_PROCESSED;
        $this->recordEvent(new events\FlightProductProcessedEvent($this->opm_id, $date->format('Y-m-d H:i:s.u')));
    }

    public function failed(\DateTimeImmutable $date): void
    {
        if ($this->isFailed()) {
            throw new \DomainException('OrderProcessManager is already Failed. Id: ' . $this->opm_id . ' Status: ' . $this->opm_status);
        }
        $this->opm_status = Status::FAILED;
        $this->recordEvent(new events\FailedEvent($this->opm_id, $date->format('Y-m-d H:i:s.u')));
    }

    public function booked(\DateTimeImmutable $date): void
    {
        if (!$this->isFlightProductProcessed()) {
            throw new \DomainException('OrderProcessManager is not Flight Product Processed. Id: ' . $this->opm_id . ' Status: ' . $this->opm_status);
        }
        $this->opm_status = Status::BOOKED;
        $this->recordEvent(new events\BookedEvent($this->opm_id, $date->format('Y-m-d H:i:s.u')));
    }

    public function isNew(): bool
    {
        return $this->opm_status === Status::NEW;
    }

    public function isWaitBoResponse(): bool
    {
        return $this->opm_status === Status::WAIT_BO_RESPONSE;
    }

    public function isFailed(): bool
    {
        return $this->opm_status === Status::FAILED;
    }

    public function isFlightProductProcessed(): bool
    {
        return $this->opm_status === Status::FLIGHT_PRODUCT_PROCESSED;
    }
}
